<?php

defined( 'ABSPATH' ) OR exit;

class payPostBySMSvalidator extends payPostBySMS {


    /**
     * Stores input errors.
     */
    public $errors = array();

    /**
     * Required input variable formats.
     */
    private static $formats;



    public function __construct()   {
        
        $this->formats = array(
            'keyword'       =>  array('[A-Za-z0-9\s]{2,}',                  'sanitize_text_field',  __("Keyword", 'pay-post-by-sms')),
            'message'       =>  array('[A-Za-z0-9\s]{2,}',                  'sanitize_text_field',  __("Message content", 'pay-post-by-sms')),
            'price'         =>  array('(\D*)\s*([\d,\.]+)\s*(\D*)',         'sanitize_text_field',  __("Price", 'pay-post-by-sms')),
            'shortcode'     =>  array('[0-9]{4,6}',                         'sanitize_text_field',  __("Shortcode", 'pay-post-by-sms')),
            'phone'         =>  array('[\+0-9]{12,15}',                     'sanitize_key',         __("Phone number", 'pay-post-by-sms')),
            'authcode'      =>  array('[a-z0-9]{6}',                        'sanitize_text_field',  __("Acess code", 'pay-post-by-sms')),  /** Related setting: file: /api/rest.php, variable $token_length */
            'postID'        =>  array('\d{1,}',                             'sanitize_key',         __("Post ID is invalid", 'pay-post-by-sms')),

            /**
             * Plugin settings variables.
             */
    		'get-phone-key'           =>  array('[a-z]{1,}',                          'sanitize_key',         __("Phone key name", 'pay-post-by-sms')),
    		'get-shortcode-key'       =>  array('[a-z]{1,}',                          'sanitize_key',         __("Shortcode key name", 'pay-post-by-sms')),
    		'get-message-key'         =>  array('[a-z]{1,}',                          'sanitize_key',         __("Message key name", 'pay-post-by-sms')),
    		'get-transactionid-key'   =>  array('[a-z]{1,}',                          'sanitize_key',         __("Transaction ID key name", 'pay-post-by-sms'))
        );
        
    }



    public function validate($inputKey, $inputValue)  {
        
        if(!empty($inputValue) && isset($inputValue))   {
        
            $regex = '|^'.$this->formats[$inputKey][0].'$|';
            
            $inputValue = $this->sanitize($inputValue, $this->formats[$inputKey][1]);
            
            if (preg_match($regex, $inputValue) === 1)    {
                return $inputValue;
            }
            else    {
    
                array_push($this->errors, $inputKey); 
                
                return '';
            }
        }
        else
            return $inputValue;
    }



    private function validate_settings($key, $value)  {
        
        $value = $this->validate($key, $value); 
        
        if (in_array($key, $this->errors))   {

        	add_settings_error(
        		'pay-post-by-sms-errors',
        		'ppbsms',
        		$this->formats[$key][2],
        		'error'
        	);
        }
        
        settings_errors('pay-post-by-sms-errors', false, true);
        
        return $value;
        
    }



    public function validate_shortcode_admin_settings($settingsData)    {
        
        return $this->validate_settings('shortcode', $settingsData);
        
    }



    public function validate_keyword_admin_settings($settingsData)    {
        
        return $this->validate_settings('keyword', $settingsData);
        
    }



    public function validate_price_admin_settings($settingsData)    {
        
        return $this->validate_settings('price', $settingsData);
        
    }



    public function validate_get_phone_key_admin_settings($settingsData)    {
        
        return $this->validate_settings('get-phone-key', $settingsData);
        
    }



    public function validate_get_shortcode_key_admin_settings($settingsData)    {
        
        return $this->validate_settings('get-shortcode-key', $settingsData);
        
    }



    public function validate_get_message_key_admin_settings($settingsData)    {
        
        return $this->validate_settings('get-message-key', $settingsData);
        
    }



    public function validate_get_transactionid_key_admin_settings($settingsData)    {
        
        return $this->validate_settings('get-transactionid-key', $settingsData);
        
    }



    public function sanitize($inputValue, $callback)  {
        
        eval('$sanitizedValue = ' . $callback . "('$inputValue')" . ';');
        
        return $sanitizedValue;
    }
    

    public function adminNotice(){
        
        if(is_array($_SESSION["pay_post_by_sms_admin_errors"]) && !empty($_SESSION["pay_post_by_sms_admin_errors"]))
            foreach($_SESSION["pay_post_by_sms_admin_errors"] as $errorKey)
                $errorMessages[] = $this->formats[$errorKey][2];
        
        print '<div class="error notice is-dismissible"><p>' . __("Following Pay Post By SMS settings are invalid: <strong>", 'pay-post-by-sms') . join(", ", $errorMessages) . '</strong></p></div>';
        
        unset($_SESSION["pay_post_by_sms_admin_errors"]);
    
        remove_action('admin_notices', 'adminNotice');
    } 



    public function adminSettingsNotice(){
        
        $settings_errors = get_settings_errors( 'pay-post-by-sms-errors' );
        
        if(is_array($settings_errors) && !empty($settings_errors))
            foreach($settings_errors as $error => $attributes)
                $errorMessages[] = $attributes['message'];
        
        print '<div class="error notice is-dismissible"><p>' . __("Error! Following settings have invalid values: <strong>", 'pay-post-by-sms') . join(", ", $errorMessages) . '</strong></p></div>';

        remove_action('admin_notices', 'adminSettingsNotice');
    } 

}
?>