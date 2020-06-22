<?php
require_once '../include/head_php.inc';

$sql = "SELECT id, title, first_name, last_name, position, signature_image
        FROM certificate_signer";
if ($result = $db->query($sql)) {
  $trainerList = array();
  while ($row = $result->fetch_assoc()) {
    array_push($trainerList, $row);
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
  <!--<link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">-->

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
        ผู้ลงนามใบรับรอง
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
              if (false && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER)) {
                ?>
                <button type="button" class="btn btn-success pull-right"
                        onclick="onClickAdd(this)">
                  <span class="fa fa-plus"></span>&nbsp;
                  เพิ่มวิทยากร
                </button>
                <?php
              }
              ?>
            </div>
            <div class="box-body">
              <table id="tableCertificateSigner" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>&nbsp;</th>
                  <th style="width: 50%; text-align: center">ชื่อ-นามสกุล</th>
                  <th style="width: 50%; text-align: center">ตำแหน่ง</th>
                  <th style="text-align: center">จัดการ</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (sizeof($trainerList) == 0) {
                  ?>
                  <!--<tr valign="middle">
                      <td colspan="20" align="center">ไม่มีข้อมูล</td>
                  </tr>-->
                  <?php
                } else {
                  $firstRow = true;
                  foreach ($trainerList as $trainer) {
                    $thaiMonth = array(
                        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                    );
                    $trainerInfo = sprintf(
                        '%s %s %s',
                        $trainer['title'], $trainer['first_name'], $trainer['last_name']
                    );
                    ?>
                    <tr style="">
                      <td><?= $firstRow ? 'ซ้าย' : 'ขวา'; ?></td>
                      <?php
                      $firstRow = false;
                      ?>
                      <td style="vertical-align: top"><?= $trainerInfo; ?></td>
                      <td style="vertical-align: top; text-align: center"><?= $trainer['position']; ?></td>
                      <td style="text-align: center" nowrap>
                        <?php
                        if (currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER)) {
                          ?>
                          <form method="get" action="certificate_signer_edit.php" style="display: inline">
                            <input type="hidden" name="trainer_id" value="<?= $trainer['id']; ?>"/>
                            <button type="submit" class="btn btn-warning" style="margin-left: 3px; margin-right: 3px">
                              <span class="fa fa-pencil"></span>&nbsp;
                              แก้ไข
                            </button>
                          </form>
                          <?php
                        }
                        ?>

                        <?php
                        if (false && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER)) {
                          ?>
                          <button type="button" class="btn btn-danger"
                                  onclick="onClickDelete(
                                    this,
                                  <?= $trainer['id']; ?>,
                                    '<?= "{$trainer['title']} {$trainer['first_name']} {$trainer['last_name']}"; ?>'
                                    )">
                            <span class="fa fa-remove"></span>&nbsp;
                            ลบ
                          </button>
                          <?php
                        }
                        ?>
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
    $('#tableCertificateSigner').DataTable({
      stateSave: false,
      stateDuration: -1, // sessionStorage
      /*order: [[1, 'desc']],*/
      ordering: false,
      paging: false,
      info: false,
      searching: false,
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
      drawCallback: function (row, data) {
        $('.my-toggle').bootstrapToggle();
      }
    });
  });

  function onClickAdd() {
    window.location.href = 'trainer_add_edit.php';
  }

  function onChangeStatus(element, trainerId, trainerDisplayName) {
    let result = confirm("ยืนยัน '" + (element.checked ? 'เปิด' : 'ปิด') + "' วิทยากรนี้ (" + trainerDisplayName + ")?");
    if (result) {
      doChangeStatus(trainerId, (element.checked ? 'active' : 'inactive'));
    } else {
      /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
      location.reload(true);
    }
  }

  function onClickDelete(element, trainerId, trainerDisplayName) {
    let result = confirm("ยืนยันลบวิทยากรนี้ (" + trainerDisplayName + ")?");
    if (result) {
      doChangeStatus(trainerId, 'deleted');
    } else {
      //do nothing
    }
  }

  function doChangeStatus(trainerId, newStatus) {
    let title = 'แก้ไขสถานะวิทยากร';
    if (newStatus === 'deleted') {
      title = 'ลบวิทยากร';
    }

    $.post(
      '../api/api.php/update_trainer_status',
      {
        trainerId: trainerId,
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
</script>

<?php require_once('../include/foot.inc'); ?>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!--Lightbox-->
<!--<script src="../dist/lightbox/js/lightbox.js"></script>-->
</body>
</html>

<?php
require_once '../include/foot_php.inc';
?>
