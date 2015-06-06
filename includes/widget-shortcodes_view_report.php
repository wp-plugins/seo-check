<?php

/**
 * Widget: View report Class
 */
class seocheck_view_report_widget extends WP_Widget {

    /** Constructor -- name this the same as the class above */
    function seocheck_view_report_widget() {
        parent::WP_Widget(false, $name = 'SEO Toolbox: View Report');
    }

    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {
        echo seocheck_view_report_generic($args, $instance);
    }

    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['show_title'] = (boolean) strip_tags($new_instance['show_title']);

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

        $max_height = (int) trim(strip_tags($new_instance['max_height']));
        if ($max_height < 1) {
            $instance['max_height'] = '10000px';
        } else {
            $instance['max_height'] = $max_height . "px";
        }

        $instance['before_text'] = strip_tags($new_instance['before_text'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['after_text'] = strip_tags($new_instance['after_text'], '<b><i><sup><sub><em><strong><u><br>');

        $instance['report_id'] = strip_tags($new_instance['report_id']);

        return $instance;
    }

    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {

        $title = esc_attr($instance['title'] === null ? __("SEO Report") : $instance['title']);
        $show_title = esc_attr($instance['show_title'] === null ? 1 : $instance['show_title']);
        $report_id = esc_attr($instance['report_id']);
        $overflow = esc_attr($instance['overflow']);
        $powered_by = esc_attr($instance['powered_by'] === null ? 1 : $instance['powered_by']);
        $theme = esc_attr($instance['theme']);
        $after_text = esc_attr($instance['after_text']);
        $before_text = esc_attr($instance['before_text']);
        $max_height = esc_attr($instance['max_height'] === null ? "10000px" : $instance['max_height']);
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('report_id'); ?>"><?php _e('Report ID or Token'); ?>:</label> 
            <input class="widefat" id="<?php echo $this->get_field_id('report_id'); ?>" 
                   name="<?php echo $this->get_field_name('report_id'); ?>" 
                   type="text" value="<?php echo $report_id; ?>" />
            <br />
            <span id="seocheck_widget_singlereport_details"> - Ajax load Report details here - </span>
        </p>

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

function seocheck_view_report_generic($args, $instance) {
    ob_start();
    extract($args);

    $title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : "");
    $show_title = !isset($instance['show_title']) || $instance['show_title'] == 1;
    $report_id = isset($instance['report_id']) ? $instance['report_id'] : null;
    $overflow = isset($instance['overflow']) ? $instance['overflow'] : 'auto';
    $powered_by = !isset($instance['powered_by']) || $instance['powered_by'] == 1;
    $theme = isset($instance['theme']) ? $instance['theme'] : 'full';
    $before_text = isset($instance['before_text']) ? $instance['before_text'] : '';
    $after_text = isset($instance['after_text']) ? $instance['after_text'] : '';
    $max_height = isset($instance['max_height']) ? $instance['max_height'] : '20000px';

    echo '<div class="seocheck_theme_' . $theme . '">';

    echo $before_widget;

    if (($title && $show_title)) {
        echo $before_title . $title . $after_title;
    }
    echo '<div>' . $before_text . '</div>';
    echo '<div style="margin-top: 5px; margin-bottom:5px;border-bottom: 1px solid #EEEEEE; padding-bottom: 5px;  border-top: 1px solid #EEEEEE; padding-top: 5px; clear: both;overflow:' . $overflow . ';max-height:' . $max_height . ';margin-bottom:5px;">';

    //Call the Report viewer
    global $seocheck_action, $seocheck_subaction, $seocheck_theme;
    $seocheck_action = SEOCHECK_ACT_REPORT;
    $seocheck_subaction = $report_id;
    $seocheck_theme = $theme;
    seocheck_act_report();
    //End of the viewer

    echo '</div>';

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

function seocheck_view_report_shortcode($atts, $content = null) {
    $empty_data = array('after_widget' => '', 'before_widget' => $content, 'before_title' => '<h3>', 'after_title' => '</h3>');
    return seocheck_view_report_generic($empty_data, $atts);
}

function seocheck_register_shortcode_view_report() {
    add_shortcode('view-report-seocheck', 'seocheck_view_report_shortcode');
}

// Add the widget and shortcode to WP
add_action('widgets_init', create_function('', 'return register_widget("seocheck_view_report_widget");'));
add_action('init', 'seocheck_register_shortcode_view_report');
