\
<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
require_role($u, "admin");
$pdo = db();

$eventId = (int)($_GET["event_id"] ?? 0);

if ($eventId > 0) {
  $st = $pdo->prepare("
    SELECT status, COUNT(*) as cnt
    FROM event_participants
    WHERE event_id=?
    GROUP BY status
  ");
  $st->execute([$eventId]);
  json_out(["event_id"=>$eventId, "by_status"=>$st->fetchAll()]);
}

$st2 = $pdo->query("
  SELECT ep.status, COUNT(*) as cnt
  FROM event_participants ep
  GROUP BY ep.status
");
$st3 = $pdo->query("SELECT COUNT(*) as total_events FROM events");
json_out([
  "overall_by_status"=>$st2->fetchAll(),
  "total_events"=>(int)$st3->fetchColumn()
]);
