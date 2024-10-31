<?php
defined( 'ABSPATH' ) OR exit;
?>

<div class="wrap">

    <div style="float: left; min-width: 55%;">

        <h2 style="font-weight: bolder;">Pay Post By SMS - <?php _e("Instructions", "pay-post-by-sms"); ?></h2>
        
        <div style="margin: 25px; max-width: 700px; text-align: justify;">
            <p style="font-size: 1.3em;"><?php _e("Instructions-p#1", "pay-post-by-sms"); ?></p>
    
            <p style="font-size: 1.3em;"><?php _e("Instructions-p#2", "pay-post-by-sms"); ?></p>
            
            <p style="font-size: 1.3em;"><?php _e("Instructions-p#3", "pay-post-by-sms"); ?></p>
            
            <div style="padding: 25px; display: inline-block; text-align: center; margin: 0px auto; width: auto;">
                <p>
                <div style="float: left; padding-right: 10px;"><a href="//www.txtnation.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/txtnation.jpg" alt="TXT Nation" width="200" height="200" /></a></div>
                <div style="float: left; padding-right: 10px;"><a href="//www.centili.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/centili.png" alt="Centili" width="200" height="200" /></a></div>
                <div style="float: left; padding-right: 10px;"><a href="//fortumo.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/fortumo.png" alt="Fortumo" width="200" height="200" /></a></div>
                </p><p>
                <div style="float: left; padding-right: 10px;"><a href="//www.nth-mobile.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/nth-mobile.png" alt="NTH mobile" width="200" height="200" /></a></div>
                <div style="float: left; padding-right: 10px;"><a href="//www.mobivate.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/mobivate.png" alt="Mobivate" width="200" height="200" /></a></div>
                <div style="float: left; padding-right: 10px;"><a href="//www.clickatell.com/" target="_blank"><img src="<?php echo plugin_dir_url(__DIR__); ?>assets/img/clickatell.png" alt="Clickatell" width="200" height="200" /></a></div>
                </p>
            </div>
            
            <p style="font-size: 1.3em;"><?php _e("Instructions-p#4", "pay-post-by-sms"); ?></p>

            <ul style="list-style-type: circle; margin-left: 2em;">

                <li style="font-size: 1.3em; margin: 1em;"><?php printf(__("Instructions-li#1", "pay-post-by-sms"), admin_url( 'admin.php?page=sms-settings' )); ?></li>
    
                <li style="font-size: 1.3em; margin: 1em;"><?php printf(__("Instructions-li#2", "pay-post-by-sms"), admin_url( 'admin.php?page=api-url-settings' )); ?></li>
    
                <li style="font-size: 1.3em; margin: 1em;"><?php printf(__("Instructions-li#3", "pay-post-by-sms"), admin_url( 'admin.php?page=security-settings' )); ?></li>

            </ul>

        </div>
    </div>

    
    <div style="float: left; margin-left: 20px;">
        <iframe src="http://wp.online.rs/announcements/pay-post-by-sms/" id="pay-post-by-sms-plugin-announcements" title="Pay Post By SMS Plugin Announcements" scrolling="no" style="width: auto; height: auto; width: 310px; height: 225px; overflow: hidden;"></iframe>
        <p style="margin-top: 30px; margin-left: 10px; font-size: 4em; text-align: center;"><a href="http://wp.online.rs/contact-us/" target="_blank"><?php _e("Instructions-p#5", "pay-post-by-sms"); ?></a></p>
    </div>

</div>
