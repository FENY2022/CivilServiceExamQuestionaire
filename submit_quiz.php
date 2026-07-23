<?php
require_once __DIR__ . '/config.php';
require_login();
header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input') ?: '{}', true);
$score = (int)($payload['score'] ?? 0);
$total = max(1, (int)($payload['total'] ?? 1));
$percent = round(($score / $total) * 100, 2);

echo json_encode([
    'score' => $score,
    'total' => $total,
    'percent' => $percent,
    'passed' => $percent >= 80,
]);
