<?php

function napln_typ(){

	//pridani uzivatele stredoskolak
	$options[] = Array ( 0 => "trening", 1 => "trening" );
	$options[] = Array ( 0 => "deathmatch", 1 => "deathmatch" );
	$options[] = Array ( 0 => "elite_dm", 1 => "elitní deathmatch" );
	$options[] = Array ( 0 => "team", 1 => "týmová hra" );

	return $options;
}

function napln_reg(){

	//pridani uzivatele stredoskolak
	$options[] = Array ( 0 => "ano", 1 => "ano" );
	$options[] = Array ( 0 => "ne", 1 => "ne" );

	return $options;
}

function napln_rocni_obdobi(){

	$options[] = Array ( 0 => "jaro", 1 => "jaro" );
	$options[] = Array ( 0 => "léto", 1 => "léto" );
	$options[] = Array ( 0 => "podzim", 1 => "podzim" );
	$options[] = Array ( 0 => "zima", 1 => "zima" );

	return $options;
}

?>