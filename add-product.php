<?php
session_start();
//error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['aid'] == 0)) {
    header('location:logout.php');
} else {

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title>Add Product</title>
        <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
        <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
        <link href="dist/css/style.css" rel="stylesheet" type="text/css">
    </head>

    <body>


        <!-- HK Wrapper -->
        <div class="hk-wrapper hk-vertical-nav">

            <!-- Top Navbar -->
            <?php include_once('includes/navbar.php');
            include_once('includes/sidebar.php');
            ?>
            <div id="hk_nav_backdrop" class="hk-nav-backdrop"></div>
            <!-- /Vertical Nav -->

            <!-- Main Content -->
            <div class="hk-pg-wrapper">
                <!-- Breadcrumb -->
                <nav class="hk-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-light bg-transparent">
                        <li class="breadcrumb-item"><a href="#">Product</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</li>
                    </ol>
                </nav>
                <!-- /Breadcrumb -->

                <!-- Container -->
                <div class="container">
                    <!-- Title -->
                    <div class="hk-pg-header">
                        <h4 class="hk-pg-title"><span class="pg-title-icon"><span class="feather-icon"><i
                                        data-feather="external-link"></i></span></span>Add Product</h4>
                    </div>
                    <!-- /Title -->

                    <!-- Row -->
                    <div class="row">
                        <div class="col-xl-12">
                            <section class="hk-sec-wrapper">

                                <div class="row">
                                    <div class="col-sm">
                                        <div class="needs-validation" method="post" novalidate>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Category</label>
                                                    <select class="form-control custom-select" name="category" required
                                                        id="input-1">
                                                        <option value="">Select category</option>
                                                        <?php
                                                        $ret = mysqli_query($con, "select CategoryName from tblcategory");
                                                        while ($row = mysqli_fetch_array($ret)) { ?>
                                                            <option value="<?php echo $row['CategoryName']; ?>">
                                                                <?php echo $row['CategoryName']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a category.</div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Company</label>
                                                    <select class="form-control custom-select" id="input-2" name="company"
                                                        required>
                                                        <option value="">Select Company</option>
                                                        <?php
                                                        $ret = mysqli_query($con, "select CompanyName from tblcompany");
                                                        while ($row = mysqli_fetch_array($ret)) { ?>
                                                            <option value="<?php echo $row['CompanyName']; ?>">
                                                                <?php echo $row['CompanyName']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a company.</div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Product Name</label>
                                                    <input type="text" class="form-control" id="input-3"
                                                        placeholder="Product Name" name="productname" required>
                                                    <div class="invalid-feedback">Please provide a valid product name.</div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6 mb-10">
                                                    <label for="validationCustom03">Product Price</label>
                                                    <input type="text" class="form-control" id="input-4"
                                                        placeholder="Product Price" name="productprice" required>
                                                    <div class="invalid-feedback">Please provide a valid product price.
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-primary" onclick="addProduct()"
                                                name="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </section>

                        </div>
                    </div>
                </div>


                <!-- Footer -->
                <?php include_once('includes/footer.php'); ?>
                <!-- /Footer -->

            </div>
            <!-- /Main Content -->

        </div>
        <script>
            function addProduct() {
                var selectedCategoryInput = document.getElementById("input-1");
                var categoryName = selectedCategoryInput.value;

                var companyNameInput = document.getElementById("input-2");
                var companyName = companyNameInput.value;

                var productNameInput = document.getElementById("input-3");
                var productName = productNameInput.value;

                var productPriceInput = document.getElementById("input-4");
                var productPrice = productPriceInput.value;

                if (categoryName === "") {
                    alert("Please select a category");
                    return;
                }

                if (companyName === "") {
                    alert("Please select a company");
                    return;
                }

                if (productName === "") {
                    alert("Please provide a product name");
                    return;
                }

                // Check if productPrice is a number and not negative
                if (!isNumeric(productPrice) || parseFloat(productPrice) < 0) {
                    alert("Please provide a valid non-negative numeric product price");
                    return;
                }

                $.ajax({
                    url: 'backend/process_addProduct.php',
                    method: 'POST',
                    data: {
                        categoryName: categoryName,
                        companyName: companyName,
                        productName: productName,
                        productPrice: productPrice
                    },
                    success: function (response) {
                        if (response == "success") {
                            alert("A new product has been added!");
                        } else {
                            alert("Duplicated entry detected. Please try again.");
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle the error
                        console.log(error);
                    }
                });
            }

            // Function to check if a value is numeric
            function isNumeric(value) {
                return !isNaN(parseFloat(value)) && isFinite(value);
            }
            
        </script>
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

    </body>

    </html>
<?php } ?>