<?php

/*
Plugin Name: Rampages Data Dashboard
Description: Provides a data dashboard to pull internal metrics from Rampages
Author: Jeff Everhart
Version: 1.0
*/


add_action('network_admin_menu', 'add_rampages_data_dashboard_menu');

function add_rampages_data_dashboard_menu() {
    add_menu_page(
        "Rampages Data",
        "Rampages Data",
        'manage_network',
        'rampages-data-dashboard',
        'rampages_data_dashboard_render_menu'
    );
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'rampages-data/v1', '/data', array(
      'methods' => 'GET',
      'callback' => 'collect_rampages_data',
    ) );
  } );

function collect_rampages_data(){
    $rampages_data = array(
        'user_registrations' => rewrite_data_properties(get_user_registrations_by_month()),
        'blog_registrations' => rewrite_data_properties(get_blog_registrations_by_month()),
        'blog_posts' => rewrite_data_properties(get_blog_posts_by_month()),
        'blog_comments' => rewrite_data_properties(get_blog_comments_by_month())
    );
    return $rampages_data;
}

function rampages_data_dashboard_render_menu(){
    require_once dirname(__FILE__) . '/rampages-data-charts-page.php';

}

function get_user_registrations_by_month(){
    global $wpdb;
   $results = $wpdb->get_results("SELECT COUNT(*) as total, YEAR(rampageswp_global.wp_users.user_registered) as Y, MONTH(rampageswp_global.wp_users.user_registered) as M FROM rampageswp_global.wp_users GROUP BY YEAR(rampageswp_global.wp_users.user_registered), MONTH(rampageswp_global.wp_users.user_registered)", OBJECT);
   return $results;
}

function get_blog_registrations_by_month(){
    global $wpdb;
   $results = $wpdb->get_results("SELECT COUNT(*) as total,
   year(rampageswp_global.wp_blogs.registered) as Y,
   month(rampageswp_global.wp_blogs.registered) as M
   FROM rampageswp_global.wp_blogs group by
   year(rampageswp_global.wp_blogs.registered),
   month(rampageswp_global.wp_blogs.registered)
   ", OBJECT);
   return $results;
}

function get_blog_posts_by_month(){
    global $wpdb;
   $results = $wpdb->get_results("SELECT COUNT(*) as total, Year(date_recorded) as Y, Month(date_recorded) as M
   FROM rampageswp_global.wp_bp_activity
   WHERE type = 'new_blog_post'
   GROUP BY Year(date_recorded), Month(date_recorded);
   ", OBJECT);
   return $results;
}

function get_blog_comments_by_month(){
    global $wpdb;
    $results = $wpdb->get_results("SELECT COUNT(*) as total, YEAR(date_recorded) as Y, Month(date_recorded) as M FROM rampageswp_global.wp_bp_activity
    WHERE type = 'activity_comment'
    GROUP BY YEAR(date_recorded), Month(date_recorded)
    ", OBJECT);
    return $results;
}


function rewrite_data_properties($array_to_process){
    foreach($array_to_process as &$item){
        $item->date = $item->M . '/' . $item->Y;
    }
    return $array_to_process;
}