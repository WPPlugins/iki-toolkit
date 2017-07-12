<?php

/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 21/03/17
 * Time: 20:15
 */
class Iki_Toolkit_VC_Icons {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_add_css' ) );
	}

	public function maybe_add_css() {
		//if there is no support for vc icons
		//then we INCLUDE the default styling for the icons.
		// themes that declare support for vc icons, should bring their own styles.

		if ( ! current_theme_supports( 'iki-toolkit-vc-social-icons' ) ) {
			wp_enqueue_style( 'iki-toolkit-vc-icons', plugin_dir_url( __FILE__ ) . '../../css/public/social-icons.min.css' );
		}
	}


}

new Iki_Toolkit_VC_Icons();