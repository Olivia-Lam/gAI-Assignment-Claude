<?php
session_start();

define('DATA_DIR', __DIR__ . '/../data/');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('USERS_FILE', DATA_DIR . 'users.csv');
define('PETS_FILE', DATA_DIR . 'pets.csv');

// ─── CSV Helpers ────────────────────────────────────────────────────────────

function csv_read($file) {
    if (!file_exists($file)) return [];
    $rows = [];
    if (($fh = fopen($file, 'r')) !== false) {
        $headers = fgetcsv($fh);
        while (($row = fgetcsv($fh)) !== false) {
            if ($headers && count($headers) === count($row)) {
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($fh);
    }
    return $rows;
}

function csv_write($file, $rows, $headers) {
    $fh = fopen($file, 'w');
    fputcsv($fh, $headers);
    foreach ($rows as $row) {
        fputcsv($fh, array_values($row));
    }
    fclose($fh);
}

// ─── User Helpers ────────────────────────────────────────────────────────────

function get_all_users() {
    return csv_read(USERS_FILE);
}

function get_user($username) {
    foreach (get_all_users() as $u) {
        if ($u['username'] === $username) return $u;
    }
    return null;
}

function save_user($data) {
    $users = get_all_users();
    $headers = ['username','password','first_name','last_name','email','phone','bio','profile_photo','created_at'];
    $found = false;
    foreach ($users as &$u) {
        if ($u['username'] === $data['username']) {
            $u = array_merge($u, $data);
            $found = true;
            break;
        }
    }
    if (!$found) $users[] = $data;
    csv_write(USERS_FILE, $users, $headers);
}

function delete_user($username) {
    $users = array_filter(get_all_users(), fn($u) => $u['username'] !== $username);
    $headers = ['username','password','first_name','last_name','email','phone','bio','profile_photo','created_at'];
    csv_write(USERS_FILE, array_values($users), $headers);
    // Delete pets
    $pets = array_filter(get_all_pets(), fn($p) => $p['username'] !== $username);
    $pheaders = ['id','username','pet_name','breed','age','photo','created_at'];
    csv_write(PETS_FILE, array_values($pets), $pheaders);
}

function username_exists($username) {
    return get_user($username) !== null;
}

// ─── Pet Helpers ─────────────────────────────────────────────────────────────

function get_all_pets() {
    return csv_read(PETS_FILE);
}

function get_pets_for_user($username) {
    return array_values(array_filter(get_all_pets(), fn($p) => $p['username'] === $username));
}

function save_pet($data) {
    $pets = get_all_pets();
    $headers = ['id','username','pet_name','breed','age','photo','created_at'];
    if (empty($data['id'])) {
        $data['id'] = uniqid();
    }
    $found = false;
    foreach ($pets as &$p) {
        if ($p['id'] === $data['id']) {
            $p = array_merge($p, $data);
            $found = true;
            break;
        }
    }
    if (!$found) $pets[] = $data;
    csv_write(PETS_FILE, $pets, $headers);
    return $data['id'];
}

function delete_pet($id) {
    $pets = array_filter(get_all_pets(), fn($p) => $p['id'] !== $id);
    $headers = ['id','username','pet_name','breed','age','photo','created_at'];
    csv_write(PETS_FILE, array_values($pets), $headers);
}

// ─── File Upload Helper ──────────────────────────────────────────────────────

function handle_upload($file, $subdir) {
    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($file['type'], $allowed)) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid() . '.' . $ext;
    $dest = UPLOADS_DIR . $subdir . '/' . $name;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'uploads/' . $subdir . '/' . $name;
    }
    return null;
}

// ─── Auth Helpers ─────────────────────────────────────────────────────────────

function is_logged_in() {
    return isset($_SESSION['username']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
