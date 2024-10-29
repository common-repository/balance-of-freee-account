<?php

namespace Ippey\BalanceOfFreeeAccount\Lib;

class Freee_Client {
	private $domain = 'https://api.freee.co.jp';
	private $client_id;
	private $client_secret;

	/**
	 * FreeeClient constructor.
	 *
	 * @param $client_id
	 * @param $client_secret
	 */
	public function __construct( $client_id, $client_secret ) {
		$this->set_client_id( $client_id );
		$this->set_client_secret( $client_secret );
	}

	/**
	 * set client_id
	 *
	 * @param $client_id
	 */
	public function set_client_id( $client_id ) {
		$this->client_id = $client_id;
	}

	/**
	 * set client_secret
	 *
	 * @param $client_secret
	 */
	public function set_client_secret( $client_secret ) {
		$this->client_secret = $client_secret;
	}

	/**
	 * create HTTP Headers
	 *
	 * @param $access_token
	 * @param array $params
	 *
	 * @return array
	 */
	public function create_headers( $access_token, $params = array() ) {
		$headers = array_merge( array(
			'Authorization' => 'Bearer ' . $access_token
		), $params );

		return $headers;
	}

	/**
	 * get oauth2 authorization url
	 *
	 * @param $callback_url
	 *
	 * @return string
	 */
	public function get_authorization_url( $callback_url ) {
		$url = 'https://secure.freee.co.jp/oauth/authorize?client_id=' . urlencode( $this->client_id ) . '&redirect_uri=' . urlencode( $callback_url ) . '&response_type=code';

		return $url;
	}

	/**
	 * get access token
	 *
	 * @param $code
	 * @param $callback_url
	 *
	 * @return array|mixed|object
	 */
	public function get_access_token( $code, $callback_url ) {
		$url      = $this->domain . '/oauth/token';
		$params   = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
			'code'          => $code,
			'redirect_uri'  => $callback_url,
		);
		$response = wp_remote_post( $url, array(
			'body' => $params,
		) );

		$this->check_response( $response );
		$json = json_decode( $response['body'] );

		return $json;
	}

	/**
	 * validate access token
	 *
	 * @param $access_token
	 * @param $expire
	 *
	 * @return bool
	 */
	public function valid_access_token( $access_token, $expire ) {
		if ( empty( $access_token ) ) {
			return false;
		}
		$now = time();
		if ( $now < $expire ) {
			return true;
		}

		return false;
	}

	/**
	 * refresh token
	 *
	 * @param $refresh_token
	 *
	 * @return array|mixed|object
	 */
	public function refresh_token( $refresh_token ) {
		$url    = $this->domain . '/oauth/token';
		$params = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
			'refresh_token' => $refresh_token,
		);

		$response = wp_remote_post( $url, array(
			'body' => $params,
		) );

		$this->check_response( $response );
		$json = json_decode( $response['body'] );

		return $json;
	}

	/**
	 * get user
	 *
	 * @param $access_token
	 *
	 * @return mixed
	 */
	public function get_user( $access_token ) {
		$url      = $this->domain . '/api/1/users/me?companies=true';
		$headers  = $this->create_headers( $access_token, array( 'Content-Type' => 'application/json' ) );
		$response = wp_remote_get( $url, array(
			'headers' => $headers,
		) );

		$this->check_response( $response );
		$json = json_decode( $response['body'] );

		return $json->user;
	}

	/**
	 * get walletable
	 *
	 * @param $access_token
	 * @param $company_id
	 *
	 * @return mixed
	 */
	public function get_walletable( $access_token, $company_id ) {
		$url          = $this->domain . '/api/1/walletables';
		$headers      = $this->create_headers( $access_token, array( 'Content-Type' => 'application/json' ) );
		$params       = array(
			'company_id'   => $company_id,
			'with_balance' => 'true'
		);
		$query_string = http_build_query( $params );
		$response     = wp_remote_get( $url . '?' . $query_string, array(
			'headers' => $headers,
		) );
		$this->check_response( $response );
		$json = json_decode( $response['body'] );

		return $json->walletables;
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	protected function check_response( $response ) {
		if ( $response instanceof \WP_Error ) {
			throw new \RuntimeException( $response->get_error_message() );
		} else if ( $response['response']['code'] != 200 ) {
			throw new \RuntimeException( $response['response']['body'], $response['response']['code'] );
		}

		return true;
	}
}
