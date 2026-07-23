<?php
if (!defined('ABSPATH')) {
    exit;
}

// Adminbar-Bump (weißer Streifen oben) auf Coming-Soon-Seite verhindern.
show_admin_bar(false);

$assets     = get_template_directory_uri() . '/graphics/coming-soon';
$hero_image = $assets . '/hero.jpg';
$zip_teeth  = $assets . '/zipper.png';
$zip_head   = $assets . '/zipper-head.png';
$zip_end    = $assets . '/zipper-end.png';
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php bloginfo('name'); ?> – Coming Soon</title>
	<link href="<?php echo esc_url(get_template_directory_uri()); ?>/graphics/icons/favicon.ico" rel="shortcut icon">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
	<style>
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
			background: #fff;
			overflow: hidden;
		}

		body.coming-soon #cookie-banner {
			display: none !important;
		}

		/* Adminbar-Bump-Fallback */
		html:has(body.coming-soon) {
			margin-top: 0 !important;
		}

		body.coming-soon #wpadminbar {
			display: none !important;
		}

		.coming-soon {
			--cs-space: clamp(1rem, 2.5vw, 2.5rem);
			position: relative;
			display: grid;
			grid-template-columns: 1fr 1fr;
			width: 100vw;
			height: 100dvh;
			margin: 0;
			padding: 0;
		}

		/* Reißverschluss auf der Trennlinie */
		.coming-soon__zipper {
			/* --zip-w = Bildschirmbreite der Zähne (Original 74px); alles andere leitet sich davon ab */
			--zip-w: clamp(18px, 2vw, 26px);
			position: absolute;
			top: 0;
			bottom: 0;
			left: 50%;
			width: calc(var(--zip-w) * 117 / 74);
			transform: translateX(-50%);
			z-index: 3;
			pointer-events: none;
			background-image: url("<?php echo esc_url($zip_teeth); ?>");
			background-repeat: repeat-y;
			background-position: center top;
			background-size: var(--zip-w) auto;
		}

		.coming-soon__zipper img {
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			display: block;
			height: auto;
		}

		.coming-soon__zipper .coming-soon__zipper-head {
			top: 0;
			width: calc(var(--zip-w) * 117 / 74);
		}

		.coming-soon__zipper .coming-soon__zipper-end {
			bottom: 0;
			width: calc(var(--zip-w) * 71 / 74);
		}

		.coming-soon__left {
			display: grid;
			grid-template-rows: 1fr auto;
			gap: var(--cs-space);
			min-width: 0;
			min-height: 0;
			background: #fff;
			color: #a4a4a4;
			padding: var(--cs-space);
		}

		.coming-soon__headline-wrap {
			display: flex;
			align-items: center;
			min-height: 0;
			overflow: hidden;
		}

		.coming-soon__headline {
			font-family: "Poppins", sans-serif;
			font-size: 48px;
			font-weight: 800;
			line-height: 0.82;
			letter-spacing: -0.02em;
			text-transform: uppercase;
			color: #dfdfd7;
			margin: 0;
			padding: 0;
			hyphens: none;
			white-space: nowrap;
		}

		.coming-soon__headline span {
			display: block;
		}

		.coming-soon__contact {
			font-family: "Poppins", sans-serif;
			font-weight: 700;
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

			/* Zipper horizontal, mittig auf der Oberkante des Fotos (halb darüber) */
			.coming-soon__zipper {
				top: calc(50% + var(--cs-space));
				bottom: auto;
				left: 50%;
				height: 100vw;
				width: calc(var(--zip-w) * 117 / 74);
				transform: translate(-50%, -50%) rotate(90deg);
				transform-origin: center center;
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
					<span>Chapter</span>
					<span>Loading...</span>
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
		<div class="coming-soon__zipper" aria-hidden="true">
			<img class="coming-soon__zipper-end" src="<?php echo esc_url($zip_end); ?>" alt="" decoding="async">
			<img class="coming-soon__zipper-head" src="<?php echo esc_url($zip_head); ?>" alt="" decoding="async">
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
