<?php
/*********************************
 * Options page
 *********************************/


/**
 *  Add menu page
 */
function njss_options_add_page() {
	$njss_hook = add_options_page( 'No JS Social Sharing Settings', // Page title
					  'No JS Social Sharing Settings', // Label in sub-menu
					  'manage_options', // capability
					  'njss-options', // page identifier 
					  'njss_options_do_page' );// call back function name
					  
	// add_action( "admin_print_scripts-" . $njss_hook, 'njss_admin_scripts' );
}
add_action( 'admin_menu', 'njss_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function njss_options_init() {
	register_setting( 'njss_options_options', 'njss_options', 'njss_options_validate', 'njss_restore_default' );

	/**
	 * Restore Default Settings
	 */
    if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'njss-options' && isset( $_POST['njss_restore'] ) ) {
        if ( wp_verify_nonce( $_POST['nonce'], 'njss_options-restore-nonce' ) ) {
			delete_option( 'njss_options' );
			add_settings_error(
				'default_restored',
				'default_restored',
				'Defaults restored.',
				'updated'
			);
		}
	}
}
add_action( 'admin_init', 'njss_options_init' );


/**
 * Draw the menu page itself
 */
function njss_options_do_page() {
	if ( !current_user_can( 'manage_options' ) ) { 
	 wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); 
	} 
	?>
	<style>
	</style>
	<div class="wrap">
		<h2 class="njss_options_title">No JS Social Sharing Settings</h2>
		<form id="njss" method="post" action="options.php" autocomplete="off">
			<?php settings_fields( 'njss_options_options' ); ?>
			<?php $options = get_option( 'njss_options', NJSS_DEFAULT_SETTINGS ); ?>
			<div class="njss-option-section">
				<h3>Select Placement:</h3>

				<select class="njss-option-placement" name="njss_options[placement]">
					<!--<option value="" disabled selected>Please Select...</option>-->
					<option value="none" <?php selected( $options['placement'] ?? '', 'none' ) ?>>None</option>
					<option value="before-content" <?php selected( $options['placement'] ?? '', 'before-content' ) ?>>Before Content</option>
					<option value="after-content" <?php selected( $options['placement'] ?? '', 'after-content' ) ?>>After Content</option>
					<option value="both" <?php selected( $options['placement'] ?? '', 'both' ) ?>>Both</option>
				</select>
				<div class="njss-option-desc-placement">(If "None" is selected, you can still manually add social media links with the <span>[njss]</span> shortcode)</div>
			</div>
			<div class="njss-option-section">
				<h3>Select Social Networks:</h3>
			
				<table class="form-table njss-selection-table">
					<tr valign="top">
					<?php 
					foreach ( NJSS_SOCIAL_NETWORKS as $njss_social_network ) { 
						$network_name = $njss_social_network['name'];
						?>
						<td>
							<div>
								<label for="njss-option-networks-<?php echo $network_name; ?>">
									<input 
										type="checkbox" 
										id="njss-option-networks-<?php echo $network_name; ?>" 
										name="njss_options[networks][<?php echo $network_name; ?>]" 
										<?php checked( ( empty( $options ) || !empty( $options['networks'][$network_name] ) ) ? 'on' : 'off', 'on' ); ?>
									>
									<img class="njss_icons" src="<?php echo plugins_url( $njss_social_network['image'], NJSS_PLUGIN ); ?>" alt="<?php echo $njss_social_network['display_name']; ?>">
									<span><?php echo $njss_social_network['display_name']; ?></span>
								</label>
							</div>
						</td>
					<?php 
					}
					?>
					</tr>
				</table>                
			</div>
			<div class="njss-option-section">
				<h3>Select Locations:</h3>
				<div class="njss-option-locations">
					<div>
						<label for="njss-option-loc-post" class="switch">
							<input type="checkbox" id="njss-option-loc-post" name="njss_options[loc_post]"
								<?php if( !is_array( $options ) || !empty( $options['loc_post'] ) ) { ?>checked="checked"<?php } ?>>

							<div class="slider round"></div>
							<span class="slider-title">Posts</span>
						</label>
					</div>
					<div>
						<label for="njss-option-loc-page" class="switch">
							<input type="checkbox" id="njss-option-loc-page" name="njss_options[loc_page]"
								<?php if( !empty( $options['loc_page'] ) ) { ?>checked="checked"<?php } ?>>
							<div class="slider round"></div>
							<span class="slider-title">Pages</span>
						</label>
					</div>

					<?php 
					$custom_post_types = get_post_types( array('public' => true, '_builtin' => false) );
					foreach( $custom_post_types as $custom_post_type ) { ?>
					<div>
						<label for="njss-option-loc-<?php echo $custom_post_type;?>" class="switch">
							<input type="checkbox" id="njss-option-loc-<?php echo $custom_post_type;?>" name="njss_options[loc_<?php echo $custom_post_type;?>]"
								<?php if( !empty( $options['loc_' . $custom_post_type] ) ) { ?>checked="checked"<?php } ?>>
							<div class="slider round"></div>
							<span class="slider-title"><?php echo ucfirst( $custom_post_type );?></span>
						</label>
					</div>
					
					<?php
					}
					?>
				</div>
			</div>
			<div class="njss-option-section">
				<h3>Enter Sharing Text:</h3>
				<input type="text" id="njss-option-sharing-title" name="njss_options[sharing_title]"
					value="<?php echo njss_option( 'sharing_title', NULL, $options ); ?>">
			</div>
			<div class="njss-option-section">
				<h3>Enter Icon Size (px):</h3>
				<div class="njss-option-icons">
					<div>
						<input type="number" id="njss-option-icon-size" name="njss_options[icon_size]" min="16" max="64"
							value="<?php echo njss_option( 'icon_size', NULL, $options ); ?>">
						<div class="njss-option-desc-icon-size">(Between 16 - 64)</div>
					</div>
				</div>
			</div>
			<div class="njss-option-section">
				<h3>CSS Styles:</h3>
				<div class="njss-option-locations">
					<div>
						<label for="njss-option-disable-css" class="switch">
							<input type="checkbox" id="njss-option-disable-css" name="njss_options[disable-css]"
								<?php if( !empty( $options['disable-css'] ) ) { ?>checked="checked"<?php } ?>>
							<div class="slider round"></div>
							<span class="slider-title">Disable CSS Styles</span>
						</label>
						<div class="njss-option-desc-disable-css">(Selecting this option will disable the default CSS styles for this plugin)</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
			</div>
		</form>
		<form class="njss-reset-settings" method="post" action="options-general.php?page=njss-options">
            <?php wp_nonce_field( 'njss_options-restore-nonce', 'nonce' ); ?>
    		<input type="submit" class="button-primary" name="njss_restore" value="Restore Default Settings" onclick="return confirm('Are you sure you want to restore default settings?');"/>
		</form>
		<br><br><br>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function njss_options_validate( $input ) {
    if ( !empty( $_POST['njss_restore'] ) && wp_verify_nonce( $_POST['nonce'], 'njss_options-restore-nonce' ) ) {
        return $input;
    }
	
	if ( !empty( $input['sharing_title'] ) ) {
		$input['sharing_title'] =  sanitize_text_field( $input['sharing_title'] );
	}

	return $input;
}

/**
 * Use to echo scripts or things in head.  Not to enqueue
 */
function njss_admin_scripts() {
	do_action ( 'njss_admin_scripts' );
}

/**
 * Enqueue scripts for the admin side.
 */
function njss_options_enqueue_scripts( $hook ) {
	if ( 'settings_page_njss-options' != $hook )
		return;

	wp_enqueue_style( 'njss_admin', plugins_url( 'assets/css/admin.css', NJSS_PLUGIN ), '', NJSS_VERSION );

	$param = array(
		  'nonce'  => wp_create_nonce( 'njss-nonce' )
	 );
	wp_localize_script( 'njss_options', 'njss', $param );
}
add_action( 'admin_enqueue_scripts', 'njss_options_enqueue_scripts' );
