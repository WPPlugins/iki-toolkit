<?php

/**
 * Handle ajax checks for valid external sevice profiles , galleries , and api keys.
 */
class Iki_External_Api_Data_Check {

	/**
	 *Check if the nonce is valid and we have all the data required to make the API calls
	 */
	public function iki_check_external_data() {

		check_ajax_referer( 'iki-admin-nonce-check' );

		$r = 0;
		if ( isset( $_POST['service'] ) && isset( $_POST['method'] ) && isset( $_POST['data'] ) ) {
			$r = $this::get_data( $_POST['service'], $_POST['method'], $_POST['data'] );
		}

		if ( $r !== false ) {
			wp_send_json( $r );

		} else {
			die( "0" );
		}
	}

	/**
	 * Pass data to appropriate service
	 *
	 * @param $service
	 * @param $method
	 * @param $data
	 *
	 * @return int|mixed
	 */
	public function get_data( $service, $method, $data ) {

		$r = 0;
		if ( $service == 'dribbble' ) {

			$r = $this->handle_dribbble_check( $method, $data );

		} elseif ( $service == '500px' ) {

			$r = $this->handle_500px_check( $method, $data );

		} elseif ( $service == 'flickr' ) {

			$r = $this->handle_flickr_check( $method, $data );

		} elseif ( 'pinterest' == $service ) {

			$r = $this->handle_pinterest_check( $method, $data );

		}

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_flickr_check( $method, $data ) {

		$api_key = ( isset( $data['api_key'] ) ? $data['api_key'] : null );

		$instance = new Iki_Flickr_Api( $api_key );
		$token    = $instance->get_token( $method, $data );
		$message  = '';
		if ( empty( $token ) ) {
			$resp = array(
				'status'  => 'failure',
				'message' => __( 'Failure: API token for Flickr service is not set.
				 Please setup your access token in wordpress customizer.', 'iki-toolkit' )
			);

		} else {

			$r = $instance->get_data( $method, $data );
			if ( 'fail' == $r['stat'] || 0 == $r ) {

				$resp = array(
					'status' => 'failure'
				);

				if ( 100 == $r['code'] ) {
					// api key fail.
					$message = __( 'Failure: Flickr API Key is incorrect, please check your key.', 'iki-toolkit' );

				} elseif ( 1 == $r['code'] || 2 == $r['code'] ) {

					if ( 'get_user' == $method ) {
						//user not found
						$message = sprintf( __( 'Failure: user "%1$s" not found', 'iki-toolkit' ), $data['user_id'] );

					} else {
						//photoset not found

						$message = sprintf( __( 'Failure: photoset "%1$s" not found', 'iki-toolkit' ), $data['photoset_id'] );
					}
				}

				$resp['message'] = $message;
			} else {
				//success

				$resp = array(
					'status' => 'success'
				);

				if ( 'get_user' == $method ) {

					$message = sprintf( __( 'Success: user "%1$s" found.', 'iki-toolkit' ), $data['user_id'] );

				} else {
					//photoset

					$message = sprintf( __( 'Success: photoset "%1$s" found.', 'iki-toolkit' ), $data['photoset_id'] );

				}

				$resp['message'] = $message;
			}
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_dribbble_check( $method, $data ) {

		$api_key = ( isset( $data['api_key'] ) ? $data['api_key'] : null );

		$instance = new Iki_Dribbble_API( $api_key );

		$token = $instance->get_token();

		if ( empty( $token ) ) {
			$resp = array(
				'status'  => 'failure',
				'message' => __( 'Failure: API token for Dribbble service is not set.
				 Please setup your access token in wordpress customizer.', 'iki-toolkit' )
			);

		} else {
			$r = $instance->get_data( $method, $data );
			if ( isset( $r['meta'] ) ) {

				if ( $r['meta']['status'] == 200 ) {

					if ( isset( $r['data']['shots_count'] ) ) {

						if ( $r['data']['shots_count'] === 0 ) {
							$resp = array(
								'status'  => 'failure',
								'message' => sprintf( __( 'Failure: user  "%1$s" found, but it appears that user doesn\'t have any shots', 'iki-toolkit' ), $data['username'] )

							);
						} else {
							$resp = array(
								'status'  => 'success',
								'message' => sprintf( __( 'Success: user  "%1$s" found', 'iki-toolkit' ), $data['username'] )

							);
						}
					}
				} elseif ( $r['meta']['status'] == 404 ) {

					$resp = array(
						'status'  => 'failure',
						'message' => sprintf( __( 'Failure: user  "%1$s" not found', 'iki-toolkit' ), $data['username'] )
					);
				} elseif ( $r['meta']['status'] == 401 ) {

					$resp = array(
						'status'  => 'failure',
						'message' => __( 'Failure: API token is wrong.', 'iki-toolkit' )
					);

				}
			} elseif ( 0 == $r ) {

				$resp = array(
					'status'  => 'failure',
					'message' => __( 'Server error or service API unavailable', 'iki-toolkit' )
				);
			}
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_500px_check( $method, $data ) {

		$api_key  = ( isset( $data['api_key'] ) ? $data['api_key'] : null );
		$instance = new Iki_500px_API( $api_key );
		$token    = $instance->get_token();

		if ( empty( $token ) ) {
			$resp = array(
				'status'  => 'failure',
				'message' => __( 'Failure: API token for 500px service is not set. Plase setup your access token in wordpress customizer.', 'iki-toolkit' )
			);

		} else {
			$r = $instance->get_data( $method, $data );
			if ( isset( $r['status'] ) ) {

				if ( $r['status'] == 404 ) {

					if ( isset( $r['user'] ) && isset( $r['user']['error'] ) && 403 == $r['user']['status'] ) {

						$resp = array(
							'status'  => 'failure',
							'message' => __( 'API key for the service is not correct.', 'iki-toolkit' )
						);
					} else {

						if ( 'get_user' == $method ) {
							$message = sprintf( __( 'Failure: user  "%1$s" not found', 'iki-toolkit' ), $data['username'] );
						} else {
							$message = sprintf( __( 'Failure: gallery  "%1$s" not found', 'iki-toolkit' ), $data['gallery'] );
						}
						$resp = array(
							'status'  => 'failure',
							'message' => $message

						);
					}
				} elseif ( $r['status'] == 403 ) {

					$resp = array(
						'status'  => 'failure',
						'message' => __( 'API key for the service is not correct.', 'iki-toolkit' )
					);
				} elseif ( 0 == $r ) {

					$resp = array(
						'status'  => 'failure',
						'message' => __( 'Server error or service API unavailable', 'iki-toolkit' )
					);
				}
			} elseif ( 'get_user' == $method && isset( $r['user'] ) && empty( $r['user']['username'] ) ) {
				//hack - there is actually user "" ( empty quotes )  - so this guards against that.
				$resp = array(
					'status'  => 'failure',
					'message' => sprintf( __( 'Failure: user  "%1$s" not found', 'iki-toolkit' ), $data['username'] )
				);
			} else {

				if ( 'get_user' == $method ) {
					$message = sprintf( __( 'Succes: user  "%1$s" found', 'iki-toolkit' ), $data['username'] );
				} else {
					$message = sprintf( __( 'Succes: gallery  "%1$s" found', 'iki-toolkit' ), $data['gallery'] );
				}
				$resp = array(
					'status'  => 'success',
					'message' => $message

				);
			}
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_pinterest_check( $method, $data ) {

		$instance = new Iki_Pinterest_API();

		$r    = $instance->get_data( $method, $data );
		$resp = $r;
		if ( isset( $r['@attributes'] ) ) {

			if ( 'get_user_latest_pins' == $method ) {

				$resp = array(
					'status'  => 'success',
					'message' => sprintf( __( 'Success: user  "%1$s" found', 'iki-toolkit' ), $data['user'] )

				);
			} elseif ( 'get_user_board' == $method ) {

				$resp = array(
					'status'  => 'success',
					'message' => sprintf( __( 'Success: "%1$s" board found.', 'iki-toolkit' ), $data['boardname'] )
				);
			}
		} else {

			if ( 0 == $r ) {

				$message = __( 'Server error or service API unavailable', 'iki-toolkit' );

			} elseif ( 'get_user_latest_pins' == $method ) {
				$message = sprintf( __( 'Failure: "%1$s" user not found.', 'iki-toolkit' ), $data['user'] );
			} else {

				$message = sprintf( __( 'Failure: "%1$s" board not found.', 'iki-toolkit' ), $data['boardname'] );
			}

			$resp = array(
				'status'  => 'failure',
				'message' => $message
			);
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 *
	 */
	public function register_ajax_callbacks() {
		add_action( 'wp_ajax_iki_check_external_data', array(
			$this,
			'iki_check_external_data'
		) );
	}
}

