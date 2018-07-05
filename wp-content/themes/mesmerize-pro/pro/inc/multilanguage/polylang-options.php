<?php


add_filter('cloudpress\customizer\global_data', function ($data) {
    if (function_exists('pll_current_language')) {
        $lang = mesmerize_get_current_language();
        if ($lang && $lang != mesmerize_get_default_language()) {
            global $polylang;
            
            $data['primaryMenuLocation'] = "primary___" . $lang;
            
            $data['homeUrl'] = $polylang->links_model->home_url(PLL()->model->get_language($lang));
        }
    }
    
    return $data;
});


add_filter("mesmerize_get_default_language", function ($lang) {
    return pll_default_language();
});

add_filter("mesmerize_get_post_language", function ($lang, $post_id) {
    return pll_get_post_language($post_id, "slug");
}, 0, 2);

add_filter("mesmerize_get_current_language", function ($lang) {
    
    global $pagenow;
    if (is_admin() && 'customize.php' === $pagenow) {
        
        $preview_url = isset($_GET['url']) ? wp_unslash($_GET['url']) : site_url();
        
        global $polylang;
        if ($polylang && method_exists($polylang->links_model, 'get_language_from_url')) {
            return $polylang->links_model->get_language_from_url($preview_url);
        }
    }
    
    if ('admin-ajax.php' === $pagenow) {
        $page_id = isset($_POST['customize_post_id']) ? intval($_POST['customize_post_id']) : -1;
        if ($page_id != -1) {
            return pll_get_post_language($page_id, "slug");
        }
    }
    
    return pll_current_language();
});


add_action('pll_before_post_translations', 'mesmerize_multilanguage_copy_from_source');


function mesmerize_multilanguage_copy_from_source()
{
    
    global $post;
    
    $sourceID  = isset($_REQUEST['from_post']) ? $_REQUEST['from_post'] : null;
    $slug      = pll_default_language('slug');
    $defaultID = pll_get_post($post->ID, $slug);
    $defaultID = $defaultID ? $defaultID : $sourceID;
    
    if ($post->ID === $defaultID) {
        return;
    }
    
    $primaryPost = get_post($defaultID);
    $content     = wp_json_encode($primaryPost->post_content);
    
    
    ?>
    <script>
        function mesmerize_multilanguage_copy_from_source() {
            var tinyMCEisVisible = (tinyMCE.get('content') && !tinyMCE.get('content').isHidden() && tinyMCE.get('content').hasVisual === true);
            var content =<?php echo $content; ?>;
            if (tinyMCEisVisible) {
                tinyMCE.get('content').setContent(content);
            } else {
                edInsertContent(edCanvas, content)
            }
        }
    </script>
    <a href="#" onclick="mesmerize_multilanguage_copy_from_source()" class="button" style="margin-top: 20px"><span class="dashicons dashicons-admin-page" style="line-height: inherit;"></span><?php _e('Copy from primary language', 'mesmerize-pro') ?></a>
    <script>

    </script>
    <?php
}

function mesmerize_get_pll_frontend_switcher($args = array(), $ulClass = "")
{
    $displayAsDropDown = get_theme_mod('mesmerize_polylang_display_as_dropdown', false);
    
    $args = array_merge((array)$args, array(
        "echo"       => 0,
        "show_flags" => '1',
        "show_names" => '1',
        'dropdown'   => intval($displayAsDropDown),
    ));
    
    
    $result = pll_the_languages($args);
    
    if ( ! intval($displayAsDropDown)) {
        $result = "<ul class='mesmerize-language-switcher {$ulClass}'>$result</ul>";
    } else {
        $hidden_args             = $args;
        $hidden_args['dropdown'] = '0';
        
        $result .= pll_the_languages($hidden_args);
        $result = "<div class='ope-pll-switcher {$ulClass}'>{$result}</div>";
    }
    
    
    return $result;
}


// polylang switcher near menu
add_action('wp_footer', function () {
    
    
    $show_switcher = get_theme_mod('mesmerize_polylang_show_switcher', true);
    
    if ( ! intval($show_switcher)) {
        return;
    }
    
    
    $content  = "";
    $position = 'after';
    
    if (function_exists('pll_the_languages')) {
        $content = mesmerize_get_pll_frontend_switcher(array(), "{$position}-menu");
    }
    
    echo $content;
}, 10, 2);


