<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
$pdo = db();

$st = $pdo->prepare("
  SELECT n.id, n.event_id, n.message, n.is_read, n.created_at,
         e.event_name, e.start_time, e.location
  FROM notifications n
  LEFT JOIN events e ON e.id = n.event_id
  WHERE n.user_id = ?
  ORDER BY n.created_at DESC
  LIMIT 100
");
$st->execute([(int)$u["id"]]);
json_out(["notifications"=>$st->fetchAll()]);
