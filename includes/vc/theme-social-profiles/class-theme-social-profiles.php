<?php

/**
 * Class for creating custom visual composer element ( theme social profiles)
 * It is connected to the theme so it only works it the theme supports it.
 * Otherwise it won't show up in frontend
 */
class Iki_Theme_Social_Profiles_VC {

	protected $vc_present = false;
	protected $social_profiles = array();

	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}


	/** Fired before visual composer init
	 * @return bool
	 */
	public function vc_before_init() {

		$this->social_profiles = $this->get_social_profiles();

		if ( $this->social_profiles ) {

			$service_data = array();

			foreach ( $this->social_profiles as $service => $url ) {

				if ( ! empty( $url ) ) {
					$service_data[ $service ] = $service;
				}
			}


			vc_map( array(
				"name"             => __( "Theme Social Profiles", 'iki-toolkit' ),
				"base"             => 'iki_social_icons_vc',
				"class"            => "iki-vc-social-icons",
				"category"         => __( "Social", 'iki-toolkit' ),
				'admin_enqueue_js' => array( plugin_dir_url( __FILE__ ) . '../js/theme-profiles.js' ),
				'js_view'          => 'ikiVCThemeProfiles',
				"params"           => array(
					array(
						"type"        => "dropdown",
						"holder"      => "div",
						"class"       => "",
						"heading"     => __( "Icon Design", 'iki-toolkit' ),
						"param_name"  => "design",
						"value"       => array(
							__( 'Classic Dark', 'iki-toolkit' )          => 'classic-dark',
							__( 'Classic Light', 'iki-toolkit' )         => 'classic-light',
							__( 'Social Service Color ', 'iki-toolkit' ) => 'service',
						),
						"description" => __( "Select the design for the social buttons", 'iki-toolkit' )
					),
					array(
						"type"        => "checkbox",
						"holder"      => "div",
						"class"       => "",
						"heading"     => __( "Rounded icons", 'iki-toolkit' ),
						"param_name"  => "rounded",
						"value"       => array(
							__( 'yes', 'iki-toolkit' ) => '1',
						),
						"description" => __( 'Please note that "rounded" icon and "spread" icon options are mutually exclusive', 'iki-toolkit' )
					),
					array(
						"type"        => "checkbox",
						"holder"      => "div",
						"class"       => "",
						"heading"     => __( "Stretch icons", 'iki-toolkit' ),
						"param_name"  => "spread",
						"value"       => array(
							__( 'yes', 'iki-toolkit' ) => '1',
						),
						"description" => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive', 'iki-toolkit' )
					),
					array(
						"type"       => "dropdown",
						"holder"     => "div",
						"class"      => "",
						"heading"    => __( "Icon aligment", 'iki-toolkit' ),
						"param_name" => "aligment",
						"value"      => array(
							__( 'Left', 'iki-toolkit' )   => 'iki-left',
							__( 'Right', 'iki-toolkit' )  => 'iki-right',
							__( 'Center', 'iki-toolkit' ) => 'iki-center',
						)
					),
					array(
						"type"        => "textfield",
						"holder"      => "div",
						"class"       => "",
						"heading"     => __( "Tooltip text", 'iki-toolkit' ),
						"param_name"  => "tooltip_text",
						"value"       => '',
						"description" => __( 'Note : service name is appended at the end of the tooltip text  ', 'iki-toolkit' )
					),
					array(
						"type"       => "checkbox",
						"holder"     => "div",
						"class"      => "",
						"heading"    => __( "Select Services", 'iki-toolkit' ),
						"param_name" => "services",
						"value"      => $service_data,
					),
				)
			) );
		}

		add_shortcode( 'iki_social_icons_vc', array( $this, 'do_shortcode' ) );
	}

	/** Get social profile that are set via the active theme
	 * @return bool
	 */
	protected function get_social_profiles() {

		return get_option( 'iki_toolkit_social_profiles' );

	}

	/** Print visual composer shortcode
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {

		if ( ! is_array( $atts ) ) {
			return false;
		}
		$atts = iki_toolkit_normalize_vc_icon_data( $atts );
		$atts = iki_toolkit_parse_post_sharing_design( $atts );

		$tooltip_text = ( isset( $atts['tooltip_text'] ) ) ? $atts['tooltip_text'] : '';
		$aligment     = ( isset( $atts['aligment'] ) ) ? $atts['aligment'] : '';

		if ( ! empty( $this->social_profiles ) ) {

			$service_arr = explode( ',', $atts['services'] );

			$services = array();
			foreach ( $service_arr as $service ) {

				if ( ! empty( $this->social_profiles[ $service ] ) ) {
					$services[ $service ] = $this->social_profiles[ $service ];
				}
			}

			$result = sprintf( '<div class="%s">', sanitize_html_class( $aligment ) );
			$result .= iki_toolkit_print_social_profiles( $services, esc_html( $tooltip_text ), $atts, false );
			$result .= '</div>';

			return $result;
		} else {
			return '';
		}
	}
}

new Iki_Theme_Social_Profiles_VC();
