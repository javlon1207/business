<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
$data = json_input();
require_fields($data, ["notification_id"]);

$pdo = db();
$st = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
$st->execute([(int)$data["notification_id"], (int)$u["id"]]);
json_out(["ok"=>true]);
