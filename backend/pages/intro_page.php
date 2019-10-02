<?php
require_once '../include/head_php.inc';

$pageTitle = 'Intro Page';

$item = null;
$sql = "SELECT * FROM intro WHERE type = 'intro'";

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
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
                    <?= $pageTitle; ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddEdit"
                      action="../api/api.php/add_update_intro_page"
                      method="post">

                    <input type="hidden" name="id" value="<?= $item == null ? '0' : $item['id']; ?>"/>
                    <input type="hidden" name="type" value="intro"/>

                    <div class="row">
                        <div class="col-xs-12">
                            <!--Link-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Link</h3>

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

                                    <!--Link-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputUrl">Link:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-link"></i>
                                                </span>
                                                    <input type="text" class="form-control"
                                                           id="inputUrl" name="url"
                                                           value="<?php echo(!is_null($item) ? $item['url'] : ''); ?>"
                                                           placeholder="กรอก Link"
                                                           oninvalid="this.setCustomValidity('กรอก Link')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->

                            <?php
                            if (FALSE) {
                                ?>
                                <!--content editor-->
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">รายละเอียด
                                            <small>&nbsp;</small>
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
                                          name="details" required>
                                    <?= (!is_null($item) ? $item['details'] : ''); ?>
                                </textarea>
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
                                <div class="box-body pad" style="background_: #f8f8f8">
                                    <?php
                                    if (!is_null($item)) {
                                        ?>
                                        <!-- Custom Tabs -->
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#cover_image_tab_1" data-toggle="tab">รูปภาพปัจจุบัน</a></li>
                                                <li><a href="#cover_image_tab_2" data-toggle="tab">อัพโหลดรูปภาพใหม่</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="cover_image_tab_1">
                                                    <div style="padding: 5px">
                                                        <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); */ ?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                    </div>
                                                    <a href="<?= (UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); ?>"
                                                       data-lightbox="coverImage" data-title="<?= $item['title']; ?>">
                                                        <img src="<?= (UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); ?>"
                                                             width="600px">
                                                    </a>
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="cover_image_tab_2" style="padding: 0px">
                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                        <?php
                                                        if ($type === TYPE_BANNER) {
                                                            ?>
                                                            <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง = 2:1 เช่น
                                                                800x400 แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                        <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                    </ul>
                                                    <input id="cover-image-file-upload" name="image"
                                                           type="file" accept="image/*"
                                                           style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                    <div id="cover-image-upload-preview"
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
                                        <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                            <?php
                                            if ($type === TYPE_BANNER) {
                                                ?>
                                                <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง = 2:1 เช่น 800x400
                                                    แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)
                                                </li>
                                                <?php
                                            }
                                            ?>
                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                        </ul>
                                        <input id="cover-image-file-upload" name="image" required
                                               type="file" accept="image/*"
                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                               oninvalid="this.setCustomValidity('เลือกรูปภาพ')"
                                               oninput="this.setCustomValidity('')"/>
                                        <div id="cover-image-upload-preview"
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
        /*$(() => {
            CKEDITOR.replace('editor');
        });*/

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });

            $('#formAddEdit #divLoading').hide();

            $('#formAddEdit').submit(event => {
                event.preventDefault();
                doAddEdit();
            });
        });

        $(function () {
            $('#cover-image-upload-preview').hide();

            const coverImagePreview = function (input, placeToInsertImagePreview) {
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

            $('#cover-image-file-upload').on('change', function () {
                coverImagePreview(this, 'div#cover-image-upload-preview');
            });
        });

        function doAddEdit() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            /*if (CKEDITOR.instances.editor) {
                CKEDITOR.instances.editor.updateElement();
            }*/

            $('#formAddEdit #buttonSave').prop('disabled', true);
            $('#formAddEdit #divLoading').show();

            $('#formAddEdit').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddEdit #buttonSave').prop('disabled', false);
                    $('#formAddEdit #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?= $pageTitle; ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    window.location.reload(true);
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.show({
                            title: '<?= $pageTitle; ?> - ผิดพลาด',
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
                    $('#formAddEdit #buttonSave').prop('disabled', false);
                    $('#formAddEdit #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?= $pageTitle; ?> - ผิดพลาด',
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
    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- CK Editor -->
    <!--<script src="../bower_components/ckeditor/ckeditor.js"></script>-->
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>