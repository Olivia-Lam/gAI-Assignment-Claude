<?php
require_once 'includes/helpers.php';

if (is_logged_in()) { header('Location: community.php'); exit; }
if (empty($_SESSION['reg']['username'])) { header('Location: register.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio   = trim($_POST['bio'] ?? '');

    if (!$first || !$last) {
        $error = 'First and last name are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $_SESSION['reg']['first_name'] = $first;
        $_SESSION['reg']['last_name']  = $last;
        $_SESSION['reg']['email']      = $email;
        $_SESSION['reg']['phone']      = $phone;
        $_SESSION['reg']['bio']        = $bio;
        header('Location: register_step3.php');
        exit;
    }
}
$d = $_SESSION['reg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Personal Info (Step 2 of 5)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="wizard-wrapper">
  <div class="wizard-card">
    <div class="wizard-header">
      <h1>🐾 Join PetPals</h1>
      <p>Tell us a little about yourself</p>
    </div>

    <div class="step-indicator">
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot active">2</div>
      <div class="step-line"></div>
      <div class="step-dot">3</div>
      <div class="step-line"></div>
      <div class="step-dot">4</div>
      <div class="step-line"></div>
      <div class="step-dot">5</div>
    </div>

    <div class="wizard-body">
      <h2 class="section-title">Personal Information</h2>

      <?php if ($error): ?>
        <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;" class="mb-3">
          <div>
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control"
                   value="<?= htmlspecialchars($d['first_name'] ?? $_POST['first_name'] ?? '') ?>"
                   placeholder="Jane" required>
          </div>
          <div>
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control"
                   value="<?= htmlspecialchars($d['last_name'] ?? $_POST['last_name'] ?? '') ?>"
                   placeholder="Smith" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address *</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($d['email'] ?? $_POST['email'] ?? '') ?>"
                 placeholder="jane@example.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phone" class="form-control"
                 value="<?= htmlspecialchars($d['phone'] ?? $_POST['phone'] ?? '') ?>"
                 placeholder="+1 555 000 0000">
        </div>
        <div class="mb-4">
          <label class="form-label">About You</label>
          <textarea name="bio" class="form-control"
                    placeholder="I love my golden retriever Max and hiking on weekends..."><?= htmlspecialchars($d['bio'] ?? $_POST['bio'] ?? '') ?></textarea>
        </div>

        <div style="display:flex;justify-content:space-between;">
          <a href="register.php" class="btn-secondary-pp" style="text-decoration:none;">← Back</a>
          <button type="submit" class="btn-primary-pp">Next →</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
