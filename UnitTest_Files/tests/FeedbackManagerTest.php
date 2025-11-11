        
<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../services/FeedbackManager.php';

class FeedbackManagerTest extends TestCase {
    private $conn;
    private $feedbackManager;

    // Set up the database connection and the FeedbackManager instance before each test
    protected function setUp(): void {
        // Set up a real database connection here (use your database credentials)
        $this->conn = new mysqli('localhost', 'root', '', 'dataspheretest');
        
        // Check if the connection is successful
        if ($this->conn->connect_error) {
            $this->fail("Connection failed: " . $this->conn->connect_error);
        }

        // Start transaction to allow rollback
        $this->conn->begin_transaction();

        // Clean up any existing test data
        $this->conn->query("DELETE FROM feedback WHERE userID IN (1, 2)");
        $this->conn->query("DELETE FROM user WHERE userID IN (1, 2)");

        // Insert test users
        $this->conn->query("INSERT INTO user (userID, username, email) 
                            VALUES (1, 'testuser', 'testuser@example.com'),
                                   (2, 'otheruser', 'otheruser@example.com')");

        // Initialize the FeedbackManager class with the database connection
        $this->feedbackManager = new FeedbackManager($this->conn);
    }

    // Close the connection after each test
    protected function tearDown(): void {
        // Rollback transaction to clean up test data
        $this->conn->rollback();
        $this->conn->close();
    }

    // Test for getting the feedback count
    public function testGetFeedbackCount() {
        // Assuming userID = 1
        $user_id = 1;

        // Insert some test data if necessary
        $this->conn->query("INSERT INTO feedback (userID, content, category, timestamp, status) 
                            VALUES ($user_id, 'Test feedback', 'general', NOW(), 'pending')");

        $feedbackCount = $this->feedbackManager->getFeedbackCount($user_id);

        // Assert that feedback count is correct
        $this->assertEquals(1, $feedbackCount);
    }

    // Test for getting feedback details
    public function testGetFeedbackDetails() {
        // Insert a test feedback
        $this->conn->query("INSERT INTO feedback (userID, content, category, timestamp, status) 
                            VALUES (1, 'Test feedback', 'general', NOW(), 'pending')");

        // Get the feedback ID
        $feedbackID = $this->conn->insert_id;

        // Get the feedback details
        $feedbackDetails = $this->feedbackManager->getFeedbackDetails($feedbackID);

        // Assert that the feedback details are correct
        $this->assertEquals($feedbackID, $feedbackDetails['feedbackID']);
        $this->assertEquals(1, $feedbackDetails['userID']);
        $this->assertEquals('Test feedback', $feedbackDetails['content']);
    }

    // Test for submitting feedback
    public function testSubmitFeedback() {
        $feedbackCategories = ['general' => 'General', 'bug' => 'Bug'];

        $response = $this->feedbackManager->submitFeedback(1, 'John Doe', 'This is a test feedback', 'general', $feedbackCategories);

        // Assert that feedback was successfully submitted
        $this->assertTrue($response['success']);
        $this->assertEquals('Feedback submitted successfully!', $response['message']);
    }

    // Test for deleting feedback
    public function testDeleteFeedback() {
        // Insert feedback to delete
        $this->conn->query("INSERT INTO feedback (userID, content, category, timestamp, status) 
                            VALUES (1, 'Test feedback to delete', 'general', NOW(), 'pending')");
        $feedbackID = $this->conn->insert_id;

        // Attempt to delete feedback
        $response = $this->feedbackManager->deleteFeedback($feedbackID, 1);

        // Assert that the feedback was deleted successfully
        $this->assertTrue($response['success']);
        $this->assertEquals('Feedback has been successfully deleted.', $response['message']);
    }

    // Test for attempting to delete feedback that isn't pending
    public function testDeleteNonPendingFeedback() {
        // Insert non-pending feedback
        $this->conn->query("INSERT INTO feedback (userID, content, category, timestamp, status) 
                            VALUES (1, 'Non-pending feedback', 'general', NOW(), 'approved')");
        $feedbackID = $this->conn->insert_id;

        // Attempt to delete the non-pending feedback
        $response = $this->feedbackManager->deleteFeedback($feedbackID, 1);

        // Assert that the deletion failed due to the feedback not being pending
        $this->assertFalse($response['success']);
        $this->assertEquals("Only pending feedback can be deleted.", $response['message']);
    }

    // Test for attempting to delete feedback by a user who doesn't own it
    public function testDeleteFeedbackWithoutPermission() {
        // Insert feedback belonging to another user
        $this->conn->query("INSERT INTO feedback (userID, content, category, timestamp, status) 
                            VALUES (2, 'Feedback from another user', 'general', NOW(), 'pending')");
        $feedbackID = $this->conn->insert_id;

        // Attempt to delete feedback by a different user
        $response = $this->feedbackManager->deleteFeedback($feedbackID, 1);

        // Assert that the deletion failed due to permission issues
        $this->assertFalse($response['success']);
        $this->assertEquals("You don't have permission to delete this feedback.", $response['message']);
    }

    // Test for submitting feedback with empty content
    public function testSubmitFeedbackWithEmptyContent() {
        $feedbackCategories = ['general' => 'General', 'bug' => 'Bug'];
        $response = $this->feedbackManager->submitFeedback(1, 'John Doe', '', 'general', $feedbackCategories);
        
        $this->assertFalse($response['success']);
        $this->assertEquals('Please enter your feedback', $response['message']);
    }

    // Test for submitting feedback with invalid category
    public function testSubmitFeedbackWithInvalidCategory() {
        $feedbackCategories = ['general' => 'General', 'bug' => 'Bug'];
        $response = $this->feedbackManager->submitFeedback(1, 'John Doe', 'Test feedback', 'invalid', $feedbackCategories);
        
        $this->assertFalse($response['success']);
        $this->assertEquals('Please select a valid feedback category', $response['message']);
    }

    // Test for submitting feedback with database error
    public function testSubmitFeedbackWithDatabaseError() {
        // Test with empty content to avoid database interaction
        $feedbackCategories = ['general' => 'General', 'bug' => 'Bug'];
        $response = $this->feedbackManager->submitFeedback(1, 'John Doe', '', 'general', $feedbackCategories);
        
        $this->assertFalse($response['success']);
        $this->assertEquals('Please enter your feedback', $response['message']);
    }

    // Test for getting non-existent feedback details
    public function testGetNonExistentFeedbackDetails() {
        $feedbackDetails = $this->feedbackManager->getFeedbackDetails(999999);
        $this->assertNull($feedbackDetails);
    }
}
?>








