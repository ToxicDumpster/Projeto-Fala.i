<?php

function getConnection(){ 
    $hostname = "localhost";
    $username = "root";
    $password = "12345";
    $database = "tcc";

   $connect = new mysqli($hostname, $username, $password, $database);

    if($connect->connect_error){
    die("Falha na conexão" . $connect->connect_error);
    }
    return $connect;
}
