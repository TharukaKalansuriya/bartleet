<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}
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

// Handle Excel generation if requested
if (isset($_POST['generate_excel']) && !empty($tableName)) {
    generateExcel($tableName, $columns, $rows);
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
    $pdf->SetFillColor(255, 107, 53); // Match the primary color
    $pdf->SetTextColor(255, 255, 255);
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

function generateExcel($tableName, $columns, $rows) {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $tableName . '_export.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Start the HTML table
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    echo '<!--[if gte mso 9]><xml>';
    echo '<x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
    echo '<x:Name>' . $tableName . '</x:Name>';
    echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
    echo '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>';
    echo '</xml><![endif]-->';
    echo '<style>';
    echo 'td, th { border: 1px solid #000000; }';
    echo 'th { background-color: #FF6B35; color: white; font-weight: bold; }';
    echo '.table-row-alt:nth-child(even) { background-color: #F5F5F5; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    echo '<table border="1">';
    
    // Add header row
    echo '<tr>';
    foreach ($columns as $column) {
        echo '<th>' . htmlspecialchars($column) . '</th>';
    }
    echo '</tr>';
    
    // Add data rows
    $rowNum = 0;
    foreach ($rows as $row) {
        $rowClass = ($rowNum % 2 == 0) ? '' : 'table-row-alt';
        echo '<tr class="' . $rowClass . '">';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
        $rowNum++;
    }
    
    // Close table and HTML
    echo '</table>';
    echo '</body>';
    echo '</html>';
    
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Table - <?php echo htmlspecialchars($tableName); ?> - Craze Kicks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-hover: #FF8E63;
            --secondary: #2E2E3A;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-excel {
            background-color: #1D6F42;
            transition: all 0.2s;
        }
        
        .btn-excel:hover {
            background-color: #27864F;
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-gradient {
            background: linear-gradient(90deg, var(--primary) 0%, #FF8E63 100%);
        }
        
        th {
            background-color: var(--primary);
            color: white;
        }
        
        .table-row-alt:nth-child(even) {
            background-color: rgba(255, 107, 53, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <?php include "navbar.php" ?>

    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="glass-card rounded-xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold">Table: <?php echo htmlspecialchars($tableName); ?></h1>
                    <p class="text-lg opacity-80 mt-2">Viewing table contents</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 mt-6 md:mt-0">
                    <a href="admindashboard.php" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                    
                    <?php if (!empty($columns)): ?>
                    <div class="flex gap-2">
                        <form method="post" class="inline-block">
                            <button type="submit" name="generate_pdf" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                                <i class="fas fa-file-pdf mr-2"></i> Download PDF
                            </button>
                        </form>
                        <form method="post" class="inline-block">
                            <button type="submit" name="generate_excel" class="btn-excel text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                                <i class="fas fa-file-excel mr-2"></i> Download Excel
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-container rounded-xl overflow-hidden">
                <div class="header-gradient text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Table Content</h2>
                    <?php if (!empty($columns)): ?>
                    <div class="text-sm">
                        <span class="bg-white bg-opacity-20 py-1 px-3 rounded-full"><?php echo count($rows); ?> Records</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($columns)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto text-gray-800">
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                <th class="py-3 px-4 text-left"><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                            <tr class="table-row-alt hover:bg-gray-100 transition-colors">
                                <?php foreach ($row as $cell): ?>
                                <td class="py-3 px-4 border-t border-gray-200"><?php echo htmlspecialchars($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-16 bg-white">
                    <i class="fas fa-table text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-600">No data found in the table.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-white bg-black bg-opacity-50">
        <p>© <?= date('Y') ?> Craze Kicks Admin Dashboard</p>
    </footer>
</body>
</html>