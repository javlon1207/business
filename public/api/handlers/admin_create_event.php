<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
require_role($u, "admin");

$data = json_input();
require_fields($data, ["event_name","start_time","location","agenda","participants"]);
$endTime = $data["end_time"] ?? null;
if (!is_array($data["participants"]) || count($data["participants"]) === 0) {
  json_out(["error"=>"participants must be non-empty array of user_id"], 400);
}

$pdo = db();
$pdo->beginTransaction();
try {
  $st = $pdo->prepare("INSERT INTO events(event_name,start_time,end_time,location,agenda,created_by) VALUES (?,?,?,?,?,?)");
  $st->execute([$data["event_name"], $data["start_time"], $endTime, $data["location"], $data["agenda"], (int)$u["id"]]);
  $eventId = (int)$pdo->lastInsertId();

  $stp = $pdo->prepare("INSERT INTO event_participants(event_id,user_id,status,notified) VALUES (?,?, 'pending', 1)");
  $stn = $pdo->prepare("INSERT INTO notifications(user_id,event_id,message) VALUES (?,?,?)");

  foreach ($data["participants"] as $uid) {
    $uid = (int)$uid;
    $stp->execute([$eventId, $uid]);
    $msg = "Siz “{$data['event_name']}” tadbiriga taklif qilindingiz. Sana: {$data['start_time']}. Joy: {$data['location']}.";
    $stn->execute([$uid, $eventId, $msg]);
  }

  $pdo->commit();
  json_out(["ok"=>true, "event_id"=>$eventId]);
} catch (Exception $e) {
  $pdo->rollBack();
  json_out(["error"=>"Create event failed", "message"=>$e->getMessage()], 500);
}
