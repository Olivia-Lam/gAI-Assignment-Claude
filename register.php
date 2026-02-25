<?php
require_once 'includes/helpers.php';

if (is_logged_in()) {
    header('Location: community.php');
    exit;
}

// Clear any existing registration session
unset($_SESSION['reg']);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username may only contain letters, numbers, and underscores.';
    } elseif (username_exists($username)) {
        $error = 'That username is already taken. Please choose another.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $_SESSION['reg'] = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        header('Location: register_step2.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Join (Step 1 of 5)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="wizard-wrapper">
  <div class="wizard-card">
    <div class="wizard-header">
      <h1>🐾 Join PetPals</h1>
      <p>Let's set up your account in just a few steps</p>
    </div>

    <!-- Step indicator -->
    <div class="step-indicator">
      <div class="step-dot active">1</div>
      <div class="step-line"></div>
      <div class="step-dot">2</div>
      <div class="step-line"></div>
      <div class="step-dot">3</div>
      <div class="step-line"></div>
      <div class="step-dot">4</div>
      <div class="step-line"></div>
      <div class="step-dot">5</div>
    </div>

    <div class="wizard-body">
      <h2 class="section-title">Create your account</h2>

      <?php if ($error): ?>
        <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username *</label>
          <input type="text" name="username" class="form-control"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 placeholder="e.g. fluffy_lover99" required minlength="3"
                 pattern="[a-zA-Z0-9_]+" autocomplete="username">
          <small style="color:var(--light-text);font-size:0.8rem;">Letters, numbers & underscores only. Cannot be changed later.</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-control"
                 placeholder="At least 6 characters" required minlength="6" autocomplete="new-password">
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="confirm" class="form-control"
                 placeholder="Repeat your password" required autocomplete="new-password">
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;">
          <a href="login.php" style="color:var(--light-text);font-size:0.9rem;text-decoration:none;">Already have an account?</a>
          <button type="submit" class="btn-primary-pp">Next →</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
