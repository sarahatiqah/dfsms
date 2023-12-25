<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['aid']==0)) {
    header('location:logout.php');
} else {
    // Code for deletion
    if(isset($_GET['del'])){
        $couponId = base64_decode($_GET['del']);
        $query = mysqli_query($con, "DELETE FROM tblcoupons WHERE CouponID = '$couponId'");
        if($query){
            echo "<script>alert('Coupon deleted successfully.');</script>";
            echo "<script>window.location.href='coupon.php'</script>";
        } else {
            echo "<script>alert('Error occurred while deleting the coupon.');</script>";
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title>Manage Coupons</title>
        <!-- Data Table CSS -->
        <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />
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
        <div class="hk-pg-wrapper" >
            <!-- Breadcrumb -->
            <nav class="hk-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-light bg-transparent">
                    <li class="breadcrumb-item"><a href="#">Coupon</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage</li>
                </ol>
            </nav>
            <!-- /Breadcrumb -->

            <!-- Container -->
            <div class="container">
                <!-- Title -->
                <div class="hk-pg-header">
                    <h4 class="hk-pg-title"><span class="pg-title-icon"><span class="feather-icon"><i data-feather="file"></i></span></span>Manage Coupons</h4>
                </div>
                <!-- /Title -->

                <!-- Row -->
                <div class="row">
                    <div class="col-xl-12">
                        <section class="hk-sec-wrapper hk-invoice-wrap pa-35"  id ='DivIdToPrint'>
                            <div class="invoice-from-wrap">
                                <div class="row">
                                    <div class="col-md-7 mb-20">
                                        <h3 class="mb-35 font-weight-600">DFSMS Coupons</h3>
                                    </div>
                                    <div>
                                        <a href="add-coupon.php" class="btn btn-primary">Add Coupon</a>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="row" >
                                <div class="col-sm">
                                    <div class="table-wrap">
                                        <table class="table mb-0 table-striped table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th>Coupon ID</th>
                                                <th>Coupon Code</th>
                                                <th>Discount Amount</th>
                                                <th>Discount Percentage</th>
                                                <th>Valid From</th>
                                                <th>Valid To</th>
                                                <th>Created On</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            // Fetch coupon data
                                            $query = mysqli_query($con, "SELECT * FROM tblcoupons");
                                            while($row = mysqli_fetch_assoc($query)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['CouponID']; ?></td>
                                                    <td><?php echo $row['CouponCode']; ?></td>
                                                    <td><?php echo ($row['DiscountAmount'] == 0) ? '-' : $row['DiscountAmount']; ?></td>
                                                    <td><?php echo ($row['DiscountPercentage'] == 0) ? '-' : $row['DiscountPercentage']; ?></td>
                                                    <td><?php echo $row['ValidFrom']; ?></td>
                                                    <td><?php echo $row['ValidTo']; ?></td>
                                                    <td><?php echo $row['CreatedOn']; ?></td>
                                                    <td>
                                                        <a href="edit-coupon.php?id=<?php echo base64_encode($row['CouponID']); ?>">Edit</a>
                                                        <a href="coupon.php?del=<?php echo base64_encode($row['CouponID']); ?>" onclick="return confirm('Are you sure you want to delete this coupon?');">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <!-- /Container -->

            <!-- Footer -->
            <?php include_once('includes/footer.php');?>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->
    </div>
    <!-- /HK Wrapper -->

    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="dist/js/jquery.slimscroll.js"></script>
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="dist/js/dataTables-data.js"></script>
    <script src="dist/js/feather.min.js"></script>
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>
    <script src="dist/js/init.js"></script>
    </body>
    </html>
<?php } ?>