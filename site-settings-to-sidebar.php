<?php
/**
 * Optionale Settings-Seite im Admin.
 * Nicht aktiv — bei Bedarf in functions.php per require einbinden.
 */

function uncut_get_settings_page_id()
{
    $page = get_page_by_path('website-einstellungen', OBJECT, 'page');
    return $page ? (int) $page->ID : 0;
}

function create_new_page()
{
    if (uncut_get_settings_page_id()) {
        return;
    }

    global $user_ID;
    $new_post = array(
        'import_id' => 3333,
        'post_title' => 'Website Einstellungen',
        'post_content' => ' ',
        'post_status' => 'publish',
        'post_date' => date('Y-m-d H:i:s'),
        'post_author' => $user_ID,
        'post_type' => 'page',
    );
    wp_insert_post($new_post);
}

function disable_gutenberg_on_settings_page($can, $post)
{
    if ($post && $post->post_name === 'website-einstellungen') {
        return false;
    }
    return $can;
}

function hide_settings_page($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    global $typenow;
    $settings_page_id = uncut_get_settings_page_id();

    if ($typenow === 'page' && $settings_page_id) {
        $query->set('post__not_in', array($settings_page_id));
    }
}

function add_site_settings_to_menu()
{
    $settings_page_id = uncut_get_settings_page_id();
    if (!$settings_page_id) {
        return;
    }

    add_menu_page(
        'Website Einstellungen',
        'Website Einstellungen',
        'manage_options',
        'post.php?post=' . $settings_page_id . '&action=edit',
        '',
        'dashicons-admin-tools',
        20
    );
}

function higlight_custom_settings_page($file)
{
    global $pagenow;
    $settings_page_id = uncut_get_settings_page_id();

    if (!$settings_page_id || $pagenow !== 'post.php') {
        return $file;
    }

    $post = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($post === $settings_page_id) {
        return 'post.php?post=' . $settings_page_id . '&action=edit';
    }

    return $file;
}

function edit_site_settings_title($title)
{
    global $post, $action, $current_screen;

    if (
        isset($current_screen->post_type) &&
        $current_screen->post_type === 'page' &&
        $action === 'edit' &&
        $post &&
        $post->post_name === 'website-einstellungen'
    ) {
        return $post->post_title . ' - ' . get_bloginfo('name');
    }

    return $title;
}
