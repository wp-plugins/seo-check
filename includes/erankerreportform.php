<?PHP
global $sat_accountinfo, $sat_subaction, $sat_reporttypeobj, $urlViewReporteRanker, $sat_reportobj, $allfactor_list, $show_lead_generator_form, $instance;
require_once 'erankerreportform-init.php';
if (empty($urlViewReporteRanker)) {
    ?>
    <form class="sat_newreport" method="<?= ((isset($_GET['sc_url']) && !empty($_GET['sc_url'])) || $show_lead_generator_form === FALSE) ? "post" : "get" ?>" action="">
        <table class="form-table sat_table sat_noborder sat_nomargin">
            <tr class="row_even sat_bglgray">
                <td class="sat_nobg" colspan="2">
                    <label title="<?php _e('Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com.', 'sat'); ?>" for="sc_url"><?php _e('URL', 'sat'); ?>:</label><br/>
                    <input title="<?php _e('Insert the url that will be searched. You can add a domain, subdomain or full URL. Ex: mycompany.com.', 'sat'); ?>" type="text" placeholder="<?php _e('www.yourcompany.com', 'sat'); ?>" id="sc_url" name="sc_url" size="65" value="<?php echo (isset($_POST['sc_url']) ? htmlspecialchars($_POST['sc_url']) : isset($_GET['sc_url']) ? htmlspecialchars($_GET['sc_url']) : ''); ?>" />
                </td>
            </tr>
            <?php if (((isset($_GET['sc_url']) && !empty($_GET['sc_url'])) && $show_lead_generator_form !== FALSE) || $show_lead_generator_form === FALSE) { ?>
                <tr class="row_even sat_bglgray">
                    <td class="sat_nobg" colspan="2">
                        Factors                    
                        <span id="sat_eranker_selectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer">
                            <a href="javascript:jQuery('.sat_newreport input[name^=factorsGroup]').prop('checked', true);jQuery('.sat_newreport #sat_eranker_selectall').hide();jQuery('.sat_newreport #sat_eranker_deselectall').show();">
                                <?php _e('Select All', 'er') ?>
                            </a>
                        </span>
                        <span id="sat_eranker_deselectall" style="font-size: 12px; font-weight: normal;float: right; cursor: pointer; display: none;">
                            <a href="javascript:jQuery('.sat_newreport input[name^=factorsGroup]').prop('checked', false);jQuery('.sat_newreport #sat_eranker_selectall').show();jQuery('.sat_newreport #sat_eranker_deselectall').hide();">
                                <?php _e('Deselect All', 'er') ?>
                            </a>
                        </span>
                    </td>
                </tr>           
                <tr class="row_even sat_bglgray">
                    <td class="sat_nobg" colspan="2">
                        <?php
                        if (!empty($allfactor_list)) {
                            foreach ($allfactor_list as $key => $value) {
                                $is_checked = false;
                                if (in_array($key, $sat_accountinfo->plan->default_factors)) {
                                    $is_checked = TRUE;
                                }
                                ?>
                                <label class="factor_list_checkbox" title="" for="factor_<?PHP echo $key ?>">
                                    <input id="factor_<?PHP echo $key ?>" type="checkbox" name="factorsGroup[]" <?PHP echo $is_checked ? 'checked="checked"' : '' ?> value="<?PHP echo $key ?>"><?PHP echo $value->friendly_name ?>
                                </label>
                            <?php } ?> 
                        </td>
                    </tr>
                <?PHP }
            } ?>
    <?php if ((isset($_GET['sc_url']) && !empty($_GET['sc_url'])) && $show_lead_generator_form !== FALSE) { ?>
                <tr class="row_even sat_bglgray">
                    <td class="sat_nobg" colspan="2">
                        <label title="<?php _e('Insert the name company', 'sat'); ?>" for="sc_companyname"><?php _e('Company Name', 'sat'); ?>:</label><br/>
                        <input title="<?php _e('Insert the name company', 'sat'); ?>" type="text" placeholder="<?php _e('yourcompany', 'sat'); ?>" id="sc_companyname" name="sc_companyname" size="65" value="<?php echo (isset($_POST['sc_companyname']) ? htmlspecialchars($_POST['sc_companyname']) : ''); ?>" />
                    </td>
                </tr> 

                <tr class="row_even sat_bglgray">
                    <td class="sat_nobg" colspan="2">
                        <label title="<?php _e('Insert the phone number', 'sat'); ?>" for="sc_phone"><?php _e('Phone', 'sat'); ?>:</label><br/>
                        <input title="<?php _e('Insert the phone number', 'sat'); ?>" type="text" placeholder="<?php _e('123 1234 1231', 'sat'); ?>" id="sc_phone" name="sc_phone" size="65" value="<?php echo (isset($_POST['sc_phone']) ? htmlspecialchars($_POST['sc_phone']) : ''); ?>" />
                    </td>
                </tr> 

                <tr class="row_even sat_bglgray">
                    <td class="sat_nobg" colspan="2">
                        <label title="<?php _e('Insert the email.', 'sat'); ?>" for="sc_email"><?php _e('Email', 'sat'); ?>:</label><br/>
                        <input title="<?php _e('Insert the email.', 'sat'); ?>" type="text" placeholder="<?php _e('your@email.com', 'sat'); ?>" id="sat_newreporteranker_url" name="sc_email" size="65" value="<?php echo (isset($_POST['sc_email']) ? htmlspecialchars($_POST['sc_email']) : ''); ?>" />
                    </td>
                </tr> 
    <?PHP } ?>
        </table>        
        <div class="sat_padded">
            <input type="submit" class="button-primary er_createreport_plugin" value="Create Report">
        </div>
    </form>

    <?php
} else {
    $redirect = sat_redirectviewreportpage($urlViewReporteRanker);
    echo $redirect;
}