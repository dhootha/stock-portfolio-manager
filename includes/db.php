<?php
PutEnv("ORACLE_SID=CS339");
PutEnv("LD_LIBRARY_PATH=/opt/oracle/product/11.2.0/db_1/lib");
PutEnv("ORACLE_HOME=/opt/oracle/product/11.2.0/db_1");
PutEnv("ORACLE_BASE=/opt/oracle/product/11.2.0");
$dbuser = "cel294";
$dbpassword = "o66abbfd4";
$ORACLE=oci_connect($dbuser, $dbpassword);
if (!$ORACLE) {
  die("Failed to connect to Oracle database");
 }
define("BASEURL", "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
?>
