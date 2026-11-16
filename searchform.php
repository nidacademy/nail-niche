<form role="search" method="get" class="nn-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search, e.g. short almond', 'nail-niche' ); ?>" aria-label="<?php esc_attr_e( 'Search nail ideas', 'nail-niche' ); ?>">
	<button type="submit"><?php esc_html_e( 'Search', 'nail-niche' ); ?></button>
</form>
