<!-- Index.-->

<?php get_header(); ?>

<main id="main-content" data-barba="container" data-barba-namespace="home">

	<div id="fullpage">
		<div class="section fp-main-container" id="fp-videoheader">
			<div id="logo-header">
				<div id="logo">the uncut</div>
			</div>

			<?php
			$landscape_video = get_field('header_video_desktop');
			$portrait_video = get_field('header_video_mobile');

			if (!$portrait_video) {
				$portrait_video = $landscape_video;
			}

			$video_url = uncut_is_portrait_mode() ? $portrait_video : $landscape_video;
			?>

			<video id="headervideo" width="320" height="240" autoplay loop muted playsinline>
				<?php if ($video_url) : ?>
					<source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
				<?php endif; ?>
				Your browser does not support the video tag.
			</video>
		</div>

		<?php if (have_rows('section-hi')) : ?>
			<?php while (have_rows('section-hi')) : the_row(); ?>
				<div class="section fp-main-container" id="fp-hi"<?php echo uncut_subfield_inline_colors() ? ' style="' . esc_attr(uncut_subfield_inline_colors()) . '"' : ''; ?>>
					<h1><?php the_sub_field('uberschrift'); ?></h1>
					<?php the_sub_field('text'); ?>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>

		<?php if (have_rows('section-projekte')) : ?>
			<?php while (have_rows('section-projekte')) : the_row(); ?>
				<section class="section fp-main-container" id="fp-projects_gallery"<?php echo uncut_subfield_inline_colors() ? ' style="' . esc_attr(uncut_subfield_inline_colors()) . '"' : ''; ?>>
					<div>
						<h2><?php the_sub_field('uberschrift'); ?></h2>
						<?php the_sub_field('text'); ?>
					</div>
					<?php get_template_part('partials/_loop-projekteSlider'); ?>
				</section>
			<?php endwhile; ?>
		<?php endif; ?>

		<section class="section fp-main-container" id="fp-service_bubbles" style="background:black">
			<h2>Services</h2>
			<?php get_template_part('partials/_services-bubbles'); ?>
		</section>

		<?php if (have_rows('section-no-bullshit')) : ?>
			<?php while (have_rows('section-no-bullshit')) : the_row(); ?>
				<?php
				$marquee_bg = uncut_esc_color(get_sub_field('textfarbe'));
				$marquee_text = uncut_esc_color(get_sub_field('hintergrundfarbe'));
				$marquee_label = get_sub_field('marquee_text');
				?>
				<section class="section fp-main-container" id="fp-no-bullshit"<?php echo uncut_subfield_inline_colors() ? ' style="' . esc_attr(uncut_subfield_inline_colors()) . '"' : ''; ?>>
					<div>
						<h2><?php the_sub_field('uberschrift'); ?></h2>
						<?php the_sub_field('text'); ?>
					</div>
					<div class="marquee"<?php echo ($marquee_bg || $marquee_text) ? ' style="' . esc_attr(trim(($marquee_bg ? 'background:' . $marquee_bg . ';' : '') . ($marquee_text ? ' color:' . $marquee_text . ';' : ''))) . '"' : ''; ?>>
						<ul class="marquee__content" aria-hidden="true">
							<?php for ($i = 0; $i < 8; $i++) : ?>
								<li><?php echo esc_html($marquee_label); ?></li>
							<?php endfor; ?>
						</ul>
						<ul class="marquee__content" aria-hidden="true">
							<?php for ($i = 0; $i < 8; $i++) : ?>
								<li><?php echo esc_html($marquee_label); ?></li>
							<?php endfor; ?>
						</ul>
					</div>
				</section>
			<?php endwhile; ?>
		<?php endif; ?>

		<?php if (have_rows('section-kontakt')) : ?>
			<?php while (have_rows('section-kontakt')) : the_row(); ?>
				<?php
				$hintergrund_bild = get_sub_field('hintergrund-bild');
				$bg_image = $hintergrund_bild
					? wp_get_attachment_image_url($hintergrund_bild['ID'], 'full')
					: '';
				$liebesbriefe_style = sprintf(
					'background:%s; color:%s;',
					uncut_esc_color(get_sub_field('hintergrundfarbe')),
					uncut_esc_color(get_sub_field('textfarbe'))
				);
				if ($bg_image) {
					$liebesbriefe_style .= sprintf(
						" background-image:url('%s');",
						esc_url($bg_image)
					);
				}
				?>

				<section class="section fp-main-container" id="fp-liebesbriefe" style="<?php echo esc_attr($liebesbriefe_style); ?>">
					<h2><?php the_sub_field('uberschrift'); ?></h2>

					<?php $bild_mobile = get_sub_field('bild-mobile'); ?>
					<?php if ($bild_mobile) : ?>
						<img id="fp-liebesbriefe-bild_mobile" src="<?php echo esc_url(wp_get_attachment_image_url($bild_mobile['ID'], 'full')); ?>" alt="<?php echo esc_attr($bild_mobile['alt']); ?>" />
					<?php endif; ?>

					<?php the_sub_field('text'); ?>
					<a class="email_button" href="mailto:hello@the-uncut.com">Schieß los! 💌</a>
				</section>
			<?php endwhile; ?>
		<?php endif; ?>

		<section class="section fp-auto-height" id="footer-section">
			<div id="footer-wrapper"></div>
		</section>
	</div>

</main>

<?php get_footer(); ?>
