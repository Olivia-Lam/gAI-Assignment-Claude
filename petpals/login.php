<?php
require_once 'includes/helpers.php';

if (is_logged_in()) {
    header('Location: community.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = get_user($username);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        header('Location: community.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Login</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-top">
      <div class="auth-logo">🐾 PetPals</div>
      <div class="auth-sub">The community for pet lovers</div>
    </div>
    <div class="auth-body">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin-bottom:1.5rem;color:var(--deep);">Welcome back!</h2>

      <?php if ($error): ?>
        <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="your_username" required autocomplete="username">
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn-primary-pp w-100" style="width:100%;">Sign In</button>
      </form>

      <div class="text-divider" style="margin:1.5rem 0;">or</div>

      <p style="text-align:center;color:var(--mid);font-size:0.9rem;">
        New to PetPals? <a href="register.php" class="link-pp">Join the community →</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
