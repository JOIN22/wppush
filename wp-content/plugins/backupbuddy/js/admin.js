jQuery(document).ready(function() {
	jQuery('.ithemes_tip').tooltip({ 
		track: true, 
		delay: 0, 
		showURL: false, 
		showBody: " - ", 
		fade: 250 
	});

	jQuery('.ithemes_pop').click(function(e) {
		showpopup('#'+jQuery(this).attr('href'),'',e);		
		return false;		
	});
	
	jQuery('.ithemes_email_pop').click(function(e) {
		jQuery('#email_file_name').val( jQuery('#file_name_'+jQuery(this).attr("id").replace('email_pop_', "")).val() );
		return false;		
	});
	
	jQuery('.ithemes_ftp_pop').click(function(e) {
		jQuery('#ftp_file_name').val( jQuery('#file_name_'+jQuery(this).attr("id").replace('ftp_pop_', "")).val() );
		return false;
	});
	
	jQuery('.ithemes_aws_pop').click(function(e) {
		jQuery('#aws_file_name').val( jQuery('#file_name_'+jQuery(this).attr("id").replace('aws_pop_', "")).val() );
		return false;
	});
	
	jQuery('#ithemes_datetime').datepicker({  
		duration: '',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		altTimeField: '',  
		time24h: false  
	});
	
	jQuery('#remote_send').change(function(e) {
		if ( jQuery('#remote_send').val() != 'none' ) {
			jQuery('#ithemes-backupbuddy-deleteafter').slideDown();
		} else {
			jQuery('#ithemes-backupbuddy-deleteafter').slideUp();
		}
	});
	
	jQuery('#ithemes_backupbuddy_ftptest').click(function(e) {
		jQuery('#ithemes_backupbuddy_ftpresponse').html('Testing ... Please wait ...');
		jQuery.post(jQuery(this).attr('alt'), { action: "ftp_test", server: jQuery('#ftp_server').val(), user: jQuery('#ftp_user').val(), pass: jQuery('#ftp_pass').val(), path: jQuery('#ftp_path').val(), type: jQuery('#ftp_type').val() }, 
			function(data) {
				jQuery('#ithemes_backupbuddy_ftpresponse').html( data );
			}
		); //,"json");
		return false;
	});
	
	jQuery('#ithemes_backupbuddy_awstest').click(function(e) {
		jQuery('#ithemes_backupbuddy_awsresponse').html('Testing ... Please wait ...');
		jQuery.post(jQuery(this).attr('alt'), { action: "aws_test", aws_accesskey: jQuery('#aws_accesskey').val(), aws_secretkey: jQuery('#aws_secretkey').val(), aws_bucket: jQuery('#aws_bucket').val(), aws_ssl: jQuery('#aws_ssl').val() }, 
			function(data) {
				jQuery('#ithemes_backupbuddy_awsresponse').html( data );
			}
		); //,"json");
		return false;
	});
	
});
