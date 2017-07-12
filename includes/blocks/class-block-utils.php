<?php

/**
 * Class for black listing some visual composer elements (removes them from available elements)
 */
class Iki_Block_Utils {

	//elemens to ignore
	public static $ignore_elements = array(
		"vc_widget_sidebar",
		"vc_pinterest",
		'iki_content_block_vc'// NOTE - disable content blocks from showing inside other content block.
	);


	public static function removeElements( $arr, $include_global = false ) {

		$all = ( $include_global ) ? array_merge( self::$ignore_elements, $arr ) : $arr;
		foreach ( $all as $vcElement ) {
			vc_remove_element( $vcElement );
		}

	}

}
