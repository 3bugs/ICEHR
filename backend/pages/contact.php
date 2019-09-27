<?php
require_once '../include/head_php.inc';

$pageTitle = 'ติดต่อ';
$hasCoverImage = TRUE;

$contactList = array();
$sql = "SELECT * FROM intro WHERE type = 'contact' ORDER BY id";

if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        array_push($contactList, $row);
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
                      action="../api/api.php/add_update_contact"
                      method="post">

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab">ท่าพระจันทร์</a></li>
                            <li><a href="#tab_2" data-toggle="tab">รังสิต</a></li>
                        </ul>
                        <div class="tab-content">
                            <?php
                            for ($i = 0; $i < 2; $i++) {
                                $contact = ((sizeof($contactList) > $i) ? $contactList[$i] : null);
                                ?>
                                <div class="tab-pane <?= $i === 0 ? 'active' : ''; ?>" id="tab_<?= $i + 1; ?>">
                                    <input type="hidden" name="id[]" value="<?= $contact ? $contact['id'] : '0'; ?>"/>

                                    <div class="row">
                                        <div class="col-xs-12">

                                            <!--content editor-->
                                            <div class="box box-warning" style="margin-top: 10px">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">ที่อยู่
                                                        <small><?= $i === 0 ? 'ท่าพระจันทร์' : 'รังสิต'; ?></small>
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
                                                    <div class="form-group" style="margin-top: 5px">
                                                        <label for="editor<?= $i; ?>">ที่อยู่ในหน้า 'ติดต่อ'</label>
                                                        <textarea id="editor<?= $i; ?>" rows="20" cols="80"
                                                                  name="details[]" required>
                                                            <?= $contact ? $contact['details'] : ''; ?>
                                                        </textarea>
                                                    </div>

                                                    <div class="form-group" style="margin-top: 15px">
                                                        <label for="inputSubTitle<?= $i; ?>">ที่อยู่+เบอร์โทร ตรง Footer</label>
                                                        <textarea id="inputSubTitle<?= $i; ?>" rows="3" cols="80"
                                                                  name="subTitle[]" required
                                                                  oninvalid="this.setCustomValidity('กรอกที่อยู่+เบอร์โทร (แสดงที่ Footer)')"
                                                                  oninput="this.setCustomValidity('')"
                                                                  style="width: 100%">
                                                            <?= $contact ? $contact['sub_title'] : ''; ?>
                                                        </textarea>

                                                        <!--<input type="text" class="form-control"
                                                                   id="inputSubTitle<?/*= $i; */ ?>" name="subTitle[]"
                                                                   value="<?/*= $contact ? $contact['sub_title'] : ''; */ ?>"
                                                                   placeholder="กรอกที่อยู่" required
                                                                   oninvalid="this.setCustomValidity('กรอกที่อยู่')"
                                                                   oninput="this.setCustomValidity('')">-->
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.box -->

                                            <!--รูปภาพ-->
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">รูปภาพ
                                                        <small><?= $i === 0 ? 'ท่าพระจันทร์' : 'รังสิต'; ?></small>
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
                                                    if ($contact) {
                                                        ?>
                                                        <!-- Custom Tabs -->
                                                        <div class="nav-tabs-custom">
                                                            <ul class="nav nav-tabs">
                                                                <li class="active"><a href="#cover_image_tab_1<?= $i; ?>" data-toggle="tab">รูปภาพปัจจุบัน</a></li>
                                                                <li><a href="#cover_image_tab_2<?= $i; ?>" data-toggle="tab">อัพโหลดรูปภาพใหม่</a></li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane active" id="cover_image_tab_1<?= $i; ?>">
                                                                    <div style="padding: 5px">
                                                                        <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_INTRO_ASSETS . $item['image_file_name']); */ ?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                                    </div>
                                                                    <a href="<?= (UPLOAD_DIR_INTRO_ASSETS . $contact['image_file_name']); ?>"
                                                                       data-lightbox="coverImage" data-title="<?= $contact['title']; ?>">
                                                                        <img src="<?= (UPLOAD_DIR_INTRO_ASSETS . $contact['image_file_name']); ?>"
                                                                             width="300px">
                                                                    </a>
                                                                </div>
                                                                <!-- /.tab-pane -->
                                                                <div class="tab-pane" id="cover_image_tab_2<?= $i; ?>" style="padding: 0px">
                                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                                        <?php
                                                                        if ($type === TYPE_BANNER) {
                                                                            ?>
                                                                            <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง =
                                                                                2:1 เช่น 800x400
                                                                                แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                                        <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                                    </ul>
                                                                    <input id="cover-image-file-upload<?= $i; ?>" name="image<?= $i; ?>"
                                                                           type="file" accept="image/*"
                                                                           style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                                    <div id="cover-image-upload-preview<?= $i; ?>"
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
                                                                <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง = 2:1 เช่น
                                                                    800x400
                                                                    แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)
                                                                </li>
                                                                <?php
                                                            }
                                                            ?>
                                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                        </ul>
                                                        <input id="cover-image-file-upload<?= $i; ?>" name="image<?= $i; ?>"
                                                               type="file" accept="image/*" required
                                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                                               oninvalid="this.setCustomValidity('เลือกรูปภาพ')"
                                                               oninput="this.setCustomValidity('')"/>
                                                        <div id="cover-image-upload-preview<?= $i; ?>"
                                                             style="background: #efffd1; padding: 10px;"></div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <!-- /.box -->

                                            <!--Google Maps Link-->
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Link แผนที่ Google Maps
                                                        <small><?= $i === 0 ? 'ท่าพระจันทร์' : 'รังสิต'; ?></small>
                                                    </h3>

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
                                                                <label for="inputUrl<?= $i; ?>">Link:</label>
                                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-map"></i>
                                                </span>
                                                                    <input type="text" class="form-control"
                                                                           id="inputUrl<?= $i; ?>" name="url[]"
                                                                           value="<?= $contact ? $contact['url'] : ''; ?>"
                                                                           placeholder="กรอก Link" required
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

                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- /.tab-pane -->
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- nav-tabs-custom -->

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
            CKEDITOR.replace('editor0');
            CKEDITOR.replace('editor1');
            CKEDITOR.replace('inputSubTitle0');
            CKEDITOR.replace('inputSubTitle1');
        });

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });

            $('#formAddEdit #divLoading').hide();

            $('#formAddEdit').submit(event => {
                event.preventDefault();
                doAddEditContact();
            });
        });

        $(function () {
            $('#cover-image-upload-preview0').hide();
            $('#cover-image-upload-preview1').hide();

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

            $('#cover-image-file-upload0').on('change', function () {
                coverImagePreview(this, 'div#cover-image-upload-preview0');
            });
            $('#cover-image-file-upload1').on('change', function () {
                coverImagePreview(this, 'div#cover-image-upload-preview1');
            });
        });

        function doAddEditContact() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            CKEDITOR.instances.editor0.updateElement();
            CKEDITOR.instances.editor1.updateElement();
            CKEDITOR.instances.inputSubTitle0.updateElement();
            CKEDITOR.instances.inputSubTitle1.updateElement();

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
                            title: 'ติดต่อ',
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
                            title: 'ติดต่อ - ผิดพลาด',
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
                        title: 'ติดต่อ - ผิดพลาด',
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
    <script src="../bower_components/ckeditor/ckeditor.js"></script>
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>