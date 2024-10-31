<?php

defined( 'ABSPATH' ) OR exit;

class payPostBySMSapi extends payPostBySMS   { 

    /**
     * Constants definitions
     */
    
    private $token_length = 6;
    
    private $SMS_SIGNATURE = '';
    
    private $STATUS_SUCCESS = 'REPLY';
    
    private $STATUS_ERROR = 'ERROR';
    
    private $REST_LOG_FILE;
    
    public function __construct()   {

        $this->REST_LOG_FILE = parent::$pluginDir . '/log/rest.log';

    }


    /**
     * 
     * Process GET HTTP request
     * 
     */
    public function processRequest($requestData)    {
        
        global $wpdb;

        /**
         * Data is already validated
         * in main class.
         */
        $PHONE = $requestData['phone'];
        $TID = $requestData['transactionid'];
        $SHORTCODE = $requestData['shortcode'];
        $MESSAGE = $requestData['message'];
        
        $STATUS = '';
        $SMS = '';

        if($PHONE && preg_match("~\d{9,12}~", $PHONE))  {
        
        	$RespID = $this->LogRequest($REST_LOG_FILE, $PHONE, $TID, $MESSAGE);
            
            if(!empty($TID))    {
        
                $SMS_TICKET = $this->getToken();
                
                $table_name = $wpdb->prefix . parent::$table_name;
        
                $sql =  $wpdb->prepare("INSERT INTO `$table_name` (`transactionid`,`timeanddate`,`phonenumber`,`messagetxt`,`ticket`) values ('$TID', NOW(), '$PHONE', '$MESSAGE', '$SMS_TICKET')");

                $insertSMS = $wpdb->query($sql);
                
            	if($insertSMS)
            	{
            
            		$SMS = sprintf(__("Your SMS ticket: %s.", 'pay-post-by-sms'), $SMS_TICKET);
            
            		$STATUS = $this->STATUS_SUCCESS;
            	}
            	else
            	{
            		$SMS = __("An error occurred while generating SMS ticket.", 'pay-post-by-sms');
        
                    if(parent::$DEBUG === true)
                        $SMS .= " ($wpdb->last_error)";
                    $SMS .= __(" You were not charged for this message.", 'pay-post-by-sms');
            
            		$STATUS = $this->STATUS_ERROR;
            	}
            }
        	else
        	{
        		$SMS = __("Bad request. Please try again.", 'pay-post-by-sms') . __(" You were not charged for this message.", 'pay-post-by-sms');
        
        		$STATUS = $this->STATUS_ERROR;
        	}
        
        }
        else
        {
        	$SMS = __("Bad request. Please try again.", 'pay-post-by-sms') . __(" You were not charged for this message.", 'pay-post-by-sms');
        
        	$STATUS = $this->STATUS_ERROR;
        }
        
        $this->LogResponse($REST_LOG_FILE, $PHONE, $TID, $SMS);
        
        $SMS .= $this->SMS_SIGNATURE;
        
        return $SMS;

    }

    #############################################################################################################################################################################################
    
    
    private function getToken(){
         $token = "";
         $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
         $codeAlphabet.= "0123456789";
         $max = strlen($codeAlphabet); // edited
    
        for ($i=0; $i < $this->token_length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }
    
        return strtoupper($token);
    }
    
    
    private function LogRequest($REST_LOG_FILE, $PHONE, $TID, $MESSAGE)   {
    
        $timedate = date("d.m.Y. H:i:s");
        
        $logString = "Request, $timedate, $PHONE, $TID, $MESSAGE\r\n";
    
        @file_put_contents($REST_LOG_FILE, $logString, FILE_APPEND);
        
    }
    
    
    private function LogResponse($REST_LOG_FILE, $PHONE, $TID, $SMS, $STATUS)   {
    
        $timedate = date("d.m.Y. H:i:s");
        
        $logString = "Response, $timedate, $TID, $PHONE, $SMS\r\n";
    
        @file_put_contents($REST_LOG_FILE, $logString, FILE_APPEND);
    
    }

}

?>
