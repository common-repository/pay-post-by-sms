    function replaceGetParams(defaultKey, newKey)   {
        
        if(newKey == "")
            newKey = defaultKey;
        
        var URL = document.getElementById("newURL");

        var oldURL = URL.innerHTML;

        var matchChars = '/<strong>'+currentURLSettings[defaultKey]+'<\\/strong>/g';
        
        var newURL = oldURL.replace(eval(matchChars), '<strong>'+newKey+'</strong>');
        
        URL.innerHTML = newURL;
    
        currentURLSettings[defaultKey] = newKey;
        
    }
