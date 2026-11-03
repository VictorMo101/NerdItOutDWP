<?php
// Kept for legacy entry points; active requests start the session in index.php.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}