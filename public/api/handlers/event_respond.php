<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
$data = json_input();
require_fields($data, ["event_id","status"]);

$eventId = (int)$data["event_id"];
$status = $data["status"];
if (!in_array($status, ["confirmed","rejected"], true)) json_out(["error"=>"Invalid status"], 400);

$pdo = db();
$st = $pdo->prepare("UPDATE event_participants SET status=? WHERE event_id=? AND user_id=?");
$st->execute([$status, $eventId, (int)$u["id"]]);

// also add notification for admin (created_by)
$st2 = $pdo->prepare("SELECT created_by, event_name FROM events WHERE id=?");
$st2->execute([$eventId]);
$ev = $st2->fetch();
if ($ev && $ev["created_by"]) {
  $msg = "{$u['last_name']} {$u['first_name']} ({$u['position']}) tadbirga {$status} qildi: {$ev['event_name']}";
  $st3 = $pdo->prepare("INSERT INTO notifications(user_id,event_id,message) VALUES (?,?,?)");
  $st3->execute([(int)$ev["created_by"], $eventId, $msg]);
}

json_out(["ok"=>true]);
