var parent = document.getElementById('certification-container');

(function () {
  var deleteButtons = document.getElementsByClassName('delete-row');

  console.log(deleteButtons.length);

  if (deleteButtons.length) {
    for (i = 0; i < deleteButtons.length; i++) {
      deleteButtons[i].addEventListener('click', function () {
        var row = document.getElementById(
          'certification_' + this.dataset.index
        );
        row.parentNode.removeChild(row);
      });
    }
  }
})();

function new_certification_container() {
  var currentCount = parent.children.length;
  var newCount = currentCount + 1;

  var node = document.createElement('div');
  node.id = 'certification_' + newCount;
  node.className = 'tech-profiles-meta-field';

  var closeSpan = document.createElement('span');
  closeSpan.className = 'delete-row dashicons dashicons-no-alt';
  closeSpan.addEventListener('click', function () {
    parent.removeChild(node);
  });

  var certName = document.createElement('input');
  certName.name = 'tech_profile_meta[certifications][' + newCount + '][name]';
  certName.type = 'text';

  var certImage = document.createElement('input');
  certImage.name = 'tech_profile_meta[certifications][' + newCount + '][image]';
  certImage.type = 'hidden';
  certImage.value = '';

  var certImagePlaceholder = document.createElement('img');
  certImagePlaceholder.src = '#';

  var certImageSelect = document.createElement('a');
  certImageSelect.id = 'image_selector_' + newCount;
  certImageSelect.href = '#';
  // certImageSelect.onclick = function() { alert('You clicked select for cert image' + newCount ) };
  certImageSelect.appendChild(document.createTextNode('Select image'));

  // var spacer = document.createElement('p');
  // var spacerText = document.createTextNode('Container ' + ( currentCount + 1 ) );

  // spacer.appendChild( spacerText );

  node.appendChild(closeSpan);
  node.appendChild(certName);
  node.appendChild(certImage);
  node.appendChild(certImagePlaceholder);
  node.appendChild(certImageSelect);

  parent.appendChild(node);
}

jQuery(document).ready(function ($) {
  function switchTabs(tab) {
    $('.lnb-tabs')
      .find('[data-tab=' + tab + ']')
      .addClass('active')
      .siblings()
      .removeClass('active');
    // $(this).addClass('active').siblings().removeClass('active');
    // $(tab).addClass('active').siblings().removeClass('active');
    sessionStorage.setItem('currentTab', tab);
  }

  if (sessionStorage.getItem('currentTab')) {
    switchTabs(sessionStorage.getItem('currentTab'));
  }

  $('.tab-nav-item').click(function (e) {
    e.preventDefault();
    switchTabs(this.dataset.tab);
  });

  //Admin Image loader Script
  var custom_uploader;

  jQuery('.upload_image_button').on('click', function (e) {
    e.preventDefault();

    var selector = jQuery(this);
    var imageField = $('.lnb-tabs').find(
      '[data-image=' + $(selector).data('selector') + ']'
    );
    var imageThumb = $('.lnb-tabs').find(
      '[data-image-placeholder=' + $(selector).data('selector') + ']'
    );
    //If the uploader object has already been created, reopen the dialog
    if (custom_uploader) {
      custom_uploader.open();
      return;
    }

    //Extend the wp.media object
    custom_uploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
        text: 'Choose Image',
      },
      multiple: false,
    });

    //When a file is selected, grab the URL and set it as the text field's value
    custom_uploader.on('select', function () {
      var media = custom_uploader.state().get('selection').first().toJSON();
      console.log(media);
      imageField.val(media.id);
      imageThumb.attr('src', media.sizes.thumbnail.url);

      // $el.closest('p').find('.upload_image').val(attachment.url);
    });

    //Open the uploader dialog
    custom_uploader.open();
  });

  // //Portfolio Scroller
  // var triggers = jQuery('ul.triggers li');
  // var images = jQuery('ul.images li');
  // var lastElem = triggers.length-1;
  // var target;

  // triggers.first().addClass('selected');
  // images.hide().first().show();

  // function sliderResponse(target) {
  // 	images.fadeOut(300).eq(target).fadeIn(300);
  // 	triggers.removeClass('selected').eq(target).addClass('selected');
  // }

  // triggers.click(function() {
  // 	if ( !jQuery(this).hasClass('selected') ) {
  // 		target = jQuery(this).index();
  // 		sliderResponse(target);
  // 		resetTiming();
  // 	}
  // });
  // jQuery('.next').click(function() {
  // 	target = jQuery('ul.triggers li.selected').index();
  // 	target === lastElem ? target = 0 : target = target+1;
  // 	sliderResponse(target);
  // 	resetTiming();
  // });
  // jQuery('.prev').click(function() {
  // 	target = jQuery('ul.triggers li.selected').index();
  // 	lastElem = triggers.length-1;
  // 	target === 0 ? target = lastElem : target = target-1;
  // 	sliderResponse(target);
  // 	resetTiming();
  // });
  // function sliderTiming() {
  // 	target = jQuery('ul.triggers li.selected').index();
  // 	target === lastElem ? target = 0 : target = target+1;
  // 	sliderResponse(target);
  // }
  // var timingRun = setInterval(function() { sliderTiming(); },5000);
  // function resetTiming() {
  // 	clearInterval(timingRun);
  // 	timingRun = setInterval(function() { sliderTiming(); },5000);
  // }

  // //Drawer slide
  // jQuery('.sb_toggle').click(function() {
  // jQuery(this).toggleClass('open');
  // jQuery('#drawer').slideToggle('slow', function() {
  // 	// Animation complete.
  //   });
  // });
});
