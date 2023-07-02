<?php
/**
* RSS2 Feed Template for displaying RSS2 Posts feed.
*
* @package SmartNews
*/

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
$more = 1;

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';

/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action( 'rss_tag_pre', 'rss2' );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:media="http://search.yahoo.com/mrss/"
  xmlns:snf="http://www.smartnews.be/snf"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss( 'url' ); ?></link>
	<description><?php bloginfo_rss( 'description' ); ?></description>
	<lastBuildDate>
	<?php
		$date = get_lastpostmodified( 'GMT' );
		echo $date ? mysql2date( 'r', $date, false ) : date( 'r' );
	?>
	</lastBuildDate>
	<copyright><?php bloginfo_rss('name'); ?> All rights reserved.</copyright>
	<?php
	$logo_url = get_the_site_logo_url();
	if (!$logo_url) {
		$logo_url = get_amp_logo_image_url();
	}
	$logo_url = apply_filters('smartnews_logo_url', $logo_url);
	?>
	<snf:logo>
			<url><?php echo $logo_url; ?></url>
	</snf:logo>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod>
	<?php
		$duration = 'hourly';

		/**
		 * Filters how often to update the RSS feed.
		 *
		 * @since 2.1.0
		 *
		 * @param string $duration The update period. Accepts 'hourly', 'daily', 'weekly', 'monthly',
		 *                         'yearly'. Default 'hourly'.
		 */
		echo apply_filters( 'rss_update_period', $duration );
	?>
	</sy:updatePeriod>
	<sy:updateFrequency>
	<?php
		$frequency = '1';

		/**
		 * Filters the RSS update frequency.
		 *
		 * @since 2.1.0
		 *
		 * @param string $frequency An integer passed as a string representing the frequency
		 *                          of RSS updates within the update period. Default '1'.
		 */
		echo apply_filters( 'rss_update_frequency', $frequency );
	?>
	</sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head' );

	while ( have_posts() ) :
		the_post();
		?>
	<item>
		<title><?php the_title_rss(); ?></title>
		<link><?php the_permalink_rss(); ?></link>
		<?php if ( get_comments_number() || comments_open() ) : ?>
		<comments><?php comments_link_feed(); ?></comments>
		<?php endif; ?>
		<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
		<dc:creator><![CDATA[<?php the_author(); ?>]]></dc:creator>
		<?php the_category_rss( 'rss2' ); ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>

		<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php echo the_excerpt_rss(); ?>]]></description>
		<?php endif; ?>
		<?php
		$content = get_the_content_feed('rss2');
		//aリンクは含めない。SmartNewsの仕様？リンクが多くあると以下のエラーが出る
		//item.content:encoded の記事内に多くのリンクが含まれています - item.title: 記事のタイトル
		//https://publishers.smartnews.com/ja/smartformat/specification_rss/
		$content = preg_replace('{<a [^>]+?>}i', '', $content);
		$content = str_replace('</a>', '', $content);
		//ブログカードリンクは取り除く
		//https://wp-cocoon.com/community/postid/24925/
		$content = preg_replace('/^.*blogcard.*$(\r\n|\r|\n)/um', ' ', $content);
		$content = apply_filters('get_the_smartnews_content', $content);
		 ?>
		<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>

		<?php if ( get_comments_number() || comments_open() ) : ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
		<?php endif; ?>
		<?php rss_enclosure(); ?>
		<?php
		/**
		 * Fires at the end of each RSS2 feed item.
		 *
		 * @since 2.0.0
		 */
		do_action( 'rss2_item' );
		?>
		<?php //アイキャッチの取得
		$image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id, true);
		if (isset($image_url[0])) {
			$thumbnail = $image_url[0];
		} else {
			$thumbnail = get_no_image_large_url();
		}
    ?>
		<media:thumbnail url="<?php echo $thumbnail; ?>" />
		<?php //Googleアナリティクス4トラッキングID
		$tracking_id = get_ga4_tracking_id();
		if ($tracking_id): ?>
			<snf:analytics ><![CDATA[
				<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $tracking_id; ?>"></script>
				<script>
						window.dataLayer = window.dataLayer || [];
						function gtag(){dataLayer.push(arguments);}
						gtag('js', new Date());

						gtag('config', '<?php echo $tracking_id; ?>',{'page_path':'<?php echo str_replace(home_url(), '', get_permalink()); ?>',
						'page_referrer':'http://www.smartnews.com/',
						'campaign_source':'SmartNews',
						'campaign_medium':'app'
						});
				</script>
				]]>
			</snf:analytics>
		<?php endif; ?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>
