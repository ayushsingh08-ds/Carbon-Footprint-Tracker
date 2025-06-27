<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$email = $data['email'];
$state_id = $data['state_id'];

$query = "UPDATE users SET username = ?, email = ?, state_id = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $username, $email, $state_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['error' => 'Failed to update profile']);
}

$stmt->close();
$conn->close();
?>