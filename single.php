<?php
/**
 * Single article.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$nn_url   = rawurlencode( get_permalink() );
	$nn_title = rawurlencode( get_the_title() );
	$nn_cat   = nn_primary_cat( get_the_ID() );
	?>
<div class="nn-wrap nn-layout">
	<article <?php post_class( 'nn-layout__main nn-article' ); ?>>
		<?php echo nn_breadcrumb(); // phpcs:ignore ?>
		<h1 class="nn-article__title"><?php the_title(); ?></h1>
		<p class="nn-article__meta">
			<?php
			/* translators: 1: author name, 2: date */
			printf( esc_html__( 'By %1$s, %2$s', 'nail-niche' ), esc_html( get_the_author() ), '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>' );
			if ( get_the_modified_date( 'Ymd' ) > get_the_date( 'Ymd' ) ) {
				/* translators: %s: date */
				echo ' ' . sprintf( esc_html__( '(updated %s)', 'nail-niche' ), '<time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>' ); // phpcs:ignore
			}
			?>
		</p>

		<?php
		// Skip the top photo when the same image is already in the article (no duplicate).
		$nn_thumb = (int) get_post_thumbnail_id();
		if ( $nn_thumb && ! str_contains( (string) get_post_field( 'post_content' ), 'wp-image-' . $nn_thumb . '"' ) ) :
			?>
		<figure class="nn-article__photo"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
		<?php endif; ?>

		<div class="nn-content" data-pin-url="<?php echo esc_url( get_permalink() ); ?>" data-pin-title="<?php echo esc_attr( get_the_title() ); ?>">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>

		<footer class="nn-article__foot">
			<?php if ( has_tag() ) : ?>
			<p><?php the_tags( esc_html__( 'Tagged: ', 'nail-niche' ), ', ', '' ); ?></p>
			<?php endif; ?>
			<p>
				<?php esc_html_e( 'Save or share:', 'nail-niche' ); ?>
				<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_attr( $nn_url ); ?>&description=<?php echo esc_attr( $nn_title ); ?>" target="_blank" rel="noopener nofollow">Pinterest</a>,
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $nn_url ); ?>" target="_blank" rel="noopener nofollow">Facebook</a>,
				<button type="button" data-copy="<?php echo esc_url( get_permalink() ); ?>" data-copied="<?php esc_attr_e( 'link copied', 'nail-niche' ); ?>"><?php esc_html_e( 'copy link', 'nail-niche' ); ?></button>
			</p>
		</footer>

		<?php
		echo nn_author_box(); // phpcs:ignore

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>

		<?php
		$nn_related = get_posts( array(
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'cat'            => $nn_cat ? $nn_cat->term_id : 0,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		) );
		if ( $nn_related ) :
			?>
		<section class="nn-block">
			<?php
			/* translators: %s: category name */
			echo nn_heading( $nn_cat ? sprintf( __( 'More %s', 'nail-niche' ), strtolower( $nn_cat->name ) ) : __( 'Keep reading', 'nail-niche' ) ); // phpcs:ignore
			?>
			<div class="nn-grid">
				<?php
				foreach ( $nn_related as $nn_id ) {
					echo nn_card( $nn_id ); // phpcs:ignore
				}
				?>
			</div>
		</section>
		<?php endif; ?>
	</article>
	<?php get_sidebar(); ?>
</div>
	<?php
endwhile;

get_footer();
