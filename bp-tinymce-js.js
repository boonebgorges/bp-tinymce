jQuery(document).ready( function() {
	var j = jQuery;
	
	j("input#whats-new-submit").unbind( 'click' );
	
		j("input#whats-new-submit").click( function() {
		var button = j(this);
		var form = button.parent().parent().parent().parent();

		form.children().each( function() {
			if ( j.nodeName(this, "textarea") || j.nodeName(this, "input") )
				j(this).attr( 'disabled', 'disabled' );
		});
		var content = j('#whats-new_ifr').contents().find('#tinymce').html();

		j( 'form#' + form.attr('id') + ' span.ajax-loader' ).show();

		/* Remove any errors */
		j('div.error').remove();
		button.attr('disabled','disabled');

		j.post( ajaxurl, {
			action: 'post_update',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce_post_update': j("input#_wpnonce_post_update").val(),
			
			'content': content,
			
			'group': j("#whats-new-post-in").val()
		},
		function(response)
		{
			j( 'form#' + form.attr('id') + ' span.ajax-loader' ).hide();

			form.children().each( function() {
				if ( j.nodeName(this, "textarea") || j.nodeName(this, "input") )
					j(this).attr( 'disabled', '' );
			});

			/* Check for errors and append if found. */
			if ( response[0] + response[1] == '-1' ) {
				form.prepend( response.substr( 2, response.length ) );
				j( 'form#' + form.attr('id') + ' div.error').hide().fadeIn( 200 );
				button.attr("disabled", '');
			} else {
				if ( 0 == j("ul#activity-list").length ) {
					j("div.error").slideUp(100).remove();
					j("div.activity").append( '<ul id="activity-list" class="activity-list item-list">' );
				}

				j("ul#activity-list").prepend(response);
				j("li.new-update").hide().slideDown( 300 );
				j("li.new-update").removeClass( 'new-update' );
				j("textarea#whats-new").val('');

				/* Re-enable the submit button after 8 seconds. */
				setTimeout( function() { button.attr("disabled", ''); }, 8000 );
			}
		});

		return false;
	});


	});