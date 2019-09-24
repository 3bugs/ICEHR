<?php
require_once '../include/head_php.inc';

define('TYPE_ABOUT', 'about');
define('TYPE_MISSION', 'mission');
define('TYPE_BANNER', 'banner');

$itemId = $_POST['id'];

$type = $_POST['type'];
$typeList = array(
    TYPE_ABOUT, TYPE_MISSION, TYPE_BANNER
);
if (!isset($type) || !in_array($type, $typeList)) {
    echo "Invalid type '$type' - ระบุประเภทไม่ถูกต้อง";
    $db->close();
    exit();
}

$pageTitleList[TYPE_ABOUT] = 'เกี่ยวกับองค์กร';
$pageTitleList[TYPE_MISSION] = 'ภารกิจ';
$pageTitleList[TYPE_BANNER] = 'แบนเนอร์';
$pageTitle = $pageTitleList[$type];

$hasCoverImageList[TYPE_ABOUT] = FALSE;
$hasCoverImageList[TYPE_MISSION] = TRUE;
$hasCoverImageList[TYPE_BANNER] = TRUE;
$hasCoverImage = $hasCoverImageList[$type];

$item = array();
if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);

    $sql = "SELECT * FROM intro WHERE id = $itemId";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
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
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
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
                    <?= (isset($itemId) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddEdit"
                      action="../api/api.php/<?= (isset($itemId) ? 'update_intro' : 'add_intro'); ?>"
                      method="post">

                    <input type="hidden" name="id" value="<?php echo $itemId; ?>"/>
                    <input type="hidden" name="type" value="<?php echo $type; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

                            <?php
                            if (TRUE /*$type === TYPE_ABOUT || $type === TYPE_MISSION*/) {
                                ?>
                                <!--หัวข้อ-->
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?= $pageTitle; ?></h3>

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

                                        <!--หัวข้อ/เรื่อง-->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="inputTitle">หัวข้อ/เรื่อง:</label>
                                                    <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                        <input type="text" class="form-control"
                                                               id="inputTitle" name="title"
                                                               value="<?php echo(!empty($item) ? $item['title'] : ''); ?>"
                                                               placeholder="กรอกหัวข้อ/เรื่อง" required
                                                               oninvalid="this.setCustomValidity('กรอกหัวข้อ/เรื่อง')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                            if ($type === TYPE_BANNER) {
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="inputSubTitle">หัวข้อรอง:</label>
                                                        <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                            <input type="text" class="form-control"
                                                                   id="inputSubTitle" name="subTitle"
                                                                   value="<?php echo(!empty($item) ? $item['sub_title'] : ''); ?>"
                                                                   placeholder="กรอกหัวข้อรอง" required
                                                                   oninvalid="this.setCustomValidity('กรอกหัวข้อรอง')"
                                                                   oninput="this.setCustomValidity('')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                                <?php
                            }
                            ?>

                            <?php
                            if ($type === TYPE_BANNER) {
                                ?>
                                <!--Link-->
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Link แบนเนอร์</h3>

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

                                        <!--แบนเนอร์-->
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
                                                               value="<?php echo(!empty($item) ? $item['url'] : ''); ?>"
                                                               placeholder="กรอก Link ของแบนเนอร์" required
                                                               oninvalid="this.setCustomValidity('กรอก Link ของแบนเนอร์')"
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
                            }
                            ?>

                            <?php
                            if (TRUE /*$type === TYPE_ABOUT || $type === TYPE_MISSION*/) {
                                ?>
                                <!--content editor-->
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">รายละเอียด<?= $type === TYPE_BANNER ? 'แบบย่อ' : ''; ?>
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
                                    <?= (!empty($item) ? $item['details'] : ''); ?>
                                </textarea>
                                    </div>
                                </div>
                                <!-- /.box -->
                                <?php
                            }
                            ?>

                            <!--รูปภาพ-->
                            <?php
                            if ($hasCoverImage) {
                                ?>
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
                                        if (!empty($item)) {
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
                                                                <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง = 2:1 เช่น 800x400 แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)</li>
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
                                                    <li>ภาพแบนเนอร์ ให้ใช้ภาพ JPG ขนาด 1000x500 (กว้าง 1,000 pixel สูง 500 pixel) หรือขนาดอื่นที่มีอัตราส่วนความกว้าง:ความสูง = 2:1 เช่น 800x400 แต่ไม่ควรมีขนาดเล็กเกินไป (ภาพจะไม่คมชัด) หรือใหญ่เกินไป (จะใช้เวลาโหลดภาพนาน)</li>
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
                                <?php
                            }
                            ?>
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
            CKEDITOR.replace('editor');
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
                doAddEditNews();
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

        function doAddEditNews() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            if (CKEDITOR.instances.editor) {
                CKEDITOR.instances.editor.updateElement();
            }

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
                            title: '<?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($itemId)) {
                                    ?>
                                    window.location.href = 'intro.php?type=<?php echo $type; ?>';
                                    <?php
                                    } else {
                                    ?>
                                    window.location.href = 'intro.php?type=<?php echo $type; ?>';
                                    //window.location.reload(true);
                                    <?php
                                    }
                                    ?>
                                }
                            }]
                        });
                    } else {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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
                        title: '<?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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