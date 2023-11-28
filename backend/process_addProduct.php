<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../includes/config.php');
session_start();

$categoryName = $_POST['categoryName'];
$companyName = $_POST['companyName'];
$productName = $_POST['productName'];
$productPrice = $_POST['productPrice'];


$checkSql = "SELECT COUNT(*) AS count FROM `tblproducts` WHERE `ProductName` = ?";
$checkStmt = $con->prepare($checkSql);
$checkStmt->bind_param("s", $productName);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$count = $checkResult->fetch_assoc()['count'];

if ($count > 0) {
    echo "duplicated entry";
} else {
    // If no duplicate, proceed with the insertion
    $insertSql = "INSERT INTO `tblproducts`(`CategoryName`,`CompanyName`,`ProductName`,`ProductPrice`) VALUES (?, ?, ?, ?)";
    $insertStmt = $con->prepare($insertSql);
    $insertStmt->bind_param("sssd", $categoryName, $companyName, $productName, $productPrice);


    if ($insertStmt->execute()) {
        echo "success";
    } else {
        echo "failed to insert";
    }

    $insertStmt->close();
}

$checkStmt->close();
$con->close();
?>