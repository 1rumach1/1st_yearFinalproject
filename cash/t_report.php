<?php
// Include the FPDF library
require('fpdf\fpdf.php');

// Convert inches to millimeters for the width (2 1/4 inches = 57mm)
$paperWidth = 57;

// Fetch data from the database to calculate the required height
$conn = new mysqli('localhost', 'root', '', 'sales_inventory');
$result = $conn->query("SELECT * FROM sales WHERE report=''");

$numberOfRows = $result->num_rows;
$baseHeight = 60; // Initial base height
$lineHeight = 24; // Approximate height needed for each set of product details (6 rows per item * 4mm height per row)
$calculatedHeight = $baseHeight + ($numberOfRows * $lineHeight);

// Create a new PDF instance with calculated height
$pdf = new FPDF('P', 'mm', array($paperWidth, $calculatedHeight));
$pdf->SetAutoPageBreak(false); // Disable auto page break
$pdf->AddPage();

// Set the title
$titleWidth = $paperWidth - 10; // Subtracting margins from the total width
$pdf->SetXY(5, 5); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($titleWidth, 5, "Lumengs Bigasan", 0, 1, "C");

// Set the subtitle (Terminal Report)
$pdf->SetXY(5, 10); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($titleWidth, 5, 'Terminal Report', 0, 1, "C");

// Set the date
$currentDate = date("Y-m-d");
$pdf->SetXY(5, 15); // Adjusted Y position for the date
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell($titleWidth, 4, $currentDate, 0, 1, "C");

$yPosition = 20; // Starting Y position for the data rows

while ($row = $result->fetch_assoc()) {
    $pdf->SetFont('Arial', '', 7);

    // Time row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Time:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['_time'], 0, 1, "R");
    $yPosition += 3;

    // Person row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Person:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['who'], 0, 1, "R");
    $yPosition += 3;

    // Item row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Item:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['item_name'], 0, 1, "R");
    $yPosition += 3;

    // Price row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Price:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['item_price'], 0, 1, "R");
    $yPosition += 3;

    // Quantity row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Quantity:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['quantity'], 0, 1, "R");
    $yPosition += 3;

    // Total row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Total:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['total'], 0, 1, "R");
    $pdf->Cell($titleWidth / 2, 4, "-----------------------------------------------------------------------------------------------------", 0, 1, "C");
    $yPosition += 6; // Add a bit more space between each product set
}

// Fetch overall from the query parameters
$overall = isset($_GET["overall"]) ? $_GET["overall"] : "";
$pdf->SetFont('Arial', 'B', 7);

// Total Price
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth / 2, 4, "Total:", 0, 0, "L");
$pdf->Cell($titleWidth / 2, 4, "P " . $overall, 0, 1, "R");

$pdf->Output(); // Output the PDF
?>
