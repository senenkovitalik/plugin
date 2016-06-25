jQuery(document).ready( function ($) {
  var wrap_div = $("div.wrap");
  var popup_div = $("<div><h3>Popup</h3><p>Congratulations. You have installed plugin.</p></div>");
  popup_div.addClass("updated notice is-dismissible");
  popup_div.appendTo(wrap_div);
});