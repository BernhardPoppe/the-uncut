<?php get_header(); ?>

<main id="main-content" data-barba="container" data-barba-namespace="page" class="error-404-main">
	<div class="content error-404-content">
		<div id="logo" aria-hidden="true"></div>
		<h1>Seite nicht gefunden</h1>
		<p>
			Die Seite, die du suchst, existiert leider nicht (mehr).
			<br>
			Vielleicht hast du dich vertippt — oder wir haben sie umgezogen.
		</p>
		<a class="email_button" href="<?php echo esc_url(home_url('/')); ?>">Zurück zur Startseite</a>
	</div>
</main>

<?php get_footer(); ?>
