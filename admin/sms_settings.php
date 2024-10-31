<?php
defined( 'ABSPATH' ) OR exit;
?>



<div class="wrap">

    <h2 style="font-weight: bolder;">Pay Post By SMS <?php _e("Settings", "pay-post-by-sms"); ?></h2>
    
    

    <form method="post" action="options.php"> 

        <?php @settings_fields('payPostBySMS-sms'); ?>

        <?php @do_settings_fields('payPostBySMS-sms'); ?>
        
        <?php wp_nonce_field( basename( __FILE__ ), 'pay_post_by_sms_nonce' ); ?>

        <table class="form-table" style="width: auto;">  

            <tr valign="top">

                <td colspan="2"><span style="font-weight: bold; font-size: 1.2em; margin-top: 3em;"><?php _e("Default SMS settings for all posts", "pay-post-by-sms"); ?></span><br /><span style="font-style: italic;"><?php _e("Settings that will be applied to all protected posts if not set individually.", "pay-post-by-sms"); ?><br /><?php _e("These are defined by premium SMS provider.", "pay-post-by-sms"); ?></span></td>

            </tr>

            <tr valign="top">

                <td><label for="pay-post-by-sms-shortcode"><?php _e("Shortcode", "pay-post-by-sms"); ?></label> <a href="#" onclick="alert('<?php _e("Number to which someone sends a Keyword in order to buy an access code.", "pay-post-by-sms"); ?>'); return false;" tabindex="-1">[?]</a></td>

                <td><input type="text" name="pay-post-by-sms-shortcode" id="pay-post-by-sms-shortcode" value="<?php echo get_option('pay-post-by-sms-shortcode'); ?>" placeholder="<?php _e("E.g. 8585", "pay-post-by-sms"); ?>" /></td>

            </tr>

            <tr valign="top">

                <td scope="row"><label for="pay-post-by-sms-keyword"><?php _e("Keyword", "pay-post-by-sms"); ?></label> <a href="#" onclick="alert('<?php _e("An SMS text to be sent to the Short Number.", "pay-post-by-sms"); ?>'); return false;" tabindex="-1">[?]</a></td>

                <td><input type="text" name="pay-post-by-sms-keyword" id="pay-post-by-sms-keyword" value="<?php echo get_option('pay-post-by-sms-keyword'); ?>" placeholder="<?php _e("E.g. TICKET", "pay-post-by-sms"); ?>" /></td>

            </tr>

			<tr valign="top">

                <td scope="row"><label for="pay-post-by-sms-price"><?php _e("SMS Price", "pay-post-by-sms"); ?></label> <a href="#" onclick="alert('<?php _e("A price someone will be charged when sending a Keyword to the Short Number.", "pay-post-by-sms"); ?>'); return false;" tabindex="-1">[?]</a></td>

                <td><input type="text" name="pay-post-by-sms-price" id="pay-post-by-sms-price" value="<?php echo get_option('pay-post-by-sms-price'); ?>" placeholder="<?php _e("E.g. $1.50", "pay-post-by-sms"); ?>" /></td>

            </tr>

        </table>

        <?php @submit_button(); ?>

    </form>

    <br />

</div>

