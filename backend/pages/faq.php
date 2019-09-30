<?php
require_once '../include/head_php.inc';

$pageTitle = 'คำถามที่พบบ่อย';

$sql = "SELECT id, title, details, created_at, status
            FROM faq
            ORDER BY created_at DESC ";
if ($result = $db->query($sql)) {
    $faqList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($faqList, $row);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

$userHasPermission = currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT);
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

    <!-- Answer Modal (คำตอบ) -->
    <div class="modal fade" id="answerModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">คำตอบ</h4>
                </div>
                <div class="modal-body">
                    <form id="formAnswer" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div id="divAnswer" class="box-body"></div>
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
                    <?= trim($pageTitle); ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <?php
                                if ($userHasPermission) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="onClickAdd(this)">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่ม<?= $pageTitle; ?>
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableFaq" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 75%;">คำถาม</th>
                                        <th style="text-align: center; width: 10%;" nowrap>คำตอบ</th>
                                        <th style="text-align: center; width: 15%;" nowrap>วันที่สร้าง</th>
                                        <th style="text-align: center;" nowrap>เผยแพร่</th>
                                        <th style="text-align: center;">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($faqList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($faqList as $faq) {
                                            $createdAt = $faq['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                            ?>
                                            <tr style="">
                                                <td><?= $faq['title']; ?></td>

                                                <td style="text-align: center; cursor: pointer"
                                                    onClick="onClickAnswer(this, '<?= str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($faq['details']))); ?>')">
                                                    <a href="javascript:void(0)">
                                                        <span style="font-size: 25px"><i class="fa fa-file-text-o"></i></span>
                                                    </a>
                                                </td>

                                                <td style="text-align: center"><?= ($dateHidden . $displayDateTime); ?></td>
                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $faq['status'] == 'publish' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           class="my-toggle"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $faq['id']; ?>, '<?= $faq['title']; ?>', <?= $userHasPermission; ?>)"
                                                        <?= $faq['status'] == 'publish' ? 'checked' : '' ?>>
                                                </td>

                                                <td nowrap>
                                                    <form method="get" action="faq_add_edit.php" style="display: inline; margin: 0">
                                                        <input type="hidden" name="faq_id" value="<?= $faq['id']; ?>"/>

                                                        <?php
                                                        if ($userHasPermission) {
                                                            ?>
                                                            <button type="submit" class="btn btn-warning"
                                                                    style="margin-left: 3px">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                            <button type="button" class="btn btn-danger"
                                                                    style="margin-left: 3px; margin-right: 3px"
                                                                    onclick="onClickDelete(this, <?= $faq['id']; ?>, '<?= $faq['title']; ?>')">
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
            $('#tableFaq').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[2, 'desc']],
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
                },
                drawCallback: function(row, data) {
                    $('.my-toggle').bootstrapToggle();
                }
            });

            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });
        });

        function onClickAnswer(element, details) {
            $('#formAnswer #divAnswer').html(details);
            $('#answerModal').modal('show');
        }

        function onClickAdd() {
            window.location.href = 'faq_add_edit.php';
        }

        function onChangeStatus(element, faqId, title, userHasPermission) {
            if (!userHasPermission) {
                alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้');
                location.reload(true);
                return;
            }

            let result = confirm("ยืนยัน" + (element.checked ? 'เผยแพร่' : 'ยกเลิกการเผยแพร่') + " '" + title + "' ?");
            if (result) {
                doChangeStatus(faqId, (element.checked ? 'publish' : 'draft'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function doChangeStatus(faqId, newStatus) {
            let title = 'แก้ไขสถานะเอกสารดาวน์โหลด';

            $.post(
                '../api/api.php/update_status',
                {
                    tableName: 'faq',
                    id: faqId,
                    newStatus: newStatus
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: title + ' - ผิดพลาด',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                                location.reload(true);
                            }
                        }]
                    });
                }
            }).fail(function () {
                BootstrapDialog.show({
                    title: title + ' - ผิดพลาด',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                            location.reload(true);
                        }
                    }]
                });
            });
        }

        function onClickDelete(element, id, title) {
            BootstrapDialog.show({
                title: 'ลบ<?= $pageTitle; ?>',
                message: 'ยืนยันลบ \'' + title + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteFaq(id);
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

        function doDeleteFaq(faqId) {
            $.post(
                '../api/api.php/delete',
                {
                    tableName: 'faq',
                    id: faqId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบ<?= $pageTitle; ?> - ผิดพลาด',
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
                    title: 'ลบ<?= $pageTitle; ?> - ผิดพลาด',
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