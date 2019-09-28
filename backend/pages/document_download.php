<?php
require_once '../include/head_php.inc';

$documentType = $_GET['document_type'];
$documentTypeList = array(
    'training', 'km', 'report', 'qa', 'booklet'
);
if (!isset($documentType) || !in_array($documentType, $documentTypeList)) {
    echo "Invalid document type '$documentType' - ระบุประเภทเอกสารไม่ถูกต้อง";
    $db->close();
    exit();
}

$hasImageCover = false;
if (in_array($documentType, array('km', 'qa', 'booklet'))) {
    $hasImageCover = true;
}

$pageTitles['training'] = 'เอกสารการอบรม';
$pageTitles['km'] = ' KM';
$pageTitles['report'] = 'รายงานผลการดำเนินงาน';
$pageTitles['qa'] = 'ประกันคุณภาพ (QA)';
$pageTitles['booklet'] = 'จุลสาร/อินโฟกราฟิค';

$pageTitle = $pageTitles[$documentType];

$sql = "SELECT id, title, short_description, image_file_name, file_name, download, created_at, status 
            FROM document_download
            WHERE document_type = '$documentType'
            ORDER BY created_at DESC ";
if ($result = $db->query($sql)) {
    $documentDownloadList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($documentDownloadList, $row);
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
        <!--Lightbox-->
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">

        <style>
            #tableDownload td:nth-child(5) {
                text-align: center;
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
                    เอกสารดาวน์โหลด
                    <small><?= trim($pageTitle); ?></small>
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
                                <table id="tableDocumentDownload" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <?php
                                        if ($hasImageCover) {
                                            ?>
                                            <th style="text-align: center; width: 40%;">เรื่อง</th>
                                            <th style="text-align: center; width: 20%;">รูปภาพหน้าปก</th>
                                            <th style="text-align: center; width: 13%;">PDF</th>
                                            <th style="text-align: center; width: 12%;">ดาวน์โหลด</th>
                                            <th style="text-align: center; width: 15%;" nowrap>วันที่สร้าง</th>
                                            <th style="text-align: center;" nowrap>เผยแพร่</th>
                                            <th style="text-align: center;">จัดการ</th>
                                            <?php
                                        } else {
                                            ?>
                                            <th style="text-align: center; width: 50%;">เรื่อง</th>
                                            <th style="text-align: center; width: 15%;">PDF</th>
                                            <th style="text-align: center; width: 15%;">ดาวน์โหลด</th>
                                            <th style="text-align: center; width: 20%;" nowrap>วันที่สร้าง</th>
                                            <th style="text-align: center;" nowrap>เผยแพร่</th>
                                            <th style="text-align: center;">จัดการ</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($documentDownloadList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($documentDownloadList as $documentDownload) {
                                            $createdAt = $documentDownload['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                            ?>
                                            <tr style="">
                                                <td><?= $documentDownload['title']; ?></td>
                                                <?php
                                                if ($hasImageCover) {
                                                    ?>
                                                    <td style="text-align: center; cursor: pointer">
                                                        <a href="<?php echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); ?>"
                                                           data-lightbox="coverImage" data-title="<?= $documentDownload['title']; ?>">
                                                            <img src="<?php echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); ?>"
                                                                 width="120px">
                                                        </a>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                                <td style="text-align: center; cursor: pointer"
                                                    onClick="window.open('<?php echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['file_name']); ?>', '_blank')">
                                                    <a target="_blank" href="javascript:void(0)">
                                                        <span style="font-size: 25px"><i class="fa fa-file-pdf-o"></i></span>
                                                    </a>
                                                </td>
                                                <td align="center"><?= $documentDownload['download']; ?></td>
                                                <td style="text-align: center"><?= ($dateHidden . $displayDateTime); ?></td>
                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $documentDownload['status'] == 'publish' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $documentDownload['id']; ?>, '<?= $documentDownload['title']; ?>', <?= $userHasPermission; ?>)"
                                                        <?= $documentDownload['status'] == 'publish' ? 'checked' : '' ?>>
                                                </td>

                                                <td nowrap>
                                                    <form method="get" action="document_download_add_edit.php" style="display: inline; margin: 0">
                                                        <input type="hidden" name="document_type" value="<?= $documentType; ?>"/>
                                                        <input type="hidden" name="id" value="<?= $documentDownload['id']; ?>"/>

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
                                                                    onclick="onClickDelete(this, <?= $documentDownload['id']; ?>, '<?= $documentDownload['title']; ?>')">
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
            $('#tableDocumentDownload').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[<?= $hasImageCover ? '4' : '3'; ?>, 'desc']],
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

            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });
        });

        function onClickAdd() {
            window.location.href = 'document_download_add_edit.php?document_type=<?= $documentType; ?>';
        }

        function onChangeStatus(element, documentDownloadId, title, userHasPermission) {
            if (!userHasPermission) {
                alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้');
                location.reload(true);
                return;
            }

            let result = confirm("ยืนยัน" + (element.checked ? 'เผยแพร่' : 'ยกเลิกการเผยแพร่') + " '" + title + "' ?");
            if (result) {
                doChangeStatus(documentDownloadId, (element.checked ? 'publish' : 'draft'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function doChangeStatus(documentDownloadId, newStatus) {
            let title = 'แก้ไขสถานะเอกสารดาวน์โหลด';

            $.post(
                '../api/api.php/update_document_download_status',
                {
                    documentDownloadId: documentDownloadId,
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
                        doDeleteDocumentDownload(id);
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

        function doDeleteDocumentDownload(id) {
            $.post(
                '../api/api.php/delete_document_download',
                {
                    id: id,
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
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>