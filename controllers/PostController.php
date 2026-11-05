<?php
class PostController {
    private $post;
    private $upload_directory;
    private $upload_url = "uploads/posts/";

    public function __construct($post) {
        $this->post = $post;
        $this->upload_directory = __DIR__ . "/../uploads/posts/";
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
        $image = null;
        unset($_SESSION['post_message']);

        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['post_message'] = "Your session has expired. Please try again.";
        } elseif ($text === '') {
            $_SESSION['post_message'] = "Post text cannot be empty.";
        } elseif (strlen($text) > 280) {
            $_SESSION['post_message'] = "Posts cannot be longer than 280 characters.";
        } elseif (!empty($_FILES['image']['name'])) {
            $image = $this->uploadImage($_FILES['image']);
            if ($image === false) {
                $_SESSION['post_message'] = "Please upload a valid image under 5 MB.";
            }
        }

        if (!isset($_SESSION['post_message'])) {
            try {
                $created = $this->post->create($_SESSION['user_id'], $text, $image);
            } catch (PDOException $e) {
                error_log("Post creation failed: " . $e->getMessage());
                $created = false;
                $_SESSION['post_message'] = "Posts are temporarily unavailable.";
            }

            if (!$created && $image !== null) {
                $this->removeImage($image);
            }

            if (!$created && !isset($_SESSION['post_message'])) {
                $_SESSION['post_message'] = "The post could not be created.";
            }
        }

        redirect_to("index.php?page=home");
    }

    private function uploadImage($file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || ($file['size'] ?? 0) > 5 * 1024 * 1024
            || !is_uploaded_file($file['tmp_name'] ?? '')) {
            return false;
        }

        $image_info = getimagesize($file['tmp_name']);
        $mime_types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        if ($image_info === false || !isset($mime_types[$image_info['mime']])) {
            return false;
        }

        if (!is_dir($this->upload_directory)
            && !mkdir($this->upload_directory, 0755, true)
            && !is_dir($this->upload_directory)) {
            error_log("Post image directory could not be created.");
            return false;
        }

        $filename = bin2hex(random_bytes(16)) . "." . $mime_types[$image_info['mime']];
        $destination = $this->upload_directory . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return false;
        }

        return $this->upload_url . $filename;
    }

    private function removeImage($image) {
        $filename = basename($image);
        $path = $this->upload_directory . $filename;

        if (is_file($path)) {
            unlink($path);
        }
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
                $image = $this->post->getImage($post_id, $_SESSION['user_id']);
                $deleted = $this->post->delete($post_id, $_SESSION['user_id']);
            } catch (PDOException $e) {
                error_log("Post deletion failed: " . $e->getMessage());
                $deleted = false;
                $_SESSION['post_message'] = "Posts are temporarily unavailable.";
            }

            if (!$deleted && !isset($_SESSION['post_message'])) {
                $_SESSION['post_message'] = "You can only delete your own posts.";
            } elseif ($deleted && $image !== null) {
                $this->removeImage($image);
            }
        }

        redirect_to("index.php?page=home");
    }
}