<?PHP

global $seocheck_settings;
global $seocheck_countrylist;
global $seocheck_error;
global $seocheck_error_msg;
global $seocheck_accountinfo;
global $seocheck_action;
global $seocheck_subaction;
global $seocheck_newreporteranker_url;
global $urlViewReporteRanker;
global $seocheck_reportobj;
global $allfactor_list;
global $seocheck_accountcachetime;
global $seocheck_factorscachetime;
global $seocheck_nocache;
global $show_lead_generator_form;

$show_lead_generator_form =  ($show_lead_generator_form == 1)? TRUE : FALSE ;

// Check if Wordpress is Loaded
if (!function_exists('add_action')) {
    exit('Sorry, you can not execute this file without wordpress.');
}

$seocheck_error = FALSE;
$seocheck_error_msg = '';
$urlViewReporteRanker = '';
$language = 'en';

//Conect to the API and test the apikey

$erapi = new eRankerAPI($seocheck_settings['email'], $seocheck_settings['apikey']);

$seocheck_accountinfo = get_transient('seocheck_account' . (md5($seocheck_settings['email'], $seocheck_settings['apikey'])));
if (empty($seocheck_accountinfo) || isset($seocheck_accountinfo->debug) || $seocheck_nocache) {
    $seocheck_accountinfo = $erapi->account();
    if (!empty($seocheck_accountinfo) && !isset($seocheck_accountinfo->debug)) {
        set_transient('seocheck_account' . (md5($seocheck_settings['email'], $seocheck_settings['apikey'])), $seocheck_accountinfo, $seocheck_accountcachetime);
    }
}

$allfactor_list = get_transient('seocheck_factors_' . $language);
if (empty($allfactor_list) || isset($allfactor_list->debug) || $seocheck_nocache) {
    $allfactor_list = $erapi->factors($language);
    if (!empty($allfactor_list) && !isset($allfactor_list->debug)) {
        set_transient('seocheck_factors_' . $language, $allfactor_list, $seocheck_factorscachetime);
    }
}

if (isset($_REQUEST['sc_url'])) {

    $seocheck_newreporteranker_url = isset($_REQUEST['sc_url']) ? $_REQUEST['sc_url'] : "";
    $checkbox_factors = isset($_REQUEST['factorsGroup']) && !empty($_REQUEST['factorsGroup']) ? $_REQUEST['factorsGroup'] : array();
    if(empty($checkbox_factors)){
        $checkbox_factors = $seocheck_accountinfo->plan->default_factors;
    }

    //Check URL
    if (isset($seocheck_newreporteranker_url)) {
        if (strlen($seocheck_newreporteranker_url) < 5 || strpos($seocheck_newreporteranker_url, ".") === FALSE) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = __("Is not a valid url. Must possess the url http:// or https://", 'seocheck');
            $seocheck_newreporteranker_url = null;
        } else {
            if (strpos($seocheck_newreporteranker_url, "http://") === FALSE || strpos($seocheck_newreporteranker_url, "https://") === FALSE) {
                $seocheck_newreporteranker_url = 'http://' . $seocheck_newreporteranker_url;
            }
        }
    } else {
        $seocheck_newreporteranker_url = null;
    }
    if (empty($seocheck_newreporteranker_url)) {
        $seocheck_error = TRUE;
        $seocheck_error_msg = __("You must specify a valid URL or domain. <br/>A URL must have at least 5 caracters and a dot (.).", 'seocheck');
    }   
    
    //Check Factors
    if (empty($checkbox_factors)) {
        $seocheck_error = TRUE;
        $seocheck_error_msg = __("You need select at least one factor from the list.", 'seocheck');
    }
    
     if ($seocheck_error === FALSE && $show_lead_generator_form === TRUE) {

        if (isset($_REQUEST['sc_companyname']) && empty($_REQUEST['sc_companyname'])) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = __("You must enter your company name", 'seocheck');
        }

        if (isset($_REQUEST['sc_phone']) && empty($_REQUEST['sc_phone'])) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = __("You must enter your phone number", 'seocheck');
        }

        if ((isset($_REQUEST['sc_email']) && empty($_REQUEST['sc_email']) && strlen($_REQUEST['sc_email']) <= 5 ) || isset($_REQUEST['sc_email']) && empty($_REQUEST['sc_email']) && strstr($_REQUEST['sc_email'], '@') == FALSE) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = __("You must enter your email address or the email address you entered is invalid.", 'seocheck');
        }

        $sc_companyName = trim(strip_tags(html_entity_decode($_REQUEST['sc_companyname'])));
        $sc_phone = trim(strip_tags(html_entity_decode($_REQUEST['sc_phone'])));
        $sc_email = trim(strip_tags(html_entity_decode($_REQUEST['sc_email'])));
        $sc_subject = 'Lead Generator info';
        $sc_message = "Hi,<br/><br/>You contacted our support about lead info generator :<br>";
        $sc_message .="<div style='background-color:#f3f3f3;padding:20px;border-radius:5px;'>";
        $sc_message .="<b>Company Name</b>: $sc_companyName<br>";
        $sc_message .="<b>Phone</b>: $sc_phone<br>";
        $sc_message .="<b>Email</b>: $sc_email<br>";
        $sc_message .="</div>";
        $admin_email = get_option('admin_email');   
//        $admin_email = 'email@jonasmarinho.com';
        if (!empty($_REQUEST['sc_companyname']) && !empty($_REQUEST['sc_phone']) && !empty($_REQUEST['sc_email'])) {
            if (!wp_mail($admin_email, $sc_subject, $sc_message)) {
                $seocheck_error = TRUE;
                $seocheck_error_msg = __("An internal error occurred while sending the email.", 'seocheck');
            }
        }
    }    
    
    if ($seocheck_error === FALSE) {
        $seocheck_reportobj = $erapi->reportnew($seocheck_newreporteranker_url, $checkbox_factors);
        if (empty($seocheck_reportobj)) {
            $seocheck_error = TRUE;
            $seocheck_error_msg = __('Could not create a report.<br/>An unknown error occurred', 'seocheck');
        } else {
            if (isset($seocheck_reportobj->msg)) {
                $seocheck_error = TRUE;
                $seocheck_error_msg = $seocheck_report->msg . '<br/>' . $seocheck_reportobj->solution;
            } else {
                 $urlViewReporteRanker = seocheck_addqueryonurl(seocheck_addqueryonurl(seocheck_getfrontendurl(), 'subaction=' . $seocheck_reportobj->id),'action=report');                  
            }
        }
    }
}

