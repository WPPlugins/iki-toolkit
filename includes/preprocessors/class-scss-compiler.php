<?php

/**
 * Class that handles compiling sass files
 */
class Iki_Sass_Compiler {


	public static function compile( $sass ) {
		require_once( 'class-scss.php' );
		$compiler = new iki_themes_scssc();
		$compiler->setFormatter( 'iki_themes_scss_formatter_compressed' );
		try {
			return $compiler->compile( $sass );
		} catch ( Exception $e ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				throw $e;
			}

			return $sass;
		}
	}

	public static function get_sass_file( $relative_path ) {
		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			require( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
			require( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		}

		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			return '';
		}

		$wp_filesystem = new WP_Filesystem_Direct( null );
		$sass          = $wp_filesystem->get_contents( trailingslashit( get_template_directory() ) . $relative_path );
		unset( $wp_filesystem );

		// This is slower, but okay since the results will be cached indefinitely.
		if ( empty( $sass ) ) {
			$request = wp_remote_get( trailingslashit( get_template_directory_uri() ) . $relative_path );
			$sass    = wp_remote_retrieve_body( $request );
		}

		return $sass;
	}

	public static function validate( $css ) {
		return ( ! empty( $css ) && strpos( $css, '$sass-success-check' ) === false );

	}


}