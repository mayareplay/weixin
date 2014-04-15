<!DOCTYPE html> 
<html>
<head>
	<title><?php echo $this->pageTitle?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php  Yii::app()->clientScript->registerCoreScript('jquery')?>
	<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl?>/js/jquery.mobile-1.4.2.min.css" />
	<script src="<?php echo Yii::app()->baseUrl?>/js/jquery.mobile-1.4.2.min.js"></script>
</head>

<body>
	<?php
		echo $content;
	?>
</body>
</html>