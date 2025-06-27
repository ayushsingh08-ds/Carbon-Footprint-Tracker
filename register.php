<?php
include 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Check the received POST data
    echo '<pre>';
    print_r($_POST); // Outputs all form data sent via POST for debugging
    echo '</pre>';
    // exit; // Uncomment this to stop execution and debug the form data

    // Fetch input values
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $state_name = $_POST['state_id']; // Fetch the state name from the form

    // Validate that state_name (state_id in form) is not empty
    if (empty($state_name)) {
        die("Error: Please select a valid state.");
    }

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Check if the state exists in the States table
        $state_id = null;
        $stmt = $conn->prepare("SELECT state_id FROM States WHERE state_name = ?");
        $stmt->bind_param("s", $state_name);
        $stmt->execute();
        $stmt->bind_result($state_id);
        $stmt->fetch();
        $stmt->close();

        // If the state does not exist, throw an error
        if (!$state_id) {
            throw new Exception("The state name '$state_name' does not exist. Please provide a valid state name.");
        }

        // Insert the user into the Users table
        $stmt = $conn->prepare("INSERT INTO Users (username, email, password, state_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $email, $password, $state_id);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='index.html'>Login Here</a>";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }

        $stmt->close();
        $conn->commit(); // Commit the transaction
    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction in case of an error
        echo "Error: " . $e->getMessage();
    } finally {
        $conn->close(); // Close the database connection
    }
}
?>