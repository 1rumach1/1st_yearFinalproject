<?php
// Include the FPDF library
require('fpdf\fpdf.php');
$currentDate = date('Y-m-d'); // Format the date as needed

// Convert inches to millimeters for the width (2 1/4 inches = 57mm)
$paperWidth = 57;

// Fetch data from the database to calculate the required height
$conn = new mysqli('localhost', 'root', '', 'sales_inventory');
$result = $conn->query("SELECT * FROM sales WHERE cart='in'");

$numberOfRows = $result->num_rows;
$baseHeight = 60; // Initial base height
$lineHeight = 20; // Approximate height needed for each set of product details
$calculatedHeight = $baseHeight + ($numberOfRows * $lineHeight);

// Create a new PDF instance with calculated height
$pdf = new FPDF('P', 'mm', array($paperWidth, $calculatedHeight));
$pdf->SetAutoPageBreak(false); // Disable auto page break
$pdf->AddPage();

// Set the title
$titleWidth = $paperWidth - 10; // Subtracting margins from the total width
$pdf->SetXY(5, 5); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($titleWidth, 5, "Lumengs Bigasan", 0, 1, "C");

// Set the subtitle (Receipt)
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(5, 10);
$pdf->Cell($titleWidth, 5, "Receipt", 0, 1, "C");

// Set the date
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetXY(5, 14);
$pdf->Cell($titleWidth, 4, "Date: " . $currentDate, 0, 1, "C");

$yPosition = 20; // Starting Y position for the data rows

while ($row = $result->fetch_assoc()) {
    $pdf->SetFont('Arial', '', 7);

    // Product row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Product:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['item_name'], 0, 1, "R");
    $yPosition += 4;

    // Price row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Price:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['item_price'], 0, 1, "R");
    $yPosition += 4;

    // Quantity row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Quantity:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['quantity'], 0, 1, "R");
    $yPosition += 4;

    // Total row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Total Price:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['total'], 0, 1, "R");
    $pdf->Cell($titleWidth / 2, 4, "--------------------------------------------------------------------------------------", 0, 1, "C");
    $yPosition += 6; // Add a bit more space between each product set
}

// Fetch overall from the query parameters
$overall = isset($_GET["overall"]) ? $_GET["overall"] : "";
$pay = isset($_GET["pay"]) ? $_GET["pay"] : "";
$change = isset($_GET["change"]) ? $_GET["change"] : "";

// Output "Overall", "Pay", and "Change" below the table
$pdf->SetFont('Arial', 'B', 7);

// Total Price
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth / 2, 4, "OverAll Price:", 0, 0, "L");
$pdf->Cell($titleWidth / 2, 4, "P " . $overall, 0, 1, "R");
$yPosition += 4;

// Payment
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth / 2, 4, "Payment:", 0, 0, "L");
$pdf->Cell($titleWidth / 2, 4, "P " . $pay, 0, 1, "R");
$yPosition += 4;

// Line separator
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth, 4, "________________________________________", 0, 1, "C");
$yPosition += 4;

// Change
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth / 2, 4, "Change:", 0, 0, "L");
$pdf->Cell($titleWidth / 2, 4, "P " . $change, 0, 1, "R");

$pdf->Output(); // Output the PDF
?>
