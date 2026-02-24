<?php
require_once 'includes/helpers.php';

if (is_logged_in()) { header('Location: community.php'); exit; }
if (empty($_SESSION['reg']['first_name'])) { header('Location: register_step2.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = $_SESSION['reg']['profile_photo'] ?? '';

    if (!empty($_FILES['photo']['name'])) {
        $p = handle_upload($_FILES['photo'], 'profiles');
        if ($p === null) {
            $error = 'Only JPG, PNG, GIF, or WebP images are allowed.';
        } else {
            $path = $p;
        }
    }

    if (!$error) {
        $_SESSION['reg']['profile_photo'] = $path;
        header('Location: register_step4.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Profile Photo (Step 3 of 5)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="wizard-wrapper">
  <div class="wizard-card">
    <div class="wizard-header">
      <h1>🐾 Join PetPals</h1>
      <p>Add a profile photo so others can recognise you</p>
    </div>

    <div class="step-indicator">
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot active">3</div>
      <div class="step-line"></div>
      <div class="step-dot">4</div>
      <div class="step-line"></div>
      <div class="step-dot">5</div>
    </div>

    <div class="wizard-body">
      <h2 class="section-title">Profile Photo <span style="font-size:0.8rem;font-weight:400;color:var(--light-text);">(optional)</span></h2>

      <?php if ($error): ?>
        <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="upload-area" id="uploadArea">
          <input type="file" name="photo" accept="image/*" id="photoInput">
          <div class="upload-icon">📸</div>
          <p>Click or drag your photo here</p>
          <p style="margin-top:0.25rem;font-size:0.8rem;">JPG, PNG, GIF, WebP — max 5 MB</p>
          <?php if (!empty($_SESSION['reg']['profile_photo'])): ?>
            <img src="<?= htmlspecialchars($_SESSION['reg']['profile_photo']) ?>" class="preview-img" id="previewImg" alt="Current photo">
          <?php else: ?>
            <img src="" class="preview-img" id="previewImg" alt="Preview" style="display:none;">
          <?php endif; ?>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:2rem;">
          <a href="register_step2.php" class="btn-secondary-pp" style="text-decoration:none;">← Back</a>
          <button type="submit" class="btn-primary-pp">Next →</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const input = document.getElementById('photoInput');
const preview = document.getElementById('previewImg');

input.addEventListener('change', function() {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

const area = document.getElementById('uploadArea');
area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('drag-over'); });
area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
area.addEventListener('drop', e => {
  e.preventDefault();
  area.classList.remove('drag-over');
  if (e.dataTransfer.files[0]) {
    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event('change'));
  }
});
</script>
</body>
</html>
