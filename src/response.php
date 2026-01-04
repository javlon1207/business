<?php
function json_input() : array {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function json_out($data, int $status=200) : void {
  http_response_code($status);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_fields(array $data, array $fields) : void {
  foreach ($fields as $f) {
    if (!array_key_exists($f, $data)) json_out(["error"=>"Missing field: $f"], 400);
  }
}
