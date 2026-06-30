<?php
/*
 *  Author: Bernhard Poppe
 *  URL: (?)
 *  Custom functions, support, custom post types and more.
 */
/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

if (!isset($content_width)) {
    $content_width = 900;
}

if (function_exists('add_theme_support')) {
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Gutenberg-Block-Styles global laden (wichtig für Barba-Transitions)
    add_theme_support('wp-block-styles');
}

function register_custom_menus()
{
    register_nav_menus(
        array(
            'main_menu' => __('Main Menu'),
            'footer_menu' => __('Footer Menu')
        )
    );
}
add_action('init', 'register_custom_menus');

/*------------------------------------*\
	Functions
\*------------------------------------*/


function manifest()
{
    static $manifest = null;

    if ($manifest !== null) {
        return $manifest;
    }

    $path = get_template_directory() . '/dist/parcel-manifest.json';
    if (!file_exists($path)) {
        $manifest = array();
        return $manifest;
    }

    $decoded = json_decode(file_get_contents($path), true);
    $manifest = is_array($decoded) ? $decoded : array();
    return $manifest;
}

function uncut_manifest_asset($key)
{
    $manifest = manifest();
    return isset($manifest[$key]) ? $manifest[$key] : null;
}

function uncut_asset_version($relative_path)
{
    $path = get_template_directory() . $relative_path;
    return file_exists($path) ? (string) filemtime($path) : false;
}

function uncut_get_page_id_by_slug($slug)
{
    $page = get_page_by_path($slug);
    return $page ? (int) $page->ID : 0;
}

function uncut_get_startseite_id()
{
    static $page_id = null;

    if ($page_id !== null) {
        return $page_id;
    }

    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-startseite.php',
        'number' => 1,
    ));

    if (!empty($pages)) {
        $page_id = (int) $pages[0]->ID;
        return $page_id;
    }

    $page_id = uncut_get_page_id_by_slug('startseite');
    return $page_id;
}

function uncut_get_footer_text()
{
    $page_id = uncut_get_startseite_id();
    if (!$page_id) {
        return '';
    }

    return get_field('footer_text', $page_id);
}

function uncut_esc_color($color)
{
    $color = is_string($color) ? trim($color) : '';

    if (preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6}|[a-fA-F0-9]{8})$/', $color)) {
        return $color;
    }

    if (preg_match('/^rgba?\([^)]+\)$/', $color)) {
        return $color;
    }

    return '';
}

function uncut_page_inline_colors()
{
    $styles = array();
    $background = uncut_esc_color(get_field('hintergrundfarbe'));
    $text = uncut_esc_color(get_field('textfarbe'));

    if ($background) {
        $styles[] = 'background:' . $background;
    }
    if ($text) {
        $styles[] = 'color:' . $text;
    }

    return $styles ? implode('; ', $styles) . ';' : '';
}

function uncut_subfield_inline_colors()
{
    $styles = array();
    $background = uncut_esc_color(get_sub_field('hintergrundfarbe'));
    $text = uncut_esc_color(get_sub_field('textfarbe'));

    if ($background) {
        $styles[] = 'background:' . $background;
    }
    if ($text) {
        $styles[] = 'color:' . $text;
    }

    return $styles ? implode('; ', $styles) . ';' : '';
}

function uncut_is_portrait_mode()
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_agents = array('Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi');

    foreach ($mobile_agents as $device) {
        if (strpos($user_agent, $device) !== false) {
            return true;
        }
    }

    return false;
}

function uncut_get_services_page_url()
{
    $page = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-services.php',
        'number' => 1,
    ));

    if (!empty($pages)) {
        return get_permalink($pages[0]->ID);
    }

    return home_url('/services/');
}

// Load scripts
function custom_header_scripts()
{
    if ($GLOBALS['pagenow'] === 'wp-login.php' || is_admin()) {
        return;
    }

    $script = uncut_manifest_asset('index.js');
    if (!$script) {
        return;
    }

    $script_uri = get_template_directory_uri() . '/dist' . $script;
    $version = uncut_asset_version('/dist' . $script);

    wp_register_script('custom_scripts', $script_uri, array(), $version, true);
    wp_enqueue_script('custom_scripts');
    wp_localize_script('custom_scripts', 'wp', array(
        'stylesheet_directory' => get_stylesheet_directory_uri(),
    ));
}

// Load styles
function custom_styles()
{
    $style = uncut_manifest_asset('theme.scss');
    if (!$style) {
        return;
    }

    $style_uri = get_template_directory_uri() . '/dist' . $style;
    $version = uncut_asset_version('/dist' . $style);

    wp_register_style('theme_css', $style_uri, array(), $version, 'all');
    wp_enqueue_style('theme_css');
}

function theme_editor_styles()
{
    $style = uncut_manifest_asset('editor.scss');
    if (!$style) {
        return;
    }

    wp_enqueue_style(
        'editor-css',
        get_template_directory_uri() . '/dist' . $style,
        array(),
        uncut_asset_version('/dist' . $style)
    );
}

add_action('enqueue_block_editor_assets', 'theme_editor_styles');


/**
 * Enqueue theme block editor scripts. // Custom Block-Styles
 */
function rich_block_editor_scripts()
{
    wp_enqueue_script('rich-editor', get_theme_file_uri('/blocks/block-styles.js'), array('wp-blocks', 'wp-dom'), wp_get_theme()->get('Version'), true);
}
add_action('enqueue_block_editor_assets', 'rich_block_editor_scripts');


// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('wp_enqueue_scripts', 'custom_header_scripts');
add_action('wp_enqueue_scripts', 'custom_styles');
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from 
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

/**
 * Disable the emoji's
 */
function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    // Remove from TinyMCE
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
}
add_action('init', 'disable_emojis');
/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

// Remove jQuery
add_filter('wp_enqueue_scripts', 'change_default_jquery', PHP_INT_MAX);
function change_default_jquery()
{
    wp_dequeue_script('jquery');
    wp_deregister_script('jquery');
}

//Import custom registered ACF-Blocks.
require('blocks/register_blocks.php');



add_action('admin_menu', 'hide_default_post_type');

function hide_default_post_type()
{
    remove_menu_page('edit.php');
    remove_submenu_page('edit.php', 'post-new.php');
}

function set_custom_post_types_order_in_admin($wp_query) {
    if (!is_admin() || !$wp_query->is_main_query()) {
        return;
    }

    $post_types = get_post_types(array('_builtin' => false), 'names');
    $post_types[] = 'post';

    if (isset($wp_query->query['post_type']) && in_array($wp_query->query['post_type'], $post_types, true)) {
        $wp_query->set('orderby', 'menu_order');
        $wp_query->set('order', 'ASC');
    }
}

add_action('pre_get_posts', 'set_custom_post_types_order_in_admin');


// Füge dies zu deiner functions.php Datei hinzu

// Kommentare-Menü im Admin-Bereich entfernen
function remove_comments_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'remove_comments_menu');

// Kommentare-Meta-Boxen auf den Beitrags- und Seitenbearbeitungsseiten entfernen
function remove_comments_meta_boxes() {
    remove_meta_box('commentsdiv', 'post', 'normal');
    remove_meta_box('commentsdiv', 'page', 'normal');
    remove_meta_box('commentstatusdiv', 'post', 'normal');
    remove_meta_box('commentstatusdiv', 'page', 'normal');
}
add_action('admin_menu', 'remove_comments_meta_boxes');

// Dashboard-Widget für Kommentare entfernen
function remove_dashboard_comments_widget() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'remove_dashboard_comments_widget');

// Kommentar-Benachrichtigungen deaktivieren
function disable_comments_notifications() {
    remove_action('admin_notices', 'comment_notification');
}
add_action('admin_init', 'disable_comments_notifications');

// Standardmäßig die Kommentarfunktion für neue Beiträge und Seiten deaktivieren
function disable_comments_default() {
    if (get_option('default_comment_status') !== 'closed') {
        update_option('default_comment_status', 'closed');
    }
    if (get_option('default_ping_status') !== 'closed') {
        update_option('default_ping_status', 'closed');
    }
}
add_action('after_setup_theme', 'disable_comments_default');








// Erstellen der Optionsseite
function my_custom_menu() {
    add_menu_page(
        'Theme Options',
        'Theme Options',
        'manage_options',
        'theme-options',
        'my_theme_options_page',
        null,
        99
    );
}
add_action('admin_menu', 'my_custom_menu');

// Anzeige der Optionsseite
function my_theme_options_page() {
    ?>
    <div class="wrap">
        <h1>Theme Options</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('theme_options_group');
                do_settings_sections('theme-options');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrierung der Einstellungen
function my_custom_settings() {
    register_setting('theme_options_group', 'coming_soon_mode', array(
        'type' => 'string',
        'sanitize_callback' => 'uncut_sanitize_coming_soon_mode',
        'default' => '0',
    ));
    add_settings_section('section', 'All Settings', null, 'theme-options');
    add_settings_field(
        'coming_soon_mode',
        'Enable Coming Soon Mode',
        'coming_soon_mode_callback',
        'theme-options',
        'section'
    );
}
add_action('admin_init', 'my_custom_settings');

function uncut_sanitize_coming_soon_mode($value) {
    return $value === '1' ? '1' : '0';
}

function uncut_is_coming_soon_enabled() {
    return get_option('coming_soon_mode') === '1';
}

function uncut_should_show_coming_soon() {
    if (!uncut_is_coming_soon_enabled()) {
        return false;
    }

    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return false;
    }

    // Eingeloggte Admins sehen die normale Seite (zum Bearbeiten/Testen)
    if (is_user_logged_in() && current_user_can('manage_options')) {
        return false;
    }

    return true;
}

function coming_soon_mode_callback() {
    $enabled = uncut_is_coming_soon_enabled();
    echo '<input type="hidden" name="coming_soon_mode" value="0" />';
    echo '<input type="checkbox" id="coming_soon_mode" name="coming_soon_mode" value="1" ' . checked($enabled, true, false) . ' />';
    echo '<p class="description">Gilt für alle Besucher. Eingeloggte Admins sehen weiterhin die normale Website. Zum Testen: Ausloggen oder Inkognito-Fenster nutzen.</p>';
}

function uncut_coming_soon_admin_notice() {
    if (!uncut_is_coming_soon_enabled()) {
        return;
    }

    echo '<div class="notice notice-warning"><p><strong>Coming Soon Mode ist aktiv.</strong> Besucher sehen die Coming-Soon-Seite. Du siehst sie nur im Inkognito-Fenster oder nach dem Ausloggen.</p></div>';
}
add_action('admin_notices', 'uncut_coming_soon_admin_notice');

function uncut_prepare_coming_soon() {
    if (!uncut_should_show_coming_soon()) {
        return;
    }

    if (!defined('UNCUT_RENDERING_COMING_SOON')) {
        define('UNCUT_RENDERING_COMING_SOON', true);
    }

    add_action('wp_enqueue_scripts', 'uncut_dequeue_scripts_for_coming_soon', 100);
}
add_action('wp', 'uncut_prepare_coming_soon', 0);

function uncut_dequeue_scripts_for_coming_soon() {
    wp_dequeue_script('custom_scripts');
    wp_dequeue_style('theme_css');

    $styles = wp_styles();
    if (!$styles instanceof WP_Styles) {
        return;
    }

    foreach ($styles->queue as $handle) {
        wp_dequeue_style($handle);
    }
}

function display_coming_soon_page() {
    if (!uncut_should_show_coming_soon()) {
        return;
    }

    status_header(200);
    nocache_headers();
    include(get_template_directory() . '/coming-soon.php');
    exit();
}
add_action('template_redirect', 'display_coming_soon_page', 0);
