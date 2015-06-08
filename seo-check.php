<?php

/*
  Plugin Name: SEO-Check
  Plugin URI: http://www.eranker.com/wordpress-plugin/
  Description: Provide eRanker SEO Check tools in your website. This plugin requires a valid FREE eRanker API Key.
  Version: 2.0.2
  Author: georanker
  Author URI: http://www.eranker.com/
  Network: false
  Licence: GNU General Public License v3

  This file is part of "seo-check" plugin for WordPress.

  SEO Check by eRanker Plugin is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**/////////////////////////////////////////////////////////////////////////////
// Plugin Contants definition
//////////////////////////////////////////////////////////////////////////////*/
define('SEOCHECK_VER', '2.0.2');
define('SEOCHECK_FOLDERNAME', 'seo-check');
define('SEOCHECK_PAGETITLE', 'seo-check'); //Do not change or the CSS will broke!
define('SEOCHECK_ACT_REPORT', 'report'); 

/**/////////////////////////////////////////////////////////////////////////////
// Check to make sure you meet the requirements
//////////////////////////////////////////////////////////////////////////////*/
global $wp_version;

if (version_compare($wp_version, "3.1", "<")) {
    exit('Sorry, but "eRanker-Plugin" no longer support pre-3.1 WordPress installs.');
}
if (!function_exists('curl_version')) {
    exit('Sorry, but "eRanker-Plugin" needs CURL PHP extension to work.');
}

/**/////////////////////////////////////////////////////////////////////////////
// Check if Wordpress is Loaded
//////////////////////////////////////////////////////////////////////////////*/
if (!function_exists('add_action')) {
    exit('Sorry, you can not execute this file without wordpress.');
}

/**/////////////////////////////////////////////////////////////////////////////
// Load Languages
//////////////////////////////////////////////////////////////////////////////*/
load_plugin_textdomain('er', false, basename(dirname(__FILE__)) . '/languages', 'languages');

/**/////////////////////////////////////////////////////////////////////////////
// Load Pages titles
//////////////////////////////////////////////////////////////////////////////*/
global $seocheck_titles;
$seocheck_titles = array();
$seocheck_titles[SEOCHECK_ACT_REPORT] = "SEO Report";

/**/////////////////////////////////////////////////////////////////////////////
// Cachetimes 
//////////////////////////////////////////////////////////////////////////////*/
global $seocheck_reportcachetime, $seocheck_factorscachetime, $seocheck_accountcachetime, $seocheck_nocache;
$seocheck_nocache = true;
$seocheck_reportcachetime = 3600 * 24 * 7;
$seocheck_accountcachetime = 180;
$seocheck_factorscachetime = 3600;


/**/////////////////////////////////////////////////////////////////////////////
// Includes
//////////////////////////////////////////////////////////////////////////////*/
require_once ('includes/eRankerAPI.class.php');
require_once ('includes/eRankerCommons.php');
eRankerCommons::$imgfolder = plugin_dir_url(__FILE__) . "images/";
eRankerCommons::$factorCreateImageFolder = plugin_dir_url(__FILE__) . "includes/";
require_once ('includes/actions.php');
require_once ('includes/widget-shortcodes_view_report.php');
require_once ('includes/widget-shortcodes_create_report.php');


/**/////////////////////////////////////////////////////////////////////////////
// Load settings from database
//////////////////////////////////////////////////////////////////////////////*/
global $seocheck_settings;
global $seocheck_pageid;
$seocheck_pageid = 0;

function seocheck_readsettings() {
    global $seocheck_settings, $seocheck_pageid;
    //Load the serialized array of settings for this plugin
    $seocheck_settings = get_option('seocheck_settings');
    $seocheck_pageid = get_option('seocheck_pageid');
    if (empty($seocheck_settings)) {
        $seocheck_settings = array('apikey' => '', 'email' => '', 'apikey_invalid' => 1);
    }
}

function seocheck_savesettings($settings, $log = true) {
    $out = update_option('seocheck_settings', $settings);

    if ($out && $log) {

        global $erapi, $seocheck_settings;

        if (!isset($erapi) || $erapi == NULL || empty($erapi)) {
            $erapi = new eRankerAPI("", "");
        }

        $erapi->pluginlog(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown', isset($settings['email']) && isset($settings['apikey']) && !empty($settings['email']) && !empty($settings['apikey']) ? 'LINK' : ' UNLINK', !empty($settings['email']) ? $settings['email'] : $erapi->email );
    }

    return $out;
}

seocheck_readsettings();

/**/////////////////////////////////////////////////////////////////////////////
// Add URLs on the plugin description
//////////////////////////////////////////////////////////////////////////////*/
add_filter('plugin_row_meta', 'seocheck_pluginpagelinks_content', 10, 2);
add_action('plugin_action_links_' . basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'seocheck_pluginpagelinks_left', 10, 4);

function seocheck_pluginpagelinks_content($links, $file) {
    if ($file == plugin_basename(basename(dirname(__FILE__)) . '/' . basename(__FILE__))) {
        $links[] = '<a href="http://www.eranker.com/register" target="_blank">' . __('Get an API Key', 'er') . '</a>';
        $links[] = '<a href="http://www.eranker.com/contactus" target="_blank">' . __('Contact Support', 'er') . '</a>';
    }
    return $links;
}

function seocheck_pluginpagelinks_left($links) {
    $settings_link = '<a href="admin.php?page=seocheck_page_settings">' . __('Settings', 'er') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

/**/////////////////////////////////////////////////////////////////////////////
// Add pages menu on admin side
//////////////////////////////////////////////////////////////////////////////*/

function seocheck_admin_add_page() {
    global $seocheck_settings;

    add_menu_page(__('Seo Check Plugin', 'er'), __('SEO Check Plugin', 'er'), 'manage_options', 'seocheck_page_settings', 'seocheck_page_settings', plugins_url(basename(dirname(__FILE__)) . '/images/eranker-plugin-icon-20x20.png'), 417);
    add_submenu_page('seocheck_page_settings', __('SEO Check Tool Settings', 'er'), __('SEO Check Settings', 'er'), 'manage_options', 'seocheck_page_settings', 'seocheck_page_settings');

    $riscado_begin = '';
    $riscado_end = '';
    if (!isset($_POST['seocheck_settings']) && !seocheck_is_apikeyvalid()) {
        $riscado_begin = '<span style="text-decoration: line-through;" title="' . __('Please, setup the plugin first', 'er') . '">';
        $riscado_end = '</span>';
    }
    add_submenu_page('seocheck_page_settings', 'wp-menu-separator', '', 'manage_options', 'seocheck_page_settings', 'seocheck_page_settings');
    add_submenu_page('seocheck_page_settings', __('New SEO Report', 'er'), $riscado_begin . __('New SEO Report', 'er') . $riscado_end, 'manage_options', 'seocheck_page_erankerreport', 'seocheck_page_erankerreport');
}

add_action('admin_menu', 'seocheck_admin_add_page');

/**/////////////////////////////////////////////////////////////////////////////
// Show an admin warning if the user does not setup the GeoRanker API Key
//////////////////////////////////////////////////////////////////////////////*/
if (is_admin() && !seocheck_is_apikeyvalid() && !isset($_POST['submit']) && !(isset($_GET['page']) && strcasecmp(trim($_GET['page']), 'seocheck_page_settings') == 0)) {

    function seocheck_warning_apikey() {
        echo " <div id='georanker-warning' class='updated fade'><p><strong>" . __('eRanker SEO Checker Plugin is almost ready to use.', 'er') . "</strong> " . sprintf(__('You must <a href="%1$s">enter your eRanker API key</a> for it to work.', 'er'), "admin.php?page=seocheck_page_settings") . "</p></div> ";
    }

    add_action('admin_notices', 'seocheck_warning_apikey');
}

/**/////////////////////////////////////////////////////////////////////////////
// Plugin activation hook
//////////////////////////////////////////////////////////////////////////////*/
register_activation_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'seocheck_activate');

function seocheck_activate() {

    global $seocheck_pageid, $wpdb, $seocheck_db_version;
    delete_option('seocheck_pageid');
    $the_page = get_page_by_title(SEOCHECK_PAGETITLE);
    if (!$the_page) {
        // Create post object
        $_p = array();
        $_p['post_title'] = SEOCHECK_PAGETITLE;
        $_p['post_content'] = "This text may be overridden by the plugin. You shouldn't edit it.";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['post_name'] = SEOCHECK_PAGETITLE;
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $seocheck_pageid = wp_insert_post($_p);
    } else {
        // the plugin may have been previously active and the page may just be trashed...
        $seocheck_pageid = $the_page->ID;
        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $seocheck_pageid = wp_update_post($the_page);
    }

    delete_option('seocheck_pageid');
    add_option('seocheck_pageid', $seocheck_pageid, '', 'yes');

//    $table_name = $wpdb->prefix . 'seocheck_sitereport';
//    /*
//     * We'll set the default character set and collation for this table.
//     * If we don't do this, some characters could end up being converted 
//     * to just ?'s when saved in our table.
//     */
//    $charset_collate = '';
//    if (!empty($wpdb->charset)) {
//        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
//    }
//    if (!empty($wpdb->collate)) {
//        $charset_collate .= " COLLATE {$wpdb->collate}";
//    }
//
//    $sql = "CREATE TABLE $table_name (
//		id int NOT NULL AUTO_INCREMENT,
//		request text NULL,
//		data longtext NULL
//	) $charset_collate;";
//
//    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//    dbDelta($sql);

    add_option('seocheck_db_version', $seocheck_db_version);

    global $erapi;
    if (!isset($erapi) || $erapi == NULL || empty($erapi)) {
        $erapi = new eRankerAPI("", "");
    }
    $erapi->pluginlog(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown', 'ACTIVATE');

//    // add a page for custom pending messages
//    $custom_message = wp_insert_post(array(
//        'post_name' => 'seo-agency-pending',
//        'post_title' => 'SEO AGENCY PENDING',
//        'post_content' => 'Your report is being generated.',
//        'post_status' => 'publish',
//        'post_type' => 'page',
//        'post_date' => date("Y-m-d H:i:s"))
//    );
}

/**/////////////////////////////////////////////////////////////////////////////
// Plugin deactivation/unistall hook
//////////////////////////////////////////////////////////////////////////////*/
register_deactivation_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'seocheck_uninstall');
register_uninstall_hook(basename(dirname(__FILE__)) . '/' . basename(__FILE__), 'seocheck_uninstall');

function seocheck_uninstall() {

    $id = get_option('seocheck_pageid');
    if ($id == true) {
        wp_delete_post($id, true);
    }
    delete_option('seocheck_pageid');

    global $erapi;
    if (!isset($erapi) || $erapi == NULL || empty($erapi)) {
        $erapi = new eRankerAPI("", "");
    }
    $erapi->pluginlog(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown', 'DEACTIVATE');
}

add_filter('parse_query', 'seocheck_query_parser');

function seocheck_query_parser($q) {
    global $seocheck_pageid;
    if (!empty($q->query_vars['page_id']) AND ( intval($q->query_vars['page_id']) == $seocheck_pageid )) {
        $q->set(SEOCHECK_PAGETITLE . '_page_is_called', true);
    } elseif (isset($q->query_vars['pagename']) AND ( ($q->query_vars['pagename'] == SEOCHECK_PAGETITLE) OR ( strpos($q->query_vars['pagename'], SEOCHECK_PAGETITLE . '/') === 0))) {
        $q->set(SEOCHECK_PAGETITLE . '_page_is_called', true);
    } else {
        $q->set(SEOCHECK_PAGETITLE . '_page_is_called', false);
    }
}

add_filter('the_posts', 'seocheck_page_filter');

function seocheck_page_filter($posts) {
    global $wp_query, $seocheck_titles, $seocheck_action, $seocheck_subaction;
    if ($wp_query->get(SEOCHECK_FOLDERNAME . '_page_is_called')) {

        $seocheck_action = (isset($_GET['action']) && !empty($_GET['action'])) ? trim(strtolower(strip_tags(addslashes($_GET['action'])))) : '';
        $seocheck_subaction = (isset($_GET['subaction'])) ? trim(strtolower($_GET['subaction'])) : null;

        //$posts[0]->post_title = htmlspecialchars(isset($seocheck_titles[$seocheck_action]) ? ucwords($seocheck_titles[$seocheck_action]) : ucwords($seocheck_action));
        $posts[0]->post_title = "SEO Report";

        ob_start();
        switch ($seocheck_action) {
            case SEOCHECK_ACT_REPORT:
                call_user_func("seocheck_act_report");
                break;
            default:
                call_user_func("seocheck_act_home");
                break;
        }
        $newcontent = ob_get_contents();
        ob_end_clean();

        $posts[0]->post_content = $newcontent;

        $wp_query->set(SEOCHECK_FOLDERNAME . '_page_is_called', false);
    }
    return $posts;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');


add_filter('wp_default_scripts', 'remove_jquery_migrate');

function remove_jquery_migrate(&$scripts) {
    if (!is_admin()) {
        $scripts->remove('jquery');
        $scripts->add('jquery', false, array('jquery-core'), '1.11.2');
    }
}

/**/////////////////////////////////////////////////////////////////////////////
// Call JS and CSS on all pages
//////////////////////////////////////////////////////////////////////////////*/

function seocheck_loadscripts() {
    //wp_enqueue_script('jquery');
    //wp_enqueue_script('jqueryuijs', '//code.jquery.com/ui/1.10.3/jquery-ui.js', array('jquery-core'), SEOCHECK_VER);
    //wp_enqueue_script('jquery-ui-core', '//code.jquery.com/jquery-2.1.3.min.js', array('jquery'));
    //wp_enqueue_script('jquery-ui-googleapis', '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', array('jquery'));
    //wp_enqueue_style('jqueryuicss', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', false, SEOCHECK_VER);

    wp_enqueue_style('er-css-resetwithgrid', plugins_url(basename(dirname(__FILE__)) . '/css/resetwithgrid.css'), array(), SEOCHECK_VER);
    //wp_enqueue_style('er-css-reset', plugins_url(basename(dirname(__FILE__)) . '/css/reset.css'), array(), SEOCHECK_VER);
    wp_enqueue_style('er-css-base', plugins_url(basename(dirname(__FILE__)) . '/css/base.css'), array(), SEOCHECK_VER);

    wp_enqueue_style('er-css-report', plugins_url(basename(dirname(__FILE__)) . '/css/report.css'), array('er-css-base'), SEOCHECK_VER);
    wp_enqueue_style('fontawsome', plugins_url(basename(dirname(__FILE__)) . '/css/vendor/font-awesome.min.css'));

    wp_enqueue_script('highstock', plugins_url(basename(dirname(__FILE__)) . '/js/highstock/js/highstock.js'), array('jquery'), SEOCHECK_VER);
    wp_enqueue_script('highstock-exporting', plugins_url(basename(dirname(__FILE__)) . '/js/highstock/js/modules/exporting.js'), array('highstock'), SEOCHECK_VER);

    wp_enqueue_script('circlesjs', plugins_url(basename(dirname(__FILE__)) . '/js/vendor/circles.min.js'), array(), SEOCHECK_VER);
    wp_enqueue_script('d3js', plugins_url(basename(dirname(__FILE__)) . '/js/vendor/d3.min.js'), array(), SEOCHECK_VER);

    wp_enqueue_script('gmaps', '//maps.google.com/maps/api/js?sensor=true"', array(), SEOCHECK_VER);

    wp_enqueue_script('er-js-report-maps-gmaps', plugins_url(basename(dirname(__FILE__)) . '/js/gmap/gmap.js'), array('er-js-report-maps'), SEOCHECK_VER);



    wp_enqueue_script('er-js-base', plugins_url(basename(dirname(__FILE__)) . '/js/base.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-tooltip', 'jquery-ui-dialog'), SEOCHECK_VER);
    wp_enqueue_script('er-js-report-printElement', plugins_url(basename(dirname(__FILE__)) . '/js/jquery-print/jQuery.print.js'), array(), SEOCHECK_VER);
    wp_enqueue_script('er-js-report', plugins_url(basename(dirname(__FILE__)) . '/js/report.js'), array('jquery', 'er-js-base'), SEOCHECK_VER);



    //wp_enqueue_script('satjs', plugins_url(basename(dirname(__FILE__)) . '/js/base.js'), array('jquery', 'jqueryuijs', 'googleplacesapi'), SEOCHECK_VER);
    //WE NEED TO CHECK THIS AND ENABLE ONLY THE MANDATORY THINGS
    //wp_enqueue_script('googleplacesapi', '//maps.googleapis.com/maps/api/js?libraries=places&amp;sensor=false', array('jquery'), SEOCHECK_VER);
//    wp_enqueue_script('googleplacesapi', '//maps.google.com/maps/api/js?sensor=true"', array('jquery'), SEOCHECK_VER);
    //wp_enqueue_script('gmapjs', plugins_url(basename(dirname(__FILE__)) . '/js/vendor/gmaps.js'), array(), SEOCHECK_VER);
    //wp_enqueue_script('readmorejs', plugins_url(basename(dirname(__FILE__)) . '/js/vendor/readmore.min.js'), array(), SEOCHECK_VER);
    //wp_enqueue_script('errpjs', plugins_url(basename(dirname(__FILE__)) . '/js/erreport.js'), array(), SEOCHECK_VER);
    //wp_enqueue_style('ercss', plugins_url(basename(dirname(__FILE__)) . '/css/eranker.css'), array('satcss'), SEOCHECK_VER);
    //wp_enqueue_style('satthemelitecss', plugins_url(basename(dirname(__FILE__)) . '/css/theme.lite.css'), array('satcss'), SEOCHECK_VER);
}

function seocheck_loadscripts_foradmin() {
    wp_enqueue_style('er-css-admin', plugins_url(basename(dirname(__FILE__)) . '/css/admin.css'), array('er-css-base'), SEOCHECK_VER);
}

add_action('wp_enqueue_scripts', 'seocheck_loadscripts');
add_action('admin_enqueue_scripts', 'seocheck_loadscripts');
add_action('admin_enqueue_scripts', 'seocheck_loadscripts_foradmin');

/**/////////////////////////////////////////////////////////////////////////////
// Define all functions to load the pages
//////////////////////////////////////////////////////////////////////////////*/

function seocheck_page_settings() {
    require 'includes/settings.php';
}

function seocheck_page_erankerreport() {
    require 'includes/newreporteranker.php';
}

/**/////////////////////////////////////////////////////////////////////////////
// Generic functions 
//////////////////////////////////////////////////////////////////////////////*/

function seocheck_redirectviewreportpage($url) {
    return '<script type="text/javascript"> window.location="' . $url . '"; </script>';
}

function seocheck_is_apikeyvalid($forcecheck = false) {
    global $seocheck_settings;
    if ($forcecheck) {
        //TODO: implement a way to force check
    } else {
        return !empty($seocheck_settings['email']) && !empty($seocheck_settings['apikey']) && !$seocheck_settings['apikey_invalid'];
    }
}

function set_seocheck_echoerror($errorcode = 503, $details = '') {
    switch ($errorcode) {
        case 404:
            header("HTTP/1.0 404 Not Found");
            echo "<h1>" . __('Error 404 - Not Found', 'er') . "</h1>";
            echo "<h4>" . __('The report you tried to load was not found.', 'er') . "</h4>";
            echo "<p>" . __('It seem the page you were looking for has moved or is no longer there. Or maybe you just mistyped something. It happens.', 'er') . "</p>";

            break;
        case 503:
        default:
            header("HTTP/1.0 503 Service Unavailable");
            echo "<h1>" . __('Error 503 - Service Unavailable', 'er') . "</h1>";
            echo "<h4>" . __('Unable to load the report.', 'er') . "</h4>";
            echo "<p>" . __('Unable to connect to the server API. Please try again in a few minutes. If the error persists contact the administrator.', 'er') . "</p>";
            break;
    }
    if (!empty($details)) {
        echo "<p>" . __('Details:', 'er') . " " . $details . "</p>";
    }
}

/**
 * Add an query string on the end of an url
 * @param String $url The original URL
 * @param String $query The query string to be added
 * @return String the final URL with the added query string
 */
function seocheck_addqueryonurl($url, $query) {
    $separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';
    return $url . $separator . $query;
}

/**
 * Get the plugin fruntend page URL. The the page does not exist, we use a default one.
 * @return String the URL for the plugin frontend page
 */
function seocheck_getfrontendurl() {
    $the_page = get_page_by_title(SEOCHECK_PAGETITLE);
    return !$the_page ? WP_HOME . '/' . SEOCHECK_ACT_REPORT . '/' : get_permalink($the_page->ID);
}

function seocheck_echo_redirect() {
    global $urlViewReporteRanker;
    $redirect = seocheck_redirectviewreportpage($urlViewReporteRanker);
    echo $redirect;
}
