jQuery(document).ready( function ($) {
  var wrap_div = $("div.wrap");

  var popup_div = $("<div style=\"padding-right: 38px; position: relative;\"><h3>Popup</h3><p>Congratulations. You have installed plugin.</p></div>");
  popup_div.addClass("updated notice "); //is-dismissible

  var button = $('<button />', {
  	'class' : "notice-dismiss",
	type : "button",
	html : "<span class=\"screen-reader-text\">Dismiss this notice.</span>",
	on : {
		click : function() {
			// close popup
			popup_div.remove();
			
			// AJAX request
			$.post(admin_object.ajax_url, {'action': 'popup_action', 'close': true});
		}
	}
  });

  button.appendTo(popup_div);

  popup_div.appendTo(wrap_div);
});