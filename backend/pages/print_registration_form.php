<?php
require_once '../include/head_php.inc';

$serviceType = $_GET['service_type'];
$formNumber = $_GET['form_number'];
$courseId = $_GET['course_id'];

$traineeList = array();

$where = false;
if (isset($formNumber)) {
    $where = ($serviceType === SERVICE_TYPE_TRAINING ? 'ct' : 'cr') . (".form_number = '$formNumber'");
} else if (isset($courseId)) {
    $where = "c.id = $courseId ORDER BY created_at";
}

switch ($serviceType) {
    case SERVICE_TYPE_TRAINING:
        $sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.form_number, ct.created_at, ct.register_status,
               c.begin_date, c.end_date,
               cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email,
               cm.title AS course_title, c.batch_number AS course_batch_number
            FROM course c 
                INNER JOIN course_registration cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_trainee ct 
                    ON ct.course_registration_id = cr.id 
                INNER JOIN course_master cm 
                    ON c.course_master_id = cm.id 
            WHERE $where";
        break;
    case SERVICE_TYPE_DRIVING_LICENSE:
        $sql = "SELECT cr.title, cr.first_name, cr.last_name, cr.phone, cr.form_number, cr.created_at, cr.register_status,
               c.begin_date, c.end_date, dlct.title AS driving_license_course_type,
               cm.title AS course_title, c.batch_number AS course_batch_number
            FROM course c 
                INNER JOIN course_registration_driving_license cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_master cm 
                    ON c.course_master_id = cm.id 
                INNER JOIN driving_license_course_type dlct 
                    ON dlct.id = cr.course_type 
            WHERE $where";
        break;
}

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $trainee = array();
            $trainee['title'] = $row['title'];
            $trainee['first_name'] = $row['first_name'];
            $trainee['last_name'] = $row['last_name'];
            $trainee['phone'] = $row['phone'];
            $trainee['email'] = $row['email'];
            $trainee['form_number'] = $row['form_number'];
            $trainee['created_at'] = $row['created_at'];
            $trainee['register_status'] = $row['register_status'];

            $trainee['coordinator'] = array(
                'title' => $row['coordinator_title'],
                'first_name' => $row['coordinator_first_name'],
                'last_name' => $row['coordinator_last_name'],
                'phone' => $row['coordinator_phone'],
                'email' => $row['coordinator_email'],
            );

            $trainee['course'] = array(
                'title' => $row['course_title'],
                'batch_number' => $row['course_batch_number'],
                'begin_date' => $row['begin_date'],
                'end_date' => $row['end_date'],
                'driving_license_course_type' => $row['driving_license_course_type']
            );

            array_push($traineeList, $trainee);
        }
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
    exit();
}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ใบสมัคร <?php echo(isset($formNumber) ? $formNumber : ''); ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="../bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

        <style>
            @media print {
                @page {
                    margin: 0
                }

                /*body { margin: 1.6cm; }*/
                #main-container {
                    margin: 1.6cm
                }

                .page-break {
                    page-break-after: always;
                }
            }

            @media screen {
                body {
                    margin: auto 200px;
                }
            }
        </style>
    </head>
    <body onload="window.print();" style="height: auto">
    <?php
    if (sizeof($traineeList) === 0) {
        ?>
        <div style="text-align: center; margin: 1.6cm">ไม่มีข้อมูลใบสมัคร</div>
        <?php
    } else {
        foreach ($traineeList as $trainee) {
            $coordinator = $trainee['coordinator'];
            $course = $trainee['course'];

            ?>
            <div id="main-container" class="wrapper" style="height: auto;">
                <!-- Main content -->
                <section class="invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                <img src="../images/logo_icehr.svg" width="300px">
                                <?php
                                $now = date('Y/m/d');
                                $displayDate = getThaiDate(date_create($now));
                                ?>
                                <small class="pull-right" style="color: #808080"><?php echo $displayDate; ?></small>
                            </h2>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            ผู้สมัคร:
                            <address>
                                <div style="margin-bottom: 2px">
                                    <strong><?php echo "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}" ?></strong><br>
                                </div>
                                <div>
                                    โทร:<!--<i class="fa fa-phone" style="color: black"></i>--> <?php echo $trainee['phone']; ?><br>
                                    <?php
                                    if ($serviceType === SERVICE_TYPE_TRAINING) {
                                        ?>
                                        Email:<!--<i class="fa fa-envelope-o" style="color: black"></i>--> <?php echo $trainee['email']; ?>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <?php
                            if ($serviceType === SERVICE_TYPE_TRAINING && $coordinator['title']) {
                                ?>
                                ผู้ประสานงาน:
                                <address>
                                    <div style="margin-bottom: 2px">
                                        <strong><?php echo "{$coordinator['title']} {$coordinator['first_name']} {$coordinator['last_name']}" ?></strong>
                                    </div>
                                    <div>
                                        โทร:<!--<i class="fa fa-phone" style="color: black"></i>--> <?php echo $coordinator['phone']; ?><br>
                                        Email:<!--<i class="fa fa-envelope-o" style="color: black"></i>--> <?php echo $coordinator['email']; ?>
                                    </div>
                                </address>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <?php
                            $createdAt = $trainee['created_at'];
                            $dateTimePart = explode(' ', $createdAt);
                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                            $timePart = explode(':', $dateTimePart[1]);
                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                            $displayDateTime = "$displayDate, $displayTime";

                            $registerStatus = $registerStatusText[$trainee['register_status']];
                            ?>
                            <div style="margin-bottom: 5px">ใบสมัครเลขที่: <br><b><?php echo $trainee['form_number']; ?></b></div>
                            <div style="margin-bottom: 5px">สมัครเมื่อ: <br><b><?php echo $displayDateTime; ?></b></div>
                            <div>สถานะการสมัคร: <br><b><?php echo $registerStatus; ?></b></div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row" style="margin-top: 30px">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>หลักสูตรที่สมัคร</th>
                                    <th style="width: 150px">วันอบรม</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?php
                                        echo $course['title'] . ($serviceType === SERVICE_TYPE_TRAINING ? " รุ่นที่ {$course['batch_number']}"
                                                : ($serviceType === SERVICE_TYPE_DRIVING_LICENSE ? " - {$course['driving_license_course_type']}" : ''))
                                        ?>
                                    </td>
                                    <?php
                                    $beginDate = date_create($course['begin_date']);
                                    $endDate = date_create($course['end_date']);
                                    $courseDisplayDate = ($course['begin_date'] === $course['end_date'] ? getThaiShortDate($beginDate) : getThaiIntervalShortDate($beginDate, $endDate));
                                    ?>
                                    <td><?php echo $courseDisplayDate; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row" style="margin-top: 30px">
                        <!-- accepted payments column -->
                        <div class="col-xs-6">
                            <p class="lead"><u>การชำระเงิน</u></p>
                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px; padding: 12px">
                                <span style="display: block; margin-bottom: 5px">ชื่อบัญชี <strong>มหาวิทยาลัยธรรมศาสตร์</strong></span>
                                <span style="display: inline-block; width: 100px">กสิกรไทย</span>เลขที่ xxx-x-xxxxx-x<br>
                                <span style="display: inline-block; width: 100px">ไทยพาณิชย์</span>เลขที่ xxx-x-xxxxx-x<br>
                            </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-6">
                            <p class="lead"><u>อัตราค่าสมัคร</u></p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width: 60%">ปกติ</th>
                                        <td>5,000</td>
                                    </tr>
                                    <tr>
                                        <th>Early bird<br>สมัครก่อน 20 มิ.ย. 62</th>
                                        <td>4,500</td>
                                    </tr>
                                    <tr>
                                        <th>บุคลากร</th>
                                        <td>4,000</td>
                                    </tr>
                                    <tr>
                                        <th>10 แถม 1</th>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </section>
                <!-- /.content -->
            </div>
            <div class="page-break"></div>
            <?php
        }
    }
    ?>
    <!-- ./wrapper -->
    </body>
    </html>
<?php
require_once '../include/foot_php.inc';
?>