<?php
require_once '../include/head_php.inc';

$itemId = $_POST['itemId'];
$item = array();
if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);
    /* Item */
    $sql = "SELECT * FROM faq WHERE id=$itemId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item['id'] = (int)$row['id'];
            $item['title'] = $row['title'];
            $item['description'] = $row['description'];
            $item['status'] = $row['status'];
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
        <style>
              .error{
                  color:red;
              }
        </style>
        <script src="../bower_components/dropzone-master/dropzone.js"></script>
        <link rel="stylesheet" href="../bower_components/dropzone-master/dropzone.css">
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
                    <?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>คำถามที่พบบ่อย
                    <small>คำถามที่พบบ่อย</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddFaq" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียดคำถามที่พบบ่อย-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดคำถามที่พบบ่อย</h3>

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

                                    <!--ชื่อคำถามที่พบบ่อย-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อคำถามที่พบบ่อย:</label>
                                                <input type="text" name="title" value="<?php echo (!empty($item) ? $item['title']:''); ?>" maxlength="50" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- รายละเอียด -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">คำตอบ:</label>
                                                <div class="box-body pad">
                                                    <textarea name="description" id="editor" rows="20" cols="80" class="form-control"><?php echo (!empty($item) ? $item['description']:''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <!-- /.box -->

                            <!--การตั้งค่า-->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">การตั้งค่า
                                        <small></small>
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
                                <div class="box-body">

                                    <!--สถานะ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="selectStatus">สถานะ:</label>
                                                <div class="input-group">
                                                    <select name="status" class="form-control" required>
                                                            <option value="publish" <?php echo (!empty($item) ? ($item['status'] == 'publish' ? 'selected':''):''); ?> >เผยแพร่</option>
                                                            <option value="draft" <?php echo (!empty($item) ? ($item['status'] == 'draft' ? 'selected':''):''); ?> >ไม่เผยแพร่</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
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
        <input name="uid" type="hidden" value="<?php echo $_SESSION[KEY_SESSION_USER_ID]; ?>">
        <?php 
            if(isset($itemId)):
        ?>
        <input name="itemId" type="hidden" value="<?php echo $itemId ?>">
        <?php
            endif;
        ?>
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

            $("#formAddFaq").validate({
                rules:{
                    title: {
                    required: true,
                    minlength: 3
                    },
                    description: {
                    required: true
                    }
                },
                messages: {
                    title: {
                    required: "ต้องระบุชื่อคำถามที่พบบ่อย",
                    minlength: "ชื่อคำถามที่พบบ่อย ควรมีความยาวอยู่ระหว่าง 3-50 ตัวอักษร"
                    },
                    description: {
                    required: "ต้องระบุคำตอบ"
                    }
                },
                submitHandler: function(form) {
                    //console.log("Testdasdadsa");
                    doAddFaq();
                }
            });

        });

        function doAddFaq() {

            $('textarea[name="description"]').text(CKEDITOR.instances.editor.getData());
            var form = $('#formAddFaq')[0];
            var data = new FormData(form);
            //console.log(data.entries());

            $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: '../api/api.php/<?php echo(isset($itemId) ? 'update_faq' : 'add_faq'); ?>',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                //console.log(data);

                if (data.error_code === 0) {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขคำถามที่พบบ่อย' : 'เพิ่มคำถามที่พบบ่อย'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });

                    window.location.href = 'faq.php';

                } else {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขคำถามที่พบบ่อย' : 'เพิ่มคำถามที่พบบ่อย'); ?> - ผิดพลาด',
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
            error: function (e) {
                console.log("ERROR : ", e);
            }

            });


        }



    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- CK Editor -->
    <script src="../bower_components/ckeditor/ckeditor.js"></script>
    <script src="../bower_components/jquery-validation-1.19.0/dist/jquery.validate.js"></script>


    </body>
    </html>

<?php
$db->close();
?>