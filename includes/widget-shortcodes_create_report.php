<?php
/**
 * Widget: View report Class
 */

global $show_lead_generator_form;

$show_lead_generator_form = FALSE;

class seocheck_create_report_widget extends WP_Widget {

    /** Constructor -- name this the same as the class above */
    function seocheck_create_report_widget() {
        parent::WP_Widget(false, $name = 'SEO Toolbox: Create Report');
    }

    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {
        echo seocheck_create_report_generic($args, $instance);
    }

    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['show_title'] = (boolean) strip_tags($new_instance['show_title']);

        $instance['show_header_report'] = (boolean) strip_tags($new_instance['show_header_report']);

        $instance['show_lead_generator_form'] = (boolean) strip_tags($new_instance['show_lead_generator_form']);

        $overflow = strip_tags($new_instance['overflow']);
        if (empty($overflow)) {
            $instance['overflow'] = 'auto';
        } else {
            $instance['overflow'] = $overflow;
        }

        $instance['powered_by'] = (boolean) strip_tags($new_instance['powered_by']);

        $theme = strip_tags($new_instance['theme']);
        if (empty($theme)) {
            $instance['theme'] = 'full';
        } else {
            $instance['theme'] = $theme;
        }

        $instance['show_title'] = (boolean) strip_tags($new_instance['show_title']);

        $max_height = (int) trim(strip_tags($new_instance['max_height']));
        if ($max_height < 1) {
            $instance['max_height'] = '10000px';
        } else {
            $instance['max_height'] = $max_height . "px";
        }

        $instance['before_text'] = strip_tags($new_instance['before_text'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['after_text'] = strip_tags($new_instance['after_text'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['type'] = strip_tags($new_instance['type']);

        $instance['seeform'] = strip_tags($new_instance['seeform']);

        $instance['credits'] = min(1000000, max(1, strip_tags($new_instance['credits'] === null ? 100 : $new_instance['credits'] )));

        $instance['creditscycle'] = strip_tags($new_instance['creditscycle']);

        $instance['captcha'] = strip_tags($new_instance['captcha'] === null ? "unauthenticated" : $new_instance['captcha']);


        return $instance;
    }

    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {

        $title = esc_attr($instance['title'] === null ? __("Create SEO Report") : $instance['title']);
        $show_title = esc_attr($instance['show_title'] === null ? 1 : $instance['show_title']);
        $headerreportshow = esc_attr($instance['show_header_report'] === null ? 1 : $instance['show_header_report']);
        $overflow = esc_attr($instance['overflow']);
        $powered_by = esc_attr($instance['powered_by'] === null ? 1 : $instance['powered_by']);
        $theme = esc_attr($instance['theme']);
        $after_text = esc_attr($instance['after_text']);
        $before_text = esc_attr($instance['before_text']);
        $max_height = esc_attr($instance['max_height'] === null ? "10000px" : $instance['max_height']);
        $type = esc_attr($instance['type']);
        $seeform = esc_attr($instance['seeform'] === null ? "authenticated" : $instance['seeform']);
        $credits = min(1000000, max(1, esc_attr($instance['credits'] === null ? 100 : $instance['credits'] )));
        $creditscycle = esc_attr($instance['creditscycle']);
        $captcha = esc_attr($instance['captcha'] === null ? "unauthenticated" : $instance['captcha']);
        $show_lead_generator_form = esc_attr($instance['show_lead_generator_form'] === null ? 1 : $instance['show_lead_generator_form']);
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Report Type'); ?>:</label> 
            <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" >
                <option value="sitereport" <?PHP echo strcasecmp('sitereport', $type) === 0 ? 'selected' : '' ?>><?php _e('Seo Report'); ?></option>                
            </select>
        </p>
        <p> 
            <label for="<?php echo $this->get_field_id('seeform'); ?>"><?php _e('Who can see the form?'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id('seeform'); ?>" name="<?php echo $this->get_field_name('seeform'); ?>" >
                <option value="everyone" <?PHP echo strcasecmp('everyone', $seeform) === 0 ? 'selected' : '' ?>><?php _e('Everyone'); ?></option>
                <option value="authenticated" <?PHP echo strcasecmp('authenticated', $seeform) === 0 ? 'selected' : '' ?>><?php _e('Authenticated Users'); ?></option>
                <option value="admin" <?PHP echo strcasecmp('admin', $seeform) === 0 ? 'selected' : '' ?>><?php _e('Administrators only'); ?></option>
            </select>
        </p>
        <!--
                <p>
                    <label for="<?php echo $this->get_field_id('credits'); ?>"><?php _e('Credits per User'); ?>:</label> 
                    <input class="widefat" id="<?php echo $this->get_field_id('credits'); ?>" 
                           name="<?php echo $this->get_field_name('credits'); ?>" 
                           type="number" value="<?php echo $credits; ?>" min="1" max="50000"/>
                </p>

                <p> 
                    <label for="<?php echo $this->get_field_id('creditscycle'); ?>"><?php _e('Credits Cycle'); ?>:</label> 
                    <select class="widefat" id="<?php echo $this->get_field_id('creditscycle'); ?>" name="<?php echo $this->get_field_name('creditscycle'); ?>" >
                        <option value="daily" <?PHP echo strcasecmp('daily', $creditscycle) === 0 ? 'selected' : '' ?>><?php _e('Daily'); ?></option>
                        <option value="weekly" <?PHP echo strcasecmp('weekly', $creditscycle) === 0 ? 'selected' : '' ?>><?php _e('Weekly'); ?></option>
                        <option value="monthly" <?PHP echo strcasecmp('monthly', $creditscycle) === 0 ? 'selected' : '' ?>><?php _e('Monthly'); ?></option>
                    </select>
                </p>

                <p> 
                    <label for="<?php echo $this->get_field_id('captcha'); ?>"><?php _e('Show Captcha Protection to'); ?>:</label> 
                    <select class="widefat" id="<?php echo $this->get_field_id('captcha'); ?>" name="<?php echo $this->get_field_name('captcha'); ?>" >
                        <option value="everyone" <?PHP echo strcasecmp('everyone', $captcha) === 0 ? 'selected' : '' ?>><?php _e('Everyone'); ?></option>
                        <option value="unauthenticated" <?PHP echo strcasecmp('unauthenticated', $captcha) === 0 ? 'selected' : '' ?>><?php _e('Unauthenticated user'); ?></option>
                        <option value="nobody" <?PHP echo strcasecmp('nobody', $captcha) === 0 ? 'selected' : '' ?>><?php _e('Nobody'); ?></option>
                    </select>
                </p>-->

        <h4><?php _e('Appearance Settings'); ?></h4>
        <hr />
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" 
                   type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <input class="widefat" id="<?php echo $this->get_field_id('show_title'); ?>" 
                   name="<?php echo $this->get_field_name('show_title'); ?>" 
                   type="checkbox" value="1" <?PHP echo $show_title ? "checked" : "" ?>/>
            <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Display Title'); ?></label> 
        </p>
        <p>
            <input class="widefat" id="<?php echo $this->get_field_id('show_lead_generator_form'); ?>" 
                   name="<?php echo $this->get_field_name('show_lead_generator_form'); ?>" 
                   type="checkbox" value="1" <?PHP echo $show_lead_generator_form ? "checked" : "" ?>/>
            <label for="<?php echo $this->get_field_id('show_lead_generator_form'); ?>"><?php _e('Show the lead generator form'); ?></label> 
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('before_text'); ?>"><?php _e('Before Text'); ?>:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('before_text'); ?>" 
                   name="<?php echo $this->get_field_name('before_text'); ?>" 
                   type="text" value="<?php echo $before_text; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('after_text'); ?>"><?php _e('After Text'); ?>:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('after_text'); ?>" 
                   name="<?php echo $this->get_field_name('after_text'); ?>" 
                   type="text" value="<?php echo $after_text; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme'); ?>:</label> 
            <select class="widefat" id="<?php echo $this->get_field_id('theme'); ?>" 
                    name="<?php echo $this->get_field_name('theme'); ?>" >
                <option value="full" <?PHP echo strcasecmp('full', $theme) === 0 ? 'selected' : '' ?>><?php _e('Full'); ?></option>
                <option value="lite" <?PHP echo strcasecmp('lite', $theme) === 0 ? 'selected' : '' ?>><?php _e('Lite'); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('overflow'); ?>"><?php _e('Overflow:'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id('overflow'); ?>" 
                    name="<?php echo $this->get_field_name('overflow'); ?>" >
                <option value="auto" <?PHP echo strcasecmp('auto', $overflow) === 0 || empty($overflow) ? 'selected' : '' ?>><?php _e('Auto'); ?></option>
                <option value="hidden" <?PHP echo strcasecmp('hidden', $theme) === 0 ? 'selected' : '' ?>><?php _e('Hidden'); ?></option>
                <option value="scroll" <?PHP echo strcasecmp('scroll', $theme) === 0 ? 'selected' : '' ?>><?php _e('Scroll'); ?></option>
                <option value="visible" <?PHP echo strcasecmp('visible', $theme) === 0 ? 'selected' : '' ?>><?php _e('Visible'); ?></option>
            </select>
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('max_height'); ?>"><?php _e('Max height:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('max_height'); ?>" 
                   name="<?php echo $this->get_field_name('max_height'); ?>" 
                   type="text" value="<?php echo $max_height; ?>" />
        </p>

        <p>
            <input class="widefat" id="<?php echo $this->get_field_id('powered_by'); ?>" 
                   name="<?php echo $this->get_field_name('powered_by'); ?>" 
                   type="checkbox" value="1" <?PHP echo $powered_by ? "checked" : "" ?>/>
            <label for="<?php echo $this->get_field_id('powered_by'); ?>"><?php _e('Display <i>Powered By<i> (Thank You!)'); ?></label> 
        </p>



        <?PHP
    }

}

//End of the widget class

function seocheck_create_report_generic($args, $instance) {
    //TODO 
    ob_start();
    extract($args);
    $title =  __("Create SEO Report");
    if (isset($instance['title'])) {
        $title = esc_attr($instance['title'] === null ? __("Create SEO Report") : $instance['title']);
    }
    $show_title = esc_attr(1);
    if (isset($instance['show_title'])) {
        $show_title = esc_attr($instance['show_title'] === null ? 1 : $instance['show_title']);
    }
    $headerreportshow = esc_attr(1);
    if (isset($instance['show_header_report'])) {
        $headerreportshow = esc_attr($instance['show_header_report'] === null ? 1 : $instance['show_header_report']);
    }
    $overflow = null;
    if (isset($instance['overflow'])) {
        $overflow = esc_attr($instance['overflow']);
    }
    $powered_by = esc_attr(1);
    if (isset($instance['powered_by'])) {
        $powered_by = esc_attr($instance['powered_by'] === null ? 1 : $instance['powered_by']);
    }
    $theme = null;
    if (isset($instance['theme'])) {
        $theme = esc_attr($instance['theme']);
    }
    $after_text = null;
    if (isset($instance['after_text'])) {
        $after_text = esc_attr($instance['after_text']);
    }
    $before_text = null;
    if (isset($instance['before_text'])) {
        $before_text = esc_attr($instance['before_text']);
    }
    $max_height = esc_attr("10000px");
    if (isset($instance['max_height'])) {
        $max_height = esc_attr($instance['max_height'] === null ? "10000px" : $instance['max_height']);
    }
    $type = null;
    if (isset($instance['type'])) {
        $type = esc_attr($instance['type']);
    }
    $seeform = esc_attr("authenticated");
    if (isset($instance['seeform'])) {
        $seeform = esc_attr($instance['seeform'] === null ? "authenticated" : $instance['seeform']);
    }
    $credits = esc_attr(100);
    if (isset($instance['credits'])) {
        $credits = min(1000000, max(1, esc_attr($instance['credits'] === null ? 100 : $instance['credits'] )));
    }
    $creditscycle = null;
    if (isset($instance['creditscycle'])) {
        $creditscycle = esc_attr($instance['creditscycle']);
    }
    $captcha = esc_attr("unauthenticated");
    if (isset($instance['captcha'])) {
        $captcha = esc_attr($instance['captcha'] === null ? "unauthenticated" : $instance['captcha']);
    }
    $show_lead_generator_form = esc_attr(1);
    if (isset($instance['show_lead_generator_form'])) {
        $show_lead_generator_form = esc_attr($instance['show_lead_generator_form'] === null ? 1 : $instance['show_lead_generator_form']);
    }
    if (!current_user_can('administrator') && strcasecmp($seeform, 'admin') === 0) {
        return null;
    }

    if (!is_user_logged_in() && strcasecmp($seeform, 'unauthenticated') === 0) {
        return null;
    }

    echo '<div class="seocheck_theme_' . $theme . '">';

    echo $before_widget;
    
      if (($title && $show_title)) {
        echo $before_title . $title . $after_title;
    }  
    
    
    echo '<div>' . $before_text . '</div>';

    if ($show_title || $powered_by || $before_text || $after_text) {
        echo '<div style="margin-top: 5px; margin-bottom:5px;border-bottom: 1px solid #EEEEEE; padding-bottom: 5px;  border-top: 1px solid #EEEEEE; padding-top: 5px; clear: both;overflow:' . $overflow . ';max-height:' . $max_height . ';margin-bottom:5px;">';
    }
    if ($headerreportshow) {
        ?>    
        <?php
    }
    //Call the Report viewer
    global $seocheck_theme;

    $seocheck_theme = $theme;
    seocheck_loadscripts();
    seocheck_loadscripts_foradmin();



    if (strcasecmp($type, 'sitereport') === 0) {
        $GLOBALS['show_lead_generator_form'] = $show_lead_generator_form;
        seocheck_page_erankerreport();
    }

    //End of the viewer
    if ($show_title || $powered_by) {
        echo '</div>';
    }
    echo '<div>' . $after_text . '</div>';

    if ($powered_by) {
        echo '<div style="margin-bottom:5px; text-align:right; font-size: smaller;">Powered by &nbsp; <a title="Local SEO Tools - GeoRanker" href="http://www.georanker.com" target="_blank"><img style="height: 20px;margin-top: -10px;display:inline; clear: both; margin-right: 5px;margin-left: -5px;" src="' . plugins_url(basename(dirname(dirname(__FILE__)))) . '/images/georanker_logo_powered_by.png"></a></div>';
    }
    echo $after_widget;

    echo '</div>';
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}

function seocheck_create_report_shortcode($atts, $content = null) {
    $empty_data = array('after_widget' => '', 'before_widget' => $content, 'before_title' => '<h3>', 'after_title' => '</h3>');
    return seocheck_create_report_generic($empty_data, $atts);
}

function seocheck_register_shortcode_create_report() {
    add_shortcode('create-report-seocheck', 'seocheck_create_report_shortcode');
}

// Add the widget and shortcode to WP
add_action('widgets_init', create_function('', 'return register_widget("seocheck_create_report_widget");'));
add_action('init', 'seocheck_register_shortcode_create_report');

