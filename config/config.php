<?php
return [
  "db" => [
    "host" => "localhost",
    "name" => "business_calendar",
    "user" => "root",
    "pass" => "",
    "charset" => "utf8mb4"
  ],
  "jwt" => [
    "secret" => "CHANGE_ME_TO_A_LONG_RANDOM_SECRET",
    "issuer" => "business-calendar",
    "ttl_seconds" => 60 * 60 * 12 // 12 hours
  ],
  "app" => [
    "cors_allow_origin" => "*" // for local dev
  ]
];
