<?php
/**
 *
 * @link              hhttps://wordpress.org/plugins/iki-toolkit
 * @since             1.0.0
 * @package           iki_toolkit
 *
 * @wordpress-plugin
 * Plugin Name:       Iki Toolkit
 * Plugin URI:        https://wordpress.org/plugins/iki-toolkit
 * Description:       The Iki Toolkit extends functionality to Iki Themes, providing custom post types and more.
 *
 * Version:           1.1.0
 * Author:            Ivan Vlatkovic
 * Author URI:        https://profiles.wordpress.org/iki_xx
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iki-toolkit
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



define( 'IKI_TOOLKIT_ROOT', plugin_dir_path( __FILE__ ) );
define( 'IKI_TOOLKIT_ROOT_URL', plugin_dir_url( __FILE__ ) );

/*Include classes that handle creation of plugin settings*/
require( 'includes/admin-settings/class-abstract-options-section.php' );
require( 'includes/admin-settings/classs-admin-settings.php' );
require( 'includes/admin-settings/class-social-profiles-section.php' );

//api keys section
require( 'includes/admin-settings/api-keys-section/class-api-keys-section.php' );

if ( ! class_exists( 'Iki_External_Api_Data_Check', false ) ) {
	require( 'includes/admin-settings/api-keys-section/api/class-abstract-api.php' );
	require( 'includes/admin-settings/api-keys-section/api/class-flickr-api.php' );
	require( 'includes/admin-settings/api-keys-section/api/class-dribbble-api.php' );
	require( 'includes/admin-settings/api-keys-section/api/class-500px-api.php' );
	require( 'includes/admin-settings/api-keys-section/api/class-external-api-data-check.php' );
}


// require classes for "blocks" functionality

require( 'includes/blocks/class-abstract-block-cpt.php' );
require( 'includes/blocks/class-block-utils.php' );
require( 'includes/blocks/content-blocks/class-content-block-cpt.php' );
require( 'includes/blocks/content-blocks/class-cb-factory.php' );
require( 'includes/blocks/content-blocks/class-content-block-widget.php' );

//instatiate classes for creation of option settings
new Iki_Admin_Settings();
new Iki_Social_Profiles_Admin_Section();
new Iki_API_Keys_Admin_Section();

//sass preprocessor.
require( 'includes/preprocessors/class-scss-compiler.php' );

//include menu-walker
require( 'includes/menu-walker/class-menu-admin-save.php' );
require( 'includes/menu-walker/class-walker-menu-admin.php' );

require( 'includes/utils/class-utils.php' );
require( 'includes/vc/social-utils.php' );
require( 'includes/vc/class-vc-icons.php' );
require( 'includes/vc/custom-social-profiles/class-custom-social-profiles.php' );
require( 'includes/vc/theme-social-profiles/class-theme-social-profiles.php' );
require( 'includes/vc/vc-share-icons/class-share-icons.php' );
require( 'includes/class-iki-toolkit.php' );

require( 'includes/breadcrumbs/class-iki-breadcrumbs.php' );

require( 'includes/wonder-grid/load.php' );

require( 'includes/team/class-team-member-cpt.php' );
require 'includes/portfolio/class-portfolio-cpt.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_iki_toolkit() {
	require( 'includes/iki-toolkit-activator.php' );
	Iki_Toolkit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_iki_toolkit() {
	require( 'includes/iki-toolkit-deactivator.php' );
	Iki_Toolkit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_iki_toolkit' );
register_deactivation_hook( __FILE__, 'deactivate_iki_toolkit' );

/**
 * Flush rewrite rules after cpt registraction,and only on activation of the plugin.
 */
function iki_toolkit_on_wp_init() {

	load_plugin_textdomain( 'iki-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	if ( get_option( 'iki_toolkit_flush_rewrite_rules_flag' ) ) {
		flush_rewrite_rules();
		delete_option( 'iki_toolkit_flush_rewrite_rules_flag' );
	}

}

add_action( 'init', 'iki_toolkit_on_wp_init', 30 );
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iki_toolkit() {

	Iki_Toolkit::get_instance()->init();
}


// run the plugin.
run_iki_toolkit();

