<?php
/**
 * RSS 1 RDF Feed Template for displaying RSS 1 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rdf') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	<?php do_action('rdf_ns'); ?>
>
<channel rdf:about="<?php bloginfo_rss("url") ?>">
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></dc:date>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<?php do_action('rdf_header'); ?>
	<items>
		<rdf:Seq>
		<?php while (have_posts()): the_post(); ?>
			<rdf:li rdf:resource="<?php the_permalink_rss() ?>"/>
		<?php endwhile; ?>
		</rdf:Seq>
	</items>
</channel>
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
<item rdf:about="<?php the_permalink_rss() ?>">
	<title><?php the_title_rss() ?></title>
	<link><?php the_permalink_rss() ?></link>
	 <dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', $post->post_date_gmt, false); ?></dc:date>
	<dc:creator><?php the_author() ?></dc:creator>
	<?php the_category_rss('rdf') ?>
<?php if (get_option('rss_use_excerpt')) : ?>
	<description><?php the_excerpt_rss() ?></description>
<?php else : ?>
	<description><?php the_excerpt_rss() ?></description>
	<content:encoded><![CDATA[<?php the_content_feed('rdf') ?>]]></content:encoded>
<?php endif; ?>
	<?php do_action('rdf_item'); ?>
</item>
	<?php restore_current_blog();?>
	<?php endforeach; ?>
</rdf:RDF>
