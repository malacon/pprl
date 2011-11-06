function aAwesomeLoginConstructor()
{

	this.rendered = false;

	this.renderLogin = function(options) {

		apostrophe.log('awesome.renderLogin');

		var template = (options['template']) ? options['template'] : 'loginForm';
		
		// Note: Having templates stored in this document are obviously not ideal
		// This is mostly a proof of concept that UI can be generated and applied to a page
		// On-the-fly in a useful and non-obstrusive manner
		
		$.template(
		  "loginForm",
			'<div class="a-awesome-login-wrapper">\
				<form action="/login" method="post" id="a-signin-form" class="a-awesome-login">\
					<h2 class="a-awesome-heading">Login to Apostrophe</h2>\
					<div class="a-awesome-row clearfix">\
						<label for="signin_username">Email</label>\
						<div class="a-awesome-field">\
							<input type="text" name="signin[username]" id="signin_username">\
						</div>\
					</div>\
					<div class="a-awesome-row clearfix">\
						<label for="signin_password">Password</label>\
						<div class="a-awesome-field">\
							<input type="password" name="signin[password]" id="signin_password">\
						</div>\
					</div>\
					<div class="a-awesome-row submit clearfix">\
						<h3 class="awesome-submit"><a href="#sign-in" class="a-act-as-submit">Sign In</a></h3>\
					</div>\
				</form>\
			</div>'
		);

		$.template(
		  "logoutConfirm",
			'<div class="a-awesome-login-wrapper">\
				<div class="a-awesome-login">\
					<h2 class="a-awesome-heading">Are you sure that you want to logout?</h2>\
					<div class="a-awesome-row submit clearfix">\
						<h3 class="awesome-submit"><a href="/logout" class="a-awesome-logout-button">Yes, log me out!</a></h3>\
					</div>\
				</div>\
			</div>'
		);

		var target = (options['target']) ? options['target'] : 'body';
		var data = (options['data']) ? options['data'] : '';
		$.tmpl(template, data).appendTo( target );

		if (template === 'loginForm') 
		{
			$('input#signin_username').focus();
			window.apostrophe.aActAsSubmit({ target : '.a-awesome-login-wrapper' });			
		}
		
		if (template === 'logoutConfirm') 
		{
			$('.a-awesome-logout-button').focus();
		}
		window.aAwesomeLogin.rendered = true;
	};

	this.destroyLogin = function(options) {
		apostrophe.log('awesome.destroyLogin');
		$('.a-awesome-login-wrapper').remove();
		window.aAwesomeLogin.rendered = false;
	};

	this.setupLogin = function(options) {

		apostrophe.log('awesome.setupAwesomeLogin');

		// Setup Template
		$( "#loginForm" ).template( "loginForm" );

		$(window).unbind('keyup.awesome').bind('keyup.awesome', function(e){

			// Pressing Shift+Escape when logged out
			if (e.shiftKey && e.keyCode === 27 && !window.aAwesomeLogin.rendered && !$('body').hasClass('logged-in'))
			{
				window.aAwesomeLogin.renderLogin({});
			}
			
			// Pressing Shift+Escape when logged in
			if (e.shiftKey && e.keyCode === 27 && !window.aAwesomeLogin.rendered && !$('body').hasClass('logged-out'))
			{
				window.aAwesomeLogin.renderLogin({ template: 'logoutConfirm' });
			}
					
			// Pressing Escape closes the window
			if (!e.shiftKey && e.keyCode === 27)
			{
				window.aAwesomeLogin.destroyLogin({});
			}
		});
	}

}

window.aAwesomeLogin = new aAwesomeLoginConstructor();

$(document).ready(function() {
	window.aAwesomeLogin.setupLogin({});
});