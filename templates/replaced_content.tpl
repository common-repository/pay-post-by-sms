<div style="display: inline-block;"><h1>{PAY_BY_SMS_POST_TITLE}</h1></div>
    <div style="display: inline-block;"><strong></strong>
        <div style="float: left;">{PAY_BY_SMS_POST_THUMBNAIL}</div>
        <div style="float: left; margin-left: 20px; width: 80%">
        {PAY_BY_SMS_POST_EXCERPT}<p>
        <img src="{PAY_BY_SMS_TICKET_IMG_SRC}" width="200" height="200" align="left" />
        <div style="float: left; padding-left: 25px; max-width: 50%; text-align: justify;">
            {PAY_BY_SMS_POST_ERROR_BOX}
        {PAY_BY_SMS_BUY_TICKET_INFO}
            <div style="margin-top: 20px;">
                <form action="{PAY_BY_SMS_FORM_ACTION_URL}" method="post">
                    <input type="hidden" name="action" value="check_sms_code" />
                    <input type="hidden" name="postid" value="{PAY_BY_SMS_FORM_POST_ID}" />
                    <input type="text" name="authcode" placeholder="{PAY_BY_SMS_FORM_AUTHCODE_PLACEHOLDER}" />
                    <input type="submit" value="{PAY_BY_SMS_FORM_BUTTON}" />
                </form>
            </div>
        </div>
    </div> 
</div>
