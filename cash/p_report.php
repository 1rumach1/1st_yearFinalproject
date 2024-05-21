<?php
// Include the FPDF library
require('fpdf/fpdf.php');

// Set the width and height for the roll paper
$paperWidth = 57;

$conn = new mysqli('localhost', 'root', '', 'sales_inventory');
$prod_id = isset($_GET['prod_id']) ? $_GET['prod_id'] : '';

if ($prod_id === '') {
    $query = "SELECT * FROM sales WHERE report='' AND _date = CURRENT_DATE() AND cart='out'";
} else {
    // If a specific product ID is selected, filter by item_id
    $query = "SELECT * FROM sales WHERE report='' AND _date = CURRENT_DATE() AND cart='out' AND item_id='$prod_id'";
}
$result = $conn->query($query);

$numberOfRows = $result->num_rows;
$baseHeight = 70; // Initial base height
$lineHeight = 24; // Approximate height needed for each set of product details (6 rows per item * 4mm height per row)
$calculatedHeight = $baseHeight + ($numberOfRows * $lineHeight);

// Create a new PDF instance with custom page size
$pdf = new FPDF('P', 'mm', array($paperWidth, $calculatedHeight));
$pdf->SetAutoPageBreak(false); // Disable auto page break

// Add the first page
$pdf->AddPage();

// Set the title
$titleWidth = $paperWidth - 10; // Subtracting margins from the total width
$pdf->SetXY(5, 5); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($titleWidth, 5, "Lumengs Bigasan", 0, 1, "C");

// Set the report title
$pdf->SetXY(5, 10); // Set the position with equal left and right margin
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($titleWidth, 5, 'Product Report', 0, 1, "C");

// Set the date
$currentDate = date("Y-m-d");
$pdf->SetXY(5, 15); // Adjusted Y position for the date
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell($titleWidth, 5, $currentDate, 0, 1, "C");

// Fetch data from the database and print in the table


$yPosition = 20; // Starting Y position for the data rows

while ($row = $result->fetch_assoc()) {
    $pdf->SetFont('Arial', '', 7);

    // Time row
    $pdf->SetXY(5, $yPosition);
    $pdf->Cell($titleWidth / 2, 4, "Time:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['_time'], 0, 1, "R");

    // Person row
    $pdf->SetXY(5, $yPosition + 4);
    $pdf->Cell($titleWidth / 2, 4, "Person:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['who'], 0, 1, "R");

    // Item row
    $pdf->SetXY(5, $yPosition + 8);
    $pdf->Cell($titleWidth / 2, 4, "Item:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['item_name'], 0, 1, "R");

    // Price row
    $pdf->SetXY(5, $yPosition + 12);
    $pdf->Cell($titleWidth / 2, 4, "Price:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['item_price'], 0, 1, "R");

    // Quantity row
    $pdf->SetXY(5, $yPosition + 16);
    $pdf->Cell($titleWidth / 2, 4, "Quantity:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, $row['quantity'], 0, 1, "R");

    // Total row
    $pdf->SetXY(5, $yPosition + 20);
    $pdf->Cell($titleWidth / 2, 4, "Total:", 0, 0, "L");
    $pdf->Cell($titleWidth / 2, 4, 'P' . $row['total'], 0, 1, "R");
    $pdf->Cell($titleWidth / 2, 4, "-----------------------------------------------------------------------------------------------------", 0, 1, "C");
    $yPosition += 28; // Increase Y position for the next set of data
}

$pdf->SetFont('Arial', 'B', 7);

// Sales total
$overall = isset($_GET["overall"]) ? $_GET["overall"] : "";
$pdf->SetXY(5, $yPosition);
$pdf->Cell($titleWidth / 2, 4, "Sales Total:", 0, 0, "L");
$pdf->Cell($titleWidth / 2, 4, "P " . $overall, 0, 1, "R");

$pdf->Output(); // Output the PDF
?>
