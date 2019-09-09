<?php
require_once '../include/head_php.inc';

$trainerId = $_GET['trainer_id'];

$trainer = array();
if (isset($trainerId)) {
    $trainerId = $db->real_escape_string($trainerId);

    $sql = "SELECT * FROM trainer WHERE id = $trainerId";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $trainer = $result->fetch_assoc();
        } else {
            echo 'ไม่พบข้อมูลวิทยากร';
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
        <!--Lightbox-->
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">

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
                    <?php echo(isset($trainerId) ? 'แก้ไขข้อมูล' : 'เพิ่ม'); ?>วิทยากร
                    <small>&nbsp;</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddTrainer"
                      action="../api/api.php/<?php echo(isset($trainerId) ? 'update_trainer' : 'add_trainer'); ?>"
                      method="post">

                    <input type="hidden" name="trainerId"
                           value="<?php echo $trainerId; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

                            <!--ข้อมูลวิทยากร-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ข้อมูลวิทยากร</h3>

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

                                    <!--คำนำหน้า ชื่อ นามสกุล-->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputTitle">คำนำหน้าชื่อ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle"
                                                           name="title"
                                                           value="<?php echo(!empty($trainer) ? $trainer['title'] : ''); ?>"
                                                           placeholder="กรอกคำนำหน้าชื่อ" required
                                                           oninvalid="this.setCustomValidity('กรอกคำนำหน้าชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputFirstName">ชื่อ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputFirstName"
                                                           name="firstName"
                                                           value="<?php echo(!empty($trainer) ? $trainer['first_name'] : ''); ?>"
                                                           placeholder="กรอกชื่อ" required
                                                           oninvalid="this.setCustomValidity('กรอกชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputLastName">นามสกุล:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputLastName"
                                                           name="lastName"
                                                           value="<?php echo(!empty($trainer) ? $trainer['last_name'] : ''); ?>"
                                                           placeholder="กรอกนามสกุล" required
                                                           oninvalid="this.setCustomValidity('กรอกนามสกุล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--เลขประชาชน อีเมล เบอร์โทร-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputPid">เลขประจำตัวประชาชน:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-hashtag"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPid"
                                                           name="pid"
                                                           value="<?php echo(!empty($trainer) ? $trainer['pid'] : ''); ?>"
                                                           placeholder="กรอกเลขประจำตัวประชาชน" required
                                                           oninvalid="this.setCustomValidity('กรอกเลขประจำตัวประชาชน')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputEmail">อีเมล:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-envelope"></i>
                                                        </span>
                                                    <input type="email" class="form-control"
                                                           id="inputEmail"
                                                           name="email"
                                                           value="<?php echo(!empty($trainer) ? $trainer['email'] : ''); ?>"
                                                           placeholder="กรอกอีเมล"
                                                           oninvalid="this.setCustomValidity('กรอกอีเมล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputPhone">เบอร์โทรมือถือ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-mobile"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPhone"
                                                           name="phone"
                                                           value="<?php echo(!empty($trainer) ? $trainer['phone'] : ''); ?>"
                                                           placeholder="กรอกเบอร์โทรมือถือ"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์โทรมือถือ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->

                            <!--รูปภาพลายเซ็น-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รูปภาพลายเซ็น
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
                                    if (!empty($trainer)) {
                                        ?>
                                        <!-- Custom Tabs -->
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#image_tab_1" data-toggle="tab">รูปภาพปัจจุบัน</a></li>
                                                <li><a href="#image_tab_2" data-toggle="tab">อัพโหลดรูปภาพใหม่</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="image_tab_1">
                                                    <div style="padding: 5px">
                                                        <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['signature_image']); */ ?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                    </div>
                                                    <a href="<?= (UPLOAD_DIR_SIGNATURES . $trainer['signature_image']); ?>"
                                                       data-lightbox="coverImage" data-title="<?= "{$trainer['title']} {$trainer['first_name']} {$trainer['last_name']}"; ?>">
                                                        <img src="<?= (UPLOAD_DIR_SIGNATURES . $trainer['signature_image']); ?>"
                                                             width="200px" style="border-radius: 0%;">
                                                    </a>
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="image_tab_2" style="padding: 0px">
                                                    <div class="callout callout-info">
                                                        <h4>คำแนะนำในการเตรียมรูปภาพลายเซ็น</h4>
                                                        <ul style="margin-top: 10px; margin-bottom: 0px">
                                                            <li>เป็นภาพแนวนอน ความกว้างไม่เกิน 1,000 pixel</li>
                                                            <li>ควรตัด (crop) ให้เหลือขอบขาวทั้ง 4 ด้านของภาพให้น้อยที่สุดเท่าที่เป็นไปได้</li>
                                                        </ul>
                                                    </div>

                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                        <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                    </ul>
                                                    <input id="image-file-upload" name="signatureImage"
                                                           type="file" accept="image/*"
                                                           style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                    <div id="image-upload-preview"
                                                         style="background: #efffd1; padding: 10px;"></div>
                                                </div>
                                                <!-- /.tab-pane -->
                                            </div>
                                            <!-- /.tab-content -->
                                        </div>
                                        <!-- nav-tabs-custom -->
                                        <?php
                                    } else {
                                        ?>
                                        <div class="callout callout-info">
                                            <h4>คำแนะนำในการเตรียมรูปภาพลายเซ็น</h4>
                                            <ul style="margin-top: 10px; margin-bottom: 0px">
                                                <li>เป็นภาพแนวนอน ความกว้างไม่เกิน 1,000 pixel</li>
                                                <li>ควรตัด (crop) ให้เหลือขอบขาวทั้ง 4 ด้านของภาพให้น้อยที่สุดเท่าที่เป็นไปได้</li>
                                            </ul>
                                        </div>

                                        <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                        </ul>
                                        <input id="image-file-upload" name="signatureImage" required
                                               type="file" accept="image/*"
                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                               oninvalid="this.setCustomValidity('เลือกรูปภาพลายเซ็น')"
                                               oninput="this.setCustomValidity('')"/>
                                        <div id="image-upload-preview"
                                             style="background: #efffd1; padding: 10px;"></div>
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
            $('#image-upload-preview').hide();

            const imagesPreview = function (input, placeToInsertImagePreview) {
                $(placeToInsertImagePreview).empty();
                $(placeToInsertImagePreview).hide();

                if (input.files) {
                    let fileCount = input.files.length;

                    for (let i = 0; i < fileCount; i++) {
                        $(placeToInsertImagePreview).show();
                        let reader = new FileReader();

                        reader.onload = function (event) {
                            $($.parseHTML('<img style="width: auto; height: 200px; margin: 3px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        };
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };

            $('#image-file-upload').on('change', function () {
                imagesPreview(this, 'div#image-upload-preview');
            });
        });

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });

            $('#formAddTrainer #divLoading').hide();

            $('#formAddTrainer').submit(event => {
                event.preventDefault();
                if (validateForm()) {
                    doAddEditUser();
                }
            });
        });

        function validateForm() {
            let valid = true;

            const inputPid = $('#inputPid');
            let pid = inputPid.val();
            if (!isValidPid(pid)) {
                inputPid.focus();
                alert('เลขประจำตัวประชาชนไม่ถูกต้อง');
                valid = false;
            }

            return valid;
        }

        function isValidPid(pid) {
            if (pid == null) return false;

            pid = pid.trim();
            if (pid.length !== 13) return false;

            let sum = 0;
            for (let i = 0; i < 12; i++) {
                sum += parseFloat(pid.charAt(i)) * (13 - i);
            }

            return (11 - sum % 11) % 10 === parseInt(pid.charAt(12));
        }

        function doAddEditUser() {
            $('#formAddTrainer #buttonSave').prop('disabled', true);
            $('#formAddTrainer #divLoading').show();

            $('#formAddTrainer').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddTrainer #buttonSave').prop('disabled', false);
                    $('#formAddTrainer #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($trainerId) ? 'แก้ไขข้อมูลวิทยากร' : 'เพิ่มวิทยากร'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($trainerId)) {
                                    ?>
                                    window.location.href = 'trainer.php';
                                    <?php
                                    } else {
                                    ?>
                                    window.location.href = 'trainer.php';
                                    //window.location.reload(true);
                                    <?php
                                    }
                                    ?>
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($trainerId) ? 'แก้ไขข้อมูลวิทยากร' : 'เพิ่มวิทยากร'); ?> - ผิดพลาด',
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
                    $('#formAddTrainer #buttonSave').prop('disabled', false);
                    $('#formAddTrainer #divLoading').hide();

                    console.log(e);

                    BootstrapDialog.show({
                        title: '<?php echo(isset($trainerId) ? 'แก้ไขข้อมูลวิทยากร' : 'เพิ่มวิทยากร'); ?> - ผิดพลาด',
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

    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- CK Editor -->
    <script src="../bower_components/ckeditor/ckeditor.js"></script>
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