<?php
require_once 'includes/helpers.php';

if (is_logged_in()) { header('Location: community.php'); exit; }
if (empty($_SESSION['reg']['first_name'])) { header('Location: register_step2.php'); exit; }

$error = '';
$reg = $_SESSION['reg'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save user
    save_user([
        'username'      => $reg['username'],
        'password'      => $reg['password'],
        'first_name'    => $reg['first_name'],
        'last_name'     => $reg['last_name'],
        'email'         => $reg['email'],
        'phone'         => $reg['phone'] ?? '',
        'bio'           => $reg['bio'] ?? '',
        'profile_photo' => $reg['profile_photo'] ?? '',
        'created_at'    => date('Y-m-d H:i:s'),
    ]);

    // Save pets
    foreach (($reg['pets'] ?? []) as $pet) {
        if (empty($pet['pet_name'])) continue;
        save_pet([
            'id'         => '',
            'username'   => $reg['username'],
            'pet_name'   => $pet['pet_name'],
            'breed'      => $pet['breed'] ?? '',
            'age'        => $pet['age'] ?? '',
            'photo'      => $pet['photo'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    $_SESSION['username'] = $reg['username'];
    unset($_SESSION['reg']);
    header('Location: community.php?welcome=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Confirm (Step 5 of 5)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="wizard-wrapper">
  <div class="wizard-card">
    <div class="wizard-header">
      <h1>🐾 Almost there!</h1>
      <p>Review your info before joining the community</p>
    </div>

    <div class="step-indicator">
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot active">5</div>
    </div>

    <div class="wizard-body">
      <?php if (!empty($reg['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($reg['profile_photo']) ?>" class="confirm-avatar" alt="Your photo">
      <?php else: ?>
        <div style="text-align:center;font-size:4rem;margin-bottom:1rem;">🙂</div>
      <?php endif; ?>

      <div class="confirm-section">
        <h4>🔑 Account</h4>
        <div class="confirm-row">
          <span class="confirm-label">Username</span>
          <span class="confirm-value">@<?= htmlspecialchars($reg['username']) ?></span>
        </div>
      </div>

      <div class="confirm-section">
        <h4>👤 Personal Info</h4>
        <div class="confirm-row">
          <span class="confirm-label">Name</span>
          <span class="confirm-value"><?= htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']) ?></span>
        </div>
        <div class="confirm-row">
          <span class="confirm-label">Email</span>
          <span class="confirm-value"><?= htmlspecialchars($reg['email']) ?></span>
        </div>
        <?php if (!empty($reg['phone'])): ?>
        <div class="confirm-row">
          <span class="confirm-label">Phone</span>
          <span class="confirm-value"><?= htmlspecialchars($reg['phone']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($reg['bio'])): ?>
        <div class="confirm-row" style="flex-direction:column;gap:0.25rem;">
          <span class="confirm-label">Bio</span>
          <span class="confirm-value" style="font-size:0.88rem;"><?= nl2br(htmlspecialchars($reg['bio'])) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($reg['pets'])): ?>
      <div class="confirm-section">
        <h4>🐾 Pets</h4>
        <?php foreach ($reg['pets'] as $pet): ?>
          <?php if (empty($pet['pet_name'])) continue; ?>
          <div style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0;border-bottom:1px solid var(--border);">
            <?php if (!empty($pet['photo'])): ?>
              <img src="<?= htmlspecialchars($pet['photo']) ?>" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:2px solid var(--sand);" alt="<?= htmlspecialchars($pet['pet_name']) ?>">
            <?php else: ?>
              <div style="width:44px;height:44px;background:var(--sand);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🐾</div>
            <?php endif; ?>
            <div>
              <div style="font-weight:700;color:var(--deep);"><?= htmlspecialchars($pet['pet_name']) ?></div>
              <div style="font-size:0.82rem;color:var(--light-text);">
                <?= htmlspecialchars($pet['breed'] ?: 'Unknown breed') ?>
                <?php if (!empty($pet['age'])): ?> · <?= htmlspecialchars($pet['age']) ?> yrs<?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div style="background:rgba(122,158,126,0.1);border-radius:12px;padding:1rem;margin-bottom:1.5rem;border:1px solid rgba(122,158,126,0.3);">
        <p style="font-size:0.88rem;color:var(--sage);font-weight:600;margin:0;">
          ✅ Everything looks good! Click <strong>Join PetPals</strong> to complete your registration.
        </p>
      </div>

      <form method="POST">
        <div style="display:flex;justify-content:space-between;">
          <a href="register_step4.php" class="btn-secondary-pp" style="text-decoration:none;">← Back</a>
          <button type="submit" class="btn-primary-pp">Join PetPals 🎉</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
