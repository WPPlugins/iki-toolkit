<?php

/**
 * Class that implements logic that drives the plugin.
 */
class Iki_Toolkit {

	private static $class = null;

	public static $VER = '1.0.0';

	/**@var Iki_Toolkit_Utils $toolkit_utils */
	public $toolkit_utils;

	/** Plugin uses singleton pattern, always return the same instance.
	 * @return Iki_Toolkit|null
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}


	/**
	 * Initialize the plugin
	 */
	public function init() {

		$GLOBALS['iki_toolkit'] = array();

		$this->toolkit_utils = new Iki_Toolkit_Utils();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_only_javascript' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ) );
		$this->pre_init();
		do_action( 'iki_toolkit_init', $this );
		$this->post_init();


	}

	/**
	 * Add admin css
	 */
	public function enqueue_admin_css() {

		wp_enqueue_style( 'admin-iki-toolkit', plugin_dir_url( __FILE__ ) . '../css/admin/admin-iki-toolkit.min.css' );
	}

	/**
	 * Pre init hook
	 */
	protected function pre_init() {
		do_action( 'iki_toolkit_pre_init', $this );

	}


	/**
	 * Post init hook
	 */
	protected function post_init() {
		do_action( 'iki_toolkit_post_init', $this );

	}

	/**
	 * Add admin javascript
	 */
	public function enqueue_admin_only_javascript() {
		//noop
	}

}