<?php
/**
 * Plugin Name: WooCommerce Recent Purchased Filter SMG
 * Plugin URI: https://solomediagroup.co/
 * Description: This plugin filter will add recently purchased products by current user widget on the website
 * Author: SMG
 * Author URI: https://solomediagroup.co/
 * Version: 1.0
 * Requires at least: 4.6
 * Tested up to: 5.2
 * WC requires at least: 3.0
 * WC tested up to: 3.6.5
 * 
 * Text Domain: woo-rcp-filter 
 * Domain Path: /languages/
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Base_Plugin class
 *
 * @class Base_Plugin The class that holds the entire Base_Plugin plugin
 */
class WC_RecentPurchasedFilter_smg {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Constructor for the WC_RecentPurchasedFilter_smg class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
        add_action('woocommerce_product_query', array( $this, 'add_query_vars_filter' ), 0 );

        $this->init_hooks();
    }
    
    public function add_query_vars_filter(  $q ){
        
        if( ! isset( $_REQUEST[ 'recently_purchased' ] ) || empty( $_REQUEST[ 'recently_purchased' ])  || $_REQUEST[ 'recently_purchased' ] != '1')  //!isset($_GET['recently_purchased']) && $_GET['recently_purchased'] !== '1' )
                return;
        
         $current_user = wp_get_current_user();
            if ( 0 == $current_user->ID ) return;
        
                $customer_orders = get_posts( array(
                    'numberposts' => -1,
                    'meta_key'    => '_customer_user',
                    'meta_value'  => $current_user->ID,
                    'post_type'   => wc_get_order_types(),
                    'post_status' => array_keys( wc_get_is_paid_statuses() ),
                ) );
            
            if ( ! $customer_orders ) {
                $meta_query = $q->get( 'meta_query' );
                $meta_query[] = array(
                    'key'       => '_no_found',
                    'value' => 1,
                    'compare' => '='
                );
                $q->set( 'meta_query', $meta_query );
                return;
            }
            
                
            $products = [];
            
            foreach ( $customer_orders as $customer_order ) {
                $order = wc_get_order( $customer_order->ID );
                $items = $order->get_items();
                foreach ( $items as $item ) {
                    //$product_id = $item->get_product_id();
                    //product_ids[] = $product_id;
                    array_push( $products, $item->get_product_id() );
                }
            }
            add_filter( 'woocommerce_page_title', function($page_title){
                return $page_title.' - Products Recently Purchased';
            }, 10, 1 ); 
            
            $q->set( 'post__in', (array) $products );

    }
    

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'BASEPLUGIN_VERSION', $this->version );
        define( 'BASEPLUGIN_FILE', __FILE__ );
        define( 'BASEPLUGIN_PATH', dirname( BASEPLUGIN_FILE ) );
        define( 'BASEPLUGIN_INCLUDES', BASEPLUGIN_PATH . '/includes' );
        define( 'BASEPLUGIN_URL', plugins_url( '', BASEPLUGIN_FILE ) );
        define( 'BASEPLUGIN_ASSETS', BASEPLUGIN_URL . '/assets' );
    }

    /**
     * Initializes the WC_RecentPurchasedFilter_smg() class
     *
     * Checks for an existing WC_RecentPurchasedFilter_smg() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WC_RecentPurchasedFilter_smg();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        update_option( 'baseplugin_version', BASEPLUGIN_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {
        require_once( BASEPLUGIN_INCLUDES . '/widget.php' );

    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {
        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'baseplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


} // WC_RecentPurchasedFilter_smg

$baseplugin = WC_RecentPurchasedFilter_smg::init();
