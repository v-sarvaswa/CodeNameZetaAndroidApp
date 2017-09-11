<?php
define('DBHOST', 'localhost'); //111.118.215.222
define('DBUSER', 'stylokzt_alpha');
define('DBPASS', 'sarvaswa');
define('DBNAME', 'stylokzt_uniportal');

$connect= mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
if(!$connect)
{
printf("Can't connect to MySQL Server.", mysqli_connect_error());
exit;
}


$connect2= mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
if (!$connect2)
{
printf("Can't connect to MySQL Server.", mysqli_connect_error());
exit;
}


$connect3= mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
if (!$connect3)
{
printf("Can't connect to MySQL Server.", mysqli_connect_error());
exit;
}
?>