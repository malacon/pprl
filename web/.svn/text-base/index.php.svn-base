<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

// DEVELOPERS: Internally, we have a separate index.php controller for each environment and 
// we rsync exclude it to avoid overwriting each environment's controller. 

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
