<div class="wcol-rules-section wcol-advance-tab-section <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'wcol-settings-tab')? '' : 'hidden' ?>">
	<table class="form-table wcol-form-table">
		<tbody>
			
			<tr>
				<td><h3><?php esc_html_e('Product Limit Options:', 'xsollwc-domain'); ?></h3></td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc"> 
					<label for=""><?php esc_html_e('Products Limits', 'xsollwc-domain'); ?>:</label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"> <?php esc_html_e('Check this box to enable products based limits.', 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-checkbox" >
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Products Limits', 'xsollwc-domain'); ?></span></legend>
						<label for="wcol-enable-product-limit">
							<input type="checkbox" name="wcol-enable-product-limit" <?php if(isset($wcol_settings['enable_product_limit']) && $wcol_settings['enable_product_limit']=='on'){ echo 'checked';} ?> > <?php esc_html_e('Enable Products Limits', 'xsollwc-domain'); ?>
						</label> 
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc"> 
					<label for="wcol-product-limit-message"><?php esc_html_e('Message for Product limit', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"> <?php esc_html_e('This message will be shown on cart page if customer do not fulfill the order limit that you specified for products.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-textarea">
					<div>
						<textarea name="wcol-product-limit-message" id="wcol-product-limit-message" rows="3"><?php echo $wcol_settings['product_limit_message']; ?></textarea>
						<span style="display: inline-block; padding: 0 50px 0 5px; font-style: italic; font-size: 11px; ">
							<?php esc_html_e('Use {product-name} for Product Name, {min-limit} for Minimum Limit, {max-limit} for Maximum Limit {applied-on} for quantity/amount , {time-span} for rule time span, {limit-reset-day} for rule rest date.' , 'xsollwc-domain');?>
						</span>

					</div>
				</td>				
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc"> 
					<label for="wcol-product-limit-accomulative"><?php esc_html_e('Message for Product limit For Accomulative Rules', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"> <?php esc_html_e('This message will be shown on cart page if customer do not fulfill rule for Acomulative Products.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-textarea">
					<div>
						<textarea name="wcol-product-limit-message-accomulative" id="wcol-product-limit-message-accomulative" rows="3"><?php echo $wcol_settings['product_limit_message_accomulative']; ?></textarea>
						<span style="display: inline-block; padding: 0 50px 0 5px; font-style: italic; font-size: 11px; ">
							<?php esc_html_e('Use {product-names} for Product Names seperated by comma, {max-limit} for Maximum Limit {min-limit} for Minimum Limit, {applied-on} for quantity/amount,  , {time-span} for rule time span, {limit-reset-day} for rule rest date.' , 'xsollwc-domain');?>
						</span>

					</div>
				</td>				
			</tr>

			<tr valign="top">
				<td><h3><?php esc_html_e('Category Limit Options:', 'xsollwc-domain'); ?></h3></td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wcol-enable-category-limit"><?php esc_html_e('Category Limits', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip" title="">
						<span class="wcol-tip"> <?php esc_html_e('Check this box to enable product categories based limits.', 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-checkbox" >
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Category Limits', 'xsollwc-domain'); ?></span></legend>
						<label for="wcol-enable-category-limit">
							<input type="checkbox" name="wcol-enable-category-limit" <?php if(isset($wcol_settings['enable_category_limit']) && $wcol_settings['enable_category_limit']=='on'){ echo 'checked';} ?> > <?php esc_html_e('Enable Category Limits', 'xsollwc-domain'); ?>
						</label> 
					</fieldset>
				</td>
			</tr>
			<tr valign="top" class="titledesc">
				<th scope="row"> 
					<label for="wcol-category-limit-message"><?php esc_html_e('Message for Category limit', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip" title="">
						<span class="wcol-tip"> <?php esc_html_e('This message will be shown on cart page if customer do not fulfill the order limit that you specified for product categories.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-textarea">
					<div>
						<textarea name="wcol-category-limit-message" id="wcol-category-limit-message" rows="3"><?php echo $wcol_settings['category_limit_message']; ?></textarea>
						<span style="display: inline-block; padding: 0 50px 0 5px; font-style: italic; font-size: 11px; ">
							<?php esc_html_e('Use {category-name} for Category, {min-limit} for Minimum Limit, {max-limit} for Maximum Limit {applied-on} for quantity/amount , {time-span} for rule time span, {limit-reset-day} for rule rest date.', 'xsollwc-domain');?>
						</span>

					</div>
				</td>				
			</tr>	
			<tr valign="top" class="titledesc">
				<th scope="row"> 
					<label for="wcol-category-limit-message-accomulative"><?php esc_html_e('Message for Category limit for Accomulative Rules', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip" title="">
						<span class="wcol-tip"> <?php esc_html_e('This message will be shown on cart page if customer do not fulfill an accomulative rule for product categories.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-textarea">
					<div>
						<textarea name="wcol-category-limit-message-accomulative" id="wcol-category-limit-message-accomulative" rows="3"><?php echo $wcol_settings['category_limit_message_accomulative']; ?></textarea>
						<span style="display: inline-block; padding: 0 50px 0 5px; font-style: italic; font-size: 11px; ">
							<?php esc_html_e('Use {category-names} for Categories seperated by comma, {max-limit} for Maximum Limit, {min-limit} for Minimum Limit, {applied-on} for quantity/amount , {time-span} for rule time span, {limit-reset-day} for rule rest date.' , 'xsollwc-domain');?>
						</span>

					</div>
				</td>				
			</tr>
			<tr valign="top">
				<td><h3><?php esc_html_e('Cart Total Limit Options:', 'xsollwc-domain'); ?></h3></td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc"> 
					<label for="wcol-enable-cart-total-limit"><?php esc_html_e('Cart Total Limits', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip" title="">
						<span class="wcol-tip"> <?php esc_html_e('Check this box to enable limits on cart total.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-checkbox" >
					<fieldset>
						<legend class="screen-reader-text"><span><?php esc_html_e('Cart Total Limits', 'xsollwc-domain'); ?></span></legend>
						<label for="wcol-enable-cart-total-limit">
							<input type="checkbox" name="wcol-enable-cart-total-limit" <?php if(isset($wcol_settings['enable_cart_total_limit']) && $wcol_settings['enable_cart_total_limit']=='on'){ echo 'checked';} ?> > <?php esc_html_e('Enable Cart Total Limits', 'xsollwc-domain'); ?>
						</label> 
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc"> 
					<label for="wcol-cart-total-limit-message"><?php esc_html_e('Message for Product limit', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip" title="">
						<span class="wcol-tip"> <?php esc_html_e('This message will be shown on cart page if customer do not fulfill the order limit that you specified for cart totals.' , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td class="wcol-forminp wcol-forminp-textarea">
					<div>
						<textarea name="wcol-cart-total-limit-message"  id="wcol-cart-total-limit-message" rows="3"><?php echo $wcol_settings['cart_total_limit_message']; ?></textarea>
						<span style="display: inline-block; padding: 0 50px 0 5px; font-style: italic; font-size: 11px; ">
							<?php esc_html_e('Use {min-limit} for Minimum Limit, {max-limit} for Maximum Limit, {applied-on} for quantity/amount.', 'xsollwc-domain');?>
						</span>

					</div>
				</td>				
			</tr>
					

			
			<tr valign="top">
				<td><h3><?php esc_html_e('Other Options:', 'xsollwc-domain'); ?></h3></td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="wcol-enable-checkout-button"><?php esc_html_e('Hide Checkout Button', 'xsollwc-domain'); ?></label>
					<div class="wcol-help-tip">
						<span class="wcol-tip"> <?php esc_html_e("If you check this checkbox then Checkout Button will be hidden on cart page if customer do not fulfill any of the limits that you specified." , 'xsollwc-domain'); ?> </span>
					</div>
				</th>
				<td><input type="checkbox" name="wcol-enable-checkout-button" <?php if(isset($wcol_settings['enable_checkout_button']) && $wcol_settings['enable_checkout_button']=='on'){ echo 'checked';} ?>></td>
			</tr>
			
			<tr valign="top" class="wc-actions-row">
				<td class="wcol-order-total-save">
					<input type="submit"  class="button button-primary button-large xs-wcol" value="<?php esc_html_e('Save Changes', 'xsollwc-domain')?>"/>
					<span class="spinner xs-wcol-spinner"></span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</form>