<?php

// src/UserManager.php
class Users {
    private $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function addUser($username, $email, $role, $password) {
        if (empty($username) || empty($email) || empty($role) || empty($password)) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
            return ['success' => false, 'message' => 'Password does not meet strength requirements.'];
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            return ['success' => false, 'message' => 'Email already exists.'];
        }

        $result = $this->conn->query("SELECT MAX(userID) AS max_id FROM user");
        $row = $result->fetch_assoc();
        $newId = ($row['max_id'] ?? 0) + 1;

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO user (userID, username, email, role, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $newId, $username, $email, $role, $hashed);
        $success = $stmt->execute();
        $stmt->close();

        return ['success' => $success, 'message' => $success ? 'User added successfully' : 'Insert failed.'];
    }

    public function updateUser($userID, $username, $email, $role, $password = null) {
        if (empty($username) || empty($email) || empty($role)) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        if ($password) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
                return ['success' => false, 'message' => 'Password does not meet strength requirements.'];
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE user SET username = ?, email = ?, role = ?, password = ? WHERE userID = ?");
            $stmt->bind_param("ssssi", $username, $email, $role, $hashed, $userID);
        } else {
            $stmt = $this->conn->prepare("UPDATE user SET username = ?, email = ?, role = ? WHERE userID = ?");
            $stmt->bind_param("sssi", $username, $email, $role, $userID);
        }

        $success = $stmt->execute();
        $stmt->close();

        return ['success' => $success, 'message' => $success ? 'User updated successfully' : 'Update failed.'];
    }

    public function deleteUser($userID) {
        $stmt = $this->conn->prepare("DELETE FROM user WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $success = $stmt->execute();
        $stmt->close();

        return ['success' => $success, 'message' => $success ? 'User deleted successfully' : 'Delete failed.'];
    }
}
?>