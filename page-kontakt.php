<!-- Index.-->

<?php get_header(); ?>
<main id="main-content" data-barba="container" data-barba-namespace="page"<?php echo uncut_page_inline_colors() ? ' style="' . esc_attr(uncut_page_inline_colors()) . '"' : ''; ?>>

	<?php get_template_part('partials/_loop'); ?>

</main>

<?php get_footer(); ?>
