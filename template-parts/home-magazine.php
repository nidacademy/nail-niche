<?php
/**
 * Home design: Magazine. One big post and two smaller ones, then the latest in four columns,
 * then a row per category. Full width, no sidebar.
 */

$nn_ids  = nn_home_posts( 11 );
$nn_big  = array_shift( $nn_ids );
$nn_side = array_splice( $nn_ids, 0, 2 );
$nn_seen = array_filter( array_merge( array( $nn_big ), $nn_side, $nn_ids ) );
?>

<div class="nn-wrap nn-mag">

	<?php if ( $nn_big ) : ?>
	<section class="nn-mag__hero" aria-label="<?php esc_attr_e( 'Featured', 'nail-niche' ); ?>">
		<article class="nn-mag__big">
			<a class="nn-mag__img" href="<?php echo esc_url( get_permalink( $nn_big ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $nn_big, 'full', true ); // phpcs:ignore ?></a>
			<div class="nn-mag__body">
				<?php echo nn_cat_link( $nn_big ); // phpcs:ignore ?>
				<h2 class="nn-mag__title"><a href="<?php echo esc_url( get_permalink( $nn_big ) ); ?>"><?php echo esc_html( get_the_title( $nn_big ) ); ?></a></h2>
				<p><?php echo esc_html( get_the_excerpt( $nn_big ) ); ?></p>
				<time class="nn-date" datetime="<?php echo esc_attr( get_the_date( 'c', $nn_big ) ); ?>"><?php echo esc_html( get_the_date( '', $nn_big ) ); ?></time>
			</div>
		</article>
		<?php if ( $nn_side ) : ?>
		<div class="nn-mag__side">
			<?php foreach ( $nn_side as $nn_id ) : ?>
			<article class="nn-mag__small">
				<a class="nn-mag__img" href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $nn_id, 'nn-card' ); // phpcs:ignore ?></a>
				<div>
					<?php echo nn_cat_link( $nn_id ); // phpcs:ignore ?>
					<h3 class="nn-mag__subtitle"><a href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>"><?php echo esc_html( get_the_title( $nn_id ) ); ?></a></h3>
					<time class="nn-date" datetime="<?php echo esc_attr( get_the_date( 'c', $nn_id ) ); ?>"><?php echo esc_html( get_the_date( '', $nn_id ) ); ?></time>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</section>
	<?php endif; ?>

	<?php if ( $nn_ids ) : ?>
	<section class="nn-block">
		<?php echo nn_heading( __( 'Latest', 'nail-niche' ), nn_ideas_url(), __( 'All nail ideas', 'nail-niche' ) ); // phpcs:ignore ?>
		<div class="nn-grid nn-grid--4">
			<?php
			foreach ( $nn_ids as $nn_id ) {
				echo nn_card( $nn_id ); // phpcs:ignore
			}
			?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	foreach ( nn_groups( 4 ) as $nn_group ) :
		$nn_row = get_posts( array( 'posts_per_page' => 4, 'cat' => $nn_group->term_id, 'fields' => 'ids', 'no_found_rows' => true, 'post__not_in' => $nn_seen ) );
		if ( ! $nn_row ) {
			continue;
		}
		?>
	<section class="nn-block">
		<?php
		/* translators: %s: category name */
		echo nn_heading( $nn_group->name, get_category_link( $nn_group ), sprintf( __( 'All %s', 'nail-niche' ), strtolower( $nn_group->name ) ) ); // phpcs:ignore
		?>
		<div class="nn-grid nn-grid--4">
			<?php
			foreach ( $nn_row as $nn_id ) {
				echo nn_card( $nn_id ); // phpcs:ignore
			}
			?>
		</div>
	</section>
	<?php endforeach; ?>

</div>
