<?php
/**
 * Not found.
 */

get_header();
?>
<div class="nn-wrap nn-page">
	<h1 class="nn-article__title"><?php esc_html_e( 'Page not found', 'nail-niche' ); ?></h1>
	<div class="nn-content">
		<p><?php esc_html_e( 'That link is broken or the page has moved. Try a search:', 'nail-niche' ); ?></p>
		<?php get_search_form(); ?>
		<p><a href="<?php echo esc_url( nn_ideas_url() ); ?>"><?php esc_html_e( 'Or see the newest nail ideas', 'nail-niche' ); ?></a></p>
	</div>
</div>
<?php
get_footer();
