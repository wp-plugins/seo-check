<?php

function sat_act_report() {
    global $sat_subaction, $sat_settings, $sat_factors, $sat_report, $sat_factorscachetime, $sat_report_scores, $sat_reportcachetime, $sat_nocache, $erapi;

    if (isset($_REQUEST['sc_url']) && !empty($_REQUEST['sc_url'])) {
        require_once 'erankerreportform-init.php';
        global $sat_error;
        global $sat_error_msg;
        global $urlViewReporteRanker;
        if (!empty($sat_error)) {
            set_echoerror(200, $sat_error_msg);
            return;
        }
        if (!empty($urlViewReporteRanker)) {
            add_action('wp_footer', 'sat_echo_redirect', 100);
            return;
        }
    }

    if (empty($sat_subaction)) {
        set_echoerror(404, __('The report id seems invalid.', 'er'));
        return;
    }

    $sat_report = get_transient('sat_report_id_report_' . $sat_subaction);
    if (empty($sat_report) || $sat_nocache) {
        $erapi = new eRankerAPI($sat_settings['email'], $sat_settings['apikey']);

        $sat_report = $erapi->report($sat_subaction);
        if (empty($sat_report) || isset($sat_report->debug)) {
            set_echoerror(404, (isset($sat_report->msg) ? $sat_report->msg : __('Error on report object.', 'er')));
            return;
        }
        if (strcasecmp($sat_report->status, 'DONE') !== 0) {
            set_transient('sat_report_id_report_' . $sat_subaction, $sat_report, $sat_reportcachetime);
        }
    }



    $language = 'en';

    if (!empty($sat_report)) {
        $sat_report_scores = get_transient('sat_report_id_scores' . $sat_subaction);
        if (empty($sat_report_scores) || $sat_nocache) {
            $sat_report_scores = $erapi->reportscores($sat_subaction, $language);
            if (empty($sat_report_scores) || isset($sat_report_scores->debug)) {
                set_echoerror(404, (isset($sat_report_scores->msg) ? $sat_report_scores->msg : __('Error on Report scores object.', 'er')));
                return;
            }
            set_transient('sat_report_id_scores' . $sat_subaction, $sat_report_scores, $sat_reportcachetime);
        }

        $sat_factors = get_transient('sat_factors_' . $language);
        if (empty($sat_factors) || isset($sat_factors->debug) || $sat_nocache) {
            $sat_factors = $erapi->factors($language);
            if (!empty($sat_factors) && !isset($sat_factors->debug)) {
                set_transient('sat_factors_' . $language, $sat_factors, $sat_factorscachetime);
            }
        }

        //AJAX requests
        if (isset($_GET['ajax']) && !empty($_GET['ajax']) && isset($_GET['factors']) && !empty($_GET['factors'])) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $ajaxObj = eRankerCommons::ajaxReport($sat_report, $sat_factors, $sat_report_scores, $_GET['factors'], true);
            header('Content-Type: application/json');
            echo json_encode($ajaxObj, JSON_PRETTY_PRINT);
            exit;
        }

        require(dirname(dirname(__FILE__)) . "/views/report.php");
    } else {
        set_echoerror(500, __('Imposible to read the report objectError on type report object.', 'er'));
        return;
    }
}

function sat_act_home() {
    echo "";
}
