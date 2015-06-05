jQuery(document).ready(function($) {
	jQuery(window).load(function() {
		jQuery('.personalizationGallery').each(function(index, el) {
			var imgHeightList = [];
			
			jQuery(el).find('img').each(function(index, el) {	
				jQuery(el).css('display', 'block');
				imgHeightList.push(jQuery(el).height()); 
			});
			
			jQuery(el).data('tmpHeight', Math.max.apply(null, imgHeightList));
		});
		
		jQuery('.personalizationGallery').cycle({ 
	    fx:     'fade', 
	    speed:   300, 
	    timeout: 3000,
	    pause:   1
		});
			
		jQuery('.personalizationGallery').each(function(index, el) {
			jQuery(el).css('height', jQuery(el).data('tmpHeight'));
		});		
	})
})