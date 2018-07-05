<?php

add_shortcode('mesmerize_maps', 'mesmerize_maps');


Kirki::add_field('one_page_express', array(
    'type'     => 'text',
    'settings' => 'mesmerize_maps_api_key',
    'label'    => __('Map api key', 'mesmerize'),
    'section'  => 'page_content_settings',
    'default'  => "",
));

function mesmerize_maps_get_location($address, $force_refresh = false)
{

    $address_hash = md5($address);

    $coordinates = get_transient($address_hash);

    if ($force_refresh || $coordinates === false) {

        $args     = array('address' => urlencode($address), 'sensor' => 'false');
        $url      = add_query_arg($args, 'https://maps.googleapis.com/maps/api/geocode/json');
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return;
        }

        $data = wp_remote_retrieve_body($response);

        if (is_wp_error($data)) {
            return;
        }

        if ($response['response']['code'] == 200) {

            $data = json_decode($data);

            if ($data->status === 'OK') {

                $coordinates = $data->results[0]->geometry->location;

                $cache_value['lat']     = $coordinates->lat;
                $cache_value['lng']     = $coordinates->lng;
                $cache_value['address'] = (string)$data->results[0]->formatted_address;

                // cache coordinates for 3 months
                set_transient($address_hash, $cache_value, 3600 * 24 * 30 * 3);
                $data = $cache_value;

            } else if ($data->status === 'ZERO_RESULTS') {
                return __('No location found for the entered address.', 'pw-maps');
            } else if ($data->status === 'INVALID_REQUEST') {
                return __('Invalid request. Did you enter an address?', 'pw-maps');
            } else {
                return __('Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'pw-maps');
            }

        } else {
            return __('Unable to contact Google API service.', 'pw-maps');
        }

    } else {
        // return cached results
        $data = $coordinates;
    }

    return $data;
}

function mesmerize_maps($atts = array())
{
    $content = "";

    if (isset($atts['shortcode'])) {
        $content = do_shortcode("[" . html_entity_decode(html_entity_decode($atts['shortcode'])) . "]");
    } else {
        $atts = shortcode_atts(
            array(
                'id'      => md5(uniqid("ope-map-", true)),
                'zoom'    => '65',
                'type'    => 'ROADMAP',
                'lat'     => "",
                'lng'     => "",
                'address' => "New York",
            ),
            $atts
        );

        $id = $atts['id'];


        $location     = mesmerize_maps_get_location($atts['address']);
        $atts['zoom'] = round($atts['zoom'] * 0.21);


        if ( ! is_array($location)) {
            if ($atts['lat'] && $atts['lng']) {
                $location        = array();
                $location['lat'] = $atts['lat'];
                $location['lng'] = $atts['lng'];
            } else {
                ?>
                <p style="color:red">Google Maps Error: <?php echo $location; ?> </p>
                <?php
            }
        }

        $atts['type'] = strtoupper($atts['type']);
        if (isset($location['lat'])) {
            $atts['lat'] = floatval($location['lat']);
        }
        if (isset($location['lng'])) {
            $atts['lng'] = floatval($location['lng']);
        }


        $key = get_theme_mod('mesmerize_maps_api_key', false);
        $key = (is_string($key) && ! empty($key)) ? $key : false;

        if ($key) {


            ob_start();
            ?>
            <div data-id="<?php echo $id ?>" class="mesmerize-google-maps">
                <div class="map_content">

                </div>
                <?php if ( ! mesmerize_is_customize_preview()) :
                    wp_enqueue_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $key);

                    ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            if (window.mesmerizeRenderMap) {
                                mesmerizeRenderMap(<?php echo json_encode($atts); ?>);
                            }
                        });
                    </script>
                <?php else: ?>
                    <script>
                        (function () {
                            var hasMapScript = (jQuery('script[src*="maps.googleapis.com/maps/"]').length > 0);

                            function renderMap() {
                                if (window.mesmerizeRenderMap) {
                                    mesmerizeRenderMap(<?php echo json_encode($atts); ?>);
                                }
                            }

                            if (!hasMapScript) {
                                var s = document.createElement('script');
                                s.type = 'text/javascript';
                                s.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo $key; ?>';
                                document.head.appendChild(s);

                                if (s.attachEvent) {
                                    s.attachEvent('onload', renderMap);
                                } else {
                                    s.addEventListener('load', renderMap, false);
                                }
                            } else {
                                renderMap();
                            }
                        })();
                    </script>
                <?php endif; ?>
            </div>
            <?php
            $content = ob_get_clean();
        } else {
            $content = mesmerize_placeholder_p(__("Google Maps Placeholder<br>Google maps requires an api key" , 'mesmerize'));
        }
    }


    return $content;
}

//add_action('wp_enqueue_scripts', function () {
//    if (mesmerize_is_customize_preview()) {
//        $key = get_theme_mod('mesmerize_maps_api_key', "");
//        wp_enqueue_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $key);
//    }
//});
