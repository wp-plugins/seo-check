<?PHP
global $seocheck_accountinfo, $seocheck_subaction, $seocheck_reporttypeobj, $urlViewReporteRanker, $seocheck_reportobj, $allfactor_list, $show_lead_generator_form, $instance;
require_once 'erankerreportform-init.php';
if (empty($urlViewReporteRanker)) {
    ?>
    <form class="seocheck_newreport" method="<?= ((isset($_GET['sc_url']) && !empty($_GET['sc_url'])) || $show_lead_generator_form === FALSE) ? "post" : "get" ?>" action="">
        <table class="form-table seocheck_table seocheck_noborder seocheck_nomargin">
            <tr class="row_even seocheck_bglgray">
                <td class="seocheck_nobg" colspan="2">
                    <label title="<?php _e('Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com.', 'seocheck'); ?>" for="sc_url"><?php _e('URL', 'seocheck'); ?>:</label><br/>
                    <input title="<?php _e('Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com.', 'seocheck'); ?>" type="text" placeholder="<?php _e('www.yourcompany.com', 'seocheck'); ?>" id="sc_url" name="sc_url" size="65" value="<?php echo (isset($_POST['sc_url']) ? htmlspecialchars($_POST['sc_url']) : isset($_GET['sc_url']) ? htmlspecialchars($_GET['sc_url']) : ''); ?>" />
                </td>
            </tr>            
            <tr class="row_even seocheck_bglgray">
                <td class="seocheck_nobg" colspan="2">
                    Factors                    
                    <span id="seocheck_eranker_selectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer">
                        <a href="javascript:jQuery('.seocheck_newreport input[name^=factorsGroup]').prop('checked', true);jQuery('.seocheck_newreport #seocheck_eranker_selectall').hide();jQuery('.seocheck_newreport #seocheck_eranker_deselectall').show();">
                            <?php _e('Select All', 'er') ?>
                        </a>
                    </span>
                    <span id="seocheck_eranker_deselectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer; display: none;">
                        <a href="javascript:jQuery('.seocheck_newreport input[name^=factorsGroup]').prop('checked', false);jQuery('.seocheck_newreport #seocheck_eranker_selectall').show();jQuery('.seocheck_newreport #seocheck_eranker_deselectall').hide();">
                            <?php _e('Deselect All', 'er') ?>
                        </a>
                    </span>
                </td>
            </tr>           
            <tr class="row_even seocheck_bglgray">
                <td class="seocheck_nobg" colspan="2">
                    <?php
                    if (!empty($allfactor_list)) {
                        foreach ($allfactor_list as $key => $value) {
                            $is_checked = false;
                            if (in_array($key, $seocheck_accountinfo->plan->default_factors)) {
                                $is_checked = TRUE;
                            }
                            ?>
                            <label class="factor_list_checkbox" title="" for="factor_<?PHP echo $key ?>">
                                <input id="factor_<?PHP echo $key ?>" type="checkbox" name="factorsGroup[]" <?PHP echo $is_checked ? 'checked="checked"' : '' ?> value="<?PHP echo $key ?>"><?PHP echo $value->friendly_name ?>
                            </label>
                        <?php } ?> 
                    </td>
                </tr>
            <?PHP } ?>
            <?php if ($show_lead_generator_form === TRUE) { ?>
                <tr class="row_even seocheck_bglgray">
                    <td class="seocheck_nobg" colspan="2">
                        <label title="<?php _e('Insert the name company', 'seocheck'); ?>" for="sc_companyname"><?php _e('Company Name', 'seocheck'); ?>:</label><br/>
                        <input title="<?php _e('Insert the name company', 'seocheck'); ?>" type="text" placeholder="<?php _e('yourcompany', 'seocheck'); ?>" id="sc_companyname" name="sc_companyname" size="65" value="<?php echo (isset($_POST['sc_companyname']) ? htmlspecialchars($_POST['sc_companyname']) : ''); ?>" />
                    </td>
                </tr> 

                <tr class="row_even seocheck_bglgray">
                    <td class="seocheck_nobg" colspan="2">
                        <label title="<?php _e('Insert the phone number', 'seocheck'); ?>" for="sc_phone"><?php _e('Phone', 'seocheck'); ?>:</label><br/>
                        <input title="<?php _e('Insert the phone number', 'seocheck'); ?>" type="text" placeholder="<?php _e('123 1234 1231', 'seocheck'); ?>" id="sc_phone" name="sc_phone" size="65" value="<?php echo (isset($_POST['sc_phone']) ? htmlspecialchars($_POST['sc_phone']) : ''); ?>" />
                    </td>
                </tr> 

                <tr class="row_even seocheck_bglgray">
                    <td class="seocheck_nobg" colspan="2">
                        <label title="<?php _e('Insert the email.', 'seocheck'); ?>" for="sc_email"><?php _e('Email', 'seocheck'); ?>:</label><br/>
                        <input title="<?php _e('Insert the email.', 'seocheck'); ?>" type="text" placeholder="<?php _e('your@email.com', 'seocheck'); ?>" id="seocheck_newreporteranker_url" name="sc_email" size="65" value="<?php echo (isset($_POST['sc_email']) ? htmlspecialchars($_POST['sc_email']) : ''); ?>" />
                    </td>
                </tr> 
            <?PHP } ?>
        </table>        
        <div class="seocheck_padded">
            <input type="submit" class="button-primary er_createreport_plugin" value="Create Report">
        </div>
    </form>

    <?php
} else {
    $redirect = seocheck_redirectviewreportpage($urlViewReporteRanker);
    echo $redirect;
}