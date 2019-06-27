<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM academic_paper ORDER BY created_at DESC ";
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
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <button type="button" class="btn btn-success pull-right"
                                        onclick="onClickAdd(this)">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มงานวิจัย/วิชาการ
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableAcademicPapaer" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 30%;">เรื่อง</th>
                                        <th style="text-align: center; width: 10%;">เผยแพร่</th>
                                        <th style="text-align: center; width: 25%;" nowrap>ผู้ทำวิจัย</th>
                                        <th style="text-align: center; width: 10%;">PDF</th>
                                        <th style="text-align: center; width: 10%;">บทคัดย่อ</th>
                                        <th style="text-align: center; width: 15%;" nowrap>อัพโหลด</th>
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
                                                <td style="text-align: center;">
                                                    <a target="_blank" href="../uploads/academic_papers/<?php echo $academicPaper['file_name']; ?>">
                                                        <span style="font-size: 25px"><i class="fa fa-file-pdf-o"></i></span>
                                                    </a>
                                                </td>
                                                <td style="text-align: center; cursor: pointer">
                                                    <a href="javascript:void(0)"
                                                       onClick="onClickAbstract(this, '<?php echo str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($academicPaper['abstract']))); ?>')">
                                                        <span style="font-size: 25px"><i class="fa fa-file-text-o"></i></span>
                                                    </a>
                                                </td>
                                                <td style="text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>
                                                <td>
                                                    <form method="get" action="academic_papers_add_edit.php" style="display: inline; margin: 0">
                                                        <input type="hidden" name="id" value="<?php echo $academicPaper['id']; ?>"/>
                                                        <button type="submit" class="btn btn-warning">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
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
            $('#tableAcademicPapaer').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[5, 'desc']],
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

        function onClickAbstract(element, abstractText) {
            $('#formAbstract #divAbstract').html(abstractText);
            $('#abstractModal').modal('show');
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