<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:  Balance of freee account
 * Plugin URI:   https://developer.wordpress.org/plugin/balance-of-freee-account/
 * Description:  Show your balance of freee account on dashboard.
 * Version:      0.1
 * Author:       Ippei Sumida
 * Author URI:   https://unplat.info
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 *
 * "Balance of freee account" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * "Balance of freee account" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "Balance of freee account". If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */


require_once( __DIR__ . '/lib/class-dashboard.php' );
require_once( __DIR__ . '/lib/class-admin.php' );
require_once( __DIR__ . '/lib/class-freee-client.php' );

// FreeeClient
$bofa_client        = get_option( 'bofa_client_id', '' );
$bofa_client_secret = get_option( 'bofa_client_secret', '' );
$freee_client       = new \Ippey\BalanceOfFreeeAccount\Lib\Freee_Client( $bofa_client, $bofa_client_secret );

// DashBoard
$dashboard = new \Ippey\BalanceOfFreeeAccount\Lib\Dashboard();
$dashboard->set_freee_client( $freee_client );
add_action( 'wp_dashboard_setup', array( $dashboard, 'set_up' ) );

// Setting
$admin = new \Ippey\BalanceOfFreeeAccount\Lib\Admin();
$admin->set_freee_client( $freee_client );
add_action( 'admin_menu', array( $admin, 'set_up' ) );

