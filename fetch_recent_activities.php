<?php
include 'db.php';

function timeElapsed($datetime) {
    if (!$datetime) return "Unknown time"; // Handle null cases

    $timestamp = strtotime($datetime);
    if (!$timestamp) return "Invalid time"; // Handle invalid dates

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return ">1 minute ago";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . " minute" . ($minutes == 1 ? "" : "s") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours == 1 ? "" : "s") . " ago";
    } elseif ($diff < 604800) { // Less than 7 days
        $days = floor($diff / 86400);
        return $days . " day" . ($days == 1 ? "" : "s") . " ago";
    } elseif ($diff < 2592000) { // Less than 30 days
        $weeks = floor($diff / 604800);
        return $weeks . " week" . ($weeks == 1 ? "" : "s") . " ago";
    } elseif ($diff < 31536000) { // Less than a year
        $months = floor($diff / 2592000);
        return $months . " month" . ($months == 1 ? "" : "s") . " ago";
    } else {
        $years = floor($diff / 31536000);
        return $years . " year" . ($years == 1 ? "" : "s") . " ago";
    }
}

// Query the latest activities
$query1 = "SELECT full_name, local, contact_name, date_added FROM miaalocals_user_inputs ORDER BY date_added DESC LIMIT 10";
$query2 = "SELECT full_name, employee_id, date_created FROM user_approval ORDER BY date_created DESC LIMIT 10";

$result1 = mysqli_query($conn, $query1);
$result2 = mysqli_query($conn, $query2);

$activities = [];

// Fetch from miaalocals_user_inputs
while ($row = mysqli_fetch_assoc($result1)) {
    $activities[] = [
        'message' => "{$row['full_name']} was added a local: {$row['local']} - {$row['contact_name']}",
        'date' => $row['date_added'],
        'time_ago' => timeElapsed($row['date_added'])
    ];
}

// Fetch from user_approval
while ($row = mysqli_fetch_assoc($result2)) {
    $activities[] = [
        'message' => "{$row['full_name']} - {$row['employee_id']} was registered",
        'date' => $row['date_created'],
        'time_ago' => timeElapsed($row['date_created'])
    ];
}

// Sort activities by date (latest first)
usort($activities, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Output the JSON result
header('Content-Type: application/json');
echo json_encode($activities, JSON_PRETTY_PRINT);
?>
