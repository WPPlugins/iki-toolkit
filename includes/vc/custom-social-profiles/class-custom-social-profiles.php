<?php

/**
 * Class for creating custom visual composer element (social icons)
 */
class Iki_Custom_Social_Profiles_VC {

	protected $vc_present = false;

	protected $iki_theme_exists = false;

	protected $socialProfiles = array();

	public function __construct() {
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}

	/** Fired before visual composer init
	 * @return bool
	 */
	public function vc_before_init() {

		$this->socialProfiles = iki_toolkit_get_social_profiles();

		$vcMapParams = array(
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
				"type"       => "dropdown",
				"holder"     => "div",
				"class"      => "",
				"heading"    => __( "Icon aligment", 'iki-toolkit' ),
				"param_name" => "aligment",
				"value"      => array(
					__( 'Left', 'iki-toolkit' )   => 'iki-left',
					__( 'Right', 'iki-toolkit' )  => 'iki-right',
					__( 'Center', 'iki-toolkit' ) => 'iki-center',
				),
			),
			array(
				"type"        => "textfield",
				"holder"      => "div",
				"class"       => "",
				"heading"     => __( "Tooltitp Text", 'iki-toolkit' ),
				"param_name"  => "tooltip_text",
				"value"       => '',
				"description" => __( 'Note : service name is appended at the end of the tooltip text  ', 'iki-toolkit' )
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
				"description" => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive', 'iki-toolkit' )

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
			)
		);


		foreach ( $this->socialProfiles as $service => $url ) {

			$heading = $service;
//            $service = ($service == 'google-plus') ? 'googleikiplus' : $service;

			array_push( $vcMapParams, array(
					"type"       => "textfield",
					"holder"     => "div",
					"class"      => "",
					"heading"    => $heading,
					"param_name" => $service,
					"value"      => '',
				)
			);
		}


		vc_map( array(
			"name"             => __( "Social Profiles", 'iki-toolkit' ),
			"base"             => 'iki_custom_social_icons_vc',
			"class"            => "iki-vc-social-icons",
			"category"         => __( "Social", 'iki-toolkit' ),
			'admin_enqueue_js' => array( plugin_dir_url( __FILE__ ) . '../js/custom-profiles.js' ),
			'js_view'          => 'ikiVCCustomProfiles',
			"params"           => $vcMapParams
		) );

		add_shortcode( 'iki_custom_social_icons_vc', array( $this, 'do_shortcode' ) );
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

		$services = array();
		foreach ( $this->socialProfiles as $service => $url ) {

//            $service = ($service == 'google-plus') ? 'googleikiplus' : $service;

			if ( isset( $atts[ $service ] ) ) {

//                if ($service == 'googleikiplus') {
//                    $atts['google-plus'] = $atts[$service];
//                    unset($atts[$service]);
//                    $service = 'google-plus';
//                }
				$services[ $service ] = $atts[ $service ];
			}
		}

		$result = sprintf( '<div class="%s">', sanitize_html_class( $aligment ) );
		$result .= iki_toolkit_print_social_profiles( $services, esc_html( $tooltip_text ), $atts, false );
		$result .= '</div>';

		return $result;
	}
}

new Iki_Custom_Social_Profiles_VC();