<?php
// filepath: c:\xampp\htdocs\carbonPorject\get_emissions.php
header('Content-Type: application/json');
include 'db.php'; // Changed from '../db.php' to match your file structure
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$period = isset($_GET['period']) ? $_GET['period'] : 'week';

try {
    $emissions = [];
    $periodName = '';
    
    switch($period) {
        case 'week':
            // Get data for current week
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate = date('Y-m-d', strtotime('sunday this week'));

            // Calculate which week of the month this is
            $firstDayOfMonth = date('Y-m-01', strtotime($startDate));
            $dayOfMonth = date('j', strtotime($startDate));
            $weekOfMonth = ceil($dayOfMonth / 7);

            // Add ordinal suffix (1st, 2nd, 3rd, 4th, etc.)
            $ordinalSuffix = 'th';
            if ($weekOfMonth == 1) $ordinalSuffix = 'st';
            if ($weekOfMonth == 2) $ordinalSuffix = 'nd';
            if ($weekOfMonth == 3) $ordinalSuffix = 'rd';

            // Format the period name
            $periodName = $weekOfMonth . $ordinalSuffix . ' Week of ' . date('F Y', strtotime($startDate));
            
            $sql = "
                SELECT 
                    DATE(date) as emission_date,
                    WEEKDAY(date) as day_number,
                    SUM(emission_value) as total
                FROM emissions
                WHERE user_id = ? 
                AND date BETWEEN ? AND ?
                GROUP BY DATE(date), WEEKDAY(date)
                ORDER BY day_number
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Create a full week array with all days
            $weekdays = [
                0 => 'Mon',
                1 => 'Tue',
                2 => 'Wed',
                3 => 'Thu',
                4 => 'Fri',
                5 => 'Sat',
                6 => 'Sun'
            ];
            
            // Initialize with zeros
            $weekData = array_fill_keys(array_keys($weekdays), 0);
            
            // Fill in actual data
            while ($row = $result->fetch_assoc()) {
                $day_number = (int)$row['day_number'];
                $weekData[$day_number] = round($row['total'], 2);
            }
            
            // Format for response
            foreach ($weekData as $dayNum => $value) {
                $emissions[] = [
                    'date' => $weekdays[$dayNum],
                    'value' => $value,
                    'day_number' => $dayNum
                ];
            }
            break;
            
        case 'month':
            // Get data for current month
            $startDate = date('Y-m-01'); // First day of current month
            $endDate = date('Y-m-t');    // Last day of current month
            $periodName = date('F Y');    // e.g., "May 2025"
            $daysInMonth = date('t'); // Number of days in current month
            
            $sql = "
                SELECT 
                    DAY(date) as day_of_month,
                    SUM(emission_value) as total
                FROM emissions
                WHERE user_id = ? 
                AND date BETWEEN ? AND ?
                GROUP BY DAY(date)
                ORDER BY day_of_month
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Create array with all days in month
            $monthData = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $monthData[$i] = 0;
            }
            
            // Fill in actual data
            while ($row = $result->fetch_assoc()) {
                $day = (int)$row['day_of_month'];
                $monthData[$day] = round($row['total'], 2);
            }
            
            // Format for response
            foreach ($monthData as $day => $value) {
                $emissions[] = [
                    'date' => (string)$day,
                    'value' => $value
                ];
            }
            break;
            
        case 'year':
            // Get data for current year
            $startDate = date('Y-01-01'); // First day of current year
            $endDate = date('Y-12-31');   // Last day of current year
            $currentYear = date('Y');      
            $periodName = "Year $currentYear";
            
            $sql = "
                SELECT 
                    MONTH(date) as month_number,
                    SUM(emission_value) as total
                FROM emissions
                WHERE user_id = ? 
                AND date BETWEEN ? AND ?
                GROUP BY MONTH(date)
                ORDER BY month_number
            ";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Create array with all months
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];
            
            // Initialize with zeros
            $yearData = array_fill_keys(array_keys($months), 0);
            
            // Fill in actual data
            while ($row = $result->fetch_assoc()) {
                $month = (int)$row['month_number'];
                $yearData[$month] = round($row['total'], 2);
            }
            
            // Format for response
            foreach ($yearData as $monthNum => $value) {
                $emissions[] = [
                    'date' => $months[$monthNum],
                    'value' => $value,
                    'month_number' => $monthNum
                ];
            }
            break;
    }
    
    echo json_encode([
        'emissions' => $emissions,
        'period' => $periodName
    ]);
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>