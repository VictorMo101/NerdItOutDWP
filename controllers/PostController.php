<?php
class PostController {
    private $post;

    public function __construct($post) {
        $this->post = $post;
    }

    public function index() {
        confirm_logged_in();

        $username = $_SESSION['user'];
        $message = $_SESSION['post_message'] ?? "";
        unset($_SESSION['post_message']);

        try {
            $posts = $this->post->getAll();
        } catch (PDOException $e) {
            error_log("Post retrieval failed: " . $e->getMessage());
            $posts = [];
            $message = "Posts are temporarily unavailable.";
        }

        require __DIR__ . "/../views/home.php";
    }

    public function create() {
        confirm_logged_in();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            return;
        }

        $text = trim($_POST['text'] ?? '');

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['post_message'] = "Your session has expired. Please try again.";
        } elseif ($text === '') {
            $_SESSION['post_message'] = "Post text cannot be empty.";
        } elseif (strlen($text) > 280) {
            $_SESSION['post_message'] = "Posts cannot be longer than 280 characters.";
        } else {
            try {
                $created = $this->post->create($_SESSION['user_id'], $text);
            } catch (PDOException $e) {
                error_log("Post creation failed: " . $e->getMessage());
                $created = false;
                $_SESSION['post_message'] = "Posts are temporarily unavailable.";
            }

            if (!$created && !isset($_SESSION['post_message'])) {
                $_SESSION['post_message'] = "The post could not be created.";
            }
        }

        redirect_to("index.php?page=home");
    }

    public function delete() {
        confirm_logged_in();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method not allowed.";
            return;
        }

        $post_id = filter_var(
            $_POST['post_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['post_message'] = "Your session has expired. Please try again.";
        } elseif ($post_id === false) {
            $_SESSION['post_message'] = "The selected post is invalid.";
        } else {
            try {
                $deleted = $this->post->delete($post_id, $_SESSION['user_id']);
            } catch (PDOException $e) {
                error_log("Post deletion failed: " . $e->getMessage());
                $deleted = false;
                $_SESSION['post_message'] = "Posts are temporarily unavailable.";
            }

            if (!$deleted && !isset($_SESSION['post_message'])) {
                $_SESSION['post_message'] = "You can only delete your own posts.";
            }
        }

        redirect_to("index.php?page=home");
    }
}