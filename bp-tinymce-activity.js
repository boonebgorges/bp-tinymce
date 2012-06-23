jQuery(document).ready( function($) {

	var configArray = [
		{
			theme : "advanced",
			mode : "none",
			language : "en",
			theme_advanced_layout_manager : "SimpleLayout",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : ""
		},
		{
			theme : "advanced",
			mode : "none",
			language : "en",
			width:"100%",
			theme_advanced_layout_manager : "SimpleLayout",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : ""
		}
	];

	tinyMCE.settings = configArray[0];
	tinyMCE.execCommand('mceAddControl',true,'whats-new');

	$("#whats-new-options").animate({
		height:'40px'
	});
	$("form#whats-new-form textarea").animate({
		height:'50px'
	});
	$("#aw-whats-new-submit").prop("disabled", false);

	// Main "what's new"
	$("input#aw-whats-new-submit").bind( 'mouseenter', function() {
		tinyMCE.triggerSave();
	});

	// A hack to re-expand the What's New submit button after first submit
	$.propHooks.disabled = {
		set: function(elem, value) {
			if ($(elem).attr('name').toLowerCase() == 'aw-whats-new-submit' && value === true) {
				$("#whats-new-options").animate(
					{height:'40px'},
					500,
					function() {
						$('#aw-whats-new-submit').removeAttr('disabled');
					}
				);

				$("form#whats-new-form textarea").animate({
					height:'50px'
				});

				$('#whats-new_ifr').contents().find('#tinymce').html('');

				return undefined;
			}

			return undefined;
		}
	};

	// When clicking Reply or Comment, dynamically add the TinyMCE control
	// Doing it this way reduces overhead by not creating zillions of instances on a page
	// The timeout is necessary to allow global.js to finish moving form to its final location
	$('#activity-stream').on('click','a',function(e){
		var timeoutHandler;

		if ( $(e.target).hasClass('acomment-reply') ) {
			timeoutHandler = setTimeout( function() {
				var aparent = $(e.target).closest("ul#activity-stream > li");
				var tarea = $(aparent).find('textarea.ac-input');

				tinyMCE.settings = configArray[1];
				tinyMCE.execCommand('mceAddControl',true, $(tarea).attr('id'));
				tinymce.execCommand('mceFocus',true,$(tarea).attr('id'));
			}, 50 );
		}
	});

	// Remove existing reply instances, so TinyMCE doesn't get confused when we rearrange
	// the DOM
	$('#activity-stream').on('mouseenter', 'a', function(e){
		if ( $(e.target).hasClass('acomment-reply') ) {
			for (edId in tinyMCE.editors) {
			    if ( 'whats-new' !== tinyMCE.editors[edId].editorId ) {
				   tinymce.get(edId).setContent('');
				   tinyMCE.execCommand('mceRemoveControl', false, edId);
			    }
			}
		}
	});

	// Save the content back to the textarea when submitting activity comments
	$('#activity-stream').on('click','input',function(e){
		if ( $(e.target).attr('name') == 'ac_form_submit' ) {
			tinyMCE.triggerSave();
		}
	});

},jQuery);

function bptinymce_onchange() {
	jQuery('#aw-whats-new-submit').removeProp('disabled');
}