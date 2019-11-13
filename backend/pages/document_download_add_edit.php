<?php
require_once '../include/head_php.inc';

$id = $_GET['id'];

$documentType = $_GET['document_type'];
$documentTypeList = array(
    'training', 'km', 'report', 'qa', 'booklet'
);
if (!isset($documentType) || !in_array($documentType, $documentTypeList)) {
    echo "Invalid document type '$documentType' - ระบุประเภทเอกสารไม่ถูกต้อง";
    $db->close();
    exit();
}

$pageTitles['training'] = 'เอกสารการอบรม';
$pageTitles['km'] = ' KM';
$pageTitles['report'] = 'รายงานผลการดำเนินงาน';
$pageTitles['qa'] = 'ประกันคุณภาพ (QA)';
$pageTitles['booklet'] = 'จุลสาร/อินโฟกราฟิค';

$pageTitle = $pageTitles[$documentType];

$documentDownload = array();
if (isset($id)) {
    $id = $db->real_escape_string($id);

    $sql = "SELECT * FROM document_download WHERE id = $id";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $documentDownload = $result->fetch_assoc();
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
        <!--Lightbox-->
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">

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
    <body class="hold-transition skin-blue sidebar-mini fixed">

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?= (isset($id) ? 'แก้ไข' : 'เพิ่ม'); ?>เอกสารดาวน์โหลด
                    <small><?= $pageTitle; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddEditDocumentDownload"
                      action="../api/api.php/<?= (empty($documentDownload) ? 'add_document_download' : 'update_document_download'); ?>"
                      method="post" enctype=multipart/form-data>

                    <input type="hidden" name="document_type" value="<?= $documentType; ?>"/>
                    <input type="hidden" name="id" value="<?= $documentDownload['id']; ?>"/>

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

                                    <!--ชื่อเรื่อง-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputTitle">ชื่อเรื่อง:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle" name="title"
                                                           value="<?= (!empty($documentDownload) ? $documentDownload['title'] : ''); ?>"
                                                           placeholder="กรอกชื่อเรื่อง" required
                                                           maxlength="80"
                                                           oninvalid="this.setCustomValidity('กรอกชื่อเรื่อง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--รายละเอียดแบบย่อ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputTitle">รายละเอียดแบบย่อ:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputShortDescription" name="short_description"
                                                           value="<?= (!empty($documentDownload) ? $documentDownload['short_description'] : ''); ?>"
                                                           placeholder="กรอกรายละเอียดแบบย่อ" required
                                                           maxlength="150"
                                                           oninvalid="this.setCustomValidity('กรอกรายละเอียดแบบย่อ')"
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
                            $documentTypeWithImageCoverList = array(
                                'km', 'qa', 'booklet'
                            );
                            if (in_array($documentType, $documentTypeWithImageCoverList)) {
                                ?>
                                <!--รูปภาพหน้าปก-->
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">รูปภาพหน้าปก
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
                                        if (!empty($documentDownload)) {
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
                                                            <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); */?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                        </div>
                                                        <a href="<?= (UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); ?>"
                                                           data-lightbox="coverImage" data-title="<?= $documentDownload['title']; ?>">
                                                            <img src="<?= (UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); ?>"
                                                                 width="600px">
                                                        </a>
                                                    </div>
                                                    <!-- /.tab-pane -->
                                                    <div class="tab-pane" id="image_tab_2" style="padding: 0px">
                                                        <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                            <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                        </ul>
                                                        <input id="image-file-upload" name="image_file"
                                                               type="file" accept="image/*"
                                                               style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                                               onChange="getImageFileData(this);"/>
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
                                            <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                            </ul>
                                            <input id="image-file-upload" name="image_file" required
                                                   type="file" accept="image/*"
                                                   style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                                   onChange="getImageFileData(this);"
                                                   oninvalid="this.setCustomValidity('เลือกรูปภาพ')"
                                                   oninput="this.setCustomValidity('')"/>
                                            <div id="image-upload-preview"
                                                 style="background: #efffd1; padding: 10px;"></div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- /.box -->
                                <?php
                            }
                            ?>

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
                                    if (!empty($documentDownload)) {
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
                                                        <a target="_blank" href="<?php echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['file_name']); ?>">แสดง PDF ในหน้าจอใหม่</a>
                                                    </div>
                                                    <embed src="<?php echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['file_name']); ?>"
                                                           width="600px" height="450px">
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="pdf_tab_2" style="padding: 0px">
                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                        <li>ไฟล์ PDF ที่อัพโหลดใหม่ จะแทนที่ไฟล์ PDF ปัจจุบัน</li>
                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                    </ul>
                                                    <input id="file-upload" name="file"
                                                           type="file" accept="application/pdf"
                                                           style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                                           onChange="getFileData(this);"/>
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
                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                        </ul>
                                        <input id="file-upload" name="file" required
                                               type="file" accept="application/pdf"
                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                               onChange="getFileData(this);"
                                               oninvalid="this.setCustomValidity('เลือกไฟล์ PDF')"
                                               oninput="this.setCustomValidity('')"/>
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
            $('#formAddEditDocumentDownload #divLoading').hide();

            $('#formAddEditDocumentDownload').submit(event => {
                event.preventDefault();
                doAddEditDocumentDownload();
            });

            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });
        });

        function getFileData(element) {
            /*const fileName = element.files[0].name;
            $('#spanFileName').text(fileName);*/
        }

        function getImageFileData(element) {
            /*const fileName = element.files[0].name;
            $('#spanFileName').text(fileName);*/
        }

        $(function () {
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
                            $($.parseHTML('<img style="width: auto; height: 120px; margin: 3px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        };
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };

            $('#image-file-upload').on('change', function () {
                imagesPreview(this, 'div#image-upload-preview');
            });
        });

        function doAddEditDocumentDownload() {
            $('#formAddEditDocumentDownload #buttonSave').prop('disabled', true);
            $('#formAddEditDocumentDownload #divLoading').show();

            $('#formAddEditDocumentDownload').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {

                    console.log(data);

                    $('#formAddEditDocumentDownload #buttonSave').prop('disabled', false);
                    $('#formAddEditDocumentDownload #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?= (isset($id) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($id)) {
                                    ?>
                                    window.location.href = 'document_download.php?document_type=<?= $documentType; ?>';
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
                            title: '<?= (isset($id) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?> - ผิดพลาด',
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
                    $('#formAddEditDocumentDownload #buttonSave').prop('disabled', false);
                    $('#formAddEditDocumentDownload #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?= (isset($id) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?> - ผิดพลาด',
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
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>