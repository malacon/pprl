<?php // If the feedback plugin is not configured, don't display it ?>
<?php if (sfConfig::get('app_aFeedback_email_manual', sfConfig::get('app_aFeedback_email_auto'))): ?>
  <?php if ($sf_params->get('module') != 'aFeedback'): ?>
  <?php use_helper('a') ?>
  <div class="a-feedback-footer clearfix" id="a-feedback-footer">
  	<?php echo a_js_button(a_('Submit a Bug Report'), array('a-link', 'a-feedback'), 'a-feedback-link') ?>
  	<div class="a-feedback-form-container clearfix" id="a-feedback-form-container">
  		<?php include_partial('aFeedback/feedback', array('form' => new aFeedbackForm(), 'feedbackSubmittedBy' => false, 'failed' => false)) ?> 
  	</div>
  	<?php if ($reportSubmittedBy = $sf_user->getFlash('reportSubmittedBy')): ?>
   	<div class="a-feedback-submitted clearfix"><span class="submitted-by"><?php echo $reportSubmittedBy ?></span> &ndash; <?php echo a_('Thank you for submitting a bug report.') ?></div>
  	<?php endif ?>
  </div>
  <?php a_js_call('aFeedback.feedbackForm(?)', array('url' => url_for('aFeedback/feedback')."?".http_build_query(array('section' => $_SERVER['REQUEST_URI'])))) ?>
  <?php endif ?>
<?php endif ?>