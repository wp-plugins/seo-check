<?php

global $seocheck_report, $seocheck_report_scores, $seocheck_factors;


$reportHTML = eRankerCommons::getReportHTML($seocheck_report, $seocheck_report_scores, $seocheck_factors, true, false, true);
echo $reportHTML;


