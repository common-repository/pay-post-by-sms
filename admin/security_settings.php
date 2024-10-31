<?php
defined( 'ABSPATH' ) OR exit;
?>


<div class="wrap">

    <h2 style="font-weight: bolder;">Pay Post By SMS <?php _e("Settings", "pay-post-by-sms"); ?></h2>
    
    

        <table class="form-table" style="width: auto;">  

            <tr valign="top">

                <td colspan="2"><div style="font-weight: bold; font-size: 1.2em; margin-top: 3em;"><?php _e("Security Settings", "pay-post-by-sms"); ?></div></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><span style="font-weight: bold;"><?php _e("Allow access only from specific IP address", "pay-post-by-sms"); ?></span><br /><span style="font-style: italic;"><?php _e("IP address of Premium SMS service provider's server.", "pay-post-by-sms"); ?></span></td>

            </tr>

            <tr valign="top">

                <td style="width: 10em;">Allowed IP address <a href="#" onclick="alert('<?php _e("IP address of Premium SMS service provider\'s server.", "pay-post-by-sms"); ?>'); return false;" tabindex="-1">[?]</a></td>

                <td><input disabled="true" style="width: 8em;" type="text" name="pay-post-by-sms-allowed-ip-address" id="pay-post-by-sms-allowed-ip-address" value="<?php echo get_option('pay-post-by-sms-allowed-ip-address'); ?>" placeholder="<?php _e("E.g. 123.45.67.8", "pay-post-by-sms"); ?>" /></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><a href="http://wp.online.rs/plugins/pay-post-by-sms/" target="_blank"><?php _e("Available in PRO version only!", "pay-post-by-sms"); ?></a></td>

            </tr>

        </table>

    <br />

</div>

