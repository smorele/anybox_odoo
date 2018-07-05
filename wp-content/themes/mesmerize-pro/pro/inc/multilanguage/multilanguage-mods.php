<?php

function mesmerize_get_translatable_mods()
{
    $mesmerize_translatable_mods = array(
        "header_front_page_image",
        
        "header_image",
        "header_bg_position",
        
        "header_slideshow",
        "header_video_external",
        
        "header_content_image",
        "header_content_video",
        
        "header_video_popup_image",
        
        "header_title",
        "header_subtitle",
        "header_subtitle2",
        
        "header_text_morph_alternatives",
        
        "latest_news_read_more",
        
        "header_content_buttons",
        "header_store_badges",
        
        "header_section_content_header-group-of-images",
        
        "footer_content_copyright_text",
        
        "footer_content_box_text",
        
        "slider_elements",
        "third_party_slider",
    );
    
    return apply_filters("mesmerize_translatable_mods", $mesmerize_translatable_mods);
}

add_filter("mesmerize_translatable_mods", function ($mods) {
    
    // TODO: move each one in it's place//
    
    $sides = array("left", "right");
    
    // header buttons//
    for ($i = 1; $i < 7; $i++) {
        array_push($mods, "header_btn_{$i}_label");
        array_push($mods, "header_btn_{$i}_url");
        array_push($mods, "header_btn_{$i}_target");
    }
    
    foreach ($sides as $key => $side) {
        
        // top bar text//
        array_push($mods, "header_top_bar_area-{$side}_text");
        
        // top bar social icons//
        for ($i = 0; $i < 5; $i++) {
            array_push($mods, "header_top_bar_area-{$side}_social_icon_{$i}_enabled");
            array_push($mods, "header_top_bar_area-{$side}_social_icon_{$i}_link");
        }
        
        // top bar info fields//
        for ($i = 0; $i < 3; $i++) {
            array_push($mods, "header_top_bar_area-{$side}_info_field_{$i}_enabled");
            array_push($mods, "header_top_bar_area-{$side}_info_field_{$i}_link");
        }
    }
    
    // footer social icons//
    for ($i = 0; $i < 5; $i++) {
        array_push($mods, "footer_content_social_icon_{$i}_enabled");
        array_push($mods, "footer_content_social_icon_{$i}_link");
    }
    
    // footer content boxes//
    for ($i = 1; $i < 4; $i++) {
        array_push($mods, "footer_box{$i}_content_text");
    }
    
    return $mods;
});

function mesmerize_get_default_language()
{
    global $pagenow;
    $lang = apply_filters("mesmerize_get_default_language", "");
    mesmerize_log2("mesmerize_get_default_language => $lang => $pagenow");
    
    return $lang;
}

function mesmerize_get_post_language($post_id)
{
    global $pagenow;
    $lang = apply_filters("mesmerize_get_post_language", "", $post_id);
    mesmerize_log2("mesmerize_get_post_language => $lang => $pagenow");
    
    return $lang;
}

function mesmerize_get_current_language()
{
    global $pagenow;
    $lang = apply_filters("mesmerize_get_current_language", "");
    
    if ( ! $lang || empty($lang)) {
        $lang = mesmerize_get_default_language();
    }
    
    mesmerize_log2("mesmerize_get_current_language => $lang => $pagenow");
    
    return $lang;
}

function mesmerize_translate_set_mods($value, $old_value)
{
    
    $page_id = isset($_POST['customize_post_id']) ? intval($_POST['customize_post_id']) : -1;
    
    if ($page_id === -1) {
        return $value;
    }
    
    $changeset_status = null;
    if (isset($_POST['customize_changeset_status'])) {
        $changeset_status = wp_unslash($_POST['customize_changeset_status']);
    }
    
    $mesmerize_translatable_mods = mesmerize_get_translatable_mods();
    global $mesmerize_default_language_mods;
    
    remove_filter("option_theme_mods_" . get_option('stylesheet'), "mesmerize_translate_get_mods");
    
    $original_value = $mesmerize_default_language_mods;
    $__entry_value  = $value;
    $value          = mesmerize_translate_mods($value);
    $old_value      = mesmerize_translate_mods($old_value);
    
    if ($page_id !== -1 && $changeset_status == "publish") {
        
        $post_language        = mesmerize_get_post_language($page_id);
        $pll_default_language = mesmerize_get_default_language();
        
        $changed_mods = mesmerize_changed_mods();
        
        if ($pll_default_language != $post_language) {
            foreach ($mesmerize_translatable_mods as $mod) {
                $cp_mod = "CP_AUTO_SETTING[" . $mod . "]";
                
                $are_eq1 = (isset($value[$mod]) && ($value[$mod] == $old_value[$mod])) || ! isset($value[$mod]);
                $are_eq2 = (isset($value['CP_AUTO_SETTING'][$mod]) && $value['CP_AUTO_SETTING'][$mod] == $old_value['CP_AUTO_SETTING'][$mod]) || ! isset($value['CP_AUTO_SETTING'][$mod]);
                $skip    = $are_eq1 && $are_eq2;
                
                
                // update translated values //
                if (isset($changed_mods[$mod]) || isset($changed_mods[$cp_mod])) {
                    if ( ! $skip) {
                        if (isset($value[$mod])) {
                            mesmerize_set_translate_mod($post_language, $mod, $value[$mod], $__entry_value[$mod]);
                        } else {
                            if (isset($value['CP_AUTO_SETTING'][$mod])) {
                                mesmerize_set_translate_mod($post_language, $mod, $value['CP_AUTO_SETTING'][$mod], $__entry_value['CP_AUTO_SETTING'][$mod]);
                            }
                        }
                    }
                }
                
                // revert to old values for translatable mods //
                
                if (isset($original_value[$mod])) {
                    $value[$mod] = $original_value[$mod];
                    if (isset($original_value['CP_AUTO_SETTING'][$mod])) {
                        $value['CP_AUTO_SETTING'][$mod] = $original_value[$mod];
                    }
                } else {
                    //if its unset in original language, it will not show up, as it will go throw default values//
                    //unset($value[$mod]);
                }
                
                if (isset($original_value['CP_AUTO_SETTING'][$mod])) {
                    $value['CP_AUTO_SETTING'][$mod] = $original_value['CP_AUTO_SETTING'][$mod];
                    $value[$mod]                    = $original_value['CP_AUTO_SETTING'][$mod];
                } else {
                    //if its unset in original language, it will not show up, as it will go throw default values//
                    //unset($value['CP_AUTO_SETTING'][$mod]);
                }
            }
            
            $value = apply_filters('mesmerize_default_language_mod_value', $value, $__entry_value);
        }
    }
    
    return $value;
}

$mesmerize_default_language_mods = false;
$mesmerize_cached                = false;

function mesmerize_translate_get_initial_value($value)
{
    global $mesmerize_default_language_mods;
    $mesmerize_default_language_mods = $value;
    
    return $value;
}

function mesmerize_translate_get_mods($value)
{
    $page_id = isset($_POST['customize_post_id']) ? intval($_POST['customize_post_id']) : -1;
    
    $default_language = mesmerize_get_default_language();
    $current_language = mesmerize_get_current_language();
    
    
    if ($default_language === $current_language) {
        return $value;
    }
    
    $changeset_status = null;
    if (isset($_POST['customize_changeset_status'])) {
        $changeset_status = wp_unslash($_POST['customize_changeset_status']);
    }
    
    // on publish return original values //
    if (is_customize_preview() && $changeset_status === "publish") {
        return $value;
    }
    
    global $mesmerize_cached;
    if ($mesmerize_cached && $changeset_status !== "publish") {
        return $mesmerize_cached;
    }
    
    $value            = mesmerize_translate_mods($value);
    $mesmerize_cached = $value;
    
    return $value;
}

function mesmerize_mods_are_changed($mesmerize_translatable_mods)
{
    $changed_mods = mesmerize_changed_mods();
    
    foreach ($mesmerize_translatable_mods as $key => $mod) {
        if (isset($changed_mods[$mod])) {
            return true;
        }
    }
    
    return false;
}

function mesmerize_changed_mods()
{
    global $wp_customize;
    
    if ($wp_customize) {
        $changeset_setting_values = $wp_customize->unsanitized_post_values(
            array(
                'exclude_post_data' => true,
                'exclude_changeset' => false,
            )
        );
        
        return $changeset_setting_values;
    }
    
    return array();
}

function mesmerize_translate_mods($value, $force = false)
{
    $mesmerize_translatable_mods = mesmerize_get_translatable_mods();
    
    
    foreach ($mesmerize_translatable_mods as $mod) {
        $cp_mod      = "CP_AUTO_SETTING[" . $mod . "]";
        $has_changed = mesmerize_mods_are_changed(array($mod, $cp_mod));
        
        if ($force || ! $has_changed) {
            if (isset($value['CP_AUTO_SETTING'][$mod])) {
                $mod_val                        = $value['CP_AUTO_SETTING'][$mod];
                $value['CP_AUTO_SETTING'][$mod] = mesmerize_translate_mod($mod, $mod_val);
            }
            
            if (isset($value[$mod])) {
                $mod_val     = $value[$mod];
                $value[$mod] = mesmerize_translate_mod($mod, $mod_val);
            }
        }
    }
    
    return $value;
}

function mesmerize_set_translate_mod($lang, $name, $value, $defaults)
{
    //mesmerize_log2("mesmerize_set_translate_mod => lang=". $lang . "; name=" . $name .";value=". json_encode($value));
    
    $langs = get_option("mesmerize_translated_mods", array());
    if ( ! isset($langs[$lang])) {
        $langs[$lang] = array();
    }
    //mesmerize_default_language_mod_value
    $value = apply_filters('mesmerize_translated_mod_value', $value, $name, $defaults);
    
    $langs[$lang][$name] = $value;
    
    update_option("mesmerize_translated_mods", $langs);
}

function mesmerize_current_language()
{
    $page_id = isset($_POST['customize_post_id']) ? intval($_POST['customize_post_id']) : -1;
    
    global $wp_customize;
    global $pagenow;
    
    // detect language for settings values//
    if ($wp_customize && 'customize.php' === $pagenow) {
        $preview_url = isset($_GET['url']) ? wp_unslash($_GET['url']) : site_url();
        $page_id     = url_to_postid($preview_url);
        
        if ( ! $page_id) {
            $page_id = -1;
        }
    }
    
    if ($page_id != -1) {
        $post_language = mesmerize_get_post_language($page_id);
    } else {
        $post_language = mesmerize_get_current_language();
    }
    
    return $post_language;
}

function mesmerize_translate_mod($name, $value)
{
    $langs = get_option("mesmerize_translated_mods", array());
    
    $post_language = mesmerize_get_current_language();
    
    if (isset($langs[$post_language]) && isset($langs[$post_language][$name])) {
        $new_value = $langs[$post_language][$name];
    } else {
        $new_value = $value;
    }
    
    //mesmerize_log2("get translation => post_language=". $post_language . "; name=" . $name .";value=". json_encode($value) . ";new_value=".json_encode($new_value));
    
    return $new_value;
}


function mesmerize_prepare_translation()
{
    add_filter("option_theme_mods_" . get_option('stylesheet'), "mesmerize_translate_get_mods", -1);
    add_filter("option_theme_mods_" . get_option('stylesheet'), "mesmerize_translate_get_initial_value");
    add_filter("pre_update_option_theme_mods_" . get_option('stylesheet'), "mesmerize_translate_set_mods", -1, 2);
}

mesmerize_prepare_translation();
