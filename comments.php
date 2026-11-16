<?php
/**
 * Comments under a post: the list, then the form. Plain, like the rest of the theme.
 */

if ( post_password_required() ) {
	return;
}
// Only real reader comments: pingbacks (one of your posts linking to another) are left out.
$nn_count = (int) get_comments( array( 'post_id' => get_the_ID(), 'type' => 'comment', 'status' => 'approve', 'count' => true ) );
?>

<section id="comments" class="nn-comments">
	<?php if ( $nn_count ) : ?>
	<h2 class="nn-comments__title">
		<?php
		/* translators: %s: number of comments */
		echo esc_html( sprintf( _n( '%s comment', '%s comments', $nn_count, 'nail-niche' ), number_format_i18n( $nn_count ) ) );
		?>
	</h2>
	<ol class="nn-comments__list">
		<?php
		wp_list_comments( array(
			'style'       => 'ol',
			'type'        => 'comment',
			'avatar_size' => 40,
			'short_ping'  => true,
			'reply_text'  => __( 'Reply', 'nail-niche' ),
		) );
		?>
	</ol>
		<?php the_comments_navigation( array( 'prev_text' => __( 'Older comments', 'nail-niche' ), 'next_text' => __( 'Newer comments', 'nail-niche' ) ) ); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && $nn_count ) : ?>
	<p class="nn-comments__closed"><?php esc_html_e( 'Comments are closed.', 'nail-niche' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'          => $nn_count ? __( 'Leave a comment', 'nail-niche' ) : __( 'Questions about these nails? Ask me here', 'nail-niche' ),
		'title_reply_before'   => '<h2 id="reply-title" class="nn-comments__title">',
		'title_reply_after'    => '</h2>',
		'label_submit'         => __( 'Post comment', 'nail-niche' ),
		'comment_notes_before' => '<p class="nn-comments__note">' . esc_html__( 'Your email address will not be shown.', 'nail-niche' ) . '</p>',
		'class_submit'         => 'nn-button',
	) );
	?>
</section>
