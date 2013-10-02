<?php

function FormularZmenaHesla(){
	$form = '';
	$form .=  '<script type="text/javascript"> 
		$(document).ready(function() {
		 $("#zmena_hesla").hide();
		});
		</script>';
	
	$form .= '<div id="zmena_hesla"><form action="" method="post">
	<table>
	<tr><td rowspan="2">Zadejte nové heslo (pro kontrolu 2x)</td>
	<td><input type="password" name="heslo"></td>
	<td rowspan="2"><input type="submit" name="odeslat" value="Změnit"></td></tr>
	
	<tr><td><input type="password" name="heslo_druhe"></td></tr>
	</table>
	</form></div>';
	
	return $form;
}

function ZpracujZmenuHesla(){
	global $users_class;
	$return = '';
	if ($_POST['heslo']==$_POST['heslo_druhe']){
		$return .= 'Heslo bylo změněno.';
		$users_class->change_pwd( $users_class->user_id, sqlsafe($_POST['heslo']) );
	}
	else{
		$return .= 'Zadaná hesla nejsou stejná.';
	}
	
	return $return;
}

?>