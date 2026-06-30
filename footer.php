	<footer>
		<div>
			<a href="<?php echo esc_url(get_home_url()); ?>">
				<div id="logo" class="white">the uncut</div>
			</a>
			<?php
			$footer_text = uncut_get_footer_text();
			if ($footer_text) {
				echo wp_kses_post($footer_text);
			}
			?>
		</div>
		<div>
			<nav aria-label="Footer-Navigation">
				<div id="smiley-footer" aria-hidden="true"></div>
				<?php wp_nav_menu(array('theme_location' => 'footer_menu')); ?>
			</nav>
			<br>Alle Rechte vorbehalten © Copyright the uncut, Wien <?php echo esc_html(date('Y')); ?>
			<button type="button" id="ga-optout">Google Analytics Opt-Out</button>
		</div>
	</footer>
	<?php wp_footer(); ?>
	</body>

	</html>
