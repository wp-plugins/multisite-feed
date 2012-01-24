<?php
/*
Plugin Name: multisite-feed
Plugin URI: http://wordpress.org/extend/plugins/multisite-feed/
Description: All the multisite feeding is merged and it displays in order of new arrival.
Version: 1.0
Author: BRANU
Author URI: http://branu.jp/
*/


//標準のRSSテンプレートを削除
remove_filter('do_feed_rdf', 'do_feed_rdf', 10);
remove_filter('do_feed_rss', 'do_feed_rss', 10);
remove_filter('do_feed_rss2', 'do_feed_rss2', 10);
remove_filter('do_feed_atom', 'do_feed_atom', 10);


//RSS読み込みディレクトリ
define('MULTISITE_RSS_DIR', dirname(__FILE__).'/feed_template/');



//RDF
function custom_feed_rdf() {
	$template_file = MULTISITE_RSS_DIR.'feed-rdf.php';
	load_template( $template_file );
}
add_action('do_feed_rdf', 'custom_feed_rdf', 10, 1);


//RSS
function custom_feed_rss() {
	$template_file = MULTISITE_RSS_DIR.'feed-rss.php';
	load_template( $template_file );

}
add_action('do_feed_rss', 'custom_feed_rss', 10, 1);


//RSS2
function custom_feed_rss2() {
	$template_file = MULTISITE_RSS_DIR.'feed-rss2.php';
	load_template( $template_file );
}
add_action('do_feed_rss2', 'custom_feed_rss2', 10, 1);


//ATMO
function custom_feed_atom() {
	$template_file = MULTISITE_RSS_DIR.'feed-atom.php';
	load_template( $template_file );
}
add_action('do_feed_atom', 'custom_feed_atom', 10, 1);
