<?php
require_once 'includes/helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    delete_user($username);
    session_destroy();
    header('Location: login.php?deleted=1');
    exit;
}

header('Location: profile.php?u=' . urlencode($_SESSION['username']));
exit;
