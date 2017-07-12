<?php


/**
 * Abstract class for creating "block" custom post type.
 */
abstract class Iki_Abstract_Block_CPT {

	protected $post_type = null;

	protected $remove_elements = array();
	protected $remove_global_elements = true;

	protected $vc_present = false;
	protected $disable_frontend = true;
	protected $register_for_vc = true;

	abstract function _action_register_cpt();

	abstract function _action_create_cpt_taxonomies();

	abstract function _action_create_default_taxonomies();

	public function __construct() {

		add_action( 'init', array( $this, '_action_register_cpt' ) );
		add_action( 'init', array( $this, '_action_create_cpt_taxonomies' ), 20 );
		add_action( 'init', array( $this, '_action_create_default_taxonomies' ), 30 );
		add_filter( 'post_updated_messages', array( $this, 'post_update_notice' ) );
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}

	/**
	 * Handle visual composer before init hook
	 */
	public function vc_before_init() {

		$this->vc_present = true;
		add_action( 'current_screen', array( $this, 'current_screen' ) );
		if ( $this->register_for_vc ) {
			vc_set_default_editor_post_types( array( $this->post_type ) );
		}
	}

	/**
	 * Respond to current screen hook in admin
	 */
	public function current_screen() {
		$screen = get_current_screen();

		if ( $screen->id == $this->post_type ) {
			if ( function_exists( 'vc_remove_element' ) ) {
				Iki_Block_Utils::removeElements( $this->get_elements_to_remove(), $this->remove_global_elements );
			}
		}
	}

	/**
	 * Elements to remove from visual composer.
	 * @return array
	 */
	public function get_elements_to_remove() {
		return $this->remove_elements;
	}

	/**
	 * Respond to post update hook
	 *
	 * @param $notice
	 *
	 * @return mixed
	 */
	public function post_update_notice( $notice ) {
		return $notice;
	}
}