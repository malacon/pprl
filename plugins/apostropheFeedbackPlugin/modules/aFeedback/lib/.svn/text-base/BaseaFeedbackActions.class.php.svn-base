<?php

/**
 * Base actions for the apostropheFeedbackPlugin aFeedback module.
 * 
 * @package     apostropheFeedbackPlugin
 * @subpackage  aFeedback
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12628 2008-11-04 14:43:36Z Kris.Wallsmith $
 */
abstract class BaseaFeedbackActions extends sfActions
{
  /**
	 * Executes feedback action
	 *
	 */
	public function executeFeedback(sfRequest $request)
	{
	  $section = $request->getParameter('section', false);
	  $this->form = new aFeedbackForm($section);
    $this->feedbackSubmittedBy = false;
    $this->failed = false;
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag', 'Url'));
    
		if ($request->isMethod('post'))
		{

			$this->form->bind($request->getParameter('feedback'), $request->getFiles('feedback'));			
			// $this->form->bind(array_merge($request->getParameter('feedback'), array('captcha' => $request->getParameter('captcha'))), $request->getFiles('feedback'));
			if ($this->form->isValid())
			{
				$feedback = $this->form->getValues();
				$feedback['browser'] = $_SERVER['HTTP_USER_AGENT'];
				
        try
        {
          // Use the SwiftMailer hotness built into Symfony, not Zend Mail
          $message = $this->getMailer()->compose(
                array($feedback['email'] => $feedback['name']),
                sfConfig::get('app_aFeedback_email_auto'),
                $this->form->getValue('subject', 'New aBugReport submission'),
                $this->getPartial('feedbackEmailText', array('feedback' => $feedback)));
          if ($screenshot = $this->form->getValue('screenshot'))
          {
            $message->attach(Swift_Attachment::fromPath($screenshot->getTempName(), $screenshot->getType()));
          }
          $this->getMailer()->send($message);

          // A new form for a new submission
          $this->form = new aFeedbackForm();      
        }
        catch (Exception $e)
        {
          $this->logMessage('Request email failed: '. $e->getMessage(), 'err');
          $this->failed = true;

          return 'Success';
        }
      	
      	$this->getUser()->setFlash('reportSubmittedBy', $feedback['name']);
        $this->redirect($feedback['section']);
			}
		}
	}
}
