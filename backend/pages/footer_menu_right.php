<?php
require_once '../include/head_php.inc';

$sql = "SELECT  a.id,
                a.name,
                a.ordering,
                a.status,
                a.created_at,
                a.created_by,
                u.username,
                (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
        "
    . " FROM menus a INNER JOIN user u ON a.created_by = u.id "
    . " WHERE a.position ='footer-right' AND parent_id ='0'"
    . " ORDER BY a.ordering ASC";
// echo $sql;
// exit();
//$sql = "SELECT * FROM course ORDER BY begin_date";
if ($result = $db->query($sql)) {
    $itemsList = array();
    while ($row = $result->fetch_assoc()) {
        $data = array();
        $data['id'] = (int)$row['id'];
        $data['name'] = $row['name'];
        $data['ordering'] = $row['ordering'];
        $data['status'] = $row['status'];
        $data['created_at'] = $row['created_at'];
        $data['username'] = $row['username'];
        $data['count_parent_id'] = $row['count_parent_id'];
        array_push($itemsList, $data);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

// echo "<pre>";
//         print_r($itemsList);

// echo "<pre>";
// exit();
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
    <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    เมนูส่วนท้าย ตำแหน่งขวา
                    <small>เมนูส่วนท้าย ตำแหน่งขวา</small>
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
                                    เพิ่มเมนูส่วนท้าย ตำแหน่งขวา
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCourse111111" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 60%; text-align: center">หัวข้อ</th>
                                        <th style="width: 10%; text-align: center">ลำดับ</th>
                                        <th style="width: 20%; text-align: center">สถานะ</th>
                                        <th style="width: 20%; text-align: center">วันที่สร้าง</th>
                                        <th style="width: 20%; text-align: center">สร้างโดย</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($itemsList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="4" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($itemsList as $item) {
                                            $itemId = $item['id'];
                                            $itemName = $item['name'];
                                            $ordering = $item['ordering'];
                                            $status = $item['status'];
                                            $created_at = $item['created_at'];
                                            $created_by = $item['username'];
                                            $count_parent_id = $item['count_parent_id'];
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: middle"><?php echo $itemName; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $ordering; ?></td>
                                                <td style="vertical-align: middle; text-align: center">
                                                    <span class="label label-info label-green">
                                                        <?php echo ($status ==='publish' ? 'เผยแพร่':'ไม่เผยแพร่') ?> 
                                                    </span>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $created_at; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $created_by; ?></td>

                                                <td style="text-align: center" nowrap>
                                                    <form method="post" action="footer_menu_right_add_edit.php">
                                                        <input type="hidden" name="itemId" value="<?php echo $itemId; ?>"/>
                                                        <button type="submit" class="btn btn-warning">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php echo $itemId; ?>, '<?php echo $itemName; ?>')">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            ลบ
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                                /* Level2 */
                                                if($count_parent_id >0){
                                                    $sql_level2 = "SELECT  a.id,
                                                                    a.name,
                                                                    a.ordering,
                                                                    a.status,
                                                                    a.created_at,
                                                                    a.created_by,
                                                                    u.username,
                                                                    (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
                                                            "
                                                        . " FROM menus a INNER JOIN user u ON a.created_by = u.id "
                                                        . " WHERE a.position ='footer-right' AND parent_id ='$itemId'"
                                                        . " ORDER BY a.ordering ASC";
                                                    if ($result_level2 = $db->query($sql_level2)) {
                                                            $itemsList_level2 = array();
                                                            while ($row_level2 = $result_level2->fetch_assoc()) {
                                                                $data_level2 = array();
                                                                $data_level2['id'] = (int)$row_level2['id'];
                                                                $data_level2['name'] = $row_level2['name'];
                                                                $data_level2['ordering'] = $row_level2['ordering'];
                                                                $data_level2['status'] = $row_level2['status'];
                                                                $data_level2['created_at'] = $row_level2['created_at'];
                                                                $data_level2['username'] = $row_level2['username'];
                                                                $data_level2['count_parent_id'] = $row_level2['count_parent_id'];
                                                                array_push($itemsList_level2, $data_level2);
                                                            }
                                                            $result_level2->close();
                                                    } else {
                                                            echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                                                            $db->close();
                                                            exit();
                                                    }
                                                    //print_r($itemsList_level2);
                                                    foreach ($itemsList_level2 as $item_level2) {
                                                        $itemId_level2 = $item_level2['id'];
                                                        $itemName_level2 = $item_level2['name'];
                                                        $ordering_level2 = $item_level2['ordering'];
                                                        $status_level2 = $item_level2['status'];
                                                        $created_at_level2 = $item_level2['created_at'];
                                                        $created_by_level2 = $item_level2['username'];
                                                        $count_parent_id_level2 = $item_level2['count_parent_id'];
                                            ?>
                                                        <tr style="">
                                                            <td style="vertical-align: middle"><?php echo "&nbsp;&nbsp;- ".$itemName_level2; ?></td>
                                                            <td style="vertical-align: middle; text-align: center"><?php echo $ordering_level2; ?></td>
                                                            <td style="vertical-align: middle; text-align: center">
                                                                <span class="label label-info label-green">
                                                                    <?php echo ($status_level2 ==='publish' ? 'เผยแพร่':'ไม่เผยแพร่') ?> 
                                                                </span>
                                                            </td>
                                                            <td style="vertical-align: middle; text-align: center"><?php echo $created_at_level2; ?></td>
                                                            <td style="vertical-align: middle; text-align: center"><?php echo $created_by_level2; ?></td>

                                                            <td style="text-align: center" nowrap>
                                                                <form method="post" action="footer_menu_right_add_edit.php">
                                                                    <input type="hidden" name="itemId" value="<?php echo $itemId_level2; ?>"/>
                                                                    <button type="submit" class="btn btn-warning">
                                                                        <span class="fa fa-pencil"></span>&nbsp;
                                                                        แก้ไข
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger"
                                                                            onclick="onClickDelete(this, <?php echo $itemId_level2; ?>, '<?php echo $itemName_level2; ?>')">
                                                                        <span class="fa fa-pencil"></span>&nbsp;
                                                                        ลบ
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                            /* Level3 */
                                                            if($count_parent_id_level2 >0){
                                                                $sql_level3 = "SELECT   a.id,
                                                                                        a.name,
                                                                                        a.ordering,
                                                                                        a.status,
                                                                                        a.created_at,
                                                                                        a.created_by,
                                                                                        u.username,
                                                                                        (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
                                                                                "
                                                                    . " FROM menus a INNER JOIN user u ON a.created_by = u.id "
                                                                    . " WHERE a.position ='footer-right' AND parent_id ='$itemId_level2'"
                                                                    . " ORDER BY a.ordering ASC";
                                                                if ($result_level3 = $db->query($sql_level3)) {
                                                                        $itemsList_level3 = array();
                                                                        while ($row_level3 = $result_level3->fetch_assoc()) {
                                                                            $data_level3 = array();
                                                                            $data_level3['id'] = (int)$row_level3['id'];
                                                                            $data_level3['name'] = $row_level3['name'];
                                                                            $data_level3['ordering'] = $row_level3['ordering'];
                                                                            $data_level3['status'] = $row_level3['status'];
                                                                            $data_level3['created_at'] = $row_level3['created_at'];
                                                                            $data_level3['username'] = $row_level3['username'];
                                                                            $data_level3['count_parent_id'] = $row_level3['count_parent_id'];
                                                                            array_push($itemsList_level3, $data_level3);
                                                                        }
                                                                        $result_level3->close();
                                                                } else {
                                                                        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                                                                        $db->close();
                                                                        exit();
                                                                }
                                                                //print_r($itemsList_level3);
                                                                foreach ($itemsList_level3 as $item_level3) {
                                                                    $itemId_level3 = $item_level3['id'];
                                                                    $itemName_level3 = $item_level3['name'];
                                                                    $ordering_level3 = $item_level3['ordering'];
                                                                    $status_level3 = $item_level3['status'];
                                                                    $created_at_level3 = $item_level3['created_at'];
                                                                    $created_by_level3 = $item_level3['username'];
                                                                    $count_parent_id_level3 = $item_level3['count_parent_id'];
                                                        ?>
                                                                    <tr style="">
                                                                        <td style="vertical-align: middle"><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- ".$itemName_level3; ?></td>
                                                                        <td style="vertical-align: middle; text-align: center"><?php echo $ordering_level3; ?></td>
                                                                        <td style="vertical-align: middle; text-align: center">
                                                                            <span class="label label-info label-green">
                                                                                <?php echo ($status_level3 ==='publish' ? 'เผยแพร่':'ไม่เผยแพร่') ?> 
                                                                            </span>
                                                                        </td>
                                                                        <td style="vertical-align: middle; text-align: center"><?php echo $created_at_level3; ?></td>
                                                                        <td style="vertical-align: middle; text-align: center"><?php echo $created_by_level3; ?></td>

                                                                        <td style="text-align: center" nowrap>
                                                                            <form method="post" action="footer_menu_right_add_edit.php">
                                                                                <input type="hidden" name="itemId" value="<?php echo $itemId_level3; ?>"/>
                                                                                <button type="submit" class="btn btn-warning">
                                                                                    <span class="fa fa-pencil"></span>&nbsp;
                                                                                    แก้ไข
                                                                                </button>
                                                                                <button type="button" class="btn btn-danger"
                                                                                        onclick="onClickDelete(this, <?php echo $itemId_level3; ?>, '<?php echo $itemName_level3; ?>')">
                                                                                    <span class="fa fa-pencil"></span>&nbsp;
                                                                                    ลบ
                                                                                </button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                        <?php
                                                                }/*Endforeach Level3 */
                                                            }/*Endif Level3 */
                                                        ?>
                                            <?php
                                                    }/* EndForeach Level 2 */
                                                }/* Endif Level2 */
                                            ?>
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
            $('#tableCourse').DataTable({
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
            window.location.href = 'footer_menu_right_add_edit.php';
        }

        function onClickDelete(element, menuId, itemName) {
            BootstrapDialog.show({
                title: 'ลบเมนูส่วนท้าย ตำแหน่งขวา',
                message: 'ยืนยันลบเมนูส่วนท้าย ตำแหน่งขวา \'' + itemName + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteMenu(menuId);
                        self.close();
                    },
                    cssClass: 'btn-primary'
                },{
                    label: 'ยกเลิก',
                    action: function (self) {
                        self.close();
                    }
                }]
            });
        }

        function doDeleteMenu(menuId) {
            $.post(
                '../api/api.php/delete_menu',
                {
                    menuId: menuId,
                }
            ).done(function (data) {
                console.log(data);
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบเมนูส่วนท้าย ตำแหน่งขวา - ผิดพลาด',
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
                    title: 'ลบเมนูส่วนท้าย ตำแหน่งขวา - ผิดพลาด',
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
$db->close();
?>