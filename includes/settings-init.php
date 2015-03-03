<?PHP

global $sat_settings, $erapi, $sat_accountcachetime;

// Check if Wordpress is Loaded
if (!function_exists('add_action')) {
    exit('Sorry, you can not execute this file without wordpress.');
}

// Only load if the page is right  
if (!isset($_GET['page']) || strcasecmp($_GET['page'], 'sat_page_settings') !== 0) {
    return; //This will stop the execution is it is not the rigth page
}

// Read the post
if (isset($_POST) && isset($_POST['sat_settings']) && !empty($_POST['sat_settings']) && is_admin() && current_user_can('manage_options')) {
    $sat_settings = array();
    $sat_settings['apikey'] = isset($_POST['sat_settings']['apikey']) ? trim(strtolower($_POST['sat_settings']['apikey'])) : '';
    $sat_settings['email'] = isset($_POST['sat_settings']['email']) ? trim(strtolower($_POST['sat_settings']['email'])) : '';
    $sat_settings['apikey_invalid'] = isset($_POST['sat_settings']['apikey_invalid']) ? trim(strtolower($_POST['sat_settings']['apikey_invalid'])) : 1;
}

//Conect to the API and test the apikey
$erapi = new eRankerAPI($sat_settings['email'], $sat_settings['apikey']);
$accountinfo = $erapi->account();
if (empty($accountinfo) || isset($accountinfo->debug)) {
    $sat_settings['apikey_invalid'] = 1;
    set_transient('sat_account' . (md5($sat_settings['email'], $sat_settings['apikey'])), $accountinfo, $sat_accountcachetime);
} else {
    $sat_settings['apikey_invalid'] = 0;
}

//Save the new data
sat_savesettings($sat_settings, isset($_POST) && !empty($_POST));
