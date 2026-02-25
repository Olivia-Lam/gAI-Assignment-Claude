<?php
require_once 'includes/helpers.php';
require_login();

$users = get_all_users();
$currentUser = get_user($_SESSION['username']);
$welcome = isset($_GET['welcome']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Community</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar-petpals">
  <a href="community.php" class="nav-brand"><span>🐾</span> PetPals</a>
  <div class="nav-links">
    <div class="nav-user-info">
      <?php if (!empty($currentUser['profile_photo'])): ?>
        <img src="<?= htmlspecialchars($currentUser['profile_photo']) ?>" class="nav-user-avatar" alt="You">
      <?php endif; ?>
      <a href="profile.php?u=<?= urlencode($_SESSION['username']) ?>">
        <?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
      </a>
    </div>
    <a href="edit.php">Edit Profile</a>
    <a href="logout.php">Sign Out</a>
  </div>
</nav>

<!-- Hero -->
<div class="page-hero">
  <h1>Welcome to the Community 🐾</h1>
  <p>Connect with fellow pet lovers from around the world</p>
</div>

<?php if ($welcome): ?>
<div style="max-width:860px;margin:-1.5rem auto 1rem;padding:0 2rem;">
  <div class="alert-pp alert-success">
    🎉 Welcome aboard, <?= htmlspecialchars($currentUser['first_name']) ?>! Your profile is all set.
  </div>
</div>
<?php endif; ?>

<div class="members-grid">
  <?php foreach ($users as $user): ?>
  <?php
    $pets = get_pets_for_user($user['username']);
    $avatar = !empty($user['profile_photo']) ? $user['profile_photo'] : null;
  ?>
  <a href="profile.php?u=<?= urlencode($user['username']) ?>" class="member-card">
    <div class="member-card-top">
      <?php if ($avatar): ?>
        <img src="<?= htmlspecialchars($avatar) ?>" class="member-avatar" alt="<?= htmlspecialchars($user['first_name']) ?>">
      <?php else: ?>
        <div class="member-avatar" style="display:flex;align-items:center;justify-content:center;font-size:2rem;">🙂</div>
      <?php endif; ?>
    </div>
    <div class="member-body">
      <div class="member-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
      <div class="member-handle">@<?= htmlspecialchars($user['username']) ?></div>
      <?php if (!empty($user['bio'])): ?>
        <div style="font-size:0.85rem;color:var(--mid);line-height:1.5;">
          <?= htmlspecialchars(mb_substr($user['bio'], 0, 90)) ?><?= mb_strlen($user['bio']) > 90 ? '…' : '' ?>
        </div>
      <?php endif; ?>
      <?php if ($pets): ?>
        <div class="pet-badges">
          <?php foreach (array_slice($pets, 0, 3) as $pet): ?>
            <span class="pet-badge">🐾 <?= htmlspecialchars($pet['pet_name']) ?></span>
          <?php endforeach; ?>
          <?php if (count($pets) > 3): ?>
            <span class="pet-badge">+<?= count($pets) - 3 ?> more</span>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div style="margin-top:0.6rem;font-size:0.82rem;color:var(--light-text);">No pets listed yet</div>
      <?php endif; ?>
    </div>
  </a>
  <?php endforeach; ?>
</div>

</body>
</html>
