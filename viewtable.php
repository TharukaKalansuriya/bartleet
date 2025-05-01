<?php
session_start();
require_once 'database.php';
require_once 'vendor/autoload.php'; // For TCPDF

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$tableName = $_GET['name'] ?? '';
$db = new Database();
$conn = $db->getConnection();

$columns = [];
$rows = [];

if (!empty($tableName)) {
    $result = $conn->query("SELECT * FROM `$tableName`");

    if ($result) {
        if ($result->num_rows > 0) {
            $columns = array_keys($result->fetch_assoc());
            $result->data_seek(0); 
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
    } else {
        die("Error loading table: " . $conn->error);
    }
}

// Handle PDF generation if requested
if (isset($_POST['generate_pdf']) && !empty($tableName)) {
    generatePDF($tableName, $columns, $rows);
    exit();
}

function generatePDF($tableName, $columns, $rows) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Table Export - ' . $tableName);
    $pdf->SetSubject('Table Data Export');
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Add title
    $pdf->Cell(0, 10, 'Table: ' . $tableName, 0, 1, 'C');
    $pdf->Ln(10);
    
    // Set font for table content
    $pdf->SetFont('helvetica', '', 10);
    
    // Calculate column widths
    $colCount = count($columns);
    $colWidth = 190 / max($colCount, 1); 
    
    // Table header
    $pdf->SetFillColor(255, 255, 0);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);
    
    foreach ($columns as $col) {
        $pdf->Cell($colWidth, 7, $col, 1, 0, 'C', 1);
    }
    $pdf->Ln();
    
    // Table content
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $fill = false;
    
    foreach ($rows as $row) {
        foreach ($row as $cell) {
            $pdf->Cell($colWidth, 6, $cell, 'LR', 0, 'L', $fill);
        }
        $pdf->Ln();
        $fill = !$fill;
    }
    
    // Closing line
    $pdf->Cell($colWidth * $colCount, 0, '', 'T');
    
    // Output PDF
    $pdf->Output($tableName . '_export.pdf', 'D');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Table - <?php echo htmlspecialchars($tableName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white p-6 min-h-screen">
    <div class="flex justify-between items-start mb-4">
        <a href="admindashboard.php" class="text-yellow-300 hover:text-yellow-500">&larr; Back to Dashboard</a>
        <?php if (!empty($columns)): ?>
            <form method="post" class="inline-block">
                <button type="submit" name="generate_pdf" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold py-2 px-4 rounded">
                    Download as PDF
                </button>
            </form>
        <?php endif; ?>
    </div>
    
    <h1 class="text-3xl font-bold mb-4 mt-2 text-yellow-400">Table: <?php echo htmlspecialchars($tableName); ?></h1>

    <div class="overflow-auto max-w-full bg-white text-black rounded-lg p-4 shadow-md">
        <?php if (!empty($columns)): ?>
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-yellow-200">
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th class="border px-4 py-2"><?php echo htmlspecialchars($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr class="hover:bg-yellow-100">
                            <?php foreach ($row as $cell): ?>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($cell); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-600">No data found in the table.</p>
        <?php endif; ?>
    </div>
</body>
</html>