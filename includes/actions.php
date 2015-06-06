<?php

function seocheck_act_report() {
    global $seocheck_subaction, $seocheck_settings, $seocheck_factors, $seocheck_report, $seocheck_factorscachetime, $seocheck_report_scores, $seocheck_reportcachetime, $seocheck_nocache, $erapi;

    if (isset($_REQUEST['sc_url']) && !empty($_REQUEST['sc_url'])) {
        require_once 'erankerreportform-init.php';
        global $seocheck_error;
        global $seocheck_error_msg;
        global $urlViewReporteRanker;
        if (!empty($seocheck_error)) {
            set_seocheck_echoerror(200, $seocheck_error_msg);
            return;
        }
        if (!empty($urlViewReporteRanker)) {
            add_action('wp_footer', 'seocheck_echo_redirect', 100);
            return;
        }
    }

    if (empty($seocheck_subaction)) {
        set_seocheck_echoerror(404, __('The report id seems invalid.', 'er'));
        return;
    }

    $seocheck_report = get_transient('seocheck_report_id_report_' . $seocheck_subaction);
    if (empty($seocheck_report) || $seocheck_nocache) {
        $erapi = new eRankerAPI($seocheck_settings['email'], $seocheck_settings['apikey']);

        $seocheck_report = $erapi->report($seocheck_subaction);
        if (empty($seocheck_report) || isset($seocheck_report->debug)) {
            set_seocheck_echoerror(404, (isset($seocheck_report->msg) ? $seocheck_report->msg : __('Error on report object.', 'er')));
            return;
        }
        if (strcasecmp($seocheck_report->status, 'DONE') !== 0) {
            set_transient('seocheck_report_id_report_' . $seocheck_subaction, $seocheck_report, $seocheck_reportcachetime);
        }
    }



    $language = 'en';

    if (!empty($seocheck_report)) {
        $seocheck_report_scores = get_transient('seocheck_report_id_scores' . $seocheck_subaction);
        if (empty($seocheck_report_scores) || $seocheck_nocache) {
            $seocheck_report_scores = $erapi->reportscores($seocheck_subaction, $language);
            if (empty($seocheck_report_scores) || isset($seocheck_report_scores->debug)) {
                set_seocheck_echoerror(404, (isset($seocheck_report_scores->msg) ? $seocheck_report_scores->msg : __('Error on Report scores object.', 'er')));
                return;
            }
            set_transient('seocheck_report_id_scores' . $seocheck_subaction, $seocheck_report_scores, $seocheck_reportcachetime);
        }

        $seocheck_factors = get_transient('seocheck_factors_' . $language);
        if (empty($seocheck_factors) || isset($seocheck_factors->debug) || $seocheck_nocache) {
            $seocheck_factors = $erapi->factors($language);
            if (!empty($seocheck_factors) && !isset($seocheck_factors->debug)) {
                set_transient('seocheck_factors_' . $language, $seocheck_factors, $seocheck_factorscachetime);
            }
        }

        //AJAX requests
        if (isset($_GET['ajax']) && !empty($_GET['ajax']) && isset($_GET['factors']) && !empty($_GET['factors'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $ajaxObj = eRankerCommons::ajaxReport($seocheck_report, $seocheck_factors, $seocheck_report_scores, $_GET['factors'], true);
            header('Content-Type: application/json');
            echo json_encode($ajaxObj, JSON_PRETTY_PRINT);
            exit;
        }

        require(dirname(dirname(__FILE__)) . "/views/report.php");
    } else {
        set_seocheck_echoerror(500, __('Imposible to read the report objectError on type report object.', 'er'));
        return;
    }
}

function seocheck_act_home() {
    echo "";
}
