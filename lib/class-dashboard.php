<?php

namespace Ippey\BalanceOfFreeeAccount\Lib;

class Dashboard {

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
		wp_add_dashboard_widget( 'balance_of_freee_account_widget', __( 'freee 残高' ), array( $this, 'show' ) );
		wp_register_style( 'bofa_style', plugins_url( '../css/bofa.css', __FILE__ ) );
		wp_enqueue_style( 'bofa_style' );
	}

	/**
	 * Show dashboard
	 */
	public function show() {
		$access_token  = get_option( 'bofa_access_token' );
		$refresh_token = get_option( 'bofa_refresh_token' );
		if ( empty( $access_token ) ) {
			$this->showNotAvailable();

			return;
		}
		try {
			if ( $this->freee_client->valid_access_token( get_option( 'bofa_access_token' ), get_option( 'bofa_expire' ) ) == false ) {
				$result = $this->freee_client->refresh_token( $refresh_token );
				update_option( 'bofa_access_token', $result->access_token );
				update_option( 'bofa_refresh_token', $result->refresh_token );
				update_option( 'bofa_expire', time() + $result->expires_in );
			}
			$user      = $this->freee_client->get_user( $access_token );
			$companies = $user->companies;
			foreach ( $companies as $company ) {
				$wallets          = $this->freee_client->get_walletable( $access_token, $company->id );
				$company->wallets = $wallets;
			}
			require_once( __DIR__ . '/view/dashboard/view.php' );
		} catch ( \RuntimeException $e ) {
			require_once( __DIR__ . '/view/dashboard/error.php' );
		}
	}

	public function showNotAvailable() {
		$setting_url = menu_page_url( 'balance_of_freee', false );
		require_once( __DIR__ . '/view/dashboard/not-available.php' );
	}
}
