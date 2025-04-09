<?php
require_once db_connection;
session_start();


CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


 


// === Submit Feedback ===
function submit_feedback($name, $email, $message) {
    $conn = connect_db();
     if (!isset($_SESSION['user_id'])) {
        return ["status" => "error", "message" => "User not logged in."];
    }

    $user_id = $_SESSION['user_id'];
    $content = $conn->real_escape_string($content);
    $rating = $conn->real_escape_string($rating);

    if (!$userID || !$content || !$rating) {
        return ["status" => "error", "message" => "All fields are required."];
    }

    $sql = "INSERT INTO feedback (userID, content, rating, timestamp) VALUES ('$user_id', '$content', '$rating', NOW())";
    if ($conn->query($sql) === TRUE) {
        return ["status" => "success", "message" => "Feedback submitted successfully."];
    } else {
        return ["status" => "error", "message" => "Failed to submit feedback."];
    }
}

// === Get All Feedbacks ===
function view_feedback() {
    $conn = connect_db();
    $sql = "SELECT id, name, email, message, submitted_at FROM feedbacks ORDER BY submitted_at DESC";
    $result = $conn->query($sql);

    $feedbacks = [];
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    return $feedbacks;
}

// === Delete Feedback by ID ===
function delete_feedback($id) {
    $conn = connect_db();
    $id = (int) $id;
    $sql = "DELETE FROM feedbacks WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            return ["status" => "success", "message" => "Feedback deleted successfully."];
        } else {
            return ["status" => "error", "message" => "Feedback not found."];
        }
    } else {
        return ["status" => "error", "message" => "Failed to delete feedback."];
    }
}

// === Request Handler ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'submit':
            echo json_encode(submit_feedback(
                $_POST['name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['message'] ?? ''
            ));
            break;

        case 'view':
            echo json_encode(view_feedback());
            break;

        case 'delete':
            echo json_encode(delete_feedback($_POST['id'] ?? 0));
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Unknown action."]);
            break;
    }

} else {
    // Help message
    echo "
    <h2>Feedback Manager</h2>
    <p>Use POST requests with action:</p>
    <ul>
        <li><strong>submit:</strong> name, email, message</li>
        <li><strong>get_all:</strong> no extra fields</li>
        <li><strong>delete:</strong> id</li>
    </ul>
    ";
}
?>
