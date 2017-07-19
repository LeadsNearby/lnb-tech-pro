jQuery(document).ready(function(){
 
	//Admin Image loader Script
    var custom_uploader;

	jQuery('.upload_image_button').on( "click", function(e) {
 
	   var $el = jQuery(this);	
	   e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        //if (custom_uploader) {
          //  custom_uploader.open();
          //  return;
        //}
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
			$el.closest('p').find('.upload_image').val(attachment.url);		
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
	
	//Portfolio Scroller
	var triggers = jQuery('ul.triggers li');
	var images = jQuery('ul.images li');
	var lastElem = triggers.length-1;
	var target;

	triggers.first().addClass('selected');
	images.hide().first().show();

	function sliderResponse(target) {
		images.fadeOut(300).eq(target).fadeIn(300);
		triggers.removeClass('selected').eq(target).addClass('selected');
	}

	triggers.click(function() {
		if ( !jQuery(this).hasClass('selected') ) {
			target = jQuery(this).index();
			sliderResponse(target);
			resetTiming();
		}
	});
	jQuery('.next').click(function() {
		target = jQuery('ul.triggers li.selected').index();
		target === lastElem ? target = 0 : target = target+1;
		sliderResponse(target);
		resetTiming();
	});
	jQuery('.prev').click(function() {
		target = jQuery('ul.triggers li.selected').index();
		lastElem = triggers.length-1;
		target === 0 ? target = lastElem : target = target-1;
		sliderResponse(target);
		resetTiming();
	});
	function sliderTiming() {
		target = jQuery('ul.triggers li.selected').index();
		target === lastElem ? target = 0 : target = target+1;
		sliderResponse(target);
	}
	var timingRun = setInterval(function() { sliderTiming(); },5000);
	function resetTiming() {
		clearInterval(timingRun);
		timingRun = setInterval(function() { sliderTiming(); },5000);
	}	
	
	//Drawer slide
	jQuery('.sb_toggle').click(function() {
	jQuery(this).toggleClass('open');
	jQuery('#drawer').slideToggle('slow', function() {
		// Animation complete.
	  });
	});	
 
 
});