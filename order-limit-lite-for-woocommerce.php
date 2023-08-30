<?php 
/**
 * Plugin Name:         Order Limit Lite for WooCommerce
 * Plugin URI:          http://www.xfinitysoft.com
 * Description:         Set Order limits i.e Minimum Order Limit for vendors, Minimum Order Limit for products, Minimum Order Limit for product categories, Minimum Order Limit for complete order. 
 * Author:              Xfinity Soft
 * Author URI:          http://www.xfinitysoft.com/
 *
 * Version:             0.0.9
 * Requires at least:   4.4.0
 * Tested up to:         6.0
 * WC requires at least: 4.0
 * WC tested up to:      6.0.1
 *
 * Text Domain:         xsollwc-domain
 * Domain Path:         /languages
 *
 * @category            Plugin
 * @author              Xfinity Soft
 * @package             WC Order Limit Lite
 */
 
 
/**
 * Check if WooCommerce is installed and activated.
 * @return ''
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;   //Exit if accessed directly.
}

function order_limit_lite_wc_activation_hook(){
	if ( !class_exists( 'WooCommerce' ) ) { 
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'WC Order Limit Lite requires WooCommerce to run. Please install WooCommerce and activate before attempting to activate again.', 'xsollwc-domain' ) );
	}
	if ( class_exists('WC_Order_limit')) { 
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices',  'xsollwc_admin_notice__error');
	}
	create_xsollwc_defaults();

}
register_activation_hook(__FILE__, 'order_limit_lite_wc_activation_hook');

function create_xsollwc_defaults(){
	$defaults = array(
					'wcol_limit_rules'      => array(),
					'wcol_customer_rules'	=> array(),
					'wcol_settings'			=> array(
						'product_limit_message'						=>	"{product-name} product minimum {applied-on} should be greater than {min-limit} and less than {max-limit}.",
						'product_limit_message_across_all_orders' 	=> 	"You can buy maximum {max-limit} items of {product-name} {time-span}, your limit is reached. You will be able to place buy more after {limit-reset-day}.",
						'product_limit_message_accomulative'		=> 	"Following products accomulative {applied-on} should be greater than {min-limit} and less than {max-limit}.<br/>{product-names}.",
						'category_limit_message'					=>	"{category-name} category item minimum {applied-on} should be greater than {min-limit} and less than {max-limit}.",
						'category_limit_message_across_all_orders'	=>	"You can buy maximum {max-limit} items of {category-name} {time-span}, your limit is reached. You will be able to place buy more after {limit-reset-day}.",
						'category_limit_message_accomulative'		=>	"Following Categorys products accomulative {applied-on} should be greater than {min-limit} and less than {max-limit}.<br/>{category-names}.",
						'vendor_limit_message'						=>	"{vendor-shop-name} item minimum {applied-on} should be greater than {min-limit} and less than {max-limit}.",
						'cart_total_limit_message'					=>	"You must have an order with a minimum of {min-limit} and maximum of {max-limit} {applied-on} to place this order.",
						'customer_message'							=>  "You can place {rule-limit} order(s) {time-span}, your limit is reached. You will be able to place your order after {limit-reset-day}.",
						'customer_message_total_amount'				=>  "You can order maximum of {rule-limit} amount {time-span}, your limit is reached. You will be able to place order after {limit-reset-day}.",
						'enable_product_limit'						=>	'on',
						'enable_category_limit'						=>	'on',
						'enable_cart_total_limit'					=>	'on',
						'enable_checkout_button'					=>	'on',
						'enable_vendor_limit'						=>	'off',
						'enable_customer_limit'						=>  'off',
						'weekly_limit_reset_date'					=>  'monday',
						'monthly_limit_reset_date'					=>	'1'
					),
					
				);
	$wcol_options = get_option('wcol_options');
	if( empty($wcol_options) || !is_array($wcol_options) ){
		add_option('wcol_options', $defaults);
	}elseif( !isset($wcol_options['wcol_settings']['customer_message']) ){
		$wcol_options['wcol_settings']['customer_message'] = "You can place {rule-limit} order(s) {time-span}, your limit is reached. You will be able to place your order after {limit-reset-day}.";
		$wcol_options['wcol_settings']['customer_message_total_amount'] = "You can order maximum of {rule-limit} amount {time-span}, your limit is reached. You will be able to place order after {limit-reset-day}.";
		$wcol_options['wcol_settings']['weekly_limit_reset_date'] = 'monday';
		$wcol_options['wcol_settings']['monthly_limit_reset_date'] = '1';
		$wcol_options['wcol_settings']['enable_customer_limit'] = 'on'; 
		update_option('wcol_options', $wcol_options);
	}elseif( !isset($wcol_options['wcol_settings']['product_limit_message_across_all_orders']) ){
		$wcol_options['wcol_settings']['product_limit_message_across_all_orders'] = "You can buy maximum {max-limit} items of {product-name} {time-span}, your limit is reached. You will be able to place buy more after {limit-reset-day}.";
		$wcol_options['wcol_settings']['product_limit_message_accomulative'] = "Following products accomulative {applied-on} should be greater than {min-limit} and less than {max-limit}.<br/>{product-names}.";
		$wcol_options['wcol_settings']['category_limit_message_across_all_orders'] = "You can buy maximum {max-limit} items of {category-name} {time-span}, your limit is reached. You will be able to place buy more after {limit-reset-day}.";
		$wcol_options['wcol_settings']['category_limit_message_accomulative'] = "Following Category's products accomulative {applied-on} should be greater than {min-limit} and less than {max-limit}.<br/>{category-name}.";
		update_option('wcol_options', $wcol_options);
	}

}
function xsollwc_admin_notice__error(){
	$class = 'notice notice-error';
    $message = esc_html__( 'WC Order Limit has activated so WC Order Limit Lite  is deactivate beacuse  both version not run at same time', 'xsollwc-domain' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

if(!class_exists('Order_limit_lite_WC')){
	class Order_limit_lite_WC{
		
		private $XSOLLWC_Admin;
		/**
		 * Constructor.
		 */
		public function __construct(){
			add_action('admin_init', array($this,'xsollwc_check_pro_version'));
			add_action('init', array($this, 'xsollwc_load_translation_files'));
			add_action('init', array($this, 'xsollwc_define_constants'));
			add_action('init', array($this, 'load_plugins_files'));
			add_action('admin_menu', array($this, 'load_xsollwc_admin_menu') );
			add_action('admin_init', array($this, 'load_xsollwc_admin') );
		}

		public function xsollwc_check_pro_version(){
			if (class_exists('WC_Order_limit')) { 
				deactivate_plugins( plugin_basename( __FILE__ ) );
				add_action( 'admin_notices','xsollwc_admin_notice__error' );
			}
		}
		
		public function xsollwc_define_constants(){
			// Let the Constants be
			define('XSOLLWC_ROOT_FILE', __FILE__);
			define('XSOLLWC_ROOT_PATH', dirname(__FILE__));
			define('XSOLLWC_ROOT_URL', plugins_url('', __FILE__));
			define('XSOLLWC_PLUGIN_SLUG', basename(dirname(__FILE__)));
			define('XSOLLWC_PLUGIN_BASE', plugin_basename(__FILE__));
		}
		
		public function load_xsollwc_admin(){
			
			add_action('woocommerce_product_write_panel_tabs', array($this->XSOLLWC_Admin, 'XSOLLWC_product_data_panel_tab') );
			
			add_action('woocommerce_product_data_panels', array($this->XSOLLWC_Admin, 'XSOLLWC_product_data_panel'));
			
			add_action('woocommerce_process_product_meta', array($this->XSOLLWC_Admin, 'process_product_meta_xsollwc_tab'), 10,2);
			
			add_action('product_cat_add_form_fields', array($this->XSOLLWC_Admin, 'XSOLLWC_product_cat_fields'), 10, 1);
			
			add_action('product_cat_edit_form_fields', array($this->XSOLLWC_Admin, 'XSOLLWC_product_cat_fields'), 10, 1);
			
			add_action('edited_product_cat', array($this->XSOLLWC_Admin, 'save_xsollwc_product_cat_fields'), 10, 1);
			
			add_action('created_product_cat', array($this->XSOLLWC_Admin, 'save_xsollwc_product_cat_fields_on_add_new'), 10, 2);

	
		}
		
		public function load_xsollwc_admin_menu(){
			$this->XSOLLWC_Admin = new XSOLLWC_Admin();
		}
		
		public function load_plugins_files(){
			include XSOLLWC_ROOT_PATH.'/includes/class-xsollwc-rule.php';
			include XSOLLWC_ROOT_PATH.'/includes/admin/class-xsollwc-admin.php';
			do_action('xsollwc_rule_inherit');
		}
		
		public function xsollwc_load_translation_files() {
			load_plugin_textdomain('xsollwc-domain', false, basename( dirname( __FILE__ ) ) . '/languages');
			
		}
	}
}
$Order_limit_lite_WC =  new Order_limit_lite_WC();