<?php global $seocheck_reportobj; ?>
<div class="seocheck_page seocheck_textaligncenter">
    <?php // custom pending message while building reports
    $data = null;
    $custom_msg = get_post(get_posts(array(
                'name' => 'seo-agency-pending', 'post_type' => 'page', 'post_status' => 'publish', 'showposts' => 1, 'caller_get_posts' => 1))[0]->ID);
    if( !is_null($custom_msg) && !empty($custom_msg->post_content) ):
        $data = $custom_msg->post_content;
    endif; ?>
    <div class="box_pendingreport text-center">
        <?php 
        $coffee = get_the_post_thumbnail($custom_msg->ID, 'full', array('class' => 'coffee-on-top')); 
        if(!empty($coffee)): 
            echo '<div id="pending-loading" class="pending-spin"></div>' . $coffee; 
        endif; 
        if(!is_null($data)): ?>
        <div class="text-center"><?php echo $data; ?></div>
        <?php else: ?>
        <h2><?PHP echo __("Your report is being generated", 'seocheck') ?></h2>
        <h4><?PHP echo __("Our servers in each location are analyzing and building the report in real time", 'seocheck') ?></h4>
        <p class="text-center"><?PHP echo __("Usually this operation takes less than 1 minute.<br> Your report will be shown as soon as it is done.", 'seocheck') ?></p>
        <?php endif; 
        if(empty($coffee)): ?>
        <p class="text-center">
            <img src="<?PHP echo plugins_url(SAT_FOLDERNAME . '/images/ajax-loader.gif') ?>" alt="<?PHP echo __("Generating Report...", 'seocheck') ?>" />
            <br><small style="font-size: 10px"><?PHP #echo __("This page will refresh every 15 seconds until the report is done.", 'seocheck') ?></small>
        </p>
        <?php endif; ?>
    </div>
</div>
<script>
    var refreshreport = setInterval(checkreport, 10000);
    function checkreport() {
        $.ajax({
            url: '<?php echo plugins_url(SAT_FOLDERNAME . '/views/pending-status.php?id=' . $seocheck_reportobj->id); ?>',
            success: function(result) {                
                if( result == '0' || result == 0 ) {
                    clearInterval(refreshreport);
                    location.reload(true);
                }
            }
        });
    }
</script>