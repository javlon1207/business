<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
require_role($u, "admin");

$pdo = db();
$st = $pdo->query("SELECT id,last_name,first_name,position,email,role FROM users ORDER BY last_name, first_name");
json_out(["users"=>$st->fetchAll()]);
