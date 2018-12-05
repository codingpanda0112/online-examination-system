<?php
$cn=mysqli_connect("localhost","root","","quiz_new");
//or die("Could not Connect My Sql");
if(!$cn){
	die("connection failed!!".mysqli_connect_error());
}
//mysql_select_db("quiz_new",$cn)  or die("Could connect to Database");
?>
