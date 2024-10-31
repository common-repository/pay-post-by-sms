<?php
defined( 'ABSPATH' ) OR exit;
?>

<div class="wrap">

    <h2 style="font-weight: bolder;">Pay Post By SMS <?php _e("Settings", "pay-post-by-sms"); ?></h2>
    
    

    <form method="post" action="options.php"> 

        <?php @settings_fields('payPostBySMS-api'); ?>

        <?php @do_settings_fields('payPostBySMS-api'); ?>
        
        <?php wp_nonce_field( basename( __FILE__ ), 'pay_post_by_sms_nonce' ); ?>

        <table class="form-table" style="width: auto;">  

            <tr valign="top">

                <td colspan="2"><div style="font-weight: bold; font-size: 1.2em; margin-top: 3em;"><?php _e("REST API Settings", "pay-post-by-sms"); ?></div></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><span style="font-weight: bold;"><?php _e("REST API URL", "pay-post-by-sms"); ?></span><br /><span style="font-style: italic;"><?php _e("URL to which your premium SMS service provider will send data about purchased access code.", "pay-post-by-sms"); ?></span></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><code><?php echo $REST_API_URL; ?></code></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><span style="font-weight: bold;"><?php _e("GET Parameters Mapping", "pay-post-by-sms"); ?></span><br /><span style="font-style: italic;"><?php _e("Map premium SMS service provider GET parameters to local. Leave blank for default.", "pay-post-by-sms"); ?></span></td>

            </tr>

            <tr valign="top">

                <td style="font-weight: bold;">Local</td>

                <td style="font-weight: bold;">Provider</td>

            </tr>

            <tr valign="top">

                <td style="width: 6em;"><?php echo $this->defaultApiUrlSettings['phone']; ?></td>

                <td><input style="width: 6em;" type="text" name="pay-post-by-sms-get-phone-key" id="pay-post-by-sms-get-phone-key" value="<?php echo $this->currentApiUrlSettings['phone']; ?>" onchange="replaceGetParams('phone', this.value == undefined ? '' : this.value);" /></td>

            </tr>

            <tr valign="top">

                <td style="width: 6em;"><?php echo $this->defaultApiUrlSettings['shortcode']; ?></td>

                <td><input style="width: 6em;" type="text" name="pay-post-by-sms-get-shortcode-key" id="pay-post-by-sms-get-shortcode-key" value="<?php echo $this->currentApiUrlSettings['shortcode']; ?>" onchange="replaceGetParams('shortcode', this.value == undefined ? '' : this.value);" /></td>

            </tr>

            <tr valign="top">

                <td style="width: 6em;"><?php echo $this->defaultApiUrlSettings['message']; ?></td>

                <td><input style="width: 6em;" type="text" name="pay-post-by-sms-get-message-key" id="pay-post-by-sms-get-message-key" value="<?php echo $this->currentApiUrlSettings['message']; ?>" onchange="replaceGetParams('message', this.value == undefined ? '' : this.value)" /></td>

            </tr>

            <tr valign="top">

                <td style="width: 6em;"><?php echo $this->defaultApiUrlSettings['transactionid']; ?></td>

                <td><input style="width: 6em;" type="text" name="pay-post-by-sms-get-transactionid-key" id="pay-post-by-sms-get-transactionid-key" value="<?php echo $this->currentApiUrlSettings['transactionid']; ?>" onchange="replaceGetParams('transactionid', this.value == undefined ? '' : this.value);" /></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><strong><?php _e("Default URL query string", "pay-post-by-sms"); ?></strong>: <code><?php echo $REST_API_URL; ?>phone/12304567890/shortcode/8855/message/TICKET/transactionid/hgjd66f8hjjy</code></td>

            </tr>

            <tr valign="top">

                <td colspan="2"><strong><?php _e("New URL query string", "pay-post-by-sms"); ?></strong>: <code id="newURL"><?php echo $REST_API_URL; ?><strong><?php echo $this->currentApiUrlSettings['phone']; ?></strong>/12304567890/<strong><?php echo $this->currentApiUrlSettings['shortcode']; ?></strong>/8855/<strong><?php echo $this->currentApiUrlSettings['message']; ?></strong>/TICKET/<strong><?php echo $this->currentApiUrlSettings['transactionid']; ?></strong>/hgjd66f8hjjy</code></td>

            </tr>

        </table>

        <?php @submit_button(); ?>

    </form>

    <br />

</div>


