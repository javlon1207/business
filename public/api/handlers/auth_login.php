<?php
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";
require_once __DIR__ . "/../../../src/jwt.php";

$cfg = require __DIR__ . "/../../../config/config.php";
$data = json_input();
require_fields($data, ["email","password"]);

$pdo = db();
$st = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$st->execute([$data["email"]]);
$u = $st->fetch();
if (!$u || !password_verify($data["password"], $u["password"])) {
  json_out(["error"=>"Email or password wrong"], 401);
}

$now = time();
$payload = [
  "iss" => $cfg["jwt"]["issuer"],
  "iat" => $now,
  "exp" => $now + (int)$cfg["jwt"]["ttl_seconds"],
  "sub" => (int)$u["id"],
  "role"=> $u["role"]
];

$token = jwt_sign($payload, $cfg["jwt"]["secret"]);
json_out([
  "token" => $token,
  "user" => [
    "id" => (int)$u["id"],
    "last_name" => $u["last_name"],
    "first_name" => $u["first_name"],
    "position" => $u["position"],
    "email" => $u["email"],
    "role" => $u["role"]
  ]
]);
