<?php
/**
 * Static page.
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
<div class="nn-wrap nn-page">
	<h1 class="nn-article__title"><?php the_title(); ?></h1>
	<div class="nn-content">
		<?php the_content(); ?>
	</div>
</div>
	<?php
endwhile;

get_footer();
