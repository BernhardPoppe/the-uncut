<!-- Index.-->

<?php get_header(); ?>
<main id="main-content" data-barba="container" data-barba-namespace="page"<?php echo uncut_page_inline_colors() ? ' style="' . esc_attr(uncut_page_inline_colors()) . '"' : ''; ?>>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="content">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>

				<section id="freelancer-section">
					<h2>Freelancer</h2>
					<?php the_field('einleitungstext_freelancer_section'); ?>

					<div id="freelancer-grid">
						<?php
						$freelancer_query = new WP_Query(array(
							'post_type' => 'freelancer',
							'posts_per_page' => -1,
							'orderby' => 'menu_order',
							'order' => 'ASC',
							'post_status' => 'publish',
						));

						if ($freelancer_query->have_posts()) {
							while ($freelancer_query->have_posts()) {
								$freelancer_query->the_post();
								$ui_color = uncut_esc_color(get_field('ui_color'));
								?>
								<article>
									<?php the_post_thumbnail('full', array(
										'sizes' => '(max-width: 768px) 100vw, (min-width: 1024px) 25vw, 50vw',
									)); ?>
									<h3<?php echo $ui_color ? ' style="color:' . esc_attr($ui_color) . '"' : ''; ?>><?php the_title(); ?></h3>
									<?php the_content(); ?>
								</article>
								<?php
							}
							wp_reset_postdata();
						}
						?>
					</div>
				</section>
			</div>
	<?php endwhile;
	else : ?>
		<p><?php esc_html_e('Sorry, es konnte kein Betrag gefunden werden.'); ?></p>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
