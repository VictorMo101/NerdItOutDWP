<?php
// MODEL: only talks to the database. No HTML, no $_POST, no $_SESSION.
class User {
    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    // Saves a new user. Returns true if it worked, false if username/email is taken.
    public function create(string $username, string $email, string $display_name, string $password): bool {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = "INSERT INTO Users (username, email, password, displayName)
                      VALUES (:username, :email, :password, :display_name)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':display_name', $display_name);
            return $stmt->execute();
        } catch (PDOException $e) {
            if (($e->errorInfo[0] ?? null) === '23000') {
                return false;
            }

            throw $e;
        }
    }

    // Returns the user's row if the login is correct, otherwise false.
    public function login(string $username, string $password): array|false {
        $query = "SELECT userID, username, password FROM Users WHERE username = :username LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $found_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($found_user && password_verify($password, $found_user['password'])) {
            return $found_user;
        }
        return false;
    }
}
