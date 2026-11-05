<?php
// This model contains database code for users.
class User {
    private $connection;

    // Store the database connection.
    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function create($username, $email, $display_name, $password) {
        // Security: password_hash protects passwords if the database is stolen.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // 1. Write the INSERT query with named placeholders.
            $query = "INSERT INTO Users (username, email, password, displayName)
                      VALUES (:username, :email, :password, :display_name)";

            // Security: prepared statements prevent SQL injection.
            $statement = $this->connection->prepare($query);

            // 2. Put the form values into the placeholders.
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->bindParam(':email', $email, PDO::PARAM_STR);
            $statement->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $statement->bindParam(':display_name', $display_name, PDO::PARAM_STR);

            // 3. Run the INSERT query.
            return $statement->execute();
        } catch (PDOException $e) {
            // 4. A duplicate username or email is a normal registration error.
            if ($e->errorInfo[0] === '23000') {
                return false;
            } else {
                throw $e;
            }
        }
    }

    public function login($username, $password) {
        // 1. Write the SELECT query with a placeholder.
        $query = "SELECT userID, username, password
                  FROM Users
                  WHERE username = :username
                  LIMIT 1";

        // Security: prepared statements prevent SQL injection.
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();

        // 2. Read the matching user.
        $found_user = $statement->fetch(PDO::FETCH_ASSOC);

        // Security: password_verify safely checks the password hash.
        if ($found_user && password_verify($password, $found_user['password'])) {
            return $found_user;
        } else {
            return false;
        }
    }
}
