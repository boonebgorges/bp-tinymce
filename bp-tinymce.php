<?php

// Massive hack. Loads my JS before BP's so that my click event is registered first.
// Talk to BP team about the craptastic way BP uses wp_enqueue_script()

if ( !class_exists( 'BP_TinyMCE' ) ) :

class BP_TinyMCE {

	function bp_tinymce() {
		$this->__construct();
	}
	
	function __construct() {
		add_action( 'bp_before_container', array( $this, 'add_js' ), 1 );
		add_action( 'init', array( $this, 'enqueue_script' ), 1 );
		
		// Some components have filterable allowedtags lists. 
		$tinymce_components = array(
			'forums',
			'activity' 
		);
		
		foreach( $tinymce_components as $component  ) {
			add_filter( "bp_$component_allowed_tags", array( $this, 'allowed_tags' ), 1 );
		}
	}

	function add_js() {
		global $bp;
		
		$baseurl = includes_url('js/tinymce');
		
		if ( $this->enable_tinymce_on_page() ) {
			wp_tiny_mce();
		}
	
	}
	
	function enqueue_script() {
		global $bp;
		
		if ( $this->enable_tinymce_on_page() ) {
			add_action( 'tiny_mce_before_init', array( $this, 'tinymce_init_params' ) );
		
			require_once( ABSPATH . '/wp-admin/includes/post.php' );
			//wp_enqueue_script( 'common' );
			//wp_enqueue_script( 'jquery-color' );
			wp_enqueue_script( 'editor' );
			//if ( function_exists( 'add_thickbox' ) ) 
				//add_thickbox();
			//wp_enqueue_script( 'utils' );
			//wp_enqueue_script( 'autosave' );
		
			wp_register_script('bp-tinymce-js', WP_PLUGIN_URL . '/bp-tinymce/bp-tinymce-js.js', array( 'jquery' ) );
			wp_enqueue_script( 'bp-tinymce-js' );
			
			
			
			
			wp_enqueue_style( 'bp-tinymce-css', WP_PLUGIN_URL . '/bp-tinymce/bp-tinymce-css.css' );
		}
	}
	
	function tinymce_init_params( $initArray ) {
		//var_dump( 'bp_tinymce_tmce_settings', 'tinyMCE.init({mode : "specific_textareas", editor_deselector: "ac-input", language : "wp-langs-en", theme : "advanced", theme_advanced_buttons1 : "bold,italic,bullist,numlist,blockquote,link,unlink", theme_advanced_buttons2 : "", theme_advanced_buttons3 : "", language : "en",theme_advanced_toolbar_location : "top", theme_advanced_toolbar_align : "left"});' );
			
		//var_dump( $initArray );

		$plugins 	= explode( ',', $initArray['plugins'] );		

		// Remove internal linking
		$wplink_key = array_search( 'wplink', $plugins );
		if ( $wplink_key ) {
			unset( $plugins[$wplink_key] );
		}
		
		unset( $initArray['editor_selector'] );
		
		$plugins = array_values( $plugins );	
		
		$initArray['plugins'] = implode( ',', $plugins );
		
		return $initArray;
	}

	
	function enable_tinymce_on_page() {
			return true;
	}
	
	function allowed_tags( $allowedtags ) {
		global $allowedtags;
		
		$allowedtags['em'] = array();
		$allowedtags['strong'] = array();
		$allowedtags['ol'] = array();
		$allowedtags['li'] = array();
		$allowedtags['ul'] = array();
		$allowedtags['blockquote'] = array();
		$allowedtags['code'] = array();
		$allowedtags['pre'] = array();
		$allowedtags['a'] = array(
		'href' => array(),
		'title' => array(),
		'target' => array(),
		);
		$allowedtags['img'] = array(
		'src' => array(),
		);
		$allowedtags['b'] = array();
		$allowedtags['span'] = array(
		'style' => array(),
		);
		$allowedtags['p'] = array();
		$allowedtags['br'] = array();
		
		return apply_filters( 'bp_tinymce_allowedtags', $allowedtags );
	}
}

endif;

$bp_tinymce = new BP_TinyMCE;

?>
