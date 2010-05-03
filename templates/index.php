<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<link href="<?php echo $basepath; ?>assets/docs.css" rel="stylesheet" type="text/css" media="screen" />

</head>
<body>

<div id="container">
<div id="header">
<h1><span id="repo-title"></span> Documentation</h1>
</div>

<div id="menu">
	<ul>
	<?php foreach($menu as $category => $link): ?>
	<li><strong><?php echo $category; ?></strong><ul>
		<?php foreach($link as $text): ?>
		<li><a href="<?php echo $baseurl.'/core/'.$category.'/'.$text; ?>"><?php echo $text; ?></a></li>
		<?php endforeach; ?>
	</ul></li>
	<?php endforeach; ?>
	</ul>
</div>
<div id="docs" class="doc">
	
	<div class="methods">
		<ul>
		<?php foreach($methods as $method): ?>
			<li><a href="<?php echo '#'.$method; ?>"><?php echo $method; ?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>

	<div class="content">
		<?php echo $content; ?>
	</div>

	
</div>
</div>
	
</body>
</html>