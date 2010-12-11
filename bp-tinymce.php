<?php

if ( !class_exists( 'BP_TinyMCE' ) ) :

class BP_TinyMCE {

	function bp_tinymce() {
		$this->__construct();
	}
	
	function __construct() {
		add_action( 'wp_head', array( $this, 'add_js' ), 1 );
		add_action( 'init', array( $this, 'enqueue_script' ) );
		
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
		
		if ( 
			$bp->current_component == $bp->activity->slug || 
			$bp->current_action == 'forum' || 
			$bp->current_action == 'home' || 
			$bp->current_component == $bp->wire->slug || 
			$bp->current_component == $bp->profile->slug || 
			$bp->curront_component = $bp->messages->slug
		) {
			?>		
			<script language="javascript" type="text/javascript" src="<?php echo $bp->root_domain ?>/<?php echo WPINC ?>/js/tinymce/tiny_mce.js"></script>
			<script type='text/javascript' src='<?php echo $baseurl ?>/langs/wp-langs-en.js?'></script>
			
			<script language="javascript" type="text/javascript">
				<?php echo apply_filters( 'bp_tinymce_tmce_settings', 'tinyMCE.init({mode : "specific_textareas", editor_deselector: "ac-input", language : "wp-langs-en", theme : "advanced", theme_advanced_buttons1 : "bold,italic,bullist,numlist,blockquote,link,unlink", theme_advanced_buttons2 : "", theme_advanced_buttons3 : "", language : "en",theme_advanced_toolbar_location : "top", theme_advanced_toolbar_align : "left"});' ) ?>
			</script>
			<?php
	
		}
	
	}
	
	function enqueue_script() {
		global $bp;
		if ( $bp->current_action == 'home' || $bp->current_component == $bp->activity->slug || bp_is_messages_component() ) {
			wp_register_script('bp-tinymce-js', WP_PLUGIN_URL . '/bp-tinymce/bp-tinymce-js.js');
			wp_enqueue_script( 'bp-tinymce-js' );
		}
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
