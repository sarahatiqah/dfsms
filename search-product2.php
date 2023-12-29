<?php
    session_start();
    //error_reporting(0);
    include "includes/config.php";
    if (strlen($_SESSION["aid"] == 0)) {
        header("location:logout.php");
    } else {

        if (isset($_GET['error']) && $_GET['error'] == 'invalidcoupon') {
            echo '<script>alert("Invalid coupon code. Please try again.");</script>';
        }

        // Code for adding and checking Coupon Code Validity
        if (isset($_POST['couponvalidate'])) {
            $couponCode = mysqli_real_escape_string($con, $_POST["couponcode"]);
            $query = "SELECT * FROM tblcoupons WHERE CouponCode = ? AND ValidFrom <= CURDATE() AND ValidTo >= CURDATE()";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $couponCode);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $discountAmount = $row['DiscountAmount'];
                $discountPercentage = $row['DiscountPercentage'];
                $discountInfo = "";
                $discountValue = "";

                $_SESSION['valid_coupon_code'] = $couponCode;

                // Check if discount is in amount or percentage and create a message accordingly
                if (!is_null($discountAmount) && $discountAmount > 0) {
                    $discountInfo = "Discount Amount: $" . $discountAmount;
                    $discountValue = "-".$discountAmount;
                } elseif (!is_null($discountPercentage) && $discountPercentage > 0) {
                    $discountInfo = "Discount Percentage: " . $discountPercentage . "%";
                    $discountValue = -$discountPercentage."%";
                } else {
                    $discountInfo = "No discount available for this coupon.";
                }

                // Add Coupon In Cart
                $itemArray = [
                    "coupon" => [
                        "catname" => "Coupon",
                        "compname" => "None",
                        "quantity" => 0,
                        "pname" => $couponCode,
                        "price" => $discountValue,
                        "code" => "coupon",
//                        "couponID" => $row["CouponID"],
                    ],
                ];

                if (!empty($_SESSION["cart_item"])) {
                    // Remove any existing coupon from the cart
                    foreach ($_SESSION["cart_item"] as $k => $v) {
                        if ($v["catname"] === "Coupon") {
                            unset($_SESSION["cart_item"][$k]);
                        }
                    }
                    // Add the new coupon
                    $_SESSION["cart_item"] = $_SESSION["cart_item"] + $itemArray;
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }

                echo "<script>alert('Coupon code is valid. $discountInfo');</script>";
            } else {
                echo "<script>alert('Invalid coupon code. Please enter a different code.');</script>";
            }
        }

        //code for Cart
        if (!empty($_GET["action"])) {
            switch ($_GET["action"]) {
                //code for adding product in cart
                case "add":
                    if (!empty($_POST["quantity"])) {
                        $pid = $_GET["pid"];
                        $result = mysqli_query($con, "SELECT * FROM tblproducts WHERE id='$pid'");
                        $productByCode = mysqli_fetch_array($result);

                        $itemArray = [
                            $productByCode["id"] => [
                                'catname' => $productByCode["CategoryName"],
                                'compname' => $productByCode["CompanyName"],
                                'quantity' => $_POST["quantity"],
                                'pname' => $productByCode["ProductName"],
                                'price' => $productByCode["ProductPrice"],
                                'code' => strval($productByCode["id"])
                            ]
                        ];

                        if (!empty($_SESSION["cart_item"])) {
                            if (
                                in_array(
                                    $productByCode["id"],
                                    array_keys($_SESSION["cart_item"])
                                )
                            ) {
                                foreach ($_SESSION["cart_item"] as $k => $v) {
                                    if ($productByCode["id"] == $k) {

                                        if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                                            $_SESSION["cart_item"][$k]["quantity"] = 0;
                                        }

                                        $_SESSION["cart_item"][$k]["quantity"] = intval($_POST["quantity"]) + $_SESSION["cart_item"][$k]["quantity"];
                                    }
                                }
                            } else {
                                $_SESSION["cart_item"] += $itemArray;

                            }
                        } else {
                            $_SESSION["cart_item"] = $itemArray;
                        }
                        // Change the URL to remove the product information
                        echo "<script>window.location.href='http://localhost/dfsms/search-product2.php';</script>";
                        exit();
                    }
                    break;

                // code for removing product from cart
                case "remove":
                    if (!empty($_SESSION["cart_item"])) {
                        foreach ($_SESSION["cart_item"] as $k => $v) {
                            if($_GET["code"] == $k) {
                                unset($_SESSION["cart_item"][$k]);
                            }
                            if(empty($_SESSION["cart_item"])) {
                                unset($_SESSION["cart_item"]);
                            }
                        }
                    }
                    break;
                // code for if cart is empty
                case "empty":
                    unset($_SESSION["cart_item"]);
                    break;
                case "remove-coupon":
                    unset($_SESSION["cart_item"]["coupon"]);
                    break;
            }
        }

        //Code for Checkout
        if(isset($_POST['checkout'])){
            $invoiceno = mt_rand(100000000, 999999999);
            $pid = $_SESSION["productid"];
            $quantity = $_POST["quantity"];
            $cname = $_POST["customername"];
            $cmobileno = $_POST["mobileno"];
            $pmode = $_POST["paymentmode"];
            $value = array_combine($pid, $quantity);
            foreach ($value as $pdid => $qty) {
                $query = mysqli_query(
                    $con,
                    "insert into tblorders(ProductId,Quantity,InvoiceNumber,CustomerName,CustomerContactNo,PaymentMode,DiscountAmount) values('$pdid','$qty','$invoiceno','$cname','$cmobileno','$pmode','')"
                );
            }
            echo '<script>alert("Invoice genrated successfully. Invoice number is "+"' .
                $invoiceno .
                '")</script>';
            unset($_SESSION["cart_item"]);
            $_SESSION["invoice"] = $invoiceno;
            echo "<script>window.location.href='invoice2.php'</script>";
        }

        // Check if a product name has been entered for search
        $pname = '';
        if (isset($_POST['productname'])) {
            $pname = mysqli_real_escape_string($con, $_POST['productname']);
        }
    ?>

    <!DOCTYPE html>
    <html lang="en">


        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
            <title>Search Product</title>
            <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
            <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
            <link href="dist/css/style.css" rel="stylesheet" type="text/css">
        </head>

<body>
	<!-- HK Wrapper -->
	<div class="hk-wrapper hk-vertical-nav">

    <!-- Top Navbar -->
    <?php include_once('includes/navbar.php');
    include_once('includes/sidebar2.php');
    ?>

            <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
            <!-- /Vertical Nav -->

            <!-- Main Content -->
            <div class="hk-pg-wrapper">
                <!-- Breadcrumb -->
                <nav class="hk-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light bg-transparent">
                        <li class="breadcrumb-item"><a href="#">Search</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Product</li>
                    </ol>
                </nav>
                <!-- /Breadcrumb -->

                <!-- Container -->
                <div class="container">
                    <!-- Title -->
                    <div class="hk-pg-header">
                        <h4 class="hk-pg-title"><span class="pg-title-icon"><span class="feather-icon"><i data-feather="external-link"></i></span></span>Search Product</h4>
                    </div>
                    <!-- /Title -->

                    <!-- Row -->
                    <div class="row">
                        <div class="col-xl-12">
                        <section class="hk-sec-wrapper">
                            <div class="row">
                                <div class="col-sm">
                                    <form class="needs-validation" method="post" novalidate>
                                        <div class="form-row">
                                            <div class="col-sm">
                                                <label for="validationCustom03">Product Name</label>
                                                <div class="d-flex justify-content-start">
                                                    <input type="text" class="form-control" id="validationCustom03" placeholder="Product Name" name="productname" required>
                                                    <button class="btn btn-primary ml-2" style="width: 150px;" type="submit" name="search">search</button>
                                                </div>
                                                <div class="invalid-feedback">Please provide a valid product name.</div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                            <!-- Product List Section -->

                        <!--code for search result -->
                        <?php if (isset($_POST['search'])) { ?>
                            <section class="hk-sec-wrapper">
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="table-wrap">
                                            <table id="datable_1" class="table table-hover w-100 display pb-30">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Category</th>
                                                    <th>Company</th>
                                                    <th>Product</th>
                                                    <th>Pricing</th>
                                                    <th>Quantity</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                        // Construct the base query
                                                        $queryString = "SELECT * FROM tblproducts";
                                                        // Append the search condition if a product name was searched
                                                        if (!empty($pname)) {
                                                            $queryString .= " WHERE ProductName LIKE '%$pname%'";
                                                        }
                                                        // Execute the query
                                                        $query = mysqli_query($con, $queryString);
                                                $cnt = 1;
                                                while ($row = mysqli_fetch_array($query)) {
                                                    ?>
                                                    <form method="post" action="search-product2.php?action=add&pid=<?php echo $row["id"]; ?>">
                                                        <tr>
                                                            <td><?php echo $cnt; ?></td>
                                                            <td><?php echo $row['CategoryName']; ?></td>
                                                            <td><?php echo $row['CompanyName']; ?></td>
                                                            <td><?php echo $row['ProductName']; ?></td>
                                                            <td><?php echo $row['ProductPrice']; ?></td>
                                                            <td><input type="text" class="product-quantity" name="quantity" value="1" size="2" /></td>
                                                            <td><input type="submit" value="Add to Cart" class="btnAddAction" /></td>
                                                        </tr>
                                                    </form>
                                                    <?php
                                                    $cnt++;
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
<?php } ?>                        

                        <section class="hk-sec-wrapper">
                            <div class="row">
                                <div class="col-sm">
                                    <form method="post">
                                        <div class="form-row">
                                            <div class="col-sm">
                                                <label for="couponCode">Add Coupon Code</label>
                                                <div class="d-flex justify-content-start">
                                                    <input type="text" class="form-control" id="couponCode" placeholder="Coupon Code" name="couponcode">
                                                    <button class="btn btn-primary ml-2" style="width: 150px;" type="submit" name="couponvalidate">Add Coupon</button>
                                                </div>
                                                <div class="invalid-feedback">Please provide a valid coupon code.</div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>

                        <form class="needs-validation" method="post" novalidate>
                            <!--- Shopping Cart ---->
                            <section class="hk-sec-wrapper">
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="table-wrap">
                                            <h4>Shopping Cart</h4>
                                            <hr style=" border-width: thick"/>
                                            <a id="btnEmpty" href="search-product2.php?action=empty">Empty Cart</a>
                                            <?php
                                            if (isset($_SESSION["cart_item"])) {
                                            $total_quantity = 0;
                                            $total_price = 0;
                                            $total_price_deducted = 0;
                                            $discount_value = 0;
                                            $discount_type = "";
                                            ?>
                                            <table id="datable_1" class="table table-hover w-100 display pb-30" border="1">
                                                <tbody>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                    <th>Company</th>
                                                    <th width="5%">Quantity</th>
                                                    <th width="10%">Unit Price</th>
                                                    <th width="10%">Price</th>
                                                    <th width="5%">Remove</th>
                                                </tr>
                                                <?php
                                                $productid = array();
                                                foreach ($_SESSION["cart_item"] as $item) {
                                                    if (!is_array($item)) {
                                                        var_dump($item);
                                                        die("Error: \$item is not an array.");
                                                    }
                                                    if ($item['code'] === 'Coupon') {
                                                        $item_price = $item["price"];
                                                        array_push($productid, $item['code']);
                                                    } else {
                                                        $item_price = (float)$item["quantity"] * (float)$item["price"];
                                                        array_push($productid, $item['code']);
                                                    }
                                                    ?>
                                                    <input type="hidden" value="<?php echo $item['quantity']; ?>" name="quantity[<?php echo $item['code']; ?>]">
                                                    <tr>
                                                        <td><?php echo $item["pname"]; ?></td>
                                                        <td><?php echo $item["catname"]; ?></td>
                                                        <td><?php echo $item["compname"]; ?></td>
                                                        <td><?php echo $item["quantity"]; ?></td>
                                                        <td><?php echo $item["price"]; ?></td>
                                                        <td><?php
                                                            if ($item['code'] === 'coupon') {
                                                                echo $item["price"];
                                                            } else {
                                                                echo number_format($item_price, 2);
                                                            }
                                                            ?></td>
                                                        <td>
                                                            <?php
                                                                if ($item['code'] === 'coupon') {
                                                                    echo '<a href="search-product2.php?action=remove-coupon" class="btnRemoveAction"><img src="dist/img/icon-delete.png" alt="Remove Coupon" /><a/>';
                                                                } else {
                                                                    $linkItemCode = "search-product2.php?action=remove&code=" . $item["code"];
                                                                    echo '<a href="'. $linkItemCode .'" class="btnRemoveAction"><img src="dist/img/icon-delete.png" alt="Remove Item" /><a/>';
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $total_quantity += $item["quantity"];
                                                    if ($item['code'] !== 'coupon') {
                                                        $total_price += ($item["price"] * $item["quantity"]);
                                                    } else {
                                                        if (strpos($item["price"], "%") !== false) {
                                                            $discount_type = "percentage";
                                                        } else {
                                                            $discount_type = "normal";
                                                        }

                                                        if (preg_match('/(\d+)/', $item["price"], $matches)) {
                                                            $discount_value = (int)$matches[0];
                                                        } else {
                                                            $discount_value = 0;
                                                        }
                                                    }
                                                }
                                                if ($discount_type === "percentage") {
                                                    $total_price = $total_price * (1 - ($discount_value/100));
                                                    $total_price_deducted = $total_price * ($discount_value/100);
                                                } else {
                                                    $total_price = $total_price - $discount_value;
                                                    $total_price_deducted = $discount_value;
                                                }
                                                $_SESSION['productid'] = $productid;

                                                ?>
                                                <tr>
                                                    <td colspan="3" align="right">Total:</td>
                                                    <td colspan="2"><?php echo $total_quantity; ?></td>
                                                    <td colspan><strong><?php echo number_format($total_price, 2); ?></strong></td>
                                                    <td></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <hr style=" border-width: thick"/>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Customer Name</label>
                                                    <input type="text" class="form-control" id="validationCustom03" placeholder="Customer Name" name="customername" required>
                                                    <div class="invalid-feedback">Please provide a valid customer name.</div>
                                                </div>
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Customer Mobile Number</label>
                                                    <input type="text" class="form-control" id="validationCustom03" placeholder="Mobile Number" name="mobileno" required>
                                                    <div class="invalid-feedback">Please provide a valid mobile number.</div>
                                                </div>
                                            </div>
                                            <hr style=" border-width: thick"/>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Payment Mode</label>
                                                    <div class="custom-control custom-radio mb-10">
                                                        <input type="radio" class="custom-control-input" id="customControlValidation2" name="paymentmode" value="cash" required>
                                                        <label class="custom-control-label" for="customControlValidation2">Cash</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mb-10">
                                                        <input type="radio" class="custom-control-input" id="customControlValidation3" name="paymentmode" value="card" required>
                                                        <label class="custom-control-label" for="customControlValidation3">Card</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-10">
                                                    <button class="btn btn-primary" type="submit" name="checkout">Checkout</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </form>
                    <?php
                    } else {
                        ?>
                        <div style="color:red" align="center">Your Cart is Empty</div>
                        <?php
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Content -->
    <div class="footer">
        <!-- Footer -->
        <?php include_once('includes/footer.php'); ?>
        <!-- /Footer -->
    </div>


        <script src="vendors/jquery/dist/jquery.min.js"></script>
        <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="vendors/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>
        <script src="dist/js/jquery.slimscroll.js"></script>
        <script src="dist/js/dropdown-bootstrap-extended.js"></script>
        <script src="dist/js/feather.min.js"></script>
        <script src="vendors/jquery-toggles/toggles.min.js"></script>
        <script src="dist/js/toggle-data.js"></script>
        <script src="dist/js/init.js"></script>
        <script src="dist/js/validation-data.js"></script>

    <style type="text/css">
        #btnEmpty {
            background-color: #ffffff;
            border: #d00000 1px solid;
            padding: 5px 10px;
            color: #d00000;
            float: right;
            text-decoration: none;
            border-radius: 3px;
            margin: 10px 0px;
        }
        footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #ffffff;
            color: black;
            text-align: center;
        }
        </style>
    </body>
    </html>
<?php } ?>