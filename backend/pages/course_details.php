<?php
require_once '../include/head_php.inc';
require_once '../components/get_course_registration_data_table.php';

$courseId = $_GET['course_id'];
$course = array();
if (isset($courseId)) {
  $courseId = $db->real_escape_string($courseId);
  $sql = "SELECT c.course_master_id, c.batch_number, c.details, c.trainee_limit, c.application_fee, 
                   c.place, c.begin_date, c.end_date, c.responsible_user_id, c.trainer_id, c.is_online,
                   cm.title AS course_title, cm.service_type
            FROM course c 
                INNER JOIN course_master cm 
                    ON c.course_master_id = cm.id 
            WHERE c.id = $courseId";

  if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $course['course_title'] = $row['course_title'];
      $course['course_master_id'] = (int)$row['course_master_id'];
      $course['batch_number'] = (int)$row['batch_number'];
      $course['details'] = $row['details'];
      $course['trainee_limit'] = (int)$row['trainee_limit'];
      $course['application_fee'] = (int)$row['application_fee'];
      $course['place'] = $row['place'];
      $course['begin_date'] = $row['begin_date'];
      $course['end_date'] = $row['end_date'];
      $course['responsible_user_id'] = (int)$row['responsible_user_id'];
      $course['trainer_id'] = (int)$row['trainer_id'];
      $course['service_type'] = $row['service_type'];
      $course['is_online'] = (int)$row['is_online'];
    }
    $result->close();
  } else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
    $db->close();
    exit();
  }
} else {
  echo 'Unexpected error: ไม่ได้ระบุ course id !';
  $db->close();
  exit();
}

$sql = "SELECT * FROM course_master";
if ($result = $db->query($sql)) {
  $courseMasterList = array();
  while ($row = $result->fetch_assoc()) {
    $courseMaster = array();
    $courseMaster['id'] = (int)$row['id'];
    $courseMaster['title'] = $row['title'];

    array_push($courseMasterList, $courseMaster);
  }
  $result->close();
} else {
  echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
  $db->close();
  exit();
}

$sql = "SELECT * FROM user";
if ($result = $db->query($sql)) {
  $userList = array();
  while ($row = $result->fetch_assoc()) {
    $user = array();
    $user['id'] = (int)$row['id'];
    $user['first_name'] = $row['first_name'];
    $user['last_name'] = $row['last_name'];
    $user['email'] = $row['email'];
    $user['phone_office'] = $row['phone_office'];

    array_push($userList, $user);
  }
  $result->close();
} else {
  echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
  $db->close();
  exit();
}

$trainerList = array();
if ($course['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
  $sql = "SELECT * FROM trainer";
  if ($result = $db->query($sql)) {
    //$trainerList = array();
    while ($row = $result->fetch_assoc()) {
      array_push($trainerList, $row);
    }
    $result->close();
  } else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
    $db->close();
    exit();
  }
}

/*$sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.created_at, ct.register_status, ct.course_registration_id,
               cr.course_id, cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email
            FROM course_registration cr
                INNER JOIN course c
                    ON cr.course_id = c.id
                INNER JOIN course_trainee ct
                    ON ct.course_registration_id = cr.id
            WHERE c.id = $courseId
            ORDER BY ct.id DESC";*/

switch ($course['service_type']) {
  case SERVICE_TYPE_TRAINING:
    $sql = "SELECT ct.register_status, ct.id AS trainee_id
        FROM course_registration cr
            INNER JOIN course c
                ON cr.course_id = c.id
            INNER JOIN course_trainee ct
                ON ct.course_registration_id = cr.id
        WHERE c.id = $courseId";
    break;

  case SERVICE_TYPE_SOCIAL:
    $sql = "SELECT cr.register_status, cr.id AS trainee_id
        FROM course_registration_social cr 
            INNER JOIN course c 
                ON cr.course_id = c.id 
        WHERE c.id = $courseId";
    break;

  case SERVICE_TYPE_DRIVING_LICENSE:
    $sql = "SELECT cr.register_status, cr.id AS trainee_id
        FROM course_registration_driving_license cr 
            INNER JOIN course c 
                ON cr.course_id = c.id 
        WHERE c.id = $courseId";
    break;
}

if ($result = $db->query($sql)) {
  $traineeList = array();
  while ($row = $result->fetch_assoc()) {
    $trainee = array();
    $trainee['register_status'] = $row['register_status'];
    $trainee['id'] = $row['trainee_id'];

    /*$trainee['id'] = (int)$row['id'];
    $trainee['form_number'] = $row['form_number'];
    $trainee['title'] = $row['title'];
    $trainee['first_name'] = $row['first_name'];
    $trainee['last_name'] = $row['last_name'];
    $trainee['phone'] = $row['phone'];
    $trainee['email'] = $row['email'];
    $trainee['created_at'] = $row['created_at'];
    $trainee['coordinator'] = array(
        'title' => $row['coordinator_title'],
        'first_name' => $row['coordinator_first_name'],
        'last_name' => $row['coordinator_last_name'],
        'phone' => $row['coordinator_phone'],
        'email' => $row['coordinator_email']
    );
    $trainee['course_id'] = (int)$row['course_id'];*/

    array_push($traineeList, $trainee);
  }
  $result->close();
} else {
  echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
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

  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">

<!-- Certificate settings modal -->
<div class="modal fade" id="certificateSettingsModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;
        </button>
        <h4 class="modal-title">ตั้งค่าการสร้างใบรับรอง</h4>
      </div>
      <div class="modal-body">
        <!--<div id="divLoading" style="text-align: center">
            <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
        </div>-->
        <!--<div id="alertReceiptSuccess" class="alert alert-success alert-dismissible">
            <button type="button" class="close" aria-hidden="true" onClick="$('#alertReceiptSuccess').hide()">&times;</button>
            <i class="icon fa fa-check"></i><span id="alertReceiptSuccessText"></span>
        </div>
        <div id="alertReceiptError" class="alert alert-danger alert-dismissible">
            <button type="button" class="close" aria-hidden="true" onClick="$('#alertReceiptError').hide()">&times;</button>
            <i class="icon fa fa-warning"></i><span id="alertReceiptErrorText"></span>
        </div>-->

        <form id="formCertificateSettings" role="form"
              action="word_ac_certificate.php?service_type=<?= $course['service_type']; ?>&course_id=<?= $courseId; ?>"
              method="post" target="_blank"
              style="margin-top: 0; margin-bottom: 0">
          <div class="box-body">
            <!--<input type="hidden" id="inputTraineeId" name="trainee_id">-->

            <!--<div id="alertCanNotPrint" class="alert alert-danger alert-dismissible">
                <i class="icon fa fa-warning"></i>ไม่สามารถพิมพ์ใบเสร็จรับเงินของใบสมัครนี้ได้ เนื่องจากสถานะการชำระเงินยังไม่สมบูรณ์
            </div>-->

            <!--ข้อความหลักสูตร-->
            <div class="form-group">
              <label for="inputCourseText">ข้อความหลักสูตร:</label>
              <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                <?php
                $courseBatchNumber = $course['batch_number'];
                $courseText = "ได้ผ่านการอบรม หลักสูตร &#8220{$course['course_title']}&#8221 รุ่นที่ {$courseBatchNumber}";
                ?>
                <textarea class="form-control" rows="3"
                          id="inputCourseText" name="courseText"
                          placeholder="กรอกข้อความหลักสูตร" required
                          oninvalid="this.setCustomValidity('กรอกข้อความหลักสูตร')"
                          oninput="this.setCustomValidity('')"><?= $courseText; ?></textarea>
              </div>
            </div>

            <!--ขนาดฟอนต์-->
            <div class="form-group">
              <label for="inputFontSize">ขนาดฟอนต์:</label>
              <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-font"></i>
                                    </span>
                <input type="number" class="form-control"
                       id="inputFontSize" name="fontSize"
                       placeholder="กรอกขนาดฟอนต์" required
                       value="24"
                       oninvalid="this.setCustomValidity('กรอกขนาดฟอนต์')"
                       oninput="this.setCustomValidity('')">
              </div>
            </div>
            <div id="responseText"
                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">

            <button id="buttonGenerateCertificate" type="submit"
                    class="btn btn-info pull-right">
              <span class="fa fa-file-word-o"></span>&nbsp;
              สร้างใบรับรอง
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
      <?php
      $registerStatusStart = 0;
      $registerStatusWaitApprove = 0;
      $registerStatusComplete = 0;
      $registerStatusCancel = 0;
      foreach ($traineeList as $trainee) {
        switch ($trainee['register_status']) {
          case 'start':
            $registerStatusStart++;
            break;
          case 'wait-approve':
            $registerStatusWaitApprove++;
            break;
          case 'complete':
            $registerStatusComplete++;
            break;
          case 'cancel':
            $registerStatusCancel++;
            break;
        }
      }
      $registerAll = $registerStatusStart + $registerStatusWaitApprove + $registerStatusComplete;
      ?>

      <h1 style="float: right; color: #666">
        <small>สมัคร</small>
        <strong><?= "$registerAll / {$course['trainee_limit']}" ?></strong>
      </h1>
      <h1>
        ข้อมูลการสมัคร
        <small><?= $serviceTypeText[$course['service_type']]; ?></small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <!--Widgets-->
      <div id="divWidgetRow" class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-pencil-square-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text"><?= (($course['service_type'] === SERVICE_TYPE_SOCIAL && $course['application_fee'] === 0) ? 'สมัคร' : 'ยังไม่ได้ชำระเงิน'); ?></span>
              <span id="spanStart" class="info-box-number"><?= $registerStatusStart; ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <?php
        if (!($course['service_type'] === SERVICE_TYPE_SOCIAL && $course['application_fee'] === 0)) {
          ?>
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">ชำระเงินและตรวจสอบแล้ว</span>
                <span id="spanComplete" class="info-box-number"><?= $registerStatusComplete; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-start"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">รอตรวจสอบการชำระเงิน</span>
                <span id="spanWaitApprove" class="info-box-number"><?= $registerStatusWaitApprove; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <?php
        }
        ?>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-times-circle"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ยกเลิก</span>
              <span id="spanCancel" class="info-box-number"><?= $registerStatusCancel; ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-xs-12">

          <!--รายละเอียดหลักสูตร-->
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">รายละเอียดหลักสูตร</h3>
              <?= $course['is_online'] === 1 ? '<small class="label bg-red"><i class="fa fa-play-circle"></i>&nbsp;อบรมออนไลน์</small>' : ''; ?>&nbsp;

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

              <!--ชื่อและรุ่นหลักสูตร-->
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label for="selectCourseMaster">ชื่อหลักสูตร:</label>
                    <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                      <select id="selectCourseMaster" class="form-control" required
                              oninvalid="this.setCustomValidity('เลือกชื่อหลักสูตร')"
                              oninput="this.setCustomValidity('')"
                              disabled>
                        <option value="" disabled selected>-- เลือกชื่อหลักสูตร --</option>
                        <?php
                        foreach ($courseMasterList as $cm) {
                          $courseMasterId = $cm['id'];
                          $courseMasterTitle = $cm['title'];
                          $selected = '';
                          if (!empty($course) && ($course['course_master_id'] === $courseMasterId)) {
                            $selected = 'selected';
                          }
                          ?>
                          <option value="<?= $courseMasterId; ?>" <?= $selected ?>>
                            <?= $courseMasterTitle ?>
                          </option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                </div>
                <div class="col-md-3">
                  <?php
                  if ($course['service_type'] !== SERVICE_TYPE_DRIVING_LICENSE) {
                    ?>
                    <div class="form-group">
                      <label for="inputBatchNumber">รุ่นที่:</label>
                      <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-hashtag"></i>
                                                    </span>
                        <input type="number" class="form-control"
                               id="inputBatchNumber"
                               value="<?= (!empty($course) ? $course['batch_number'] : ''); ?>"
                               placeholder="กรอกเลขรุ่น" required
                               oninvalid="this.setCustomValidity('กรอกเลขรุ่น')"
                               oninput="this.setCustomValidity('')"
                               disabled>
                      </div>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>

              <!--ค่าสมัครและวันอบรม-->
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="inputTraineeLimit">จำนวนผู้เข้าอบรมที่รับ (คน):</label>
                    <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-users"></i>
                                            </span>
                      <input type="number" class="form-control"
                             id="inputTraineeLimit"
                             value="<?= (!empty($course) ? $course['trainee_limit'] : ''); ?>"
                             placeholder="กรอกจำนวนผู้เข้าอบรมที่รับได้" required
                             oninvalid="this.setCustomValidity('กรอกจำนวนผู้เข้าอบรมที่รับได้')"
                             oninput="this.setCustomValidity('')"
                             disabled>
                    </div>
                  </div>
                </div>
                <?php
                if ($course['service_type'] === SERVICE_TYPE_TRAINING || $course['service_type'] === SERVICE_TYPE_SOCIAL) {
                  ?>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="inputApplicationFee">ค่าสมัคร (บาท):</label>
                      <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <strong>฿</strong>
                                                    </span>
                        <!--แสดงผลอย่างเดียว ไม่ได้ให้กรอก ดังนั้นกำหนด type เป็น text แทนที่จะเป็น number จะได้แสดง comma ได้-->
                        <input type="text" class="form-control"
                               id="inputApplicationFee"
                               value="<?= (!empty($course) ? number_format($course['application_fee']) : ''); ?>"
                               placeholder="กรอกค่าสมัคร" required
                               oninvalid="this.setCustomValidity('กรอกค่าสมัคร')"
                               oninput="this.setCustomValidity('')"
                               disabled>
                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="inputBeginDate">วันอบรมวันแรก:</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <?php
                      if (!empty($course)) {
                        $beginDatePart = explode('-', $course['begin_date']);
                        $year = $beginDatePart[0];
                        $month = $beginDatePart[1];
                        $day = $beginDatePart[2];
                        $beginDate = "$day/$month/$year";
                      }
                      ?>
                      <input type="text" class="form-control pull-right"
                             id="inputBeginDate"
                             value="<?= (!empty($course) ? $beginDate : ''); ?>"
                             placeholder="เลือกวันอบรมวันแรก" required
                             oninvalid="this.setCustomValidity('เลือกวันอบรมวันแรก')"
                             oninput="this.setCustomValidity('')"
                             disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="inputEndDate">วันอบรมวันสุดท้าย:</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <?php
                      if (!empty($course)) {
                        $endDatePart = explode('-', $course['end_date']);
                        $year = $endDatePart[0];
                        $month = $endDatePart[1];
                        $day = $endDatePart[2];
                        $endDate = "$day/$month/$year";
                      }
                      ?>
                      <input type="text" class="form-control pull-right"
                             id="inputEndDate"
                             value="<?= (!empty($course) ? $endDate : ''); ?>"
                             placeholder="เลือกวันอบรมวันสุดท้าย" required
                             oninvalid="this.setCustomValidity('เลือกวันอบรมวันสุดท้าย')"
                             oninput="this.setCustomValidity('')"
                             disabled>
                    </div>
                  </div>
                </div>
              </div>

              <!--สถานที่อบรม-->
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label for="inputPlace">สถานที่อบรม:</label>
                    <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-map-marker"></i>
                                            </span>
                      <input type="text" class="form-control"
                             id="inputPlace"
                             value="<?= (!empty($course) ? $course['place'] : ''); ?>"
                             placeholder="กรอกสถานที่อบรม" required
                             oninvalid="this.setCustomValidity('กรอกสถานที่อบรม')"
                             oninput="this.setCustomValidity('')"
                             disabled>
                    </div>
                  </div>
                </div>
              </div>

              <!--ผู้รับผิดชอบโครงการ-->
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">
                    <label for="selectResponsibleUser">ผู้รับผิดชอบโครงการ:</label>
                    <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user-secret"></i>
                                                </span>
                      <select id="selectResponsibleUser" class="form-control" required
                              oninvalid="this.setCustomValidity('เลือกผู้รับผิดชอบโครงการ')"
                              oninput="this.setCustomValidity('')"
                              disabled>
                        <option value="" disabled selected>-- เลือกผู้รับผิดชอบโครงการ --</option>
                        <?php
                        foreach ($userList as $u) {
                          $userId = $u['id'];
                          $userFirstName = $u['first_name'];
                          $userLastName = $u['last_name'];
                          $userEmail = $u['email'];
                          $userPhoneOffice = $u['phone_office'];

                          $selected = '';
                          if (!empty($course) && ($course['responsible_user_id'] === $userId)) {
                            $selected = 'selected';
                          }
                          ?>
                          <option value="<?= $userId; ?>" <?= $selected; ?>>
                            <?= "$userFirstName $userLastName  |  $userEmail  |  $userPhoneOffice"; ?>
                          </option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!--วิทยากร-->
              <?php
              if ($course['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
                ?>
                <div class="row">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label for="selectTrainer">วิทยากร:</label>
                      <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </span>
                        <select id="selectTrainer" class="form-control" required
                                name="trainerId" disabled
                                oninvalid="this.setCustomValidity('เลือกวิทยากร')"
                                oninput="this.setCustomValidity('')">
                          <option value="" disabled selected>-- เลือกวิทยากร --</option>
                          <?php
                          foreach ($trainerList as $trainer) {
                            $trainerId = (int)$trainer['id'];
                            $trainerFirstName = $trainer['first_name'];
                            $trainerLastName = $trainer['last_name'];
                            $trainerPid = formatPid($trainer['pid']);
                            $trainerEmail = $trainer['email'];
                            $trainerPhone = $trainer['phone'];

                            $selected = '';
                            if (!empty($course) && ($course['trainer_id'] === $trainerId)) {
                              $selected = 'selected';
                            }
                            ?>
                            <option value="<?php echo $trainerId; ?>" <?php echo $selected; ?>>
                              <?php echo "$trainerFirstName $trainerLastName  |  $trainerPid  |  $trainerEmail  |  $trainerPhone"; ?>
                            </option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
              }
              ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!--ใบสมัคร-->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">ใบสมัคร (แยกรายบุคคล)</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"
                        data-toggle="tooltip" title="ย่อ">
                  <i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <div class="box-body">
              <?php
              getCourseRegistrationDataTable($db, $course['service_type'], $courseId, $course['responsible_user_id']);
              ?>
            </div>
            <!-- /.box-body -->
          </div>

          <!--รายงาน-->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">รายงาน/พิมพ์</h3>

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
                <?php
                if (TRUE /*$course['service_type'] !== SERVICE_TYPE_SOCIAL*/) {
                  ?>
                  <!--ใบสมัครทั้งหมด-->
                  <!--<div class="btn-group" style="margin-right: 6px">
                                            <a target="_blank" href="print_registration_form.php?service_type=<?php /*echo $course['service_type']; */ ?>&course_id=<?php /*echo $courseId; */ ?>"
                                               class="btn btn-default"><i class="fa fa-print"></i>&nbsp;&nbsp;ใบสมัครทั้งหมด</a>
                                        </div>-->
                  <?php
                  $traineeIdListText = '';
                  $i = 0;
                  foreach ($traineeList as $trainee) {
                    $traineeIdListText .= ($i++ == 0 ? '' : ',') . $trainee['id'];
                  }

                  if ($course['service_type'] === SERVICE_TYPE_TRAINING || $course['service_type'] === SERVICE_TYPE_SOCIAL) {
                    ?>
                    <div class="btn-group" style="margin-right: 6px">
                      <a target="_blank" href="print_ac_registration_form.php?service_type=<?= $course['service_type']; ?>&trainee_id=<?= $traineeIdListText; ?>"
                         class="btn btn-default"><i class="fa fa-print"></i>&nbsp;&nbsp;ใบสมัครทั้งหมด</a>
                    </div>
                    <div class="btn-group" style="margin-right: 6px">
                      <a target="_blank" href="javascript:void(0)"
                         onClick="onClickWordCertificate()"
                         class="btn btn-default"><i class="fa fa-file-word-o"></i>&nbsp;&nbsp;ใบรับรองการผ่านการอบรม</a>
                    </div>
                    <?php
                  } else if ($course['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
                    ?>
                    <div class="btn-group" style="margin-right: 6px">
                      <a target="_blank" href="print_dl_registration_form.php?trainee_id=<?= $traineeIdListText; ?>&thai=1"
                         class="btn btn-default"><i class="fa fa-print"></i>&nbsp;&nbsp;ใบสมัครทั้งหมด</a>
                    </div>
                    <?php
                  }
                }
                ?>

                <!--สรุปผลผู้สมัคร-->
                <!--<div class="btn-group">
                    <button type="button" class="btn btn-default"><i class="fa fa-download"></i>&nbsp;&nbsp;สรุปผลผู้สมัคร</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                        <li><a href="#"><i class="fa fa-file-word-o"></i> Word</a></li>
                    </ul>
                </div>&nbsp;&nbsp;-->

                <!--ทำเนียบผู้เข้ารับการอบรม-->
                <div class="btn-group" style="margin-right: 6px">
                  <button type="button" class="btn btn-default">ทำเนียบผู้เข้ารับการอบรม</button>
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a target="_blank" href="excel_class_directory.php?course_id=<?= $courseId; ?>"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                    <li><a target="_blank" href="print_trainee_list.php?course_id=<?= $courseId; ?>"><i class="fa fa-print"></i> พิมพ์</a></li>
                  </ul>
                </div>

                <?php
                if ($course['service_type'] === SERVICE_TYPE_TRAINING) {
                  ?>
                  <!--สรุปผู้เข้ารับการอบรม-->
                  <div class="btn-group" style="margin-right: 6px">
                    <a target="_blank" href="excel_trainee_list.php?course_id=<?= $courseId; ?>"
                       class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;สรุปผู้เข้ารับการอบรม</a>
                  </div>
                  <!--ข้อมูลใบเซ็นชื่อ-->
                  <div class="btn-group" style="margin-right: 6px">
                    <a target="_blank" href="excel_trainee_list_all.php?course_id=<?= $courseId; ?>"
                       class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;ข้อมูลใบเซ็นชื่อ</a>
                  </div>
                  <?php
                } else if ($course['service_type'] === SERVICE_TYPE_SOCIAL) {
                  ?>
                  <!--ข้อมูลใบเซ็นชื่อ-->
                  <div class="btn-group" style="margin-right: 6px">
                    <a target="_blank" href="excel_trainee_list_all.php?course_id=<?= $courseId; ?>"
                       class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;ข้อมูลใบเซ็นชื่อ</a>
                  </div>
                  <?php
                } else if ($course['service_type'] === SERVICE_TYPE_DRIVING_LICENSE) {
                  ?>
                  <!--ข้อมูลใบเซ็นชื่อ-->
                  <div class="btn-group" style="margin-right: 6px">
                    <button type="button" class="btn btn-default">ข้อมูลใบเซ็นชื่อ</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a target="_blank" href="excel_dl_trainee_list_all.php?course_id=<?= $courseId; ?>"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                      <li><a target="_blank" href="print_dl_trainee_list_all.php?course_id=<?= $courseId; ?>"><i class="fa fa-print"></i> พิมพ์</a></li>
                      <!--<li class="divider"></li>
                      <li><a href="#">Separated link</a></li>-->
                    </ul>
                  </div>

                  <!--แบบฟอร์มเปล่า-->
                  <div class="btn-group" style="margin-right: 6px">
                    <button type="button" class="btn btn-default">แบบฟอร์มเปล่า</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                      <li><a target="_blank" href="print_dl_registration_form.php"><i class="fa fa-print"></i> ใบสมัคร</a></li>
                      <li><a target="_blank" href="ใบสมัคร.docx"><i class="fa fa-file-word-o"></i> ใบสมัคร</a></li>
                      <li><a target="_blank" href="ใบบันทึกผลการอบรม.doc"><i class="fa fa-file-word-o"></i> ใบบันทึกผลการอบรม</a></li>
                      <li><a target="_blank" href="ใบรับรองการผ่านการอบรม.doc"><i class="fa fa-file-word-o"></i> ใบรับรองการผ่านการอบรม</a></li>
                    </ul>
                  </div>

                  <!--<div class="btn-group" style="margin-right: 6px">
                                            <a target="_blank" href="print_dl_trainee_list_all.php?course_id=<?/*= $courseId; */ ?>"
                                               class="btn btn-default"><i class="fa fa-print"></i>&nbsp;&nbsp;ข้อมูลใบเซ็นชื่อ</a>
                                        </div>-->
                  <?php
                }
                ?>

                <?php
                if (!($course['service_type'] === SERVICE_TYPE_SOCIAL && $course['application_fee'] === 0)) {
                  ?>
                  <!--สรุปยอดเงินหลักสูตร-->
                  <div class="btn-group" style="margin-right: 6px">
                    <a target="_blank" href="excel_course_summary.php?course_id=<?= $courseId; ?>"
                       class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;สรุปยอดเงินหลักสูตร</a>
                  </div>
                  <?php
                }
                ?>

                <!--<div class="btn-group">
                                        <button type="button" class="btn btn-default"><i class="fa fa-download"></i>&nbsp;&nbsp;ทำเนียบผู้เข้ารับการอบรม</button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a target="_blank" href="print_trainee_list.php?service_type=<? /*= $course['service_type']; */ ?>&course_id=<? /*= $courseId; */ ?>"><i class="fa fa-print"></i> Print</a></li>
                                            <li><a href="#"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                            <li><a href="#"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                                            <li><a href="#"><i class="fa fa-file-word-o"></i> Word</a></li>
                                        </ul>
                                    </div>-->

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
  const KEY_CERTIFICATE_FONT_SIZE = 'certificate_font_size';
  const DEFAULT_CERTIFICATE_FONT_SIZE = 24;

  $(document).ready(function () {
    $('#tableCourse').DataTable({
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

    lightbox.option({
      fadeDuration: 500,
      imageFadeDuration: 500,
      resizeDuration: 500,
    });

    $('#formCertificateSettings').on('submit', (event) => {
      //event.preventDefault();
      const fontSize = parseInt($('#formCertificateSettings #inputFontSize').val());
      window.localStorage.setItem(KEY_CERTIFICATE_FONT_SIZE, fontSize);
    });
  });

  $(() => {
    //Date picker
    $('#inputBeginDate').datepicker({
      format: 'dd/mm/yyyy',
      orientation: 'bottom',
      autoclose: true
    }).on('changeDate', e => {
      e.target.setCustomValidity('');
    });

    $('#inputEndDate').datepicker({
      format: 'dd/mm/yyyy',
      orientation: 'bottom',
      autoclose: true
    }).on('changeDate', e => {
      e.target.setCustomValidity('');
    });
  });

  function doPrintRegForm() {

  }

  function doPrintReceipt() {
    //$('#formEditCourseMaster #inputCourseMasterId').val(courseMasterId);
    //$('#formEditCourseMaster #inputCourseMasterTitle').val(courseMasterTitle);
    //$('#printReceiptModal').modal('show');
  }

  function doPrintCertificate() {

  }

  function onClickWordCertificate() {
    let fontSize = window.localStorage.getItem(KEY_CERTIFICATE_FONT_SIZE);
    if (fontSize == null) {
      fontSize = DEFAULT_CERTIFICATE_FONT_SIZE;
    }
    $('#formCertificateSettings #inputFontSize').val(fontSize);
    $('#certificateSettingsModal').modal('show');
    //window.location.href = "word_ac_certificate.php?service_type=<?= $course['service_type']; ?>&course_id=<?= $courseId; ?>";
  }

</script>

<?php require_once('../include/foot.inc'); ?>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!--jQuery Form Plugin-->
<script src="../dist/js/jquery.form.js"></script>
<!--Lightbox-->
<script src="../dist/lightbox/js/lightbox.js"></script>
</body>
</html>

<?php
require_once '../include/foot_php.inc';
?>
