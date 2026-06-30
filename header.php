<!doctype html>
<html <?php language_attributes(); ?> class="no-js">

<head>

	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php
		if (is_404()) {
			echo esc_html('Seite nicht gefunden – ' . get_bloginfo('name'));
		} else {
			the_title();
			if (wp_title('', false)) {
				echo ' :';
			}
			echo ' ' . esc_html(get_bloginfo('name'));
		}
	?></title>

	<link href="<?php echo esc_url(get_template_directory_uri()); ?>/graphics/icons/favicon.ico" rel="shortcut icon">
	<link href="<?php echo esc_url(get_template_directory_uri()); ?>/graphics/icons/touch.png" rel="apple-touch-icon">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<meta name="theme-color" content="#fff" />

	<script>
		function loadGoogleAnalytics() {
			var script = document.createElement('script');
			script.async = true;
			script.src = 'https://www.googletagmanager.com/gtag/js?id=G-7ZP13JV1C8';
			document.head.appendChild(script);

			script.onload = function() {
				window.dataLayer = window.dataLayer || [];

				function gtag() {
					dataLayer.push(arguments);
				}
				gtag('js', new Date());
				gtag('config', 'G-7ZP13JV1C8');
			};
		}

		document.addEventListener('DOMContentLoaded', function() {
			var banner = document.getElementById('cookie-banner');
			if (!banner) return;

			if (!document.cookie.includes('cookies-accepted=true') && !document.cookie.includes('cookies-declined=true')) {
				banner.style.display = 'flex';
			}

			document.getElementById('accept-cookies').addEventListener('click', function() {
				document.cookie = 'cookies-accepted=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
				banner.style.display = 'none';
				loadGoogleAnalytics();
			});

			document.getElementById('decline-cookies').addEventListener('click', function() {
				document.cookie = 'cookies-declined=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
				banner.style.display = 'none';
			});
		});
	</script>

	<?php wp_head(); ?>

</head>



<body <?php body_class(); ?> data-barba="wrapper">
	<a id="skip-nav-link" href="#main-content">Zum Inhalt springen</a>

	<div id="cookie-banner" role="dialog" aria-label="Cookie-Einstellungen">
		<div>
			Wir verwenden Cookies. Klicken Sie <a href="<?php echo esc_url(home_url('/datenschutz')); ?>">hier</a>, um mehr zu erfahren.
		</div>
		<div>
			<button type="button" id="accept-cookies">Akzeptieren</button>
			<button type="button" id="decline-cookies">Ablehnen</button>
		</div>
	</div>

	<div class="transition-cover top"></div>
	<div class="transition-cover bottom"></div>

	<header class="header clear" role="banner">

		<a id="logo-mobile" href="<?php echo esc_url(get_home_url()); ?>">uncut</a>
		<button type="button" id="menu-button-mobile" aria-label="Menü öffnen" aria-expanded="false"></button>

		<nav id="main_nav" aria-label="Hauptnavigation">
			<?php wp_nav_menu(array('theme_location' => 'main_menu')); ?>
		</nav>

	</header>
