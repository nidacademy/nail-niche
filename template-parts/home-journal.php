<?php
/**
 * Home design: Minimal list. A short intro, then post titles grouped by month,
 * each with a small square photo. One narrow column, no sidebar.
 */

$nn_ids    = nn_home_posts( 30 );
$nn_months = array();
foreach ( $nn_ids as $nn_id ) {
	$nn_months[ get_the_date( 'Y-m', $nn_id ) ][] = $nn_id;
}
?>

<div class="nn-journal">
	<header class="nn-journal__intro">
		<h1 class="nn-journal__title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
		<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : nn_opt( 'about_text' ) ); ?></p>
	</header>

	<?php foreach ( $nn_months as $nn_month => $nn_list ) : ?>
	<section class="nn-journal__month">
		<h2><?php echo esc_html( wp_date( 'F Y', (int) strtotime( $nn_month . '-15' ) ) ); ?></h2>
		<ul>
			<?php foreach ( $nn_list as $nn_id ) : ?>
			<li class="nn-journal__item">
				<a class="nn-journal__img" href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $nn_id, 'nn-thumb' ); // phpcs:ignore ?></a>
				<div>
					<a class="nn-journal__link" href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>"><?php echo esc_html( get_the_title( $nn_id ) ); ?></a>
					<span class="nn-journal__meta"><?php echo esc_html( get_the_date( 'j M', $nn_id ) ); ?><?php $nn_c = nn_primary_cat( $nn_id ); echo $nn_c ? ' · ' . esc_html( $nn_c->name ) : ''; ?></span>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endforeach; ?>

	<?php if ( $nn_ids ) : ?>
	<p class="nn-board__more"><a class="nn-button" href="<?php echo esc_url( nn_ideas_url() ); ?>"><?php esc_html_e( 'Older posts', 'nail-niche' ); ?></a></p>
	<?php endif; ?>
</div>
