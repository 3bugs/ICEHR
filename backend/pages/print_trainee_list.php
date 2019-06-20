<?php
require_once '../include/head_php.inc';

$serviceType = $_GET['service_type'];
$courseId = $_GET['course_id'];

$traineeList = array();
$course = null;

switch ($serviceType) {
    case SERVICE_TYPE_TRAINING:
        $sql = "SELECT ct.title, ct.first_name, ct.last_name, ct.birth_date, ct.phone, ct.email, ct.job_position, ct.organization_name,
               c.begin_date, c.end_date,
               cm.title AS course_title, c.batch_number AS course_batch_number
            FROM course c 
                INNER JOIN course_registration cr 
                    ON cr.course_id = c.id 
                INNER JOIN course_trainee ct 
                    ON ct.course_registration_id = cr.id 
                INNER JOIN course_master cm 
                    ON c.course_master_id = cm.id 
            WHERE c.id = $courseId";
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
            WHERE c.id = $courseId";
        break;
}

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $course = array(
                'title' => $row['course_title'],
                'batch_number' => $row['course_batch_number'],
                'begin_date' => $row['begin_date'],
                'end_date' => $row['end_date'],
                'driving_license_course_type' => $row['driving_license_course_type']
            );

            $trainee = array();
            $trainee['title'] = $row['title'];
            $trainee['first_name'] = $row['first_name'];
            $trainee['last_name'] = $row['last_name'];
            $trainee['birth_date'] = $row['birth_date'];
            $trainee['phone'] = $row['phone'];
            $trainee['email'] = $row['email'];
            $trainee['job_position'] = $row['job_position'];
            $trainee['organization_name'] = $row['organization_name'];

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
        <title>ทำเนียบรุ่น <?php echo(isset($formNumber) ? $formNumber : ''); ?></title>
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
        <div style="text-align: center; margin: 1.6cm">ไม่มีข้อมูลผู้เข้าอบรม</div>
        <?php
    } else {
        ?>
        <div id="main-container" class="wrapper" style="height: auto;">
            <!-- Main content -->
            <section class="invoice">
                <!-- title row -->
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="page-header">
                            <img src="../images/logo_icehr.svg" width="300px">

                            <small class="pull-right" style="text-align: right; margin-top: 15px">
                                <?php
                                $courseName = $course['title'] . ($serviceType === SERVICE_TYPE_TRAINING ? " รุ่นที่ {$course['batch_number']}" : '');

                                $beginDate = date_create($course['begin_date']);
                                $endDate = date_create($course['end_date']);
                                $courseDisplayDate = ($course['begin_date'] === $course['end_date'] ? getThaiShortDate($beginDate) : getThaiIntervalShortDate($beginDate, $endDate));

                                echo "$courseName<br><br>$courseDisplayDate";
                                ?>
                            </small>
                        </h2>
                    </div>
                    <!-- /.col -->
                </div>

                <p class="lead" style="text-align: center"><u>ทำเนียบผู้เข้าอบรม</u></p>

                <!-- Table row -->
                <div class="row" style="margin-top: 10px">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ชื่อ-นามสกุล</th>
                                <th>วันเกิด</th>
                                <th>ตำแหน่ง</th>
                                <th>หน่วยงาน</th>
                                <th>เบอร์โทร</th>
                                <th>อีเมล</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($traineeList as $trainee) {
                                $birthDate = date_create($trainee['birth_date']);
                                $birthDateThai = getThaiShortDate($birthDate);

                                ?>
                                <tr>
                                    <td><?php echo "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}" ?></td>
                                    <td><?php echo $birthDateThai; ?></td>
                                    <td><?php echo $trainee['job_position']; ?></td>
                                    <td><?php echo $trainee['organization_name']; ?></td>
                                    <td><?php echo $trainee['phone']; ?></td>
                                    <td><?php echo $trainee['email']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!--<div class="page-break"></div>-->
        <?php
    }
    ?>
    <!-- ./wrapper -->
    </body>
    </html>
<?php
require_once '../include/foot_php.inc';
?>