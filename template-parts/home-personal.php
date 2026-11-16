<?php
/**
 * Home design: Personal blog. A welcome from the writer (About photo, heading, text),
 * then a simple list of posts, photo left and text right, with the sidebar.
 */

$nn_ids   = nn_home_posts( 10 );
$nn_photo = nn_opt( 'about_image' );
$nn_about = get_page_by_path( 'about' );
?>

<section class="nn-welcome">
	<div class="nn-wrap nn-welcome__inner">
		<?php if ( $nn_photo ) : ?>
		<img class="nn-welcome__photo" src="<?php echo esc_url( $nn_photo ); ?>" alt="" width="120" height="120">
		<?php endif; ?>
		<div>
			<h1 class="nn-welcome__title"><?php echo esc_html( nn_opt( 'home_welcome_title' ) ); ?></h1>
			<p><?php echo esc_html( nn_opt( 'home_welcome_text' ) ); ?></p>
			<?php if ( $nn_about ) : ?>
			<a class="nn-welcome__link" href="<?php echo esc_url( get_permalink( $nn_about ) ); ?>"><?php esc_html_e( 'More about me', 'nail-niche' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="nn-wrap nn-layout">
	<div class="nn-layout__main">
		<?php echo nn_heading( __( 'Latest posts', 'nail-niche' ), nn_ideas_url(), __( 'All nail ideas', 'nail-niche' ) ); // phpcs:ignore ?>
		<?php foreach ( $nn_ids as $nn_i => $nn_id ) : ?>
		<article class="nn-entry">
			<a class="nn-entry__img" href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo nn_thumb( $nn_id, 'nn-card', 0 === $nn_i ); // phpcs:ignore ?></a>
			<div class="nn-entry__body">
				<?php echo nn_cat_link( $nn_id ); // phpcs:ignore ?>
				<h2 class="nn-entry__title"><a href="<?php echo esc_url( get_permalink( $nn_id ) ); ?>"><?php echo esc_html( get_the_title( $nn_id ) ); ?></a></h2>
				<p><?php echo esc_html( get_the_excerpt( $nn_id ) ); ?></p>
				<time class="nn-date" datetime="<?php echo esc_attr( get_the_date( 'c', $nn_id ) ); ?>"><?php echo esc_html( get_the_date( '', $nn_id ) ); ?></time>
			</div>
		</article>
		<?php endforeach; ?>
		<?php if ( $nn_ids ) : ?>
		<p class="nn-board__more"><a class="nn-button" href="<?php echo esc_url( nn_ideas_url() ); ?>"><?php esc_html_e( 'Older posts', 'nail-niche' ); ?></a></p>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>
