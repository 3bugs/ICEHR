<?php
require_once '../include/head_php.inc';

$courseId = $_POST['courseId'];
$course = array();
if (isset($courseId)) {
    $courseId = $db->real_escape_string($courseId);
    $sql = "SELECT * FROM course WHERE id=$courseId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $course['course_master_id'] = (int)$row['course_master_id'];
            $course['batch_number'] = (int)$row['batch_number'];
            $course['details'] = $row['details'];
            $course['trainee_limit'] = (int)$row['trainee_limit'];
            $course['application_fee'] = (int)$row['application_fee'];
            $course['place'] = $row['place'];
            $course['begin_date'] = $row['begin_date'];
            $course['end_date'] = $row['end_date'];
            $course['responsible_user_id'] = (int)$row['responsible_user_id'];
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
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
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
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
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

$sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.created_at, ct.register_status, ct.course_registration_id,
               cr.course_id, cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email
            FROM course_registration cr  
                INNER JOIN course c
                    ON cr.course_id = c.id
                INNER JOIN course_trainee ct
                    ON ct.course_registration_id = cr.id
            WHERE c.id = $courseId
            ORDER BY ct.id DESC";

if ($result = $db->query($sql)) {
    $traineeList = array();
    while ($row = $result->fetch_assoc()) {
        $trainee = array();
        $trainee['id'] = (int)$row['id'];
        $trainee['form_number'] = $row['form_number'];
        $trainee['title'] = $row['title'];
        $trainee['first_name'] = $row['first_name'];
        $trainee['last_name'] = $row['last_name'];
        $trainee['phone'] = $row['phone'];
        $trainee['email'] = $row['email'];
        $trainee['created_at'] = $row['created_at'];
        $trainee['register_status'] = $row['register_status'];
        $trainee['coordinator'] = array(
            'title' => $row['coordinator_title'],
            'first_name' => $row['coordinator_first_name'],
            'last_name' => $row['coordinator_last_name'],
            'phone' => $row['coordinator_phone'],
            'email' => $row['coordinator_email']
        );
        $trainee['course_id'] = (int)$row['course_id'];

        array_push($traineeList, $trainee);
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

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ข้อมูลการสมัคร
                    <small>บริการฝึกอบรม</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

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
                ?>

                <!--Widgets-->
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-pencil-square-o"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">สมัคร / รับได้</span>
                                <span class="info-box-number"><?php echo $registerStatusStart . ' / ' . $course['trainee_limit']; ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">ชำระเงินและตรวจสอบแล้ว</span>
                                <span class="info-box-number"><?php echo $registerStatusComplete; ?></span>
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
                                <span class="info-box-number"><?php echo $registerStatusWaitApprove; ?></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-times-circle"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">ยกเลิก</span>
                                <span class="info-box-number"><?php echo $registerStatusCancel; ?></span>
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
                                                        <option value="<?php echo $courseMasterId; ?>" <?php echo $selected ?>>
                                                            <?php echo $courseMasterTitle ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="inputBatchNumber">รุ่นที่:</label>
                                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-hashtag"></i>
                                            </span>
                                                <input type="number" class="form-control"
                                                       id="inputBatchNumber"
                                                       value="<?php echo(!empty($course) ? $course['batch_number'] : ''); ?>"
                                                       placeholder="กรอกเลขรุ่น" required
                                                       oninvalid="this.setCustomValidity('กรอกเลขรุ่น')"
                                                       oninput="this.setCustomValidity('')"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--ค่าสมัครและวันอบรม-->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="inputTraineeLimit">รับผู้เข้าอบรมจำนวน (ท่าน):</label>
                                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-users"></i>
                                            </span>
                                                <input type="number" class="form-control"
                                                       id="inputTraineeLimit"
                                                       value="<?php echo(!empty($course) ? $course['trainee_limit'] : ''); ?>"
                                                       placeholder="กรอกจำนวนผู้เข้าอบรมที่รับได้" required
                                                       oninvalid="this.setCustomValidity('กรอกจำนวนผู้เข้าอบรมที่รับได้')"
                                                       oninput="this.setCustomValidity('')"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="inputApplicationFee">ค่าสมัคร (บาท):</label>
                                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-dollar"></i>
                                            </span>
                                                <input type="number" class="form-control"
                                                       id="inputApplicationFee"
                                                       value="<?php echo(!empty($course) ? $course['application_fee'] : ''); ?>"
                                                       placeholder="กรอกค่าสมัคร" required
                                                       oninvalid="this.setCustomValidity('กรอกค่าสมัคร')"
                                                       oninput="this.setCustomValidity('')"
                                                       disabled>
                                            </div>
                                        </div>
                                    </div>
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
                                                       value="<?php echo(!empty($course) ? $beginDate : ''); ?>"
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
                                                       value="<?php echo(!empty($course) ? $endDate : ''); ?>"
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="inputPlace">สถานที่อบรม:</label>
                                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-map-marker"></i>
                                            </span>
                                                <input type="text" class="form-control"
                                                       id="inputPlace"
                                                       value="<?php echo(!empty($course) ? $course['place'] : ''); ?>"
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
                                    <div class="col-md-12">
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
                                                        <option value="<?php echo $userId; ?>" <?php echo $selected; ?>>
                                                            <?php echo "$userFirstName $userLastName  |  $userEmail  |  $userPhoneOffice"; ?>
                                                        </option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                <table id="tableCourse" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: center">เลขที่</th>
                                        <!--<th style="width: 50%; text-align: center">หลักสูตรที่สมัคร</th>-->
                                        <!--<th style="width: 10%; text-align: center">จำนวน</th>-->
                                        <!--<th style="width: 15%; text-align: center">ผู้กรอกใบสมัคร</th>-->
                                        <th style="width: 30%; text-align: center">ผู้สมัคร</th>
                                        <th style="width: 30%; text-align: center">ผู้ประสานงาน</th>
                                        <th style="width: 15%; text-align: center">วัน/เวลาที่สมัคร</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center" nowrap>พิมพ์</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($traineeList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="7" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($traineeList as $trainee) {
                                            $traineeId = $trainee['id'];
                                            $formNumber = $trainee['form_number'];
                                            $traineeDetails = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $trainee['title'], $trainee['first_name'], $trainee['last_name'],
                                                $trainee['phone'], $trainee['email'], $trainee['email']
                                            );
                                            $coordinatorDetails = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $trainee['coordinator']['title'], $trainee['coordinator']['first_name'], $trainee['coordinator']['last_name'],
                                                $trainee['coordinator']['phone'], $trainee['coordinator']['email'], $trainee['coordinator']['email']
                                            );
                                            $createdAt = $trainee['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                                            $registerStatus = $trainee['register_status'];
                                            ?>

                                            <tr>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $formNumber; ?></td>
                                                <td style="vertical-align: middle"><?php echo $traineeDetails; ?></td>
                                                <td style="vertical-align: middle"><?php echo ($trainee['coordinator']['first_name'] == null ? '&nbsp;' : $coordinatorDetails); ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <form method="post" action="course_add_edit.php">
                                                        <input type="hidden" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                                        <?php
                                                        switch ($registerStatus) {
                                                            case 'start':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-warning" style="width: 90px;">
                                                                    รอชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'wait-approve':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-info" style="width: 90px;">
                                                                    แจ้งชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'complete':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-success" style="width: 90px;">
                                                                    สมบูรณ์
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'cancel':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-danger" style="width: 90px;">
                                                                    ยกเลิก
                                                                </button>
                                                                <?php
                                                                break;
                                                        }
                                                        ?>
                                                    </form>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right" role="menu">
                                                            <li><a href="javascript:void(0)" onClick="doPrintRegForm()"><i class="fa fa-print"></i>ใบสมัคร</a></li>
                                                            <li><a href="javascript:void(0)" onClick="doPrintReceipt()"><i class="fa fa-print"></i>ใบเสร็จรับเงิน</a></li>
                                                            <li><a href="javascript:void(0)" onClick="doPrintCertificate()"><i class="fa fa-print"></i>ใบรับรองการผ่านอบรม</a></li>
                                                            <!--<li class="divider"></li>-->
                                                        </ul>
                                                    </div>
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

                        <!--รายงาน-->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">รายงาน</h3>

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
                                    <!--สรุปผลผู้สมัคร-->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default"><i class="fa fa-download"></i>&nbsp;&nbsp;สรุปผลผู้สมัคร</button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                            <li><a href="#"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                                            <li><a href="#"><i class="fa fa-file-word-o"></i> Word</a></li>
                                            <!--<li class="divider"></li>-->
                                        </ul>
                                    </div>&nbsp;&nbsp;
                                    <!--ทำเนียบผู้เข้ารับการอบรม-->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default"><i class="fa fa-download"></i>&nbsp;&nbsp;ทำเนียบผู้เข้ารับการอบรม</button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                            <li><a href="#"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                                            <li><a href="#"><i class="fa fa-file-word-o"></i> Word</a></li>
                                            <!--<li class="divider"></li>-->
                                        </ul>
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