<?php
require_once __DIR__ . "/../../../src/auth.php";
$u = auth_user();
json_out(["user"=>$u]);
