<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
require_role($u, "admin");

$eventId = (int)($_GET["event_id"] ?? 0);
if ($eventId <= 0) json_out(["error"=>"event_id required"], 400);

$pdo = db();
$st = $pdo->prepare("SELECT * FROM events WHERE id=?");
$st->execute([$eventId]);
$ev = $st->fetch();
if (!$ev) json_out(["error"=>"Not found"], 404);

$st2 = $pdo->prepare("
  SELECT ep.user_id, ep.status, ep.notified,
         u.last_name, u.first_name, u.position, u.email
  FROM event_participants ep
  JOIN users u ON u.id = ep.user_id
  WHERE ep.event_id=?
  ORDER BY u.last_name, u.first_name
");
$st2->execute([$eventId]);

json_out([
  "event"=>$ev,
  "participants"=>$st2->fetchAll()
]);
