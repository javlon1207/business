<?php
$cfg = require __DIR__ . "/../../config/config.php";

// CORS
header("Access-Control-Allow-Origin: " . $cfg["app"]["cors_allow_origin"]);
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(204); exit; }

require_once __DIR__ . "/../../src/response.php";

/**
 * Routing works in 2 modes:
 * 1) With mod_rewrite: /api/auth/login -> this file, REQUEST_URI contains /api/auth/login
 * 2) Without mod_rewrite: JS calls /public/api/index.php?r=/auth/login
 */
$method = $_SERVER["REQUEST_METHOD"];
$path = $_GET["r"] ?? null;

if ($path) {
  if ($path[0] !== "/") $path = "/" . $path;
} else {
  $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
  $path = preg_replace('#^/api/?#', '/', $path);
  $path = preg_replace('#^/public/api/?#', '/', $path);
  $path = preg_replace('#/index\.php$#', '/', $path);
  if ($path === "") $path = "/";
}

function route($methodNeedle, $pathNeedle, $handlerFile) {
  global $method, $path;
  if ($method === $methodNeedle && $path === $pathNeedle) {
    require __DIR__ . "/handlers/" . $handlerFile;
    exit;
  }
}

// Auth
route("POST", "/auth/login", "auth_login.php");
route("POST", "/auth/me", "auth_me.php");

// Admin
route("POST", "/admin/create_user", "admin_create_user.php");
route("POST", "/admin/create_event", "admin_create_event.php");
route("GET",  "/admin/event_detail", "admin_event_detail.php");
route("GET",  "/admin/users_list", "admin_users_list.php");
route("GET",  "/admin/report", "admin_report.php");

// Common
route("GET", "/events/list", "events_list.php");
route("GET", "/notifications/list", "notifications_list.php");
route("POST","/notifications/mark_read", "notifications_mark_read.php");
route("POST","/event/respond", "event_respond.php");

json_out(["error"=>"Not Found", "path"=>$path], 404);
