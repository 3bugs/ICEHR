<?php
require_once '../include/head_php.inc';

$newsType = $_GET['news_type'];
$newsTypeList = array(
    'training', 'public-relations', 'in-house', 'activity'
);
if (!isset($newsType) || !in_array($newsType, $newsTypeList)) {
    echo "Invalid news type '$newsType' - ระบุประเภทข่าวไม่ถูกต้อง";
    $db->close();
    exit();
}

$pageTitles['training'] = 'ข่าวการฝึกอบรม';
$pageTitles['public-relations'] = 'ข่าวประชาสัมพันธ์';
$pageTitles['in-house'] = 'ข่าวหลักสูตร In-House ที่ผ่านมา';
$pageTitles['activity'] = 'ภาพกิจกรรม';

$pageTitle = $pageTitles[$newsType];

$sql = "SELECT id, title, details, image_file_name, news_date, created_at, status
            FROM news
            WHERE news_type = '$newsType'
            ORDER BY created_at DESC ";
if ($result = $db->query($sql)) {
    $newsList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($newsList, $row);
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
                                <button type="button" class="btn btn-success pull-right"
                                        onclick="onClickAdd(this)">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่ม<?= $pageTitle; ?>
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableNews" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 40%;">หัวข้อ/เรื่อง</th>
                                        <th style="text-align: center; width: 20%;">รูปภาพหน้าปก</th>
                                        <th style="text-align: center; width: 22%;">วันที่ข่าว/กิจกรรม</th>
                                        <th style="text-align: center; width: 18%;" nowrap>วันที่สร้าง</th>
                                        <th style="text-align: center;" nowrap>เผยแพร่</th>
                                        <th style="text-align: center;">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($newsList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($newsList as $news) {
                                            $createdAt = $news['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                                            $newsDate = $news['news_date'];
                                            $dateTimePart = explode(' ', $newsDate);
                                            $displayNewsDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $newsDateHidden = '<span style="display: none">' . $newsDate . '</span></span>';
                                            ?>
                                            <tr style="">
                                                <td><?= $news['title']; ?></td>
                                                <td style="text-align: center; cursor: pointer">
                                                    <a href="<?php echo(UPLOAD_DIR_NEWS_ASSETS . $news['image_file_name']); ?>"
                                                       data-lightbox="coverImage" data-title="<?= $news['title']; ?>">
                                                        <img src="<?php echo(UPLOAD_DIR_NEWS_ASSETS . $news['image_file_name']); ?>"
                                                             width="120px">
                                                    </a>
                                                </td>
                                                <td style="text-align: center"><?= ($newsDateHidden . $displayNewsDate); ?></td>
                                                <td style="text-align: center"><?= ($dateHidden . $displayDateTime); ?></td>
                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $news['status'] == 'publish' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $news['id']; ?>, '<?= $news['title']; ?>')"
                                                        <?= $news['status'] == 'publish' ? 'checked' : '' ?>>
                                                </td>

                                                <td nowrap>
                                                    <form method="get" action="news_add_edit.php" style="display: inline; margin: 0">
                                                        <input type="hidden" name="news_type" value="<?= $newsType; ?>"/>
                                                        <input type="hidden" name="news_id" value="<?= $news['id']; ?>"/>

                                                        <button type="submit" class="btn btn-warning"
                                                                style="margin-left: 3px">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                style="margin-left: 3px; margin-right: 3px"
                                                                onclick="onClickDelete(this, <?= $news['id']; ?>, '<?= $news['title']; ?>')">
                                                            <span class="fa fa-remove"></span>&nbsp;
                                                            ลบ
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
        let downloadListDataTable = null;

        $(document).ready(function () {
            $('#tableNews').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[3, 'desc']],
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
            window.location.href = 'news_add_edit.php?news_type=<?= $newsType; ?>';
        }

        function onChangeStatus(element, newsId, title) {
            let result = confirm("ยืนยัน" + (element.checked ? 'เผยแพร่' : 'ยกเลิกการเผยแพร่') + " '" + title + "' ?");
            if (result) {
                doChangeStatus(newsId, (element.checked ? 'publish' : 'draft'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function doChangeStatus(newsId, newStatus) {
            let title = 'แก้ไขสถานะเอกสารดาวน์โหลด';

            $.post(
                '../api/api.php/update_news_status',
                {
                    newsId: newsId,
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
                        doDeleteNews(id);
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

        function doDeleteNews(id) {
            $.post(
                '../api/api.php/delete_news',
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