<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://torontodigits.com/
 * @since             1.0.0
 * @package           Woocommerce_Order_Filtering_By_Customer
 *
 * @wordpress-plugin
 * Plugin Name:       Order Filtering by Customer
 * Plugin URI:        https://wordpress.org/plugins/order-filtering-by-customer/
 * Description:       This plugin creates a new tab in My Account Page named as Order History & allows user to filter the own orders based on month.
 * Version:           1.0.0
 * Author:            Abdullah
 * Author URI:        https://profiles.wordpress.org/torontodigits
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       order-filtering-by-customer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TD_ORDER_FILTERING_BY_CUSTOMER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-order-filtering-by-customer-activator.php
 */
function activate_woocommerce_order_filtering_by_customer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-order-filtering-by-customer-activator.php';
	Woocommerce_Order_Filtering_By_Customer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-order-filtering-by-customer-deactivator.php
 */
function deactivate_woocommerce_order_filtering_by_customer() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-order-filtering-by-customer-deactivator.php';
	Woocommerce_Order_Filtering_By_Customer_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_order_filtering_by_customer' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_order_filtering_by_customer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-order-filtering-by-customer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_order_filtering_by_customer() {

	$plugin = new Woocommerce_Order_Filtering_By_Customer();
	$plugin->run();

}
run_woocommerce_order_filtering_by_customer();
