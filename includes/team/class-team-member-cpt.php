<?php

/**
 * Class for registering "iki_team_member" custom post type
 */
class Iki_Team_Member_CPT {


	public function __construct() {
		add_action( 'init', array( $this, 'iki_register_cpt' ), 20 );
		add_action( 'init', array( $this, 'iki_create_cpt_taxonomies' ), 20 );
	}



	/***********************************************************************************************/
	/* Create custom team member type  */
	/***********************************************************************************************/

	public function iki_register_cpt() {
		$labels = array(
			'name'               => __( 'Team Member', 'iki-toolkit' ),
			'singular_name'      => __( 'Team Member', 'iki-toolkit' ),
			'add_new'            => __( 'Add New Team Member', 'iki-toolkit' ),
			'add_new_item'       => __( 'Add New Team Member', 'iki-toolkit' ),
			'edit_item'          => __( 'Edit Team Member', 'iki-toolkit' ),
			'new_item'           => __( 'New Team Member', 'iki-toolkit' ),
			'all_items'          => __( 'All Team Members', 'iki-toolkit' ),
			'view_item'          => __( 'View Team Member', 'iki-toolkit' ),
			'search_items'       => __( 'Search Team Members', 'iki-toolkit' ),
			'not_found'          => __( 'No team members found', 'iki-toolkit' ),
			'not_found_in_trash' => __( 'No team members found in trash', 'iki-toolkit' ),
			'menu_name'          => 'Team Members'
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'taxonomies'          => array( 'iki_team_member_cat', 'iki_team_member_tag' ),
			'rewrite'             => array( 'slug' => 'team-members' )
		);
		register_post_type( 'iki_team_member', $args );

	}
	/***********************************************************************************************/
	/* Create Taxonomy  */
	/***********************************************************************************************/
	public function iki_create_cpt_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => __( 'Team Member Categories', 'iki-toolkit' ),
			'singular_name'     => __( 'Team Member Category', 'iki-toolkit' ),
			'search_items'      => __( 'Search Team Member Categories', 'iki-toolkit' ),
			'all_items'         => __( 'All Team Member Categories', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Team Member Category', 'iki-toolkit' ),
			'update_item'       => __( 'Update Team Member Category', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Team Member Category', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Team Member Category Category', 'iki-toolkit' ),
			'menu_name'         => __( 'Team Member Categories', 'iki-toolkit' ),
		);
		$args   = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'team_member_category' ),

		);

		register_taxonomy( 'iki_team_member_cat', array( 'iki_team_member' ), $args );

		$labels = array(
			'name'              => __( ' Tean Member Tag', 'iki-toolkit' ),
			'singular_name'     => __( 'Team Member Tags', 'iki-toolkit' ),
			'search_items'      => __( 'Search Tags', 'iki-toolkit' ),
			'all_items'         => __( 'All Tags', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Team Member Tag', 'iki-toolkit' ),
			'update_item'       => __( 'Update Team Member Tag', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Team Member Tag', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Team Member Tag', 'iki-toolkit' ),
			'menu_name'         => __( 'Team Member Tags', 'iki-toolkit' ),
		);
		$args   = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'iki_team_member_tag' ),

		);

		register_taxonomy( 'iki_team_member_tag', array( 'iki_team_member' ), $args );
	}
}

new Iki_Team_Member_CPT();