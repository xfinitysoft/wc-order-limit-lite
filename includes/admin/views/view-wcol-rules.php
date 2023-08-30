<div class="wrap wcol">
	<h3><?php esc_html_e('Order Limit Lite for WooCommerce', 'xsollwc-domain'); ?>
		<a class="xs-pro-link" href="https://codecanyon.net/item/woocommerce-wc-vendors-order-limit/20688279" target="_blank">
            <div class="xs-button-main">
                <?php submit_button(esc_html__("Pro Version"), 'secondary' , "xs-button"); ?>
            </div>
        </a>
	</h3>
	<div class="xs-wcol-alert xs-wcol-alert-success wcol-data-save-notice">
    	<button type="button" class="xs-wcol-close xs-wcol-notice-dismiss" >&times;</button>
    	<strong><?php esc_html_e( 'Success!','xsollwc-domain'); ?></strong><?php esc_html_e( 'Data Saved Successfully.','xsollwc-domain'); ?> 
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
			<a id = "wcol-advance-tab" href="?page=order-limit-lite-wc&tab=wcol-settings-tab" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-settings-tab')? 'nav-tab-active' : '' ?>" ><?php esc_html_e('Advanced','xsollwc-domain'); ?></a>
			<a id = "wcol-advance-tab" href="?page=order-limit-lite-wc&tab=wcol-support-tab" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-support-tab')? 'nav-tab-active' : '' ?>" ><?php esc_html_e('Support','xsollwc-domain'); ?></a>

		</nav>
		<?php
		$tab1 = isset($_GET['tab1']) ? $_GET['tab1'] : 'report';
		$tab = '';
		if(isset($_GET['tab'])){
			$tab = esc_html($_GET['tab']);
		}
		if($tab == 'wcol-support-tab' ){
		?>
		<ul class="subsubsub xs-list">
		    <li>
		        <a class="<?php  if($tab1 =='report' ){ echo 'current'; } ?>" href="?page=order-limit-lite-wc&tab=wcol-support-tab&tab1=report">
		            <?php esc_html_e( 'Report a bug','xsollwc-domain'); ?>
		        </a>
		        |
		    </li>
		    <li>
		        <a class="<?php if($tab1 =='request' ){ echo 'current'; } ?>" href="?page=order-limit-lite-wc&tab=wcol-support-tab&tab1=request">
		            <?php esc_html_e( 'Request a Feature','xsollwc-domain'); ?>
		        </a>
		        |
		    </li>
		    <li>
		        <a class="<?php if($tab1 =='hire' ){ echo 'current'; } ?>" href="?page=order-limit-lite-wc&tab=wcol-support-tab&tab1=hire" >
		            <?php esc_html_e( 'Hire US','xsollwc-domain'); ?>
		        </a>
		        |
		    </li>
		    <li>
		        <a class="<?php  if($tab1=='review' ){ echo 'current'; } ?>" href="?page=order-limit-lite-wc&tab=wcol-support-tab&tab1=review">
		            <?php esc_html_e( 'Review','xsollwc-domain'); ?>
		        </a>
		    </li>
		</ul>
		<?php }?>
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
			case 'wcol-support-tab':
				require ('view-wcol-support-tab.php');
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
				  <h4 class="xs-wcol-modal-title"><?php esc_html_e( 'Do  you want to ?','xsollwc-domain'); ?></h4>
				</div>
				<!-- Modal footer -->
				<div class="xs-wcol-modal-footer">
					<button type="button" class="xs-wcol-btn xs-wcol-btn-info" id='wcol-modal-sbp'><?php esc_html_e( 'Save before proceeding','xsollwc-domain'); ?> </button>
					<button type="button" class="xs-wcol-btn xs-wcol-btn-info" id='wcol-modal-pwos'><?php esc_html_e( 'Proceed without saving','xsollwc-domain'); ?></button>
				  	<button type="button" class="xs-wcol-btn xs-wcol-btn-danger" id='wcol-modal-close'><?php esc_html_e( 'Close','xsollwc-domain'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>