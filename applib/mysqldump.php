<?php
// NASTAVENI SQL //
require_once("config/config.php");

// DALSI NASTAVENI
$myFile = date("Y-n-j_g-i-s");
$file_name= $DIR_LOG . "sql_dump_". $myFile; // jmeno souboru pro DUMP
$show_echo=0; // Zobrzit DUMP (0=ne / 1=ano);
$crlf="\n"; // nastaveni konce radku

// Use the same connection and dump implementation as the application.
require_once("core/dblib.php");
$dump_db = new DbLibMySQL();
$dump_db->init($DB_SERVER, $DB_PORT, $DB_USER, $DB_PASS, $DB_DATABASE);
$dump_db->OpenConnection();
$tables = $dump_db->Query('SHOW TABLES');
$num_tables = $dump_db->GetNumRows($tables);
$dump_buffer="#MySQL DUMP$crlf";


for ($i=0;$i<$num_tables;$i++){
	$table=$dump_db->GetResult($i, 0, $tables);
	//ktere tabulky zalohovat
	if ( strpos($table, 'in_game')===false || $table=='ligy'){
		continue;
	}
	$dump_buffer.="$crlf#Table name: $table$crlf$crlf";
	$dump_buffer.=$dump_db->GetTableDef($table,"$crlf");
	$dump_buffer.="$crlf#DATA$crlf";
	$dump_buffer.=$dump_db->GetTableContent($table,"$crlf");
	$dump_buffer.="$crlf$crlf";
}

$dump_db->CloseConnection();
$f=Fopen($file_name,"w");
FPutS($f,$dump_buffer);
FClose($f);
if ($show_echo==1){
  echo nl2br($dump_buffer);
}
?>