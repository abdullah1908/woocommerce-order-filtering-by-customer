<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://mrabdullahramzan.wordpress.com/
 *
 * @since      1.0.0
 * @package    Woocommerce_Order_Filtering_By_Customer
 * @subpackage Woocommerce_Order_Filtering_By_Customer/includes
 * @author     Abdullah <abdullahmzm@gmail.com>
 */
class Woocommerce_Order_Filtering_By_Customer_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-order-filtering-by-customer',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
