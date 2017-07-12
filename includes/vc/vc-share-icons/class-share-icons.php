<?php

/**
 * Class for creating custom visual composer element ( share icons )
 */
class Iki_Share_Icons_VC {

	protected $share_services = array();

	public function __construct() {
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}

	/** Fired before visual composer init
	 * @return bool
	 */
	public function vc_before_init() {

		$this->share_services = iki_toolkit_get_share_services();

		if ( ! empty( $this->share_services ) ) {

			$serviceData = array();

			foreach ( $this->share_services as $service => $url ) {

				$serviceData[ $service ] = $service;

			}


			vc_map( array(
				"name"             => __( "Share services", 'iki-toolkit' ),
				"base"             => 'iki_share_icons_vc',
				"class"            => "iki-vc-share-icons",
				"category"         => __( "Social", 'iki-toolkit' ),
				'admin_enqueue_js' => array( plugin_dir_url( __FILE__ ) . '../js/share-icons.js' ),
				'js_view'          => 'ikiVCShareIcons',
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
						),
					),
					array(
						"type"        => "checkbox",
						"holder"      => "div",
						"class"       => "",
						"heading"     => __( "Services", 'iki-toolkit' ),
						"param_name"  => "services",
						"value"       => $serviceData,
						"description" => __( "Check services that you wish to be shown.", 'iki-toolkit' )
					)
				)
			) );
			add_shortcode( 'iki_share_icons_vc', array( $this, 'do_shortcode' ) );
		}

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

		$aligment = ( isset( $atts['aligment'] ) ) ? $atts['aligment'] : '';


		$services_empty = empty( $atts['services'] );

		if ( ! $services_empty ) {

			$service_arr = explode( ',', $atts['services'] );

			$services = array();
			foreach ( $service_arr as $service ) {

				$services[ $service ] = $this->share_services[ $service ];

			}
			$result = sprintf( '<div class="%s">', sanitize_html_class( $aligment ) );
			$result .= iki_toolkit_print_share_icons( $atts, $services, false );
			$result .= '</div>';

			return $result;

		} else {
			return '';
		}
	}

}

new Iki_Share_Icons_VC();
