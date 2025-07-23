#!/usr/bin/env php
<?php
// Pusty skrypt CGI
// Ten skrypt jest wymagany przez aplikację

header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'message' => 'CGI script loaded']);
?> 