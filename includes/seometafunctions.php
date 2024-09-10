<?php

// Fungsi untuk menambahkan pengaturan global di Admin
function asmb_add_admin_menu() {
    add_menu_page(
        'Global SEO Settings',
        'SEO Settings',
        'manage_options',
        'asmb-seo-settings',
        'asmb_seo_settings_page',
        'dashicons-admin-generic'
    );
}
add_action('admin_menu', 'asmb_add_admin_menu');

// Fungsi halaman pengaturan SEO Global
function asmb_seo_settings_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'asmb_seo_settings';

    if (isset($_POST['submit'])) {
        $meta_title = sanitize_text_field($_POST['meta_title']);
        $meta_desc = sanitize_text_field($_POST['meta_desc']);
        $meta_keywords = sanitize_text_field($_POST['meta_keywords']);

        $wpdb->replace($table_name, array(
            'id' => 1,
            'meta_title' => $meta_title,
            'meta_desc' => $meta_desc,
            'meta_keywords' => $meta_keywords
        ));
    }

    $seo_settings = $wpdb->get_row("SELECT * FROM $table_name WHERE id = 1");

    ?>
    <div class="wrap">
        <h1>Global SEO Settings</h1>
        <form method="post" action="">
            <label for="meta_title">Meta Title</label>
            <input type="text" name="meta_title" value="<?php echo esc_attr($seo_settings->meta_title); ?>" />

            <label for="meta_desc">Meta Description</label>
            <textarea name="meta_desc"><?php echo esc_attr($seo_settings->meta_desc); ?></textarea>

            <label for="meta_keywords">Meta Keywords</label>
            <input type="text" name="meta_keywords" value="<?php echo esc_attr($seo_settings->meta_keywords); ?>" />

            <input type="submit" name="submit" value="Save Settings" class="button button-primary" />
        </form>
    </div>
    <?php
}
