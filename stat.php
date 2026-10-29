<?php
require_once __DIR__ . '/common.php';

$kod = (string)($_GET['k'] ?? '');

$stmt = db()->prepare('SELECT kod, cel, kattintas, letrehozva FROM link WHERE kod = ?');
$stmt->execute([$kod]);
$l = $stmt->fetch();

if (!$l) {
    http_response_code(404);
    exit('Nincs ilyen link.');
}

$rovid = ALAP_URL . '/r.php?k=' . rawurlencode($l['kod']);
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statisztika – <?= e($l['kod']) ?></title>
    <style>
        :root {
            --bg: 
            --card-bg: 
            --accent: 
            --text: 
            --text-muted: 
            --border: 
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 1.6rem;
            margin-bottom: 24px;
            text-align: center;
            color: var(--text);
        }

        .link-box {
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-bottom: 24px;
        }

        .short-url {
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: bold;
            text-decoration: none;
            word-break: break-all;
        }

        .short-url:hover {
            text-decoration: underline;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--accent);
            margin-top: 4px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-row {
            margin-bottom: 24px;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            word-break: break-all;
            color: var(--text);
        }

        .btn-back {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            background-color: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background-color: var(--border);
            color: var(--text);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rövid link elkészült!</h1>
        
        <div class="link-box">
            <a href="<?= e($rovid) ?>" target="_blank" class="short-url"><?= e($rovid) ?></a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Kattintások</div>
                <div class="stat-value"><?= (int)$l['kattintas'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Létrehozva</div>
                <div class="stat-value" style="font-size: 0.95rem; margin-top: 10px;"><?= e(date('Y.m.d', strtotime($l['letrehozva']))) ?></div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Eredeti cél URL:</div>
            <div class="info-value"><?= e($l['cel']) ?></div>
        </div>

        <a href="/index.php" class="btn-back">← Új link rövidítése</a>
    </div>
</body>
</html>