<?php
function redirect_to($location) {
    header("Location: {$location}");
    exit;
}

function logged_in() {
    return isset($_SESSION['user_id']);
}

function confirm_logged_in() {
    if (!logged_in()) {
        redirect_to("index.php?page=login");
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
