<?php
/*
Plugin Name: BP-TinyMCE
Plugin URI: http://teleogistic.net
Description: Puts TinyMCE on BP pages
Version: 0.1
Requires at least: BP 1.1
Author: Boone Gorges
Author URI: http://teleogistic.net
Site Wide Only: true
*/




function bp_tinymce_add_js() {
	global $bp;
	
	if ( $bp->current_action == 'forum' || $bp->current_action == 'home' || $bp->current_component == 'wire' || $bp->current_component == $bp->profile->slug || $bp->current_action == 'compose' ) {
		echo '<script language="javascript" type="text/javascript" src="',$bp->root_domain,'/',WPINC,'/js/tinymce/tiny_mce.js"></script>';
    	echo '<script language="javascript" type="text/javascript">';
    	echo 'tinyMCE.init({mode : "textareas", language : "en", theme : "advanced", theme_advanced_buttons1 : "bold,italic,bullist,numlist,blockquote,link,unlink", theme_advanced_buttons2 : "", theme_advanced_buttons3 : "", language : "en",theme_advanced_toolbar_location : "top", theme_advanced_toolbar_align : "left"});';
    	echo '</script>';

	}

}
add_action( 'wp_head', 'bp_tinymce_add_js', 1 );

function bp_tinymce_allowed_tags($c) {
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

	$search = array(	'&lt;p&gt;',
						'&lt;/p&gt;',
						'&lt;br&gt;',
						'&lt;br /&gt;',	
						'&lt;em&gt;',
						'&lt;/em&gt;',
						'&lt;i&gt;',
						'&lt;/i&gt;',
						'&lt;strong&gt;',
						'&lt;/strong&gt;',
						'&lt;b&gt;',
						'&lt;/b&gt;',
						'&lt;/a&gt;',
						'&lt;ol&gt;',
						'&lt;/ol&gt;',
						'&lt;ul&gt;',
						'&lt;/ul&gt;',
						'&lt;li&gt;',
						'&lt;/li&gt;',
						'&lt;blockquote&gt;',
						'&lt;/blockquote&gt;',
					);
	$replace = array(	'<p>',
						'</p>',
						'<br>',
						'<br />',
						'<em>',
						'</em>',
						'<em>',
						'</em>',
						'<strong>',
						'</strong>',
						'<strong>',
						'</strong>',
						'</a>',
						'<ol>',
						'</ol>',
						'<ul>',
						'</ul>',
						'<li>',
						'</li>',
						'<blockquote>',
						'</blockquote>',
					);
	$c = preg_replace( "/&lt;a (title.*?)?href=&quot;http:([a-zA-Z_.\/-]+?)&quot;( target.*?)?&gt;/", '<a href="http://$1">', $c );
	$c = preg_replace( '/&lt;span style=&quot;text-decoration: underline;?&quot;&gt;(.*?)&lt;\/span&gt;/', '<span style="text-decoration: underline">$1</span>', $c );
	
	$c = str_replace( $search, $replace, $c );
	
	return wp_kses( $c, $allowedtags );
}
add_filter( 'bp_get_the_topic_post_content', 'bp_tinymce_allowed_tags', 10, 1 );
add_filter( 'bp_get_activity_content', 'bp_tinymce_allowed_tags', 2 );
add_filter( 'bp_wire_post_content_before_save', 'bp_tinymce_allowed_tags', 2 );
add_filter( 'bp_get_wire_post_content', 'bp_tinymce_allowed_tags', 2 );

?>