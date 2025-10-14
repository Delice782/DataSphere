
<?php

// tests/UserManagerTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../services/Users.php';

class userTest extends TestCase {
    private $conn;
    private $userManager;

    protected function setUp(): void {
        $this->conn = new mysqli('localhost', 'root', '', 'dataspheretest');
    
        // Disable foreign key checks temporarily
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");
        $this->conn->query("DROP TABLE IF EXISTS user");
    
        $this->conn->query("CREATE TABLE user (
            userID INT PRIMARY KEY,
            username VARCHAR(255),
            email VARCHAR(255) UNIQUE,
            role VARCHAR(50),
            password VARCHAR(255)
        )");
    
        // Re-enable foreign key checks
        $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");
    
        $this->userManager = new Users($this->conn);
    }
    

    public function testAddUserSuccess(): void {
        $response = $this->userManager->addUser("John", "john@example.com", "admin", "StrongP@ss1!");
        $this->assertTrue($response['success']);
        $this->assertEquals("User added successfully", $response['message']);
    }

    public function testAddUserExistingEmail(): void {
        $this->userManager->addUser("John", "john@example.com", "admin", "StrongP@ss1!");
        $response = $this->userManager->addUser("Jane", "john@example.com", "editor", "AnotherP@ss2!");
        $this->assertFalse($response['success']);
        $this->assertEquals("Email already exists.", $response['message']);
    }

    public function testAddUserWeakPassword(): void {
        $response = $this->userManager->addUser("Jane", "jane@example.com", "editor", "123");
        $this->assertFalse($response['success']);
        $this->assertStringContainsString("Password does not meet", $response['message']);
    }

    public function testUpdateUserWithoutPassword(): void {
        $this->userManager->addUser("John", "john@example.com", "admin", "StrongP@ss1!");
        $response = $this->userManager->updateUser(1, "Johnny", "johnny@example.com", "admin");
        $this->assertTrue($response['success']);
        $this->assertEquals("User updated successfully", $response['message']);
    }

    public function testUpdateUserWithPassword(): void {
        $this->userManager->addUser("Jane", "jane@example.com", "editor", "StrongP@ss2!");
        $response = $this->userManager->updateUser(1, "Jane Updated", "janeu@example.com", "editor", "NewP@ss3!");
        $this->assertTrue($response['success']);
    }

    public function testDeleteUser(): void {
        $this->userManager->addUser("John", "john@example.com", "admin", "StrongP@ss1!");
        $response = $this->userManager->deleteUser(1);
        $this->assertTrue($response['success']);
        $this->assertEquals("User deleted successfully", $response['message']);
    }
}

?>
