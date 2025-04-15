<?php
/*
Plugin Name: Auto Spam Comment Cleaner
Plugin URI: https://ratnsanghanifashion.com/
Description: Automatically deletes spam comments daily.
Version: 1.0
Author: Ratnesh Sharma
Author URI: https://www.linkedin.com/in/ratnesh-sharma-498a5b55/
License: GPL2
*/

// Schedule the event on plugin activation
register_activation_hook(__FILE__, 'ascc_schedule_daily_cleanup');
function ascc_schedule_daily_cleanup() {
    if (!wp_next_scheduled('ascc_daily_cleanup')) {
        wp_schedule_event(time(), 'daily', 'ascc_daily_cleanup');
    }
}

// Clear the scheduled event on plugin deactivation
register_deactivation_hook(__FILE__, 'ascc_unschedule_daily_cleanup');
function ascc_unschedule_daily_cleanup() {
    $timestamp = wp_next_scheduled('ascc_daily_cleanup');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'ascc_daily_cleanup');
    }
}

// Hook into the custom event
add_action('ascc_daily_cleanup', 'ascc_delete_spam_comments');

// Function to delete spam comments
function ascc_delete_spam_comments() {
    global $wpdb;
    $deleted = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
    
    if ($deleted !== false) {
        error_log("Auto Spam Comment Cleaner: Deleted $deleted spam comments.");
    } else {
        error_log("Auto Spam Comment Cleaner: Failed to delete spam comments.");
    }
}
