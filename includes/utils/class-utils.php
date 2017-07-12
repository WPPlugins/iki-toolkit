<?php

/**
 * Class with miscellaneous helper methods for the plugin
 * @since 1.0.0
 */
class Iki_Toolkit_Utils {


	public $ignoredProps;


	/** Sanitize nested array of value agains html class sanitization method.
	 *  This is basically a wrapper for "sanitize_html_class"
	 *
	 * @param $classes array Array of classes to sanitize
	 *
	 * @return string sanitized string
	 */
	public static function sanitize_html_class_array( $classes, $return_as_array = true ) {
		$classes = join( ' ', $classes );

		return esc_attr( $classes );
	}


	/** Convert array data to html attributes
	 *  And sanitize it
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function array_to_html_attr( $data ) {
		$s = '';
		foreach ( $data as $key => $value ) {

			if ( ! is_array( $value ) ) {
				$value = esc_attr( $value );
			}

			$s .= esc_attr( $key ) . '=' . json_encode( $value ) . ' ';

		}

		return $s;
	}

	/**
	 * @return string Current url
	 */
	public static function get_current_url() {
		static $url = null;

		if ( $url === null ) {
			$url = 'http://';

			$server_wildcard_or_regex = preg_match( '/(^~\^|^\*\.|\.\*$)/', $_SERVER['SERVER_NAME'] );

			if ( $_SERVER['SERVER_NAME'] === '_' || 1 == $server_wildcard_or_regex ) { // https://github.com/ThemeFuse/Unyson/issues/126
				$url .= $_SERVER['HTTP_HOST'];
			} else {
				$url .= $_SERVER['SERVER_NAME'];
			}

			if ( ! in_array( intval( $_SERVER['SERVER_PORT'] ), array( 80, 443 ) ) ) {
				$url .= ':' . $_SERVER['SERVER_PORT'];
			}

			$url .= $_SERVER['REQUEST_URI'];

			$url = set_url_scheme( $url ); // https fix

			if ( is_multisite() ) {
				if ( defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) {
					$site_url = parse_url( $url );

					if ( isset( $site_url['query'] ) ) {
						$url = home_url( $site_url['path'] . '?' . $site_url['query'] );
					} else {
						$url = home_url( $site_url['path'] );
					}
				}
			}
		}

		return $url;
	}
}