<?php
class Post {
    private PDO $connection;

    public function __construct(PDO $connection) {
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

    public function create($user_id, $text) {
        $query = "INSERT INTO Posts (userID, text) VALUES (:user_id, :text)";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':text', $text, PDO::PARAM_STR);

        return $statement->execute();
    }
}