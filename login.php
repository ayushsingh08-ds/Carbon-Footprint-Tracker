<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $sql = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id']; // Updated to match the `user_id` field
            header("Location: main.html");
            exit;
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "No user found!";
    }

    $sql->close();
}
?>