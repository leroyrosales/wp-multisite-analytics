<?php
/*
Plugin Name:  WP Multisite Analytics
Description:  A plugin for having one GTM instance for all sites and subsites in a multisite environment.
Author:       Leroy Rosales
Author URI:  https://leroyrosales.com
Version:      1.0
Text Domain:  wp-multisite-analytics
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/


// disable direct file access
if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

// Add admin dashboard
function wpmultisite_multisite_analytics_admin_menu() {
    add_menu_page(
        'WP Multisite Analytics',
        'WP Multisite Analytics',
        'manage_network_options',
        'wp-multisite-analytics',
        'wpmultisite_multisite_analytics_admin_page',
        'dashicons-analytics',
        100
    );

    // Register the setting
    register_setting('wpmultisite_multisite_analytics', 'wpmultisite_multisite_analytics_gtm_code');
}
add_action( 'network_admin_menu', 'wpmultisite_multisite_analytics_admin_menu' );

// Admin page content
function wpmultisite_multisite_analytics_admin_page() {
    // Check if the form is submitted and the nonce is valid
    if (isset($_POST['wpmultisite_multisite_analytics_gtm_code']) && check_admin_referer('wpmultisite_multisite_analytics_update', 'wpmultisite_multisite_analytics_nonce')) {
        // Update the option
        update_network_option(null, 'wpmultisite_multisite_analytics_gtm_code', sanitize_text_field($_POST['wpmultisite_multisite_analytics_gtm_code']));
    }

    // Get the current value
    $gtm_code = get_network_option( null, 'wpmultisite_multisite_analytics_gtm_code', '' );

    ?>
    <div class="wrap">
        <h1>WP Multisite Analytics</h1>
        <form method="post">
            <label for="wpmultisite_multisite_analytics_gtm_code">GTM Code:</label>
            <input type="text" id="wpmultisite_multisite_analytics_gtm_code" name="wpmultisite_multisite_analytics_gtm_code" value="<?php echo esc_attr($gtm_code); ?>" />
            <?php wp_nonce_field( 'wpmultisite_multisite_analytics_update', 'wpmultisite_multisite_analytics_nonce' ); ?>
            <input type="submit" class="button button-primary" value="Save Changes" />
        </form>
    </div>
    <?php

    // Output the nonce field
    wp_nonce_field('utma_nonce_action', 'utma_nonce');
}

// Code that adds gtm code to header of wp site
function wpmultisite_multisite_analytics_header() {

    $gtm_code = get_network_option( null, 'wpmultisite_multisite_analytics_gtm_code' );

    // Only add the GTM scripts if a GTM code exists
    if ( ! empty( $gtm_code ) ) { ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo get_network_option( null,  'wpmultisite_multisite_analytics_gtm_code' ); ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    }
}
add_action( 'wp_head', 'wpmultisite_multisite_analytics_header', );

// code that echos gtm in body of wordpress site
function wpmultisite_multisite_analytics_noscript_tag() {

    $gtm_code = get_network_option( null, 'wpmultisite_multisite_analytics_gtm_code' );

    // Only add the GTM scripts if a GTM code exists
    if ( ! empty( $gtm_code ) ) {
        // Escape the URL for output
        $gtm_url = esc_url( 'https://www.googletagmanager.com/ns.html?id=' . $gtm_code );
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="<?php echo $gtm_url; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}
add_action( 'wp_body_open', 'wpmultisite_multisite_analytics_noscript_tag' ); // For non-Genesis themes
add_action( 'genesis_before', 'wpmultisite_multisite_analytics_noscript_tag' ); // For Genesis themes

