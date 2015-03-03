<?PHP

global $sat_settings;
global $sat_countrylist;
global $sat_error;
global $sat_error_msg;
global $sat_accountinfo;
global $sat_action;
global $sat_subaction;
global $sat_newreporteranker_url;
global $urlViewReporteRanker;
global $sat_reportobj;
global $allfactor_list;
global $sat_accountcachetime;
global $sat_factorscachetime;
global $sat_nocache;
global $show_lead_generator_form;

$show_lead_generator_form =  ($show_lead_generator_form == 1)? TRUE : FALSE ;

// Check if Wordpress is Loaded
if (!function_exists('add_action')) {
    exit('Sorry, you can not execute this file without wordpress.');
}

$sat_error = FALSE;
$sat_error_msg = '';
$urlViewReporteRanker = '';
$language = 'en';

//Conect to the API and test the apikey

$erapi = new eRankerAPI($sat_settings['email'], $sat_settings['apikey']);

$sat_accountinfo = get_transient('sat_account' . (md5($sat_settings['email'], $sat_settings['apikey'])));
if (empty($sat_accountinfo) || isset($sat_accountinfo->debug) || $sat_nocache) {
    $sat_accountinfo = $erapi->account();
    if (!empty($sat_accountinfo) && !isset($sat_accountinfo->debug)) {
        set_transient('sat_account' . (md5($sat_settings['email'], $sat_settings['apikey'])), $sat_accountinfo, $sat_accountcachetime);
    }
}

$allfactor_list = get_transient('sat_factors_' . $language);
if (empty($allfactor_list) || isset($allfactor_list->debug) || $sat_nocache) {
    $allfactor_list = $erapi->factors($language);
    if (!empty($allfactor_list) && !isset($allfactor_list->debug)) {
        set_transient('sat_factors_' . $language, $allfactor_list, $sat_factorscachetime);
    }
}

if (isset($_REQUEST['sc_url'])) {

    $sat_newreporteranker_url = isset($_REQUEST['sc_url']) ? $_REQUEST['sc_url'] : "";
    $checkbox_factors = isset($_REQUEST['factorsGroup']) && !empty($_REQUEST['factorsGroup']) ? $_REQUEST['factorsGroup'] : array();
    if(empty($checkbox_factors)){
        $checkbox_factors = $sat_accountinfo->plan->default_factors;
    }

    //Check URL
    if (isset($sat_newreporteranker_url)) {
        if (strlen($sat_newreporteranker_url) < 5 || strpos($sat_newreporteranker_url, ".") === FALSE) {
            $sat_error = TRUE;
            $sat_error_msg = __("Is not a valid url. Must possess the url http:// or https://", 'sat');
            $sat_newreporteranker_url = null;
        } else {
            if (strpos($sat_newreporteranker_url, "http://") === FALSE || strpos($sat_newreporteranker_url, "https://") === FALSE) {
                $sat_newreporteranker_url = 'http://' . $sat_newreporteranker_url;
            }
        }
    } else {
        $sat_newreporteranker_url = null;
    }
    if (empty($sat_newreporteranker_url)) {
        $sat_error = TRUE;
        $sat_error_msg = __("You must specify a valid URL or domain. <br/>A URL must have at least 5 caracters and a dot (.).", 'sat');
    }   
    
    //Check Factors
    if (empty($checkbox_factors)) {
        $sat_error = TRUE;
        $sat_error_msg = __("You need select at least one factor from the list.", 'sat');
    }
    
     if ($sat_error === FALSE && $show_lead_generator_form === TRUE) {

        if (isset($_REQUEST['sc_companyname']) && empty($_REQUEST['sc_companyname'])) {
            $sat_error = TRUE;
            $sat_error_msg = __("You must enter your company name", 'sat');
        }

        if (isset($_REQUEST['sc_phone']) && empty($_REQUEST['sc_phone'])) {
            $sat_error = TRUE;
            $sat_error_msg = __("You must enter your phone number", 'sat');
        }

        if ((isset($_REQUEST['sc_email']) && empty($_REQUEST['sc_email']) && strlen($_REQUEST['sc_email']) <= 5 ) || isset($_REQUEST['sc_email']) && empty($_REQUEST['sc_email']) && strstr($_REQUEST['sc_email'], '@') == FALSE) {
            $sat_error = TRUE;
            $sat_error_msg = __("You must enter your email address or the email address you entered is invalid.", 'sat');
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
        if (!empty($_REQUEST['sc_companyname']) && !empty($_REQUEST['sc_phone']) && !empty($_REQUEST['sc_email'])) {
            if (!wp_mail($admin_email, $sc_subject, $sc_message)) {
                $sat_error = TRUE;
                $sat_error_msg = __("An internal error occurred while sending the email.", 'sat');
            }
        }
    }    
    
    if ($sat_error === FALSE) {
        $sat_reportobj = $erapi->reportnew($sat_newreporteranker_url, $checkbox_factors);
        if (empty($sat_reportobj)) {
            $sat_error = TRUE;
            $sat_error_msg = __('Could not create a report.<br/>An unknown error occurred', 'sat');
        } else {
            if (isset($sat_reportobj->msg)) {
                $sat_error = TRUE;
                $sat_error_msg = $sat_report->msg . '<br/>' . $sat_reportobj->solution;
            } else {
                 $urlViewReporteRanker = sat_addqueryonurl(sat_addqueryonurl(sat_getfrontendurl(), 'subaction=' . $sat_reportobj->id),'action=report');                  
            }
        }
    }
}

