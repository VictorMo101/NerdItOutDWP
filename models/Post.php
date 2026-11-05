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
}