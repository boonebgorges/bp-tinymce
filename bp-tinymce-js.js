jQuery(document).ready( function() {
	var j = jQuery;
	
	j("input#aw-whats-new-submit").click( function() {
		var content = j('#whats-new_ifr').contents().find('#tinymce').html();
		j("textarea#whats-new").val(content);
		j('#whats-new_ifr').contents().find('#tinymce').html('');
	});
	
	j("input#send_reply_button").click( function() {
		var content = j('#message_content_ifr').contents().find('#tinymce').html();
		j("textarea#message_content").val(content);
		j('#whats-new_ifr').contents().find('#tinymce').html('');
	});
});