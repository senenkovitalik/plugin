jQuery(document).ready( function ($) {

  var wrap_div = $("div.wrap");

  // create global function
  window.closeAJAX = function(e, target) {
  	e.preventDefault();

	  var obj = {
	  	'action' : 'rate_action',
  	}

  	// determine the element that was clicked
  	switch (target.nodeName) {
  		case "A" : 
  			obj.closeForever = true;
  			break;
  		case "BUTTON" : 
  			obj.closeForAWeek = true;
  			break;
  	}

  	jQuery.post(admin_object.ajax_url, obj);
  	jQuery("#rate_popup").remove();
  }

  // container for popup #2
  var popup_div = $('<div />', {
  	'id' : "rate_popup",
  	'class' : "updated notice",
  	css : {
  		paddingRight : "38px",
  		position : "relative"
  	},
  	// You can change content of popup #2 by modifying line below
  	html : '<h3>Congratulations!!!</h3><p>Do you want to rate our plugin?  \
  			<a href="https://wordpress.org/support/view/plugin-reviews/rs-feedburner">Rate us</a> - \
  			<a href="" onclick="closeAJAX(window.event, this);">Dont show</a></p>'
  });

  // close button at right upper corner
  var button = $('<button />', {
    'class' : "notice-dismiss",
  	type : "button",
  	html : '<span class="screen-reader-text">Dismiss this notice.</span>',
  	on : {
  		click : function() {
  			closeAJAX(window.event, this);
  		}
  	}
  });

  button.appendTo(popup_div);
  popup_div.appendTo(wrap_div);
});