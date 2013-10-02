<?php
class Printer {
	
	function Printer(){
		
	}
	/** set pageproperties specific to this skin */
	function set_page_properties($page){
		$page->add_css($page->skin_dir.'/admin.css');
	}

	function print_page($page){
		global $users_class, $LANGUAGE;
		
		$this->set_page_properties($page);
		
		require_once($page->skin_dir."/../allpages/default_file_header.php");
		
		 		 	
  		echo	'<h1>'.$page->headline.'</h1>'."\n";
 		
  		echo	'<div class="menu">' . "\n";
  		echo	'<a href="index.php?section=logout" onclick="return confirm(\''
				. "Opravdu se chcete odhlasit?" . '\');">LOGOUT ('
  				. $users_class->user_login() . ')</a><br />' . "\n";	
  		echo	'<a href="index.php">Návrat na titulní stránku </a><br />';
  		echo	'<ul>' . "\n";
  		if (isset($page->menuitems['admin_menu'])) {
	  		foreach ($page->menuitems['admin_menu'] as $index => $value) {
	 			if (isset($value['active']) && $value['active'] == 1 && isset($value['submenu'])) {
	 				echo	'	<li>'.$value['text'] ."\n";
					echo	'		<ul>' . "\n";
	 				foreach ($value['submenu'] as $index => $value) {
			 			if (isset($value['active']) && $value['active'] == 1 && isset($value['submenu'])) {
			 				echo	'	<li>'.$value['text'] ."\n";
							echo	'		<ul>' . "\n";
			 				foreach ($value['submenu'] as $index => $value) {
								echo	'			<li><a href="'.$value["link"].'" class="menuitem">'.$value["text"].'</a></li>' ."\n";	
							}
							echo	'		</ul>' . "\n";
							echo	'		</li>' . "\n";	
			 			} else {
			 				echo	'	<li><a href="'.$value["link"].'" class="menuitem">'.$value["text"].'</a></li>' ."\n";	
			 			}
	 				}
					echo	'		</ul>' . "\n";
					echo	'		</li>' . "\n";	
	 			} else {
	 				echo	'	<li><a href="'.$value["link"].'" class="menuitem">'.$value["text"].'</a></li>' ."\n";	
	 			}	
			}
  		}
		echo	'</ul>' . "\n";
		echo	'</div>' . "\n";
		
		echo 	'<div>'."\n";
		foreach ($page->elements as $index => $value) {
  		 echo	'				'.$value->draw()."\n"; 
		}		
		echo 	'</div>'."\n";	
		
		echo 	'<div>'."\n";
		foreach ($page->texts as $index => $value) {
  		 echo	'				'.$value."\n";	 
		}		
		echo 	'</div>'."\n";				
		/*echo 	'		'.$page->footer_text_left."\n".
				"		  $page->footer_text_centered"."\n". 
				'		'.$page->footer_text_right."\n";*/
		
  global $GOOGLE_ANALYTICS, $ANALYZE;
  if ($ANALYZE){
  		echo $GOOGLE_ANALYTICS;
  }
  
  		echo 	'</body>'."\n";
  		echo 	'</html>'."\n";
	}
	
}
?>