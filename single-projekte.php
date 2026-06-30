<!-- Index.-->

<?php get_header(); ?>
<main id="main-content" data-barba="container" data-barba-namespace="page">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="content">
				<h1><?php the_title(); ?></h1>
				<?php if (get_field('client')) : ?>
					<p>Client: <?php the_field('client'); ?></p><br>
				<?php endif; ?>
				<?php the_content(); ?>
				<?php
				$beteiligte_freelancer = get_field('beteiligte_freelancer');
				if ($beteiligte_freelancer) :
					$freelancer_names = array();
					foreach ($beteiligte_freelancer as $post) :
						setup_postdata($post);
						$freelancer_names[] = get_the_title();
					endforeach;
					wp_reset_postdata();
					echo '<br><p>Freelancer: ' . esc_html(implode(', ', $freelancer_names)) . '</p>';
				endif;
				?>

				<?php
				$beteiligte_services = get_field('beteiligte_services');
				if ($beteiligte_services) :
					$service_names = array();
					foreach ($beteiligte_services as $post) :
						setup_postdata($post);
						$service_names[] = get_the_title();
					endforeach;
					wp_reset_postdata();
					$service_label = count($service_names) > 1 ? 'Services' : 'Service';
					echo '<p>' . esc_html($service_label . ': ' . implode(', ', $service_names)) . '</p>';
				endif;
				?>
			</div>

		<?php endwhile;
	else : ?>
		<p><?php esc_html_e('Sorry, es konnte kein Betrag gefunden werden.'); ?></p>
	<?php endif; ?>

	<nav class="project-navigation" aria-label="Projekt-Navigation">
		<?php
		$current_post = get_the_ID();
		$posts = get_posts(array(
			'post_type' => 'projekte',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		));

		$next_post = null;
		$prev_post = null;

		if ($posts) {
			foreach ($posts as $index => $post) {
				if ((int) $post->ID === (int) $current_post) {
					if ($index > 0) {
						$prev_post = $posts[$index - 1];
					}
					if ($index < count($posts) - 1) {
						$next_post = $posts[$index + 1];
					}
					break;
				}
			}
		}

		if ($prev_post) :
			$prev_title = get_the_title($prev_post->ID);
			?>
			<a class="next-project" href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" aria-label="<?php echo esc_attr('Vorheriges Projekt: ' . $prev_title); ?>">
				<span class="screen-reader-text"><?php echo esc_html($prev_title); ?></span>
			</a>
		<?php endif; ?>

		<?php if ($next_post) :
			$next_title = get_the_title($next_post->ID);
			?>
			<a class="prev-project" href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" aria-label="<?php echo esc_attr('Nächstes Projekt: ' . $next_title); ?>">
				<span class="screen-reader-text"><?php echo esc_html($next_title); ?></span>
			</a>
		<?php endif; ?>
	</nav>

</main>

<?php get_footer(); ?>
