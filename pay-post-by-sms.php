<?php

defined( 'ABSPATH' ) OR exit;

/*
 * Plugin Name: Pay Post By SMS
 * Description: Pay Post By SMS
 * Version: 1.2
 * Author: ONLINE Solutions Ltd.
 * Author URI: http://wp.online.rs
 * Plugin URI: http://wp.online.rs/plugins/pay-post-by-sms/
 * Text Domain: pay-post-by-sms
 * Domain Path: /languages
 * Copyright 2017  ONLINE Solutions Ltd. (email : solutions@online.rs)
 * License: GPL2
 *
 */


register_activation_hook(   __FILE__, array( 'payPostBySMS', 'activatePlugin' ) );
register_deactivation_hook( __FILE__, array( 'payPostBySMS', 'deactivatePlugin' ) );
register_uninstall_hook(    __FILE__, array( 'payPostBySMS', 'uninstallPlugin' ) );

add_action( 'plugins_loaded', array( 'payPostBySMS', 'init' ) );

	if(!class_exists('payPostBySMS'))  {

	    class payPostBySMS {

            protected static $instance;
            
            protected static $pluginDir;
            
            protected static $pluginURL;
            
            protected static $DEBUG = true;
            
            protected static $table_name = 'sms_payments';
            
            public $defaultApiUrlSettings;

            public $savedApiUrlSettings; 

            public $currentApiUrlSettings;

            public $api_url_settings_page = false;
            
            public $validator;


            public static function init()   {
                is_null( self::$instance ) AND self::$instance = new self;
                return self::$instance;
            }



            /**
	         * Construct the plugin object.
	         */
	        public function __construct()  {

	            add_action( 'admin_init', array(&$this, 'admin_init'));
				add_action( 'admin_menu', array(&$this, 'add_menu'));
                add_action( 'load-post.php', array(&$this, 'pay_post_by_sms_setup' ));
                add_action( 'load-post-new.php', array(&$this, 'pay_post_by_sms_setup' ));
                add_action( 'admin_post_nopriv_check_sms_code', array(&$this, 'check_sms_code' )); //admin_post_nopriv_check_sms_code
                add_action( 'admin_post_check_sms_code', array(&$this, 'check_sms_code' ));
                add_filter( 'the_content', array(&$this, 'restrict_content' ));
                add_action( 'rest_api_init', array(&$this, 'pay_post_by_sms_rest_api'));

                load_plugin_textdomain( 'pay-post-by-sms', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
                 
                $this->pluginDir = plugin_dir_path( __FILE__ );
                
                $this->pluginURL = plugin_dir_url( __FILE__ );
                
                wp_register_style( 'pay_post_by_sms_css', $this->pluginURL . 'assets/css/pay_post_by_sms.css' );

                wp_enqueue_style( 'pay_post_by_sms_css' );


                $this->savedApiUrlSettings = array(
                    'phone' => get_option('pay-post-by-sms-get-phone-key'),
                    'shortcode' => get_option('pay-post-by-sms-get-shortcode-key'),
                    'message' => get_option('pay-post-by-sms-get-message-key'),
                    'transactionid' => get_option('pay-post-by-sms-get-transactionid-key')
                );

                $this->defaultApiUrlSettings = array(
                    'phone' => 'phone',
                    'shortcode' => 'shortcode',
                    'message' => 'message',
                    'transactionid' => 'transactionid'
                );

                $this->currentApiUrlSettings = array(
                    'phone' => (isset($this->savedApiUrlSettings['phone']) && !empty($this->savedApiUrlSettings['phone'])) ? $this->savedApiUrlSettings['phone'] : $this->defaultApiUrlSettings['phone'],
                    'shortcode' => (isset($this->savedApiUrlSettings['shortcode']) && !empty($this->savedApiUrlSettings['shortcode'])) ? $this->savedApiUrlSettings['shortcode'] : $this->defaultApiUrlSettings['shortcode'],
                    'message' => (isset($this->savedApiUrlSettings['message']) && !empty($this->savedApiUrlSettings['message'])) ? $this->savedApiUrlSettings['message'] : $this->defaultApiUrlSettings['message'],
                    'transactionid' => (isset($this->savedApiUrlSettings['transactionid']) && !empty($this->savedApiUrlSettings['transactionid'])) ? $this->savedApiUrlSettings['transactionid'] : $this->defaultApiUrlSettings['transactionid']
                );

                include(sprintf("%s/utils/validator.php", dirname(__FILE__)));

                $this->validator = new payPostBySMSvalidator();
	        } 



            /**
             * Meta box setup function.
             */
            public function pay_post_by_sms_setup() {
            
              /**
               * Add meta boxes on the 'add_meta_boxes' hook.
               */
              add_action( 'add_meta_boxes', array(&$this, 'pay_post_by_sms_add_post_meta_boxes' ), 1);
              
              /**
               * Save post meta on the 'save_post' hook.
               */
              add_action( 'save_post', array(&$this, 'pay_post_by_sms_save_post_meta'), 10, 2);
            }



            /**
             * Create one or more meta boxes to be displayed on the post editor screen.
             */
            public function pay_post_by_sms_add_post_meta_boxes() {
                
                add_action( 'admin_notices', array(&$this, 'myAdminNotice'));                                
            
              add_meta_box(
                'pay-post-by-sms',                                  // Unique ID
                esc_html__( 'Pay Post By SMS', 'pay-post-by-sms' ), // Box Title
                array(&$this, 'pay_post_by_sms_meta_box'),          // Callback function
                'post',                                             // Admin page (or post type)
                'side',                                             // Context
                'high'                                              // Priority
              );
            }



            /**
             * Save the meta box's post metadata.
             */
            public function pay_post_by_sms_save_post_meta( $post_id, $post ) {
                
              /**
               * Verify the nonce before proceeding.
               */
              if ( !isset( $_POST['pay_post_by_sms_nonce'] ) || !wp_verify_nonce( $_POST['pay_post_by_sms_nonce'], basename( __FILE__ ) ) )
                return $post_id;

              /**
               * Get the post type object.
               */
              $post_type = get_post_type_object( $post->post_type );

              /**
               * Check if the current user has permission to edit the post.
               */
              if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
                return $post_id;

              /**
               * Get the posted data and sanitize it for use as an HTML class.
               */
              $new_meta_value = array(
                    "pay_post_by_sms_checkbox" => $_POST['pay_post_by_sms_checkbox']
              );


              if(!empty($this->validator->errors))
                $_SESSION["pay_post_by_sms_admin_errors"] = $this->validator->errors;

              /**
               * Get the meta key.
               */
              $meta_key = 'pay_post_by_sms';

              /**
               * Get the meta value of the custom field key.
               */
              $meta_value = get_post_meta( $post_id, $meta_key, true );
              
              /**
               * If a new meta value was added and there was no previous value, add it.
               */
              if ( $new_meta_value && '' == $meta_value )
                add_post_meta( $post_id, $meta_key, $new_meta_value, true );
            
              /**
               * If the new meta value does not match the old value, update it.
               */
              elseif ( $new_meta_value && $new_meta_value != $meta_value )
                update_post_meta( $post_id, $meta_key, $new_meta_value );
            
              /**
               * If there is no new meta value but an old value exists, delete it.
               */
              elseif ( '' == $new_meta_value && $meta_value )
                delete_post_meta( $post_id, $meta_key, $meta_value );

            }



            /**
             * Display the post meta box.
             */
            public function pay_post_by_sms_meta_box( $post ) {
                
                $metaData = get_post_meta( $post->ID, 'pay_post_by_sms', true );
                
                $pay_post_by_sms_checkbox_checked = isset($metaData["pay_post_by_sms_checkbox"]) ? "checked" : "";

                wp_nonce_field( basename( __FILE__ ), 'pay_post_by_sms_nonce' ); ?>
            
                  <p>
                    <label for="pay_post_by_sms_checkbox"><?php _e( "Restrict an access?", 'pay-post-by-sms' ); ?></label>&nbsp;<input class="widefat" type="checkbox" name="pay_post_by_sms_checkbox" id="pay_post_by_sms_checkbox" value="yes" <?php if ( isset ( $metaData['pay_post_by_sms_checkbox'] ) ) checked( $metaData['pay_post_by_sms_checkbox'], 'yes' ); ?> />
                  </p>
                  <p>
                    <label for="pay-post-by-sms-price"><?php _e( "Post price", 'pay-post-by-sms' ); ?></label>&nbsp;<input disabled="true" class="" type="text" name="pay-post-by-sms-price" id="pay-post-by-sms-price" value="<?php echo esc_attr(get_option('pay-post-by-sms-price')); ?>" size="5" />
                  </p>
                  <p>
                    <label for="pay-post-by-sms-shortcode"><?php _e( "Shortcode", 'pay-post-by-sms' ); ?></label>&nbsp;<input disabled="true" class="" type="text" name="pay-post-by-sms-shortcode" id="pay-post-by-sms-shortcode" value="<?php echo esc_attr(get_option('pay-post-by-sms-shortcode')); ?>" size="5" />
                  </p>
                  <p>
                    <label for="pay-post-by-sms-keyword"><?php _e( "SMS Keyword", 'pay-post-by-sms' ); ?></label>&nbsp;<input disabled="true" class="" type="text" name="pay-post-by-sms-keyword" id="pay-post-by-sms-keyword" value="<?php echo esc_attr(get_option('pay-post-by-sms-keyword')); ?>" size="10" />
                  </p>
                  <p>
                    <a href="http://wp.online.rs/plugins/pay-post-by-sms/" target="_blank"><?php _e("Settings per post or page are available in PRO version only!", "pay-post-by-sms"); ?></a>
                  </p>
                <?php
                
                add_action('admin_notices', array(&$this, 'myAdminNotice__error'));
            }



	        /**
	         * Activate the plugin.
	         */
	        public static function activatePlugin()  {

                if ( ! current_user_can( 'activate_plugins' ) )
                    return;

                $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

                check_admin_referer( "activate-plugin_{$plugin}" );
        
                # Uncomment the following line to see the function in action
                # exit( var_dump( $_GET ) );

            	global $wpdb;

            	$charset_collate = $wpdb->get_charset_collate();
                
            	$table_name = $wpdb->prefix . $this->table_name;

                $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                  transactionid varchar(12) NOT NULL,
                  timeanddate datetime NOT NULL,
                  websession varchar(256) NOT NULL,
                  phonenumber varchar(12) NOT NULL,
                  messagetxt varchar(160) NULL,
                  ticket varchar(12) NOT NULL,
                  PRIMARY KEY (transactionid),
                  KEY phonenumber (phonenumber)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";// $charset_collate;
                
            	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            	dbDelta( $sql );

	        } 



	        /**
	         * Deactivate the plugin.
	         */
	        public static function deactivatePlugin()  {

                if ( ! current_user_can( 'activate_plugins' ) )
                    return;

                $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

                check_admin_referer( "deactivate-plugin_{$plugin}" );
                
                # Uncomment the following line to see the function in action
                # exit( var_dump( $_GET ) );
	        } 



	        /**
	         * Uninstall the plugin.
	         */     
	        public static function uninstallPlugin()    {
	           
                if ( ! current_user_can( 'activate_plugins' ) )
                    return;

                check_admin_referer( 'bulk-plugins' );
        
                # Uncomment the following line to see the function in action
                # exit( var_dump( $_GET ) );
        
                global $wpdb;

                $table_name = $wpdb->prefix . $this->table_name;

                $sql = "DROP TABLE IF EXISTS $table_name;";

                $wpdb->query($sql);
                
                delete_option('pay_post_by_sms');
	        } 



	        public function admin_init()   {

    			/**
                 * Set up the settings for this plugin.
                 */
    			$this->init_settings();

                add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_custom_admin_js') );
                
                add_action( 'admin_print_scripts-'.$this->api_url_settings_page, array(&$this, 'api_url_settings_js_vars'));

                if(!empty($_SESSION["pay_post_by_sms_admin_errors"]))
                    add_action( 'admin_notices', array(&$this->validator, 'adminNotice'));

                $settings_errors = get_settings_errors( 'pay-post-by-sms-errors' );

                if(!empty($settings_errors))
                    add_action( 'admin_notices', array(&$this->validator, 'adminSettingsNotice'));

			} 



            /**
             * Register and enqueue a custom stylesheet in the WordPress admin.
             */
            public function enqueue_custom_admin_js() {
                
                $script_path = $this->pluginURL . 'assets/js/replace_get_params.js';
                
                wp_enqueue_script( 'replace_get_params_script', $script_path );
                
                wp_register_script( 'replace_get_params_script', $script_path ); //plugins_url( '/assets/js/replace_get_params.js' , dirname(__FILE__) )

            }



			private function init_settings()  {

    			/**
                 * Register the settings for this plugin.
                 * 
                 */
    			register_setting('payPostBySMS-sms', 'pay-post-by-sms-shortcode',              array(&$this->validator, 'validate_shortcode_admin_settings'));
    			register_setting('payPostBySMS-sms', 'pay-post-by-sms-keyword',                array(&$this->validator, 'validate_keyword_admin_settings'));
    			register_setting('payPostBySMS-sms', 'pay-post-by-sms-price',                  array(&$this->validator, 'validate_price_admin_settings'));
    			register_setting('payPostBySMS-api', 'pay-post-by-sms-get-phone-key',          array(&$this->validator, 'validate_get_phone_key_admin_settings'));
    			register_setting('payPostBySMS-api', 'pay-post-by-sms-get-shortcode-key',      array(&$this->validator, 'validate_get_shortcode_key_admin_settings'));
    			register_setting('payPostBySMS-api', 'pay-post-by-sms-get-message-key',        array(&$this->validator, 'validate_get_message_key_admin_settings'));
    			register_setting('payPostBySMS-api', 'pay-post-by-sms-get-transactionid-key',  array(&$this->validator, 'validate_get_transactionid_key_admin_settings'));
                
			}



			/**
 			* Add a menu.
 			*/     
			public function add_menu()   {

                $dashboardIcon = $this->pluginURL . "/assets/img/dashboard_icon.png";

                add_menu_page( 'Pay Post By SMS Settings', 'Pay Post By SMS', 'manage_options', 'payPostBySMS', array(&$this, 'pay_post_by_sms_instructions_page'), $dashboardIcon, 100 );
                add_submenu_page( 'payPostBySMS', 'Pay Post By SMS - Instructions', 'Instructions', 'manage_options', 'payPostBySMS', array(&$this, 'pay_post_by_sms_instructions_page'));
                add_submenu_page( 'payPostBySMS', 'Pay Post By SMS - Default SMS Settings', 'Default SMS Settings', 'manage_options', 'sms-settings', array(&$this, 'pay_post_by_sms_sms_settings_page'));
                $this->api_url_settings_page = add_submenu_page( 'payPostBySMS', 'Pay Post By SMS - REST API Settings', 'REST API Settings', 'manage_options', 'api-url-settings', array(&$this, 'pay_post_by_sms_api_url_settings_page'));
                add_submenu_page( 'payPostBySMS', 'Pay Post By SMS - Security Settings', 'Security Settings', 'manage_options', 'security-settings', array(&$this, 'pay_post_by_sms_security_settings_page'));

			} 



			/**
 			* Menu Callback.
 			*/     
			public function pay_post_by_sms_instructions_page()   {

    			/**
                 * Render the settings template.
                 */

    			include(sprintf("%s/admin/instructions.php", dirname(__FILE__)));

			}



			public function pay_post_by_sms_sms_settings_page()   {

    			if(!current_user_can('manage_options'))  {

        			wp_die(__("You do not have sufficient permissions to access this page.", 'pay-post-by-sms'));

    			}
                
    			/**
                 * Render the settings template.
                 */

    			include(sprintf("%s/admin/sms_settings.php", dirname(__FILE__)));

			}



			public function pay_post_by_sms_api_url_settings_page()   {

    			if(!current_user_can('manage_options'))  {

        			wp_die(__("You do not have sufficient permissions to access this page.", 'pay-post-by-sms'));

    			}
                
                $REST_API_URL = site_url('/wp-json/pay_post_by_sms/');

    			/**
                 * Render the settings template.
                 */
    			include(sprintf("%s/admin/api_url_settings.php", dirname(__FILE__)));

			}



			public function pay_post_by_sms_security_settings_page()   {

    			if(!current_user_can('manage_options'))  {

        			wp_die(__("You do not have sufficient permissions to access this page.", 'pay-post-by-sms'));

    			}

    			/**
                 * Render the settings template.
                 */

    			include(sprintf("%s/admin/security_settings.php", dirname(__FILE__)));

			}



            public function api_url_settings_js_vars(){
            	echo "<script type='text/javascript'>\n";
            	echo 'var currentURLSettings = ' . wp_json_encode( $this->currentApiUrlSettings ) . ';';//
            	echo "\n</script>";
            }



            /**
             * Check received SMS code
             * and return the protected content
             * or display an error.
             */
            public function check_sms_code() {

                global $wpdb;
 
                $SESSIONID = session_id();

                $contentID = $this->validator->validate('postID', $_POST["postid"]);
                
                $ticket = $this->validator->validate('authcode', $_POST["authcode"]);
                
                /**
                 * Check the received code
                 * query database for a record
                 * with the sms code and session id.
                 * 
                 * If it's found, set session variable 'unlocked' to true
                 * otherwise, set it to false.
                 */
                
                if(!empty($contentID))   {
                    
                    if(!empty($ticket)) {
                        
                        $table_name = $wpdb->prefix . $this->table_name;

                        $sql = "SELECT * FROM " . $table_name . " WHERE ticket = '$ticket' AND websession = ''";
        
                        $dbAuthcode = $wpdb->get_row($sql);
        
                        if($dbAuthcode->ticket === $ticket) {
                           
                           /**
                            * Update database record
                            * with session id and paid flag.
                            */
                            $dbUpdate = $wpdb->update($table_name, array( 'websession' => $SESSIONID), array( 'ticket' => $ticket));

                            $_SESSION["unlocked"][$contentID] = true;
                        }
                        else
                            $_SESSION["pay_post_by_sms_error"] = __( "Submitted access code is invalid", 'pay-post-by-sms' );
                    }
                    else
                        $_SESSION["pay_post_by_sms_error"] = __( "No access code provided", 'pay-post-by-sms' );

                    wp_redirect( get_permalink($contentID));

                }
                else
                    wp_redirect("/");
            
            }



        	private function plugin_settings_link($links)  { 
    
            	$settings_link = '<a href="options-general.php?page=payPostBySMS">Settings</a>'; 
    
            	array_unshift($links, $settings_link); 
    
            	return $links; 
    
        	}



            public function restrict_content( $content ) {

                global $post;

                /**
                 * Check if we're inside the main loop in a single post page.
                 */
                if ( is_single() && in_the_loop() && is_main_query() ) {
                    
                    $metaData = get_post_meta( $post->ID, 'pay_post_by_sms', true );
                 
                    $short_number = get_option( 'pay-post-by-sms-shortcode');    
                    $keyword = get_option( 'pay-post-by-sms-keyword');    
                    $price = get_option( 'pay-post-by-sms-price');    

                    if(($metaData['pay_post_by_sms_checkbox'] == 'yes' && !empty($short_number) && !empty($keyword) && !empty($price)) && $_SESSION["unlocked"][$post->ID] == false)  {
                        
                        $postThumbnail = get_the_post_thumbnail( $post->ID, $size = 'medium' );
                        
                        $content = file_get_contents ("$this->pluginDir/templates/replaced_content.tpl");

                        if(isset($_SESSION["pay_post_by_sms_error"]) && !empty($_SESSION["pay_post_by_sms_error"])) {

                            $errorBox = file_get_contents ("$this->pluginDir/templates/error_message.tpl");

                            if(!$errorBox)
                                $errorBox = "<div style=\"color: red; font-weight: bold; text-align: center; padding: 1.5em; border: red 1px solid;\">" . __("Could not display an error! <br />", 'pay-post-by-sms') . "</div>";
                            else
                                $errorBox = preg_replace('|{PAY_BY_SMS_POST_ERROR}|', $_SESSION["pay_post_by_sms_error"], $errorBox);

                            unset($_SESSION["pay_post_by_sms_error"]);
                        }
                        else
                            $errorBox = '';

                        $contentKeys = array(
                                            '|{PAY_BY_SMS_POST_TITLE}|',
                                            '|{PAY_BY_SMS_POST_THUMBNAIL}|',
                                            '|{PAY_BY_SMS_POST_EXCERPT}|',
                                            '|{PAY_BY_SMS_TICKET_IMG_SRC}|',
                                            '|{PAY_BY_SMS_POST_ERROR_BOX}|',
                                            '|{PAY_BY_SMS_BUY_TICKET_INFO}|',
                                            '|{PAY_BY_SMS_FORM_ACTION_URL}|',
                                            '|{PAY_BY_SMS_FORM_POST_ID}|',
                                            '|{PAY_BY_SMS_FORM_AUTHCODE_PLACEHOLDER}|',
                                            '|{PAY_BY_SMS_FORM_BUTTON}|'
                                        );

                        $contentValues = array(
                                            $title,
                                            $postThumbnail,
                                            $post->post_excerpt,
                                            $this->pluginURL . "/assets/img/ticket.png",
                                            $errorBox,
                                            sprintf(__( "To view this content, you need to buy an access code by sending an SMS with text %s to %s. Access code price is %s.", 'pay-post-by-sms' ), $keyword, $short_number, $price) . " " . __( "After receiving an access code via SMS, please enter it in the following field.", 'pay-post-by-sms' ),
                                            esc_url( admin_url('admin-post.php') ),
                                            $post->ID,
                                            __( "Enter received code", 'pay-post-by-sms' ),
                                            __( "Enter", 'pay-post-by-sms' )
                                        );

                        if(!$content)
                            $content = "<div style=\"color: red; font-weight: bold; text-align: center; padding: 1.5em; border: red 1px solid;\">" . __("Error opening template file.", 'pay-post-by-sms') . "</div>";
                        else
                            $content = preg_replace($contentKeys, $contentValues, $content);

                    }
                }

                if($this->DEBUG === true)
                    unset($_SESSION["unlocked"]);
                
                return $content;
            }
            
            
            /**
             * Create a REST API.
             * 
             */
            public function pay_post_by_sms_rest_api() {

                /**
                 * Premium SMS service provider
                 * URL variables mapping.
                 */
                $requestString  =   '/'.$this->currentApiUrlSettings['phone'].'/(?P<phone>\d+)'.
                                    '/'.$this->currentApiUrlSettings['shortcode'].'/(?P<shortcode>\d+)'.
                                    '/'.$this->currentApiUrlSettings['message'].'/(?P<message>\S+)'.
                                    '/'.$this->currentApiUrlSettings['transactionid'].'/(?P<transactionid>\S+)';
                                    
                register_rest_route( 'pay_post_by_sms', $requestString, array(
                    'methods' => 'GET',
                    'callback' => array(&$this, 'pay_post_by_sms_api'),
                    )
                );
            }
            
            public function pay_post_by_sms_api(WP_REST_Request $request) {
                
                /**
                 * Validate & sanitaze request data.
                 */
                // 
                $requestData['phone'] = $this->validator->validate('phone', $requestData['phone']);
                $requestData['transactionid'] = $this->validator->validate('transactionid', $requestData['transactionid']);
                $requestData['shortcode'] = $this->validator->validate('shortcode', $requestData['shortcode']);
                $requestData['message'] = $this->validator->validate('message', $requestData['message']);

                include(sprintf("%s/api/rest.php", dirname(__FILE__)));

                $payPostBySMSapi = new payPostBySMSapi();
                
                $response = $payPostBySMSapi->processRequest($request->get_params());

                return $response;
            }

	    }

	} 
