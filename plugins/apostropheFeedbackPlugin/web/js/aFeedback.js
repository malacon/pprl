function aFeedbackConstructor() 
{	
	this.feedbackForm = function(options)
	{
		var feedbackURL = options['url'];
		var ajax = options['ajax'];
		var feedback = $('.a-feedback-footer');
		var feedbackLink = feedback.find('#a-feedback-link');
		var feedbackSubmitted = feedback.find('.a-feedback-submitted');
		var feedbackContainer = feedback.find('.a-feedback-form-container');
		
		feedbackLink.unbind('click.feedbackLink').bind('click.feedbackLink',function(e){
			feedbackSubmitted.html(''); 
			feedbackLink.hide();
			feedback.addClass('open');
			aBusy('#a-feedback-footer .a-feedback-form-container');
			$.get(feedbackURL, { }, function (data) { 
				feedbackContainer.html(data); 
				feedbackContainer.show();
				aReady('#a-feedback-footer .a-feedback-form-container');
				$('#feedback_name').focus();
				$('.a-feedback-footer').delegate('.a-cancel','click.cancelFeedback',function(event){
					feedbackContainer.hide(); 
					feedbackLink.show();
					feedback.removeClass('open');
					return false;
				});
			});
			return false;
		});
	};			
}

window.aFeedback = new aFeedbackConstructor();