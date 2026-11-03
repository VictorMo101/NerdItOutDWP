<?php
// CONTROLLER: handles the form, asks the Model, then shows a View.
class AuthController {
    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function home(): void {
        confirm_logged_in();
        $username = $_SESSION['user'];
        require __DIR__ . "/../views/home.php";
    }

    public function login(): void {
        if (logged_in()) {
            redirect_to("index.php");
        }

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['user'] ?? ''));
            $password = (string) ($_POST['pass'] ?? '');

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $message = "Your session has expired. Please try again.";
            } elseif ($username === '' || $password === '') {
                $message = "Enter your username and password.";
            } else {
                try {
                    $found_user = $this->user->login($username, $password);
                } catch (PDOException $e) {
                    error_log("Login query failed: " . $e->getMessage());
                    $found_user = false;
                    $message = "The application is temporarily unavailable.";
                }

                if ($found_user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $found_user['userID'];
                    $_SESSION['user'] = $found_user['username'];
                    redirect_to("index.php");
                } elseif ($message === "") {
                    $message = "Username/password combination incorrect.";
                }
            }
        } elseif (isset($_GET['logout'])) {
            $message = "You are now logged out.";
        } elseif (isset($_GET['registered'])) {
            $message = "User created. You can log in now.";
        }

        require __DIR__ . "/../views/login.php";
    }

    public function register(): void {
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim((string) ($_POST['user'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $display_name = trim((string) ($_POST['displayName'] ?? ''));
            $password = (string) ($_POST['pass'] ?? '');

            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $message = "Your session has expired. Please try again.";
            } elseif ($username === "" || $display_name === "" || $password === "") {
                $message = "Fill in all fields. Password needs at least 8 characters.";
            } elseif (strlen($username) > 50 || strlen($email) > 80 || strlen($display_name) > 80) {
                $message = "One or more fields are too long.";
            } elseif (strlen($password) < 8) {
                $message = "Password needs at least 8 characters.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Please enter a valid email.";
            } else {
                try {
                    $created = $this->user->create($username, $email, $display_name, $password);
                } catch (PDOException $e) {
                    error_log("User creation failed: " . $e->getMessage());
                    $created = false;
                    $message = "The application is temporarily unavailable.";
                }

                if ($created) {
                    redirect_to("index.php?page=login&registered=1");
                } elseif ($message === "") {
                    $message = "That username or email is already in use.";
                }
            }
        }

        require __DIR__ . "/../views/register.php";
    }

    public function logout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST'
            || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
            http_response_code(405);
            echo "Method not allowed.";
            return;
        }

        $_SESSION = array();
        session_destroy();
        redirect_to("index.php?page=login&logout=1");
    }
}
