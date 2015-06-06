<?php 
require_once( dirname(__FILE__) . '/../../../../wp-blog-header.php' );
require_once( dirname(__FILE__) . '/../includes/eRankerAPI.class.php' );

$seocheck_subaction	= $_GET['id'];
$erapi          = new eRankerAPI( get_option('seocheck_settings')['email'], get_option('seocheck_settings')['apikey'] );
$loginobj       = $erapi->login();
$seocheck_reportobj	= $erapi->reportscores($seocheck_subaction,'en');
echo $seocheck_reportobj->status;
?>

    