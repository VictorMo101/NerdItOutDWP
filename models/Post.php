<?php
class Post {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function getAll() {
        $query = "SELECT Posts.postID, Posts.text, Posts.createdAt, Posts.image,
                         Users.username, Users.displayName
                  FROM Posts
                  INNER JOIN Users ON Users.userID = Posts.userID
                  ORDER BY Posts.createdAt DESC, Posts.postID DESC";
        $statement = $this->connection->query($query);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $text, $image = null) {
        $query = "INSERT INTO Posts (userID, text, image)
                VALUES (:user_id, :text, :image)";
        $statement = $this->connection->prepare($query);

        $image_type = $image === null ? PDO::PARAM_NULL : PDO::PARAM_STR;

        $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $statement->bindParam(':text', $text, PDO::PARAM_STR);
        $statement->bindParam(':image', $image, $image_type);

        return $statement->execute();
    }

    public function getImage($post_id, $user_id) {
        $query = "SELECT image FROM Posts
                  WHERE postID = :post_id AND userID = :user_id";
        $statement = $this->connection->prepare($query);
        $statement->BindParam(':post_id', $post_id, PDO::PARAM_INT);
        $statement->BindParam(':user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();

        $post = $statement->fetch(PDO::FETCH_ASSOC);
        return $post ? $post['image'] : null;
    }

    public function delete($post_id, $user_id) {
        $query = "DELETE FROM Posts
                  WHERE postID = :post_id AND userID = :user_id";
        $statement = $this->connection->prepare($query);
        $statement->BindParam(':post_id', $post_id, PDO::PARAM_INT);
        $statement->BindParam(':user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount() === 1;
    }
}