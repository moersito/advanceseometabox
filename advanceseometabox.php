<?php
/*
Plugin Name: Advanced SEO Meta Box
Plugin URI: https://grintbyte.com/advanceseometabox
Description: Plugin SEO dengan pengaturan untuk setiap halaman dan pengaturan global.
Version: 1.0
Author: Moersito
Author URI: https://moersito.github.io
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Includekan file fungsi tambahan
require_once plugin_dir_path(__FILE__) . 'includes/seometafunctions.php';

// Tambahkan Meta Box di halaman editor post
function asmb_add_meta_box() {
    add_meta_box(
        'asmb_meta_box', 
        'SEO Settings', 
        'asmb_meta_box_callback', 
        ['post', 'page'], // di post dan page
        'side', 
        'high'
    );
}
add_action('add_meta_boxes', 'asmb_add_meta_box');

// Callback untuk Meta Box
function asmb_meta_box_callback($post) {
    $meta_title = get_post_meta($post->ID, '_asmb_meta_title', true);
    $meta_desc = get_post_meta($post->ID, '_asmb_meta_desc', true);
    $meta_keywords = get_post_meta($post->ID, '_asmb_meta_keywords', true);

    echo '<label for="asmb_meta_title">Meta Title</label>';
    echo '<input type="text" id="asmb_meta_title" name="asmb_meta_title" value="' . esc_attr($meta_title) . '" maxlength="60" />';
    echo '<p class="description">Max 60 characters</p>';

    echo '<label for="asmb_meta_desc">Meta Description</label>';
    echo '<textarea id="asmb_meta_desc" name="asmb_meta_desc" rows="2" maxlength="160">' . esc_textarea($meta_desc) . '</textarea>';
    echo '<p class="description">Max 160 characters</p>';

    echo '<label for="asmb_meta_keywords">Meta Keywords (Comma separated)</label>';
    echo '<input type="text" id="asmb_meta_keywords" name="asmb_meta_keywords" value="' . esc_attr($meta_keywords) . '" />';
}

// Simpan data SEO
function asmb_save_meta_data($post_id) {
    if (array_key_exists('asmb_meta_title', $_POST)) {
        update_post_meta($post_id, '_asmb_meta_title', sanitize_text_field($_POST['asmb_meta_title']));
    }
    if (array_key_exists('asmb_meta_desc', $_POST)) {
        update_post_meta($post_id, '_asmb_meta_desc', sanitize_text_field($_POST['asmb_meta_desc']));
    }
    if (array_key_exists('asmb_meta_keywords', $_POST)) {
        update_post_meta($post_id, '_asmb_meta_keywords', sanitize_text_field($_POST['asmb_meta_keywords']));
    }
}
add_action('save_post', 'asmb_save_meta_data');

// Fungsi untuk menambahkan Meta Tags di Header
function asmb_add_meta_tags() {
    if (is_single() || is_page()) {
        global $post;
        $meta_title = get_post_meta($post->ID, '_asmb_meta_title', true);
        $meta_desc = get_post_meta($post->ID, '_asmb_meta_desc', true);
        $meta_keywords = get_post_meta($post->ID, '_asmb_meta_keywords', true);

        if ($meta_title) {
            echo '<meta name="title" content="' . esc_attr($meta_title) . '">';
        }
        if ($meta_desc) {
            echo '<meta name="description" content="' . esc_attr($meta_desc) . '">';
        }
        if ($meta_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">';
        }
    }
}
add_action('wp_head', 'asmb_add_meta_tags');

// Fungsi untuk pengaturan global
function asmb_create_global_seo_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'asmb_seo_settings';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        meta_title varchar(255) DEFAULT '' NOT NULL,
        meta_desc varchar(255) DEFAULT '' NOT NULL,
        meta_keywords text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'asmb_create_global_seo_table');

// Fungsi untuk menghapus tabel saat plugin dihapus
function asmb_delete_global_seo_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'asmb_seo_settings';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_uninstall_hook(__FILE__, 'asmb_delete_global_seo_table');
