<?php
/**
 * Plugin Name: WP Schedule Report
 * Description: Analisa artikel schedule per kategori dengan laporan di admin page.
 * Version: 1.0
 * Author: Fian
 */

if (!defined('ABSPATH')) exit;

class WPScheduleReport {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_admin_page() {
        add_menu_page(
            'Schedule Report',
            'Schedule Report',
            'manage_options',
            'wp-schedule-report',
            [$this, 'render_admin_page'],
            'dashicons-schedule',
            25
        );
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_wp-schedule-report') return;

        // jQuery sudah built-in WP
        wp_enqueue_script('jquery');

        // Bootstrap 5
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);

        // Custom style
        wp_enqueue_style('schedule-report-css', plugin_dir_url(__FILE__) . 'assets/style.css');
    }

    public function render_admin_page() {
        include __DIR__ . '/admin-page.php';
    }
}

new WPScheduleReport();
