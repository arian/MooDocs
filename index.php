<?php

include_once 'libs/Request/Path.php';
include_once 'libs/Template.php';
include_once 'libs/markdown.php';

$rq = new Awf_Request_Path();

$coreOrMore = 'core';

// Determine the right file
$file = (string) $rq;
if(empty($file)) $file = $coreOrMore.'Core/Core';
$file = preg_replace('/[^a-zA-Z0-9\-\.\/]+/','',str_replace('../','',$file));
$file = 'Docs/'.$file.'.md';
if(!file_exists($file)) $file = 'Docs/'.$coreOrMore.'/Core/Core.md';

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
$dir = new DirectoryIterator('Docs/'.$coreOrMore);
foreach ($dir as $fileinfo){
	if(!$fileinfo->isDot() && $fileinfo->isDir()){
    	$category = array();
    	$dir2 = new DirectoryIterator('Docs/'.$coreOrMore.'/'.$fileinfo->getFilename());
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