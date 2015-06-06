<?PHP require_once 'settings-init.php'; ?>
<div class="wrap">   
    <div class="er_plugin seocheck_page_settings">
        <a href="http://www.eranker.com" target="blank" class="seocheck_floatrigth seocheck_marigin5bottom seocheck_mariginleft" title="<?php _e('Visit eRanker.com Website!', 'er'); ?>" >
            <img src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/eranker-logo-big.png') ?>" height="42" alt="eRanker" />
        </a>
        <h2><?php _e('SEO Check Plugin Settings', 'er'); ?></h2>

        <?PHP if (empty($accountinfo) || isset($accountinfo->debug)) { ?>

            <p>
                <?php _e('Create deep SEO reports directly from your admin page. Let your users see the data and monitor the results.', 'er'); ?>
            </p>
            <div class="seocheck_box" id="seocheck_box_info">
                <div class="seocheck_box_title">
                    <?php _e('Get Started with eRanker!', 'er'); ?>
                </div>   
                <a class="button-secondary seocheck_floatingrigthbuttontop" href="http://www.eranker.com/register/" target="_blank"><strong><?php _e('Setup your free account', 'er'); ?></strong></a>
                <p>
                    <?php printf(__('To be able to use this plugin you first of all need to create a free account at %s.', 'er'), '<a href="http://www.eranker.com/register" target="_blank">http://www.eranker.com/</a>'); ?>
                    <?php _e('After having created your account, please enter the API Key and your login email in the form below.', 'er'); ?>
                    <?php _e("Don't worry the setup takes only a couple of seconds!", 'er'); ?>
                </p>
            </div>
            <?php
        } else {
            ?>
            <p></p>
            <div class="seocheck_box" id="seocheck_box_info">
                <div class="seocheck_box_title">
                    <?php _e('Your API Account is setup correctly', 'er'); ?>
                </div>
                <a class="button-secondary seocheck_floatingrigthbuttontop" href="http://www.eranker.com/login/" target="_blank"><strong><?php _e('Visit your eRanker account', 'er'); ?></strong> </a>
                <p>
                    <?php _e('Login to your account to manage your reports, monitors and plan.', 'er'); ?> 
                    <?php _e('Detailed information of all reports you create using your api key will be available under your eRanker account dashboard.', 'er'); ?>
                </p>
            </div>
            <?php
        }
        ?>

        <?php
        if (isset($_POST) && isset($_POST['seocheck_settings']) && !empty($_POST['seocheck_settings']) && is_admin() && current_user_can('manage_options')) {
            if (!empty($accountinfo) && !isset($accountinfo->debug)) {
                echo '<div class="seocheck_box" id="seocheck_box_updated">';
                echo __('Your modifications have been saved successfully!', 'er');
                echo '</div>';
            } else {
                echo '<div class="seocheck_box" id="seocheck_box_updatederror">';
                echo __('Unable to connect to eRanker API. Make sure that you entred a valid API Key and your email registred on eRanker!', 'er');
                if (isset($accountinfo->msg) && !empty($accountinfo->msg)) {
                    echo '<br/>' . __('Details', 'er') . ': <span style="font-weight:normal">' . strip_tags($accountinfo->msg) . '</span>';
                }
                echo '</div>';
            }
        }
        ?>
        <?PHP if (empty($accountinfo) || isset($accountinfo->debug)) { ?>
            <div class="widget seocheck_widget">
                <div class="widget-top seocheck_nomovecursor">
                    <div class="widget-title">
                        <a class="seocheck_floatrigth seocheck_mariginleft" href="http://www.eranker.com/settings" target="_blank"><?php _e('Click here to create and view your API Credentials', 'er'); ?></a>
                        <h4><?php _e('API Connection Settings', 'er'); ?> <span class="in-widget-title"></span></h4>
                    </div>
                </div>
                <div class="widget-inside seocheck_nopadding" style="display: block;">
                    <form method="post" action="admin.php?page=seocheck_page_settings">
                        <table class="form-table seocheck_table seocheck_noborder seocheck_nomargin">
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px">
                                    <label for="seocheck_settings_api_email"><?php _e('Email', 'er'); ?>:</label>
                                </td>
                                <td class="seocheck_nobg">
                                    <input type="text"  id="seocheck_settings_api_email" name="seocheck_settings[email]" size="65" value="<?php echo (isset($seocheck_settings['email']) ? htmlspecialchars($seocheck_settings['email']) : ''); ?>" />
                                </td>
                            </tr>
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px">
                                    <label for="seocheck_settings_api_key"><?php _e('API Key', 'er'); ?>:</label>
                                </td>
                                <td class="seocheck_nobg">
                                    <input type="text"  id="seocheck_settings_api_key" name="seocheck_settings[apikey]" size="65" value="<?php echo (isset($seocheck_settings['apikey']) ? htmlspecialchars($seocheck_settings['apikey']) : ''); ?>" />
                                </td>
                            </tr>                       
                        </table>
                        <div class="seocheck_padded">
                            <input type="hidden" name="seocheck_settings[apikey_invalid]" value="0" />
                            <input type="submit" name="submit" class="button-primary" value="<?php _e('Save &amp; Verify API Settings', 'er') ?>" />
                        </div>
                    </form> 
                </div>
            </div>
        <?PHP } else { ?>
            <div class="widget seocheck_widget">
                <div class="widget-top seocheck_nomovecursor">
                    <div class="widget-title">                       
                        <h4 ><?php _e('API account status', 'er'); ?> <span class="in-widget-title"></span>
                            <a style="font-size: 12px;" class="seocheck_floatrigth seocheck_mariginleft" href="http://www.eranker.com/settings" target="_blank"><?php _e('Account Settings at eRanker', 'er'); ?> <img src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/icon-new-window.gif') ?>" alt="" /></a>
                        </h4>
                    </div>
                </div>
                <div class="widget-inside seocheck_nopadding" style="display: block;">
                    <form method="post" action="admin.php?page=seocheck_page_settings">
                        <table class="form-table seocheck_table seocheck_noborder seocheck_nomargin">
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px">
                                    <?php _e('User', 'er'); ?>:
                                </td>
                                <td class="seocheck_nobg">
                                    <img class="seocheck_floatleft seocheck_mariginright seocheck_borderradius3" src="<?PHP echo "https://www.gravatar.com/avatar/" . md5(strtolower(trim($accountinfo->email))) . "?d=mm&s=40" ?>" alt="<?php echo stripslashes($accountinfo->display_name); ?>"/>
                                    <h3 style="height: 20px;margin: 0;padding: 0;color: '#48524B';"><?php echo $accountinfo->display_name; ?></h3>
                                    <?php echo $accountinfo->email; ?>
                                </td>
                            </tr>
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px" >
                                    <?php _e('API Key', 'er'); ?>:
                                </td>
                                <td class="seocheck_nobg" style="font-family: monospace; ">
                                    <?php echo substr(strtoupper($seocheck_settings['apikey']), 0, 10); ?><img style="vertical-align: text-top;" src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/apikeyblur.jpg') ?>" alt="XXXXXXXXXXXXXXXXXXXXXXX" />
                                </td>
                            </tr>   
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px">
                                    <?php _e('Credits', 'er'); ?>:
                                </td>
                                <td class="seocheck_nobg">
                                    <?php
                                    //TODO add htmlspecial char in all output that com from post or api
                                    echo $accountinfo->credits;
                                    ?> of <?php echo $accountinfo->plan->monthly_credits; ?>
                                </td>
                            </tr>   
                            <tr class="row_even seocheck_bglgray">
                                <td class="row_multi seocheck_nobg" style="width:200px">
                                    <?php _e('Plan', 'er'); ?>:
                                </td>
                                <td class="seocheck_nobg">
                                    <?PHP
                                    $medalimg = 'medal_gray_small.png';
                                    $planamecolor = '#48524B';
                                    if ($accountinfo->plan->price > 0) {
                                        $medalimg = 'medal_glod_small.png';
                                        $planamecolor = '#686004';
                                    }
                                    if ($accountinfo->plan->price > 30) {
                                        $medalimg = 'medal_blue_small.png';
                                        $planamecolor = '#0916AC';
                                    }
                                    if ($accountinfo->plan->price > 90) {
                                        $medalimg = 'medal_red_small.png';
                                        $planamecolor = '#AC0909';
                                    }
                                    ?>
                                    <a href="http://www.eranker.com/plans" target="_blank"><img src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/' . $medalimg) ?>" alt="" class="seocheck_floatleft seocheck_mariginright"/></a>
                                    <a href="http://www.eranker.com/plans" target="_blank" style="text-decoration: none"><h3 style="height: 20px;margin: 0;padding: 0;color: <?php echo $planamecolor; ?>;"><?php echo $accountinfo->plan->display_name; ?> <img src="<?PHP echo plugins_url(SEOCHECK_FOLDERNAME . '/images/icon-new-window.gif') ?>" alt="" /></h3></a>
                                    Renew: <?php echo $accountinfo->plan->expiration; ?>
                                </td>
                            </tr> 
                        </table>
                        <div class="seocheck_padded">
                            <input type="hidden" name="seocheck_settings[email]" value="" />
                            <input type="hidden" name="seocheck_settings[apikey_invalid]" value="1" />
                            <input type="hidden" name="seocheck_settings[apikey]" value="" />
                            <input onClick="return confirm('<?php _e('Are you sure you want to clear your API settings?\nNOTE: The plugin will stop work until you add new credentials!', 'er'); ?>');" type="submit" name="submit" class="button-primary" value="<?php _e('Unlink Account', 'er') ?>" />
                        </div>
                    </form>
                </div>
            </div>
        <?PHP } ?>


        <div class="seocheck_box" id="seocheck_box_help">
            <div class="seocheck_box_title">
                <?php _e('Help, Updates &amp; Documentation', 'er'); ?>
            </div>
            <ul>
                <li><?php printf(__('<a target="_blank" href="%s">Read the online documentation</a> and our <a target="_blank" href="%s">Blog</a> for more information about this plugin', 'er'), 'http://www.eRanker.com/wordpress-plugin/', 'http://www.eRanker.com/blog/'); ?>;</li>
                <li><?php printf(__('<a target="_blank" href="%s">Contact us</a> if you have feedback or need assistance', 'er'), 'http://www.eRanker.com/contact/'); ?>;   </li>   
                <li><?php printf(__('Do you want <strong>develop your own plugins</strong> using eRanker API? <a target="_blank" href="%s">See our API documentation</a>', 'er'), 'http://www.eRanker.com/api/'); ?>.</li>
            </ul>
        </div>
    </div>
</div>