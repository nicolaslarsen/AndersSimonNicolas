<?php
session_start();
try
{
  //Connect to the database
  $dbh = new PDO('oci:dbname=//localhost:1521/dbwc', 'vgn209', 'dbx2014');
} 
catch (Exception $ex) 
{
    echo "ERROR";
}
?>