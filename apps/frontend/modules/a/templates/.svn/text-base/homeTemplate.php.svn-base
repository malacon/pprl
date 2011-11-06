<?php use_helper('a') ?>

<?php // See the documentation for exciting ways to completely customize the areas and slots used here. This ?>
<?php // is just a copy of what's in the plugin to show you that you can override template files at project level. ?>
<?php // You should never modify the plugin. ?>

<?php // Defining the <body> class ?>
<?php slot('a-body-class','a-home') ?>

<?php // Breadcrumb is removed for the home page template because it is redundant ?>
<?php slot('a-breadcrumb', '') ?>

<?php // Subnav is removed for the home page template because it is redundant ?>
<?php slot('a-subnav', '') ?>

<?php a_slot('home-banner',
'aSlideshow', array(
	'width' => 960,
	'height' => 300,
	'resizeType' => 'c',
	'flexHeight' => false,
	'constraints' => array('minimum-width' => 960, 'minimum-height' => 300),
	'arrows' => true,
	'interval' => 8,
	'random' => true,
	'title' => false,
	'description' => false,
	'credit' => false,
	'position' => true,
	'transition' => 'crossfade',
	'duration' => 500,
	'itemTemplate' => 'homeBannerItem',
	'allowed_variants' => array('autoplay','normal'),
)) ?>

<?php include_component('a', 'standardArea', array('name' => 'body', 'width' => 680, 'toolbar' => 'Main')) ?>

<?php include_component('a', 'standardArea', array('name' => 'sidebar', 'width' => 240, 'toolbar' => 'Sidebar')) ?>


<?php slot('a-footer') ?>
<div class='a-footer-wrapper clearfix'>
	<div class='a-footer clearfix'>
	  <?php include_partial('a/footer') ?>
		<?php include_partial('aFeedback/feedbackForm'); ?>	
	</div>
</div>
<?php end_slot() ?>