<?php
/**
 * Plugin Name: Vitepos - Point of sale (POS) for WooCommerce
 * Plugin URI: http://appsbd.com
 * Description: It's a Point of Sale plugin for Woocommerce, so fast and easy.
 * Version: 3.2.3
 * Author: appsbd
 * Author URI: http://www.appsbd.com
 * Text Domain: vitepos-lite
 * Domain Path: /languages
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * wc require:3.2.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Vitepos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


include_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once 'vendor/autoload.php';

use VitePos_Lite\Core\VitePosLite;

if ( true === \VitePos_Lite\Libs\Vitepos_Loader::is_ready_to_load( __FILE__ ) ) {
	require_once 'vitepos_lite/helper/plugin-helper.php';
	require_once 'vitepos_lite/core/class-viteposlite.php';
	


	$vtpos = new VitePosLite( __FILE__ );
	$vtpos->start_plugin();
}

/**
* SDK Integration
*/
