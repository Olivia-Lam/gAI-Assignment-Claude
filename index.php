<?php
require_once 'includes/helpers.php';
if (is_logged_in()) {
    header('Location: community.php');
} else {
    header('Location: login.php');
}
exit;
