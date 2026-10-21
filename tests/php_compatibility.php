<?php
// Run from the repository root against a test database; only temporary tables are created.
if (PHP_SAPI !== 'cli') exit(1);
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false;
});
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
require 'core/dblib.php';
require 'core/lib.php';
require 'core/page.php';
require 'applib/fm_fce/fm_fce.php';
$DIR_LIB = 'applib/';
$LANGUAGE = array('prazdny_seznam' => 'No rows', 'DEFAULT_ERROR_TEXT' => 'Error');
require 'core/list_element.php';
require 'core/error.php';

$page = new Page();
check(is_array($page->scripts), 'Page constructor must initialize scripts');
check((new AppError())->count() === 0, 'Application error class must initialize');
check((new textElement('hello'))->text === 'hello', 'Element constructor must run');
$filter = new listbox_filter('test', array());
check($filter->name === 'test', 'Filter must call its parent constructor');
check(check_email_vrana('Test+tag~@example.com') === 1, 'Valid email rejected');
check(check_email_vrana('invalid@') === 0, 'Invalid email accepted');
check(htmlsafe(null) === '', 'Nullable HTML values must remain supported');
check(my_stripslashes("O'Reilly") === "O'Reilly", 'Do not remove input slashes');

$db = new DbLibMySQL();
$db->init(getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_PORT') ?: 3306,
    getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: '', getenv('DB_NAME') ?: 'berlin');
$db->OpenConnection();
try {
    $db->Query('CREATE TEMPORARY TABLE php_compatibility (id INT AUTO_INCREMENT PRIMARY KEY, value TEXT NULL)');
    $value = "O'Reilly \\ Czech: žluťoučký";
    $db->DbQuery("INSERT INTO php_compatibility (value) VALUES ('" . sqlsafe($value) . "'), (NULL)");
    check($db->GetAffectedRows() === 2, 'Affected row count');
    check($db->GetLastId() === 1, 'Insert ID');
    $result = $db->Query('SELECT id, value FROM php_compatibility ORDER BY id');
    check($db->GetNumRows($result) === 2, 'Row count');
    check($db->GetResult(0, 'value', $result) === $value, 'Named field and SQL escaping');
    check($db->GetResult(1, 1, $result) === null, 'SQL NULL must remain null');
    check($db->GetResult(2, 0, $result) === false, 'Missing row must return false');
    $db->FreeResult($result);
    $result = $db->DbQuery('SELECT id FROM php_compatibility ORDER BY id');
    check($db->GetNextRecord($result) && (int) $db->GetField('id') === 1, 'Unbuffered result');
    $db->FreeResult($result);
    check((int) $db->GetSingleValue('SELECT COUNT(*) FROM php_compatibility') === 2, 'Scalar query');
    $db->Query('SELECT id FROM php_compatibility WHERE 1=0');
    check($db->GetResult() === false, 'Empty result must return false');
    check(str_contains($db->GetTableDef('php_compatibility', "\n"), 'CREATE TEMPORARY TABLE'), 'Table definition dump');
    check(str_contains($db->GetTableContent('php_compatibility', "\n"), 'INSERT INTO php_compatibility'), 'Table data dump');

    $_SERVER['REQUEST_URI'] = '/test?section=empty';
    $list = new listElement($db, $page, 'Empty', '(SELECT id FROM php_compatibility WHERE 1=0) AS empty_rows', array());
    $list->get_data_from_db();
    check($list->db_data === array(), 'Empty administration lists must not count null');

    // Query failures must still use the application's error path, without recursive unlock failures.
    $caught = false;
    try {
        $db->DbQuery('SELECT FROM');
    } catch (Exception $e) {
        $caught = str_contains($e->getMessage(), 'Chyba databáze');
    }
    check($caught, 'Database error handling');
} finally {
    $db->CloseConnection();
}
check(!$db->IsConnected(), 'Closed connections must not appear connected');
echo "PHP compatibility checks passed on " . PHP_VERSION . "\n";
