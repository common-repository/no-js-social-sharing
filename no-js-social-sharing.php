<?php
/*
Plugin Name: No JS Social Sharing
Author: Webhead LLC
Description: Social media sharing without the BS.  A light, SEO friendly way to share your posts and pages.
Version: 1.2
*/

define( 'NJSS_VERSION', '1.2' );
define( 'NJSS_PLUGIN', __FILE__);
define( 'NJSS_DIR', dirname( NJSS_PLUGIN) );

require_once( NJSS_DIR . '/inc/constants.php' );
require_once( NJSS_DIR . '/inc/general.php' );
require_once( NJSS_DIR . '/inc/public.php' );
require_once( NJSS_DIR . '/inc/options-page.php' );

