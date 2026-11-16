<?php
/**
 * Home design: Pinterest board. Category buttons, then a wall of tall photos
 * at their own height (masonry with CSS columns). Full width, no sidebar.
 */

$nn_ids = nn_home_posts( 24 );
?>

<div class="nn-wrap nn-board">

	<nav class="nn-board__chips" aria-label="<?php esc_attr_e( 'Categories', 'nail-niche' ); ?>">
		<a class="nn-chip is-current" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-current="page"><?php esc_html_e( 'All', 'nail-niche' ); ?></a>
		<?php foreach ( nn_groups( 8 ) as $nn_group ) : ?>
			<a class="nn-chip" href="<?php echo esc_url( get_category_link( $nn_group ) ); ?>"><?php echo esc_html( $nn_group->name ); ?></a>
		<?php endforeach; ?>
		<?php $nn_guides = nn_guides_cat(); ?>
		<?php if ( $nn_guides ) : ?>
			<a class="nn-chip" href="<?php echo esc_url( get_category_link( $nn_guides ) ); ?>"><?php echo esc_html( $nn_guides->name ); ?></a>
		<?php endif; ?>
	</nav>

	<?php if ( $nn_ids ) : ?>
	<div class="nn-board__wall">
		<?php foreach ( $nn_ids as $nn_i => $nn_id ) : ?>
		<article class="nn-pin">
			<a class="nn-pin__img" href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $nn_id, 'medium_large', $nn_i < 4 ); // phpcs:ignore ?></a>
			<div class="nn-pin__body">
				<h2 class="nn-pin__title"><a href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>"><?php echo esc_html( get_the_title( $nn_id ) ); ?></a></h2>
				<?php echo nn_cat_link( $nn_id ); // phpcs:ignore ?>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
	<p class="nn-board__more"><a class="nn-button" href="<?php echo esc_url( nn_ideas_url() ); ?>"><?php esc_html_e( 'More nail ideas', 'nail-niche' ); ?></a></p>
	<?php endif; ?>

</div>
