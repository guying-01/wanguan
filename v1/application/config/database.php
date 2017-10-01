<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/
session_start();
global $db_prefix;
$ci = &get_instance();
$cid=$ci->input->post('cid');
$db_prefix = $_SESSION["db_prefix"] ? $_SESSION["db_prefix"]."_":$cid. "_";  //"qq_";

define('LOG',$db_prefix.'log');  
define('ROLE',$db_prefix.'role');             
define('MENU',$db_prefix.'menu');   
define('CONTACT',$db_prefix.'contact');  
define('GOODS',$db_prefix.'goods'); 
define('GOODS_IMG',$db_prefix.'goods_img');  
define('STAFF',$db_prefix.'staff'); 
define('ASSISTINGPROP',$db_prefix.'assistingprop');
define('RECEIPT_INFO',$db_prefix.'receipt_info');
define('PAYMENT_INFO',$db_prefix.'payment_info');
define('ACCOUNT',$db_prefix.'account');
define('STORAGE',$db_prefix.'storage');     
define('ADMIN',$db_prefix.'admin'); 
define('SETTLEMENT',$db_prefix.'settlement'); 
define('CATEGORY',$db_prefix.'category'); 
define('UNIT',$db_prefix.'unit');   
define('UNITTYPE',$db_prefix.'unittype');  
define('ADDRESS',$db_prefix.'address');
define('ASSISTSKU',$db_prefix.'assistsku');
define('INVPS',$db_prefix.'invps'); 
define('INVPS_INFO',$db_prefix.'invps_info'); 
define('INVOICE',$db_prefix.'invoice');    
define('INVOICE_INFO',$db_prefix.'invoice_info'); 
define('INVOICE_TYPE',$db_prefix.'invoice_type'); 
define('ACCOUNT_INFO',$db_prefix.'account_info'); 
define('OPTIONS',$db_prefix.'options');
define('PRINTTEMPLATES',$db_prefix.'printtemplates');
$active_group = 'default';
$active_record = TRUE;
<<<<<<< .mine
$db['default']['hostname'] = 'localhost';   //æ•°æ®åº“åœ°å€
$db['default']['username'] = 'root';        //æ•°æ®åº“ç”¨æˆ·å
$db['default']['password'] = '123456';            //æ•°æ®åº“å¯†ç 
$db['default']['database'] = 'erpunqq';   //æ•°æ®åº“åç§°
||||||| .r72
$db['default']['hostname'] = 'localhost';   //Êý¾Ý¿âµØÖ·
$db['default']['username'] = 'root';        //Êý¾Ý¿âÓÃ»§Ãû
$db['default']['password'] = '123456';            //Êý¾Ý¿âÃÜÂë
$db['default']['database'] = 'erpunqq';   //Êý¾Ý¿âÃû³Æ
=======
$db['default']['hostname'] = 'localhost';   //ï¿½ï¿½ï¿½Ý¿ï¿½ï¿½Ö·
$db['default']['username'] = 'root';        //ï¿½ï¿½ï¿½Ý¿ï¿½ï¿½Ã»ï¿½ï¿½ï¿½
$db['default']['password'] = '123456';            //ï¿½ï¿½ï¿½Ý¿ï¿½ï¿½ï¿½ï¿½ï¿½
$db['default']['database'] = 'erpunqq';   //ï¿½ï¿½ï¿½Ý¿ï¿½ï¿½ï¿½ï¿½ï¿½
>>>>>>> .r140
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_unicode_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
/* End of file database.php */
/* Location: ./application/config/database.php */
