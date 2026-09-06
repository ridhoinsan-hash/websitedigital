<?php
require_once 'includes/config.php';
require_once 'includes/captcha.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'pembeli';
    header("Location: " . ($role === 'pembeli' ? 'produk.php' : 'index.php'));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $password     = $_POST['password'] ?? '';
    $password2    = $_POST['password2'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $captcha      = trim($_POST['captcha'] ?? '');

    if ($username === '' || $password === '' || $nama_lengkap === '') {
        $error = 'Username, password, dan nama lengkap wajib diisi!';
    } elseif (strlen($username) < 4) {
        $error = 'Username minimal 4 karakter!';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh huruf, angka, dan underscore!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $password2) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (!verifyCaptcha($captcha)) {
        $error = 'Kode CAPTCHA salah atau kedaluwarsa!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($stmt)) > 0) {
            $error = 'Username sudah dipakai!';
        } else {
            if ($email !== '') {
                $stmtE = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND email != ''");
                mysqli_stmt_bind_param($stmtE, "s", $email);
                mysqli_stmt_execute($stmtE);
                if (mysqli_num_rows(mysqli_stmt_get_result($stmtE)) > 0) {
                    $error = 'Email sudah terdaftar!';
                }
                mysqli_stmt_close($stmtE);
            }
            if ($error === '') {
                $hash   = password_hash($password, PASSWORD_DEFAULT);
                $role   = 'pembeli';
                $status = 'aktif';
                $stmt2  = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "sssssss", $username, $hash, $nama_lengkap, $email, $no_hp, $role, $status);
                if (mysqli_stmt_execute($stmt2)) {
                    $success = 'Pendaftaran berhasil! Silakan login.';
                    $_POST = [];
                } else {
                    $error = 'Gagal mendaftar: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt2);
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sign Up - Digital Electronic Ridho</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0a0a12;
            padding: 28px 14px;
            position: relative;
        }

        .bg-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 70% 50% at 15% 85%, rgba(236, 72, 153, 0.22), transparent 60%),
                radial-gradient(ellipse 60% 45% at 90% 15%, rgba(59, 130, 246, 0.2), transparent 55%),
                radial-gradient(ellipse 40% 30% at 50% 40%, rgba(139, 92, 246, 0.08), transparent);
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            border-radius: 28px;
            padding: 2px;
            background: linear-gradient(145deg, #ec4899, #a855f7, #6366f1, #3b82f6);
            box-shadow:
                0 0 30px rgba(168, 85, 247, 0.3),
                0 0 60px rgba(59, 130, 246, 0.12);
        }

        .card-inner {
            background: radial-gradient(circle at 50% 0%, #1c1c2e 0%, #12121c 55%, #0c0c14 100%);
            border-radius: 26px;
            padding: 28px 24px 24px;
        }

        .brand {
            text-align: center;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #c4b5fd;
            margin-bottom: 6px;
        }

        h1 {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .alert-err {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-bottom: 14px;
        }

        .alert-ok {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.35);
            color: #86efac;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-bottom: 14px;
        }

        .alert-ok a { color: #86efac; font-weight: 700; }

        .form-group { margin-bottom: 12px; }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .field {
            display: flex;
            align-items: center;
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 11px 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field:focus-within {
            border-color: rgba(168, 85, 247, 0.65);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
        }

        .field input {
            flex: 1;
            min-width: 0;
            width: 100%;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            color: #f1f5f9;
            font-size: 0.9rem;
            font-family: inherit;
            -webkit-appearance: none;
            appearance: none;
        }

        .field input::placeholder { color: #64748b; }

        .field input:-webkit-autofill,
        .field input:-webkit-autofill:hover,
        .field input:-webkit-autofill:focus {
            -webkit-text-fill-color: #f1f5f9;
            -webkit-box-shadow: 0 0 0 40px #1a1a28 inset !important;
            transition: background-color 9999s ease-in-out 0s;
            caret-color: #f1f5f9;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .captcha-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .captcha-row img {
            height: 42px;
            width: auto;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            background: #1e1b4b;
            flex-shrink: 0;
        }

        .captcha-row .btn-refresh {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #e2e8f0;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
        }

        .captcha-row .btn-refresh:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .captcha-row .field {
            flex: 1;
            padding: 10px 14px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            margin-top: 8px;
            border: none;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(90deg, #ec4899 0%, #a855f7 50%, #3b82f6 100%);
            box-shadow: 0 4px 18px rgba(168, 85, 247, 0.4);
            font-family: inherit;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(168, 85, 247, 0.55);
        }

        .links {
            text-align: center;
            margin-top: 16px;
            font-size: 0.82rem;
            color: #94a3b8;
        }

        .links a {
            color: #f472b6;
            font-weight: 600;
            text-decoration: none;
        }

        .links a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .row { grid-template-columns: 1fr; }
            .card-inner { padding: 24px 18px 20px; }
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>

    <div class="card">
        <div class="card-inner">
            <div class="brand">⚡ Digital Electronic Ridho</div>
            <h1>Sign Up</h1>
            <p class="subtitle">Create your account to continue</p>

            <?php if ($error): ?>
                <div class="alert-err"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert-ok">
                    <?= htmlspecialchars($success); ?><br>
                    <a href="login.php">→ Login sekarang</a>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap *</label>
                    <div class="field">
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               placeholder="Nama lengkap"
                               value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username *</label>
                    <div class="field">
                        <input type="text" id="username" name="username" required minlength="4"
                               placeholder="min. 4 karakter"
                               value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="field">
                            <input type="password" id="password" name="password" required minlength="6"
                                   placeholder="Min. 6 karakter">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password2">Ulangi Password *</label>
                        <div class="field">
                            <input type="password" id="password2" name="password2" required minlength="6"
                                   placeholder="Ulangi">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="field">
                            <input type="email" id="email" name="email" placeholder="opsional"
                                   value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <div class="field">
                            <input type="text" id="no_hp" name="no_hp" placeholder="08xxx"
                                   value="<?= htmlspecialchars($_POST['no_hp'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="captcha">CAPTCHA *</label>
                    <div class="captcha-row">
                        <img src="captcha_image.php?t=<?= time(); ?>" alt="CAPTCHA" id="captchaImg"
                             title="Klik untuk ganti kode" onclick="refreshCaptcha()">
                        <button type="button" class="btn-refresh" onclick="refreshCaptcha()" title="Refresh">🔄</button>
                        <div class="field">
                            <input type="text" id="captcha" name="captcha" required maxlength="8"
                                   placeholder="KODE" autocomplete="off"
                                   style="text-transform: uppercase;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign Up →</button>
            </form>
            <?php endif; ?>

            <div class="links">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>

    <script>
    function refreshCaptcha() {
        document.getElementById('captchaImg').src = 'captcha_image.php?t=' + Date.now();
        document.getElementById('captcha').value = '';
    }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>