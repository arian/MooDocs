<?php

header('Content-type: text/html');

include_once 'libs/Request/Path.php';
include_once 'libs/Template.php';
include_once 'libs/markdown.php';
include_once 'libs/geshi/geshi.php';

$rq = new Awf_Request_Path();

// Determine the right file
$file = $rq->toArray();

$packages = array(
	'core' => array('../mootools-core/Docs/', 'Intro.md'),
	'more' => array('../mootools-more/Docs/', 'More/More.md'),
	'art' => array('../art/Docs/', 'ART/ART.md')
);

$module = !empty($file[0]) ? $file[0] : 'core';
$docsPath = $packages[$module][0];
$defaultFile = $packages[$module][1];


if(!empty($file) && isset($file[0])){
	unset($file[0]);
	$file = implode('/',$file);
}else{
	$file = $defaultFile;
}

$file = preg_replace('/[^a-zA-Z0-9\-\.\/]+/','',str_replace('../','',$file));
$filePath = $docsPath.'/'.$file.'.md';

if(!file_exists($filePath)){
	$file = $defaultFile;
	$filePath = $docsPath.'/'.$file;
}

// Create template instance
$tpl = new Awf_Template();

$tpl->baseurl = $baseurl = $_SERVER['SCRIPT_NAME'];
$tpl->basepath = $basepath = str_replace('index.php','',$baseurl);
$tpl->title = $file;
$tpl->module = $module;

// Unparsed markdown content;
$markdown = file_get_contents($filePath);

// Get the content of the current page
$md = new MarkdownExtra_Parser();
$md->no_markup = true;
$content = $md->transform($markdown);
// Replace urls with the right ones
$content = str_replace('href="/','href="'.$baseurl.'/',$content);
// Highlight code examples
$geshi = new GeSHi('', 'javascript');
$geshi->enable_classes();
foreach ( explode("<pre><code>", $content) as $key => $value ){
	if ( $key === 0 ){
		$content = $value;
		continue;
	}

	$halve = explode("</code></pre>", $value);

	$geshi->set_source(rtrim($halve[0]));
	$content .= $geshi->parse_code();

	$content .= $halve[1];
}
$tpl->content = $content;


// Get the menu
$categories = array();
$dir = new DirectoryIterator($docsPath);
foreach ($dir as $fileinfo){
	if(!$fileinfo->isDot() && $fileinfo->isDir()){
    	$category = array();
    	$dir2 = new DirectoryIterator($docsPath.'/'.$fileinfo->getFilename());
		foreach($dir2 as $fileinfo2){
			if($fileinfo2->isFile()) $category[] = str_replace('.md','',$fileinfo2->getFilename());
		}
		$categories[$fileinfo->getFilename()] = $category;
	}
}
$tpl->menu = $categories;

// Get the methods
$methods = array();
preg_match_all('/\{#(.*)\}/',$markdown,$tmpMethods);
if(isset($tmpMethods[1]) && is_array($tmpMethods[1])){
	$methods = array();

	foreach($tmpMethods[1] as $method){
		$tmp = explode(':',$method);
		if(count($tmp) >= 2){
			$groupName = $tmp[0];
			unset($tmp[0]);
			if(!isset($methods[$groupName])) $methods[$groupName] = array();
			$methods[$groupName][] = implode(':',$tmp);
		}else{
			$methods[$method] = array();
		}
	}

	$tpl->methods = $methods;
}


$tpl->display('index.php');
