<?php
if (!defined('ABSPATH')) {
    exit;
}

$hero_image = get_template_directory_uri() . '/graphics/coming-soon/hero.jpg';
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php bloginfo('name'); ?> – Coming Soon</title>
	<link href="<?php echo esc_url(get_template_directory_uri()); ?>/graphics/icons/favicon.ico" rel="shortcut icon">
	<?php wp_head(); ?>
	<style>
		@font-face {
			font-family: "headingnow_extrabold";
			src: url("<?php echo esc_url(get_template_directory_uri()); ?>/graphics/fonts/headingnow-47extrabold-webfont.woff2") format("woff2"),
				url("<?php echo esc_url(get_template_directory_uri()); ?>/graphics/fonts/headingnow-47extrabold-webfont.woff") format("woff");
			font-weight: normal;
			font-style: normal;
		}

		*,
		*::before,
		*::after {
			box-sizing: border-box;
		}

		html,
		body.coming-soon {
			height: 100%;
			margin: 0;
		}

		body.coming-soon {
			background: #ff3e11;
			overflow: hidden;
		}

		body.coming-soon #cookie-banner {
			display: none !important;
		}

		.coming-soon {
			--cs-space: clamp(1rem, 2.5vw, 2.5rem);
			display: grid;
			grid-template-columns: 1fr 1fr;
			width: 100vw;
			height: 100dvh;
			margin: 0;
			padding: 0;
		}

		.coming-soon__left {
			display: grid;
			grid-template-rows: 1fr auto;
			gap: var(--cs-space);
			min-width: 0;
			min-height: 0;
			background: #ff3e11;
			color: #000;
			padding: var(--cs-space);
		}

		.coming-soon__headline-wrap {
			display: flex;
			align-items: flex-start;
			min-height: 0;
			overflow: hidden;
		}

		.coming-soon__headline {
			font-family: "headingnow_extrabold", sans-serif;
			font-size: 48px;
			font-weight: normal;
			line-height: 0.82;
			letter-spacing: -0.02em;
			text-transform: uppercase;
			margin: 0;
			padding: 0;
			hyphens: none;
			white-space: nowrap;
		}

		.coming-soon__headline span {
			display: block;
		}

		.coming-soon__contact {
			font-family: ui-monospace, "SFMono-Regular", "Roboto Mono", "Courier New", monospace;
			font-size: 0.7rem;
			line-height: 1.45;
			margin: 0;
		}

		.coming-soon__contact p {
			margin: 0;
		}

		.coming-soon__contact a {
			color: inherit;
			text-decoration: none;
		}

		.coming-soon__contact a:hover,
		.coming-soon__contact a:focus-visible {
			text-decoration: underline;
		}

		.coming-soon__right {
			min-width: 0;
			min-height: 0;
			overflow: hidden;
		}

		.coming-soon__right img {
			display: block;
			width: 100%;
			height: 100%;
			object-fit: cover;
			object-position: center top;
		}

		@media (max-width: 780px) {
			body.coming-soon {
				overflow: auto;
			}

			.coming-soon {
				grid-template-columns: 1fr;
				grid-template-rows: 1fr 1fr;
				gap: calc(var(--cs-space) * 2);
				height: 100dvh;
				min-height: 100dvh;
			}
		}
	</style>
</head>

<body <?php body_class('coming-soon'); ?>>
	<main class="coming-soon">
		<div class="coming-soon__left">
			<div class="coming-soon__headline-wrap">
				<h1 class="coming-soon__headline">
					<span>New</span>
					<span>Website</span>
					<span>Loading</span>
				</h1>
			</div>
			<footer class="coming-soon__contact">
				<p>contact: <a href="mailto:hello@the-uncut.com">hello@the-uncut.com</a></p>
				<p>instagram: <a href="https://www.instagram.com/theuncut.agency/" target="_blank" rel="noopener noreferrer">@theuncut.agency</a></p>
			</footer>
		</div>
		<div class="coming-soon__right">
			<img src="<?php echo esc_url($hero_image); ?>" alt="" width="512" height="576" decoding="async">
		</div>
	</main>
	<script>
		(function () {
			var wrap = document.querySelector('.coming-soon__headline-wrap');
			var headline = document.querySelector('.coming-soon__headline');
			if (!wrap || !headline) return;

			function fitHeadline() {
				var left = wrap.closest('.coming-soon__left');
				var gap = left ? parseFloat(getComputedStyle(left).gap) || 0 : 0;
				var maxHeight = wrap.clientHeight - gap;
				var maxWidth = wrap.clientWidth;
				var low = 8;
				var high = 800;
				var best = low;

				while (low <= high) {
					var mid = Math.floor((low + high) / 2);
					headline.style.fontSize = mid + 'px';

					var overflows =
						headline.scrollWidth > maxWidth ||
						headline.scrollHeight > maxHeight;

					if (!overflows) {
						best = mid;
						low = mid + 1;
					} else {
						high = mid - 1;
					}
				}

				headline.style.fontSize = best + 'px';
			}

			function init() {
				if (document.fonts && document.fonts.ready) {
					document.fonts.ready.then(fitHeadline);
				} else {
					fitHeadline();
				}
			}

			init();

			if ('ResizeObserver' in window) {
				new ResizeObserver(fitHeadline).observe(wrap);
			}

			window.addEventListener('resize', fitHeadline);
		})();
	</script>
	<?php wp_footer(); ?>
</body>

</html>
