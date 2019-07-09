<?php
require_once '../include/head_php.inc';

$itemId = $_POST['itemId'];
$item = array();
$media_cover = array();
if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);
    /* Item */
    $sql = "SELECT * FROM article WHERE id=$itemId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item['id'] = (int)$row['id'];
            $item['title'] = $row['title'];
            $item['status'] = $row['status'];
            $item['meta_title'] = $row['meta_title'];
            $item['meta_keywords'] = $row['meta_keywords'];
            $item['meta_description'] = $row['meta_description'];
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }

    /* Media Cover */
    $sql_media_cover = "SELECT * FROM media WHERE model_id=$itemId AND collection_name ='cover_desktop' AND model_type='organizational_structure'";    
    if ($result = $db->query($sql_media_cover)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $media_cover['id'] = (int)$row['id'];
            $media_cover['name'] = $row['name'];
            $media_cover['file_name'] = $row['file_name'];
            $media_cover['mime_type'] = $row['mime_type'];
            $media_cover['collection_name'] = $row['collection_name'];
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }

}



function pathUrl($dir = __DIR__){
    $root = "";
    $dir = str_replace('\\', '/', realpath($dir));

    //HTTPS or HTTP
    $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http';

    //HOST
    $root .= '://' . $_SERVER['HTTP_HOST'];

    //ALIAS
    // if(!empty($_SERVER['CONTEXT_PREFIX'])) {
    //     $root .= $_SERVER['CONTEXT_PREFIX'];
    //     $root .= substr($dir, strlen($_SERVER[ 'CONTEXT_DOCUMENT_ROOT' ]));
    // } else {
    //     $root .= substr($dir, strlen($_SERVER[ 'DOCUMENT_ROOT' ]));
    // }

    $root .= '/';
    return $root;
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
                    <?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>โครงสร้างองค์กร
                    <small>โครงสร้างองค์กร</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddOrganizationalStructure" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียดโครงสร้างองค์กร-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดโครงสร้างองค์กร</h3>

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

                                    <!--ชื่อโครงสร้างองค์กร-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อโครงสร้างองค์กร:</label>
                                                <input type="text" name="title" value="<?php echo (!empty($item) ? $item['title']:''); ?>" maxlength="50" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    
                            <!-- ปภาพหน้าปก -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รูปภาพหน้าปก
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
                                    
                                    <!--รูปภาพหน้าปก-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group" id="cover_desktop">
                                                    <div id="DropzoneCoverDesktop" class="dropzone">
  
                                                    </div>
                                                    <div>
                                                        <?php 
                                                            if(!empty($media_cover)):
                                                            $path_cover = pathUrl()."static/media/".$media_cover['file_name'];
                                                            //echo $path_cover;
                                                        ?>  
                                                            1.<img src="<?php echo $path_cover; ?>" style="width:50%;height:100%;padding:20px"><a data-id="<?php echo $media_cover['id']; ?>" data-filename="<?php echo $media_cover['file_name']; ?>" class="btn btn-info btn-remove-images" style="margin-left:35px">ลบ</a>
                                                        <?php 
                                                            endif; 
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.box -->

                           
                            <!-- SEO -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">SEO
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

                                    <!--Meta Title-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputMetaTitle">Meta Title:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                    <input name="meta_title" type="text" value="<?php echo (!empty($item) ? $item['meta_title']:''); ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Meta Keywords-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputMetaKeywords">Meta Keywords:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                    <input name="meta_keywords" type="text" value="<?php echo (!empty($item) ? $item['meta_keywords']:''); ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Meta Description-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputMetaDescription">Meta Description:</label>
                                                <div class="input-group">   
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span> 
                                                    <textarea name="meta_description" class="form-control"><?php echo (!empty($item) ? $item['meta_description']:''); ?></textarea>
                                                </div>
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
        Dropzone.autoDiscover = false;
        
        $(() => {
            //Date picker
            CKEDITOR.replace('editor');
        });


        $(document).ready(function () {

            $("#DropzoneCoverDesktop").dropzone({
                
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 2, // MB
                maxFiles:1,
                acceptedFiles: ".png,.jpg,.jpeg",
                url: "../pages/upload-target.php",
                sending:function(file, xhr, formData){

                    let date_now = new Date();
                    let file_name = date_now.getDate()+'-'+date_now.getMonth()+'-'+date_now.getFullYear()+'-'+date_now.getHours()+'-'+date_now.getMinutes()+'-'+date_now.getMilliseconds()+'.'+file.name.split(".")[1];
                    let name = file.name.split(".")[0];
                    formData.append('file_name',file_name);
                    formData.append('name',name);

                    file.custom_file_name = file_name;
                    file.custom_name = name;
                  
                    //console.log(file);

                    html = '<input name="cover_desktop_file_name" type="hidden" value="'+file.custom_file_name+'">';
                    html += '<input name="cover_desktop_name" type="hidden" value="'+file.custom_name+'" >';
                    html += '<input name="cover_desktop_size" type="hidden" value="'+file.size+'" >';
                    html += '<input name="cover_desktop_type" type="hidden" value="'+file.type+'" >';
                    $('#cover_desktop').append(html);

                },
                accept: function(file, done) {
                    //#add_galleryconsole.log(file,file.myCustomName);
                    ///console.log(file);
                    done();
                    //if (file.type != "image/jpeg" && file.type != "image/png") {}
                }

            });


        
            $("#formAddOrganizationalStructure").validate({
                rules:{
                    title: {
                    required: true,
                    minlength: 3
                    }
                },
                messages: {
                    title: {
                    required: "ต้องระบุชื่อโครงสร้างองค์กร",
                    minlength: "ชื่อโครงสร้างองค์กร ควรมีความยาวอยู่ระหว่าง 3-50 ตัวอักษร"
                    }
                },
                submitHandler: function(form) {
                    //console.log("Testdasdadsa");
                    doAddOrganizationalStructure();
                }
            });

 
            $('.btn-remove-images').click(function(){

                id = $(this).attr('data-id');
                file_name = $(this).attr('data-filename');
                //delete_media
                //console.log(id,file_name);

                $.ajax({
                    type: "POST",
                    url: '../api/api.php/delete_media',
                    dataType: 'json',
                    data: {id:id,file_name:file_name},
                    timeout: 600000,
                    success: function (data) {

                        //console.log(data);

                        if (data.error_code === 0) {

                            BootstrapDialog.show({
                                title: 'โครงสร้างองค์กร',
                                message: data.error_message,
                                buttons: [{
                                    label: 'ปิด',
                                    action: function (self) {
                                        self.close();
                                    }
                                }]
                            });
                            location.reload();

                        } else {

                            BootstrapDialog.show({
                                title: 'โครงสร้างองค์กร - ผิดพลาด',
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

            });

        });

        function doAddOrganizationalStructure() {

            var form = $('#formAddOrganizationalStructure')[0];
            var data = new FormData(form);
            //console.log(data.entries());



            $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: '../api/api.php/<?php echo(isset($itemId) ? 'update_organizational_structure' : 'add_organizational_structure'); ?>',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                //console.log(data);

                if (data.error_code === 0) {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขโครงสร้างองค์กร' : 'เพิ่มโครงสร้างองค์กร'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });

                    window.location.href = 'organizational_structure.php';

                } else {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขโครงสร้างองค์กร' : 'เพิ่มโครงสร้างองค์กร'); ?> - ผิดพลาด',
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