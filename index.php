<?php
require_once __DIR__ . '/common.php';

$hiba = null;
$url = '';
$alias = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Érvénytelen CSRF token.');
    }

    $url = trim((string)($_POST['url'] ?? ''));
    $alias = trim((string)($_POST['alias'] ?? ''));

    if (!ervenyes_cel($url)) {
        $hiba = 'Csak érvényes http:// vagy https:// cím adható meg.';
    } 
    elseif ($alias !== '' && (!preg_match('/^[A-Za-z0-9_-]{3,30}$/', $alias) || in_array(strtolower($alias), FOGLALT_KODOK, true))) {
        $hiba = 'Az alias 3–30 karakter (betű, szám, - és _), és nem lehet foglalt szó.';
    } 
    else {
        $probalkozasok = ($alias !== '') ? 1 : 5;
        
        for ($i = 0; $i < $probalkozasok; $i++) {
            $kod = ($alias !== '') ? $alias : rovid_kod();
            try {
                $stmt = db()->prepare('INSERT INTO link (kod, cel) VALUES (?, ?)');
                $stmt->execute([$kod, $url]);
                
                header('Location: /stat.php?k=' . rawurlencode($kod), true, 303);
                exit;
            } catch (PDOException $ex) {
                if ($ex->getCode() !== '23000' && $ex->getCode() !== '19') {
                    throw $ex;
                }
            }
        }
        $hiba = ($alias !== '') ? 'Ez az alias már foglalt.' : 'Nem sikerült egyedi kódot generálni, próbáld újra.';
    }
    
    http_response_code(422);
}
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL-rövidítő</title>
    <style>
        :root {
            --bg: 
            --card-bg: 
            --accent: 
            --accent-hover: 
            --text: 
            --text-muted: 
            --error: 
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
            max-width: 480px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            text-align: center;
            color: var(--text);
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            text-align: center;
            margin-bottom: 28px;
        }

        .alert {
            background-color: rgba(244, 63, 94, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        input {
            width: 100%;
            padding: 12px 16px;
            background-color: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: var(--accent);
            color: 
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        button:hover {
            background-color: var(--accent-hover);
            color: 
        }

        button:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>URL-rövidítő</h1>
        <p class="subtitle">Illeszd be a hosszú linket a rövidítéshez</p>
        
        <?php if ($hiba): ?>
            <div class="alert"><?= e($hiba) ?></div>
        <?php endif; ?>

        <form method="post" action="/index.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            
            <div class="form-group">
                <label for="url">Hosszú URL *</label>
                <input id="url" name="url" type="url" required value="<?= e($url) ?>" placeholder="https://umfst.ro/..." autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="alias">Egyedi alias (választható)</label>
                <input id="alias" name="alias" pattern="[A-Za-z0-9_-]{3,30}" placeholder="pl. sajtos-pizza" value="<?= e($alias) ?>" autocomplete="off">
            </div>
            
            <button type="submit">Link Generálása</button>
        </form>
    </div>
</body>
</html>