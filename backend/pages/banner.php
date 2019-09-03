<?php
require_once '../include/head_php.inc';

$pageTitle = 'แบนเนอร์';
$sortColumn = 0;

$sql = "SELECT id, title, details, image_file_name, sort_index, created_at, status
            FROM intro
            WHERE type = '$type'
            ORDER BY sort_index";
if ($result = $db->query($sql)) {
    $itemList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($itemList, $row);
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
        <!--jQuery UI (ใช้ sortable)-->
        <link rel="stylesheet" href="../bower_components/jquery-ui/themes/base/jquery-ui.css">

        <style>
            #sortableItems {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            #sortableItems li {
                cursor: pointer;
                margin: 0 3px 3px 3px;
                padding: 0.4em;
                padding-left: 1.5em;
                font-size: 1.0em;
            }

            #sortableItems li span {
                position: absolute;
                margin-left: -1.3em;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <!-- Sort Modal -->
    <div class="modal fade" id="sortItemModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เรียงลำดับการแสดงในหน้าเว็บ</h4>
                </div>
                <div class="modal-body">
                    <form id="formSortItem" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <ul id="sortableItems">
                                <?php
                                foreach ($itemList as $item) {
                                    if ($item['status'] === 'publish') {
                                        ?>
                                        <li class="ui-state-default">
                                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                            <?= $item['title']; ?>
                                            <input type="hidden" value="<?= $item['id']; ?>"/>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>

                            <div id="divLoading" style="text-align: center; margin-top: 25px">
                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button id="buttonSave" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                บันทึก
                            </button>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!--<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>-->
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
                                <!--<button type="button" class="btn btn-info pull-right"
                                        data-toggle="modal" data-target="#sortItemModal">
                                    <span class="fa fa-sort"></span>&nbsp;
                                    เรียงลำดับ
                                </button>-->
                                <form method="post" action="intro_add_edit.php" style="display: inline">
                                    <input type="hidden" name="type" value="<?= $type; ?>"/>
                                    <button type="submit" class="btn btn-success pull-right">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่ม<?= $pageTitle; ?>
                                    </button>
                                </form>
                            </div>
                            <div class="box-body">
                                <table id="tableItems" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 40%;">หัวข้อ/เรื่อง</th>
                                        <th style="text-align: center; width: 20%;">รูปภาพ</th>
                                        <th style="text-align: center; width: 20%;">Link</th>
                                        <th style="text-align: center; width: 20%;" nowrap>วันที่สร้าง</th>
                                        <th style="text-align: center;" nowrap>เผยแพร่</th>
                                        <th style="text-align: center;">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($itemList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($itemList as $item) {
                                            $createdAt = $item['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                            ?>
                                            <tr style="">
                                                <td style="text-align: center"><?= $item['sort_index'] != null ? $item['sort_index'] : '<span style="display: none">999999</span>'; ?></td>
                                                <td><?= $item['title']; ?></td>
                                                <?php
                                                if ($type === TYPE_MISSION) {
                                                    ?>
                                                    <td style="text-align: center; cursor: pointer">
                                                        <a href="<?php echo(UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); ?>"
                                                           data-lightbox="coverImage" data-title="<?= $item['title']; ?>">
                                                            <img src="<?php echo(UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); ?>"
                                                                 width="120px">
                                                        </a>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                                <td style="text-align: center"><?= ($dateHidden . $displayDateTime); ?></td>
                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $item['status'] == 'publish' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $item['id']; ?>, '<?= $item['title']; ?>')"
                                                        <?= $item['status'] == 'publish' ? 'checked' : '' ?>>
                                                </td>

                                                <td nowrap>
                                                    <form method="POST" action="intro_add_edit.php" style="display: inline; text-align: right; margin: 0">
                                                        <input type="hidden" name="type" value="<?= $type; ?>"/>
                                                        <input type="hidden" name="id" value="<?= $item['id']; ?>"/>

                                                        <button type="submit" class="btn btn-warning"
                                                                style="margin-left: 3px">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                style="margin-left: 3px; margin-right: 3px"
                                                                onclick="onClickDelete(this, <?= $item['id']; ?>, '<?= $item['title']; ?>')">
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
        let sortableItems = $('#sortableItems');

        $(document).ready(function () {
            $('#tableItems').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[<?= $sortColumn; ?>, 'asc']],
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

            $('#formSortItem').submit(event => {
                event.preventDefault();
                sortItems();
            });
            $('#formSortItem').submit(event => {
                event.preventDefault();
                sortItems();
            });

            $('#formSortItem #divLoading').hide();
            sortableItems.sortable();
            sortableItems.disableSelection();
        });

        function sortItems() {
            let sortValue = '';
            sortableItems.children('li').each((index, listItem) => {
                let itemId = $(listItem).find('input').val();
                //msg += $(listItem).text().trim() + ' [' + departmentId + '-' + (index + 1) + '],';
                sortValue += itemId + '-' + (index + 1) + ',';
            });
            doSortItems(sortValue.slice(0, -1));
            //alert(sortValue);
        }

        function doSortItems(sortValue) {
            $('#formSortItem #responseText').text('');
            $('#formSortItem #buttonSave').prop('disabled', true);
            $('#formSortItem #divLoading').show();
            $.post(
                '../api/api.php/sort_intro',
                {
                    sortValue: sortValue
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formSortItem #buttonSave').prop('disabled', false);
                    $('#formSortItem #divLoading').hide();
                    $('#formSortItem #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formSortItem #buttonSave').prop('disabled', false);
                $('#formSortItem #divLoading').hide();
                $('#formSortItem #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickAdd() {
            //window.location.href = 'intro_add_edit.php?type=<?= $type; ?>';
        }

        function onChangeStatus(element, id, title) {
            let result = confirm("ยืนยัน" + (element.checked ? 'เผยแพร่' : 'ยกเลิกการเผยแพร่') + " '" + title + "' ?");
            if (result) {
                doChangeStatus(id, (element.checked ? 'publish' : 'draft'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function doChangeStatus(id, newStatus) {
            let title = 'แก้ไขสถานะ';

            $.post(
                '../api/api.php/update_intro_status',
                {
                    id: id,
                    newStatus: newStatus,
                    type: '<?= $type; ?>'
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
                        doDelete(id);
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

        function doDelete(id) {
            $.post(
                '../api/api.php/delete_intro',
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
    <!--jQuery UI (ใช้ sortable)-->
    <script src="../bower_components/jquery-ui/jquery-ui.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>