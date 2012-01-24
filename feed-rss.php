<?php
/**
 * RSS 0.92 Feed Template for displaying RSS 0.92 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss_head'); ?>

<?php
//postデータ変更
$sql = 'SELECT blog_id FROM '.$wpdb->blogs.' WHERE site_id='.$wpdb->siteid;
$blogs = $wpdb->get_results($sql);
$count = count($blogs);
$where = "WHERE post_type='post' AND post_status='publish'";
$orderby = "ORDER BY post_date DESC";
$limit = "LIMIT 10";

$sql = '';
for($i=0; $i<$count; $i++){
	switch_to_blog($blogs[$i]->blog_id);
	$sql.= "(SELECT *," . $blogs[$i]->blog_id . " AS blog_id FROM " . $wpdb->posts . " " . $where.")";
	if($i != ($count-1)){
		$sql.= "\nUNION\n";
	}
	restore_current_blog();
}
$sql.= " " . $orderby . " " . $limit . ";";
$results = $wpdb->get_results($sql);
?>
	<?php foreach($results as $post): setup_postdata($post); ?>
	<?php switch_to_blog($post->blog_id);?>
	<item>
		<title><?php the_title_rss() ?></title>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
		<link><?php the_permalink_rss() ?></link>
		<?php do_action('rss_item'); ?>
	</item>
	<?php restore_current_blog();?>
	<?php endforeach; ?>
</channel>
</rss>
