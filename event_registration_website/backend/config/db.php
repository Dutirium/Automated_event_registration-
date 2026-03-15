<?php 

function getDB(): mysqli
{
static $conn =null;

$host='localhost';
$user='kaden';
$pass='kanyal';
$dbname='gyanJyoti';


$conn= new mysqli($host,$user,$pass,$dbname);

if ($conn->connect_error)
    {
        die("Connection Failed.." .$conn->connect_error);
    }

return $conn;

}


?>