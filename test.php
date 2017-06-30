<?php
ini_set('display_errors', 'on'); // Display all errors on screen
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
header("Cache-Control: public");
header("Expires: " . date("r", time() + 3600));
header('Content-Type: text/html; charset=UTF-8');
ob_start();
@session_start();
//    define('DS', DIRECTORY_SEPARATOR);
define('DS', '/');
define('HOST', dirname(__FILE__)); // Root path
define('MULTI_LANGUAGE', false);
define('APPLICATION', 'frontend'); // Choose application - backend|frontend
define('PROFILER', false); // On/off profiler
define('START_TIME', microtime(true)); // For profiler. Don't touch!
define('START_MEMORY', memory_get_usage()); // For profiler. Don't touch!

require_once 'loader.php';

print_r(\Core\FB::factory()->checkToken('EAAVvn9PGOiQBAPjG7oJOSq1QZBHFnORwHB8SQMaIs6I3TULNSk35dFXOZCAmk0TxZCW0N58GDC7Bi74reo5Vn3eqPpbA9ZArhrEiYMiP9wVu6RcZABLcZC5uZAfNnbJM6jIaBlEeZCO6NXWLaMcZBWiZBJkpcNMzV1K5KoqDQNwnZAHzAht1Eb7XnyD6siuV6lZCCN1BzmI3YmdZAyWXRq015qRnNaGxaKoF5jGYD1ZAUrA5RNWAZDZD'));
die;
Core\Route::factory()->execute();
\Profiler::view();
