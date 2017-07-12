<?php

/** Handle API calls to 500px api*/
class Iki_500px_API extends Iki_Abstract_External_API {

	private $end_points = array(
		'get_user'         => 'https://api.500px.com/v1/users/show',
		'get_user_photos'  => 'https://api.500px.com/v1/photos',
		'get_user_gallery' => 'https://api.500px.com/v1/users/==iki_user_id==/galleries/==iki_gallery==/items',
		'getUserGalleries' => 'https://api.500px.com/v1/users/==iki_user==/galleries'
	);

	private $transient_keys = array(
		'get_user'         => 'iki5==user==',
		'get_user_photos'  => 'iki5==user==_n_==page==',
		'get_user_gallery' => 'iki5==user==_==page==_==gallery==',
		'getUserGalleries' => 'iki5==user==_galleries'
	);

	/**
	 * Iki_500px_API constructor.
	 *
	 * @param null $access_token API token
	 */
	public function __construct( $access_token = null ) {

		parent::__construct( $access_token );
	}

	/** Get user data
	 *
	 * @param $data array Data to pass to API
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_user( $data ) {
		$transient_key = $this->transient_keys['get_user'];
		$transient_key = str_replace( '==user==', $data['username'], $transient_key );
		$transient_key = substr( $transient_key, 0, 44 );

		return $this->handle_request( $data, $this->end_points['get_user'], $transient_key );

	}

	/** Get user photos
	 *
	 * @param $data array Data required for successful API call
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_user_photos( $data ) {
		$current_page  = ( isset( $data['page'] ) ) ? $data['page'] : 1;
		$transient_key = $this->transient_keys['get_user_photos'];
		$transient_key = str_replace( '==user==', $data['username'], $transient_key );
		$transient_key = str_replace( '==page==', $current_page, $transient_key );
		$transient_key = substr( $transient_key, 0, 44 );

		return $this->handle_request( $data, $this->end_points['get_user_photos'], $transient_key );
	}


	/** Get user gallery
	 *
	 * @param $data array Data required for successful API call
	 *
	 * @return array|int|mixed|object|string
	 */
	public function get_user_gallery( $data ) {
		$user   = false;
		$userId = '';
		if ( ! isset( $data['userId'] ) ) {
			$user = $this->get_user( $data );
			if ( isset( $user['user'] ) ) {
				$userId = $user['user']['id'];
			}
		} else {
			$userId = $data['userId'];
		}

		if ( isset( $data['cache'] ) && 'disabled' == $data['cache'] ) {

			$cache_data = false;

		} else {

			$cache_data = true;
		}

		$endPoint = str_replace( '==iki_user_id==', $userId, $this->end_points['get_user_gallery'] );
		$endPoint = str_replace( '==iki_gallery==', $data['gallery'], $endPoint );


		$current_page = ( isset( $data['page'] ) ) ? $data['page'] : 1;


		$transient_key = $this->transient_keys['get_user_gallery'];
		$transient_key = str_replace( '==user==', $data['username'], $transient_key );
		$transient_key = str_replace( '==page==', $current_page, $transient_key );
		$transient_key = str_replace( '==gallery==', $data['gallery'], $transient_key );
		$transient_key = substr( $transient_key, 0, 44 );

		$data_cache = get_transient( $transient_key );

		if ( $data_cache && $cache_data ) {
			$r = $data_cache;
		} else {

			unset( $data['gallery'] );
			unset( $data['username'] );
			$apiUrl   = $this->construct_api_url( $data, $endPoint );
			$response = wp_remote_get( $apiUrl );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$r = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $r ) ) {
				return 0;
			}
			$r = json_decode( $r, true );
			if ( $response['response']['code'] == 200 && $cache_data ) { //404 not found(user) 401 unothorized

				set_transient( $transient_key, $r, HOUR_IN_SECONDS );
				$this->update_transient_list( $transient_key );

			}
		}
		if ( $r !== 0 ) {
			$r['images'] = $r;
			$r['user']   = $user;
		}

		return $r;
	}


	/**
	 * @param $data
	 * @param $end_point
	 * @param $transient_key
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	private function handle_request( $data, $end_point, $transient_key ) {
		$r = 0;

		if ( isset( $data['username'] ) ) {

			if ( isset( $data['cache'] ) && 'disabled' == $data['cache'] ) {

				$cache_data = false;

			} else {

				$cache_data = true;
			}

			$dataCache = get_transient( $transient_key );

			if ( $dataCache && $cache_data ) {
				$r = $dataCache;
			} else {
				$apiUrl   = $this->construct_api_url( $data, $end_point );
				$response = wp_remote_get( $apiUrl );
				if ( is_wp_error( $response ) ) {
					return false;
				}
				$r = wp_remote_retrieve_body( $response );

				if ( is_wp_error( $r ) ) {
					return 0;
				}
				$r = json_decode( $r, true );
				if ( $response['response']['code'] == 200 && $cache_data ) { //404 not found(user) 401 unothorized

					set_transient( $transient_key, $r, HOUR_IN_SECONDS * 24 );
					$this->update_transient_list( $transient_key );

				}
			}

		}

		return $r;
	}

	/** Construct the API to the url
	 *
	 * @param $data
	 * @param $end_point string end point for the url
	 *
	 * @return string Final url
	 */
	public function construct_api_url( $data, $end_point ) {
		$s = '?consumer_key=' . $this->get_token();

		if ( isset( $data['image_size'] ) ) {
			//
			foreach ( $data['image_size'] as $value ) {
				$s .= '&image_size[]=' . $value;
			}

			unset ( $data['image_size'] );
		}

		foreach ( $data as $key => $value ) {

			$s .= '&' . $key . '=' . $value;
		}

		return $end_point . $s;

	}

	/** Get API token from admin options
	 * @return mixed|null
	 */
	public function get_token() {
		if ( is_null( $this->access_token ) ) {

			$token    = '';
			$api_keys = get_option( 'iki_toolkit_api_keys' );
			if ( $api_keys && isset( $api_keys['500px_api_key'] ) ) {

				$token = $api_keys['500px_api_key'];
			}

			$this->access_token = $token;
		}

		return $this->access_token;
	}
}