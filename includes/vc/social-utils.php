<?php

/**
 * Get available social profile services
 * @return array
 */
function iki_toolkit_get_social_profiles() {

	return array(
		'google'    => '',
		'facebook'  => '',
		'twitter'   => '',
		'linkedin'  => '',
		'vk'        => '',
		'weibo'     => '',
		'pinterest' => '',
		'reddit'    => '', // ovde sam stao.
		'tumblr'    => '',
		'lastFM'    => '',
		'myspace'   => '',
		'instagram' => '',
		'dribbble'  => '',
		'flickr'    => '',
		'500px'     => '',
		'github'    => '',
		'bitbucket' => '',
	);
}

/**
 * Get available share services
 * @return array
 */
function iki_toolkit_get_share_services() {

	return array(
		'google'    => 'https://plus.google.com/share?url=',
		'facebook'  => 'http://www.facebook.com/sharer/sharer.php?u=',
		'twitter'   => 'https://twitter.com/intent/tweet?url=',
		'linkedin'  => 'http://www.linkedin.com/shareArticle?mini=true&url=',
		'vk'        => 'http://vk.com/share.php?url=',
		'weibo'     => 'http://service.weibo.com/staticjs/weiboshare.html?url=',
		'pinterest' => 'http://pinterest.com/pin/create/button?url=',
		'reddit'    => 'http://www.reddit.com/submit?url=',
//        'tumblr' => 'http://www.tumblr.com/share/link?url=',
		'tumblr'    => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=',
		'buffer'    => 'http://bufferapp.com/add?url=',
		'digg'      => 'http://digg.com/submit?phase=2&url='
	);
}

/** Get default social icons design
 * @return array
 */
function iki_toolkit_get_default_social_design() {

	return array(
		'fg'            => '',
		'bg'            => '',
		'rounded'       => '0',
		'spread'        => '0',
		'design'        => 'dark',
		'chosen_design' => 'pre_made'
	);
}

/** Print social icons
 *
 * @param $profiles
 * @param string $link_title
 * @param null $design
 * @param bool $echo
 *
 * @return string
 */
function iki_toolkit_print_social_profiles( $profiles, $link_title = '', $design = null, $echo = true ) {
	$r = iki_toolkit_build_service_links( $profiles, $link_title, $design );
	if ( $echo ) {
		echo $r;
	} else {
		return $r;
	}
}

/**
 * Build html for service / social links
 *
 * @param $profiles
 * @param string $link_title
 * @param null $design
 *
 * @return string
 */
function iki_toolkit_build_service_links( $profiles, $link_title = '', $design = null ) {

	$ul_classes = array( 'post-social-share ', $design['class'] );
	$li_style   = '';

	$number_of_profiles = count( $profiles );

	if ( $design['spread'] == '1' ) {

		// do the spread.
		$design['rounded']   = '0';
		$ul_classes[]        = 'iki-spread-social';
		$single_spread_width = sprintf( 'width:%1$s%%;', round( 100 / $number_of_profiles, 4 ) );
		$li_style            = sprintf( 'style="%1$s"', $single_spread_width );

	}

	( $design['rounded'] == '1' ) ? $ul_classes[] = ' iki-round' : '';


	$r = '<ul class="' . Iki_Toolkit_Utils::sanitize_html_class_array( $ul_classes ) . '">';

	$fg = $design['fg'];
	$bg = $design['bg'];

	if ( ! empty( $fg ) ) {

		$color = 'color:' . $fg . ';';

		if ( 'custom_symbol' == $design['chosen_design'] ) {
			$color .= 'border-color:' . $fg . ';';
		}
		$fg = sprintf( 'style="%1$s;"', esc_attr( $color ) );
	}
	if ( ! empty( $bg ) ) {
		$bg = sprintf( 'style="background-color:%s;"', esc_attr( $bg ) );
	}


	foreach ( $profiles as $service => $url ) {

		$r .= sprintf( '<li class="iki-share-btn-wrap" %4$s data-iki-share="%1$s" ><span %3$s class="iki-sc-back iki-sc-%1$s %2$s"></span>',
			sanitize_html_class( $service ),
			sanitize_html_class( $design['class'] ),
			$bg,
			$li_style
		);

		$title = str_replace( '-', ' ', $service );
		$title = ucwords( $title );

		if ( 'Google' == $title ) {
			//change title to google plus
			$title .= ' plus';
		}

		$r .= sprintf( '<a href="%1$s" target="_blank" %3$s title="%2$s" class="iki-share-btn"><i class="%4$s"></i></a>',
			esc_url( $url ),
			esc_attr( $link_title . ' ' . $title ),
			$fg,
			'iki-icon-' . $service
		);

		$r .= '</li>';

	}
	$r .= '</ul>';

	return $r;

}

/** Parse data for social icons design
 *
 * @param null|array $design icon design data
 *
 * @return  array parsed design
 */
function iki_toolkit_parse_post_sharing_design( $design = null ) {


	if ( ! isset( $design ) ) {

		$design = iki_toolkit_get_default_social_design();

	} elseif ( isset( $design['chosen_design'] ) && isset( $design[ $design['chosen_design'] ] ) ) {

		$chosen = $design['chosen_design'];
		$design = $design[ $chosen ];

		$design['chosen_design'] = $chosen;

	}

	$design['fg']      = ( isset( $design['fg'] ) ) ? $design['fg'] : '';
	$design['bg']      = ( isset( $design['bg'] ) ) ? $design['bg'] : '';
	$design['spread']  = ( isset( $design['spread'] ) ) ? $design['spread'] : '';
	$design['rounded'] = ( isset( $design['rounded'] ) ) ? $design['rounded'] : '';


	$chosen          = isset( $design['chosen_design'] ) ? $design['chosen_design'] : 'custom';
	$design['class'] = isset( $design['class'] ) ? $design['class'] : '';
	if ( $chosen == 'custom_symbol' ) {


		$design['class'] = 'sc-custom-symbol';

	} elseif ( $chosen == 'pre_made' ) {
		$design['design'] = str_replace( 'classic-', '', $design['design'] );
		$design['class']  = 'sc-' . $design['design'];
	} elseif ( $chosen == 'custom_background' ) {

		$design['class'] = 'sc-custom-background';

	}

	return $design;
}

/** Normalize icon data.
 *
 * @param $data
 *
 * @return mixed
 */
function iki_toolkit_normalize_vc_icon_data( $data ) {
	$data['chosen_design'] = ( isset( $data['chosen_design'] ) ) ? $data['chosen_design'] : 'pre_made';
	$data['fg']            = ( isset( $data['fg'] ) ) ? $data['fg'] : '';
	$data['bg']            = ( isset( $data['bg'] ) ) ? $data['bg'] : '';
	$data['design']        = ( isset( $data['design'] ) ) ? $data['design'] : 'dark';
	$data['spread']        = ( isset( $data['spread'] ) ) ? $data['spread'] : 0;
	$data['rounded']       = ( isset( $data['rounded'] ) ) ? $data['rounded'] : 0;

	return $data;
}

/** Print share icons.
 *
 * @param null $design
 * @param null $services
 * @param bool $echo
 *
 * @return string
 */
function iki_toolkit_print_share_icons( $design = null, $services = null, $echo = true ) {
	global $wp;
	$design = iki_toolkit_parse_post_sharing_design( $design );

	$link_title = _x( 'Share on ', 'Text for the link sharing button ', 'iki-toolkit' );

	$current_url = home_url( $wp->request );
	$share_links = array();

	foreach ( $services as $service => $url ) {
		$share_links[ $service ] = $url . $current_url;
	}

	$class = ' post-sharing ';
	$r     = sprintf( '<div class="%1$s">', $class );
	$r     .= iki_toolkit_build_service_links( $share_links, $link_title, $design );
	$r     .= '</div>';

	if ( $echo ) {
		echo $r;
	} else {
		return $r;
	}

}
