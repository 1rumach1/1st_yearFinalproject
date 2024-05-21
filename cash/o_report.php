<?php
// Include the FPDF library
require('fpdf\fpdf.php');

// Convert inches to millimeters for the width (2 1/4 inches = 57mm)
$paperWidth = 57;
$paperHeight = 70; // Height is arbitrary, but can be long enough to fit your content

// Create a new PDF instance with custom page size
$pdf = new FPDF('P', 'mm', array($paperWidth, $paperHeight));
$pdf->SetAutoPageBreak(false); // Disable auto page break
$pdf->AddPage();

// Set the title
$titleWidth = $paperWidth - 10; // Subtracting margins from the total width
$pdf->SetXY(5, 5); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($titleWidth, 5, "Lumengs Bigasan", 0, 1, "C");

// Set the subtitle
$pdf->SetXY(5, 10); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($titleWidth, 5, ' Cash Report: ', 0, 1, "C");

// Set the date
$currentDate = date("Y-m-d");
$pdf->SetXY(5, 15); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell($titleWidth, 5, $currentDate, 0, 1, "C");

// Add header for the table
$headerWidth = $paperWidth - 10; // Subtracting margins from the total width
$headerCellWidth = $headerWidth / 5; // Divide by 5 for 5 header cells
$pdf->SetXY(5, 20); // Adjusted Y position for the header
$pdf->SetFont('Arial', 'B', 4); // Reduce font size if necessary
$pdf->Cell($headerCellWidth, 5, 'Time', 0, 0, "C"); // Adjusted width to fit within 57mm
$pdf->Cell($headerCellWidth, 5, 'Person', 0, 0, "C");
$pdf->Cell($headerCellWidth, 5, 'Type', 0, 0, "C");
$pdf->Cell($headerCellWidth, 5, 'In_amount', 0, 0, "C");
$pdf->Cell($headerCellWidth, 5, 'Out_amount', 0, 0, "C");

// Fetch data from the database and print in the table
$conn = new mysqli('localhost', 'root', '', 'sales_inventory');
$result = $conn->query("SELECT * FROM cash_history WHERE _date = CURRENT_DATE() ORDER BY id DESC");
$yPosition = 22; // Starting Y position for the data rows (below the header)

while ($row = $result->fetch_assoc()) {
    $pdf->SetFont('Arial', '', 4);
    $pdf->SetXY(5, $yPosition); // Set position for each row
    $pdf->Cell($headerCellWidth, 5, $row['_time'], 0, 0, "C"); // Adjusted cell width
    $pdf->Cell($headerCellWidth, 5, $row['who'], 0, 0, "C"); // Adjusted cell width
    $pdf->Cell($headerCellWidth, 5, $row['type'], 0, 0, "C"); // Adjusted cell width
    $pdf->Cell($headerCellWidth, 5, 'P'.$row['in_amount'], 0, 0, "C"); // Adjusted cell width
    $pdf->Cell($headerCellWidth, 5, $row['out_amount'], 0, 0, "C"); // Adjusted cell width
    $yPosition += 2; // Move to the next row position (height of each row is 7)
}

// Fetch overall from the query parameters
$overall = isset($_GET["overall"]) ? $_GET["overall"] : "";

// Set position for the overall total, dynamically below the last row of the table
$pdf->SetFont('Arial', 'B', 5);
$pdf->SetXY(5, $yPosition + 3); // Adding some space after the last row
$pdf->Cell($headerWidth, 7, "Cash Total: P " . $overall, 0, 1, "R");

$pdf->Output(); // Output the PDF
?>
