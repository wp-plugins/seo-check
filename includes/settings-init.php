<?PHP

global $seocheck_settings, $erapi, $seocheck_accountcachetime;

// Check if Wordpress is Loaded
if (!function_exists('add_action')) {
    exit('Sorry, you can not execute this file without wordpress.');
}

// Only load if the page is right  
if (!isset($_GET['page']) || strcasecmp($_GET['page'], 'seocheck_page_settings') !== 0) {
    return; //This will stop the execution is it is not the rigth page
}

// Read the post
if (isset($_POST) && isset($_POST['seocheck_settings']) && !empty($_POST['seocheck_settings']) && is_admin() && current_user_can('manage_options')) {
    $seocheck_settings = array();
    $seocheck_settings['apikey'] = isset($_POST['seocheck_settings']['apikey']) ? trim(strtolower($_POST['seocheck_settings']['apikey'])) : '';
    $seocheck_settings['email'] = isset($_POST['seocheck_settings']['email']) ? trim(strtolower($_POST['seocheck_settings']['email'])) : '';
    $seocheck_settings['apikey_invalid'] = isset($_POST['seocheck_settings']['apikey_invalid']) ? trim(strtolower($_POST['seocheck_settings']['apikey_invalid'])) : 1;
}

//Conect to the API and test the apikey
$erapi = new eRankerAPI($seocheck_settings['email'], $seocheck_settings['apikey']);
$accountinfo = $erapi->account();
if (empty($accountinfo) || isset($accountinfo->debug)) {
    $seocheck_settings['apikey_invalid'] = 1;
    set_transient('seocheck_account' . (md5($seocheck_settings['email'], $seocheck_settings['apikey'])), $accountinfo, $seocheck_accountcachetime);
} else {
    $seocheck_settings['apikey_invalid'] = 0;
}

//Save the new data
seocheck_savesettings($seocheck_settings, isset($_POST) && !empty($_POST));
