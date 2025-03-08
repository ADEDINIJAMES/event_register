<?php
session_start();
require_once 'config/Database.php';
require_once 'config/config.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$perPage = 10; // Records per page
$offset = ($page - 1) * $perPage; // Offset for SQL query

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

// Query to fetch total number of records
$totalQuery = "SELECT COUNT(*) as total FROM registrations WHERE name LIKE ? OR name2 LIKE ? OR ticket LIKE ? OR email LIKE ? AND registration_type LIKE ?";
$totalStmt = $conn->prepare($totalQuery);
$searchParamTotal = "%$search%";
$filterParamTotal = "%$filter%";
$totalStmt->bind_param("sssss", $searchParamTotal, $searchParamTotal, $searchParamTotal, $searchParamTotal, $filterParamTotal);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total']; // Total number of records
$totalPages = ceil($totalRecords / $perPage); // Total number of pages

// Query to fetch paginated records
$sql = "SELECT * FROM registrations WHERE name LIKE ? OR name2 LIKE ? OR ticket LIKE ? OR email LIKE ? AND registration_type LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Bind parameters
$searchParam = "%$search%";
$searchParam2 = "%$search%";
$searchParam3 = "%$search%";
$searchParam4 = "%$search%";
$filterParam = "%$filter%";
$stmt->bind_param("sssssii", $searchParam, $searchParam2, $searchParam3, $searchParam4, $filterParam, $perPage, $offset);

// Execute query
$stmt->execute();
$result = $stmt->get_result();
$registrations = $result->fetch_all(MYSQLI_ASSOC);
$index=1;

// $totalAttendance = 0;
// foreach ($registrations as $registration) {
//     $totalAttendance += (int)$registration['attendance'];
// }

// Query to fetch all attendance values
$attendanceQuery = "SELECT attendance FROM registrations";
$attendanceResult = $conn->query($attendanceQuery);
$totalAttendance = 0;
while ($row = $attendanceResult->fetch_assoc()) {
    $totalAttendance += (int)$row['attendance'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
    /* Print-specific styles */
    @media print {
        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .no-print {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    }
    </style>
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">View Registrations</h1>

        <!-- Search and Filter Form -->
        <form method="GET" action="" class="mb-4">
            <div class="flex gap-4">
                <input type="text" name="search" placeholder="Search by name..."
                    value="<?= htmlspecialchars($search) ?>" class="p-2 border rounded">
                <input type="text" name="filter" placeholder="Filter by type..."
                    value="<?= htmlspecialchars($filter) ?>" class="p-2 border rounded">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded">Search</button>
                <button onclick="window.print()" class="bg-green-500 text-white p-2 rounded inline-block mt-4">Print All
                    Members</button>
                <button> <a href="logout.php" class="bg-red-500 text-white p-2 rounded">Logout</a></button>
            </div>
        </form>

        <!-- Data Table -->
        <div class="bg-white shadow-md rounded">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Partner's name</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Registration Type</th>
                        <th class="py-2 px-4 border-b">Ticket No</th>
                        <th class="py-2 px-4 border-b">Registration Date</th>
                        <th class="py-2 px-4 border-b">Payment Status</th>
                        <th class="py-2 px-4 border-b">Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($index) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($registration['name']) ?></td>
                        <td class="py-2 px-4 border-b">
                            <?= htmlspecialchars(!empty($registration['name2']) ? $registration['name2'] : '') ?>
                        </td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($registration['email']) ?></td>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($registration['registration_type']) ?></td>
                        <td class="py-2 px-4 border-b">
                            <?= htmlspecialchars($registration['ticket']? $registration['ticket'] : 'no ticket') ?>
                        </td>
                        <td class="py-2 px-4 border-b">
                            <?= $registration['registration_date'] ?>
                        </td>
                        <td class="py-2 px-4 border-b">
                            <?= htmlspecialchars($registration['payment_status']? $registration['payment_status'] : 'error') ?>
                        </td>
                        <td class="py-2 px-4 border-b">
                            <form method="POST" action="update_attendance.php" class="inline">
                                <input type="number" name="attendance"
                                    value="<?= htmlspecialchars($registration['attendance']) ?>" min="0" max="2"
                                    class="w-20 p-1 border rounded">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($registration['id']) ?>">
                                <button type="submit" class="bg-blue-500 text-white p-1 rounded ml-2">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php $index++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 p-4 bg-blue-100 rounded">
            <strong>Total Attendance:</strong> <?= $totalAttendance ?>
        </div>
        <!-- Pagination Controls -->
        <div class="flex justify-between mt-4">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>"
                class="bg-blue-500 text-white p-2 rounded">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>"
                class="bg-blue-500 text-white p-2 rounded">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>