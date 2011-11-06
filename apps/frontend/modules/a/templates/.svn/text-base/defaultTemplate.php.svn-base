<?php use_helper('a') ?>

<?php // See the documentation for exciting ways to completely customize the areas and slots used here. This ?>
<?php // is just a copy of what's in the plugin to show you that you can override template files at project level. ?>
<?php // You should never modify the plugin. ?>

<?php // Defining the <body> class ?>
<?php slot('a-body-class','a-default') ?>

<?php // The a/standardArea component is an easy way to pull in the usual list of great content slots. ?>
<?php // You don't have to limit yourself to it, see a_area and a_slot in the documentation. ?>

<?php include_component('a', 'standardArea', array('name' => 'body', 'width' => 480, 'toolbar' => 'Main')) ?>

<?php include_component('a', 'standardArea', array('name' => 'sidebar', 'width' => 200, 'toolbar' => 'Sidebar')) ?>

<?php slot('a-footer') ?>
<div class='a-footer-wrapper clearfix'>
	<div class='a-footer clearfix'>
	  <?php include_partial('a/footer') ?>
		<?php include_partial('aFeedback/feedbackForm'); ?>	
	</div>
</div>
<?php end_slot() ?>