<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://mrabdullahramzan.wordpress.com/
 * @since      1.0.0
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Order_Filtering_By_Customer
 * @subpackage Woocommerce_Order_Filtering_By_Customer/admin
 * @author     Abdullah <abdullahmzm@gmail.com>
 */
class Woocommerce_Order_Filtering_By_Customer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        /**
         * The class responsible for defining all the plugin settings that occur in the my account page area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-order-history-page.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * It is used to enqueue Admin Style.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-order-filtering-by-customer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * It is used to enqueue Admin Script.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-order-filtering-by-customer-admin.js', array( 'jquery' ), $this->version, false );

	}
}
