<?php
class PostController {
    private Post $post;

    public function __construct(Post $post) {
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
}