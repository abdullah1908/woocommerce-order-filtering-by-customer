<?php
/**
 * The Woocommerce_Order_History_Page class deals with Order History
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
class Woocommerce_Order_History_Page {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		//Adds Endpoint for My Account Page - Order History
		add_action('init', array($this, 'wofbc_order_history_endpoints'));

		//Adds Endpoint in My Account Menu
		add_filter('woocommerce_account_menu_items', array($this,'wofbc_order_history_my_account_menu_items'), 10);

        //Callback for Endpoint Content
		add_action('woocommerce_account_order_history_endpoint', array($this,'wofbc_order_history_endpoint_content'), 10);

        //Ajax Callback for Orders request
        add_action( 'wp_ajax_woocommerce_order_filter', array($this, 'wofbc_woocommerce_order_filter' ));
        add_action( 'wp_ajax_nopriv_woocommerce_order_filter', array($this, 'wofbc_woocommerce_order_filter' ));

	}

    /**
     * Register new endpoint to use inside My Account page.
     *
     * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
     *
     * @since    1.0.0
     */
	public function wofbc_order_history_endpoints() {
        add_rewrite_endpoint('order_history', EP_ROOT | EP_PAGES);
    }

    /**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
    public function wofbc_order_history_my_account_menu_items($items) {
        $new_items = array();
        $new_items['order_history'] = __('Order History','woocommerce-order-filtering-by-customer' );

        // Add the new item after `orders`.
        return $this->wofbc_order_history_insert_after_helper($items, $new_items, 'orders');
    }

    /**
     * Endpoint HTML content.
     */
    public function wofbc_order_history_endpoint_content() {
        $user_id = get_current_user_id();
        echo $this->wofbc_get_customer_order_list($user_id);
    }

    /*
     * Callback function that returns HTML for Orders History
     */
    public function wofbc_get_customer_order_list($user_id, $product_detail_page = false){
        if (!$user = get_userdata($user_id))
            return false;
        $my_orders_columns = apply_filters(
            'woocommerce_my_account_my_orders_columns',
            array(
                'order-number'  => esc_html__( 'Order', 'woocommerce-order-filtering-by-customer'),
                'order-date'    => esc_html__( 'Date', 'woocommerce-order-filtering-by-customer'),
                'order-status'  => esc_html__( 'Status', 'woocommerce-order-filtering-by-customer'),
                'order-total'   => esc_html__( 'Total', 'woocommerce-order-filtering-by-customer'),
                'order-actions' => '&nbsp;',
            )
        );
        $customer_orders = get_posts(
            apply_filters(
                'woocommerce_my_account_my_orders_query',
                array(
                    'numberposts' => -1,
                    'meta_key'    => '_customer_user',
                    'meta_value'  => get_current_user_id(),
                    'post_type'   => wc_get_order_types( 'view-orders' ),
                    'post_status' => array_keys( wc_get_order_statuses() ),
                )
            )
        );
        if ( $customer_orders ) : ?>
            <h3><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', esc_html__( 'Orders History', 'woocommerce-order-filtering-by-customer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
            <?php
            global $wp_locale, $wpdb;
            $extra_checks = "AND post_status ='wc-completed' ";
            $extra_checks .= "OR post_status ='wc-processing' ";
            $extra_checks .= "OR post_status ='wc-refunded' ";
            $filter_post_status = filter_input( INPUT_GET, "post_status" );
            if ( !isset( $filter_post_status ) || 'trash' !== $filter_post_status ) {
                $extra_checks .= " AND post_status != 'trash'";
            } elseif ( isset( $filter_post_status ) ) {
                $extra_checks = $wpdb->prepare( ' AND post_status = %s', $filter_post_status );
            }
            $months = $wpdb->get_results( $wpdb->prepare( " SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month FROM $wpdb->posts WHERE post_type = %s $extra_checks ORDER BY post_date DESC ", 'shop_order' ) );
            $month_count = count( $months );
            if ( $month_count < 1 ) {
                echo 'There is no sale yet!';
            } ?>
            <label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
            <select name="m" id="pt-filter-by-date">
                <option value="all">All Month</option>
                <?php
                foreach ( $months as $arc_row ) {
                    if ( 0 == $arc_row->year )
                        continue;
                    $month = zeroise( $arc_row->month, 2 );
                    $year = $arc_row->year;
                    printf( "<option %s value='%s'>%s</option>\n", selected( $month, $year . $month, false ), esc_attr( $arc_row->year . '-' . $month . '-1' ),
                        /* translators: 1: month name, 2: 4-digit year */ sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
                    );
                } ?>
            </select>
            <table class="shop_table shop_table_responsive my_account_orders">
                <thead>
                <tr>
                    <?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
                        <th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ( $customer_orders as $customer_order ) :
                    $order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    $item_count = $order->get_item_count();
                    ?>
                    <tr class="order">
                        <?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
                            <td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
                                <?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
                                    <?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

                                <?php elseif ( 'order-number' === $column_id ) : ?>
                                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
                                        <?php echo _x( '#', 'hash before order number', 'woocommerce-order-filtering-by-customer' ) . $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </a>

                                <?php elseif ( 'order-date' === $column_id ) : ?>
                                    <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>

                                <?php elseif ( 'order-status' === $column_id ) : ?>
                                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>

                                <?php elseif ( 'order-total' === $column_id ) : ?>
                                    <?php
                                    /* translators: 1: formatted order total 2: total order items */
                                    printf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce-order-filtering-by-customer' ), $order->get_formatted_order_total(), $item_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>

                                <?php elseif ( 'order-actions' === $column_id ) : ?>
                                    <?php
                                    $actions = wc_get_account_orders_actions( $order );

                                    if ( ! empty( $actions ) ) {
                                        foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                                            echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;
    }

    /**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
    public function wofbc_order_history_insert_after_helper($items, $new_items, $after) {

        // Search for the item position and +1 since is after the selected item key.
        $position = array_search($after, array_keys($items)) + 2;
        // Insert the new item.
        $array = array_slice($items, 1, $position, true);
        $array += $new_items;
        $array += array_slice($items, $position, count($items) - $position, true);
        return $array;
    }

    public function wofbc_woocommerce_order_filter() {
        $initial_date = $_POST['date'];
        $final_date = date("Y-m-t", strtotime($initial_date));
        if($initial_date == 'all'){
            $customer_orders = wc_get_orders(array(
                    'limit'=>-1,
                    'customer' => get_current_user_id(),
                    'type'=> 'shop_order',
                    'status'=> array( 'wc-completed','wc-refunded', 'wc-processing' ),
                )
            );
        } else{
            $customer_orders = wc_get_orders(array(
                    'limit'=>-1,
                    'customer' => get_current_user_id(),
                    'type'=> 'shop_order',
                    'status'=> array( 'wc-completed','wc-refunded', 'wc-processing' ),
                    'date_created'=> $initial_date .'...'. $final_date
                )
            );
        }
        if($customer_orders) {
            foreach ($customer_orders as $order_id) {
                $item_count = $order_id->get_item_count();
                $datef = esc_html(wc_format_datetime($order_id->get_date_created()));
                echo '<tr>
                    <td class="order-number" data-title="Order">
                        <a href="/my-account/view-order/' . $order_id->get_order_number() . '">#
                            ' .$order_id->get_order_number().'
                        </a>
                    </td>
                    <td class="order-date" data-title="Date">' . $datef . '</td>
                    <td class="order-status" data-title="Status">' . $order_id->status . '</td>
                    <td class="order-total" data-title="Total">
                        Rs' . $order_id->total . ' for ' . $item_count . ' items
                     </td>
                    <td class="order-actions" data-title="&nbsp;">
                        <a class="button view" href="/my-account/view-order/' . $order_id->get_order_number() . '">
                            '.esc_html__( 'View', 'woocommerce-order-filtering-by-customer').'
                        </a>
                    </td>
               </tr>';
            }
        } else{
            esc_html__( 'No Order Found', 'woocommerce-order-filtering-by-customer');
        }
        die();
    }
}

new Woocommerce_Order_History_Page();