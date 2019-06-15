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

if (isset($courseId)) {
    $sql = "SELECT id, form_number, status, created_at, coordinator_first_name, coordinator_last_name, coordinator_email, coordinator_phone 
                FROM course_registration 
                WHERE course_id=$courseId 
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

            $traineeSql = "SELECT title, first_name, last_name, phone FROM course_trainee WHERE course_registration_id=$courseRegId";
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
                echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                $result->close();
                $db->close();
                exit();
            }

            array_push($courseRegList, $courseReg);
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }
}

?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- Material Design Bootstrap -->
        <!--<link href="../lib/mdb/mdb.min.css" rel="stylesheet">-->
        <!-- Material Design Bootstrap -->
        <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.2/css/mdb.min.css" rel="stylesheet">-->
        <style>
            input[type="file"] {
                display: none;
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
                    <small>บริการฝึกอบรม</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddCourse">
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
                                                           oninput="this.setCustomValidity('')">
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
                                                           oninput="this.setCustomValidity('')">
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
                                                           oninput="this.setCustomValidity('')">
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
                                                           oninput="this.setCustomValidity('')">
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
                                                           oninput="this.setCustomValidity('')">
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
                            <div class="box box-info">
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
                                <textarea id="editor" rows="20" cols="80">
                                    <?php echo(!empty($course) ? $course['details'] : ''); ?>
                                </textarea>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--pictures gallery-->
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Picture Gallery
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
                                <div class="box-body pad">
                                    <label for="file-upload" class="custom-file-upload">
                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลดรูปภาพ
                                    </label>
                                    <input id="file-upload" type="file"/>
                                    <div style="margin-top: 10px">
                                        <img src="../images/image-test.png" style="width: 200px; margin: 0 0 10px 10px"/>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--pdf gallery-->
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">เอกสาร PDF
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
                                <div class="box-body pad">
                                    <label for="file-upload" class="custom-file-upload">
                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลด PDF
                                    </label>
                                    <input id="file-upload" type="file"/>
                                    <div style="margin-top: 10px">
                                        <a class="btn btn-app">
                                            <i class="fa fa-file-pdf-o"></i> ทดสอบ PDF 1
                                        </a>
                                        <a class="btn btn-app">
                                            <i class="fa fa-file-pdf-o"></i> ทดสอบ PDF 2
                                        </a>
                                        <a class="btn btn-app">
                                            <i class="fa fa-file-pdf-o"></i> ทดสอบ PDF 3
                                        </a>
                                        <a class="btn btn-app">
                                            <i class="fa fa-file-pdf-o"></i> ทดสอบ PDF 4
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box -->

                            <div class="row">
                                <div class="col-12 text-center">
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

            CKEDITOR.replace('editor');
        });

        $(document).ready(function () {
            $('#formAddCourse').submit(event => {
                event.preventDefault();
                doAddCourse();
            });
        });

        function doAddCourse() {
            let beginDate = $('#inputBeginDate').val();
            let beginDatePart = beginDate.split('/');
            beginDate = beginDatePart[2] + '-' + beginDatePart[1] + '-' + beginDatePart[0];

            let endDate = $('#inputEndDate').val();
            let endDatePart = endDate.split('/');
            endDate = endDatePart[2] + '-' + endDatePart[1] + '-' + endDatePart[0];

            $.post(
                '../api/api.php/<?php echo(isset($courseId) ? 'update_course' : 'add_course'); ?>',
                {
                    courseId: <?php echo(isset($courseId) ? "$courseId" : '0'); ?>,
                    courseMasterId: $('#selectCourseMaster').val(),
                    batchNumber: $('#inputBatchNumber').val(),
                    applicationFee: $('#inputApplicationFee').val(),
                    beginDate,
                    endDate,
                    place: $('#inputPlace').val(),
                    responsibleUserId: $('#selectResponsibleUser').val(),
                    details: CKEDITOR.instances.editor.getData(),
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    BootstrapDialog.show({
                        title: '<?php echo(isset($courseId) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });
                    //window.location.href = 'course.php';
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
            });
        }

    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- CK Editor -->
    <script src="../bower_components/ckeditor/ckeditor.js"></script>
    <!-- MDB core JavaScript -->
    <!--<script type="text/javascript" src="../lib/mdb/mdb.min.js"></script>-->
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>