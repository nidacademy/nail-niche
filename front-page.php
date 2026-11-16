<?php
/**
 * Homepage: the design picked in Customize → Home design.
 */

get_header();
get_template_part( 'template-parts/home', nn_sanitize_home_design( nn_opt( 'home_design' ) ) );
get_footer();
