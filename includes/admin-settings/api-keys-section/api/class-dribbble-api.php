<?php

/*Handles Dribbble API calls*/

class Iki_Dribbble_API extends Iki_Abstract_External_API {

	private $end_points = array(
		'get_user'       => ' https://api.dribbble.com/v1/users/==user==?==access_token==',
		'get_user_shots' => 'https://api.dribbble.com/v1/users/==user==/shots/?==access_token==',
		'get_team_shots' => 'https://api.dribbble.com/v1/teams/==user==/shots/?==access_token=='
	);

	private $transient_keys = array(
		'get_user'       => 'ikid_==user==',
		'get_user_shots' => 'ikidus==user==_==page=='
	);

	/**
	 * Iki_Dribbble_API constructor.
	 *
	 * @param null $access_token API access token
	 */
	public function __construct( $access_token = null ) {
		parent::__construct( $access_token );
	}

	/**
	 * @param $data
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_user( $data ) {

		if ( $data['username'] ) {
			$data['user'] = $data['username'];
			unset( $data['username'] );
		}
		$t = $this->setup_transient( $data, $this->transient_keys['get_user'] );

		return $this->handle_request( $data, $this->end_points['get_user'], $t );

	}

	/**
	 * @param $data
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_user_shots( $data ) {
		$t = $this->setup_transient( $data, $this->transient_keys['get_user_shots'] );

		return $this->handle_request( $data, $this->end_points['get_user_shots'], $t );
	}

	/**
	 * @param $data
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_team_shots( $data ) {

		$t = $this->setup_transient( $data, $this->transient_keys['get_user_shots'] ); // same as for userShots

		return $this->handle_request( $data, $this->end_points['get_team_shots'], $t );
	}

	/**
	 * @param $data
	 * @param $transient_key
	 *
	 * @return mixed|string
	 */
	private function setup_transient( $data, $transient_key ) {

		if ( isset( $data['user'] ) ) {
			$transient_key = str_replace( '==user==', $data['user'], $transient_key );
		}

		if ( isset( $data['link'] ) ) {

			$parseUrl  = parse_url( $data['link'], PHP_URL_QUERY );
			$queryArgs = array();
			parse_str( $parseUrl, $queryArgs );


			if ( isset( $queryArgs['page'] ) ) {
				$transient_key = str_replace( '==page==', $queryArgs['page'], $transient_key );
			}
		} elseif ( isset( $data['page'] ) ) {
			$transient_key = str_replace( '==page==', $data['page'], $transient_key );
		}

		//max transient key is 45 characters
		//http://www.barrykooij.com/maximum-option-transient-key-length/
		$transient_key = $transient_key . $this->get_token();
		$transient_key = substr( $transient_key, 0, 44 );

		return $transient_key;
	}

	/** Handle the request from cache or hit the external API
	 *
	 * @param $data
	 * @param $end_point
	 * @param $transient_key
	 *
	 * @return array|int|mixed|object|string
	 */
	private function handle_request( $data, $end_point, $transient_key ) {
		// check transient
		$r = 0;

//        if (isset($data['user'])) {


		if ( isset( $data['cache'] ) && 'disabled' == $data['cache'] ) {
			$cache_data = false;
		} else {
			$cache_data = true;
		}
		if ( ! isset( $data['callback'] ) ) {
			$data['callback'] = 'bar';
		}
		$transient_key = md5( $transient_key );
		$transient_key = substr( $transient_key, 0, 44 );

		$dataCache = get_transient( $transient_key );

		if ( $dataCache ) {
			$r = $dataCache;

		} else {
			if ( isset( $data['link'] ) ) {
				$apiUrl = $data['link'];
			} else {
				$apiUrl = $this->construct_api_url( $data, $end_point );
			}
			$response = wp_remote_get( $apiUrl, array( 'timeout' => 20 ) );
			if ( is_wp_error( $response ) ) {
				return 0;
			}

			$r = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $r ) ) {
				return 0;
			}

			$r = $this->jsonp_decode( $r, true );

			//404 not found(user) 401 unothorized
			if ( $r['meta']['status'] == 200 && $cache_data && ! empty( $r['data'] ) ) {

				set_transient( $transient_key, $r, HOUR_IN_SECONDS * 24 );
				$this->update_transient_list( $transient_key );

			}


		}

//        }

		return $r;
	}

	/** Construct API url
	 *
	 * @param $replace
	 * @param $target
	 *
	 * @return string
	 */
	public function construct_api_url( $replace, $target ) {

		$r = str_replace( '==user==', $replace['user'], $target );
		$r = str_replace( '==access_token==', 'access_token=' . $this->get_token(), $r );

		unset( $replace['user'] );

		$s = '';
		foreach ( $replace as $key => $value ) {

			$s .= '&' . $key . '=' . $value;
		}

		return $r . $s;

	}

	/** get access token
	 * @return mixed|null
	 */
	public function get_token() {
		if ( is_null( $this->access_token ) ) {
			$token    = '';
			$api_keys = get_option( 'iki_toolkit_api_keys' );
			if ( $api_keys && isset( $api_keys['dribbble_api_key'] ) ) {

				$token = $api_keys['dribbble_api_key'];
			}

			$this->access_token = $token;
		}

		return $this->access_token;
	}
}