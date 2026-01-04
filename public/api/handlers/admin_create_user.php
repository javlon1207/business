<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
require_role($u, "admin");

$data = json_input();
require_fields($data, ["last_name","first_name","position","email","role"]);
$role = $data["role"];
if (!in_array($role, ["admin","user"], true)) json_out(["error"=>"Invalid role"], 400);

$pdo = db();
$pass = password_hash("123", PASSWORD_DEFAULT);

try {
  $st = $pdo->prepare("INSERT INTO users(last_name,first_name,position,email,password,role) VALUES (?,?,?,?,?,?)");
  $st->execute([$data["last_name"], $data["first_name"], $data["position"], $data["email"], $pass, $role]);
  json_out(["ok"=>true, "user_id"=>(int)$pdo->lastInsertId(), "default_password"=>"123"]);
} catch (PDOException $e) {
  if (strpos($e->getMessage(), "Duplicate") !== false) {
    json_out(["error"=>"Email already exists"], 409);
  }
  json_out(["error"=>"DB error"], 500);
}
