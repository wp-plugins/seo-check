<?php global $sat_error, $sat_error_msg, $sat_accountinfo; ?>
<div class="wrap">   
    <div class="er_plugin sat_page_localrankchecker">    
        <a href="http://www.eranker.com" target="blank" class="sat_floatrigth sat_marigin5bottom sat_mariginleft" title="<?php _e('Visit eRanker.com Website!', 'er'); ?>" >
            <img src="<?PHP echo plugins_url(SAT_FOLDERNAME . '/images/eranker-logo-big.png') ?>" height="42" alt="eRanker" />
        </a>
        <h2><?php _e('SEO Check Plugin', 'er'); ?></h2>
        
        <div class="widget sat_widget clearfix">
            <div class="widget-top sat_nomovecursor">
                <div class="widget-title">                    
                    <h4><?php _e('New SEO Report', 'er'); ?> <span class="in-widget-title"></span>
                        <a style="font-size: 12px; font-weight: normal;" class="sat_floatrigth sat_mariginleft" href="http://www.eranker.com/login" target="_blank"><?php _e('Latest Reports', 'er'); ?> <img src="<?PHP echo plugins_url(SAT_FOLDERNAME . '/images/icon-new-window.gif') ?>" alt="" style="display: inline;"></a>
                    </h4>
                </div>
            </div>
            <div class="widget-inside sat_nopadding" style="display: block;">
                <?PHP
                global $sat_subaction;
                $sat_subaction = 'sitereport';
                include 'erankerreportform.php';
                if ($sat_error) {
                    echo '<div id="sat_error-modal" title="' . __('An error occurred', 'er') . '">';
                    echo '<p><span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>';
                    echo $sat_error_msg;
                    echo '</p>';
                    echo '</div>';
                }
                ?> 
            </div>
        </div>
    </div>
</div>