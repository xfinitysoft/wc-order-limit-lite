<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;   //Exit if accessed directly.
}

if(!class_exists('XSOLLWC_Rule')){
	class XSOLLWC_Rule{
		public function __construct(){
			add_action('wp_ajax_wcol_load_new_row', array( $this , 'wcol_load_new_row' ) );
			add_action('wp_ajax_nopriv_wcol_load_new_row', array( $this , 'wcol_load_new_row' ) );

			add_action('wp_ajax_save_rules', array( $this , 'save_rules' ) );
			add_action('wp_ajax_nopriv_save_rules' , array( $this , 'save_rules' ) );
			add_action('wp_ajax_xsollwc_support_form',array( $this , 'xsollwc_support_form'));
			add_action('wp_ajax_nopriv_xsollwc_support_form',array( $this , 'xsollwc_support_form'));
			
			// checck for order limit rules on cart page and checkout page
			add_action( 'woocommerce_checkout_process', array( $this , 'wcol_order_limit_check' ), 10);
			add_action( 'woocommerce_before_cart' , array( $this , 'wcol_order_limit_check' ), 10);
			
			// Redirect to cart page if user not fulfil rules when visits checkout page
			add_action('template_redirect', array( $this , 'wcol_restrict_checkout' ) );
			
			add_action('wp_footer', array(  $this , 'enqueue_wcol_footer_scripts' ) );
			
			$get_wcol_settings = $this->get_wcol_settings();
			
			// Hook when order is processed to save order creation date.
			add_action( 'woocommerce_checkout_order_processed', array($this, 'save_order_creation_timestamp') );
		}
		
		public function wcol_load_new_row(){
			ob_start();
			switch($_POST['row_for']){
				case 'product' :
					include XSOLLWC_ROOT_PATH.'/includes/admin/views/ajax-templates/template-products-rule-row.php';
					break;
				case 'single_product' :
					include XSOLLWC_ROOT_PATH.'/includes/admin/views/ajax-templates/template-single-product-rule-row.php';
					break;
				case 'product_cat' :
					include XSOLLWC_ROOT_PATH.'/includes/admin/views/ajax-templates/template-categories-rule-row.php';
					break;
				case 'single_product_cat' :
					include XSOLLWC_ROOT_PATH.'/includes/admin/views/ajax-templates/template-single-product-cat-rule-row.php';
					break;
			}
			$new_row = ob_get_clean();
			echo $new_row;
			die();
		}
		
		function enqueue_wcol_footer_scripts(){
			echo '<script>jQuery(".woocommerce-cart-form__cart-item.cart_item .product-remove a.remove").on("click",function(){
				jQuery(".woocommerce-error").remove();
			});</script>';
		}
		
		/*	GET All Rules	*/
		/*	@params	null	*/
		/*	returns	Array	*/
		public function get_rules(){
			$wcol_options = get_option('wcol_options');
			if(isset($wcol_options['wcol_limit_rules'])){
				return $wcol_options['wcol_limit_rules'];
			}else{
				return array();
			}
			
		}
		
		/*	GET All Settings	*/
		/*	@params	null	*/
		/*	returns	Array	*/
		public function get_wcol_settings(){
			$wcol_options = get_option('wcol_options');
			return $wcol_options['wcol_settings'];
		}
		public function xsollwc_support_form(){
			$data = array();
	        parse_str($_POST['data'], $data);
	        $data['plugin_name'] = 'WC Order limit lite';
	        $data['version'] = 'lite';
	        $data['website'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'];
	        $to = 'xfinitysoft@gmail.com';
	        switch ($data['type']) {
	            case 'report':
	                $subject = 'Report a bug';
	                break;
	            case 'hire':
	                $subject = 'Hire us to customize/develope Plugin/Theme or WordPress projects';
	                break;
	            
	            default:
	                $subject = 'Request a Feature';
	                break;
	        }
	        
	        $body = '<html><body><table>';
	        $body .='<tbody>';
	        $body .='<tr><th>User Name</th><td>'.$data['xs_name'].'</td></tr>';
	        $body .='<tr><th>User email</th><td>'.$data['xs_email'].'</td></tr>';
	        $body .='<tr><th>Plugin Name</th><td>'.$data['plugin_name'].'</td></tr>';
	        $body .='<tr><th>Version</th><td>'.$data['version'].'</td></tr>';
	        $body .='<tr><th>Website</th><td><a href="'.$data['website'].'">'.$data['website'].'</a></td></tr>';
	        $body .='<tr><th>Message</th><td>'.$data['xs_message'].'</td></tr>';
	        $body .='</tbody>';
	        $body .='</table></body></html>';
	        $headers = array('Content-Type: text/html; charset=UTF-8');
	        $params ="name=".$data['xs_name'];
	        $params.="&email=".$data['xs_email'];
	        $params.="&site=".$data['website'];
	        $params.="&version=".$data['version'];
	        $params.="&plugin_name=".$data['plugin_name'];
	        $params.="&type=".$data['type'];
	        $params.="&message=".$data['xs_message'];
	        $sever_response = wp_remote_post("https://xfinitysoft.com/wp-json/plugin/v1/quote/save/?".$params);
	        $se_api_response = json_decode( wp_remote_retrieve_body( $sever_response ), true );	
	        if($se_api_response['status']){
	            $mail = wp_mail( $to, $subject, $body, $headers );
	            wp_send_json(array('status'=>true));
	        }else{
	            wp_send_json(array('status'=>false));
	        }
	        wp_die();
		}
		
		/*	Save Rules		*/
		/*	@params	null	*/
		/*	returns	null	*/
		public function save_rules(){
			$params = array();
			if(isset($_POST['rules'])){
 				parse_str($_POST['rules'] , $params);
 			}

			$new_wcol_options = get_option('wcol_options');
			$raw_product_rules = isset($params['wcol-rules']['product-rules']) ? array_map( array($this ,'xsollwc_sanitize') , $params['wcol-rules']['product-rules']) : array('object-ids'=>'') ;
			$raw_category_rules = isset($params['wcol-rules']['category-rules']) ? array_map( array($this ,'xsollwc_sanitize') , $params['wcol-rules']['category-rules']) : array('object-ids'=>'') ;
			$wcol_rules = array();
			$wcol_product_rules = array();
			if($raw_product_rules['object-ids']!='' && is_array($raw_product_rules['object-ids']) ){
				foreach($raw_product_rules['object-ids'] as $key=>$value){
					if($value!=''){
						
						$users = (!empty($raw_product_rules['rule-users'][$key]) )?implode("," , $raw_product_rules['rule-users'][$key]):"";
						$roles = (!empty($raw_product_rules['rule-roles'][$key]) )?implode("," , $raw_product_rules['rule-roles'][$key]):"";
						$wcol_product_rules[] = array(
							'rule-id'  					=>  esc_html( $raw_product_rules['rule-id'][$key] ),
							'disable-limit'				=>	esc_html( $raw_product_rules['disable-limit'][$key] ),
							'object_ids'				=>	$value,
							'object_type'				=>	'product',
							'across-all-orders'			=>	esc_html( $raw_product_rules['across-all-orders'][$key] ),
							'wcol_min_order_limit'		=>	esc_html( $raw_product_rules['rule-limit'][$key] )!='' ? esc_html( $raw_product_rules['rule-limit'][$key] ) : 0,
							'enable-max-rule-limit'		=>	esc_html( $raw_product_rules['enable-max-rule-limit'][$key] ),
							'wcol_max_order_limit'		=>	esc_html( $raw_product_rules['max-rule-limit'][$key] )!='' ? esc_html( $raw_product_rules['max-rule-limit'][$key] ) : 0,
							'enable_time_limit'			=>	esc_html( $raw_product_rules['enable-time-limit'][$key] ),
							'wcol_rule_time_span'		=>	esc_html( $raw_product_rules['rule-time-span'][$key] ),
							'wcol_yearly_start_day'		=>  esc_html( $raw_product_rules['yearly-start-day'][$key] ),
							'wcol_yearly_start_month'	=>  esc_html( $raw_product_rules['yearly-start-month'][$key] ),
							'wcol_weekly_start_day'		=>  esc_html( $raw_product_rules['weekly-start-day'][$key] ),
							'wcol_monthly_start_date'	=>  esc_html( $raw_product_rules['monthly-start-date'][$key] ),
							'wcol_rule_start_time'		=>	esc_html( $raw_product_rules['rule-start-time'][$key] ),
							'wcol_rule_end_time'		=>	esc_html( $raw_product_rules['rule-end-time'][$key] ),
							'wcol_applied_on'			=>	esc_html( $raw_product_rules['applied-on'][$key] ),
							'accomulative'				=>	esc_html( $raw_product_rules['accomulative'][$key] ),
							'enable_for_users'			=>	esc_html( $raw_product_rules['enable-for-users'][$key] ),
							'wcol_rule_user_type'		=>	esc_html( $raw_product_rules['user-type'][$key] ),
							'wcol_rule_users'			=>	$users,
							'wcol_rule_roles'			=>	$roles,
						);
					}
				}
			}else{
				if(!isset($params['product_rules'])){
					$wcol_product_rules = $this->get_product_rules();
				}
				
			}

			$wcol_category_rules= array();
			if($raw_category_rules['object-ids']!='' && is_array($raw_category_rules['object-ids']) ){
				foreach($raw_category_rules['object-ids'] as $key=>$value){
					if($value!=''){
						$users = (!empty($raw_category_rules['rule-users'][$key]) )?implode("," , $raw_category_rules['rule-users'][$key]):"";
						$roles = (!empty($raw_category_rules['rule-roles'][$key]) )?implode("," , $raw_category_rules['rule-roles'][$key]):"";
						$wcol_category_rules[] = array(
							'rule-id'  					=>  esc_html( $raw_category_rules['rule-id'][$key] ),
							'disable-limit'				=>	esc_html( $raw_category_rules['disable-limit'][$key] ),
							'object_ids'				=>	$value,
							'object_type'				=>	'product_cat',
							'across-all-orders'			=>	esc_html( $raw_category_rules['across-all-orders'][$key] ),
							'wcol_min_order_limit'		=>	esc_html( $raw_category_rules['rule-limit'][$key] ) != '' ? esc_html( $raw_category_rules['rule-limit'][$key] ) : 0,
							'enable-max-rule-limit'		=>	esc_html( $raw_category_rules['enable-max-rule-limit'][$key] ),
							'wcol_max_order_limit'		=>	esc_html( $raw_category_rules['max-rule-limit'][$key] )!='' ? esc_html( $raw_category_rules['max-rule-limit'][$key] ) : 0,
							'enable_time_limit'			=>	esc_html( $raw_category_rules['enable-time-limit'][$key] ),
							'wcol_rule_time_span'		=>	esc_html( $raw_category_rules['rule-time-span'][$key] ),
							'wcol_yearly_start_day'		=>  esc_html( $raw_category_rules['yearly-start-day'][$key] ),
							'wcol_yearly_start_month'	=>  esc_html( $raw_category_rules['yearly-start-month'][$key] ),
							'wcol_weekly_start_day'		=>  esc_html( $raw_category_rules['weekly-start-day'][$key] ),
							'wcol_monthly_start_date'	=>  esc_html( $raw_category_rules['monthly-start-date'][$key] ),
							'wcol_rule_start_time'		=>	esc_html( $raw_category_rules['rule-start-time'][$key] ),
							'wcol_rule_end_time'		=>	esc_html( $raw_category_rules['rule-end-time'][$key] ),
							'wcol_applied_on'			=>	esc_html( $raw_category_rules['applied-on'][$key] ),
							'accomulative'				=>	esc_html( $raw_category_rules['accomulative'][$key] ),
							'enable_for_users'			=>	esc_html( $raw_category_rules['enable-for-users'][$key] ),
							'wcol_rule_user_type'		=>	esc_html( $raw_category_rules['user-type'][$key] ),
							'wcol_rule_users'			=>	$users,
							'wcol_rule_roles'			=>	$roles,
						);
					}
				}
			}else{
				if(!isset($params['category_rules'])){
					$wcol_category_rules= $this->get_category_rules();
				}
			}
			
			$wcol_customer_rules = array();
			$wcol_vendor_rules = array();					
			$wcol_rules = array_merge($wcol_product_rules, $wcol_category_rules, $wcol_vendor_rules);
			$new_wcol_options['wcol_limit_rules'] =	$wcol_rules;
			$new_wcol_options['wcol_customer_rules'] = $wcol_customer_rules;
			
			if(isset($params['settings-rules-changes'])){
				$new_wcol_options['wcol_settings'] = array(
					'product_limit_message'						=>	esc_html( $params['wcol-product-limit-message'] ),
					'product_limit_message_across_all_orders'	=>	esc_html( $params['wcol-product-limit-message-across-all-orders'] ),
					'product_limit_message_accomulative'		=>	esc_html( $params['wcol-product-limit-message-accomulative'] ),
					'category_limit_message'					=>	esc_html( $params['wcol-category-limit-message'] ),
					'category_limit_message_across_all_orders'	=>	esc_html( $params['wcol-category-limit-message-across-all-orders'] ),
					'category_limit_message_accomulative'		=>	esc_html( $params['wcol-category-limit-message-accomulative'] ),
					'vendor_limit_message'						=>	isset ($params['wcol-vendor-limit-message']) ? esc_html( $params['wcol-vendor-limit-message'] ) : '',
					'cart_total_limit_message'					=>	esc_html( $params['wcol-cart-total-limit-message'] ),
					'cart_total_minimum_limit'					=>	esc_html( $params['wcol-cart-total-min-limit'] ),
					'cart_total_enable_maximum_limit'			=>	isset($params['wcol-cart-total-enable-max-limit']) ? esc_html( $params['wcol-cart-total-enable-max-limit'] ) : '',
					'cart_total_maximum_limit'					=>	esc_html( $params['wcol-cart-total-max-limit'] ),
					'cart_total_applied_on'						=>	esc_html( $params['wcol-cart-total-applied-on'] ),
					'enable_product_limit'						=>	isset($params['wcol-enable-product-limit']) ? esc_html( $params['wcol-enable-product-limit'] ) : '',
					'enable_category_limit'						=>	isset($params['wcol-enable-category-limit']) ? esc_html( $params['wcol-enable-category-limit'] ) : '',
					'enable_vendor_limit'						=>	isset($params['wcol-enable-vendor-limit']) ? esc_html( $params['wcol-enable-vendor-limit'] ) : '',
					'enable_cart_total_limit'					=>	isset($params['wcol-enable-cart-total-limit']) ? esc_html( $params['wcol-enable-cart-total-limit'] ) : '',
					'enable_checkout_button'					=>	isset($params['wcol-enable-checkout-button']) ? esc_html( $params['wcol-enable-checkout-button'] ) : '',
					'enable_customer_limit'						=>  isset($params['wcol-enable-customer-limit']) ? esc_html( $params['wcol-enable-customer-limit'] ) : '',
					'customer_message'							=>  esc_html( $params['wcol-customer-limit-message'] ),
					'customer_message_total_amount'				=>  esc_html( $params['wcol-customer-limit-message-order-amount'] ),
					'monthly_limit_reset_date'					=>	esc_html( $params['wcol-monthly-limit-reset-date'] ),
					'weekly_limit_reset_date'					=>	esc_html( $params['wcol-weekly-limit-reset-date'] )
				);
			}
			return(update_option('wcol_options', $new_wcol_options));
		}
		
		/*	Get Product Base Rules	*/
		/*	@params	null			*/
		/*	returns	Array			*/
		public function get_product_rules(){
			$wcol_rules = self::get_rules();
			$product_rules = array();
			if(is_array($wcol_rules) ){
				foreach($wcol_rules as $rule){
					if($rule['object_type']==='product'){
						$product_rules[]=$rule;
					}
				}
			}
			return $product_rules;
		}
		
		/*	Get Category Base Rules	*/
		/*	@params	null			*/
		/*	returns	Array			*/
		public function get_category_rules(){
			$wcol_rules = self::get_rules();
			$category_rules = array();
			if(is_array($wcol_rules) ){
				foreach($wcol_rules as $rule){
					if($rule['object_type'] === 'product_cat'){
						$category_rules[] = $rule;
					}
				}
			}
			return $category_rules;
		}
		
		/*	Get get_accomulative_rules	*/
		/*	@params	string object_type	*/
		/*	returns	Array				*/
		public function get_accomulative_rules($object_type=''){
			$wcol_rules = self::get_rules();
			$accomulative_rules = array();
			switch($object_type){
				case 'product':
					$wcol_rules = self::get_product_rules();
					break;
				
				case 'product_cat':
					$wcol_rules = self::get_category_rules();
					break;
				
				default:
					$wcol_rules = self::get_rules();
					break;
			}
			if(is_array($wcol_rules) ){
				foreach($wcol_rules as $rule){
					if( isset($rule['accomulative']) && $rule['accomulative']==='on' && $rule['disable-limit']!='on'){
						if($rule['enable_for_users'] == 'on'){
							switch($rule['wcol_rule_user_type']){
								case 'guest-users':
									if(!is_user_logged_in()){
										$accomulative_rules[]=$rule;
									}
									break;
								case 'specific-users':
									if( is_user_logged_in() AND in_array( get_current_user_id(), explode( ',', $rule['wcol_rule_users'] ) ) ){
										$accomulative_rules[]=$rule;
									}
									break;
								case 'specific-roles':
									$current_user = wp_get_current_user();
									$current_user_roles = $current_user->roles;
									if(is_array($current_user_roles) ){
										foreach($current_user_roles as $role){
											if( in_array( $role, explode(',', $rule['wcol_rule_roles'] ) ) ){
												$accomulative_rules[]=$rule;
											}
										}
									}
									break;
								case 'all-users':
									$accomulative_rules[]=$rule;
								break;
							}
						}else{
							$accomulative_rules[]=$rule;
						}
					}
				}
			}
			return $accomulative_rules;
		}
		
		/*	GET wcol options	*/
		/*	@params	int,string	*/
		/*	returns	Array		*/
		public function get_wcol_options($object_id, $object_type){
			$wcol_options = self::get_rules();
			if(is_array($wcol_options )){
				foreach($wcol_options as $key=>$option){
					if( in_array($object_id, $option['object_ids']) AND $option['object_type']==$object_type ){
						$option['key']=$key;
						return $option;
					}
				}
			}
		}
		
		/*	GET wcol options form admin side	*/
		/*	@params	int,string	*/
		/*	returns	Array		*/
		public function get_wcol_options_admin($object_id, $object_type){
			$wcol_options = $this->get_rules();
			$matched_rules = array();
			if(is_array($wcol_options)){
				foreach($wcol_options as $key=>$option){
					if( in_array($object_id, $option['object_ids']) AND $option['object_type']==$object_type ){
						$option['key']=$key;
						$matched_rules[] = $option;
					}
				}
				return $matched_rules;
			}
		}
		
		/*	GET wcol option by user	*/
		/*	@params	int,string	*/
		/*	returns	Array		*/
		public function get_wcol_option_by_user($object_id, $object_type){
			$wcol_options = $this->get_rules();
			$matched_rules = array();
			if(is_array($wcol_options)){
				foreach($wcol_options as $key=>$option){
					if($option['object_type']==$object_type && $option['disable-limit']!='on' && ( in_array($object_id, $option['object_ids']) || in_array( "-1" , $option['object_ids']) ) ){
						if(isset($option['enable_for_users']) && $option['enable_for_users'] == 'on'){
							switch($option['wcol_rule_user_type']){
								case 'guest-users':
									if( !is_user_logged_in() ){
										$option['key']=$key;
										$matched_rules[] = $option;
									}
									break;
								case 'specific-users':
									if( is_user_logged_in() AND in_array( get_current_user_id(), explode( ',', $option['wcol_rule_users'] ) ) ){
										$option['key']=$key;
										$matched_rules[] =  $option;
									}
									break;
								case 'specific-roles':
									$current_user = wp_get_current_user();
									$current_user_roles = $current_user->roles;
									if(is_array($current_user_roles)){
										foreach($current_user_roles as $role){
											if( is_user_logged_in() AND in_array( $role, explode(',', $option['wcol_rule_roles'] ) ) ){
												$option['key']=$key;
												$matched_rules[] =  $option;
											}
										}
									}
									break;
								case 'all-users':
										$option['key']=$key;
										$matched_rules[] = $option;
									break;
							}
						}else{
							$option['key']=$key;
							$matched_rules[] =  $option;
						}
					}
				}
			}
			if(empty($matched_rules))
				return false;
			
			return $matched_rules;
		}
		
		/*	save wcol options	*/
		/*	@params	int,string	*/
		/*	returns	null		*/
		
		public function save_wcol_options($object_id, $object_type){
			$wcol_options = array();
			$raw_rules = isset($_POST['wcol_rules']) ? array_map(array($this , 'xsollwc_sanitize' ) , $_POST['wcol_rules'] ) : array();
			$wcol_options = array();
			if(count($raw_rules) > 0){
				if(is_array($raw_rules['wcol_min_order_limit'])){
					foreach($raw_rules['wcol_min_order_limit'] as $key=>$value){
						$users = (!empty( $raw_rules['rule-users'][$key] ) )?implode("," , $raw_rules['rule-users'][$key]):"";
						$roles = (!empty( $raw_rules['rule-roles'][$key] ) )?implode("," , $raw_rules['rule-roles'][$key]):"";					
						$wcol_options[] = array(
							'rule-id'					=>  esc_html( $raw_rules['rule-id'][$key] ),
							'disable-limit'				=>	esc_html( $raw_rules['disable-limit'][$key] ),
							'object_ids'				=>	array( $object_id ),
							'object_type'				=>	$object_type,
							'wcol_min_order_limit'		=>	esc_html( $raw_rules['wcol_min_order_limit'][$key] )!='' ? esc_html( $raw_rules['wcol_min_order_limit'][$key] ) : 0,
							'enable-max-rule-limit'		=>	esc_html( $raw_rules['enable-max-rule-limit'][$key] ),
							'wcol_max_order_limit'		=>	esc_html( $raw_rules['wcol_max_order_limit'][$key] ),
							'enable_time_limit'			=>	esc_html( $raw_rules['wcol_enable_time_limit'][$key] ),
							'across-all-orders'			=>	esc_html( $raw_rules['across-all-orders'][$key] ),
							'wcol_rule_time_span'		=>	esc_html( $raw_rules['rule-time-span'][$key] ),
							'wcol_yearly_start_day'		=>  esc_html( $raw_rules['yearly-start-day'][$key] ),
							'wcol_yearly_start_month'	=>  esc_html( $raw_rules['yearly-start-month'][$key] ),
							'wcol_weekly_start_day'		=>  esc_html( $raw_rules['weekly-start-day'][$key] ),
							'wcol_monthly_start_date'	=>  esc_html( $raw_rules['monthly-start-date'][$key] ),
							'wcol_rule_start_time'		=>	esc_html( $raw_rules['wcol_rule_start_time'][$key] ),
							'wcol_rule_end_time'		=>	esc_html( $raw_rules['wcol_rule_end_time'][$key] ),
							'wcol_applied_on'			=>	esc_html( $raw_rules['wcol_applied_on'][$key] ),
							'accomulative'				=>	esc_html( $raw_rules['accomulative'][$key] ),
							'enable_for_users'			=>	esc_html( $raw_rules['enable-for-users'][$key] ),
							'wcol_rule_user_type'		=>	esc_html( $raw_rules['user-type'][$key] ),
							'wcol_rule_users'			=>	$users,
							'wcol_rule_roles'			=>	$roles,
							'wcol_rule_key'				=>	esc_html( $raw_rules['wcol_rule_key'][$key] ),
							'wcol_deleted_rule_key' 	=>  (isset($raw_rules['wcol_deleted_rule_key'][$key]) and esc_html( $raw_rules['wcol_deleted_rule_key'][$key] ) != '') ? esc_html( $raw_rules['wcol_deleted_rule_key'][$key] ) : '',
						);
					}
				}
			}
			
			
			$rules = self::get_rules();
			if(is_array($wcol_options)){
				foreach($wcol_options as $rule){
					if( $rule['wcol_rule_key']!='' ){
						$previous_rule = $rules[$rule['wcol_rule_key']];
						if(isset($rule['wcol_deleted_rule_key']) and $rule['wcol_deleted_rule_key'] != ''){
							$object_key_in_previous_rule = array_search( $object_id, $previous_rule['object_ids'] );
							if( count($previous_rule['object_ids'])==1 && $previous_rule['object_ids'][$object_key_in_previous_rule] == $object_id ){
								unset($rules[$rule['wcol_deleted_rule_key']]);
							}else{
								unset($rules[$rule['wcol_deleted_rule_key']]['object_ids'][$object_key_in_previous_rule]);
							}
						}else{
							if( self::is_rules_equal($previous_rule , $rule) ){
								continue;
							}else{
								$object_key_in_previous_rule = array_search( $object_id, $previous_rule['object_ids'] );
								if( count($previous_rule['object_ids'])==1 && $previous_rule['object_ids'][$object_key_in_previous_rule] == $object_id ){
									unset($rules[$rule['wcol_rule_key']]);
								}else{
									unset($rules[$rule['wcol_rule_key']]['object_ids'][$object_key_in_previous_rule]);
								}
								$rule_matched = false;
								if(is_array($rules)){
									foreach($rules as $key => $previous_rule_1){
										if( self::is_rules_equal($previous_rule_1 , $rule) ){
											$rules[$key]['object_ids'][] = $object_id;
											$rule_matched = true;
											break;
										}
									}
								}
								if(!$rule_matched){
									unset($rule['wcol_rule_key']);
									$rules[] = $rule;
								}
							}
						}
					}else{
						$rule_matched = false;
						if(is_array($rules)){
							foreach($rules as $key =>$previous_rule_1){
								if( self::is_rules_equal($previous_rule_1 , $rule) ){
									$rules[$key]['object_ids'][] = $object_id;
									$rule_matched = true;
									break;
								}
							}
						}
						if(!$rule_matched){
							unset($rule['wcol_rule_key']);
							$rules[] = $rule;
						}
					}
				}
			}
			$new_wcol_options=array();
			$new_wcol_options['wcol_limit_rules']=$rules;
			$new_wcol_options['wcol_customer_rules']= array();
			$new_wcol_options['wcol_settings']=self::get_wcol_settings();
			update_option('wcol_options',$new_wcol_options);
		}
		/*	compare two rules and return true if equal else false	*/
		/*	@params Array Aray										*/
		/*	returns	Bool											*/
		public function is_rules_equal($rule1, $rule2){
			if(	$rule1['disable-limit'] 			== $rule2['disable-limit']	 		&&
				$rule1['object_type'] 				== $rule2['object_type'] 			&&
				$rule1['wcol_min_order_limit'] 		== $rule2['wcol_min_order_limit'] 	&&
				$rule1['enable-max-rule-limit'] 	== $rule2['enable-max-rule-limit'] 	&&
				$rule1['wcol_max_order_limit'] 		== $rule2['wcol_max_order_limit'] 	&&
				$rule1['enable_time_limit'] 		== $rule2['enable_time_limit'] 		&&
				$rule1['across-all-orders']			== $rule2['across-all-orders']		&&
				$rule1['wcol_rule_time_span']		== $rule2['rule-time-span']			&&
				$rule1['wcol_yearly_start_day']		== $rule2['yearly-start-day']		&&
				$rule1['wcol_yearly_start_month']	== $rule2['yearly-start-month']		&&
				$rule1['wcol_weekly_start_day']		== $rule2['weekly-start-day']		&&
				$rule1['wcol_monthly_start_date']	== $rule2['monthly-start-date']		&&
				$rule1['wcol_rule_start_time'] 		== $rule2['wcol_rule_start_time'] 	&&
				$rule1['wcol_rule_end_time'] 		== $rule2['wcol_rule_end_time'] 	&&
				$rule1['wcol_applied_on'] 			== $rule2['wcol_applied_on'] 		&&
				$rule1['accomulative'] 				== $rule2['accomulative'] 			&&
				$rule1['enable_for_users'] 			== $rule2['enable_for_users'] 		&&
				$rule1['wcol_rule_user_type'] 		== $rule2['wcol_rule_user_type'] 	&&
				$rule1['wcol_rule_users'] 			== $rule2['wcol_rule_users'] 		&&
				$rule1['wcol_rule_roles'] 			== $rule2['wcol_rule_roles']
			){
				return true;
			}else{
				return false;
			}
		}
		
		/*	Group cart totals by product cat	*/
		/*	@params	null						*/
		/*	returns	Array						*/
		public function cart_totals_by_product_cat(){
			$product_cats = array();
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				//$product_id = $_product->post->ID;
				$product_id = $cart_item['product_id'];
				$terms = wp_get_post_terms( $product_id, 'product_cat');
				if(is_array($terms)){
					foreach($terms as $term){
						$product_cats[$term->term_id][]=$cart_item;
					}
				}
			}
			$product_cats_total=array();
			if(is_array($product_cats)){
				foreach($product_cats as $term_id => $items){
					$amount = 0;
					$qty=0;
					if(is_array($items)){
						foreach($items as $item){
							$amount+=apply_filters('wcol_cart_item_total', $item['line_total'], $item, 'product_cat');
							$qty += apply_filters('wcol_cart_item_qty', $item['quantity'], $item, 'product_cat');
						}
					}
					$arg = array('amount'=>$amount, 'quantity'=>$qty);
					$product_cats_total[$term_id] = apply_filters('xswcol_product_total' , $arg , $items );
				}
			}
			return $product_cats_total;
		}

		/*  Override XSOLLWC_Rule Group cart totals by product  bookiing    */
	    /*  @params null                        */
	    /*  returns Array                       */
	    public function cart_totals_by_product_booking(){
	        $product_bookings = array();
	        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item) {
	            $product_bookings[$cart_item['product_id']][] = $cart_item;
	        }
	        $product_booking_total = array();
	        if(is_array($product_bookings)){
	            foreach($product_bookings as $product_id => $booking){
	                $amount = 0;
	                $qty = 0;
	                if(is_array($booking)){
	                    foreach($booking as $item){
	                        $amount+=apply_filters('wcol_cart_item_total', $item['line_total'], $item, 'product');
	                        $qty += apply_filters('wcol_cart_item_qty', $item['quantity'], $item, 'product');
	                    }
	                }
	                $arg = array('amount'=>$amount, 'quantity'=>$qty);
	                $product_booking_total[$product_id] = apply_filters('xswcol_product_total' , $arg , $booking );
	            }
	        }
	        return $product_booking_total;
	    }
		
		public function wcol_order_limit_check(){
			$product_cat_total = self::cart_totals_by_product_cat();
			$product_booking_total = self::cart_totals_by_product_booking();
			$wcol_settings = self::get_wcol_settings();
			WC()->session->set('is_valid_order', true);
			$default_time_zone = date_default_timezone_get();
			
				/*	*	*	*	*	*	*	*	*	*	*/
				/* 	Chech for Cart total Order Limit	*/
				/*	*	*	*	*	*	*	*	*	*	*/
			if ( isset($wcol_settings['enable_cart_total_limit']) && $wcol_settings['enable_cart_total_limit']=='on') {
				$applied = false;
										
				$applied_on = isset($wcol_settings['cart_total_applied_on'])?$wcol_settings['cart_total_applied_on']:'';
				switch($applied_on){
					case 'amount':
							$cart_total_amount = apply_filters( 'wcol_cart_total_amount', WC()->cart->total, $wcol_settings, WC()->cart );
							if($wcol_settings['cart_total_enable_maximum_limit']=='on' && ($cart_total_amount < $wcol_settings['cart_total_minimum_limit'] || $cart_total_amount > $wcol_settings['cart_total_maximum_limit']) ){
								$applied = true;
								$min_value = wc_price($wcol_settings['cart_total_minimum_limit']);
								$max_value = wc_price($wcol_settings['cart_total_maximum_limit']);
							}elseif($cart_total_amount < $wcol_settings['cart_total_minimum_limit']){
								$applied = true;
								$min_value = wc_price($wcol_settings['cart_total_minimum_limit']);
								$max_value = wc_price($wcol_settings['cart_total_maximum_limit']);
							}else{
								$applied = false;
							}
							break;
					case 'quantity':
							$cart_contents_count = apply_filters( 'wcol_cart_contents_count', WC()->cart->get_cart_contents_count(), $wcol_settings, WC()->cart );
							if($wcol_settings['cart_total_enable_maximum_limit']=='on' && ($cart_contents_count < $wcol_settings['cart_total_minimum_limit'] || $cart_contents_count > $wcol_settings['cart_total_maximum_limit']) ){
								$applied = true;
								$min_value = $wcol_settings['cart_total_minimum_limit'];
								$max_value = $wcol_settings['cart_total_maximum_limit'] != '' ? $wcol_settings['cart_total_maximum_limit'] : '';
							}elseif($cart_contents_count < $wcol_settings['cart_total_minimum_limit']){
								$applied = true;
								$min_value = $wcol_settings['cart_total_minimum_limit'];
								$max_value = $wcol_settings['cart_total_maximum_limit'];
							}else{
								$applied = false;
							}
							break;
				}
				if($applied){
					if($wcol_settings['cart_total_enable_maximum_limit']!='on'){
						$max_value = '<span style="font-size:30px; font-weight:bold;vertical-align: middle;">∞</span>';
					}
					$message = $wcol_settings['cart_total_limit_message'];
					$message = str_replace(
								array(
									 '{min-limit}',
									 '{max-limit}',
									 '{applied-on}'
								),
								array(
									$min_value,
									$max_value ,
									$applied_on
								),
								$message
							);
					if( is_cart() ) {
						wc_print_notice(apply_filters('order_total_order_limit_notice', $message,  $wcol_settings), 'error');
					} else {
						wc_add_notice(apply_filters('order_total_order_limit_notice', $message, $wcol_settings), 'error');
					}
					WC()->session->set('is_valid_order', false);
				}
			}
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
				/* 	Chech for Product Base Rules Order Limit	*/
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
			if(isset($wcol_settings['enable_product_limit']) && $wcol_settings['enable_product_limit']=='on'){
				foreach(WC()->cart->get_cart() as $cart_item_key=>$cart){
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart['data'], $cart, $cart_item_key );
					$product_id = $_product->get_id();
					$xs_pro_value = array();
					$xs_pro_value['quantity'] = apply_filters('wcol_cart_item_qty', $cart['quantity'], $cart, 'product');
					$xs_pro_value['amount'] = apply_filters('wcol_cart_item_total', $cart['line_total'], $cart, 'product');
					$rules_applied = self::is_valid_order($cart['product_id'], 'product', $xs_pro_value);
					if(is_array($rules_applied)){
						foreach($rules_applied as $rule){
							
							if($rule['wcol_applied_on']=='amount'){
								$wcol_min_value = wc_price( $rule['wcol_min_order_limit'] );
								$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? wc_price( $rule['wcol_max_order_limit'] ) : '';
							}else{
								$wcol_min_value = $rule['wcol_min_order_limit'];
								$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? $rule['wcol_max_order_limit'] : '';
							}
							if($rule['enable-max-rule-limit']!='on'){
								$wcol_max_value = '<span style="font-size:30px; font-weight:bold;vertical-align: middle;">∞</span>';
							}
						 	$message = $wcol_settings['product_limit_message'];;
							$message = str_replace(
									array(
										'{product-name}',
										'{min-limit}',
										'{max-limit}',
										'{applied-on}',
									),
									array(
										$_product->get_title(),
										$wcol_min_value,
										$wcol_max_value,
										$rule['wcol_applied_on'],
									),
									$message
							);
							if( is_cart() ) {
								wc_print_notice(apply_filters('product_order_limit_notice', $message, $product_id, $rule), 'error');
							} else {
								wc_add_notice(apply_filters('product_order_limit_notice', $message, $product_id, $rule), 'error');
							}
							WC()->session->set('is_valid_order', false);
						}
					}
				}
			}
			
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
				/* 	Chech for Category Base Rules Order Limit	*/
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
			if(isset($wcol_settings['enable_category_limit']) && $wcol_settings['enable_category_limit']=='on' && is_array($product_cat_total) ){
				foreach($product_cat_total as $term_id=>$value){
					$rules_applied = self::is_valid_order($term_id, 'product_cat', $value);
					if(is_array($rules_applied)){
						foreach($rules_applied as $rule){
							if($rule['wcol_applied_on']=='amount'){
								$wcol_min_value = wc_price( $rule['wcol_min_order_limit'] );
								$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? wc_price( $rule['wcol_max_order_limit'] ) : '';
							}else{
								$wcol_min_value = $rule['wcol_min_order_limit'];
								$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? $rule['wcol_max_order_limit'] : '';
							}
							$term = get_term( $term_id, 'product_cat');
							
							if($rule['enable-max-rule-limit']!='on'){
								$wcol_max_value = '<span style="font-size:30px; font-weight:bold;vertical-align: middle;">∞</span>';
							}
							$message = $wcol_settings['category_limit_message'];
							$message = str_replace(
									array(
										'{category-name}',
										'{min-limit}',
										'{max-limit}',
										'{applied-on}',
									),
									array(
										$term->name,
										$wcol_min_value,
										$wcol_max_value,
										$rule['wcol_applied_on'],
									),
									$message
								);
							if( is_cart() ) {
								wc_print_notice(apply_filters('product_cat_order_limit_notice', $message, $term_id, $rule) , 'error' );
							} else {
								wc_add_notice( apply_filters('product_cat_order_limit_notice', $message, $term_id, $rule) , 'error' );
							}
							WC()->session->set('is_valid_order', false);
						}
					}
				}
			}
			
				/*	*	*	*	*	*	*	*	*	*	*/
				/*	 Accomulative Checks for Products	*/
				/*	*	*	*	*	*	*	*	*	*	*/
			if(isset($wcol_settings['enable_product_limit']) && $wcol_settings['enable_product_limit']=='on' && is_array( self::get_accomulative_rules_on_cart('product') ) ){
				foreach(self::get_accomulative_rules_on_cart('product') as $rule){
					if(isset( $rule['wcol_applied_on'] ) && $rule[$rule['wcol_applied_on']] > 0  ){
						$products = array();
						if( is_array($rule['object_ids']) ){
							foreach($rule['object_ids'] as $object_id){
									if($object_id != "-1"){
										$products[] = get_the_title($object_id);
									}else{
										$products[] = "All Products";
									}	
							}
						}

						if($rule['wcol_applied_on']=='amount'){
							$wcol_min_value = wc_price( $rule['wcol_min_order_limit'] );
							$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? wc_price( $rule['wcol_max_order_limit'] ) : '';
						}else{
							$wcol_min_value = $rule['wcol_min_order_limit'];
							$wcol_max_value = $rule['wcol_max_order_limit'];
						}
						if($rule['enable-max-rule-limit']!='on'){
							$wcol_max_value = '<span style="font-size:30px; font-weight:bold;vertical-align: middle;">∞</span>';
						}
						
						$message = $wcol_settings['product_limit_message_accomulative'];
						$message = str_replace(
								array(
									'{product-names}',
									'{min-limit}',
									'{max-limit}',
									'{applied-on}',
									'{time-span}',
									'{limit-reset-day}',
								),
								array(
									implode(',', $products),
									$wcol_min_value,
									$wcol_max_value,
									$rule['wcol_applied_on'],
									'',
									date('d M Y h:i:s a', $rule['to'])
								),
								$message
						);

						if( is_cart() ) {
							wc_print_notice( apply_filters('accomulative_products_limit_notice', $message, $rule), 'error' );
						} else {
							wc_add_notice( apply_filters('accomulative_products_limit_notice', $message, $rule), 'error' );
						}
						WC()->session->set('is_valid_order', false);
					}
				}
			}
			
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
				/*	 Accomulative Checks for Product Categories	*/
				/*	*	*	*	*	*	*	*	*	*	*	*	*/
			if(isset($wcol_settings['enable_category_limit']) && $wcol_settings['enable_category_limit']=='on' && is_array( self::get_accomulative_rules_on_cart('product_cat' ) ) ){
				foreach(self::get_accomulative_rules_on_cart('product_cat') as $rule){
					if( isset( $rule['wcol_applied_on'] ) && $rule[$rule['wcol_applied_on']] > 0 ){
						$cats = array();
						if( is_array($rule['object_ids'] ) ){
							foreach($rule['object_ids'] as $object_id){
								if($object_id != "-1" ){
									$term = get_term( $object_id, 'product_cat');
									$cats[] = $term->name;
								}else{
									$cats[] = "All Categories";
								}
							}
						}
						if($rule['wcol_applied_on']=='amount'){
							$wcol_min_value = wc_price( $rule['wcol_min_order_limit'] );
							$wcol_max_value = $rule['wcol_max_order_limit'] != '' ? wc_price( $rule['wcol_max_order_limit'] ) : '';
						}else{
							$wcol_min_value = $rule['wcol_min_order_limit'];
							$wcol_max_value = $rule['wcol_max_order_limit'];
						}
						if(empty($wcol_max_value) || $rule['enable-max-rule-limit']!='on'){
							$wcol_max_value = '<span style="font-size:30px; font-weight:bold;vertical-align: middle;">∞</span>';
						}
						$message = $wcol_settings['category_limit_message_accomulative'];

						$message = str_replace(
								array(
									'{category-names}',
									'{min-limit}',
									'{max-limit}',
									'{applied-on}',
									'{time-span}',
									'{limit-reset-day}',
								),
								array(
									implode(', ', $cats),
									$wcol_min_value,
									$wcol_max_value,
									$rule['wcol_applied_on'],
									'',
									date('d M Y h:i:s a', $rule['to'])
								),
								$message
							);
						if( is_cart() ) {
							wc_print_notice( apply_filters('accomulative_product_cats_limit_notice', $message, $rule), 'error' );
						} else {
							wc_add_notice( apply_filters('accomulative_product_cats_limit_notice', $message, $rule), 'error' );
						}
						WC()->session->set('is_valid_order', false);
					}
				}
			}
			
			if(!WC()->session->get('is_valid_order', false) and $wcol_settings['enable_checkout_button']=='on'){
				remove_action( 'woocommerce_proceed_to_checkout',
				'woocommerce_button_proceed_to_checkout', 20);
			}
			date_default_timezone_set($default_time_zone);
		}
		
		public function is_valid_order($object_id, $object_type, $total){
			$rules = self::get_wcol_option_by_user($object_id, $object_type);
			if(!$rules)
				return true;
			
			$gmt_ofset = get_option('gmt_offset')*60*60;
			$current_time = time() + $gmt_ofset;
			$rules_applied = array();
			if(is_array($rules)){
				foreach($rules as $rule){	
					$accomulative_check = isset($rule['accomulative']) && $rule['accomulative']=='on' ? true : false;
					
					if(!$accomulative_check){
						$rule['applied_for'] = '';
						if($rule['enable-max-rule-limit']=='on' && $rule['wcol_max_order_limit'] > 0 ){
							$cart_total = $total[$rule['wcol_applied_on']];			
							$gtotal = $total[$rule['wcol_applied_on']];
							if($gtotal > $rule['wcol_max_order_limit'] || $cart_total < $rule['wcol_min_order_limit']){
								$rule['remaining_limit'] = $rule['wcol_max_order_limit'];
								if($cart_total < $rule['wcol_min_order_limit']){
									$rule['applied_for'] = 'minimum_limit';
									$rule['wcol_max_order_limit'] = $rule['wcol_max_order_limit'];
								}
								
								$rules_applied[] = $rule;
							}
						}else{
							if($rule['enable-max-rule-limit']=='on' && ($total[$rule['wcol_applied_on']] < $rule['wcol_min_order_limit'] || $total[$rule['wcol_applied_on']] > $rule['wcol_max_order_limit']) ){
										$rules_applied[] = $rule;
							}elseif($total[$rule['wcol_applied_on']] < $rule['wcol_min_order_limit']){
								$rules_applied[] = $rule;
							}
						}
					}
				}
			}
			if(empty($rules_applied)){
				return false;
			}
			
			return $rules_applied;
		}
		
		/*	Get Accomulative rules applied in the cart 	*/
		/*	@params	string object Type								*/
		/*	returns	Array applied rules								*/
		public function get_accomulative_rules_on_cart($object_type){
			$gmt_ofset = get_option('gmt_offset')*60*60;
			$current_time = time() + $gmt_ofset;
			$accomulative_rules = self::get_accomulative_rules($object_type);
			$applied_rules = array();
			if(is_array($accomulative_rules)){
				foreach($accomulative_rules as $rule){
					$qty=0;
					$amount=0;
					$other = 0;
					$product_items_arr = array();
					if(is_array($rule['object_ids'])){
						foreach($rule['object_ids'] as $id){
							foreach(WC()->cart->get_cart() as $key=>$cart_item){
								if( ($id==$cart_item['product_id'] || $id == "-1") && $object_type=='product'){
									$product_items_arr[$cart_item['product_id']] = $cart_item;
								}elseif( (has_term( $id, $object_type, $cart_item['product_id'] )|| $id == "-1" ) && $object_type=='product_cat'){
									$cart_item['category_id'] = $id;
									$product_items_arr[$cart_item['product_id']] = $cart_item;
								}else{
									continue;
								}
							}
						}
					}

					if(is_array($product_items_arr)){
						foreach ($product_items_arr as $key => $product_items) {
							$qty += apply_filters('wcol_cart_item_qty', $product_items['quantity'], $product_items, $object_type);
							$amount += apply_filters('wcol_cart_item_total', $product_items['line_total'], $product_items, $object_type);
							if($rule['across-all-orders']=='on' || ($rule['enable-max-rule-limit']=='on' && $rule['wcol_max_order_limit'] > 0) ){
								
								if( $rule['wcol_applied_on'] == 'quantity' ){
									$qty = $qty;
								}elseif( $rule['wcol_applied_on'] == 'amount' ){
									$amount = $amount;
								}
							}
							
						}
					}
					$rule['quantity'] = $qty;
					$rule['amount'] = $amount;
					$rule = apply_filters('xswcol_get_accomulative_rules_on_cart' , $rule , $product_items_arr, $other);
					if(	$rule[$rule['wcol_applied_on']] < $rule['wcol_min_order_limit'] || ($rule['enable-max-rule-limit'] == 'on' && $rule[$rule['wcol_applied_on']] > $rule['wcol_max_order_limit']) ){
								$applied_rules[]=$rule;
					}	
					
				}
			}
			return $applied_rules;
		}
		
		/*	Restrict checkout page 	*/
		/*	@params	null			*/
		/*	returns	null			*/
		public function wcol_restrict_checkout(){
			$wcol_settings = self::get_wcol_settings();
			if( !WC()->cart->is_empty() && is_checkout() ){
				if(!WC()->session->get('is_valid_order')){
					$cart_page_id = wc_get_page_id( 'cart' );
					$cart_page_url = $cart_page_id ? get_permalink( $cart_page_id ) : '';
					wp_redirect($cart_page_url);
				}
			}
		}
		
		/*	Save Order Creation Time Stamp */
		/*	@params	int Order ID			*/
		/*	returns	null					*/
		public function save_order_creation_timestamp($order_id){
			update_post_meta($order_id, 'wcol_order_created', time() );
		}

		/**
		* Sanitize array
		* @param array $data
		* @return array $data
		*/
		public function xsollwc_sanitize($data){
			if(is_array($data)){
				foreach($data as $key=>$values){
					if(is_array($values)){
						foreach($values as $subkey => $value){
							$data[$key][$subkey] = sanitize_text_field($value);
						}

					}else{
						$data[$key] = sanitize_text_field($values);
					}
				}
			}
            return $data;
		}
		
	}
}
$XSOLLWC_Rule = new XSOLLWC_Rule;
?>