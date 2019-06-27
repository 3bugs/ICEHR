<?php
require_once '../include/head_php.inc';

$id = $_GET['id'];

$academicPaper = array();
if (isset($id)) {
    $id = $db->real_escape_string($id);

    $sql = "SELECT * FROM academic_paper WHERE id = $id";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $academicPaper['id'] = (int)$row['id'];
            $academicPaper['title'] = $row['title'];
            $academicPaper['file_name'] = $row['file_name'];
            $academicPaper['first_name'] = $row['first_name'];
            $academicPaper['last_name'] = $row['last_name'];
            $academicPaper['year_published'] = $row['year_published'];
            $academicPaper['abstract'] = $row['abstract'];
            $academicPaper['fund_source'] = $row['fund_source'];
            $academicPaper['created_at'] = $row['created_at'];
        } else {
            echo 'ไม่พบข้อมูล';
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

?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <style>
            input[type="file"] {
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
                    <?php echo(isset($id) ? 'แก้ไข' : 'เพิ่ม'); ?>งานวิจัย/วิชาการ
                    <small><?php echo $serviceTypeText[$serviceType]; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddEditAcademicPaper"
                      action="../api/api.php/<?php echo(empty($academicPaper) ? 'add_academic_paper' : 'update_academic_paper'); ?>"
                      method="post" enctype=multipart/form-data>

                    <input type="hidden" name="id" value="<?php echo $academicPaper['id']; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียด-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียด</h3>

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

                                    <!--ชื่อเรื่องและปีที่เผยแพร่-->
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="inputTitle">ชื่อเรื่อง:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle" name="title"
                                                           value="<?php echo(!empty($academicPaper) ? $academicPaper['title'] : ''); ?>"
                                                           placeholder="กรอกชื่อเรื่อง" required
                                                           oninvalid="this.setCustomValidity('กรอกชื่อเรื่อง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputYearPublished">ปี พ.ศ. ที่เผยแพร่:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                    <input type="number" class="form-control"
                                                           id="inputYearPublished" name="year_published"
                                                           value="<?php echo(!empty($academicPaper) ? $academicPaper['year_published'] : ''); ?>"
                                                           max="9999"
                                                           placeholder="กรอกปีที่เผยแพร่" required
                                                           oninvalid="this.setCustomValidity('กรอกปีที่เผยแพร่')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--ผู้ทำวิจัย, แหล่งทุน-->
                                    <div class="row">
                                        <div class="col-md-3" style="padding-right: 0">
                                            <div class="form-group">
                                                <label for="inputFirstName">ผู้ทำวิจัย:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputFirstName" name="first_name"
                                                           value="<?php echo(!empty($academicPaper) ? $academicPaper['first_name'] : ''); ?>"
                                                           placeholder="กรอกชื่อผู้ทำวิจัย" required
                                                           oninvalid="this.setCustomValidity('กรอกชื่อผู้ทำวิจัย')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputLastName">&nbsp;</label>
                                                <input type="text" class="form-control" style="position: relative; left: 0"
                                                       id="inputLastName" name="last_name"
                                                       value="<?php echo(!empty($academicPaper) ? $academicPaper['last_name'] : ''); ?>"
                                                       placeholder="กรอกนามสกุลผู้ทำวิจัย" required
                                                       oninvalid="this.setCustomValidity('กรอกนามสกุลผู้ทำวิจัย')"
                                                       oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputFundSource">แหล่งทุนวิจัย:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-dollar"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputFundSource" name="fund_source"
                                                           value="<?php echo(!empty($academicPaper) ? $academicPaper['fund_source'] : ''); ?>"
                                                           placeholder="กรอกแหล่งทุนวิจัย" required
                                                           oninvalid="this.setCustomValidity('กรอกแหล่งทุนวิจัย')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->

                            <!--บทคัดย่อ-->
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h3 class="box-title">บทคัดย่อ
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
                                    <textarea id="textAreaAbstract" name="abstract" required
                                              rows="15" style="width: 100%; padding: 12px"
                                              placeholder="กรอกบทคัดย่อ"
                                              oninvalid="this.setCustomValidity('กรอกบทคัดย่อ')"
                                              oninput="this.setCustomValidity('')"><?php echo(!empty($academicPaper) ? $academicPaper['abstract'] : ''); ?></textarea>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--pdf-->
                            <div class="box box-info">
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
                                    <?php
                                    if (!empty($academicPaper)) {
                                        ?>
                                        <!-- Custom Tabs -->
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#pdf_tab_1" data-toggle="tab">PDF ปัจจุบัน</a></li>
                                                <li><a href="#pdf_tab_2" data-toggle="tab">อัพโหลด PDF ใหม่</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="pdf_tab_1">
                                                    <div style="padding: 5px">
                                                        <a target="_blank" href="../uploads/academic_papers/<?php echo $academicPaper['file_name']; ?>">เปิดในหน้าจอใหม่</a>
                                                    </div>
                                                    <embed src="../uploads/academic_papers/<?php echo $academicPaper['file_name']; ?>"
                                                           width="600px" height="450px">
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="pdf_tab_2" style="padding: 0px">
                                                    <!--<label for="file-upload" class="custom-file-upload" style="margin: 20px 0;">
                                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลด PDF ใหม่
                                                    </label>-->
                                                    <input id="file-upload" name="file"
                                                           type="file"
                                                           style="margin-top: 10px"
                                                           onChange="getFileData(this);"/>&nbsp;<!--<span id="spanFileName">ยังไม่ได้เลือกไฟล์</span>-->
                                                    <div style="color: red; margin: 5px">
                                                        <div style="margin-bottom: 5px"><u>หมายเหตุ</u>:</div>
                                                        <ul>
                                                            <li>PDF ที่อัพโหลดใหม่ จะแทนที่ PDF ปัจจุบัน</li>
                                                            <li>ต้องกดปุ่ม "บันทึก" ข้างล่าง จึงจะบันทึก PDF ที่อัพโหลดใหม่เข้าสู่ระบบ</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!-- /.tab-pane -->
                                            </div>
                                            <!-- /.tab-content -->
                                        </div>
                                        <!-- nav-tabs-custom -->
                                        <?php
                                    } else {
                                        ?>
                                        <!--<label for="file-upload" class="custom-file-upload">
                                            <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลด PDF
                                        </label>-->
                                        <input id="file-upload" name="file" required
                                               type="file"
                                               style="margin-top: 10px"
                                               onChange="getFileData(this);"
                                               oninvalid="this.setCustomValidity('เลือกไฟล์ PDF')"
                                               oninput="this.setCustomValidity('')"/>&nbsp;<!--<span id="spanFileName">ยังไม่ได้เลือกไฟล์</span>-->
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--ปุ่ม "บันทึก"-->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                    </div>
                                    <button id="buttonSave" name="submit" type="submit"
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
        $(document).ready(function () {
            $('#formAddEditAcademicPaper #divLoading').hide();

            $('#formAddEditAcademicPaper').submit(event => {
                event.preventDefault();
                doAddEditAcademicPaper();
            });
        });

        function getFileData(element) {
            /*const fileName = element.files[0].name;
            $('#spanFileName').text(fileName);*/
        }

        function doAddEditAcademicPaper() {
            $('#formAddEditAcademicPaper #buttonSave').prop('disabled', true);
            $('#formAddEditAcademicPaper #divLoading').show();

            $('#formAddEditAcademicPaper').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {

                    console.log(data);

                    $('#formAddEditAcademicPaper #buttonSave').prop('disabled', false);
                    $('#formAddEditAcademicPaper #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($id) ? 'แก้ไขงานวิจัย/วิชาการ' : 'เพิ่มงานวิจัย/วิชาการ'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($id)) {
                                    ?>
                                    window.location.href = 'academic_papers.php';
                                    <?php
                                    } else {
                                    ?>
                                    location.reload(true);
                                    <?php
                                    }
                                    ?>
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($id) ? 'แก้ไขงานวิจัย/วิชาการ' : 'เพิ่มงานวิจัย/วิชาการ'); ?> - ผิดพลาด',
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
                error: () => {
                    $('#formAddEditAcademicPaper #buttonSave').prop('disabled', false);
                    $('#formAddEditAcademicPaper #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?php echo(isset($id) ? 'แก้ไขงานวิจัย/วิชาการ' : 'เพิ่มงานวิจัย/วิชาการ'); ?> - ผิดพลาด',
                        message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
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

        function doAddEditAcademicPaper_old() {
            $('#formAddEditAcademicPaper #buttonSave').prop('disabled', true);
            $('#formAddEditAcademicPaper #divLoading').show();

            $.post(
                '../api/api.php/<?php echo(isset($id) ? 'update_course' : 'add_course'); ?>',
                {
                    courseId: <?php echo(isset($id) ? "$id" : '0'); ?>,
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
                $('#formAddEditAcademicPaper #buttonSave').prop('disabled', false);
                $('#formAddEditAcademicPaper #divLoading').hide();

                if (data.error_code === 0) {
                    BootstrapDialog.show({
                        title: '<?php echo(isset($id) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                                <?php
                                if (!isset($id)) {
                                ?>
                                window.location.href = 'course.php?service_type=<?php echo $serviceType; ?>';
                                <?php
                                }
                                ?>
                            }
                        }]
                    });
                } else {
                    BootstrapDialog.show({
                        title: '<?php echo(isset($id) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
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
                $('#formAddEditAcademicPaper #buttonSave').prop('disabled', false);
                $('#formAddEditAcademicPaper #divLoading').hide();

                BootstrapDialog.show({
                    title: '<?php echo(isset($id) ? 'แก้ไขหลักสูตร' : 'เพิ่มหลักสูตร'); ?> - ผิดพลาด',
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
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>