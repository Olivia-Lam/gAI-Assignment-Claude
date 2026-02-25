<?php
require_once 'includes/helpers.php';
require_login();

$username = $_GET['u'] ?? '';
$user = get_user($username);
if (!$user) { header('Location: community.php'); exit; }
$pets = get_pets_for_user($username);
$isOwn = ($_SESSION['username'] === $username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar-petpals">
  <a href="community.php" class="nav-brand"><span>🐾</span> PetPals</a>
  <div class="nav-links">
    <a href="community.php">← Community</a>
    <?php if ($isOwn): ?>
      <a href="edit.php">Edit Profile</a>
    <?php endif; ?>
    <a href="logout.php">Sign Out</a>
  </div>
</nav>

<div class="profile-wrapper" style="margin-top:2rem;">

  <!-- Profile card -->
  <div class="profile-card">
    <div class="profile-header-bg">
      <?php if (!empty($user['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($user['profile_photo']) ?>" class="profile-avatar-lg" alt="<?= htmlspecialchars($user['first_name']) ?>">
      <?php else: ?>
        <div class="profile-avatar-lg" style="display:flex;align-items:center;justify-content:center;font-size:3rem;">🙂</div>
      <?php endif; ?>
    </div>

    <div class="profile-info">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.5rem;">
        <div>
          <div class="profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
          <div class="profile-username">@<?= htmlspecialchars($user['username']) ?></div>
        </div>
        <?php if ($isOwn): ?>
          <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a href="edit.php" class="btn-sage" style="text-decoration:none;padding:0.5rem 1.25rem;font-size:0.88rem;">✏️ Edit</a>
            <form method="POST" action="delete.php" onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.')">
              <button type="submit" class="btn-danger-pp">🗑️ Delete Account</button>
            </form>
          </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($user['bio'])): ?>
        <p class="profile-bio" style="margin-top:1rem;"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
      <?php endif; ?>

      <div class="info-grid">
        <?php if (!empty($user['email'])): ?>
        <div class="info-chip">
          <div class="info-chip-label">Email</div>
          <div class="info-chip-val"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($user['phone'])): ?>
        <div class="info-chip">
          <div class="info-chip-label">Phone</div>
          <div class="info-chip-val"><?= htmlspecialchars($user['phone']) ?></div>
        </div>
        <?php endif; ?>
        <div class="info-chip">
          <div class="info-chip-label">Member Since</div>
          <div class="info-chip-val"><?= date('M Y', strtotime($user['created_at'])) ?></div>
        </div>
        <div class="info-chip">
          <div class="info-chip-label">Pets</div>
          <div class="info-chip-val"><?= count($pets) ?> companion<?= count($pets) !== 1 ? 's' : '' ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pets section -->
  <?php if ($pets): ?>
  <div class="profile-card" style="padding:2rem;">
    <h2 class="section-title" style="margin-bottom:1.25rem;">🐾 <?= htmlspecialchars($user['first_name']) ?>'s Pets</h2>
    <div class="pet-cards-grid">
      <?php foreach ($pets as $pet): ?>
      <div class="pet-card">
        <?php if (!empty($pet['photo'])): ?>
          <img src="<?= htmlspecialchars($pet['photo']) ?>" class="pet-card-img" alt="<?= htmlspecialchars($pet['pet_name']) ?>">
        <?php else: ?>
          <div class="pet-card-img">🐾</div>
        <?php endif; ?>
        <div class="pet-card-body">
          <div class="pet-card-name"><?= htmlspecialchars($pet['pet_name']) ?></div>
          <?php if (!empty($pet['breed'])): ?>
            <div class="pet-card-detail"><?= htmlspecialchars($pet['breed']) ?></div>
          <?php endif; ?>
          <?php if (!empty($pet['age'])): ?>
            <div class="pet-card-detail"><?= htmlspecialchars($pet['age']) ?> year<?= $pet['age'] != 1 ? 's' : '' ?> old</div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php elseif ($isOwn): ?>
  <div style="text-align:center;padding:2rem;background:var(--warm-white);border-radius:20px;box-shadow:0 4px 20px var(--shadow);">
    <div style="font-size:3rem;margin-bottom:0.5rem;">🐾</div>
    <p style="color:var(--light-text);margin-bottom:1rem;">You haven't added any pets yet.</p>
    <a href="edit.php#pets" class="btn-primary-pp" style="text-decoration:none;">Add a Pet</a>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
