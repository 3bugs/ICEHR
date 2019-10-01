<?php
require_once '../include/head_php.inc';

$sql = "SELECT ap.id, ap.title, ap.first_name, ap.last_name, ap.file_name, ap.year_published, ap.abstract, ap.fund_source, ap.created_at, 
               COUNT(apd.id) AS download_count 
            FROM academic_paper ap 
                LEFT JOIN academic_paper_download apd 
                    ON ap.id = apd.academic_paper_id 
            GROUP BY ap.id 
            ORDER BY created_at DESC ";
if ($result = $db->query($sql)) {
    $academicPaperList = array();
    while ($row = $result->fetch_assoc()) {
        $academicPaper = array();
        $academicPaper['id'] = (int)$row['id'];
        $academicPaper['title'] = $row['title'];
        $academicPaper['file_name'] = $row['file_name'];
        $academicPaper['first_name'] = $row['first_name'];
        $academicPaper['last_name'] = $row['last_name'];
        $academicPaper['year_published'] = $row['year_published'];
        $academicPaper['abstract'] = $row['abstract'];
        $academicPaper['fund_source'] = $row['fund_source'];
        $academicPaper['created_at'] = $row['created_at'];
        $academicPaper['download_count'] = (int)$row['download_count'];

        array_push($academicPaperList, $academicPaper);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <style>
            #tableDownload td:nth-child(5) {
                text-align: center;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <!-- Abstract Modal (บทคัดย่อ) -->
    <div class="modal fade" id="abstractModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">บทคัดย่อ</h4>
                </div>
                <div class="modal-body">
                    <form id="formAbstract" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div id="divAbstract" class="box-body"></div>
                        <!-- /.box-body -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Download list modal -->
    <div class="modal fade" id="downloadListModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <div id="divTitle">&nbsp;</div>
                    </h4>
                </div>

                <div class="modal-body">
                    <div id="spanLoading" style="text-align: center">
                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                    </div>
                    <div id="alertWarning" class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertSuccess').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertWarningText">ไม่มีข้อมูลการดาวน์โหลดสำหรับเอกสารนี้</span>
                    </div>
                    <div id="alertError" class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertError').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertErrorText"></span>
                    </div>

                    <table id="tableDownload" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="text-align: center; width: 20%;">ผู้ดาวน์โหลด</th>
                            <th style="text-align: center; width: 20%;">ตำแหน่ง/หน่วยงาน</th>
                            <th style="text-align: center; width: 15%;">อาชีพ</th>
                            <th style="text-align: center; width: 35%;">การนำไปใช้ประโยชน์</th>
                            <th style="text-align: center; width: 10%;">ดาวน์โหลดเมื่อ</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    งานวิจัยและวิชาการ
                    <small><?php /**/ ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">

                        <!--ตารางงานวิจัยและวิชาการ-->
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <?php
                                if (currentUserHasPermission(PERMISSION_MANAGE_ACADEMIC_PAPERS)) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="onClickAdd(this)">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่มงานวิจัย/วิชาการ
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableAcademicPapaer" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 30%;">เรื่อง</th>
                                        <th style="text-align: center; width: 10%;">เผยแพร่</th>
                                        <th style="text-align: center; width: 20%;" nowrap>ผู้ทำวิจัย</th>
                                        <th style="text-align: center; width: 8%;">PDF</th>
                                        <th style="text-align: center; width: 9%;">บทคัดย่อ</th>
                                        <th style="text-align: center; width: 8%;">ดาวน์โหลด</th>
                                        <th style="text-align: center; width: 15%;" nowrap>วันที่สร้าง</th>
                                        <th style="text-align: center;">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($academicPaperList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($academicPaperList as $academicPaper) {
                                            $createdAt = $academicPaper['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                            ?>
                                            <tr style="">
                                                <td><?php echo $academicPaper['title']; ?></td>
                                                <td style="text-align: center"><?php echo $academicPaper['year_published']; ?></td>
                                                <td><?php echo "{$academicPaper['first_name']} {$academicPaper['last_name']}"; ?></td>
                                                <td style="text-align: center; cursor: pointer"
                                                    onClick="window.open('<?php echo(UPLOAD_DIR_ACADEMIC_PAPERS . $academicPaper['file_name']); ?>', '_blank')">
                                                    <a target="_blank" href="javascript:void(0)">
                                                        <span style="font-size: 25px"><i class="fa fa-file-pdf-o"></i></span>
                                                    </a>
                                                </td>
                                                <td style="text-align: center; cursor: pointer"
                                                    onClick="onClickAbstract(this, '<?php echo str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($academicPaper['abstract']))); ?>')">
                                                    <a href="javascript:void(0)">
                                                        <span style="font-size: 25px"><i class="fa fa-file-text-o"></i></span>
                                                    </a>
                                                </td>
                                                <td style="text-align: center; cursor: pointer"
                                                    onClick="onClickDownloadList(this, <?= $academicPaper['id']; ?>, '<?= $academicPaper['title']; ?>')">
                                                    <a href="javascript:void(0)">
                                                        <?= $academicPaper['download_count']; ?>
                                                    </a>
                                                </td>
                                                <td style="text-align: center"><?= ($dateHidden . $displayDateTime); ?></td>
                                                <td nowrap>
                                                    <form method="get" action="academic_papers_add_edit.php" style="display: inline; margin: 0">
                                                        <input type="hidden" name="id" value="<?= $academicPaper['id']; ?>"/>
                                                        <?php
                                                        if (currentUserHasPermission(PERMISSION_MANAGE_ACADEMIC_PAPERS)) {
                                                            ?>
                                                            <button type="submit" class="btn btn-warning"
                                                                    style="margin-left: 3px">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                            <button type="button" class="btn btn-danger"
                                                                    style="margin-left: 3px; margin-right: 3px"
                                                                    onclick="onClickDelete(this, <?= $academicPaper['id']; ?>, '<?= $academicPaper['title']; ?>')">
                                                                <span class="fa fa-remove"></span>&nbsp;
                                                                ลบ
                                                            </button>
                                                            <?php
                                                        }
                                                        ?>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->

                        <!--Export-->
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Export</h3>

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
                                <div class="margin">
                                    <div class="btn-group" style="margin-right: 6px">
                                        <a target="_blank" href="excel_academic_papers_download_history.php"
                                           class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;ประวัติการดาวน์โหลด</a>
                                    </div>
                                </div>
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

    <script>
        let downloadListDataTable = null;

        $(document).ready(function () {
            $('#tableAcademicPapaer').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[6, 'desc']],
                language: {
                    lengthMenu: "แสดงหน้าละ _MENU_ แถวข้อมูล",
                    zeroRecords: "ไม่มีข้อมูล",
                    emptyTable: "ไม่มีข้อมูล",
                    info: "หน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
                    infoEmpty: "แสดง 0 แถวข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ แถวข้อมูล)",
                    search: "ค้นหา:",
                    thousands: ",",
                    loadingRecords: "รอสักครู่...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                }
            });
            downloadListDataTable = $('#downloadListModal #tableDownload').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
                order: [[4, 'desc']],
                language: {
                    lengthMenu: "แสดงหน้าละ _MENU_ แถวข้อมูล",
                    zeroRecords: "ไม่มีข้อมูล",
                    emptyTable: "ไม่มีข้อมูล",
                    info: "หน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
                    infoEmpty: "แสดง 0 แถวข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ แถวข้อมูล)",
                    search: "ค้นหา:",
                    thousands: ",",
                    loadingRecords: "รอสักครู่...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                }
            });
        });

        function onClickAdd() {
            window.location.href = 'academic_papers_add_edit.php';
        }

        function onClickExportExcel() {
            window.open('excel_academic_papers_download_history.php', '_blank');
        }

        function onClickAbstract(element, abstractText) {
            $('#formAbstract #divAbstract').html(abstractText);
            $('#abstractModal').modal('show');
        }

        function onClickDownloadList(element, academicPaperId, academicPaperTitle) {
            doGetDownloadList(academicPaperId);
            $('#downloadListModal #divTitle').text(academicPaperTitle);
            $('#downloadListModal').modal('show');
        }

        function doGetDownloadList(id) {
            const loadingIcon = $('#downloadListModal #spanLoading');
            const noDownloadListAlert = $('#downloadListModal #alertWarning');
            const errorAlert = $('#downloadListModal #alertError');
            const dataTableElement = $('#downloadListModal #tableDownload');

            downloadListDataTable.clear().draw();

            dataTableElement.parents('div.dataTables_wrapper').first().hide();
            loadingIcon.show();
            noDownloadListAlert.hide();
            errorAlert.hide();

            $.post(
                '../api/api.php/get_academic_paper_download',
                {
                    academicPaperId: id,
                }
            ).done(function (data) {
                loadingIcon.hide();
                if (data.error_code === 0) {
                    data.data_list.forEach(row => {
                        downloadListDataTable.row.add([
                            `${row.first_name} ${row.last_name}<br><i class="fa fa-envelope-o" style="color: black"></i>&nbsp;&nbsp;<a href="mailto:${row.email}">${row.email}</a>`,
                            `${row.job_position}<br>${row.organization_name}`,
                            `${row.occupation}`,
                            `${row.use_purpose}`,
                            `${row.download_date_format}<br>${row.download_time_format}`,
                        ]).draw(false);
                    });
                    dataTableElement.parents('div.dataTables_wrapper').first().show();
                } else {
                    errorAlert.text(data.error_message);
                    errorAlert.show();
                }
            }).fail(function () {
                loadingIcon.hide();
                errorAlert.text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                errorAlert.show();
            });
        }

        function onClickDelete(element, id, title) {
            BootstrapDialog.show({
                title: 'ลบงานวิจัยและวิชาการ',
                message: 'ยืนยันลบ \'' + title + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteAcademicPaper(id);
                        self.close();
                    },
                    cssClass: 'btn-primary'
                }, {
                    label: 'ยกเลิก',
                    action: function (self) {
                        self.close();
                    }
                }]
            });
        }

        function doDeleteAcademicPaper(id) {
            $.post(
                '../api/api.php/delete_academic_paper',
                {
                    id: id,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบงานวิจัยและวิชาการ - ผิดพลาด',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });
                }
            }).fail(function () {
                BootstrapDialog.show({
                    title: 'ลบชื่อหลักสูตร - ผิดพลาด',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                        }
                    }]
                });
            });
        }
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