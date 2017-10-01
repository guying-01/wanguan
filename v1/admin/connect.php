<?php
/******数据库配置文件***********/
function connect_main() {
  $db_host = "localhost:3306"; // address
  $db_login = "root"; // database username 
  $db_pass = "123456"; //   password 
  $db_database = "erpunqq"; // databasename
  $verbinding = mysql_connect("$db_host", "$db_login", "$db_pass") or die("MsSQL connectie mistake."); 
  mysql_select_db("$db_database")or die("Selecte  database mistake."); 
  mysql_query("SET NAMES 'utf8'");
  return $verbinding;
}

?>
