<?php
/*
Plugin Name: Utvecklarkollektivet Article Management Plugin
Version: 1.0
Author: Christoffer Artmann
Author URI: http://artmann.co/
License: Free for all
*/

require_once(dirname(__FILE__)."/article.php");

add_action( 'init', 'uamp_create_post_type' );
add_action( 'admin_init', 'uamp_admin' );
add_action( 'save_post', 'save_uamp_meta', 10, 2);
add_action( 'edit_post', 'save_uamp_meta', 10, 2);

register_activation_hook( __FILE__, 'uamp_create_db' );

function uamp_create_db() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	global $charset_collate;

	$wpdb->uamp_approves = "{$wpdb->prefix}uamp_approves";
	$sql_create_table = "CREATE TABLE {$wpdb->uamp_approves} (
          id bigint(20) unsigned NOT NULL auto_increment,
          user_id bigint(20) unsigned NOT NULL default '0',
          post_id bigint(20) unsigned NOT NULL default '0',
          description text NOT NULL default '',
          PRIMARY KEY  (id),
          KEY post_id (post_id)
     ) $charset_collate; ";
	dbDelta( $sql_create_table );

}

function uamp_create_post_type() {
	register_post_type( 'uamp_article',
		array(
			'labels' => array(
				'name' => __( 'Artiklar' ),
				'singular_name' => __( 'Artikel' )
			),
		'public' => true,
		'has_archive' => true,
		)
	);
}

function uamp_admin() {
	add_meta_box(
		"uamp_article_visibility",
    "Visibility",
    "uamp_display_visibility_meta",
    "uamp_article",
		"normal",
    "high"
	);
}

function uamp_display_visibility_meta($uamp_article) {
	$visibilty =get_post_meta($uamp_article->ID, "uamp_visibility", true);
	$soptions = array("Public", "Internal");
	echo "<select name=\"uamp_article_visibility\" >";
		foreach($soptions as $opt) {
			echo "<option value=\"$opt\" ";
				if($opt == $visibilty)
					echo "selected=selected";
			echo ">$opt</option>";
		}
	echo "</select>";
}

function save_uamp_meta($uamp_article_id, $uamp_article) {
	if($uamp_article->post_type == "uamp_article") {
		if(isset($_POST["uamp_article_visibility"])) {
			update_post_meta($uamp_article_id, "uamp_visibility", $_POST["uamp_article_visibility"]);
		}
	}
}

function include_template_function( $template_path ) {
	if(get_post_type() == "uamp_article") {
		if(is_single()) {
			if($theme_file = locate_template(array("single-uamp_article.php"))) {
				$template_path = $theme_file;
			}
			else {
				$template_path = plugin_dir_path( __FILE__ ) . 'single-uamp_article.php';
			}
		}
	}

  return $template_path;
}

add_filter( 'template_include', 'include_template_function', 1 );


