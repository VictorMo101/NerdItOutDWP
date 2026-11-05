<?php
class PostController {
    private Post $post;

    public function __construct(Post $post) {
        $this->post = $post;
    }

    public function index() {
        confirm_logged_in();

        $username = $_SESSION['user'];
        $message = "";

        try {
            $posts = $this->post->getAll();
        } catch (PDOException $e) {
            error_log("Post retrieval failed: " . $e->getMessage());
            $posts = [];
            $message = "Posts are temporarily unavailable.";
        }

        require __DIR__ . "/../views/home.php";
    }
}