<?php

add_shortcode('mesmerize_gallery', 'mesmerize_gallery_shortcode');


$mesmerize_gallery_index = 0;


function mesmerize_gallery_masonry_script($atts)
{
    $script             = "";
    $isShortcodeRefresh = apply_filters('mesmerize_is_shortcode_refresh', false);
    if ($atts['masonry'] == 1) {
        ob_start();
        ?>
        (function ($) {
        var masonryGallery = $(".<?php echo $atts['id'] ?>-dls-wrapper");

        if(!masonryGallery.length){
        	return;
        }

        masonryGallery.masonry({
        itemSelector: '.gallery-item',
        percentPosition: true,
        });

        var images = masonryGallery.find('img');
        var loadedImages = 0;

        function imageLoaded() {
        loadedImages++;
        if (images.length === loadedImages) {
        masonryGallery.data().masonry.layout();

        }
        }

        images.each(function () {
        $(this).on('load', imageLoaded);
        });

        $(window).on('load', function () {
        if(masonryGallery.length){
        masonryGallery.data().masonry.layout();
        }
        });

        })(<?php mesmerize_print_contextual_jQuery(); ?>);
        
        <?php
        $script = ob_get_clean();
        
    } else {
        if ($isShortcodeRefresh) {
            ob_start();
            ?>
            jQuery(function ($) {
            var masonryGallery = $(".<?php echo $atts['id'] ?>-dls-wrapper");
            try {
            masonryGallery.masonry('destroy');
            } catch (e) {

            }
            });
            <?php
            $script = ob_get_clean();
            
        }
    }
    
    return $script;
}

function mesmerize_gallery_shortcode($atts)
{
    global $mesmerize_gallery_index;
    $atts = shortcode_atts(
        array(
            'id'      => 'ope-gallery-' . (++$mesmerize_gallery_index),
            'columns' => '4',
            'ids'     => '',
            'link'    => 'file',
            'lb'      => '1',
            'orderby' => '',
            'skin'    => 'skin01',
            'masonry' => '1',
            'size'    => 'medium',
        ),
        $atts
    );
    
    if (empty($atts['ids'])) {
        
        $colors = array('03A9F4', '4CAF50', 'FBC02D', '9C27B0');
        
        ob_start();
        
        $imagesColors = array();
        
        ?>
        <div class="<?php echo $atts['id'] ?>-dls-wrapper gallery-items-wrapper">
            <?php for ($img = 0; $img < ($atts['columns'] * 2); $img++): ?>
                <dl class="gallery-item">
                    <dt class="gallery-icon landscape">
                        <?php
                        $imgIndex = $img % 8 + 1;
                        $prefix   = ($atts['masonry'] == 1) ? 'masonry-' : '';
                        $imgURL   = "//extendthemes.com/assets/mesmerize/previews/sections/gallery/{$prefix}{$imgIndex}.jpg";
                        ?>
                        <a <?php echo($atts['lb'] == '1' ? "data-fancybox='{$atts['id']}-group'" : "") ?> href="<?php echo $imgURL ?>">
                            <img src="<?php echo $imgURL ?>" class="<?php echo $atts['id'] ?>-image" alt=""></a>
                    </dt>
                </dl>
            <?php endfor; ?>
        </div>
        <?php
        
        $gallery = ob_get_clean();
    } else {
        
        
        add_filter('use_default_gallery_style', '__return_false');
        
        // make sure the gallery_shortcode function will return the default gallery
        // fixes japck issue
        add_filter('post_gallery', '__return_empty_string', PHP_INT_MAX);
        
        
        $gallery = gallery_shortcode($atts);
        
        remove_filter('post_gallery', '__return_empty_string', PHP_INT_MAX); // remove the previous filter
        remove_filter('use_default_gallery_style', '__return_false');
        
        $gallery = preg_replace("/<br(.*?)>/is", "", $gallery);
        $gallery = preg_replace("/<div(.*?)id='gallery-(.*?)>/", "<div $1 class='" . $atts['id'] . "-dls-wrapper gallery-items-wrapper' >", $gallery);
        $gallery = preg_replace("/<img(.*)class=\"(.*?)\"/", "<img $1 class='" . $atts['id'] . "-image'", $gallery);
        
        $gallery = trim($gallery);
        
        if ( ! empty($gallery)) {
            $gallery = $gallery . '<style>#' . $atts['id'] . ' .wp-caption-text.gallery-caption{display:none;}</style>';
        }
    }
    
    if (empty($gallery)) {
        return mesmerize_placeholder_p(__("Empty gallery", "mesmerize"));
    }
    
    ob_start();
    
    ?>
    <style type="text/css">
        @media only screen and (min-width: 768px) {
        #<?php echo $atts['id'] ?> dl {
            float: left;
            width: <?php echo (100 / $atts['columns']) ?>% !important;
            max-width: <?php echo (100 / $atts['columns']) ?>% !important;
            min-width: <?php echo (100 / $atts['columns']) ?>% !important;
        }

        #<?php echo $atts['id'] ?> dl:nth-of-type(<?php echo $atts['columns']?>n +1 ) {
                                       clear: both;
                                   }
        }
    </style>
    <?php
    
    
    $style = ob_get_clean();
    
    $gallery = $style . $gallery;
    
    if ($atts['lb'] == 1) {
        $gallery = preg_replace('/<a/', '<a data-fancybox="' . $atts['id'] . '-group"', $gallery);
    }
    
    $masonry = mesmerize_gallery_masonry_script($atts);
    
    $isShortcodeRefresh = apply_filters('mesmerize_is_shortcode_refresh', false);
    if ( ! $isShortcodeRefresh) {
        wp_add_inline_script('masonry', $masonry);
    } else {
        $gallery .= "<script>{$masonry}</script>";
    }
    
    
    return "<div id='{$atts['id']}' class='gallery-wrapper'>{$gallery}</div>";
    
}
