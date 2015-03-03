<?php

global $sat_report, $sat_report_scores, $sat_factors;


$reportHTML = eRankerCommons::getReportHTML($sat_report, $sat_report_scores, $sat_factors, true, false, true);
echo $reportHTML;
