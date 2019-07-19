<?php
require_once '../include/head_php.inc';
require_once '../components/get_course_registration_data_table.php';
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <style>

        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <?php
                $serviceType = $_GET['service_type'];
                $serviceTypeText = '';
                switch ($serviceType) {
                    case SERVICE_TYPE_TRAINING:
                        $serviceTypeText = 'บริการวิชาการ';
                        break;
                    case SERVICE_TYPE_SOCIAL:
                        $serviceTypeText = 'บริการสังคม';
                        break;
                    case SERVICE_TYPE_DRIVING_LICENSE:
                        $serviceTypeText = 'อบรมใบขับขี่';
                        break;
                }
                ?>
                <h1>
                    ใบสมัคร
                    <small><?php echo $serviceTypeText; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                            </div>
                            <div class="box-body">
                                <?php
                                if (!isset($serviceType)) {
                                    echo '<div style="color: red; text-align: center">ERROR: ไม่ได้ระบุ Service Type</div>';
                                } else {
                                    getCourseRegistrationDataTable($db, $serviceType);
                                }
                                ?>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php require_once('../include/footer.inc'); ?>
    </div>
    <!-- ./wrapper -->

    <?php require_once('../include/foot.inc'); ?>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    </body>
    </html>
<?php
require_once '../include/foot_php.inc';
?>