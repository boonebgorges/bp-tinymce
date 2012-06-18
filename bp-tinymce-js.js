


jQuery(document).ready( function($) {

	var eid = '';
	var thisarea = '';
	$('textarea').each(function(i,v){
		eid = $(v).attr('id');
		$.ajax({
			url: ajaxurl,
			data: { action: 'bp_tinymce_editor', eid: eid, area_content: $(v).html() },
			success: function(data){
				$(v).replaceWith(data);

				// For some reason we need to get a fresh id
				var ps = $(data).attr('id').split('-');
				var the_true_id = ps[1];

				//var foo = tinyMCE.execCommand("mceAddControl", true, v);

				// Switch to Visual
				switchEditors.go( the_true_id, 'tmce' );

				// Manually remove inline height. Groan.
				$('iframe#' + the_true_id + '_ifr').css('height', '');

				// Hack. If this is a message reply, keep it from submitting via
				// AJAX
				if ( the_true_id == 'message_content' ) {
					var send_button = $('#send_reply_button');
					if ( 0 != send_button.length ) {
						$(send_button).unbind('click');
					}
				}
			}
		});
	});
},jQuery);