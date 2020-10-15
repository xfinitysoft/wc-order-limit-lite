<div class="wrap wcol">
	<h3><?php esc_html_e('Order Limit Lite for WooCommerce', 'xsollwc-domain'); ?></h3>
	<p><?php esc_html_e( 'If you want to use full functionality kindly buy our pro version ' , 'xsollwc-domain'); ?> 
	<span><a href="https://codecanyon.net/item/woocommerce-wc-vendors-order-limit/20688279?s_rank=15" target="_blank"><input type="button" name="wcoll-button" id="wcoll-button" class="button button-secondary" value="<?php esc_attr_e("WC Order Limit" , 'xsollwc-domain' )?>"  /></a></span></p>
	<div class="xs-wcol-alert xs-wcol-alert-success wcol-data-save-notice">
    	<button type="button" class="xs-wcol-close xs-wcol-notice-dismiss" >&times;</button>
    	<strong>Success!</strong> Data Save Successfully.
  	</div>
	<!-- <form action="" method="POST"> -->
		<!-- Main Setting tab -->	
		<nav class="nav nav-tabs wcol-nav">
			
			<!-- Order Limit Rules For Products Tab -->
			<a id = "wcol-products" href="?page=order-limit-lite-wc&tab=wcol-products-tab" class="nav-tab <?php echo ( !isset($_GET['tab']) || $_GET['tab'] == 'wcol-products-tab') ? 'nav-tab-active' : '' ?> " ><?php esc_html_e('Products','xsollwc-domain'); ?></a>
			
			<!-- Order Limit Rules For Categories Tab -->
			<a id = "wcol-categories" href="?page=order-limit-lite-wc&tab=wcol-categories-tab" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-categories-tab')?  'nav-tab-active' : '' ?>"  ><?php esc_html_e('Categories','xsollwc-domain'); ?></a>
			
			<!-- Order Limit Rules For Order Total  Tab -->				
			<a id = "wcol-order-total" href="?page=order-limit-lite-wc&tab=wcol-order-total-tab" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-order-total-tab')? 'nav-tab-active' : '' ?>"  ><?php esc_html_e('Order Total','xsollwc-domain'); ?></a>
							
			<!-- Advance Tab -->				
			<a id = "wcol-advance-tab" href="?page=order-limit-lite-wc&tab=wcol-settings-tab" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-settings-tab')? 'nav-tab-active' : '' ?>" ><?php esc_html_e('Advance','xsollwc-domain'); ?></a>

		</nav>
		<div class="wcol-inner">
		<?php
		/*wp_nonce_field( 'wcol_save_rules', '_wcol_save_rules_nonce', true );*/
		$tab = '';
		if(isset($_GET['tab'])){
			$tab = esc_html($_GET['tab']);
		}
		switch ($tab) {
			case 'wcol-products-tab':
				require('view-wcol-product-rules.php');
				break;
			case 'wcol-categories-tab':
				require('view-wcol-category-rules.php');
				break;
			case 'wcol-order-total-tab':
				require('view-wcol-order-total-rules.php');
				require('view-wcol-advance-tab.php');
				break;
			case 'wcol-settings-tab':
				require('view-wcol-order-total-rules.php');
				require('view-wcol-advance-tab.php');
				break;
			default:
				require('view-wcol-product-rules.php');
				break;
		}
		
		?>
		</div>
	<!-- </form> -->
	<div class="xs-wcol-modal" id="wcol-modal">
		<div class="xs-wcol-modal-dialog">
			<div class="xs-wcol-modal-content">
				<!-- Modal Header -->
				<div class="xs-wcol-modal-header">
				  <h4 class="xs-wcol-modal-title">Do  you want to ?</h4>
				</div>
				<!-- Modal footer -->
				<div class="xs-wcol-modal-footer">
					<button type="button" class="xs-wcol-btn xs-wcol-btn-info" id='wcol-modal-sbp'>Save before proceeding </button>
					<button type="button" class="xs-wcol-btn xs-wcol-btn-info" id='wcol-modal-pwos'>Proceed without saving</button>
				  	<button type="button" class="xs-wcol-btn xs-wcol-btn-danger" id='wcol-modal-close'>Close</button>
				</div>
			</div>
		</div>
	</div>
</div>