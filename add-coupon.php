<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['aid']) == 0) {
    header('location:logout.php');
} else {
    if(isset($_POST['submit'])) {
//        // Retrieve form data
//        $couponCode = $_POST['couponCode'];
//        $validFrom = $_POST['validFrom'];
//        $validTo = $_POST['validTo'];
//        $discountType = '';
//        $discountValue = 0;
//
//        // Assign values based on discount type
//        if($discountType == 'amount') {
//            $discountAmount = $discountValue;
//        } else {
//            $discountPercentage = $discountValue;
//        }
//
//        $discountAmount = $_POST['discountAmount'];
//        $discountPercentage = $_POST['discountPercentage'];

        // Retrieve form data
        $couponCode = $_POST['couponCode'];
        $validFrom = $_POST['validFrom'];
        $validTo = $_POST['validTo'];
        $discountType = $_POST['discountType'];
        $discountValue = $_POST['discountValue'];

        // Initialize variables
        $discountAmount = 0;
        $discountPercentage = 0;

        // Assign values based on discount type
        if ($discountType == 'amount') {
            $discountAmount = $discountValue;
        } elseif ($discountType == 'percentage') {
            $discountPercentage = $discountValue;
        }

        // Insert into database
        $query = mysqli_query($con, "INSERT INTO tblcoupons (CouponCode, DiscountAmount, DiscountPercentage, ValidFrom, ValidTo) VALUES ('$couponCode', '$discountAmount', '$discountPercentage', '$validFrom', '$validTo')");
        if($query) {
            echo "<script>alert('Coupon added successfully.');</script>";
            echo "<script>window.location.href='coupon.php'</script>";
        } else {
            echo "<script>alert('Error occurred while adding the coupon.');</script>";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <!-- Include necessary head elements -->
    </head>
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
        .form-group select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
    </style>
    <body>
    <!-- Include necessary body elements -->
    <div class="form-container">
        <form method="post">
            <div class="form-group">
                <label>Coupon Code</label>
                <input type="text" name="couponCode" required>
            </div>
            <div class="form-group">
                <label>Discount Type</label>
                <select name="discountType" required>
                    <option value="amount">Discount Amount</option>
                    <option value="percentage">Discount Percentage</option>
                </select>
            </div>
            <div class="form-group">
                <label>Discount Value</label>
                <input type="number" name="discountValue" required>
            </div>
            <div class="form-group">
                <label>Valid From</label>
                <input type="date" name="validFrom" required>
            </div>
            <div class="form-group">
                <label>Valid To</label>
                <input type="date" name="validTo" required>
            </div>
            <div class="form-group">
                <button type="submit" name="submit">Add Coupon</button>
            </div>
        </form>
    </div>
    </body>
    </html>
<?php } ?>
