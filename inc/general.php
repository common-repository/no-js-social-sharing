<?php

/**
 * Get option
 */
function njss_option($name, $default=NULL, $options = false) {
    if ( empty( $options ) ) {
        $options = get_option( 'njss_options' );
    }

    $ret = '';
    if ( !empty( $options ) && isset( $options[$name] ) ) {
        $ret = $options[$name];
    }
    else {
        if ( $default == NULL ) {
            $default = NJSS_DEFAULT_SETTINGS[$name] ?? NULL;
        }
        else {
            $ret = $default;
        }
    }
    return $ret;
}


/**
 * NJSS Shortcode
 */
function njss_shortcode( $atts, $content = null )	{
    $atts = shortcode_atts( array(
        'text'=> null,
        'networks' => null,
        'icon_size' => null,
    ), $atts, 'njss' );

    if ( !is_null ( $atts['text'] ) ) {
        $atts['sharing_title'] = esc_attr( $atts['text'] );
        unset($atts['text']);
    }
    if ( !empty ( $atts['networks'] ) ) {
        $njss_sc_networks = explode( ',', strtolower( $atts['networks'] ) );
        $atts['networks'] = array();
        foreach ( $njss_sc_networks as $njss_sc_network ) {
            $atts['networks'][ trim( $njss_sc_network ) ] = 'on';
        }
    }
    if ( !filter_var( $atts['icon_size'], FILTER_VALIDATE_INT, array( "options" => array( "min_range" => 16, "max_range" => 64 ) ) ) ) {
        $atts['icon_size'] = null;
    }

    $njss_content = njss_social_icons_share( $content, $atts );
    return $njss_content;
}
add_shortcode( 'njss', 'njss_shortcode' );


/**
 * Add Settings Link to Plugins Page
 */
function njss_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'njss-options',
		get_admin_url() . 'admin.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
add_filter( 'plugin_action_links_no-js-social-sharing/no-js-social-sharing.php', 'njss_settings_link' );
