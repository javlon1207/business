<?php
require_once __DIR__ . "/../../../src/auth.php";
require_once __DIR__ . "/../../../src/response.php";
require_once __DIR__ . "/../../../src/db.php";

$u = auth_user();
$pdo = db();

if ($u["role"] === "admin") {
  $st = $pdo->query("SELECT id,event_name,start_time,end_time,location,agenda FROM events ORDER BY start_time DESC");
  $events = $st->fetchAll();
} else {
  $st = $pdo->prepare("
    SELECT e.id,e.event_name,e.start_time,e.end_time,e.location,e.agenda, ep.status
    FROM events e
    JOIN event_participants ep ON ep.event_id = e.id
    WHERE ep.user_id = ?
    ORDER BY e.start_time DESC
  ");
  $st->execute([(int)$u["id"]]);
  $events = $st->fetchAll();
}

$out = [];
foreach ($events as $e) {
  $out[] = [
    "id" => (int)$e["id"],
    "title" => $e["event_name"],
    "start" => date("c", strtotime($e["start_time"])),
    "end" => ($e["end_time"] ? date("c", strtotime($e["end_time"])) : null),
    "extendedProps" => [
      "location" => $e["location"],
      "agenda" => $e["agenda"],
      "status" => $e["status"] ?? null
    ]
  ];
}
json_out(["events"=>$out]);
