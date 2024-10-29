<?php

namespace Ippey\BalanceOfFreeeAccount\Lib;

class Admin {

	/** @var Freee_Client */
	private $freee_client;

	/**
	 * Set freee client
	 *
	 * @param $freee_client
	 */
	public function set_freee_client( $freee_client ) {
		$this->freee_client = $freee_client;
	}

	/**
	 * Setup
	 */
	public function set_up() {
		add_menu_page( 'Balance of Freee Account', 'Balance of Freee Account', 'manage_options', 'balance_of_freee', array(
			$this,
			'show'
		) );
	}

	/**
	 * Show setting form
	 */
	public function show() {
		if ( ! empty( $_GET['code'] ) ) {
			$result = $this->update_token( $_GET );
		} else if ( ! empty( $_POST ) && $_POST['action'] == 'update' && wp_verify_nonce( $_POST['bofa_setting_nonce'], 'bofa_setting_nonce' ) ) {
			$this->update_client( $_POST );
			$callback_url = menu_page_url( 'balance_of_freee', false );
			$link_url     = $this->freee_client->get_authorization_url( $callback_url );
			require_once( __DIR__ . '/view/admin/connect.php' );

			return;
		}

		$this->show_form( $result['status'], $result['message'] );
	}

	public function show_form( $status = '', $message = '' ) {
		$callback_url  = menu_page_url( 'balance_of_freee', false );
		$nonce         = wp_create_nonce( 'bofa_setting_nonce' );
		$client_id     = get_option( 'bofa_client_id' );
		$client_secret = get_option( 'bofa_client_secret' );
		require_once( __DIR__ . '/view/admin/form.php' );
	}

	/**
	 * update client_id/client_secret
	 *
	 * @param $post
	 */
	public function update_client( $post ) {
		update_option( 'bofa_client_id', $post['client_id'] );
		update_option( 'bofa_client_secret', $post['client_secret'] );
		$this->freee_client->set_client_id( $post['client_id'] );
		$this->freee_client->set_client_secret( $post['client_secret'] );
	}

	/**
	 * Update Access/Refresh token
	 *
	 * @param $get
	 *
	 * @return array
	 */
	public function update_token( $get ) {
		$callback_url = menu_page_url( 'balance_of_freee', false );
		$result       = array();
		try {
			$response = $this->freee_client->get_access_token( $get['code'], $callback_url );
			update_option( 'bofa_access_token', $response->access_token );
			update_option( 'bofa_refresh_token', $response->refresh_token );
			update_option( 'bofa_expire', time() + $response->expires_in );
			$result['status']  = 'ok';
			$result['message'] = 'freeeと連携しました。';
		} catch ( \RuntimeException $e ) {
			$result['status']  = 'ng';
			$result['message'] = 'freeeとの連携に失敗しました。再度おためしください。';
		}

		return $result;
	}
}
