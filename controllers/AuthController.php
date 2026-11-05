<?php
// CONTROLLER: handles the form, asks the Model, then shows a View.
class AuthController {
    private User $user;

    // Receives the User model so the controller can access user-related database functions.
    public function __construct(User $user) {
        $this->user = $user;
    }

    // Displays the home page. Only logged-in users are allowed access.
    public function home() {
        confirm_logged_in();
        $username = $_SESSION['user'];
        require __DIR__ . "/../views/home.php";
    }

    public function login() {
        if (logged_in()) {
            redirect_to("index.php");
        }

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['user'] ?? '');
            $password = $_POST['pass'] ?? '';

            // Server-side CSRF validation to make sure the request is legitimate.
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $message = "Your session has expired. Please try again.";
            } elseif ($username === '' || $password === '') {
                $message = "Enter your username and password.";
            } else {
                try {
                    // Ask the User model to verify the login details.
                    $found_user = $this->user->login($username, $password);
                } catch (PDOException $e) {
                    error_log("Login query failed: " . $e->getMessage());
                    $found_user = false;
                    $message = "The application is temporarily unavailable.";
                }

                if ($found_user) {
                    // Regenerate the session ID after login to prevent session fixation.
                    session_regenerate_id(true);

                    // Store the logged-in user's information in the session.
                    $_SESSION['user_id'] = $found_user['userID'];
                    $_SESSION['user'] = $found_user['username'];
                    redirect_to("index.php");

                } elseif ($message === "") {
                    $message = "Username/password combination incorrect.";
                }
            }

        // Display messages when users log out or successfully register.
        } elseif (isset($_GET['logout'])) {
            $message = "You are now logged out.";
        } elseif (isset($_GET['registered'])) {
            $message = "User created. You can log in now.";
        }

        require __DIR__ . "/../views/login.php";
    }

    // Handles new user registration.
    public function register() {
        $message = "";

        // Check if the registration form has been submitted.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['user'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $display_name = trim($_POST['displayName'] ?? '');
            $password = $_POST['pass'] ?? '';

            // Server-side CSRF validation to protect against forged requests.
            if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
                $message = "Your session has expired. Please try again.";

            // Server-side validation to make sure all required fields are filled in.
            } elseif ($username === "" || $email === "" || $display_name === "" || $password === "") {
                $message = "Please fill in all required fields.";
            } elseif (strlen($username) > 50 || strlen($email) > 80 || strlen($display_name) > 80) {
                $message = "One or more fields are too long.";
            } elseif (strlen($password) < 8) {
                $message = "Password needs at least 8 characters.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "Please enter a valid email.";
            } else {
                try {
                    // Ask the User model to create the new user in the database.
                    $created = $this->user->create($username, $email, $display_name, $password);
                } catch (PDOException $e) {
                    error_log("User creation failed: " . $e->getMessage());
                    $created = false;
                    $message = "The application is temporarily unavailable.";
                }

                if ($created) {
                    redirect_to("index.php?page=login&registered=1");

                // Display an error if the username or email is already taken.
                } elseif ($message === "") {
                    $message = "That username or email is already in use.";
                }
            }
        }

        require __DIR__ . "/../views/register.php";
    }

    // Handles user logout.
    public function logout() {
        // Only allow POST requests with a valid CSRF token.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) 
        {
            http_response_code(405);
            echo "Method not allowed.";
            return;
        }

        // Clear the session and destroy it to log the user out.
        $_SESSION = array();
        session_destroy();

        // Redirect to the login page with a logout confirmation.
        redirect_to("index.php?page=login&logout=1");
    }
}