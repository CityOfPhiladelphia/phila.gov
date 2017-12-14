<?php
/**
 * RSS2 Feed Template for displaying MLA Gallery feed.
 *
 * @package MLA Custom Feed Example
 */
header('Content-Type: ' . feed_content_type( MLACustomFeedExample::$active_feed['type'] ) . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';

/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action( 'rss_tag_pre', MLACustomFeedExample::$active_feed['type'] );
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
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
	<title><?php echo MLACustomFeedExample::$active_feed['title']; ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo MLACustomFeedExample::$active_feed['link']; ?></link>
	<description><?php echo MLACustomFeedExample::$active_feed['description']; ?></description>
	<lastBuildDate><?php
		echo MLACustomFeedExample::$active_feed['last_build_date'];
	?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
<?php
if ( !empty( MLACustomFeedExample::$active_feed['ttl'] ) ) {
	echo '<ttl>' . absint( MLACustomFeedExample::$active_feed['ttl'] ) . "</ttl>\n";
}

if ( 'none' !== MLACustomFeedExample::$active_feed['update_period'] ) :
 ?>	<sy:updatePeriod><?php

		/**
		 * Filters how often to update the RSS feed.
		 *
		 * @since 2.1.0
		 *
		 * @param string $update_period The update period. Accepts 'hourly', 'daily', 'weekly', 'monthly',
		 *                         'yearly'. Default 'hourly'.
		 */
		echo apply_filters( 'rss_update_period', MLACustomFeedExample::$active_feed['update_period'] );
	?></sy:updatePeriod>
	<sy:updateFrequency><?php

		/**
		 * Filters the RSS update frequency.
		 *
		 * @since 2.1.0
		 *
		 * @param string $update_frequency An integer passed as a string representing the frequency
		 *                          of RSS updates within the update period. Default '1'.
		 */
		echo apply_filters( 'rss_update_frequency', MLACustomFeedExample::$active_feed['update_frequency'] );
	?></sy:updateFrequency>
	<sy:updateBase><?php

		/**
		 * Filters the RSS update frequency.
		 *
		 * @since 2.1.0
		 *
		 * @param string $update_base An integer passed as a string representing the frequency
		 *                          of RSS updates within the update period. Default '1'.
		 */
		echo apply_filters( 'rss_update_base', MLACustomFeedExample::$active_feed['update_base'] );
	?></sy:updateBase>
	<?php
	endif;
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head');

	while ( MLACustomFeedExample::$wp_query_object->have_posts() ) : MLACustomFeedExample::$wp_query_object->the_post(); 
	?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
<?php if ( get_comments_number() || comments_open() ) : ?>
		<comments><?php comments_link_feed(); ?></comments>
<?php endif; ?>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><![CDATA[<?php the_author() ?>]]></dc:creator>
		<?php echo MLACustomFeedExample::mla_get_the_terms_rss( MLACustomFeedExample::$active_feed['taxonomies'], MLACustomFeedExample::$active_feed['type'] ); ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
<?php else : ?>
		<description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
	<?php $content = get_the_content_feed('rss2'); ?>
	<?php if ( strlen( $content ) > 0 ) : ?>
		<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
	<?php else : ?>
		<content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
	<?php endif; ?>
<?php endif; ?>
<?php if ( get_comments_number() || comments_open() ) : ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
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
	</item>
	<?php endwhile; ?>
</channel>
</rss>
