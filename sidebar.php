<?php
/**
 * Sidebar: About box, guides, categories, then the widget area for this page.
 */

$nn_guides = nn_guides_cat();
$nn_about  = get_page_by_path( 'about' );
$nn_photo  = nn_opt( 'about_image' );
?>
<aside class="nn-side" aria-label="<?php esc_attr_e( 'Sidebar', 'nail-niche' ); ?>">
	<?php if ( ! ( is_front_page() && 'personal' === nn_opt( 'home_design' ) ) ) : // The Personal home already opens with the welcome. ?>
	<section class="nn-about">
		<?php if ( $nn_photo ) : ?>
		<img class="nn-about__photo" src="<?php echo esc_url( $nn_photo ); ?>" alt="" width="72" height="72" loading="lazy">
		<?php else : ?>
		<span class="nn-about__photo nn-about__photo--blank" aria-hidden="true"><?php echo esc_html( mb_substr( wp_strip_all_tags( get_the_author_meta( 'display_name', 1 ) ), 0, 1 ) ); ?></span>
		<?php endif; ?>
		<h2><?php echo esc_html( nn_opt( 'about_title' ) ); ?></h2>
		<p><?php echo esc_html( nn_opt( 'about_text' ) ); ?></p>
		<?php if ( $nn_about ) : ?>
		<a href="<?php echo esc_url( get_permalink( $nn_about ) ); ?>"><?php esc_html_e( 'More about me', 'nail-niche' ); ?></a>
		<?php endif; ?>
	</section>
	<?php endif; ?>

	<?php
	if ( $nn_guides ) :
		$nn_guide_posts = get_posts( array( 'posts_per_page' => 5, 'cat' => $nn_guides->term_id, 'fields' => 'ids', 'no_found_rows' => true ) );
		if ( $nn_guide_posts ) :
			?>
	<section class="nn-side__block">
		<h2><?php echo esc_html( nn_opt( 'guides_title' ) ); ?></h2>
		<ul class="nn-side__list">
			<?php foreach ( $nn_guide_posts as $nn_id ) : ?>
			<li><a href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>"><?php echo esc_html( get_the_title( $nn_id ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<a class="nn-side__more" href="<?php echo esc_url( get_category_link( $nn_guides ) ); ?>"><?php esc_html_e( 'All guides', 'nail-niche' ); ?></a>
	</section>
			<?php
		endif;
	endif;
	?>

	<section class="nn-side__block">
		<h2><?php esc_html_e( 'Browse', 'nail-niche' ); ?></h2>
		<?php foreach ( nn_groups( 6 ) as $nn_group ) : ?>
		<p class="nn-side__group">
			<a href="<?php echo esc_url( get_category_link( $nn_group ) ); ?>"><?php echo esc_html( $nn_group->name ); ?></a>
			<?php
			$nn_links = array();
			foreach ( get_categories( array( 'parent' => $nn_group->term_id, 'orderby' => 'name' ) ) as $nn_child ) {
				$nn_links[] = '<a href="' . esc_url( get_category_link( $nn_child ) ) . '">' . esc_html( $nn_child->name ) . '</a>';
			}
			if ( $nn_links ) {
				echo '<span>' . implode( ', ', $nn_links ) . '</span>'; // phpcs:ignore
			}
			?>
		</p>
		<?php endforeach; ?>
	</section>

	<?php if ( is_active_sidebar( nn_widget_area() ) ) : ?>
		<?php dynamic_sidebar( nn_widget_area() ); ?>
	<?php endif; ?>
</aside>
