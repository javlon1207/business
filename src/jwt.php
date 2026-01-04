<?php
function b64url_encode(string $data): string {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function b64url_decode(string $data): string {
  $remainder = strlen($data) % 4;
  if ($remainder) $data .= str_repeat('=', 4 - $remainder);
  return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_sign(array $payload, string $secret): string {
  $header = ["alg"=>"HS256","typ"=>"JWT"];
  $segments = [
    b64url_encode(json_encode($header)),
    b64url_encode(json_encode($payload, JSON_UNESCAPED_UNICODE))
  ];
  $data = implode(".", $segments);
  $sig = hash_hmac("sha256", $data, $secret, true);
  $segments[] = b64url_encode($sig);
  return implode(".", $segments);
}

function jwt_verify(string $token, string $secret): array {
  $parts = explode(".", $token);
  if (count($parts) !== 3) throw new Exception("Invalid token format");
  [$h, $p, $s] = $parts;

  $data = $h . "." . $p;
  $sig = b64url_decode($s);
  $expected = hash_hmac("sha256", $data, $secret, true);
  if (!hash_equals($expected, $sig)) throw new Exception("Invalid signature");

  $payload = json_decode(b64url_decode($p), true);
  if (!is_array($payload)) throw new Exception("Invalid payload");

  if (isset($payload["exp"]) && time() > (int)$payload["exp"]) {
    throw new Exception("Token expired");
  }
  return $payload;
}
