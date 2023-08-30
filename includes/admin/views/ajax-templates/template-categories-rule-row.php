<?php $id = uniqid(); ?>
<tr class="wcol-new" data-id="<?php echo $id  ?>">
	<td class="check-column wcol-cb">
		<input type="checkbox"/>
		<input type="hidden" name="wcol-rules[category-rules][rule_id][<?php echo esc_html($_POST['wcol_cid']); ?>]" value="<?php echo $id  ?>">
	</td>
	<td class="column-primary">
		<select class="wcol-select-categories" name="wcol-rules[category-rules][object-ids][<?php echo esc_html($_POST['wcol_cid']); ?>][]" multiple="multiple"></select>
		<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e( 'Show more details', 'xsollwc-domain') ?></span></button>
	</td>
	<td data-colname="<?php esc_attr_e('Minimum Limit', 'xsollwc-domain'); ?>">
		<input type="number" min="0" class="wcol-rule-min-limit" name="wcol-rules[category-rules][rule-limit][<?php echo esc_html($_POST['wcol_cid']); ?>]" value="0" />
	</td>
	
	<td data-colname="<?php esc_attr_e('Applied On', 'xsollwc-domain'); ?>">
		<?php 
		$applied_on_ajax_options = '<option value="amount" >'. esc_html__('Amount', 'xsollwc-domain').'</option>';
		$applied_on_ajax_options .= '<option value="quantity" >'. esc_html__('Quantity', 'xsollwc-domain') .'</option>';
		$applied_on_ajax_options = apply_filters('wcol_applied_on_ajax_option' , $applied_on_ajax_options );
		?>
		<select class="wcol-select-applied-on" name="wcol-rules[category-rules][applied-on][<?php echo esc_html($_POST['wcol_cid']); ?>]" >
			<?php echo $applied_on_ajax_options; ?>
		</select>
	</td>
	<td data-colname="<?php esc_attr_e('Accumulatively', 'xsollwc-domain'); ?>">
		<input type="hidden" class="wcol-loop-checkbox-hidden" name="wcol-rules[category-rules][accomulative][<?php echo esc_html($_POST['wcol_cid']); ?>]"/>
		<input type="checkbox" class="wcol-accomulative wcol-loop-checkbox"/>
	</td>	
	<td class="wcol-more-options-td">
		<div class="wcol-more-options">
			<a class="wcol-show-more-options" href="#"><?php esc_html_e('More Options', 'xsollwc-domain'); ?></a>
			<a class="wcol-hide-more-options wcol-hidden" href="#"><?php esc_html_e('Hide Options', 'xsollwc-domain'); ?></a>
			<div class="wcol-options-open wcol-hidden"></div>
			
			<div class=" wcol-rule-options wcol-hidden">
				<div class="wcol-more-options-header">
					<h3><?php esc_html_e('More Options' , 'xsollwc-domain'); ?></h3>
				</div>
				<table class="">
					<tr>
						<th><?php esc_html_e('Disable' , 'xsollwc-domain'); ?>:</th>
						<td>
							<input type="hidden" class="wcol-loop-checkbox-hidden" name="wcol-rules[category-rules][disable-limit][<?php echo esc_html($_POST['wcol_cid']); ?>]" />
							<input class="wcol-disable-rule-limit wcol-loop-checkbox" type="checkbox" />
						</td>
					</tr>

					
					<tr>
						<th><?php esc_html_e('Enable Maximum Limit' , 'xsollwc-domain'); ?>:</th>
						<td>
							<input type="hidden" class="enable-max-rule-limit-hidden wcol-loop-checkbox-hidden" name="wcol-rules[category-rules][enable-max-rule-limit][<?php echo esc_html($_POST['wcol_cid']); ?>]"/>
							<input class="enable-max-rule-limit wcol-loop-checkbox" type="checkbox" />
						</td>
					</tr>
					
					<tr class="wcol-hidden">
						<th><?php esc_html_e('Maximum Limit' , 'xsollwc-domain'); ?>:</th>
						<td><input type="number" min="0" class="wcol-rule-max-limit" name="wcol-rules[category-rules][max-rule-limit][<?php echo esc_html($_POST['wcol_cid']); ?>]" /></td>
					</tr>
					
				</table>
				
			</div>
		</div>
	</td>
</tr>