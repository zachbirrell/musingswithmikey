<?php # script 9.2, database connection string

// set the db access information as constants
define('DB_USER',     'root');
define('DB_PASSWORD', 'Davistechistheplace2b');
define('DB_HOST',     'localhost');
define('DB_NAME',     'blogsite');

// make the connection
$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
	OR die('Could not connect to MySQL: ' . mysqli_connect_error() );
	
// set encoding
mysqli_set_charset($dbc, 'utf8');

