<?php

include_once 'libs/Request/Path.php';
include_once 'libs/Template.php';
include_once 'libs/markdown.php';

$rq = new Awf_Request_Path();

$docsPath = 'Docs/core';
$defaultFile = 'Core/Core';

// Determine the right file
$filePath = $rq->toArray();
if(!empty($filePath) && isset($filePath[0])){
	unset($filePath[0]);
	$file = implode('/',$filePath);
}else{
	$file = $defaultFile;
}

$file = preg_replace('/[^a-zA-Z0-9\-\.\/]+/','',str_replace('../','',$file));
$file = $docsPath.'/'.$file.'.md';

if(!file_exists($file)){
	$file = $docsPath.'/'.$defaultFile;
}

// Create template instance
$tpl = new Awf_Template();

$tpl->baseurl = $baseurl = $_SERVER['SCRIPT_NAME'];
$tpl->basepath = $basepath = str_replace('index.php','',$baseurl);

// Unparsed markdown content;
$markdown = file_get_contents($file);

// Get the content of the current page
$content = markdown($markdown);
// Replace urls with the right ones
$content = str_replace('href="/','href="'.$baseurl.'/',$content);
$tpl->content = $content;


// Get the menu
$categories = array();
$dir = new DirectoryIterator($docsPath);
foreach ($dir as $fileinfo){
	if(!$fileinfo->isDot() && $fileinfo->isDir()){
    	$category = array();
    	$dir2 = new DirectoryIterator($docsPath.'/'.$fileinfo->getFilename());
		foreach($dir2 as $file){
			if($file->isFile()) $category[] = str_replace('.md','',$file->getFilename());
		}
		$categories[$fileinfo->getFilename()] = $category;
	}
}
$tpl->menu = $categories;

// Get the methods
$methods = array();
preg_match_all('/\{#(.*)\}/',$markdown,$methods);
if(isset($methods[1]) && is_array($methods[1])){
	$tpl->methods = $methods[1];
}


$tpl->display('index.php');