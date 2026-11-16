<?php
/**
 * Home design: Seasonal. A big banner for the season or holiday (picked by date,
 * or chosen in Customize → Home design), more ideas for it, then the latest, with the sidebar.
 */

list( $nn_cat, $nn_label ) = nn_season_cat();
$nn_season = $nn_cat
	? get_posts( array( 'posts_per_page' => 4, 'cat' => $nn_cat->term_id, 'fields' => 'ids', 'no_found_rows' => true ) )
	: nn_home_posts( 4 );
$nn_hero   = array_shift( $nn_season );
$nn_latest = array_values( array_diff( nn_home_posts( 9 ), array_filter( array_merge( array( $nn_hero ), $nn_season ) ) ) );
$nn_latest = array_slice( $nn_latest, 0, 6 );
$nn_link   = $nn_cat ? get_category_link( $nn_cat ) : nn_ideas_url();
?>

<?php if ( $nn_hero ) : ?>
<section class="nn-season">
	<div class="nn-wrap">
		<a class="nn-season__banner" href="<?php echo esc_url( get_permalink( $nn_hero ) ); ?>">
			<?php echo nn_thumb( $nn_hero, 'full', true ); // phpcs:ignore ?>
			<span class="nn-season__text">
				<span class="nn-season__label"><?php echo esc_html( $nn_label ); ?></span>
				<span class="nn-season__title"><?php echo esc_html( get_the_title( $nn_hero ) ); ?></span>
				<span class="nn-season__cta"><?php esc_html_e( 'See the ideas', 'nail-niche' ); ?> &rarr;</span>
			</span>
		</a>
	</div>
</section>
<?php endif; ?>

<div class="nn-wrap nn-layout">
	<div class="nn-layout__main">
		<?php if ( $nn_season ) : ?>
		<section class="nn-block nn-block--first">
			<?php /* translators: %s: season label, e.g. Christmas nails */ ?>
			<?php echo nn_heading( sprintf( __( 'More %s', 'nail-niche' ), strtolower( $nn_label ) ), $nn_link, __( 'See all', 'nail-niche' ) ); // phpcs:ignore ?>
			<div class="nn-grid">
				<?php
				foreach ( $nn_season as $nn_id ) {
					echo nn_card( $nn_id ); // phpcs:ignore
				}
				?>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $nn_latest ) : ?>
		<section class="nn-block<?php echo $nn_season ? '' : ' nn-block--first'; ?>">
			<?php echo nn_heading( __( 'Latest', 'nail-niche' ), nn_ideas_url(), __( 'All nail ideas', 'nail-niche' ) ); // phpcs:ignore ?>
			<div class="nn-grid">
				<?php
				foreach ( $nn_latest as $nn_id ) {
					echo nn_card( $nn_id ); // phpcs:ignore
				}
				?>
			</div>
		</section>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
