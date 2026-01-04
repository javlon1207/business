<?php
require_once __DIR__ . "/response.php";
require_once __DIR__ . "/jwt.php";
require_once __DIR__ . "/db.php";

function get_bearer_token(): ?string {
  $hdr = $_SERVER["HTTP_AUTHORIZATION"] ?? "";
  if (!$hdr) return null;
  if (preg_match('/Bearer\s+(.+)/i', $hdr, $m)) return trim($m[1]);
  return null;
}

function auth_user(): array {
  $cfg = require __DIR__ . "/../config/config.php";
  $token = get_bearer_token();
  if (!$token) json_out(["error"=>"Unauthorized"], 401);

  try {
    $payload = jwt_verify($token, $cfg["jwt"]["secret"]);
  } catch (Exception $e) {
    json_out(["error"=>"Unauthorized", "message"=>$e->getMessage()], 401);
  }

  // load user from DB to ensure still exists
  $pdo = db();
  $st = $pdo->prepare("SELECT id,last_name,first_name,position,email,role FROM users WHERE id=? LIMIT 1");
  $st->execute([(int)$payload["sub"]]);
  $u = $st->fetch();
  if (!$u) json_out(["error"=>"Unauthorized"], 401);
  return $u;
}

function require_role(array $u, string $role): void {
  if (($u["role"] ?? "") !== $role) json_out(["error"=>"Forbidden"], 403);
}
