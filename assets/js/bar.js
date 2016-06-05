
jQuery(document).ready( function ($) {

	var check_username = "[a-zA-Z0-9]{3,15}";
	var check_email = "^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$";

    var content =
        "<form id='header_form'>"+
            "<label>Name</label><input id='username' type='text' placeholder='Name' pattern='"+check_username+"' />" +
            "<label>Email</label><input id='email' type='email' placeholder='Email' pattern='"+check_email+"' />" +
            "<input type='submit' value='submit' id='form_button' />" +
        "</form>";

    $("#sidebar").before( content );
});