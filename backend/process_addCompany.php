<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('../includes/config.php');
    session_start();

    $companyName = $_POST['companyName'];
    
    $checkSql = "SELECT COUNT(*) AS count FROM `tblcompany` WHERE `CompanyName` = ?";
    $checkStmt = $con->prepare($checkSql);
    $checkStmt->bind_param("s", $companyName);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $count = $checkResult->fetch_assoc()['count'];

    if ($count > 0) {
        echo "duplicated entry";
    } else {
        // If no duplicate, proceed with the insertion
        $insertSql = "INSERT INTO `tblcompany`(`CompanyName`) VALUES (?)";
        $insertStmt = $con->prepare($insertSql);
        $insertStmt->bind_param("s", $companyName); // Change $categoryName to $companyName

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
