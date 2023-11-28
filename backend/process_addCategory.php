<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('../includes/config.php');
    session_start();

    $categoryName = $_POST['categoryName'];
    $categoryCode = $_POST['categoryCode'];


    // Check if the same record exists
    $checkSql = "SELECT COUNT(*) AS count FROM `tblcategory` WHERE `CategoryName` = ? OR `CategoryCode` = ?";
    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("ss", $categoryName, $categoryCode);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $count = $checkResult->fetch_assoc()['count'];

    if ($count > 0) {
        echo "duplicated entry";
    } else {
        // If no duplicate, proceed with the insertion
        $insertSql = "INSERT INTO `tblcategory`(`CategoryName`, `CategoryCode`) VALUES (?,?)";
        $insertStmt = $con->prepare($insertSql);
        $insertStmt->bind_param("ss", $categoryName, $categoryCode);

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
