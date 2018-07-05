<?php

function mesmerize_get_slide_per_language_keys()
{
    $result = array(
        //
        // style
        //
        'slide_background_image',
        'slide_background_video',
        'slide_background_video_external',
        'slide_background_video_external',
        'slide_background_video_poster',
        
        //
        // content
        //
        
        //      texts
        'slide_title_options_title_text',
        'slide_title_options_text_animation_alternatives',
        'slide_subtitle_options_subtitle_text',
        'slide_subtitle2_options_subtitle2_text',
        'slide_buttons_options_normal_buttons',
        'slide_buttons_options_store_buttons',
        
        //      media
        'slide_media_box_settings_media_image',
        'slide_media_box_settings_content_video',
        'slide_media_box_settings_video_poster',
    
    );
    
    $result = apply_filters('mesmerize_slider_slide_per_language_options', $result);
    
    return $result;
}


function mesmerize_get_default_language_slider_settings($primary_language_value, $current_language_value)
{
    $translatableKeys   = mesmerize_get_slide_per_language_keys();
    $primary_slides_ids = array_keys($primary_language_value);
    $current_slides_ids = array_keys($current_language_value);
    
    
    // if the current language has more slides add it to the main language
    if (count($current_slides_ids) > count($primary_slides_ids)) {
        foreach ($current_slides_ids as $id) {
            if ( ! isset($primary_language_value[$id])) {
                $primary_language_value[$id] = $current_language_value[$id];
            }
        }
        
        $primary_slides_ids = $current_slides_ids;
    }
    
    
    // if the current language has less slides remove it from the main language
    if (count($current_slides_ids) < count($primary_slides_ids)) {
        foreach ($primary_slides_ids as $id) {
            if ( ! isset($current_language_value[$id])) {
                unset($primary_language_value[$id]);
            }
        }
        
        $primary_slides_ids = $current_slides_ids;
    }
    
    $keys_to_merge = null;
    
    foreach ($primary_slides_ids as $slide_id) {
        if ( ! $keys_to_merge) {
            $slide_keys    = array_keys($primary_language_value[$slide_id]);
            $keys_to_merge = array_diff($slide_keys, $translatableKeys);
        }
        
        foreach ($keys_to_merge as $key) {
            $primary_language_value[$slide_id][$key] = $current_language_value[$slide_id][$key];
        }
    }
    
    return $primary_language_value;
}


// for main language
//add_filter('mesmerize_default_language_mod_value', function ($value, $current_value) {
//
//    if (isset($value['slider_elements'])) {
//        if (isset($current_value['slider_elements'])) {
//            $value['slider_elements'] = mesmerize_get_default_language_slider_settings($value['slider_elements'], $current_value['slider_elements']);
//        }
//    }
//
//    if (isset($value['CP_AUTO_SETTING']) && isset($value['CP_AUTO_SETTING']['slider_elements'])) {
//        if (isset($current_value['CP_AUTO_SETTING']) && isset($current_value['CP_AUTO_SETTING']['slider_elements'])) {
//            $value['CP_AUTO_SETTING']['slider_elements'] = mesmerize_get_default_language_slider_settings($value['CP_AUTO_SETTING']['slider_elements'], $current_value['CP_AUTO_SETTING']['slider_elements']);
//        }
//    }
//
//    return $value;
//
//}, 10, 3);


// for other languages
//add_filter('mesmerize_translated_mod_value', function ($value, $mod, $defaults) {
//    if ($mod !== 'slider_elements') {
//        return $value;
//    }
//
//    $translatableKeys = mesmerize_get_slide_per_language_keys();
//    $keys_to_remove   = null;
//    $slide_ids        = array_keys($value);
//    $defaults_ids     = array_keys($defaults);
//
//    foreach ($slide_ids as $slide_id) {
//
//        if ( ! in_array($slide_id, $defaults_ids)) {
//            unset($value[$slide_id]);
//            continue;
//        }
//
//        if ( ! $keys_to_remove) {
//            $slide_keys     = array_keys($value[$slide_id]);
//            $keys_to_remove = array_diff($slide_keys, $translatableKeys);
//        }
//
//        foreach ($keys_to_remove as $key) {
//            unset($value[$slide_id][$key]);
//        }
//    }
//
//    return $value;
//}, 10, 3);

//add_filter("option_theme_mods_" . get_option('stylesheet'), function ($value) {
//
//
//    $slider_elements = isset($value['slider_elements']) ? $value['slider_elements'] : array();
//
//    $GLOBALS['mesmerize_slider_default_language_values'] = $slider_elements;
//
//    return $value;
//
//}, -2);
//
//
//// build the slider for the current language
//add_filter('theme_mod_slider_elements', function ($value) {
//
//
//    if (function_exists('pll_current_language') || class_exists('SitePress')) {
//        $current_language     = mesmerize_get_current_language();
//        $pll_default_language = mesmerize_get_default_language();
//
//        if ($pll_default_language !== $current_language) {
//            $result                  = array();
//            $current_language_slides = mesmerize_translate_mod("slider_elements", array());
//
//
//            if (mesmerize_is_customize_preview()) {
//                global $wp_customize;
//                if ($wp_customize->autosaved()) {
//                    return $value;
//                }
//            }
//
//            if ( ! isset($GLOBALS['mesmerize_slider_current_language_values'])) {
//                if (isset($GLOBALS['mesmerize_slider_default_language_values'])) {
//
//                    // make sure to load only the slides that are in the main language too
//                    foreach ($GLOBALS['mesmerize_slider_default_language_values'] as $slide_id => $slide) {
//                        $current_language_slide_value = isset($current_language_slides[$slide_id]) ? $current_language_slides[$slide_id] : array();
//                        $result[$slide_id]            = array_replace_recursive($slide, $current_language_slide_value);
//                    }
//
//                }
//                $GLOBALS['mesmerize_slider_current_language_values'] = $result;
//            }
//
//            $value = $GLOBALS['mesmerize_slider_current_language_values'];
//        }
//    }
//
//    return $value;
//}, 100);
