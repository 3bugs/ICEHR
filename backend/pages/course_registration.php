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
  <!--Lightbox-->
  <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">
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
      <?php
      $numRowsPerPage = 10;
      $serviceType = $_GET['service_type'];
      $page = (int)$_GET['page'] ?: 1;

      switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
          $sql = "SELECT COUNT(id) AS row_count FROM course_trainee";
          break;
        case SERVICE_TYPE_SOCIAL:
          $sql = "SELECT COUNT(id) AS row_count FROM course_registration_social";
          break;
        case SERVICE_TYPE_DRIVING_LICENSE:
          $sql = "SELECT COUNT(id) AS row_count FROM course_registration_driving_license";
          break;
      }
      $result = $db->query($sql);
      $row = $result->fetch_assoc();

      $rowCount = (int)$row['row_count'];
      $pageCount = (int)ceil($rowCount / $numRowsPerPage);

      /*$serviceTypeText = '';
      switch ($serviceType) {
          case SERVICE_TYPE_TRAINING:
              $serviceTypeText = 'บริการวิชาการ&nbsp;&nbsp;<i class="fa fa-mortar-board"></i>';
              break;
          case SERVICE_TYPE_SOCIAL:
              $serviceTypeText = 'บริการสังคม&nbsp;&nbsp;<i class="fa fa-male"></i>';
              break;
          case SERVICE_TYPE_DRIVING_LICENSE:
              $serviceTypeText = 'บริการใบขับขี่&nbsp;&nbsp;<i class="fa fa-car"></i>';
              break;
      }*/
      ?>
      <h1>
        ส่วนจัดการใบสมัครสำหรับการเงิน
        <small><?= $serviceTypeText[$serviceType]; ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title" style="display: flex; justify-content: space-between; align-items: center">
                <form class="form-inline" style="display: flex; justify-content: center; align-items: center">
                  <div style="display: flex; justify-content: left; align-items: center">
                    <span>หน้าที่</span>&nbsp;
                    <select class="form-control" id="page" name="page" form="my-form" onchange="handleChangeSelect(this)">
                      <?php
                      for ($i = 1; $i <= $pageCount; $i++) {
                        ?>
                        <option <?= $page === $i ? 'selected' : ''; ?> value="<?= $i; ?>"><?= $i; ?></option>
                        <?php
                      }
                      ?>
                    </select>&nbsp;
                    <span>จากทั้งหมด <?= $pageCount; ?> หน้า</span>
                  </div>
                </form>

                <div>
                  <button <?= $page === 1 ? 'disabled' : '' ?> type="button" class="btn btn-outline-primary" onclick="handleClickNavigate(-1)"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;หน้าก่อนหน้า</button>
                  <button <?= $page === $pageCount ? 'disabled' : '' ?> type="button" class="btn btn-outline-primary" onclick="handleClickNavigate(1)">หน้าถัดไป&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></button>
                </div>
              </h3>
            </div>
            <div class="box-body">
              <?php
              if (!isset($serviceType)) {
                echo '<div style="color: red; text-align: center">ERROR: ไม่ได้ระบุ Service Type</div>';
              } else {
                getCourseRegistrationDataTable($db, $serviceType, null, null, ($page - 1) * $numRowsPerPage, $numRowsPerPage);
              }
              ?>
            </div>
            <!-- /.box-body -->
            <div class="box-header">
              <h3 class="box-title" style="display: flex; justify-content: space-between; align-items: center">
                <form class="form-inline" style="display: flex; justify-content: center; align-items: center">
                  <div style="display: flex; justify-content: left; align-items: center">
                    <span>หน้าที่</span>&nbsp;
                    <select class="form-control" id="page" name="page" form="my-form" onchange="handleChangeSelect(this)">
                      <?php
                      for ($i = 1; $i <= $pageCount; $i++) {
                        ?>
                        <option <?= $page === $i ? 'selected' : ''; ?> value="<?= $i; ?>"><?= $i; ?></option>
                        <?php
                      }
                      ?>
                    </select>&nbsp;
                    <span>จากทั้งหมด <?= $pageCount; ?> หน้า</span>
                  </div>
                </form>

                <div>
                  <button <?= $page === 1 ? 'disabled' : '' ?> type="button" class="btn btn-outline-primary" onclick="handleClickNavigate(-1)"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;หน้าก่อนหน้า</button>
                  <button <?= $page === $pageCount ? 'disabled' : '' ?> type="button" class="btn btn-outline-primary" onclick="handleClickNavigate(1)">หน้าถัดไป&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></button>
                </div>
              </h3>
            </div>
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
<!--Lightbox-->
<script src="../dist/lightbox/js/lightbox.js"></script>
<script>
  function handleClickNavigate(pageOffset) {
    const currentPage = <?= $page ?>;
    const pageCount = <?= $pageCount; ?>;
    const pageToGo = currentPage + pageOffset;

    if (pageToGo < 1 || pageToGo > pageCount) {
      return;
    }

    const thisPageUrl = location.protocol + '//' + location.host + location.pathname;
    window.location.href = `${thisPageUrl}?service_type=<?= $serviceType; ?>&page=${pageToGo}`;
  }

  function handleChangeSelect(e) {
    const thisPageUrl = location.protocol + '//' + location.host + location.pathname;
    window.location.href = `${thisPageUrl}?service_type=<?= $serviceType; ?>&page=${e.options[e.selectedIndex].text}`;
  }
</script>
</body>
</html>
<?php
require_once '../include/foot_php.inc';
?>
