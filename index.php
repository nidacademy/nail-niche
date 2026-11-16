<?php
/**
 * Blog, category, tag and search listings.
 */

get_header();

$nn_term     = is_category() ? get_queried_object() : null;
$nn_children = $nn_term ? get_categories( array( 'parent' => $nn_term->term_id, 'orderby' => 'name' ) ) : array();
?>

<div class="nn-wrap nn-layout">
	<div class="nn-layout__main">
		<header class="nn-pagehead">
			<?php echo nn_breadcrumb(); // phpcs:ignore ?>
			<h1><?php echo esc_html( nn_archive_title() ); ?></h1>
			<?php if ( ( is_category() || is_tag() ) && term_description() ) : ?>
			<div class="nn-pagehead__desc"><?php echo wp_kses_post( term_description() ); ?></div>
			<?php endif; ?>
			<?php if ( $nn_children ) : ?>
			<p class="nn-pagehead__links">
				<?php foreach ( $nn_children as $nn_child ) : ?>
				<a href="<?php echo esc_url( get_category_link( $nn_child ) ); ?>"><?php echo esc_html( $nn_child->name ); ?></a>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
		<div class="nn-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				echo nn_card( get_the_ID() ); // phpcs:ignore
			}
			?>
		</div>
			<?php
			the_posts_pagination( array(
				'mid_size'  => 1,
				'prev_text' => __( 'Newer', 'nail-niche' ),
				'next_text' => __( 'Older', 'nail-niche' ),
			) );
			?>
		<?php else : ?>
		<p><?php esc_html_e( 'Nothing matched. Try a different word, or pick a category from the menu.', 'nail-niche' ); ?></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php
get_footer();
