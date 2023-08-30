
jQuery(document).ready(function(){
	"use strict";		
	/* JS for tabs*/
	jQuery("form :input").on('change',function() {
	  jQuery(this).closest('form').addClass('xs-changed');
	});
	jQuery("select").on('change',function() {
	  jQuery(this).closest('form').addClass('xs-changed');
	});
	jQuery('nav.wcol-nav a.nav-tab').on('click',function(e){
		var	url	= jQuery(this).attr('href');
		e.preventDefault();
		if(jQuery('form').hasClass('xs-changed')) {
			jQuery('#wcol-modal').show();
			jQuery('#wcol-modal-close').on('click',function(){
				jQuery('#wcol-modal').hide();
				return;
			});

			jQuery('#wcol-modal-pwos').on('click',function(){
				jQuery('#wcol-modal').hide();
				location.replace(url);
			});

			jQuery('#wcol-modal-sbp').on('click',function(){
				jQuery('#wcol-modal').hide();
				jQuery('input[type="submit"]').trigger( "click" );
				location.replace(url);
			});

  		}else{
  			location.replace(url);
  		}
	});
	jQuery('.wcol-select-products').select2({
		placeholder: 'Select Products',
		data:wcol_script_vars.products,
		width:"95%",
		multiple:true
	});
	jQuery('.wcol-select-categories').select2({
		placeholder: 'Select Categories',
		data:wcol_script_vars.categories,
		width:"95%",
		multiple:true
	});
	jQuery('#wcol-add-product-rule').on('click', function(){
		jQuery(this).closest('form').addClass('xs-changed');
		jQuery('.wcol-rule-options').addClass('wcol-hidden');
		jQuery('.wcol-hide-more-options').addClass('wcol-hidden');
		jQuery('.wcol-show-more-options').removeClass('wcol-hidden');
		jQuery('.wcol-options-open').addClass('wcol-hidden');
		jQuery('table.wcol-collapsed').removeClass('wcol-collapsed');
		jQuery('.wcol-new').removeClass('wcol-new');
		var table = jQuery(this).parent().parent().find('table.wp-list-table').find('tbody.wcol-main-body');
		var spinner = jQuery(this).closest('.wcol-actions-btn').find('.wcol_spinner');
		spinner.closest('form').css('pointer-events','none');
		spinner.addClass('wcol_is_active');
		var wcol_rid = jQuery(".xswcol-pid").val();
		jQuery.ajax({
			url: wcol_script_vars.ajax_url,
			type:'post',
			data:{'action':'wcol_load_new_row', 'row_for':'product' , 'wcol_pid': wcol_rid},
			success: function(res){
				wcol_rid++;
				jQuery(".xswcol-pid").val(wcol_rid);
				jQuery(table).append(res);
				jQuery('.wcol-new .wcol-select-products').select2({
					placeholder: 'Select Products',
					data:wcol_script_vars.products,
					width:"95%",
					multiple:true,
				});
				jQuery('.wcol-new .wcol-select-users').select2({
					placeholder: 'Select Users',
					data:wcol_script_vars.users,
					width:"95%",
					multiple:true
				});
				
				jQuery('.wcol-new .wcol-select-roles').select2({
					placeholder: 'Select Roles',
					data:wcol_script_vars.user_roles,
					width:"95%",
					multiple:true
				});
				
			}
		}).always(function(jqXHR, textStatus, errorThrown){
			if(textStatus !== 'success'){
				alert(errorThrown);
			}
			spinner.closest('form').css('pointer-events','auto');
			spinner.removeClass('wcol_is_active');
		});
		//jQuery(table).append(new_product_html);
	});
	
	jQuery('#wcol-add-category-rule').on('click', function(){
		jQuery(this).closest('form').addClass('xs-changed');
		jQuery('.wcol-rule-options').addClass('wcol-hidden');
		jQuery('.wcol-hide-more-options').addClass('wcol-hidden');
		jQuery('.wcol-show-more-options').removeClass('wcol-hidden');
		jQuery('.wcol-options-open').addClass('wcol-hidden');
		jQuery('table.wcol-collapsed').removeClass('wcol-collapsed');
		var table = jQuery(this).parent().parent().find('table.wp-list-table').find('tbody.wcol-main-body');
		jQuery('.wcol-new').removeClass('wcol-new');
		var spinner = jQuery(this).closest('.wcol-actions-btn').find('.wcol_spinner');
		spinner.closest('form').css('pointer-events','none');
		spinner.addClass('wcol_is_active');
		var wcol_rid = jQuery(".xswcol-cid").val();
		jQuery.ajax({
			url: wcol_script_vars.ajax_url,
			type:'post',
			data:{'action':'wcol_load_new_row', 'row_for':'product_cat' , 'wcol_cid': wcol_rid},
			success: function(res){
				jQuery(table).append(res);
				wcol_rid++;
				jQuery(".xswcol-cid").val(wcol_rid);
				jQuery('.wcol-select-categories').select2({
					placeholder: 'Select Categories',
					data:wcol_script_vars.categories,
					width:"95%",
					multiple:true
				});
			},
		}).always(function(jqXHR, textStatus, errorThrown){
			if(textStatus !== 'success'){
				alert(errorThrown);
			}
			spinner.closest('form').css('pointer-events','auto');
			spinner.removeClass('wcol_is_active');	
		});
		//jQuery(table).append(new_category_html);		
	});
	
	jQuery('.wcol-select-products').on('change',function(){
		var check = jQuery.inArray("-1" , jQuery(this).val() ); 
		if(check != "-1"){
			jQuery(this).val(['-1']).trigger('change.select2');
		}
	});
	jQuery('.wcol-select-categories').on('change',function(){
		var check = jQuery.inArray("-1" , jQuery(this).val() ); 
		if(check != "-1"){
			jQuery(this).val(['-1']).trigger('change.select2');
		}
	});
	jQuery('.wcol-select-all input').on('change', function(){
		if(jQuery(this).is(':checked')){
			jQuery(this).parent().parent().parent().parent().find('.wcol-cb input').attr('checked', true);
		}else{
			jQuery(this).parent().parent().parent().parent().find('.wcol-cb input').attr('checked', false);
		}
	});
	
	jQuery('.wcol-delete-selected').on('click', function(){
		if(window.confirm(wcol_script_vars.text3)){
			jQuery(this).parent().parent().parent().find('.wcol-cb input').each(function(){
				if(jQuery(this).is(':checked')){
					jQuery(this).parent().parent().remove();
				}
			});
		}
	});
	jQuery('.wp-list-table').on('click', '.wcol-show-more-options', function(e){
		e.preventDefault();
		jQuery('.wcol-rule-options').addClass('wcol-hidden');
		jQuery('.wcol-hide-more-options').addClass('wcol-hidden');
		jQuery('.wcol-show-more-options').removeClass('wcol-hidden');
		jQuery('.wcol-options-open').addClass('wcol-hidden');
		
		var position_top = jQuery(this).position().top-10;
		var position_top2 = position_top+1;
		var bgc = jQuery(this).parent().parent().parent().css('background-color');
		if(bgc=='rgba(0, 0, 0, 0)'){
			bgc = 'white';
		}
		
		jQuery(this).addClass('wcol-hidden');
		jQuery(this).parent().find('.wcol-hide-more-options').removeClass('wcol-hidden');
		
		jQuery(this).parent().find('.wcol-options-open').removeClass('wcol-hidden');
		jQuery(this).parent().find('.wcol-rule-options').removeClass('wcol-hidden');
		jQuery(this).parent().parent().parent().parent().parent().addClass('wcol-collapsed');
		jQuery(this).parent().parent().parent().parent().parent().parent().find('.wcol-actions-btn').addClass('wcol-collapsed');
		jQuery(this).parent().find('.wcol-rule-options').attr('style','top:'+position_top+'px ; background-color:'+bgc+';');
		jQuery(this).parent().find('.wcol-options-open').attr('style','top:'+position_top2 +'px ; background-color:'+bgc+';');
		jQuery(this).parent().find('.wcol-options-open').attr('style','top:'+position_top2 +'px ; background-color:'+bgc+'; height:'+jQuery(this).closest('tr').height()+'px ;');
	});
	
	jQuery('.wp-list-table').on('click', '.wcol-hide-more-options', function(e){
		e.preventDefault();
		jQuery(this).parent().find('.wcol-show-more-options').removeClass('wcol-hidden');
		jQuery(this).addClass('wcol-hidden');
		jQuery(this).parent().find('.wcol-options-open').addClass('wcol-hidden');
		jQuery(this).parent().find('.wcol-rule-options').addClass('wcol-hidden');
		jQuery('table.wcol-collapsed').removeClass('wcol-collapsed');
		jQuery('div.wcol-collapsed').removeClass('wcol-collapsed');
	});
	
	jQuery('.enable-cart-total-max-rule-limit').on('change', function(){
		if(jQuery(this).is(':checked')){
				jQuery(this).parent().parent().parent().find('.wcol-rule-max-limit').parent().parent().removeClass('wcol-hidden');
		}else{
			jQuery(this).parent().parent().parent().find('.wcol-rule-max-limit').parent().parent().addClass('wcol-hidden');
		}
	});
	jQuery('.wp-list-table , #wcol_tab , .form-table').on('change', '.enable-max-rule-limit' , function(){
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0 ){
			if(jQuery(this).is(':checked')){
				jQuery(this).parent().parent().find('.wcol-rule-max-limit').parent().removeClass('wcol-hidden');
			}else{
				jQuery(this).parent().parent().find('.wcol-rule-max-limit').parent().addClass('wcol-hidden');
			}
		}else if( window.location.href.indexOf('term.php') > 0 || window.location.href.indexOf('edit-tags.php') > 0 ){
			if(jQuery(this).is(':checked')){
				jQuery(this).parent().parent().find('.wcol-rule-max-limit').parent().removeClass('wcol-hidden');
			}else{
				jQuery(this).parent().parent().find('.wcol-rule-max-limit').parent().addClass('wcol-hidden');
			}
		}else{	
			if(jQuery(this).is(':checked')){
				jQuery(this).parent().parent().parent().find('.wcol-rule-max-limit').parent().parent().removeClass('wcol-hidden');
			}else{
				jQuery(this).parent().parent().parent().find('.wcol-rule-max-limit').parent().parent().addClass('wcol-hidden');
			}
		}
	});
		
	jQuery('.wp-list-table , #wcol_tab , .form-table').on('change', '.wcol-loop-checkbox', function(){
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0 ){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('p.form-field').find('.wcol-loop-checkbox-hidden').val('on');
			}else{
				jQuery(this).closest('p.form-field').find('.wcol-loop-checkbox-hidden').val('');
			}
		}else if( window.location.href.indexOf('term.php') > 0 || window.location.href.indexOf('edit-tags.php') > 0 ){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('p.form-field').find('.wcol-loop-checkbox-hidden').val('on');
			}else{
				jQuery(this).closest('p.form-field').find('.wcol-loop-checkbox-hidden').val('');
			}
		}else{
			if(jQuery(this).is(':checked')){
				jQuery(this).parent().find('.wcol-loop-checkbox-hidden').val('on');
			}else{
				jQuery(this).parent().find('.wcol-loop-checkbox-hidden').val('');
			}
		}
	});
	jQuery('.wcol_edit_rule').on('change', function(){
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0 ){
			if(jQuery(this).is(':checked') && jQuery(this).closest('.wc-metabox-content').find('.wcol_accomulative').val() != ''){
				jQuery(this).closest('.wc-metabox').removeClass('wcol_accomulative_rule');
				jQuery(this).closest('.wc-metabox-content').find('.wcol_accomulative').val('');
			}else{
				jQuery(this).closest('.wc-metabox').addClass('wcol_accomulative_rule');
				jQuery(this).closest('.wc-metabox-content').find('.wcol_accomulative').val('on');
			}
		}else{
			if(jQuery(this).is(':checked') && jQuery(this).closest('.wcol_single_cat_rule').find('.wcol_accomulative').val() != ''){
				jQuery(this).closest('.wcol_single_cat_rule').removeClass('wcol_accomulative_rule');
				jQuery(this).closest('.wcol_single_cat_rule').find('.wcol_accomulative').val('');
			}else{
				jQuery(this).closest('.wcol_single_cat_rule').addClass('wcol_accomulative_rule');
				jQuery(this).closest('.wcol_single_cat_rule').find('.wcol_accomulative').val('on');
			}
		}
		
	});
	jQuery('#wcol_add_new_rule').on('click', function(e){
		e.preventDefault();
		jQuery(this).closest('form').addClass('xs-changed');
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0 ){
			jQuery('#wcol_tab  .wc-metaboxes .wc-metabox').removeClass('open').addClass('closed').find('.wc-metabox-content').hide();
			jQuery('.wcol-new').removeClass('wcol-new');
			var spinner = jQuery(this).closest('#wcol_tab').find('.wcol_spinner');
			spinner.closest('form').css('pointer-events','none');
			spinner.addClass('wcol_is_active');
			var wcol_rid = jQuery(".xswcol-spid").val();
			wcol_rid++;
			jQuery.ajax({
				url: wcol_script_vars.ajax_url,
				type:'post',
				data:{'action':'wcol_load_new_row', 'row_for':'single_product', 'wcol_spid': wcol_rid },
				success: function(res){
					jQuery(".xswcol-spid").val(wcol_rid);
					jQuery('#wcol_tab .wc-metaboxes').append(res);
					jQuery('.wcol-new .wcol-select-users').select2({
						placeholder: 'Select Users',
						data:wcol_script_vars.users,
						width:"95%",
						multiple:true
					});
					jQuery('.wcol-new .wcol-select-roles').select2({
						placeholder: 'Select Roles',
						data:wcol_script_vars.user_roles,
						width:"95%",
						multiple:true
					});

				},
			}).always(function(jqXHR, textStatus, errorThrown){
				if(textStatus !== 'success'){
					alert(errorThrown);
				}
				spinner.closest('form').css('pointer-events','auto');
				spinner.removeClass('wcol_is_active');
			});
		}else{
			jQuery('.wcol_single_cat_rules .wcol_single_cat_rule').removeClass('wcol_single_cat_rule_open').find('.wcol_cat_panel').slideUp();
			jQuery('.wcol-new').removeClass('wcol-new');
			var spinner = jQuery(this).parent().find('.wcol_spinner');
			spinner.closest('form').css('pointer-events','none');
			spinner.addClass('wcol_is_active');
			var wcol_rid = jQuery(".xswcol-spcid").val();
			wcol_rid++;
			jQuery.ajax({
				url: wcol_script_vars.ajax_url,
				type:'post',
				data:{'action':'wcol_load_new_row', 'row_for':'single_product_cat' ,'wcol_spcid': wcol_rid},
				success: function(res){
					jQuery(".xswcol-spcid").val(wcol_rid);
					jQuery('.wcol_single_cat_rules').append(res);
					jQuery('.wcol-new .wcol-select-users').select2({
						placeholder: 'Select Users',
						data:wcol_script_vars.users,
						width:"95%",
						multiple:true
					});
					jQuery('.wcol-new .wcol-select-roles').select2({
						placeholder: 'Select Roles',
						data:wcol_script_vars.user_roles,
						width:"95%",
						multiple:true
					});
				},
			}).always(function(jqXHR, textStatus, errorThrown){
				if(textStatus !== 'success'){
					alert(errorThrown);
				}
				spinner.closest('form').css('pointer-events','auto');
				spinner.removeClass('wcol_is_active');
			});
		}
	});
	
	jQuery('.wcol_cat_accordion').on('click', function(){
		if(jQuery(this).closest('.wcol_single_cat_rule').hasClass('wcol_single_cat_rule_open')){
			jQuery(this).closest('.wcol_single_cat_rule').removeClass('wcol_single_cat_rule_open');
			jQuery(this).closest('.wcol_single_cat_rule').find('.wcol_cat_panel').slideUp();
		}else{
			jQuery(this).closest('.wcol_single_cat_rule').addClass('wcol_single_cat_rule_open');
			jQuery(this).closest('.wcol_single_cat_rule').find('.wcol_cat_panel').slideDown();
		}
	});
	
	jQuery('.wcol-delete').on('click', function(){
		var confirm_message = '';
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0 ){
			confirm_message = 'This will delete the rule when you will save/update the product!.';
		}else{
			confirm_message = 'This will delete the rule when you will save/update the category!.';
		}
		
		if( window.confirm(confirm_message) ){
			var rule_key = jQuery(this).parent().parent().find('input[name="wcol_rules[wcol_rule_key][]"]').val();
			if(rule_key){
				jQuery(this).parent().parent().addClass('wcol-deleted-rule').append('<input type="hidden" name="wcol_rules[wcol_deleted_rule_key][]" class="wcol_deleted_rule_key" value="'+rule_key+'">');
				jQuery(this).removeClass('wcol-delete').addClass('wcol-undo').text('Undo');
			}else{
				jQuery(this).parent().parent().remove();
			}
		}
	});
	
	jQuery('.wcol-undo').on('click', function(){
		jQuery(this).parent().parent().removeClass('wcol-deleted-rule').find('.wcol_deleted_rule_key').remove();
		jQuery(this).removeClass('wcol-undo').addClass('wcol-delete').text('Delete');
	});
	
	jQuery('.wp-list-table , #wcol_tab , .form-table').on('change','.wcol-disable-rule-limit', function(){
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-rule-max-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-rule-min-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-applied-on').addClass('wcol-disabled');	
				
				

			}else{
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-rule-max-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-rule-min-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-applied-on').removeClass('wcol-disabled');	
			}
		}else if(window.location.href.indexOf('term.php') > 0 || window.location.href.indexOf('edit-tags.php') > 0){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-rule-max-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-rule-min-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-applied-on').addClass('wcol-disabled');	
				
			}else{
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-rule-max-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-rule-min-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-applied-on').removeClass('wcol-disabled');	
			}
		}else{
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-categories').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-products').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-rule-min-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-applied-on').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-accomulative').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-editable').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.enable-max-rule-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-rule-max-limit').addClass('wcol-disabled');
			}else{
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-products').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-categories').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-rule-min-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-select-applied-on').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-accomulative').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-editable').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.enable-max-rule-limit').removeClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').closest('tr').find('.wcol-rule-max-limit').removeClass('wcol-disabled');
			}
		}
	});		
	jQuery('.wp-list-table , #wcol_tab , .form-table').on('change','.across-all-orders-limit', function(){
		if( window.location.href.indexOf('post.php') > 0 || window.location.href.indexOf('post-new') > 0){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit').prop('checked', true);
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit').addClass('wcol-disabled').closest('.options_group').removeClass('wcol-hidden');
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit-hidden').val('on');
				jQuery(this).closest('.wc-metabox-content').find('.wcol-rule-max-limit').closest('.form-field').removeClass('wcol-hidden');
				
			}else{
				jQuery(this).closest('.wc-metabox-content').find('.enable-max-rule-limit').removeClass('wcol-disabled');
			}
		}else if(window.location.href.indexOf('term.php') > 0 || window.location.href.indexOf('edit-tags.php') > 0){
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit').prop('checked', true);
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit').addClass('wcol-disabled').closest('.options_group').removeClass('wcol-hidden');
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit-hidden').val('on');
				jQuery(this).closest('.wcol_cat_panel').find('.wcol-rule-max-limit').closest('.form-field').removeClass('wcol-hidden');
				
			}else{
				jQuery(this).closest('.wcol_cat_panel').find('.enable-max-rule-limit').removeClass('wcol-disabled');
			}
		}else{
			if(jQuery(this).is(':checked')){
				jQuery(this).closest('.wcol-rule-options').find('.enable-max-rule-limit').prop('checked', true);
				jQuery(this).closest('.wcol-rule-options').find('.enable-max-rule-limit').addClass('wcol-disabled');
				jQuery(this).closest('.wcol-rule-options').find('.enable-max-rule-limit-hidden').val('on');
				jQuery(this).closest('.wcol-rule-options').find('.wcol-rule-max-limit').closest('tr').removeClass('wcol-hidden');
			}else{
				jQuery(this).closest('.wcol-rule-options').find('.enable-max-rule-limit').removeClass('wcol-disabled');
			}
		}
	});

	jQuery('.xs-wcol').on('click' , function(e){
		e.preventDefault();
		jQuery(this).closest('form').removeClass('xs-changed');
		jQuery.ajax({
			url: wcol_script_vars.ajax_url,
			type:'post',
			dataType: "json",
			beforeSend:function(){ 
				jQuery('.spinner').show();
				jQuery('.spinner').css('visibility', 'visible');
			},
			complete:function(){
				jQuery('.spinner').hide();
				jQuery('.spinner').css('visibility', 'hidden');
			},
			data:{
				action:'save_rules',
				rules:jQuery(this).closest('form').serialize(),
			},
			success: function(res){
				jQuery('.wcol-data-save-notice').show();
			},

		});
	});
	jQuery('.xs-wcol-notice-dismiss').on('click',function(){
		jQuery('.wcol-data-save-notice').hide();
	});
	jQuery('#xs_name , #xs_email , #xs_message').on('change',function(e){
        if(!jQuery(this).val()){
            jQuery(this).addClass("error");
        }else{
            jQuery(this).removeClass("error");
        }
    });
	jQuery('.xsollwc_support_form').on('submit' , function(e){ 
        e.preventDefault();
        jQuery('.xs-send-email-notice').hide();
        jQuery('.xs-mail-spinner').addClass('xs_is_active');
        jQuery('#xs_name').removeClass("error");
        jQuery('#xs_email').removeClass("error");
        jQuery('#xs_message').removeClass("error"); 
        jQuery.ajax({ 
            url:ajaxurl,
            type:'post',
            data:{'action':'xsollwc_support_form','data':jQuery(this).serialize()},
            beforeSend: function(){
            	if(!jQuery('#xs_name').val()){
                    jQuery('#xs_name').addClass("error");
                    jQuery('.xs-send-email-notice').removeClass('notice-success');
                    jQuery('.xs-send-email-notice').addClass('notice');
                    jQuery('.xs-send-email-notice').addClass('error');
                    jQuery('.xs-send-email-notice').addClass('is-dismissible');
                    jQuery('.xs-send-email-notice p').html('Please fill all the fields');
                    jQuery('.xs-send-email-notice').show();
                    jQuery('.xs-notice-dismiss').show();
                    window.scrollTo(0,0);
                    jQuery('.xs-mail-spinner').removeClass('xs_is_active');
                    return false;
                }
                 if(!jQuery('#xs_email').val()){
                    jQuery('#xs_email').addClass("error");
                    jQuery('.xs-send-email-notice').removeClass('notice-success');
                    jQuery('.xs-send-email-notice').addClass('notice');
                    jQuery('.xs-send-email-notice').addClass('error');
                    jQuery('.xs-send-email-notice').addClass('is-dismissible');
                    jQuery('.xs-send-email-notice p').html('Please fill all the fields');
                    jQuery('.xs-send-email-notice').show();
                    jQuery('.xs-notice-dismiss').show();
                    window.scrollTo(0,0);
                    jQuery('.xs-mail-spinner').removeClass('xs_is_active');
                    return false;
                }
                 if(!jQuery('#xs_message').val()){
                    jQuery('#xs_message').addClass("error");
                    jQuery('.xs-send-email-notice').removeClass('notice-success');
                    jQuery('.xs-send-email-notice').addClass('notice');
                    jQuery('.xs-send-email-notice').addClass('error');
                    jQuery('.xs-send-email-notice').addClass('is-dismissible');
                    jQuery('.xs-send-email-notice p').html('Please fill all the fields');
                    jQuery('.xs-send-email-notice').show();
                    jQuery('.xs-notice-dismiss').show();
                    window.scrollTo(0,0);
                    jQuery('.xs-mail-spinner').removeClass('xs_is_active');
                    return false;
                }
                jQuery(".xsollwc_support_form :input").prop("disabled", true);
                jQuery("#xs_message").prop("disabled", true);
               	jQuery('.xs-send-mail').prop('disabled',true);
            },
            success: function(res){
                jQuery('.xs-send-email-notice').find('.xs-notice-dismiss').show();
                jQuery('.xs-send-mail').prop('disabled',false);
                jQuery(".xsollwc_support_form :input").prop("disabled", false);
                jQuery("#xs_message").prop("disabled", false);
                if(res.status == true){
                    jQuery('.xs-send-email-notice').removeClass('error');
                    jQuery('.xs-send-email-notice').addClass('notice');
                    jQuery('.xs-send-email-notice').addClass('notice-success');
                    jQuery('.xs-send-email-notice').addClass('is-dismissible');
                    jQuery('.xs-send-email-notice p').html('Successfully sent');
                    jQuery('.xs-send-email-notice').show();
                    jQuery('.xs-notice-dismiss').show();
                    jQuery('.xsollwc_support_form')[0].reset();
                }else{
                    jQuery('.xs-send-email-notice').removeClass('notice-success');
                    jQuery('.xs-send-email-notice').addClass('notice');
                    jQuery('.xs-send-email-notice').addClass('error');
                    jQuery('.xs-send-email-notice').addClass('is-dismissible');
                    jQuery('.xs-send-email-notice p').html('Sent Failed');
                    jQuery('.xs-send-email-notice').show();
                    jQuery('.xs-notice-dismiss').show();
                }
                jQuery('.xs-mail-spinner').removeClass('xs_is_active');
            }

        });
    });
    jQuery('.notice-dismiss, .xs-notice-dismiss').on('click',function(e){
        e.preventDefault();
        jQuery(this).parent().hide();
        jQuery(this).hide();
    })
});