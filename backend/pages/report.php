<?php
require_once '../include/head_php.inc';
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

        <style>
            input[type="file"] {
                margin-bottom: 15px;
                /*display: none;*/
            }

            .custom-file-upload {
                border: 1px solid #ccc;
                display: inline-block;
                padding: 6px 12px;
                cursor: pointer;
            }

            .custom-file-upload:hover {
                background: #f4f4f4;
            }

            .nav-tabs {
                background-color: #f8f8f8;
            }

            .tab-content {
                /*background-color:#ccc;
                color:#00ff00;
                padding:5px*/
            }

            .nav-tabs > li > a {
                /*border: medium none;*/
            }

            .nav-tabs > li > a:hover {
                /*background-color: #ccc !important;
                border: medium none;
                border-radius: 0;
                color:#fff;*/
            }
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
                <h1>
                    รายงาน
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <input type="hidden" name="courseId"
                       value="<?php echo $courseId; ?>"/>

                <div class="row">
                    <div class="col-xs-12">

                        <!--สรุปผู้เข้ารับการอบรมประจำเดือน บริการใบขับขี่-->
                        <form id="formDlTraineeListByDate"
                              method="post">
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">สรุปผู้เข้ารับการอบรมประจำเดือน <small>บริการใบขับขี่</small></h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                data-toggle="tooltip" title="ย่อ">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                    <!--เดือนและปี-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="selectMonth">เดือน:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                    <select id="selectMonth" class="form-control" required
                                                            name="month"
                                                            oninvalid="this.setCustomValidity('เลือกเดือน')"
                                                            oninput="this.setCustomValidity('')">
                                                        <option value="" disabled selected>-- เลือกเดือน --</option>
                                                        <?php
                                                        for ($i = 0; $i < sizeof($monthNames); $i++) {
                                                            ?>
                                                            <option value="<?= $i + 1; ?>"><?= substr('0' . ($i + 1), -2) . " - {$monthNames[$i]}"; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="selectYear">ปี:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                    <select id="selectYear" class="form-control" required
                                                            name="year"
                                                            oninvalid="this.setCustomValidity('เลือกปี')"
                                                            oninput="this.setCustomValidity('')">
                                                        <option value="" disabled selected>-- เลือกปี --</option>
                                                        <?php
                                                        $currentYear = (int)date("Y");
                                                        for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
                                                            $beYear = $i + 543;
                                                            ?>
                                                            <option value="<?= $i; ?>" <?= $i === $currentYear ? 'selected' : ''; ?>><?= $i . "&nbsp;&nbsp;(พ.ศ. $beYear)"; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="button">&nbsp;</label>
                                                <div class="input-group">
                                                    <button id="button" type="submit"
                                                            class="btn btn-info">
                                                        <span class="fa fa-print"></span>&nbsp;
                                                        พิมพ์
                                                    </button>
                                                    <span id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--ปุ่ม "ค้นหา"-->
                                    <!--<div class="row">
                                        <div class="col-8 text-center">
                                            <div id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                            </div>
                                            <button id="button" type="submit"
                                                    class="btn btn-info">
                                                <span class="fa fa-print"></span>&nbsp;
                                                พิมพ์
                                            </button>
                                        </div>
                                    </div>-->

                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </form>

                        <!--ข้อมูลสมาชิก-->
                        <form id="formMemberListByDate"
                              method="post">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ข้อมูลสมาชิกเว็บไซต์ / ผู้สมัครอบรม</h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                data-toggle="tooltip" title="ย่อ">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.box-tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                    <!--เดือนและปี-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="selectMonth">เดือน:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                    <select id="selectMonth" class="form-control" required
                                                            name="month"
                                                            oninvalid="this.setCustomValidity('เลือกเดือน')"
                                                            oninput="this.setCustomValidity('')">
                                                        <option value="0" selected>ไม่ระบุเดือน</option>
                                                        <?php
                                                        for ($i = 0; $i < sizeof($monthNames); $i++) {
                                                            ?>
                                                            <option value="<?= $i + 1; ?>"><?= substr('0' . ($i + 1), -2) . " - {$monthNames[$i]}"; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="selectYear">ปี:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                    <select id="selectYear" class="form-control" required
                                                            name="year"
                                                            oninvalid="this.setCustomValidity('เลือกปี')"
                                                            oninput="this.setCustomValidity('')">
                                                        <option value="0" selected>ไม่ระบุปี</option>
                                                        <?php
                                                        for ($i = $currentYear - 5; $i <= $currentYear; $i++) {
                                                            $beYear = $i + 543;
                                                            ?>
                                                            <option value="<?= $i; ?>"><?= $i . "&nbsp;&nbsp;(พ.ศ. $beYear)"; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="button">&nbsp;</label>
                                                <div class="input-group">
                                                    <button id="button" type="submit"
                                                            class="btn btn-info">
                                                        <span class="fa fa-file-excel-o"></span>&nbsp;
                                                        DOWNLOAD
                                                    </button>
                                                    <span id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </form>
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

    <script>
        $(document).ready(function () {
            $('#formDlTraineeListByDate #divLoading').hide();

            $('#formDlTraineeListByDate').submit(event => {
                event.preventDefault();
                const selectMonthValue = $('#formDlTraineeListByDate #selectMonth').val();
                const selectYearValue = $('#formDlTraineeListByDate #selectYear').val();
                window.open(`print_trainee_list_by_date.php?month=${selectMonthValue}&year=${selectYearValue}`, '_blank');
            });

            $('#formMemberListByDate #divLoading').hide();

            $('#formMemberListByDate').submit(event => {
                event.preventDefault();
                const selectMonthValue = parseInt($('#formMemberListByDate #selectMonth').val());
                const selectYearValue = parseInt($('#formMemberListByDate #selectYear').val());

                if (selectMonthValue !== 0 && selectYearValue === 0) {
                    alert('ถ้าหากเลือกเดือน จะต้องเลือกปีด้วย');
                    $('#formMemberListByDate #selectYear').focus();
                    return;
                }
                window.open(`excel_member_list.php?month=${selectMonthValue}&year=${selectYearValue}`, '_blank');
            });
        });
    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>