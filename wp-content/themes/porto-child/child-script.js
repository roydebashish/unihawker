jQuery(document).ready(function($){
    $(document).on('click','#place_order', function(event){
        event.preventDefault();
       
       $.ajax({
            
            url: wp_ajax_obj.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'token_gen'
            },
            success: function (data) {
               if(data.verified == true){
                   $('form[name=checkout]').submit();
                   //alert('got verification');
               }else{
                    alert(data.msg);
               }
               
            },
            error: function (xmlHttpRequest,statusText,errThrown) {
                alert(errThrown);
                console.log(xmlHttpRequest);
            }
        });
    });
});