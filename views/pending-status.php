<?php 
require_once( dirname(__FILE__) . '/../../../../wp-blog-header.php' );
require_once( dirname(__FILE__) . '/../includes/eRankerAPI.class.php' );

$sat_subaction	= $_GET['id'];
$erapi          = new eRankerAPI( get_option('sat_settings')['email'], get_option('sat_settings')['apikey'] );
$loginobj       = $erapi->login();
$sat_reportobj	= $erapi->reportscores($sat_subaction,'en');
echo $sat_reportobj->status;
?>

    