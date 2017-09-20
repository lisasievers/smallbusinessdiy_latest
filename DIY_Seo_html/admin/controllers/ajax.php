<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework v1.0
 * @copyright © 2015 ProThemes.Biz
 *
 */

//AJAX ONLY 


if(isset($_GET['manageUsers'])){
    
// DB table to use
$table = 'users';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'username', 'dt' => 0 ),
	array( 'db' => 'email_id',  'dt' => 1 ),
	array( 'db' => 'date',  'dt' => 2),
	array( 'db' => 'platform',   'dt' => 3 ),
	array( 'db' => 'oauth_uid',   'dt' => 4 ),
	array( 'db' => 'id',   'dt' => 5 ),
	array( 'db' => 'verified',   'dt' => 6 )
);

$columns2 = array(
	array( 'db' => 'username', 'dt' => 0 ),
	array( 'db' => 'email_id',  'dt' => 1 ),
	array( 'db' => 'date',  'dt' => 2),
	array( 'db' => 'platform',   'dt' => 3 ),
	array( 'db' => 'oauth_uid',   'dt' => 4 ),
	array( 'db' => 'ban',  'dt' => 5 ),
	array( 'db' => 'view',  'dt' => 6 ),
	array( 'db' => 'delete',   'dt' => 7)
);


// SQL server connection information
$sql_details = array(
	'user' => $dbUser,
	'pass' => $dbPass,
	'db'   => $dbName,
	'host' => $dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
	SSPUSER::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $columns2 )
);
 die();   
}

if(isset($_GET['managePages'])){
    
// DB table to use
$table = 'pages';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'posted_date', 'dt' => 0 ),
	array( 'db' => 'page_name',  'dt' => 1 ),
	array( 'db' => 'page_title',  'dt' => 2),
	array( 'db' => 'id',   'dt' => 3 ),
	array( 'db' => 'page_url',   'dt' => 4 )
);

$columns2 = array(
	array( 'db' => 'posted_date', 'dt' => 0 ),
	array( 'db' => 'page_name',  'dt' => 1 ),
	array( 'db' => 'page_title',  'dt' => 2),
	array( 'db' => 'view',   'dt' => 3 ),
	array( 'db' => 'edit',   'dt' => 4 ),
	array( 'db' => 'delete',   'dt' => 5)
);


// SQL server connection information
$sql_details = array(
	'user' => $dbUser,
	'pass' => $dbPass,
	'db'   => $dbName,
	'host' => $dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
	SSPPAGE::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $columns2 )
);
 die();   
}


if(isset($_GET['userQuery'])){
    
// DB table to use
$table = 'user_input_history';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'id', 'dt' => 0 ),
	array( 'db' => 'visitor_ip',  'dt' => 1 ),
	array( 'db' => 'tool_name',  'dt' => 2),
	array( 'db' => 'user',   'dt' => 3 ),
	array( 'db' => 'date',   'dt' => 4 ),
    array( 'db' => 'user_input',   'dt' => 5 )
);

$columns2 = array(
	array( 'db' => 'tool_name',  'dt' => 0),
    array( 'db' => 'user_input',   'dt' => 1 ),
	array( 'db' => 'user',   'dt' => 2 ),
	array( 'db' => 'visitor_ip',  'dt' => 3),
	array( 'db' => 'date',   'dt' => 4 )
);


// SQL server connection information
$sql_details = array(
	'user' => $dbUser,
	'pass' => $dbPass,
	'db'   => $dbName,
	'host' => $dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
	SSPRC::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $columns2 )
);
 die();   
}


if(isset($_GET['userHis'])){

require_once (LIB_DIR . 'geoip.inc');
$gi = geoip_open('../core/library/GeoIP.dat', GEOIP_MEMORY_CACHE);
 
// DB table to use
$table = 'recent_history';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => 'id', 'dt' => 0 ),
	array( 'db' => 'visitor_ip',  'dt' => 1 ),
	array( 'db' => 'tool_name',  'dt' => 2),
	array( 'db' => 'user',   'dt' => 3 ),
	array( 'db' => 'date',   'dt' => 4 )
);

$columns2 = array(
	array( 'db' => 'tool_name',  'dt' => 0),
	array( 'db' => 'user',   'dt' => 1 ),
	array( 'db' => 'visitor_ip',  'dt' => 2),
	array( 'db' => 'visitor_country',  'dt' => 3),
	array( 'db' => 'date',   'dt' => 4 )
);


// SQL server connection information
$sql_details = array(
	'user' => $dbUser,
	'pass' => $dbPass,
	'db'   => $dbName,
	'host' => $dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

echo json_encode(
	SSPHIS::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $columns2, $gi )
);
geoip_close($gi);
die();   
}

die();
?>