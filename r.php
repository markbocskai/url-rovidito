<?php
require_once __DIR__ . '/common.php';

$kod = (string)($_GET['k'] ?? '');

if (!preg_match('/^[A-Za-z0-9_-]{3,30}$/', $kod)) {
    http_response_code(404);
    exit('Ismeretlen rövid link.');
}


$stmt = db()->prepare('UPDATE link SET kattintas = kattintas + 1 WHERE kod = ?');
$stmt->execute([$kod]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    exit('Ismeretlen rövid link.');
}


$celStmt = db()->prepare('SELECT cel FROM link WHERE kod = ?');
$celStmt->execute([$kod]);
$cel = $celStmt->fetchColumn();


header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Location: ' . $cel, true, 302);
exit;