<?php
/**
 * Home design: Color shop. A row of color swatches (the sub-categories of the Colors category chosen in Customize → Theme options), then four posts for each color. Full width, no sidebar.
 */

$nn_colors = nn_children( nn_opt( 'colors_slug' ) );
if ( ! $nn_colors ) {
	get_template_part( 'template-parts/home', 'classic' ); // No color categories yet.
	return;
}
$nn_parent = nn_cat( nn_opt( 'colors_slug' ) );
?>

<div class="nn-wrap nn-shop">
	<section class="nn-shop__intro">
		<h1 class="nn-shop__title"><?php esc_html_e( 'Find nails by color', 'nail-niche' ); ?></h1>
		<ul class="nn-swatches">
			<?php foreach ( $nn_colors as $nn_c ) : ?>
			<li>
				<a class="nn-swatch" href="<?php echo esc_url( get_category_link( $nn_c ) ); ?>">
					<span class="nn-swatch__dot" style="background:<?php echo esc_attr( nn_color_hex( $nn_c->slug ) ); ?>"></span>
					<span class="nn-swatch__name"><?php echo esc_html( $nn_c->name ); ?></span>
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<?php foreach ( $nn_colors as $nn_c ) : ?>
		<?php
		$nn_ids = get_posts( array( 'posts_per_page' => 4, 'cat' => $nn_c->term_id, 'fields' => 'ids', 'no_found_rows' => true ) );
		if ( ! $nn_ids ) {
			continue;
		}
		?>
	<section class="nn-block">
		<div class="nn-heading nn-heading--swatch">
			<h2><span class="nn-swatch__dot nn-swatch__dot--sm" style="background:<?php echo esc_attr( nn_color_hex( $nn_c->slug ) ); ?>"></span><?php echo esc_html( $nn_c->name ); ?></h2>
			<?php /* translators: %s: color */ ?>
			<a href="<?php echo esc_url( get_category_link( $nn_c ) ); ?>"><?php echo esc_html( sprintf( __( 'All %s nails', 'nail-niche' ), strtolower( $nn_c->name ) ) ); ?></a>
		</div>
		<div class="nn-grid nn-grid--4">
			<?php
			foreach ( $nn_ids as $nn_id ) {
				echo nn_card( $nn_id ); // phpcs:ignore
			}
			?>
		</div>
	</section>
	<?php endforeach; ?>

	<?php if ( $nn_parent ) : ?>
	<p class="nn-board__more"><a class="nn-button" href="<?php echo esc_url( get_category_link( $nn_parent ) ); ?>"><?php esc_html_e( 'All color ideas', 'nail-niche' ); ?></a></p>
	<?php endif; ?>
</div>
