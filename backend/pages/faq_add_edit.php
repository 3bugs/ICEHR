<?php
require_once '../include/head_php.inc';

$faqId = $_GET['faq_id'];

$pageTitle = 'คำถามที่พบบ่อย';

$faq = array();
if (isset($faqId)) {
    $faqId = $db->real_escape_string($faqId);

    $sql = "SELECT * FROM faq WHERE id = $faqId";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $faq = $result->fetch_assoc();
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
                    <?= (isset($faqId) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddFaq"
                      action="../api/api.php/<?= (isset($faqId) ? 'update' : 'add'); ?>"
                      method="post">

                    <input type="hidden" name="tableName" value="faq"/>
                    <input type="hidden" name="id" value="<?= $faqId; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

                            <!--หัวข้อ-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">คำถาม (Q)</h3>

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
                                                <label for="inputTitle">คำถาม:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span style="font-weight: bold">Q</span>
                                                </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle"
                                                           name="title"
                                                           value="<?=(!empty($faq) ? $faq['title'] : ''); ?>"
                                                           placeholder="กรอกคำถาม" required
                                                           oninvalid="this.setCustomValidity('กรอกคำถาม')"
                                                           oninput="this.setCustomValidity('')">
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
                                    <h3 class="box-title">คำตอบ (A)
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
                                          name="details" required
                                          oninvalid="this.setCustomValidity('กรอกคำตอบ')"
                                          oninput="this.setCustomValidity('')">
                                    <?= (!empty($faq) ? $faq['details'] : ''); ?>
                                </textarea>
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
            CKEDITOR.replace('editor');
        });

        $(document).ready(function () {
            $('#formAddFaq #divLoading').hide();

            $('#formAddFaq').submit(event => {
                event.preventDefault();
                doAddEditFaq();
            });
        });

        function doAddEditFaq() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            CKEDITOR.instances.editor.updateElement();

            $('#formAddFaq #buttonSave').prop('disabled', true);
            $('#formAddFaq #divLoading').show();

            $('#formAddFaq').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddFaq #buttonSave').prop('disabled', false);
                    $('#formAddFaq #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?=(isset($faqId) ? 'แก้ไข' : 'เพิ่ม'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($faqId)) {
                                    ?>
                                    window.location.href = 'faq.php';
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
                            title: '<?=(isset($faqId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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
                    $('#formAddFaq #buttonSave').prop('disabled', false);
                    $('#formAddFaq #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?=(isset($faqId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>