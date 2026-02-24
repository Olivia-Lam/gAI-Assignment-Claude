<?php
require_once 'includes/helpers.php';
require_login();

$user = get_user($_SESSION['username']);
$pets = get_pets_for_user($_SESSION['username']);
$success = '';
$error = '';

// ── Handle Personal Info update ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'personal') {
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio   = trim($_POST['bio'] ?? '');

        if (!$first || !$last) { $error = 'Name fields are required.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Valid email required.'; }
        else {
            $photo = $user['profile_photo'];
            if (!empty($_FILES['photo']['name'])) {
                $p = handle_upload($_FILES['photo'], 'profiles');
                if ($p) $photo = $p;
                else $error = 'Invalid image file.';
            }
            if (!$error) {
                save_user(array_merge($user, [
                    'first_name'    => $first,
                    'last_name'     => $last,
                    'email'         => $email,
                    'phone'         => $phone,
                    'bio'           => $bio,
                    'profile_photo' => $photo,
                ]));
                $user = get_user($_SESSION['username']);
                $success = 'Profile updated successfully!';
            }
        }

    } elseif ($_POST['action'] === 'password') {
        $cur  = $_POST['current_password'] ?? '';
        $new  = $_POST['new_password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';
        if (!password_verify($cur, $user['password'])) { $error = 'Current password is incorrect.'; }
        elseif (strlen($new) < 6) { $error = 'New password must be at least 6 characters.'; }
        elseif ($new !== $conf) { $error = 'Passwords do not match.'; }
        else {
            save_user(array_merge($user, ['password' => password_hash($new, PASSWORD_DEFAULT)]));
            $user = get_user($_SESSION['username']);
            $success = 'Password changed successfully!';
        }

    } elseif ($_POST['action'] === 'add_pet') {
        $pname = trim($_POST['pet_name'] ?? '');
        if ($pname) {
            $photo = '';
            if (!empty($_FILES['pet_photo']['name'])) {
                $p = handle_upload($_FILES['pet_photo'], 'pets');
                if ($p) $photo = $p;
            }
            save_pet([
                'id'         => '',
                'username'   => $_SESSION['username'],
                'pet_name'   => $pname,
                'breed'      => trim($_POST['breed'] ?? ''),
                'age'        => trim($_POST['pet_age'] ?? ''),
                'photo'      => $photo,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $pets = get_pets_for_user($_SESSION['username']);
            $success = 'Pet added!';
        } else { $error = 'Pet name is required.'; }

    } elseif ($_POST['action'] === 'edit_pet') {
        $pid = $_POST['pet_id'] ?? '';
        $pname = trim($_POST['pet_name'] ?? '');
        if ($pid && $pname) {
            // Find existing pet photo
            $existing = array_filter(get_all_pets(), fn($p) => $p['id'] === $pid);
            $existing = array_values($existing);
            $photo = $existing ? $existing[0]['photo'] : '';
            if (!empty($_FILES['pet_photo']['name'])) {
                $p = handle_upload($_FILES['pet_photo'], 'pets');
                if ($p) $photo = $p;
            }
            save_pet([
                'id'         => $pid,
                'username'   => $_SESSION['username'],
                'pet_name'   => $pname,
                'breed'      => trim($_POST['breed'] ?? ''),
                'age'        => trim($_POST['pet_age'] ?? ''),
                'photo'      => $photo,
                'created_at' => $existing ? $existing[0]['created_at'] : date('Y-m-d H:i:s'),
            ]);
            $pets = get_pets_for_user($_SESSION['username']);
            $success = 'Pet updated!';
        }

    } elseif ($_POST['action'] === 'delete_pet') {
        $pid = $_POST['pet_id'] ?? '';
        if ($pid) { delete_pet($pid); $pets = get_pets_for_user($_SESSION['username']); $success = 'Pet removed.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Edit Profile</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar-petpals">
  <a href="community.php" class="nav-brand"><span>🐾</span> PetPals</a>
  <div class="nav-links">
    <a href="profile.php?u=<?= urlencode($_SESSION['username']) ?>">My Profile</a>
    <a href="community.php">Community</a>
    <a href="logout.php">Sign Out</a>
  </div>
</nav>

<div class="edit-wrapper">
  <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;margin-bottom:1.5rem;color:var(--deep);">Edit Profile</h1>

  <?php if ($success): ?>
    <div class="alert-pp alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Personal Info -->
  <div class="edit-card">
    <h3>👤 Personal Information</h3>
    <p style="font-size:0.82rem;color:var(--light-text);margin-top:-0.75rem;margin-bottom:1rem;">Username <strong>@<?= htmlspecialchars($user['username']) ?></strong> cannot be changed.</p>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="personal">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;" class="mb-3">
        <div>
          <label class="form-label">First Name *</label>
          <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
        </div>
        <div>
          <label class="form-label">Last Name *</label>
          <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control"><?= htmlspecialchars($user['bio']) ?></textarea>
      </div>
      <div class="mb-4">
        <label class="form-label">Profile Photo</label>
        <?php if (!empty($user['profile_photo'])): ?>
          <div style="margin-bottom:0.75rem;">
            <img src="<?= htmlspecialchars($user['profile_photo']) ?>" class="preview-img" style="display:block;" alt="Current photo" id="profilePreview">
          </div>
        <?php else: ?>
          <img src="" class="preview-img" style="display:none;" alt="Preview" id="profilePreview">
        <?php endif; ?>
        <div class="upload-area" style="padding:0.75rem 1rem;" onclick="this.querySelector('input').click()">
          <input type="file" name="photo" accept="image/*" style="display:none;" onchange="previewProfile(this)">
          <p style="margin:0;font-size:0.85rem;">Click to change photo</p>
        </div>
      </div>
      <button type="submit" class="btn-primary-pp">Save Changes</button>
    </form>
  </div>

  <!-- Password -->
  <div class="edit-card">
    <h3>🔑 Change Password</h3>
    <form method="POST">
      <input type="hidden" name="action" value="password">
      <div class="mb-3">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" minlength="6" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn-primary-pp">Update Password</button>
    </form>
  </div>

  <!-- Pets -->
  <div class="edit-card" id="pets">
    <h3>🐾 My Pets</h3>

    <?php foreach ($pets as $pet): ?>
    <div class="pet-entry">
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit_pet">
        <input type="hidden" name="pet_id" value="<?= htmlspecialchars($pet['id']) ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
          <div>
            <label class="form-label">Name</label>
            <input type="text" name="pet_name" class="form-control" value="<?= htmlspecialchars($pet['pet_name']) ?>" required>
          </div>
          <div>
            <label class="form-label">Breed</label>
            <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($pet['breed']) ?>">
          </div>
        </div>
        <div class="mt-2 mb-2">
          <label class="form-label">Age</label>
          <input type="number" name="pet_age" class="form-control" value="<?= htmlspecialchars($pet['age']) ?>" min="0" max="99" style="max-width:120px;">
        </div>
        <div class="mb-2">
          <label class="form-label">Photo</label>
          <?php if (!empty($pet['photo'])): ?>
            <div style="margin-bottom:0.5rem;">
              <img src="<?= htmlspecialchars($pet['photo']) ?>" class="preview-img-pet" alt="<?= htmlspecialchars($pet['pet_name']) ?>">
            </div>
          <?php endif; ?>
          <div class="upload-area" style="padding:0.75rem 1rem;" onclick="this.querySelector('input').click()">
            <input type="file" name="pet_photo" accept="image/*" style="display:none;">
            <p style="margin:0;font-size:0.82rem;">Click to change photo</p>
          </div>
        </div>
        <div style="display:flex;gap:0.5rem;margin-top:0.75rem;">
          <button type="submit" class="btn-sage" style="font-size:0.85rem;padding:0.5rem 1.2rem;">Save Pet</button>
        </div>
      </form>
      <form method="POST" style="margin-top:0.5rem;" onsubmit="return confirm('Remove this pet?')">
        <input type="hidden" name="action" value="delete_pet">
        <input type="hidden" name="pet_id" value="<?= htmlspecialchars($pet['id']) ?>">
        <button type="submit" class="btn-danger-pp" style="font-size:0.82rem;padding:0.4rem 1rem;">Remove Pet</button>
      </form>
    </div>
    <?php endforeach; ?>

    <!-- Add new pet -->
    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:2px dashed var(--border);">
      <h4 style="font-family:'Playfair Display',serif;font-size:1rem;color:var(--mid);margin-bottom:1rem;">+ Add New Pet</h4>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_pet">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
          <div>
            <label class="form-label">Name *</label>
            <input type="text" name="pet_name" class="form-control" placeholder="Buddy">
          </div>
          <div>
            <label class="form-label">Breed</label>
            <input type="text" name="breed" class="form-control" placeholder="Labrador">
          </div>
        </div>
        <div class="mt-2 mb-2">
          <label class="form-label">Age</label>
          <input type="number" name="pet_age" class="form-control" placeholder="2" min="0" max="99" style="max-width:120px;">
        </div>
        <div class="mb-3">
          <label class="form-label">Photo</label>
          <div class="upload-area" style="padding:0.75rem 1rem;" onclick="this.querySelector('input').click()">
            <input type="file" name="pet_photo" accept="image/*" style="display:none;">
            <p style="margin:0;font-size:0.82rem;">Click to upload photo</p>
          </div>
        </div>
        <button type="submit" class="btn-primary-pp">Add Pet</button>
      </form>
    </div>
  </div>

</div>

<script>
function previewProfile(input) {
  const preview = document.getElementById('profilePreview');
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(file);
  }
}
</script>
</body>
</html>
