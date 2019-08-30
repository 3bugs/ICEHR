<?php
require_once '../include/head_php.inc';

$courseId = $_POST['course_id'];
$serviceType = $_POST['service_type'];

if (!isset($serviceType)) {
    echo '<div style="color: red">ERROR: ไม่ได้ระบุ service type</div>';
    $db->close();
    exit();
} else if (!array_key_exists($serviceType, $serviceTypeText)) {
    echo '<div style="color: red">ERROR: ระบุ service type ไม่ถูกต้อง</div>';
    $db->close();
    exit();
}

$course = array();
if (isset($courseId)) {
    $courseId = $db->real_escape_string($courseId);

    $sql = "SELECT * FROM course WHERE id = $courseId";

    /*$sql = "SELECT c.course_master_id, c.batch_number, c.details, c.trainee_limit, c.application_fee,
                   c.place, c.begin_date, c.end_date, c.responsible_user_id, cm.service_type
            FROM course c 
                INNER JOIN course_master cm 
                    ON c.course_master_id = cm.id 
            WHERE c.id = $courseId";*/

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $course['course_master_id'] = (int)$row['course_master_id'];
            $course['batch_number'] = (int)$row['batch_number'];
            $course['category'] = (int)$row['category'];
            $course['details'] = $row['details'];
            $course['trainee_limit'] = (int)$row['trainee_limit'];
            $course['application_fee'] = (int)$row['application_fee'];
            $course['place'] = $row['place'];
            $course['begin_date'] = $row['begin_date'];
            $course['end_date'] = $row['end_date'];
            $course['responsible_user_id'] = (int)$row['responsible_user_id'];
            //$course['service_type'] = $row['service_type'];
        } else {
            echo 'ไม่พบข้อมูลหลักสูตร';
            $result->close();
            $db->close();
            exit();
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
        $db->close();
        exit();
    }
}

$sql = "SELECT * FROM course_master WHERE service_type = '$serviceType'";
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

$imageList = array();
$pdfList = array();
if (isset($courseId)) {
    $sql = "SELECT * FROM course_asset WHERE course_id = $courseId";
    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $asset = array();
            $asset['id'] = (int)$row['id'];
            $asset['title'] = $row['title'];
            $asset['file_name'] = $row['file_name'];
            $asset['type'] = $row['type'];
            $asset['created_at'] = $row['created_at'];

            $prefixPosition = strpos($row['file_name'], '-');
            $extensionPosition = strpos($row['file_name'], '.');
            $asset['title'] = substr(
                $row['file_name'],
                $prefixPosition + 1,
                $extensionPosition - ($prefixPosition + 1)
            );

            if ($row['type'] === 'image') {
                array_push($imageList, $asset);
            } else if ($row['type'] === 'pdf') {
                array_push($pdfList, $asset);
            }
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
        $db->close();
        exit();
    }
}

$feeList = array();
if (isset($courseId)) {
    $sql = "SELECT * FROM course_fee WHERE course_id = $courseId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fee = array();
                $fee['id'] = (int)$row['id'];
                $fee['title'] = $row['title'];
                $fee['amount'] = $row['amount'];
                $fee['created_at'] = $row['created_at'];

                array_push($feeList, $fee);
            }
        } else {
            $fee = array();
            $fee['id'] = 0;
            $fee['title'] = '';
            $fee['amount'] = '';
            $fee['created_at'] = '';
            array_push($feeList, $fee);
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
        $db->close();
        exit();
    }
} else {
    $fee = array();
    $fee['id'] = 0;
    $fee['title'] = '';
    $fee['amount'] = '';
    $fee['created_at'] = '';
    array_push($feeList, $fee);
}

/*if (isset($courseId)) {
    $sql = "SELECT id, form_number, status, created_at, coordinator_first_name, coordinator_last_name, 
                   coordinator_email, coordinator_phone 
            FROM course_registration 
            WHERE course_id = $courseId 
            ORDER BY created_at DESC";

    if ($result = $db->query($sql)) {
        $courseRegList = array();
        while ($row = $result->fetch_assoc()) {
            $courseReg = array();
            $courseRegId = (int)$row['id'];
            $courseReg['id'] = $courseRegId;
            $courseReg['form_number'] = $row['form_number'];
            $courseReg['status'] = $row['status'];
            $courseReg['created_at'] = $row['created_at'];

            $courseReg['coordinator'] = array(
                'first_name' => $row['coordinator_first_name'],
                'last_name' => $row['coordinator_last_name'],
                'phone' => $row['coordinator_phone'],
                'email' => $row['coordinator_email'],
            );
            $courseReg['trainee_list'] = array();

            $traineeSql = "SELECT title, first_name, last_name, phone 
                           FROM course_trainee 
                           WHERE course_registration_id = $courseRegId";

            if ($traineeResult = $db->query($traineeSql)) {
                while ($traineeRow = $traineeResult->fetch_assoc()) {
                    $trainee['title'] = $traineeRow['title'];
                    $trainee['first_name'] = $traineeRow['first_name'];
                    $trainee['last_name'] = $traineeRow['last_name'];
                    $trainee['phone'] = $traineeRow['phone'];

                    array_push($courseReg['trainee_list'], $trainee);
                }
                $traineeResult->close();
            } else {
                echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
                $result->close();
                $db->close();
                exit();
            }

            array_push($courseRegList, $courseReg);
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
        $db->close();
        exit();
    }
}*/

?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <!--Lightbox-->
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">

        <!-- Material Design Bootstrap -->
        <!--<link href="../lib/mdb/mdb.min.css" rel="stylesheet">-->
        <!-- Material Design Bootstrap -->
        <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.2/css/mdb.min.css" rel="stylesheet">-->

        <style>
            input[type="file"] {
                margin-bottom: 15px;
                /*display: none;*/
            }

            .custom-file-upload {
                border: 1px solid #ccc;
                display: inline-block;
                padding: 6px 12px;
                cursor: pointer;
            }

            .custom-file-upload:hover {
                background: #f4f4f4;
            }

            .nav-tabs {
                background-color: #f8f8f8;
            }

            .tab-content {
                /*background-color:#ccc;
                color:#00ff00;
                padding:5px*/
            }

            .nav-tabs > li > a {
                /*border: medium none;*/
            }

            .nav-tabs > li > a:hover {
                /*background-color: #ccc !important;
                border: medium none;
                border-radius: 0;
                color:#fff;*/
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
                    <?php echo(isset($courseId) ? 'แก้ไข' : 'เพิ่ม'); ?>หลักสูตร
                    <small><?php echo $serviceTypeText[$serviceType]; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddCourse"
                      action="../api/api.php/<?php echo(isset($courseId) ? 'update_course' : 'add_course'); ?>"
                      method="post">

                    <input type="hidden" name="courseId"
                           value="<?php echo $courseId; ?>"/>

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
                                                            name="courseMasterId"
                                                            oninvalid="this.setCustomValidity('เลือกชื่อหลักสูตร')"
                                                            oninput="this.setCustomValidity('')">
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
                                                            <option value="<?php echo $courseMasterId; ?>" <?php echo $selected; ?>>
                                                                <?php echo $courseMasterTitle; ?>
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
                                            if ($serviceType !== SERVICE_TYPE_DRIVING_LICENSE) {
                                                ?>
                                                <div class="form-group">
                                                    <label for="inputBatchNumber">รุ่นที่:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-hashtag"></i>
                                                        </span>
                                                        <input type="number" class="form-control"
                                                               id="inputBatchNumber"
                                                               name="batchNumber"
                                                               value="<?php echo(!empty($course) ? $course['batch_number'] : ''); ?>"
                                                               placeholder="กรอกเลขรุ่น" required
                                                               oninvalid="this.setCustomValidity('กรอกเลขรุ่น')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!--ประเภทหลักสูตร-->
                                    <?php
                                    /*if ($serviceType === SERVICE_TYPE_TRAINING) {
                                        */ ?><!--
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="selectCourseCategory">หมวดหมู่หลักสูตร:</label>
                                                    <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                        <select id="selectCourseCategory" class="form-control" required
                                                                name="courseCategory"
                                                                oninvalid="this.setCustomValidity('เลือกหมวดหมู่หลักสูตร')"
                                                                oninput="this.setCustomValidity('')">
                                                            <option value="" disabled selected>-- เลือกหมวดหมู่หลักสูตร --</option>
                                                            <?php
                                    /*                                                            foreach ($trainingCourseCategoryList as $trainingCourseCategory) {
                                                                                                    $selected = '';
                                                                                                    if (!empty($course) && ($course['category'] === $trainingCourseCategory['id'])) {
                                                                                                        $selected = 'selected';
                                                                                                    }
                                                                                                    */ ?>
                                                                <option value="<?php /*echo $trainingCourseCategory['id']; */ ?> <?php /*echo $selected; */ ?>">
                                                                    <?php /*echo $trainingCourseCategory['title']; */ ?>
                                                                </option>
                                                                <?php
                                    /*                                                            }
                                                                                                */ ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        --><?php
                                    /*                                    }*/
                                    ?>

                                    <!--ค่าสมัครและวันอบรม-->
                                    <div class="row" style="margin-top: 20px">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputTraineeLimit">จำนวนผู้เข้าอบรมที่รับ (คน):</label>
                                                <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-users"></i>
                                            </span>
                                                    <input type="number" class="form-control"
                                                           id="inputTraineeLimit"
                                                           name="traineeLimit"
                                                           value="<?php echo(!empty($course) ? $course['trainee_limit'] : ''); ?>"
                                                           placeholder="กรอกจำนวนผู้เข้าอบรมที่รับได้" required
                                                           oninvalid="this.setCustomValidity('กรอกจำนวนผู้เข้าอบรมที่รับได้')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                            ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="inputApplicationFee">ค่าสมัคร (บาท):</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <strong>฿</strong>
                                                        </span>
                                                        <input type="number" class="form-control"
                                                               id="inputApplicationFee"
                                                               name="applicationFee"
                                                               value="<?php echo(!empty($course) ? $course['application_fee'] : ''); ?>"
                                                               min="0"
                                                               placeholder="กรอกค่าสมัคร" required
                                                               oninvalid="this.setCustomValidity('กรอกค่าสมัคร')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                    <?php
                                                    if ($serviceType === SERVICE_TYPE_SOCIAL) {
                                                        ?>
                                                        <div style="text-align: center; color: orangered">หลักสูตรฟรี ให้กรอก 0</div>
                                                        <?php
                                                    }
                                                    ?>
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
                                                    <input type="text" class="form-control pull-right"
                                                           id="inputBeginDate"
                                                           name="beginDate" autocomplete="off"
                                                           placeholder="เลือกวันอบรมวันแรก" required
                                                           oninvalid="this.setCustomValidity('เลือกวันอบรมวันแรก')"
                                                           oninput="this.setCustomValidity('')">
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
                                                    <input type="text" class="form-control pull-right"
                                                           id="inputEndDate"
                                                           name="endDate" autocomplete="off"
                                                           placeholder="เลือกวันอบรมวันสุดท้าย" required
                                                           oninvalid="this.setCustomValidity('เลือกวันอบรมวันสุดท้าย')"
                                                           oninput="this.setCustomValidity('')">
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
                                                           name="place"
                                                           value="<?php echo(!empty($course) ? $course['place'] : ''); ?>"
                                                           placeholder="กรอกสถานที่อบรม" required
                                                           oninvalid="this.setCustomValidity('กรอกสถานที่อบรม')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                            ?>
                                            <div class="col-md-3">
                                                <?php
                                                if ($serviceType === SERVICE_TYPE_TRAINING) {
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="selectPlaceType">ประเภท:</label>
                                                        <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-map-marker"></i>
                                                        </span>
                                                            <select id="selectPlaceType" class="form-control" required
                                                                    name="placeType"
                                                                    oninvalid="this.setCustomValidity('เลือกประเภทสถานที่')"
                                                                    oninput="this.setCustomValidity('')">
                                                                <option value="0" disabled selected>-- เลือกประเภทสถานที่ --</option>
                                                                <option value="1">กรุงเทพมหานคร</option>
                                                                <option value="2">ภูมิภาค</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } else if ($serviceType === SERVICE_TYPE_SOCIAL) {
                                                    ?>
                                                    <div class="form-group">
                                                        <label for="selectPlaceType">วิทยาเขต:</label>
                                                        <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-map-marker"></i>
                                                        </span>
                                                            <select id="selectPlaceType" class="form-control" required
                                                                    name="placeType"
                                                                    oninvalid="this.setCustomValidity('เลือกวิทยาเขต')"
                                                                    oninput="this.setCustomValidity('')">
                                                                <option value="0" disabled selected>-- เลือกวิทยาเขต --</option>
                                                                <option value="1">ท่าพระจันทร์</option>
                                                                <option value="2">ศูนย์รังสิต</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
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
                                                            name="responsibleUserId"
                                                            oninvalid="this.setCustomValidity('เลือกผู้รับผิดชอบโครงการ')"
                                                            oninput="this.setCustomValidity('')">
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

                            <!--content editor-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดเพิ่มเติม
                                        <small>หลักการและเหตุผล / วัตถุประสงค์ / เนื้อหาการอบรม</small>
                                    </h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                data-toggle="tooltip" title="ย่อ">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /. tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body pad">
                                <textarea id="editor" rows="20" cols="80"
                                          name="details">
                                    <?php echo(!empty($course) ? $course['details'] : ''); ?>
                                </textarea>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--ตารางราคา-->
                            <?php
                            if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                ?>
                                <div class="box box-warning" id="boxFee">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">ตารางราคา
                                            <small>ราคาเต็ม / Early Bird / ราคาสำหรับบุคลากร ฯลฯ</small>
                                        </h3>
                                        <!-- tools box -->
                                        <div class="pull-right box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                    data-toggle="tooltip" title="ย่อ">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <!-- /. tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body pad">
                                        <?php
                                        if (isset($courseId) && sizeof($feeList) === 1 && $feeList[0]['title'] === '') {
                                            ?>
                                            <div class="callout callout-danger" style="margin-top: 10px">
                                                <p>ยังไม่มีรายการราคา!</p>
                                            </div>
                                            <?php
                                        }
                                        ?>

                                        <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                            <li>รายการราคาที่เพิ่ม/ลบ/แก้ไข จะถูกบันทึกลงฐานข้อมูลจริง หลังจากกดปุ่ม "บันทึก"</li>
                                        </ul>

                                        <table id="tableFee" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: 80%;">ข้อความ</th>
                                                <th style="text-align: center; width: 20%;">ราคา</th>
                                                <th style="text-align: center;">จัดการ</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <?php
                                            foreach ($feeList as $fee) {
                                                ?>
                                                <tr id="trFeeRow">
                                                    <td style="">
                                                        <input type="text" class="form-control"
                                                               id="inputFeeTitle"
                                                               name="feeTitle[]"
                                                               value="<?php echo $fee['title']; ?>"
                                                               placeholder="กรอกข้อความ" required
                                                               oninvalid="this.setCustomValidity('กรอกข้อความสำหรับรายการราคานี้')"
                                                               oninput="this.setCustomValidity('')">
                                                    </td>
                                                    <td style="">
                                                        <input type="number" class="form-control"
                                                               style="text-align: right"
                                                               id="inputFeeAmount"
                                                               name="feeAmount[]"
                                                               value="<?php echo $fee['amount']; ?>"
                                                               placeholder="กรอกราคา">
                                                    </td>
                                                    <td style="">
                                                        <button type="button" class="btn btn-danger removeFeeRow"
                                                                style="margin-left: 6px; margin-right: 6px"
                                                                onClick="onClickDeleteFee(this)">
                                                            <span class="fa fa-remove"></span>&nbsp;
                                                            ลบ
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>

                                            </tbody>
                                        </table>

                                        <div class="row" style="margin: 10px">
                                            <div class="col-12" style="text-align: center">
                                                <button type="button" class="btn btn-success"
                                                        id="buttonAddFeeRow">
                                                    <span class="fa fa-plus"></span>&nbsp;
                                                    เพิ่มรายการราคา
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box -->
                                <?php
                            }
                            ?>

                            <!--รูปภาพ-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รูปภาพ
                                        <!--<small>อัพโหลดรูปภาพ</small>-->
                                    </h3>
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                data-toggle="tooltip" title="ย่อ">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /. tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body pad" style="background: #f8f8f8">
                                    <!--<label for="file-upload" class="custom-file-upload">
                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลดรูปภาพ
                                    </label>-->

                                    <!-- Custom Tabs -->
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <?php
                                            if (isset($courseId)) {
                                                ?>
                                                <li class="active"><a href="#image_tab_1" data-toggle="tab">รูปภาพปัจจุบัน <strong>(<?php echo sizeof($imageList); ?>)</strong></a></li>
                                                <?php
                                            }
                                            ?>
                                            <li <?php echo(!isset($courseId) ? 'class="active"' : ''); ?>><a href="#image_tab_2" data-toggle="tab">เพิ่มรูปภาพ</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <?php
                                            if (isset($courseId)) {
                                                ?>
                                                <!--ตารางรูปภาพ-->
                                                <div class="tab-pane active" id="image_tab_1">
                                                    <?php
                                                    if (sizeof($imageList) === 0) {
                                                        ?>
                                                        <div class="callout callout-danger" style="margin-top: 10px">
                                                            <p>ยังไม่มีรูปภาพ!</p>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <table id="tableImage" class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <!--<th style="text-align: center; width: 40%;">ชื่อ</th>-->
                                                            <th style="text-align: center; width: 80%;">รูปภาพ</th>
                                                            <th style="text-align: center; width: 20%;">อัพโหลดเมื่อ</th>
                                                            <th style="text-align: center;">จัดการ</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach ($imageList as $image) {
                                                            $createdAt = $image['created_at'];
                                                            $dateTimePart = explode(' ', $createdAt);
                                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                                            $timePart = explode(':', $dateTimePart[1]);
                                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                                            $displayDateTime = "$displayDate<br>$displayTime";
                                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                                            ?>
                                                            <tr>
                                                                <!--<td><?php /*echo $image['title']; */ ?></td>-->
                                                                <td style="text-align: center">
                                                                    <a href="<?php echo(UPLOAD_DIR_COURSE_ASSETS . $image['file_name']); ?>" data-lightbox="courseImage">
                                                                        <img src="<?php echo(UPLOAD_DIR_COURSE_ASSETS . $image['file_name']); ?>"
                                                                             height="120px">
                                                                    </a>
                                                                </td>
                                                                <td style="text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger"
                                                                            style="margin-left: 6px; margin-right: 6px"
                                                                            onClick="onClickDeleteAsset(this, <?php echo $image['id']; ?>, 'รูปภาพ')">
                                                                        <span class="fa fa-remove"></span>&nbsp;
                                                                        ลบ
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!--เพิ่มรูปภาพใหม่-->
                                            <div class="tab-pane <?php echo(!isset($courseId) ? 'active' : ''); ?>" id="image_tab_2">
                                                <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                    <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                    <li>สามารถเลือกได้หลายไฟล์พร้อมกัน</li>
                                                    <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                </ul>
                                                <input id="image-upload" type="file" accept="image/*" multiple
                                                       name="imageFiles[]" style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                                    <?= !isset($courseId) ? 'required' : '' ?>
                                                       oninvalid="this.setCustomValidity('เลือกอย่างน้อย 1 รูปภาพ')"
                                                       oninput="this.setCustomValidity('')"/>
                                                <div id="image-upload-preview"
                                                     style="background: #efffd1; padding: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.box -->

                            <!--เอกสารดาวน์โหลด-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">เอกสารดาวน์โหลด (PDF)
                                        <!--<small></small>-->
                                    </h3>

                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                                data-toggle="tooltip" title="ย่อ">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /. tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body pad" style="background: #f8f8f8">
                                    <!--<label for="file-upload" class="custom-file-upload">
                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลด PDF
                                    </label>-->

                                    <!-- Custom Tabs -->
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <?php
                                            if (isset($courseId)) {
                                                ?>
                                                <li class="active"><a href="#pdf_tab_1" data-toggle="tab">PDF ปัจจุบัน <strong>(<?php echo sizeof($pdfList); ?>)</strong></a></li>
                                                <?php
                                            }
                                            ?>
                                            <li <?php echo(!isset($courseId) ? 'class="active"' : ''); ?>><a href="#pdf_tab_2" data-toggle="tab">เพิ่ม PDF</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <?php
                                            if (isset($courseId)) {
                                                ?>
                                                <!--ตาราง PDF-->
                                                <div class="tab-pane active" id="pdf_tab_1">
                                                    <?php
                                                    if (sizeof($pdfList) === 0) {
                                                        ?>
                                                        <div class="callout callout-danger" style="margin-top: 10px">
                                                            <p>ยังไม่มีเอกสารดาวน์โหลด!</p>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <table id="tablePdf" class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <!--<th style="text-align: center; width: 40%;">ชื่อ</th>-->
                                                            <th style="text-align: center; width: 50%;">ชื่อ</th>
                                                            <th style="text-align: center; width: 30%;">PDF</th>
                                                            <th style="text-align: center; width: 20%;">อัพโหลดเมื่อ</th>
                                                            <th style="text-align: center;">จัดการ</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach ($pdfList as $pdf) {
                                                            /*$fileName = $pdf['file_name'];
                                                            $prefixPosition = strpos($fileName, '-');
                                                            $extensionPosition = strpos($fileName, '.');
                                                            $title = substr($fileName, $prefixPosition + 1, $extensionPosition - ($prefixPosition + 1));*/

                                                            $createdAt = $pdf['created_at'];
                                                            $dateTimePart = explode(' ', $createdAt);
                                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                                            $timePart = explode(':', $dateTimePart[1]);
                                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                                            $displayDateTime = "$displayDate<br>$displayTime";
                                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                                            ?>
                                                            <tr>
                                                                <!--<td><?php /*echo $image['title']; */ ?></td>-->
                                                                <td>
                                                                    <?php echo $pdf['title']; ?>
                                                                </td>
                                                                <td style="text-align: center; cursor: pointer"
                                                                    onClick="window.open('<?php echo(UPLOAD_DIR_COURSE_ASSETS . $pdf['file_name']); ?>', '_blank')">
                                                                    <a href="javascript:void(0)">
                                                                        <span style="font-size: 25px"><i class="fa fa-file-pdf-o"></i></span>
                                                                    </a>
                                                                </td>
                                                                <td style="text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger"
                                                                            style="margin-left: 6px; margin-right: 6px"
                                                                            onClick="onClickDeleteAsset(this, <?php echo $pdf['id']; ?>, 'เอกสาร PDF ')">
                                                                        <span class="fa fa-remove"></span>&nbsp;
                                                                        ลบ
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!--เพิ่ม PDF ใหม่-->
                                            <div class="tab-pane <?php echo(!isset($courseId) ? 'active' : ''); ?>" id="pdf_tab_2">
                                                <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                    <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                    <li>สามารถเลือกได้หลายไฟล์พร้อมกัน</li>
                                                    <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                </ul>
                                                <input id="pdf-upload" type="file" accept="application/pdf" multiple
                                                       name="pdfFiles[]" style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                <ul id="pdf-upload-preview"
                                                    style="background: #efffd1; padding-top: 10px; padding-bottom: 10px"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--ปุ่ม "บันทึก"-->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                    </div>
                                    <button id="buttonSave" type="submit"
                                            class="btn btn-info">
                                        <span class="fa fa-save"></span>&nbsp;
                                        บันทึก
                                    </button>
                                </div>
                            </div>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </form>

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php require_once('../include/footer.inc'); ?>
    </div>
    <!-- ./wrapper -->

    <script>
        $(() => {
            <?php
            if (!empty($course)) {
                $beginDatePart = explode('-', $course['begin_date']);
                $year = $beginDatePart[0];
                $month = $beginDatePart[1];
                $day = $beginDatePart[2];
                $beginDate = "$day/$month/$year";

                $endDatePart = explode('-', $course['end_date']);
                $year = $endDatePart[0];
                $month = $endDatePart[1];
                $day = $endDatePart[2];
                $endDate = "$day/$month/$year";
            }
            ?>

            //Date picker
            let inputBeginDate = $('#inputBeginDate');
            inputBeginDate.datepicker({
                language: 'th',
                thaiyear: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom',
                autoclose: true
            }).on('changeDate', e => {
                e.target.setCustomValidity('');
            }).datepicker('update', '<?php echo(!empty($course) ? $beginDate : ''); ?>');

            $('#inputEndDate').datepicker({
                language: 'th',
                thaiyear: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom',
                autoclose: true
            }).on('changeDate', e => {
                e.target.setCustomValidity('');
            }).datepicker('update', '<?php echo(!empty($course) ? $endDate : ''); ?>');

            CKEDITOR.replace('editor');
        });

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });

            $('#buttonAddFeeRow').on('click', function () {
                const feeRow = $('#trFeeRow');
                const newRow = feeRow.clone();
                newRow.find('input#inputFeeTitle').val('');
                newRow.find('input#inputFeeAmount').val('');
                feeRow.parent().append(newRow);
            });

            $('#tableFee_').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
                //order: [[1, 'desc']],
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
            $('#tableImage').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
                order: [[1, 'desc']],
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
            $('#tablePdf').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
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
                }
            });

            $('#formAddCourse #divLoading').hide();

            $('#formAddCourse').submit(event => {
                event.preventDefault();
                doAddEditCourse();
            });

            /*$("#image-upload").change(function() {
                readURL(this);
            });*/

            const inputApplicationFee = $('#inputApplicationFee');

            inputApplicationFee.keyup(function () {
                let fee = parseInt($(this).val());
                if (fee === 0) {
                    setFeeTableEnabled(false);
                } else {
                    setFeeTableEnabled(true);
                }
            });
            inputApplicationFee.change(function () {
                let fee = parseInt($(this).val());
                if (fee === 0) {
                    setFeeTableEnabled(false);
                } else {
                    setFeeTableEnabled(true);
                }
            });

            <?php
            if ($course['application_fee'] === 0) {
            ?>
            setFeeTableEnabled(false);
            <?php
            }
            ?>
        });

        /*function readURL(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-upload-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }*/

        function setFeeTableEnabled(enabled) {
            const inputFeeTitle = $('#inputFeeTitle');
            const inputFeeAmount = $('#inputFeeAmount');
            const buttonAddFeeRow = $('#buttonAddFeeRow');
            const tableFeeRow = $('#tableFee > tbody > tr');

            if (enabled) {
                //$('#boxFee').show();
                inputFeeTitle.prop('required', true);
                inputFeeTitle.prop('disabled', false);
                inputFeeAmount.prop('disabled', false);
                buttonAddFeeRow.prop('disabled', false);
            } else {
                //$('#boxFee').hide();
                inputFeeTitle.prop('required', false);
                inputFeeTitle.prop('disabled', true);
                inputFeeAmount.prop('disabled', true);
                buttonAddFeeRow.prop('disabled', true);
                inputFeeTitle.val('');
                inputFeeAmount.val('');

                /*ลบให้เหลือเฉพาะแถวแรก*/
                tableFeeRow.each(function (index, item) {
                    if (index > 0) item.remove();
                });
            }
        }

        $(function () {
            $('#image-upload-preview').hide();
            $('#pdf-upload-preview').hide();

            const imagesPreview = function (input, placeToInsertImagePreview) {
                $(placeToInsertImagePreview).empty();
                $(placeToInsertImagePreview).hide();

                if (input.files) {
                    let fileCount = input.files.length;

                    for (let i = 0; i < fileCount; i++) {
                        $(placeToInsertImagePreview).show();
                        let reader = new FileReader();

                        reader.onload = function (event) {
                            $($.parseHTML('<img style="width: auto; height: 120px; margin: 3px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        };
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };

            $('#image-upload').on('change', function () {
                imagesPreview(this, 'div#image-upload-preview');
            });

            const pdfPreview = function (input, placeToInsertPdfPreview) {
                $(placeToInsertPdfPreview).empty();
                $(placeToInsertPdfPreview).hide();

                if (input.files) {
                    let fileCount = input.files.length;

                    for (let i = 0; i < fileCount; i++) {
                        $(placeToInsertPdfPreview).show();
                        $($.parseHTML('<li style="">' + input.files[i].name + '</li>')).appendTo(placeToInsertPdfPreview);
                    }
                }
            };

            $('#pdf-upload').on('change', function () {
                pdfPreview(this, 'ul#pdf-upload-preview');
            });
        });

        function onClickDeleteFee(element) {
            if ($(element).closest('tbody').children('tr').length > 1) {
                BootstrapDialog.show({
                    title: 'ลบรายการราคา',
                    message: `ยืนยันลบรายการราคานี้?\n\nหมายเหตุ: การลบจะยังไม่มีผลกับฐานข้อมูลจริง จนกว่าจะกดปุ่มบันทึก`,
                    buttons: [{
                        label: 'ลบ',
                        action: function (self) {
                            $(element).closest('tr').remove();
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
            } else {
                alert('ลบไม่ได้, ต้องมีราคาอย่างน้อย 1 รายการ')
            }

        }

        function doAddEditCourse() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            CKEDITOR.instances.editor.updateElement();

            $('#formAddCourse #buttonSave').prop('disabled', true);
            $('#formAddCourse #divLoading').show();

            $('#formAddCourse').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddCourse #buttonSave').prop('disabled', false);
                    $('#formAddCourse #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($courseId)) {
                                    ?>
                                    window.location.href = 'course.php?service_type=<?php echo $serviceType; ?>';
                                    <?php
                                    } else {
                                    ?>
                                    window.location.href = 'course.php?service_type=<?php echo $serviceType; ?>';
                                    //window.location.reload(true);
                                    <?php
                                    }
                                    ?>
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                }
                            }]
                        });
                    }
                },
                error: (e) => {
                    $('#formAddCourse #buttonSave').prop('disabled', false);
                    $('#formAddCourse #divLoading').hide();

                    console.log(e);

                    BootstrapDialog.show({
                        title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
                        message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server: ' + e,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });
                }
            });
        }

        function onClickDeleteAsset(element, assetId, assetType) {
            BootstrapDialog.show({
                title: 'ลบ' + assetType,
                message: `การลบ${assetType}จะมีผลกับฐานข้อมูลทันที!\n\nยืนยันลบ${assetType}นี้?`,
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteAsset(assetId, assetType);
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

        function doDeleteAsset(assetId, assetType) {
            $.post(
                '../api/api.php/delete_course_asset',
                {
                    assetId: assetId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: `ลบ${assetType} - ผิดพลาด`,
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
                    title: `ลบ${assetType} - ผิดพลาด`,
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

        function doAddEditCourse_old() {
            /*alert(CKEDITOR.instances.editor.getData() + '\n\n' + $('#editor').text());
            return;*/

            let beginDate = $('#inputBeginDate').val();
            let beginDatePart = beginDate.split('/');
            beginDate = beginDatePart[2] + '-' + beginDatePart[1] + '-' + beginDatePart[0];

            let endDate = $('#inputEndDate').val();
            let endDatePart = endDate.split('/');
            endDate = endDatePart[2] + '-' + endDatePart[1] + '-' + endDatePart[0];

            $('#formAddCourse #buttonSave').prop('disabled', true);
            $('#formAddCourse #divLoading').show();

            $.post(
                '../api/api.php/<?php echo(isset($courseId) ? 'update_course' : 'add_course'); ?>',
                {
                    courseId: <?php echo(isset($courseId) ? "$courseId" : '0'); ?>,
                    courseMasterId: $('#selectCourseMaster').val(),
                    batchNumber: $('#inputBatchNumber').val(),
                    applicationFee: $('#inputApplicationFee').val(),
                    traineeLimit: $('#inputTraineeLimit').val(),
                    beginDate,
                    endDate,
                    place: $('#inputPlace').val(),
                    responsibleUserId: $('#selectResponsibleUser').val(),
                    details: CKEDITOR.instances.editor.getData(),
                }
            ).done(function (data) {
                $('#formAddCourse #buttonSave').prop('disabled', false);
                $('#formAddCourse #divLoading').hide();

                if (data.error_code === 0) {
                    BootstrapDialog.show({
                        title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                                <?php
                                if (!isset($courseId)) {
                                ?>
                                window.location.href = 'course.php?service_type=<?php echo $serviceType; ?>';
                                <?php
                                } else {
                                ?>
                                window.location.reload(true);
                                <?php
                                }
                                ?>
                            }
                        }]
                    });
                } else {
                    BootstrapDialog.show({
                        title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
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
                $('#formAddCourse #buttonSave').prop('disabled', false);
                $('#formAddCourse #divLoading').hide();

                BootstrapDialog.show({
                    title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
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
    <!-- CK Editor -->
    <script src="../bower_components/ckeditor/ckeditor.js"></script>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>

    <!-- MDB core JavaScript -->
    <!--<script type="text/javascript" src="../lib/mdb/mdb.min.js"></script>-->
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>