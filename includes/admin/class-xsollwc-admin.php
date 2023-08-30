<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;   //Exit if accessed directly.
}

if(!class_exists('XSOLLWC_Admin')){
	class XSOLLWC_Admin{
		public function __construct(){
			$this->XSOLLWC_menus();
			// enqueue Scripts
			add_action('admin_enqueue_scripts', array($this,'XSOLLWC_enqueue_admin_script'),10,1);
		}
		
		/*	Loading wcol admin Menu	*/
		/*	@params	null			*/
		/*	returns	null			*/
		public function XSOLLWC_menus(){
			add_submenu_page ( 'woocommerce', esc_html__( 'Order Limit Lite for WooCommerce' , 'xsollwc-domain' ), esc_html__( 'Order Limit Lite for WooCommerce' , 'xsollwc-domain'), 'manage_options', 'order-limit-lite-wc', array($this, 'wcol_menu_callback'));
		}

		
		

		/*	Loading wcol admin view	*/
		/*	@params	null			*/
		/*	returns	null			*/
		public function wcol_menu_callback(){
			//Varify nounce and then save data
			$wcol_rule = new XSOLLWC_Rule();
			if(isset( $_POST['_wcol_save_rules_nonce'] ) 
			   AND wp_verify_nonce( $_POST['_wcol_save_rules_nonce'], 'wcol_save_rules' ) 
			){
				$wcol_rule->save_rules();
			}
			$wcol_product_rules = $wcol_rule->get_product_rules();
			$wcol_category_rules = $wcol_rule->get_category_rules();
			$wcol_settings = $wcol_rule->get_wcol_settings();
			require('views/view-wcol-rules.php');
		}

		/*	Enqueue Admin Scripts and Styles	*/
		/*	@params	null						*/
		/*	returns	null						*/		
		public function XSOLLWC_enqueue_admin_script($hook){
			global $post;
			// Registering Admin Scripts
			wp_enqueue_script('jquery');
			wp_register_script('jQuery-select2-js', XSOLLWC_ROOT_URL.'/assets/js/select2.full.min.js', array('jquery'));
			wp_register_script('wcoll-admin-js', XSOLLWC_ROOT_URL.'/assets/js/wcol-admin-script.js', array('jquery'));
			// localize script 
			$script_vars = $this->get_wcol_script_vars();
			wp_localize_script( 'wcoll-admin-js', 'wcol_script_vars', $script_vars );
			
			// Registering Admin Styles
			wp_register_style('jQuery-select2-css', XSOLLWC_ROOT_URL.'/assets/css/select2.min.css', array());
			wp_register_style('wcoll-admin-css', XSOLLWC_ROOT_URL.'/assets/css/wcol-admin-styles.css', array());
			
			if( (isset($_GET['page']) && $_GET['page'] == 'order-limit-lite-wc') || (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'product_cat') ||  ($hook=='post-new.php' && 'product' === $post->post_type) || ($hook=='post.php' && 'product' === $post->post_type) || $hook=='user-edit.php' || $hook=='profile.php' ){
				wp_enqueue_style('jQuery-select2-css');
				wp_enqueue_script('jQuery-select2-js');
				wp_enqueue_script('wcoll-admin-js');
				wp_enqueue_style('wcoll-admin-css');
			}
		}
		
		/*	get all wc products		*/
		/*	@params	null			*/
		/*	returns	array()			*/
		public function get_all_wc_products(){
			global $wpdb;
			$product_query = "SELECT ID as id, post_title as text FROM {$wpdb->prefix}posts
							  WHERE post_status IN ('pending', 'publish', 'private') AND post_type='product'
							  ORDER BY post_title";
			$all_products  = array(  array('id' => '-1' , 'text' =>'All Products ' ) );
			$xs_products = array_merge( $all_products, $wpdb->get_results($product_query, ARRAY_A) );
			return $xs_products;				  
		}
		
		/*	get all wc product categories	*/
		/*	@params	null					*/
		/*	returns	array()					*/
		public function get_all_wc_products_cat(){
			$args = array(
				 'taxonomy'     => 'product_cat',
				 'orderby'      => 'name',
				 'show_count'   => 0,
				 'pad_counts'   => 0,
				 'hierarchical' => 0,
				 'title_li'     => '',
				 'hide_empty'   => 0
			);
			$terms = get_categories( $args );
			$product_cat = array();
			$product_cat[] = array('id' => '-1' , 'text' =>'All Categories ');
			if(is_array($terms)){
				foreach($terms as $term){
					$product_cat[]=array('id'=>$term->term_id,'text'=>$term->name);
				}
			}
			return $product_cat;	  
		}
		
		
		/*	get all users	*/
		/*	@params	null		*/
		/*	returns	array()		*/
		public function get_all_users(){
			$users = array();
			foreach(get_users() as $user){
				$users[] = array('id'=>$user->ID, 'text' => $user->user_login);
			}
			return $users;					
		}
		
		/*	get all roles	*/
		/*	@params	null		*/
		/*	returns	array()		*/
		public function get_all_roles(){
			$roles = array();
			global $wp_roles;
			if(is_array($wp_roles->roles)){
				foreach($wp_roles->roles as $key => $role){
					$roles[] = array('id'=>$key, 'text'=>$role['name']);
				}
			}
			return $roles;					
		}
		
		/*	get script variables	*/
		/*	@params	null			*/
		/*	returns	array()			*/
		public function get_wcol_script_vars(){
			if(is_plugin_active( 'wc-vendors/class-wc-vendors.php' )){
				$vendor_plugin = 1;
			}else{
				$vendor_plugin = 0;
			}
			$script_var=array(
							'ajax_url'		=> admin_url('admin-ajax.php'),
							'products'		=>	$this->get_all_wc_products(),
							'categories'	=> 	$this->get_all_wc_products_cat(),
							'users'			=> 	$this->get_all_users(),
							'user_roles'	=> 	$this->get_all_roles(),
							'text1'			=> esc_html__('Amount', 'xsollwc-domain'),
							'text2'			=> esc_html__('Quantity', 'xsollwc-domain'),
							'text3'			=> esc_html__('Sure you want to delete selected Rule(s)'),
							'vendor_plugin'	=> $vendor_plugin
							);
			return 	$script_var;			
		}
		
		/*	XSOLLWC tab in product data panel	*/
		/*	@params	null					*/
		/*	returns	null					*/
		public function XSOLLWC_product_data_panel_tab(){
			?><li class="wcol_tab"><a href="#wcol_tab"><?php esc_html_e('Order Limit Lite for WooCommerce', 'xsollwc-domain'); ?></a></li><?php
		}
		
		/*	XSOLLWC panel in product data panel	*/
		/*	@params	null					*/
		/*	returns	null					*/
		public function XSOLLWC_product_data_panel(){
			global $post;
			$XSOLLWC_Rule = new XSOLLWC_Rule();
			$wcol_rules = $XSOLLWC_Rule->get_wcol_options_admin($post->ID, 'product');
			?>
			<div id="wcol_tab" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
				
				<div class="toolbar">
					<div class="">
						<span class="spinner wcol_spinner"></span>
						<a id="wcol_add_new_rule" class="button" href="#"><?php esc_html_e('Add New Rule', 'xsollwc-domain'); ?></a>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="wc-metaboxes">
					<?php 
						if(is_array($wcol_rules)){
				    	foreach($wcol_rules as $mkey => $wcol_options ) {
							$max_limit = $wcol_options['wcol_max_order_limit'];
							$min_limit = $wcol_options['wcol_min_order_limit'];
							if( empty($max_limit) || $wcol_options['enable-max-rule-limit'] != 'on' ){
								$max_limit = '<span style="font-size:20px; font-weight:bold;vertical-align: text-bottom;">∞</span>';
							}else{
								if($wcol_options['wcol_applied_on'] == 'amount'){
									$max_limit = wc_price($max_limit);
								}
							}
							if($wcol_options['wcol_applied_on'] == 'amount'){
								$min_limit = wc_price($min_limit);
							}
						?>
					<div class="wc-metabox closed <?php if(isset($wcol_options['accomulative']) and $wcol_options['accomulative']=='on'){echo 'wcol_accomulative_rule';} ?>">
						<h3>
							<?php 
								echo esc_html__('Rule for all users', 'xsollwc-domain').' - '. $min_limit.' - '.$max_limit;
							?>
							<span class="wcol-delete"><?php esc_html_e('Delete', 'xsollwc-domain'); ?></span>
						</h3>
						<div class="wc-metabox-content" style="display:none;">
							<input type="hidden" value="<?php echo $wcol_options['rule-id']; ?>" name="wcol_rules[rule-id][<?php echo $mkey; ?>]"/>
							<input type="hidden" value="<?php echo $wcol_options['key']; ?>" name="wcol_rules[wcol_rule_key][<?php echo $mkey; ?>]"/>
							<?php if(isset($wcol_options['accomulative']) && $wcol_options['accomulative']=='on'){ ?>
							<input type="hidden" class="wcol-loop-checkbox-hidden wcol_accomulative" value="<?php echo $wcol_options['accomulative']; ?>" name="wcol_rules[accomulative][<?php echo $mkey; ?>]"/>
							<div class="options_group">
								<p class="form-field">
									<label><?php esc_html_e('Edit This Rule:', 'xsollwc-domain'); ?></label>
									<span class="wcol-help-tip" style="float:none;">
										<span class="wcol-tip" > <?php esc_html_e("This Product is included in a Rule that is being applied accomulatively with other products so if you edit this products's limit options then it will be excluded from that accomulative rule.", 'xsollwc-domain'); ?> </span>
									</span>
									<input type="checkbox" class="wcol_edit_rule" />
								</p>
							</div>
							<?php }else{ ?>
							<input type="hidden" class="wcol_accomulative" value="<?php echo $wcol_options['accomulative']; ?>" name="wcol_rules[accomulative][<?php echo $mkey; ?>]"/>
							<?php } ?>
							
							<div class="options_group">
								<p class="form-field">
									<label><?php esc_html_e('Disable:', 'xsollwc-domain'); ?></label>
									<input type="hidden" class="wcol-loop-checkbox-hidden" name="wcol_rules[disable-limit][<?php echo $mkey; ?>]" value="<?php echo $wcol_options['disable-limit']; ?>"/>
									<input class="wcol-disable-rule-limit wcol-loop-checkbox" type="checkbox" <?php if($wcol_options['disable-limit']=='on'){echo "checked";} ?> />
								</p>
								<p class="wcol-description"><?php esc_html_e('Leave blank for no limit.', 'xsollwc-domain'); ?></p>
							</div>
								
							<div class="options_group">
								<p class="form-field">
									<label><?php esc_html_e('Minimum Order:', 'xsollwc-domain'); ?></label>
									<input type="number" min="0"  class="wcol-rule-min-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" name="wcol_rules[wcol_min_order_limit][<?php echo $mkey; ?>]" value="<?php echo isset($wcol_options['wcol_min_order_limit']) ? $wcol_options['wcol_min_order_limit'] : ''; ?>" placeholder="<?php esc_html_e('Enter Minimum Order Limit', 'xsollwc-domain'); ?>" />
								</p>
								<p class="wcol-description"><?php esc_html_e('Leave blank for no limit.', 'xsollwc-domain'); ?></p>
							</div>
							<div class="options_group">
								<p class="form-field">
									<label><?php esc_html_e('Enable Maximum Limit:', 'xsollwc-domain'); ?></label>
									<input type="hidden" class="enable-max-rule-limit-hidden wcol-loop-checkbox-hidden" value="<?php echo $wcol_options['enable-max-rule-limit']; ?>" name="wcol_rules[enable-max-rule-limit][<?php echo $mkey; ?>]"/>
									<input type="checkbox" class="wcol-loop-checkbox enable-max-rule-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>"  min="0" <?php  if(isset($wcol_options['enable-max-rule-limit']) and $wcol_options['enable-max-rule-limit']){ echo 'checked';} ?> />
								</p>
								<p class="form-field <?php if($wcol_options['enable-max-rule-limit']!='on'){echo "wcol-hidden";}?>">
									<label><?php esc_html_e('Maximum Order:', 'xsollwc-domain'); ?></label>
									<input type="number" class="wcol-rule-max-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" min="0" name="wcol_rules[wcol_max_order_limit][<?php echo $mkey; ?>]" value="<?php echo isset($wcol_options['wcol_max_order_limit']) ? $wcol_options['wcol_max_order_limit'] : ''; ?>" placeholder="<?php esc_html_e('Enter Maximum Order Limit', 'xsollwc-domain'); ?>" />
								</p>
							</div>
							
							<div class="options_group">                                                                        
								<p class="form-field">
									<label><?php esc_html_e('Applied on:', 'xsollwc-domain'); ?></label>
									<select class="wcol-applied-on <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" name="wcol_rules[wcol_applied_on][<?php echo $mkey; ?>]" >
										<option value="amount" <?php if(isset($wcol_options['wcol_applied_on']) && $wcol_options['wcol_applied_on']=='amount'){ echo 'selected'; } ?>> <?php esc_html_e('Amount', 'xsollwc-domain'); ?> </option>
										<option value="quantity" <?php if(isset($wcol_options['wcol_applied_on']) && $wcol_options['wcol_applied_on']=='quantity'){ echo 'selected'; } ?> > <?php esc_html_e('Quantity', 'xsollwc-domain'); ?> </option>
									</select>
								</p>
								<p class="wcol-description"><?php esc_html_e('Select if limit will be applied on quantity or amount.', 'xsollwc-domain'); ?></p>
							</div>
						</div>	
					</div>
					
					<?php $xs_i = $mkey; } } ?>
					<input type="hidden" class="xswcol-spid" value="<?php echo $xs_i;?>">
				</div>
			</div>
			<?php
		}
		
		public function process_product_meta_xsollwc_tab($post_id){
			$XSOLLWC_Rule = new XSOLLWC_Rule();
			$XSOLLWC_Rule->save_wcol_options($post_id, 'product');
		}
		
		public function XSOLLWC_product_cat_fields($term){
			$XSOLLWC_Rule = new XSOLLWC_Rule();
			// retrieve the existing value(s) for this meta field.
			$wcol_rules = $XSOLLWC_Rule->get_wcol_options_admin($term->term_id, 'product_cat');
			?>
			<tr class="form-field">
				<td colspan="2">
					<h2> <?php esc_html_e('Order Limit Lite for WooCommerce', 'xsollwc-domain'); ?> </h2>
					<div class="">
						<span class="spinner wcol_spinner"></span>
						<a id="wcol_add_new_rule" class="button" href="#"><?php esc_html_e('Add New Rule', 'xsollwc-domain'); ?></a>
					</div>
					<div class="clear"></div>
					<div class="wcol_single_cat_rules">
						<?php
						if(is_array($wcol_rules)) {
						foreach($wcol_rules as $mkey => $wcol_options){
							$max_limit = $wcol_options['wcol_max_order_limit'];
							$min_limit = $wcol_options['wcol_min_order_limit'];
							if( empty($max_limit) || $wcol_options['enable-max-rule-limit'] != 'on' ){
								$max_limit = '<span style="font-size:20px; font-weight:bold;vertical-align: text-bottom;">∞</span>';
							}else{
								if($wcol_options['wcol_applied_on'] == 'amount'){
									$max_limit = wc_price($max_limit);
								}
							}
							if($wcol_options['wcol_applied_on'] == 'amount'){
								$min_limit = wc_price($min_limit);
							}
						?>
						<div class="wcol_single_cat_rule <?php if(isset($wcol_options['accomulative']) and $wcol_options['accomulative']=='on'){echo 'wcol_accomulative_rule';} ?>">
							<h3 class="wcol_cat_accordion">
							<?php 
								echo esc_html__('Rule for all users', 'xsollwc-domain').' - '. $min_limit.' - '.$max_limit;
							?>
							<span class="wcol-delete"><?php esc_html_e('Delete', 'xsollwc-domain'); ?></span>
							</h3>
							<div class="wcol_cat_panel">
								<input type="hidden" value="<?php echo $wcol_options['rule-id']; ?>" name="wcol_rules[rule-id][<?php echo $mkey; ?>]"/>
								<input type="hidden" value="<?php echo $wcol_options['key']; ?>" name="wcol_rules[wcol_rule_key][<?php echo $mkey; ?>]"/>
								<?php if(isset($wcol_options['accomulative']) && $wcol_options['accomulative']=='on'){ ?>
								<input type="hidden" class="wcol-loop-checkbox-hidden wcol_accomulative" value="<?php echo $wcol_options['accomulative']; ?>" name="wcol_rules[accomulative][<?php echo $mkey; ?>]"/>
								<div class="options_group">
									<p class="form-field">
										<label><?php esc_html_e('Edit This Rule:', 'xsollwc-domain'); ?></label>
										<span class="wcol-help-tip" style="float:none;">
											<span class="wcol-tip" > <?php esc_html_e("This Product is included in a Rule that is being applied accomulatively with other products so if you edit this products's limit options then it will be excluded from that accomulative rule.", 'xsollwc-domain'); ?> </span>
										</span>
										<input type="checkbox" class="wcol_edit_rule" />
									</p>
								</div>
								<?php }else{ ?>
								<input type="hidden" class="wcol_accomulative" value="<?php echo $wcol_options['accomulative']; ?>" name="wcol_rules[accomulative][<?php echo $mkey; ?>]"/>
								<?php } ?>
								
								<div class="options_group">
									<p class="form-field">
										<label><?php esc_html_e('Disable:', 'xsollwc-domain'); ?></label>
										<input type="hidden" class="wcol-loop-checkbox-hidden" name="wcol_rules[disable-limit][<?php echo $mkey; ?>]" value="<?php echo $wcol_options['disable-limit']; ?>"/>
										<input class="wcol-disable-rule-limit wcol-loop-checkbox" type="checkbox" <?php if($wcol_options['disable-limit']=='on'){echo "checked";} ?> />
									</p>
									<p class="wcol-description"><?php esc_html_e('Leave blank for no limit.', 'xsollwc-domain'); ?></p>
								</div>
								
								<div class="options_group">
									<p class="form-field">
										<label><?php esc_html_e('Minimum Order:', 'xsollwc-domain'); ?></label>
										<input type="number" min="0"  name="wcol_rules[wcol_min_order_limit][<?php echo $mkey; ?>]" class="wcol-rule-min-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" value="<?php echo isset($wcol_options['wcol_min_order_limit']) ? $wcol_options['wcol_min_order_limit'] : ''; ?>" placeholder="<?php esc_html_e('Enter Minimum Order Limit', 'xsollwc-domain'); ?>" />
									</p>
									<p class="wcol-description"><?php esc_html_e('Leave blank for no limit.', 'xsollwc-domain'); ?></p>
								</div>
									
								<div class="options_group">
									<p class="form-field">
										<label><?php esc_html_e('Enable Maximum Limit:', 'xsollwc-domain'); ?></label>
										<input type="hidden" class="wcol-loop-checkbox-hidden enable-max-rule-limit-hidden" value="<?php echo $wcol_options['enable-max-rule-limit']; ?>" name="wcol_rules[enable-max-rule-limit][<?php echo $mkey; ?>]"/>
										<input type="checkbox" class="wcol-loop-checkbox enable-max-rule-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>"  min="0" <?php  if(isset($wcol_options['enable-max-rule-limit']) and $wcol_options['enable-max-rule-limit']){ echo 'checked';} ?> />
									</p>
									<p class="form-field <?php if($wcol_options['enable-max-rule-limit']!='on'){echo "wcol-hidden";}?>">
										<label><?php esc_html_e('Maximum Order:', 'xsollwc-domain'); ?></label>
										<input type="number" class="wcol-rule-max-limit <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" min="0" name="wcol_rules[wcol_max_order_limit][<?php echo $mkey; ?>]" value="<?php echo isset($wcol_options['wcol_max_order_limit']) ? $wcol_options['wcol_max_order_limit'] : ''; ?>" placeholder="<?php esc_html_e('Enter Maximum Order Limit', 'xsollwc-domain'); ?>" />
									</p>
								</div>
								
								<div class="options_group">                                                                        
									<p class="form-field">
										<label><?php esc_html_e('Applied on:', 'xsollwc-domain'); ?></label>
										<select class="wcol-applied-on <?php if($wcol_options['disable-limit']=='on'){ echo 'wcol-disabled' ;} ?>" name="wcol_rules[wcol_applied_on][<?php echo $mkey; ?>]" >
											<option value="amount" <?php if(isset($wcol_options['wcol_applied_on']) && $wcol_options['wcol_applied_on']=='amount'){ echo 'selected'; } ?>> <?php esc_html_e('Amount', 'xsollwc-domain'); ?> </option>
											<option value="quantity" <?php if(isset($wcol_options['wcol_applied_on']) && $wcol_options['wcol_applied_on']=='quantity'){ echo 'selected'; } ?> > <?php esc_html_e('Quantity', 'xsollwc-domain'); ?> </option>
										</select>
									</p>
									<p class="wcol-description"><?php esc_html_e('Select if limit will be applied on quantity or amount.', 'xsollwc-domain'); ?></p>
								</div>
							</div>
						</div>
						<?php  $xs_i = $mkey; } 
						} ?>
						<input type="hidden" name="xswcol-spcid" value="<?php echo $xs_i; ?>">
					</div>	
				</td>
			</tr>
			<?php
		}
		
		public function save_xsollwc_product_cat_fields($term_id){
			$XSOLLWC_Rule = new XSOLLWC_Rule();
			$XSOLLWC_Rule->save_wcol_options($term_id, 'product_cat');
		}
		
		
		public function save_xsollwc_product_cat_fields_on_add_new($term_id, $tt_id){
			$XSOLLWC_Rule = new XSOLLWC_Rule();
			$XSOLLWC_Rule->save_wcol_options($term_id, 'product_cat');
		}


	}
}