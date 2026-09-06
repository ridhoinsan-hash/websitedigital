<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $konfirmasi = $_POST['konfirmasi'] ?? '';
    if ($konfirmasi === 'ya') {
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        $role = $_SESSION['role'] ?? 'pembeli';
        header("Location: " . ($role === 'pembeli' ? 'produk.php' : 'index.php'));
        exit;
    }
}
$nama = $_SESSION['nama_lengkap'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Logout - Digital Electronic Ridho</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #050508;
            overflow: hidden;
            position: relative;
        }
        body::before, body::after {
            content: '';
            position: absolute;
            width: 120%;
            height: 60%;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            background: radial-gradient(ellipse, rgba(236,72,153,0.3) 0%, transparent 70%);
            top: -20%; right: -30%;
        }
        body::after {
            background: radial-gradient(ellipse, rgba(59,130,246,0.28) 0%, transparent 70%);
            bottom: -25%; left: -25%;
        }
        .wave {
            position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 80% 50% at 80% 80%, rgba(236,72,153,0.12), transparent),
                radial-gradient(ellipse 60% 40% at 20% 20%, rgba(99,102,241,0.18), transparent);
        }

        .logout-ring {
            position: relative;
            z-index: 1;
            width: min(400px, 90vw);
            aspect-ratio: 1;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #ec4899, #8b5cf6, #3b82f6, #06b6d4);
            box-shadow:
                0 0 40px rgba(236,72,153,0.35),
                0 0 80px rgba(59,130,246,0.2);
            animation: ringPulse 4s ease-in-out infinite;
        }
        @keyframes ringPulse {
            0%, 100% { box-shadow: 0 0 40px rgba(236,72,153,0.35), 0 0 80px rgba(59,130,246,0.2); }
            50% { box-shadow: 0 0 55px rgba(236,72,153,0.5), 0 0 100px rgba(59,130,246,0.3); }
        }
        .logout-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 50% 30%, #1a1a2e 0%, #0d0d14 60%, #08080c 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 14% 12%;
            text-align: center;
        }
        .icon-out {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(236,72,153,0.25), rgba(139,92,246,0.25));
            border: 2px solid rgba(244,114,182,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 16px;
        }
        .logout-inner h1 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }
        .logout-inner p {
            font-size: 0.88rem;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 8px;
        }
        .logout-inner .name {
            color: #e9d5ff;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 22px;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 240px;
        }
        .actions form { width: 100%; }
        .btn-ya {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(90deg, #ec4899 0%, #8b5cf6 50%, #3b82f6 100%);
            box-shadow: 0 4px 20px rgba(139,92,246,0.4);
            font-family: inherit;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-ya:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(139,92,246,0.55);
        }
        .btn-tidak {
            width: 100%;
            padding: 12px 20px;
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #e2e8f0;
            cursor: pointer;
            background: rgba(255,255,255,0.06);
            font-family: inherit;
            transition: background 0.15s;
        }
        .btn-tidak:hover { background: rgba(255,255,255,0.12); }

        .brand-mini {
            margin-top: 20px;
            font-size: 0.68rem;
            color: #64748b;
            letter-spacing: 0.04em;
        }

        @media (max-width: 420px) {
            .logout-ring { aspect-ratio: auto; border-radius: 28px; }
            .logout-inner { border-radius: 26px; padding: 36px 24px; height: auto; }
        }
    </style>
</head>
<body>
    <div class="wave"></div>
    <div class="logout-ring">
        <div class="logout-inner">
            <div class="icon-out">🚪</div>
            <h1>Logout</h1>
            <p>Apakah Anda yakin ingin keluar?</p>
            <div class="name"><?= htmlspecialchars($nama); ?></div>
            <div class="actions">
                <form method="POST">
                    <input type="hidden" name="konfirmasi" value="ya">
                    <button type="submit" class="btn-ya">Ya, Logout →</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="konfirmasi" value="tidak">
                    <button type="submit" class="btn-tidak">Tidak, Kembali</button>
                </form>
            </div>
            <div class="brand-mini">⚡ Digital Electronic Ridho</div>
        </div>
    </div>
</body>
</html>
