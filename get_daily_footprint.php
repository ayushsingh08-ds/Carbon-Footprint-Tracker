<?php
// filepath: c:\xampp\htdocs\carbonPorject\get_daily_footprint.php
header('Content-Type: application/json');
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get today's date
    $today = date('Y-m-d');
    
    // Calculate today's total emissions for the logged-in user
    $sql = "
        SELECT 
            SUM(emission_value) AS total_emissions
        FROM emissions
        WHERE user_id = ? AND DATE(date) = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Check if any emissions were found
    $footprint = 0;
    if ($row && $row['total_emissions'] !== null) {
        $footprint = round($row['total_emissions'], 2);
    }
    
    // If no emissions today, get the most recent day's data
    if ($footprint == 0) {
        $sql = "
            SELECT 
                SUM(emission_value) AS total_emissions,
                DATE(date) AS emission_date
            FROM emissions
            WHERE user_id = ?
            GROUP BY DATE(date)
            ORDER BY DATE(date) DESC
            LIMIT 1
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row && $row['total_emissions'] !== null) {
            $footprint = round($row['total_emissions'], 2);
        }
    }
    
    // Determine status message based on footprint amount
    $status = 'Good jobs!';
    $achievement = false;
    
    if ($footprint > 20) {
        $status = 'Try to reduce emissions tomorrow';
    } elseif ($footprint > 10) {
        $status = 'You\'re doing well!';
    } else if ($footprint > 0) {
        $status = 'Excellent! Great eco-footprint';
        $achievement = true;
    } else {
        $status = 'No emissions recorded yet';
    }
    
    // Return formatted data
    echo json_encode([
        'footprint' => $footprint,
        'status' => $status,
        'achievement' => $achievement
    ]);
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
