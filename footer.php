</main>

<footer class="nn-footer">
	<div class="nn-wrap nn-footer__in">
		<p class="nn-footer__copy">
			<?php
			/* translators: 1: year, 2: site name */
			printf( esc_html__( '&copy; %1$s %2$s', 'nail-niche' ), esc_html( gmdate( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
			?>
		</p>
		<?php echo nn_menu( 'footer', 'nn-footer__menu' ); // phpcs:ignore ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
