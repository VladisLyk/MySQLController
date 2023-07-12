<?php
include "libs/MySQL/mysql.php";

$mysql = new MySQL("host", "user", "pass", "3306", "base");

$query = $mysql->createQuery();
$selectrow = $query->select("testtable", ["id" => "1"])["name"];
$updaterow = $query->update("testtable", ["id" => "1"], ["name" => "Hello"]);

?>