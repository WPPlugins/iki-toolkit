<?php

/**
 * Create content block custom post type.
 */
class Iki_Content_Block_CPT extends Iki_Abstract_Block_CPT {


	public function __construct() {

		parent::__construct();
		$this->post_type       = 'iki_content_block';
		$this->remove_elements = array( $this->post_type . '_vc' );
	}

	/**
	 * @param $notice
	 *
	 * @return mixed
	 */
	public function post_update_notice( $notice ) {

		$notice = parent::post_update_notice( $notice );

		$post = get_post();

		if ( $post && $post->post_type == $this->post_type ) {

			if ( isset( $notice['post'][1] ) ) {
				$notice['post'][1] = __( 'Content block updated', 'iki-toolkit' );
			}
			if ( isset( $notice['post'][6] ) ) {
				$notice['post'][6] = __( 'Content block published', 'iki-toolkit' );
			}
			if ( isset( $notice['post'][8] ) ) {
				$notice['post'][8] = __( 'Content block submitted', 'iki-toolkit' );
			}
			if ( isset( $notice['post'][9] ) ) {
				$notice['post'][9] = __( 'Content block scheduled', 'iki-toolkit' );
			}
			if ( isset( $notice['post'][10] ) ) {
				$notice['post'][10] = __( 'Content block draft updated', 'iki-toolkit' );
			}

		}

		return $notice;
	}

	/**
	 *
	 */
	public function _action_register_cpt() {
		$labels  = array(
			'name'               => _x( 'Content Blocks', 'post type general name', 'iki-toolkit' ),
			'singular_name'      => _x( 'Content Block', 'post type singular name', 'iki-toolkit' ),
			'plural_name'        => _x( 'Content Blocks', 'post type plural name', 'iki-toolkit' ),
			'add_new'            => _x( 'Add New Content Block', 'block', 'iki-toolkit' ),
			'add_new_item'       => __( 'Add New Content Block', 'iki-toolkit' ),
			'edit_item'          => __( 'Edit Content Block', 'iki-toolkit' ),
			'new_item'           => __( 'New Content Block', 'iki-toolkit' ),
			'view_item'          => __( 'View Content Block', 'iki-toolkit' ),
			'search_items'       => __( 'Search Content Blocks', 'iki-toolkit' ),
			'not_found'          => __( 'No Content Blocks Found', 'iki-toolkit' ),
			'not_found_in_trash' => __( 'No Content Blocks found in Trash', 'iki-toolkit' )
		);
		$options = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-admin-tools',
			'supports'            => array( 'title', 'editor', 'revisions' )
		);

		register_post_type( $this->post_type, $options );
	}

	public function _action_create_cpt_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$category_labels = array(
			'name'              => __( 'Content block Categories', 'iki-toolkit' ),
			'singular_name'     => __( 'Content block Category', 'iki-toolkit' ),
			'search_items'      => __( 'Search Content block Categories', 'iki-toolkit' ),
			'all_items'         => __( 'All Content block Categories', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Content block Category', 'iki-toolkit' ),
			'update_item'       => __( 'Update Content block Category', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Content block Category', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Content block Category', 'iki-toolkit' ),
			'menu_name'         => __( 'Content block Categories', 'iki-toolkit' ),
		);

		$category_args = array(
			'hierarchical'       => true,
			'labels'             => $category_labels,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'query_var'          => false,
			'publicly_queryable' => false,
			'rewrite'            => array( 'slug' => 'content_block_category' ),

		);

		register_taxonomy( 'iki_content_block_cat', array( 'iki_content_block' ), $category_args );
	}

	public function _action_create_default_taxonomies() {
		wp_insert_term( 'Menu',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for mega menu', 'iki-toolkit' ),
				'slug'        => 'menu'
			) );

		wp_insert_term( 'Author',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for author pages.', 'iki-toolkit' ),
				'slug'        => 'author'
			) );

		wp_insert_term( 'Portfolio',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for portfolio pages.', 'iki-toolkit' ),
				'slug'        => 'portfolio'
			) );
		wp_insert_term( 'Team Member',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for team member pages.', 'iki-toolkit' ),
				'slug'        => 'team_member'
			) );
		wp_insert_term( 'Portfolio project',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for Portfolio project section.',
					'iki-toolkit' ),
				'slug'        => 'portfolio_project'
			) );
		wp_insert_term( 'Global',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected everyhere ( Portfolio, Team Member etc.) Except in Portfolio Project.', 'iki-toolkit' ),
				'slug'        => 'global'
			) );

		wp_insert_term( 'Header',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for Header section.',
					'iki-toolkit' ),
				'slug'        => 'header'
			) );

		wp_insert_term( 'Hero Section',
			'iki_content_block_cat',
			array(
				'description' => __( 'For content blocks that can be selected for Hero section.',
					'iki-toolkit' ),
				'slug'        => 'hero_section'
			) );
	}
}

new Iki_Content_Block_CPT();