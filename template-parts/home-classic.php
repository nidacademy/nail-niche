<?php
/**
 * Home design: Classic. Newest post first, then the latest, then one row per category, with the sidebar.
 */

$guides = nn_guides_cat();
$latest = get_posts( array(
	'posts_per_page'      => 7,
	'ignore_sticky_posts' => true,
	'fields'              => 'ids',
	'no_found_rows'       => true,
	'category__not_in'    => $guides ? array( $guides->term_id ) : array(),
) );
$lead   = array_shift( $latest );
?>

<div class="nn-wrap nn-layout">
	<div class="nn-layout__main">

		<?php if ( $lead ) : ?>
		<article class="nn-lead">
			<a class="nn-lead__img" href="<?php echo esc_url( get_permalink( $lead ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $lead, 'nn-card', true ); // phpcs:ignore ?></a>
			<div class="nn-lead__body">
				<?php echo nn_cat_link( $lead ); // phpcs:ignore ?>
				<h2 class="nn-lead__title"><a href="<?php echo esc_url( get_permalink( $lead ) ); ?>"><?php echo esc_html( get_the_title( $lead ) ); ?></a></h2>
				<p><?php echo esc_html( get_the_excerpt( $lead ) ); ?></p>
				<time class="nn-date" datetime="<?php echo esc_attr( get_the_date( 'c', $lead ) ); ?>"><?php echo esc_html( get_the_date( '', $lead ) ); ?></time>
			</div>
		</article>
		<?php endif; ?>

		<?php if ( $latest ) : ?>
		<section class="nn-block">
			<?php echo nn_heading( __( 'Latest', 'nail-niche' ), nn_ideas_url(), __( 'All nail ideas', 'nail-niche' ) ); // phpcs:ignore ?>
			<div class="nn-grid">
				<?php
				foreach ( $latest as $nn_id ) {
					echo nn_card( $nn_id ); // phpcs:ignore
				}
				?>
			</div>
		</section>
		<?php endif; ?>

		<?php
		foreach ( nn_groups( 4 ) as $group ) :
			$ids = get_posts( array( 'posts_per_page' => 3, 'cat' => $group->term_id, 'fields' => 'ids', 'no_found_rows' => true, 'post__not_in' => array_filter( array( $lead ) ) ) );
			if ( ! $ids ) {
				continue;
			}
			?>
		<section class="nn-block">
			<?php
			/* translators: %s: category name */
			echo nn_heading( $group->name, get_category_link( $group ), sprintf( __( 'All %s', 'nail-niche' ), strtolower( $group->name ) ) ); // phpcs:ignore
			?>
			<div class="nn-grid">
				<?php
				foreach ( $ids as $nn_id ) {
					echo nn_card( $nn_id ); // phpcs:ignore
				}
				?>
			</div>
		</section>
		<?php endforeach; ?>

	</div>
	<?php get_sidebar(); ?>
</div>

