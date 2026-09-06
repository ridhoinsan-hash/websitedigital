<?php
require_once 'includes/config.php';
require_once 'includes/captcha.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'pembeli';
    header("Location: " . ($role === 'pembeli' ? 'produk.php' : 'index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha  = trim($_POST['captcha'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi!';
    } elseif (!verifyCaptcha($captcha)) {
        $error = 'Kode CAPTCHA salah atau kedaluwarsa!';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, nama_lengkap, role, status FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && ($user['status'] ?? 'aktif') === 'nonaktif') {
            $error = 'Akun nonaktif. Hubungi administrator.';
        } elseif ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['nama_lengkap']  = $user['nama_lengkap'];
            $_SESSION['role']          = $user['role'];
            header("Location: " . ($user['role'] === 'pembeli' ? 'produk.php' : 'index.php'));
            exit;
        } else {
            $error = 'Username atau password salah!';
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
    <title>Login - Digital Electronic Ridho</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0a0a12;
            overflow: hidden;
            position: relative;
        }

        .bg-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 70% 50% at 15% 85%, rgba(236, 72, 153, 0.22), transparent 60%),
                radial-gradient(ellipse 60% 45% at 90% 15%, rgba(59, 130, 246, 0.2), transparent 55%),
                radial-gradient(ellipse 40% 30% at 50% 50%, rgba(139, 92, 246, 0.08), transparent);
        }

        .login-ring {
            position: relative;
            z-index: 1;
            width: min(420px, 94vw);
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(145deg, #ec4899, #a855f7, #6366f1, #3b82f6);
            box-shadow:
                0 0 30px rgba(168, 85, 247, 0.35),
                0 0 60px rgba(59, 130, 246, 0.15);
        }

        .login-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 50% 25%, #1c1c2e 0%, #12121c 55%, #0c0c14 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 9% 12% 8%;
            text-align: center;
        }

        .brand {
            font-size: 0.62rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #c4b5fd;
            margin-bottom: 4px;
        }

        h1 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        .alert {
            width: 100%;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.72rem;
            margin-bottom: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
            text-align: left;
        }

        form { width: 100%; }

        .field {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 9px 14px;
            margin-bottom: 8px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field:focus-within {
            border-color: rgba(168, 85, 247, 0.65);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
        }

        .field .icon {
            color: #94a3b8;
            font-size: 0.9rem;
            flex-shrink: 0;
            line-height: 1;
        }

        .field input {
            flex: 1;
            min-width: 0;
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
        }

        .captcha-row {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            margin-bottom: 8px;
        }

        .captcha-row img {
            height: 38px;
            width: auto;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            background: #1e1b4b;
            flex-shrink: 0;
        }

        .captcha-row .btn-refresh {
            width: 38px;
            height: 38px;
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
            margin-bottom: 0;
            padding: 10px 14px;
        }

        .opts {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.68rem;
            color: #94a3b8;
        }

        .opts label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
        }

        .opts input[type="checkbox"] { accent-color: #a855f7; }
        .opts span.forgot { opacity: 0.55; cursor: default; }

        .btn-login {
            width: 100%;
            padding: 10px 18px;
            border: none;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            cursor: pointer;
            background: linear-gradient(90deg, #ec4899 0%, #a855f7 50%, #3b82f6 100%);
            box-shadow: 0 4px 18px rgba(168, 85, 247, 0.4);
            font-family: inherit;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(168, 85, 247, 0.55);
        }

        .divider {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 0 4px;
            color: #64748b;
            font-size: 0.62rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .demo {
            font-size: 0.6rem;
            color: #64748b;
            line-height: 1.4;
            padding: 0 4px;
            word-break: break-word;
        }

        .demo strong {
            color: #c4b5fd;
            font-weight: 600;
        }

        .signup {
            margin-top: 8px;
            font-size: 0.72rem;
            color: #94a3b8;
            padding-bottom: 2px;
        }

        .signup a {
            color: #f472b6;
            font-weight: 600;
            text-decoration: none;
        }

        .signup a:hover { text-decoration: underline; }

        @media (max-width: 440px) {
            .login-ring {
                aspect-ratio: auto;
                border-radius: 28px;
            }
            .login-inner {
                border-radius: 26px;
                height: auto;
                padding: 32px 22px 28px;
            }
            h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>

    <div class="login-ring">
        <div class="login-inner">
            <div class="brand">⚡ Digital Electronic Ridho</div>
            <h1>Login</h1>
            <p class="subtitle">Please sign in to continue</p>

            <?php if ($error): ?>
                <div class="alert"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="field">
                    <span class="icon">👤</span>
                    <input type="text" name="username" placeholder="Username" required autofocus
                           value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="field">
                    <span class="icon">🔒</span>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="captcha-row">
                    <img src="captcha_image.php?t=<?= time(); ?>" alt="CAPTCHA" id="captchaImg"
                         title="Klik untuk ganti kode" onclick="refreshCaptcha()">
                    <button type="button" class="btn-refresh" onclick="refreshCaptcha()" title="Refresh">🔄</button>
                    <div class="field">
                        <input type="text" name="captcha" id="captcha" placeholder="CAPTCHA"
                               required maxlength="8" autocomplete="off"
                               style="text-transform: uppercase;">
                    </div>
                </div>

                <div class="opts">
                    <label>
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                    <span class="forgot">Forgot password?</span>
                </div>

                <button type="submit" class="btn-login">Login →</button>
            </form>

            <div class="divider">demo accounts</div>
            <div class="demo">
                <strong>admin</strong>/admin123 ·
                <strong>kasir</strong>/kasir123 ·
                <strong>pembeli</strong>/pembeli123
            </div>

            <p class="signup">Don't have an account? <a href="register.php">Sign up</a></p>
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