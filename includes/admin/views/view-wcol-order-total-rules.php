<form action="" method="POST">
<?php wp_nonce_field( 'wcol_save_rules', '_wcol_save_rules_nonce', true ); ?>
<input type="hidden" name='settings-rules-changes' value="1">
<div class="wcol-rules-section wcol-order-total-section <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-order-total-tab')? '' : 'hidden' ?>">
	<?php if($wcol_settings['enable_cart_total_limit']!='on'){
		$style="style='pointer-events: none;opacity:0.5;'";
		echo '<span style="color:red"><strong>'.esc_html__('Note! ', 'xsollwc-domain').'</strong>'.esc_html__('Order Total Limits are Disabled.','xsollwc-domain').'</span>';
		?><div class="wcol-help-tip" style="float:none; margin-right:0;">
			<span class="wcol-tip" > <?php esc_html_e('Orser Total Limits are disabled, You can enable Order Total Limits in Advance Tab.' , 'xsollwc-domain'); ?> </span>
		</div><?php
	}else{
		$style="";
	} ?>
	<table class="form-table wcol-form-table" <?php echo $style; ?>>
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wcol-cart-total-min-limit"><?php esc_html_e('Cart Total Minumum Limit:', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"><?php esc_html_e('Cart Total(amount or total Items in Cart) should be greater than or equal to this limit.', 'xsollwc-domain'); ?></span>
					</div>
				</th>
				<td>
					<input type="number" min="0" name="wcol-cart-total-min-limit" value="<?php if(isset($wcol_settings['cart_total_minimum_limit'])){ echo $wcol_settings['cart_total_minimum_limit'];} ?>"/>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wcol-cart-total-enable-max-limit"><?php esc_html_e('Enable Cart Total Maximum Limit:', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"><?php esc_html_e('Check this box if you want to enable Maximum Limit for Cart Total.', 'xsollwc-domain'); ?></span>
					</div>
				</th>
				<td>
					<input type="checkbox" class="enable-cart-total-max-rule-limit" name="wcol-cart-total-enable-max-limit" <?php if(isset($wcol_settings['cart_total_enable_maximum_limit']) && $wcol_settings['cart_total_enable_maximum_limit']=='on' ){echo 'checked'; } ?> />
				</td>
			</tr>
			
			<tr valign="top" class="<?php if(isset($wcol_settings['cart_total_enable_maximum_limit']) && $wcol_settings['cart_total_enable_maximum_limit']!='on' ){ echo 'wcol-hidden ';}?>">
				<th scope="row" class="titledesc">
					<label for="wcol-cart-total-max-limit"><?php esc_html_e('Cart Total Maximum Limit:', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"><?php esc_html_e('Cart Total(amount or total Items in Cart) should be less than or equal to this limit.', 'xsollwc-domain'); ?></span>
					</div>
				</th>
				<td>
					<input type="number" min="0" class="wcol-rule-max-limit" name="wcol-cart-total-max-limit" value="<?php if(isset($wcol_settings['cart_total_maximum_limit'])){ echo  $wcol_settings['cart_total_maximum_limit'];} ?>"/>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wcol-cart-total-applied-on"><?php esc_html_e('Applied On', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"><?php esc_html_e('Select whether Min and Max limits for Cart Total will be applied on Cart total Amount or on total Items in Cart.', 'xsollwc-domain'); ?></span>
					</div>
				</th>
				<td>
					<select name="wcol-cart-total-applied-on">
						<option value="quantity" <?php if(isset($wcol_settings['cart_total_applied_on']) && $wcol_settings['cart_total_applied_on']=='quantity'){{echo 'selected';}} ?> ><?php esc_html_e('Total items in Cart','xsollwc-domain');?></option>
						<option value="amount" <?php if(isset($wcol_settings['cart_total_applied_on']) && $wcol_settings['cart_total_applied_on']=='amount'){{echo 'selected';}} ?> ><?php esc_html_e('Cart Total','xsollwc-domain');?></option>
					</select>
				</td>
			</tr>
			<tr valign="top" class="wc-actions-row">
				<td class="wcol-order-total-save">
					<input type="submit" class="button button-primary button-large xs-wcol" value="<?php esc_html_e('Save Changes', 'xsollwc-domain')?>"/>
					<span class="spinner xs-wcol-spinner"></span>
				</td>
			</tr>
		</tbody>
		
	</table>
</div>