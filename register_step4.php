<?php
require_once 'includes/helpers.php';

if (is_logged_in()) { header('Location: community.php'); exit; }
if (empty($_SESSION['reg']['first_name'])) { header('Location: register_step2.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pets = [];
    $pet_names  = $_POST['pet_name']  ?? [];
    $pet_breeds = $_POST['breed']     ?? [];
    $pet_ages   = $_POST['pet_age']   ?? [];
    $existing_photos = $_POST['existing_photo'] ?? [];

    foreach ($pet_names as $i => $name) {
        if (trim($name) === '') continue;
        $photo = $existing_photos[$i] ?? '';
        if (!empty($_FILES['pet_photo']['name'][$i])) {
            $file = [
                'name'     => $_FILES['pet_photo']['name'][$i],
                'type'     => $_FILES['pet_photo']['type'][$i],
                'tmp_name' => $_FILES['pet_photo']['tmp_name'][$i],
                'error'    => $_FILES['pet_photo']['error'][$i],
                'size'     => $_FILES['pet_photo']['size'][$i],
            ];
            $p = handle_upload($file, 'pets');
            if ($p !== null) $photo = $p;
        }
        $pets[] = [
            'pet_name' => trim($name),
            'breed'    => trim($pet_breeds[$i] ?? ''),
            'age'      => trim($pet_ages[$i] ?? ''),
            'photo'    => $photo,
        ];
    }

    $_SESSION['reg']['pets'] = $pets;
    header('Location: register_step5.php');
    exit;
}

$savedPets = $_SESSION['reg']['pets'] ?? [[]];
if (empty($savedPets)) $savedPets = [[]];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetPals — Pet Info (Step 4 of 5)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="paw-decor tl">🐾</div>
<div class="paw-decor br">🐾</div>

<div class="wizard-wrapper">
  <div class="wizard-card">
    <div class="wizard-header">
      <h1>🐾 Join PetPals</h1>
      <p>Tell us about your furry (or scaly!) companions</p>
    </div>

    <div class="step-indicator">
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot done">✓</div>
      <div class="step-line done"></div>
      <div class="step-dot active">4</div>
      <div class="step-line"></div>
      <div class="step-dot">5</div>
    </div>

    <div class="wizard-body">
      <h2 class="section-title">Your Pets <span style="font-size:0.8rem;font-weight:400;color:var(--light-text);">(optional)</span></h2>

      <?php if ($error): ?>
        <div class="alert-pp alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="petForm">
        <div id="petsContainer">
          <?php foreach ($savedPets as $idx => $pet): ?>
          <div class="pet-entry" id="pet-<?= $idx ?>">
            <div class="pet-entry-title">
              <span>🐶 Pet <?= $idx + 1 ?></span>
              <?php if ($idx > 0): ?>
                <button type="button" class="remove-pet" onclick="removePet(<?= $idx ?>)">✕</button>
              <?php endif; ?>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
              <div>
                <label class="form-label">Pet Name</label>
                <input type="text" name="pet_name[]" class="form-control"
                       value="<?= htmlspecialchars($pet['pet_name'] ?? '') ?>"
                       placeholder="Max">
              </div>
              <div>
                <label class="form-label">Breed / Type</label>
                <input type="text" name="breed[]" class="form-control"
                       value="<?= htmlspecialchars($pet['breed'] ?? '') ?>"
                       placeholder="Golden Retriever">
              </div>
            </div>
            <div class="mt-2">
              <label class="form-label">Age (years)</label>
              <input type="number" name="pet_age[]" class="form-control"
                     value="<?= htmlspecialchars($pet['age'] ?? '') ?>"
                     placeholder="3" min="0" max="99" style="max-width:120px;">
            </div>
            <div class="mt-2">
              <label class="form-label">Photo</label>
              <input type="hidden" name="existing_photo[]" value="<?= htmlspecialchars($pet['photo'] ?? '') ?>">
              <div class="upload-area" style="padding:1rem;" onclick="this.querySelector('input[type=file]').click()">
                <input type="file" name="pet_photo[]" accept="image/*" style="display:none;" onchange="previewPet(this,<?= $idx ?>)">
                <?php if (!empty($pet['photo'])): ?>
                  <img src="<?= htmlspecialchars($pet['photo']) ?>" class="preview-img-pet" id="pet-preview-<?= $idx ?>" alt="Pet">
                <?php else: ?>
                  <div style="font-size:1.8rem;">📷</div>
                  <p style="font-size:0.8rem;margin-top:0.25rem;">Upload pet photo</p>
                  <img src="" class="preview-img-pet" id="pet-preview-<?= $idx ?>" style="display:none;" alt="Pet preview">
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <button type="button" class="btn-sage" id="addPetBtn" onclick="addPet()" style="width:100%;margin-bottom:1.5rem;">
          + Add Another Pet
        </button>

        <div style="display:flex;justify-content:space-between;">
          <a href="register_step3.php" class="btn-secondary-pp" style="text-decoration:none;">← Back</a>
          <button type="submit" class="btn-primary-pp">Next →</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let petCount = <?= count($savedPets) ?>;

function addPet() {
  const idx = petCount++;
  const container = document.getElementById('petsContainer');
  const div = document.createElement('div');
  div.className = 'pet-entry';
  div.id = 'pet-' + idx;
  div.innerHTML = `
    <div class="pet-entry-title">
      <span>🐶 Pet ${idx + 1}</span>
      <button type="button" class="remove-pet" onclick="removePet(${idx})">✕</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
      <div>
        <label class="form-label">Pet Name</label>
        <input type="text" name="pet_name[]" class="form-control" placeholder="Max">
      </div>
      <div>
        <label class="form-label">Breed / Type</label>
        <input type="text" name="breed[]" class="form-control" placeholder="Golden Retriever">
      </div>
    </div>
    <div class="mt-2">
      <label class="form-label">Age (years)</label>
      <input type="number" name="pet_age[]" class="form-control" placeholder="3" min="0" max="99" style="max-width:120px;">
    </div>
    <div class="mt-2">
      <label class="form-label">Photo</label>
      <input type="hidden" name="existing_photo[]" value="">
      <div class="upload-area" style="padding:1rem;" onclick="this.querySelector('input[type=file]').click()">
        <input type="file" name="pet_photo[]" accept="image/*" style="display:none;" onchange="previewPet(this,${idx})">
        <div style="font-size:1.8rem;">📷</div>
        <p style="font-size:0.8rem;margin-top:0.25rem;">Upload pet photo</p>
        <img src="" class="preview-img-pet" id="pet-preview-${idx}" style="display:none;" alt="Pet preview">
      </div>
    </div>
  `;
  container.appendChild(div);
}

function removePet(idx) {
  const el = document.getElementById('pet-' + idx);
  if (el) el.remove();
}

function previewPet(input, idx) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('pet-preview-' + idx);
    img.src = e.target.result;
    img.style.display = 'block';
  };
  reader.readAsDataURL(file);
}
</script>
</body>
</html>
