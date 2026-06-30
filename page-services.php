<!-- Index.-->

<?php get_header(); ?>
<main id="main-content" data-barba="container" data-barba-namespace="page"<?php echo uncut_page_inline_colors() ? ' style="' . esc_attr(uncut_page_inline_colors()) . '"' : ''; ?>>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="content">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>

				<div class="services-grid">
					<?php
					$services_query = new WP_Query(array(
						'post_type' => 'services',
						'posts_per_page' => -1,
						'orderby' => 'menu_order',
						'order' => 'ASC',
						'post_status' => 'publish',
					));

					if ($services_query->have_posts()) {
						while ($services_query->have_posts()) {
							$services_query->the_post();
							$service_bg = uncut_esc_color(get_field('service_bg_color'));
							$ui_color = uncut_esc_color(get_field('ui_color'));
							?>
							<div class="service-row">
								<div<?php echo $service_bg ? ' style="background:' . esc_attr($service_bg) . '"' : ''; ?>>
									<h2<?php echo $ui_color ? ' style="color:' . esc_attr($ui_color) . '"' : ''; ?>><?php the_title(); ?></h2>
								</div>
								<div><?php the_content(); ?></div>
							</div>
							<?php
						}
						wp_reset_postdata();
					}
					?>
				</div>
			</div>
	<?php endwhile;
	else : ?>
		<p><?php esc_html_e('Sorry, es konnte kein Betrag gefunden werden.'); ?></p>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
