<?php
function redirect_to(string $location): void {
    header("Location: {$location}");
    exit;
}

function logged_in(): bool {
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function confirm_logged_in(): void {
    if (!logged_in()) {
        redirect_to("index.php?page=login");
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
