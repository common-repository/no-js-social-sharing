<?php
/**
 * Enqueue styles
 */
function njss_enqueue() {
    $njss_selected_options = get_njss_options();
    if ( !array_key_exists( 'disable-css', $njss_selected_options ) ) {
        wp_enqueue_style( 'njss-style',
            plugins_url( '/assets/css/public.css', NJSS_PLUGIN ),
            array(),
            NJSS_VERSION );
    }
}
add_action( 'wp_enqueue_scripts', 'njss_enqueue' );


/**
 * Prints the contents of the file in the head section while still utilizing wp_enqueue.
 */
function njss_inline_style_filter( $html, $handle, $href ) {
    if ( $handle !== 'njss-style' ) {
        return $html;
    }
    $rel_path = wp_parse_url( $href, PHP_URL_PATH );
    $file_name = basename($rel_path);
    $html = '<style id="' . esc_attr( $handle ) . '-css">';
    $html .= file_get_contents( NJSS_DIR . '/assets/css/' . $file_name );
    $html .= '</style>';
    return $html;
}
add_filter( 'style_loader_tag', 'njss_inline_style_filter', 99, 3 );

/**
 * Get Plugin Seleted or Default Options
 */
function get_njss_options() {
    $njss_options = !empty( get_option( 'njss_options' ) ) ? get_option( 'njss_options' ) : NJSS_DEFAULT_SETTINGS;
    return $njss_options;
}

/**
 * Must be in loop, shows social icons to share.
 */
function njss_social_icons_share( $the_content, $njss_shortcode = null ) {
    global $post;

    if ( !is_singular( $post->post_type ) ) {
        return $the_content;
    }

    $njss_selected_options = get_njss_options();

    // Set Shortcode Attributes
    if ( isset( $njss_shortcode ) ) {
        foreach ( $njss_shortcode as $njss_sc_key => $njss_sc_value ) {
            if ( !is_null( $njss_sc_value ) ) {
                $njss_selected_options[$njss_sc_key] = $njss_sc_value ?? $njss_selected_options[$njss_sc_key];
            }
        }
    }

    $selected_networks = $njss_selected_options['networks'];
    $njss_list = array_values( array_filter( NJSS_SOCIAL_NETWORKS, function( $network ) use ( $selected_networks ) {
        return isset( $selected_networks[$network['name']] );
    } ) );
    $njss_list = apply_filters( 'njss_social_networks', $njss_list, $post );

    // Constant URL Placeholders
    $urlphs = array(
        '[[PERMALINK]]' => get_permalink(),
        '[[BLOGINFO]]' => get_bloginfo( 'title' ),
        '[[TITLE]]' => urlencode( $post->post_title ),
    );

    // Check if Post Type is Allowed
    if ( !empty( $njss_selected_options ) ) {
        $njss_display = 0;
        $current_post_type = "loc_" . get_post_type();
        if ( array_key_exists( $current_post_type, $njss_selected_options ) || isset( $njss_shortcode ) ) {
            $njss_display = 1;
        }

        // Add NJSS to Post Content
        if ( $njss_display == 1 ) {
            $njss_sharing_title = apply_filters( 'njss_sharing_title', $njss_selected_options['sharing_title'] );

            $new_content = '<div class="njss__title">' . $njss_sharing_title . '</div>';
            $new_content .= '<div class="njss__list">';
            
            $count = count( $njss_list );
            for ( $i = 0; $i < $count; $i++ ) {
                $njss_social_network = $njss_list[$i];
                $classes = ( $i == $count - 1 ) ? 'njss__link--last-child' : '';

                // Switch out URL Placeholders
                foreach ( $urlphs as $urlph => $replacement ) {
                    $njss_social_network['url'] = str_replace( $urlph, $replacement, $njss_social_network['url'] );
                }

                $icon_url = plugins_url( $njss_social_network['image'], NJSS_PLUGIN );
                $icon_size = !empty( $njss_selected_options['icon_size'] ) ? $njss_selected_options['icon_size'] : NJSS_DEFAULT_SETTINGS['icon_size'];

                $new_content .= sprintf( '<a class="njss__link %s" target="_blank" href="%s" title="%s">', 
                                    $classes,
                                    esc_url( $njss_social_network['url'] ),
                                    esc_attr( $njss_social_network['link_title'] ),
                                );
                $new_content .= sprintf( '<img loading="lazy" class="njss__img" src="%s" width="%s" height="%s" alt="%s">', 
                                      esc_url( $icon_url ), 
                                      $icon_size, 
                                      $icon_size, 
                                      esc_attr( $njss_social_network['display_name'] )
                                );
                $new_content .= '</a>';
            }
            
            $new_content .= '</div>';
            
            // Plugin Post Placement
            $updated_content = '';
            if ( isset( $njss_shortcode ) ) {
                $updated_content .= '<div class="njss njss_short">' . $new_content . '</div>'; 
                $the_content = $updated_content;
            }
            elseif ( $njss_selected_options['placement'] != 'none' ) {
                if ( in_array( $njss_selected_options['placement'], ['before-content', 'both'] ) ) {
                    $updated_content .= '<div class="njss njss_above">' . $new_content . '</div>';
                }
                $updated_content .= $the_content;
    
                if ( in_array( $njss_selected_options['placement'], ['after-content', 'both'] ) ) {
                    $updated_content .= '<div class="njss njss_below">' . $new_content . '</div>'; 
                }
                $the_content = $updated_content;
            }
        }
    }
    return $the_content;

}
add_filter( 'the_content', 'njss_social_icons_share', 99 );