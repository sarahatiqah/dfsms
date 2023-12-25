<?php
session_start();
include('includes/config.php');

// Check if the user is logged in
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    // Check if the coupon ID is set in the URL
    if(isset($_GET['id'])){
        $couponId = base64_decode($_GET['id']);

        // Fetch coupon data from the database
        $query = mysqli_query($con, "SELECT * FROM tblcoupons WHERE CouponID = '$couponId'");
        $couponData = mysqli_fetch_assoc($query);

        // Check if the form is submitted
        if(isset($_POST['update'])){
            // Capture the new values from the form
            $newCouponCode = $_POST['couponCode'];
            $newDiscountAmount = $_POST['discountAmount'];
            $newDiscountPercentage = $_POST['discountPercentage'];
            $newValidFrom = $_POST['validFrom'];
            $newValidTo = $_POST['validTo'];

            // Update the coupon in the database
            if ($couponData['DiscountPercentage']) {
                $updateQuery = mysqli_query($con, "UPDATE tblcoupons SET CouponCode = '$newCouponCode', DiscountPercentage = '$newDiscountPercentage', ValidFrom = '$newValidFrom', ValidTo = '$newValidTo' WHERE CouponID = '$couponId'");
            } elseif ($couponData['DiscountAmount']) {
                $updateQuery = mysqli_query($con, "UPDATE tblcoupons SET CouponCode = '$newCouponCode', DiscountAmount = '$newDiscountAmount', ValidFrom = '$newValidFrom', ValidTo = '$newValidTo' WHERE CouponID = '$couponId'");
            } else {
                $updateQuery = mysqli_query($con, "UPDATE tblcoupons SET CouponCode = '$newCouponCode', DiscountAmount = '$newDiscountAmount', DiscountPercentage = '$newDiscountPercentage', ValidFrom = '$newValidFrom', ValidTo = '$newValidTo' WHERE CouponID = '$couponId'");
            }


            if($updateQuery){
                // Redirect with a success message
                header('Location: coupon.php?msg=Coupon updated successfully');
            } else {
                // Handle errors
                echo "<script>alert('Error occurred while updating the coupon.');</script>";
            }
        }

        ?>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .form-container {
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                width: 50%;
                margin: 0 auto;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
            }
            .form-group input[type="text"],
            .form-group input[type="number"],
            .form-group input[type="date"] {
                width: 100%;
                padding: 8px;
                border-radius: 4px;
                border: 1px solid #ddd;
                box-sizing: border-box;
            }
            .form-group input[type="submit"] {
                background-color: #0056b3;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }
            .form-group input[type="submit"]:hover {
                background-color: #004494;
            }
        </style>

        <script>
            function validateForm() {
                var discountAmount = document.getElementById('discountAmount').value;
                var discountPercentage = document.getElementById('discountPercentage').value;

                if (discountAmount !== '' && discountPercentage !== '') {
                    alert('Please fill either Discount Amount or Discount Percentage, not both.');
                    return false;
                }
                return true;
            }
        </script>

        <div class="form-container">
            <form method="post">
                <div class="form-group">
                    <label for="couponCode">Coupon Code:</label>
                    <input type="text" id="couponCode" name="couponCode" value="<?php echo htmlspecialchars($couponData['CouponCode']); ?>">
                </div>
                <?php
                $discountAmount = $couponData['DiscountAmount'];
                $discountPercentage = $couponData['DiscountPercentage'];

                // Check if there is a Discount Percentage
                if ($couponData['DiscountPercentage']) {
                    // If Discount Percentage exists, show Discount Percentage input
                    ?>
                    <div class="form-group">
                        <label for="discountPercentage">Discount Percentage:</label>
                        <input type="number" id="discountPercentage" name="discountPercentage" value="<?php echo htmlspecialchars($couponData['DiscountPercentage']); ?>" step="0.01">
                    </div>
                    <?php
                } else {
                    // Otherwise, show Discount Amount input
                    ?>
                    <div class="form-group">
                        <label for="discountAmount">Discount Amount:</label>
                        <input type="number" id="discountAmount" name="discountAmount" value="<?php echo htmlspecialchars($couponData['DiscountAmount']); ?>" step="0.01">
                    </div>
                    <?php
                }
                ?>
                <div class="form-group">
                    <label for="validFrom">Valid From:</label>
                    <input type="date" id="validFrom" name="validFrom" value="<?php echo htmlspecialchars($couponData['ValidFrom']); ?>">
                </div>
                <div class="form-group">
                    <label for="validTo">Valid To:</label>
                    <input type="date" id="validTo" name="validTo" value="<?php echo htmlspecialchars($couponData['ValidTo']); ?>">
                </div>
                <div class="form-group">
                    <input type="submit" name="update" value="Update">
                </div>
            </form>
        </div>
        <?php
    }
}
?>
