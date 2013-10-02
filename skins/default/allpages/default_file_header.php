<?php
	global $DIR_SCRIPTS;
		echo "". 
				'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".
				'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">'."\n";
		echo	'<head>'."\n".
  				'	<meta http-equiv="Content-language" content="'.$page->document_language.'" />'."\n".
  				'	<meta http-equiv="Content-Type" content="text/html;charset='.$page->document_charset.'" />'."\n".
  				'	<title>'.$page->title.'</title>'."\n";
  		
  		foreach ($page->css as $index => $value) {
  		 echo	'	<link rel="stylesheet" type="text/css" href="'.$value.'" media="screen,projection,print" />'."\n";
  		}
  		global $DIRECTORY;
  		echo	'	<link rel="shortcut icon" href="'.$DIRECTORY.'favicon.ico" />'."\n";
  		
  		echo	'	<meta name="description" content="'.$page->headline.'" />'."\n".
  				'	<meta name="copyright" content="'.$page->copyright.'" />'."\n".
  				'	<meta name="author" content="'.$page->document_author.'" />'."\n".
  				'	<meta name="robots" content="'.$page->robots.'" />'."\n";
		foreach ($page->scripts as $index => $value) {
    	 echo	'	<script language="'.$value["language"].'" src="'.$value["fileName"].'" type="'.$value["type"].'"></script>'."\n";	
		}		
		foreach ($page->pub_javascripts as $index => $value) {
    	 echo	'	<script language="JavaScript" src="'.$value.'" type="text/javascript"></script>'."\n";	
		}
		
		 /* 
         tooltips js  
         */
         echo '<script language="JavaScript1.2" src="'.$DIR_SCRIPTS.'/main.js" type="text/javascript"></script>' . "\n";
                
		
 		echo	'</head>'."\n";
 		
 		
 		
 		echo "".
				'<body>'."\n";
		
		global $error;
		$errors = $error->get_printable_errors();
		if (count($errors)) {
			global $LANGUAGE;
			
			echo "	<fieldset id=\"errors\">\n";
			echo "		<legend>{$LANGUAGE['ERROR_CAPTION']}</legend>\n";
			echo "		<p id=\"error_text\">{$LANGUAGE['ERROR_HEADER']}</p>\n\n";
			foreach ($errors as $index => $value) {
    	 		echo "		<p class=\"err\">$value</p>\n";
			}
			echo "\n		<p id=\"error_info\">{$LANGUAGE['ERROR_INFO']}</p>\n";
    	 	echo "	</fieldset>\n\n";
		}
				
		foreach ($page->directInputScripts as $index => $value) {
    	 	include_once $value;
		}
		
?>