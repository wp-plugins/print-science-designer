jQuery(document).ready(function($) {
    
	
	jQuery.each(jQuery('.product_type_simple'), function() {
         if(jQuery(this).html()=='Personalize'){
		    jQuery(this).removeClass('add_to_cart_button').addClass('personalize');   
		 }
        });
	  
	jQuery('a.personalizep').click(function(){
		jQuery(this).attr('href');
		var host= jQuery("[name='host']").val();
		var url 	=	host+jQuery(this).attr('href');
		jQuery(this).bind('click'); 
		jQuery('#popup_frame').attr('src',url);
		//jQuery('#popup-wrapper').modalPopLite({openButton:'.personalizep',closeButton:'#close-btn', isModal: true });
	  
	});
	jQuery('button.personalizep').click(function(){
   	 var data_array=jQuery(".variations_form").serialize();
   	 var product_addon=jQuery(".addon").serialize();
	  var server_url= jQuery("[name='server_url']").val();	
	  if(data_array=='' && product_addon==''){
  	   var attcart= jQuery("[name='add-to-cart']").val();
	   var quantity= jQuery("[name='quantity']").val();	
	   data_array=  'add-to-cart='+attcart+'&quantity='+quantity; 
	 }
	 if(product_addon!='' && data_array == "" ){
	   var attcart= jQuery("[name='add-to-cart']").val();
	   var quantity= jQuery("[name='quantity']").val();	
	   data_array=  'add-to-cart='+attcart+'&quantity='+quantity+'&'+product_addon;
	 } 
	 if(product_addon=='' && data_array != "" ){
	   var attcart= jQuery("[name='add-to-cart']").val();
	   var quantity= jQuery("[name='quantity']").val();	
	   data_array=  'add-to-cart='+attcart+'&quantity='+quantity+'&'+data_array;
	 } 
	 if(product_addon!='' && data_array != "" ){
	   var attcart= jQuery("[name='add-to-cart']").val();
	   var quantity= jQuery("[name='quantity']").val();	
	   data_array=  'add-to-cart='+attcart+'&quantity='+quantity+'&'+data_array+'&'+product_addon;
	 } 
	 //var url= server_url+''+data_array;
         var url = "";
         var strfind = server_url.indexOf('?');
	 if(strfind > 0)
         {
             var querystr = url;
             url = server_url + "&" + data_array;
         }
         else
         {
           url = server_url + "?" + data_array;  
         }
         //alert(url);
         //alert(newstr);
	 jQuery(this).bind('click'); 
		jQuery('#popup_frame').attr('src',url);
		//jQuery('#popup-wrapper').modalPopLite({openButton:'.personalizep',closeButton:'#close-btn', isModal: true });
	   
	}); 
	
	
	//jQuery('.personalizep').trigger('click'); 
	
	//jQuery('.buttons_added:button').removeClass('single_add_to_cart_button');
	//alert(jQuery('.cart .button').html());
	 
});
function closethepopup(){
            //alert(jQuery('#server_url').val());
			window.parent.location.href=jQuery('#server_url').val();
			//window.parent.location.reload(true);
			jQuery('#popup_frame').attr("src", "");
			//jQuery('#close-btn').trigger('click');
	}
	
jQuery(function () {
	 var maskWidth = document.body.clientWidth;
	 var maskHeight = jQuery(window).height();
     var margin =  jQuery("[name='margin']").val();
     jQuery("#popup-wrapper").css("width",(maskWidth - (2*margin)));
	 jQuery("#popup-wrapper").css("height",(maskHeight - 2*margin));
	 jQuery("#popup_frame").css("width",(maskWidth - 2*margin));
	 jQuery("#popup_frame").css("height",(maskHeight - 2*margin));	 
	
	jQuery("#popup-wrapper").modalPopLite({openButton:".personalizep",closeButton:"#close-btn", isModal: true });
	 
 });


/*	jQuery(function (){
	
		var maskWidth = document.body.clientWidth;
		var maskHeight = jQuery(window).height();
		jQuery(".anchoreditlink").attr("target","popup_frame");
		jQuery(".personalize_btn_link").attr("target","popup_frame");
		jQuery('#popup-wrapper').modalPopLite({ openButton: '.personalizep', closeButton: '#close-btn', isModal: true });
	});	
	function closethepopup(){
		window.top.location.reload();
		
		
	}
	function setPersonalizeLocation(formObj) {
		jQuery("#popup-wrapper").css("display","block");
		if (jQuery(formObj).length > 0)
		{
			jQuery(formObj).attr("target","popup_frame");
			jQuery(formObj).submit();
		}	
		jQuery('.personalizep').trigger('click');
		return false;
	}
*/