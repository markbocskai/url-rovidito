<?php
declare(strict_types=1);

session_start();

const ALAP_URL = 'http://localhost:8000';
const FOGLALT_KODOK = ['admin', 'api', 'stat', 'login', 'index', 'r'];


function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        
        $pdo = new PDO('sqlite:' . __DIR__ . '/adatbazis.sqlite', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS link (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                kod TEXT NOT NULL UNIQUE,
                cel TEXT NOT NULL,
                kattintas INTEGER NOT NULL DEFAULT 0,
                letrehozva DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
    return $pdo;
}

function e(string $s): string { 
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}

function csrf_token(): string { 
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); 
}

function rovid_kod(int $hossz = 6): string {
    $abc = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $k = '';
    $max = strlen($abc) - 1;
    for ($i = 0; $i < $hossz; $i++) {
        $k .= $abc[random_int(0, $max)];
    }
    return $k;
}

function ervenyes_cel(string $url): bool {
    if (strlen($url) > 2048 || !filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    $sema = strtolower((string)parse_url($url, PHP_URL_SCHEME));
    $host = (string)parse_url($url, PHP_URL_HOST);
    
    return in_array($sema, ['http', 'https'], true) && $host !== '';
}