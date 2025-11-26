                            
<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../services//ResponseLogic.php';

class ResponseLogicTest extends TestCase {
    private $conn;
    private $responseLogic;
    private $feedbackId;

    protected function setUp(): void {
        // Connect to your test database
        $this->conn = new mysqli('localhost', 'root', '', 'dataspheretest');

        if ($this->conn->connect_error) {
            $this->fail("Connection failed: " . $this->conn->connect_error);
        }

        $this->conn->query("START TRANSACTION"); // So test changes can be rolled back

        $this->responseLogic = new ResponseLogic($this->conn);

        // Insert dummy feedback row
        $this->conn->query("INSERT INTO user (userID, username) VALUES (9999, 'Test User') ON DUPLICATE KEY UPDATE username='Test User'");

        $this->conn->query("INSERT INTO feedback (feedbackID, userID, content, status) VALUES (9999, 9999, 'Test message', 'pending') ON DUPLICATE KEY UPDATE content='Test message', status='pending'");
        $this->feedbackId = 9999;
    }

    protected function tearDown(): void {
        // Rollback changes to leave test DB clean
        $this->conn->query("ROLLBACK");
        $this->conn->close();
    }

    public function testRespondToFeedbackSuccess(): void {
        $result = $this->responseLogic->respondToFeedback($this->feedbackId, 1, "This is a test response.");

        $this->assertTrue($result['success']);
        $this->assertEquals('Response submitted successfully.', $result['message']);

        // Optionally assert DB state
        $responseResult = $this->conn->query("SELECT * FROM response WHERE feedbackID = {$this->feedbackId}");
        $this->assertGreaterThan(0, $responseResult->num_rows);

        $feedbackStatus = $this->conn->query("SELECT status FROM feedback WHERE feedbackID = {$this->feedbackId}")->fetch_assoc();
        $this->assertEquals('responded', $feedbackStatus['status']);

        $notificationResult = $this->conn->query("SELECT * FROM notification WHERE feedbackID = {$this->feedbackId}");
        $this->assertGreaterThan(0, $notificationResult->num_rows);
    }
}





























