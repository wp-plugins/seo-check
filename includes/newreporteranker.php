<?php global $seocheck_error, $seocheck_error_msg, $seocheck_accountinfo; ?>
<div class="wrap">   
    <div class="er_plugin seocheck_page_localrankchecker">   
        
        <h2><?php _e('SEO Check Plugin', 'er'); ?></h2>
        
        <div class="widget seocheck_widget clearfix">
            <div class="widget-top seocheck_nomovecursor">
                <div class="widget-title">                    
                    <h4><?php _e('New SEO Report', 'er'); ?> <span class="in-widget-title"></span>
                        <a style="font-size: 12px; font-weight: normal;" class="seocheck_floatrigth seocheck_mariginleft" href="http://www.eranker.com/login" target="_blank"><?php _e('Latest Reports', 'er'); ?> <img src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/icon-new-window.gif') ?>" alt="" style="display: inline;"></a>
                    </h4>
                </div>
            </div>
            <div class="widget-inside seocheck_nopadding" style="display: block;">
                <?PHP
                global $seocheck_subaction;
                $seocheck_subaction = 'sitereport';
                include 'erankerreportform.php';
                if ($seocheck_error) {
                    echo '<div id="seocheck_error-modal" title="' . __('An error occurred', 'er') . '">';
                    echo '<p><span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>';
                    echo $seocheck_error_msg;
                    echo '</p>';
                    echo '</div>';
                }
                ?> 
            </div>
        </div>
    </div>
</div>