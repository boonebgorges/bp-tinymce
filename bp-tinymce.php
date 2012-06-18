<?php

if ( !class_exists( 'BP_TinyMCE' ) ) :

class BP_TinyMCE {
	var $is_teeny;
	var $init_filter;

	var $textarea_id;

	var $enabled_components;

	function bp_tinymce() {
		$this->__construct();
	}

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ), 1 );

		add_action( 'wp_ajax_bp_tinymce_editor', array( &$this, 'handler' ) );

		$this->is_teeny = apply_filters( 'bp_tinymce_is_teeny', false );
		$this->init_filter = $this->is_teeny ? 'teeny_mce_before_init' : 'tiny_mce_before_init';

		$this->enabled_components = apply_filters( 'bp_tinymce_enabled_components', array(
			'forums',
			//'activity',
			'xprofile',
			'groups',
			'messages'
		) );

		foreach( $this->enabled_components as $component  ) {
			add_filter( 'bp_forums_allowed_tags', array( $this, 'allowed_tags' ), 1 );
			add_filter( 'bp_activity_allowed_tags', array( $this, 'allowed_tags' ), 1 );
			add_filter( 'bp_' . $component . "_allowed_tags", array( $this, 'allowed_tags' ), 1 );
			add_filter( "bp_" . $component . "_filter_kses", array( $this, 'allowed_tags' ), 1 );
		}

		// Hack
		if ( in_array( 'messages', $this->enabled_components ) ) {
			add_action( 'bp_actions', array( &$this, 'messages_send_hack' ), 1 );
		}
	}

	function handler() {
		$args = array(
			'media_buttons' => false,
			'teensy' => isset( $_REQUEST['teensy'] ) && '1' == $_REQUEST['teensy'] ? true : false
		);

		// This is not ideal, but we need to keep double escaping from happening
		add_filter( 'the_editor_content', function( $content ) {
			remove_filter('the_editor_content', 'wp_richedit_pre');
			return $content;
		}, 8 );

		echo wp_editor( $_REQUEST['area_content'], $_REQUEST['eid'], $args );
		die();
	}

	function enqueue_script() {
		global $bp;

		if ( $this->enable_tinymce_on_page() ) {

			if ( ! class_exists('_WP_Editors' ) )
				require_once( ABSPATH . WPINC . '/class-wp-editor.php' );

			$set = array(
				'teeny' => $this->is_teeny,
				'tinymce' => array( 'mode' => 'exact' ),
				'quicktags' => false
			);

			foreach( (array) $this->textarea_id as $tid ) {
				$set = _WP_Editors::parse_settings( $tid, $set );
				_WP_Editors::editor_settings( $tid, $set );
			}

			// We have to deregister the DTheme ajax and reregister it to be dependent
			// on our own, so that our click events are registered before bp-default's
			wp_deregister_script( 'dtheme-ajax-js' );

			// Register our custom JS
			wp_register_script('bp-tinymce-js', WP_PLUGIN_URL . '/bp-tinymce/bp-tinymce-js.js', array( 'jquery' ) );
			wp_enqueue_script( 'bp-tinymce-js' );

			//wp_localize_script( 'bp-tinymce-js', 'BP_TinyMCE', $textarea_whitelist );

			// Reload bp-default ajax
			wp_enqueue_script( 'dtheme-ajax-js', get_template_directory_uri() . '/_inc/global.js', array( 'jquery', 'bp-tinymce-js' ) );

			// Enqueue the styles
			wp_enqueue_style( 'bp-tinymce-css', WP_PLUGIN_URL . '/bp-tinymce/bp-tinymce-css.css' );
		}
	}

	function enable_tinymce_on_page() {
		$enable = false;

		// Group forums
		if ( bp_is_group_forum() && in_array( 'forums', $this->enabled_components ) ) {
			if ( bp_is_group_forum_topic() ) {
				$this->textarea_id = 'reply_text';
			} else {
				$this->textarea_id = 'topic_text';
			}
		}

		// Group edit-details (description)
		if ( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) && in_array( 'groups', $this->enabled_components ) ) {
			$this->textarea_id = 'group-desc';
		}

		// Messages
		if ( bp_is_messages_component() && in_array( 'messages', $this->enabled_components ) ) {
			$this->textarea_id = 'message_content';
		}

		// Profile edit
		if ( bp_is_user_profile_edit() && in_array( 'xprofile', $this->enabled_components ) ) {
			$args = array(
				'hide_empty_fields' => false,
				'fetch_field_data' => false
			);

			if ( bp_has_profile( $args ) ) {
				while ( bp_profile_groups() ) {
					bp_the_profile_group();

					global $profile_template;
					foreach( (array) $profile_template->group->fields as $f ) {
						if ( 'textarea' == $f->type ) {
							$this->textarea_id[] = 'field_' . $f->id;
						}
					}
				}
			}
		}

		// "What's new" activity update
		if ( bp_is_activity_component() && in_array( 'activity', $this->enabled_components ) ) {
			$this->textarea_id = 'whats-new';
		}

		// todo
		return apply_filters( 'bp_tinymce_enable_on_page', (bool) $this->textarea_id );
	}

	function allowed_tags( $allowedtags ) {
		global $allowedtags;

		$allowedtags['em']     = array();
		$allowedtags['strong'] = array();
		$allowedtags['ol']     = array();
		$allowedtags['del']    = array();
		$allowedtags['li']     = array();
		$allowedtags['ul']     = array();
		$allowedtags['blockquote'] = array();
		$allowedtags['code']   = array();
		$allowedtags['pre']    = array();
		$allowedtags['a']      = array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
		);
		$allowedtags['img']     = array(
			'src' => array(),
		);
		$allowedtags['b']       = array();
		$allowedtags['span']    = array(
			'style' => array(),
		);
		$allowedtags['p']       = array(
			'style' => array()
		);
		$allowedtags['br']      = array();
		$allowedtags['pre']     = array();
		$allowedtags['h1']      = array();
		$allowedtags['h2']      = array();
		$allowedtags['h3']      = array();
		$allowedtags['h4']      = array();
		$allowedtags['address'] = array();

		return apply_filters( 'bp_tinymce_allowedtags', $allowedtags );
	}

	/**
	 * Copy the content of the 'message_content' key into 'content'
	 */
	function messages_send_hack() {
		if ( bp_is_messages_component() && isset( $_POST['send'] ) && empty( $_POST['content'] ) && !empty( $_POST['message_content'] ) ) {
			$_POST['content'] = $_POST['message_content'];
		}
	}
}

endif;

$bp_tinymce = new BP_TinyMCE;

?>
