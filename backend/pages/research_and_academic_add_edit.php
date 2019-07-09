<?php
require_once '../include/head_php.inc';

$itemId = $_POST['itemId'];
$item = array();
$document = array();
if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);
    /* Item */
    $sql = "SELECT * FROM research_and_academic WHERE id=$itemId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item['id'] = (int)$row['id'];
            $item['title'] = $row['title'];
            $item['description'] = $row['description'];
            $item['published_year'] = $row['published_year'];
            $item['researcher_name'] = $row['researcher_name'];
            $item['research_funding_source'] = $row['research_funding_source'];
            $item['featured'] = (int)$row['featured'];
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


    /* Document */
    $sql_documents = "SELECT * FROM documents_download WHERE article_id=$itemId AND document_type='research_and_academic'";    
    if ($result = $db->query($sql_documents)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($row as $key => $value) {
                $document[$key]['id'] = (int)$value['id'];
                $document[$key]['file_name'] = $value['file_name'];
                $document[$key]['file_path'] = $value['file_path'];
            }
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }


}


// $target_path = $_SERVER['DOCUMENT_ROOT'] . "static/media/test.docx";

// if (!unlink($target_path))
// {
//    echo ("$file_pointer cannot be deleted due to an error");
// }
// else
// {
//    echo ("$file_pointer has been deleted");
// }

// echo $target_path;
// exit();



// echo $itemId;
// echo "<pre>";
//      print_r($item);
//      print_r($media_cover);
//      print_r($media_gallery);
//      print_r($document);
// echo "</pre>";

// exit();


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
                    <?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>โครงการวิจัยและวิชาการ
                    <small>โครงการวิจัยและวิชาการ</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddResearchAndAcademic" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียดโครงการวิจัยและวิชาการ-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดโครงการวิจัยและวิชาการ</h3>

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

                                    <!--ชื่อโครงการวิจัยและวิชาการ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อโครงการวิจัยและวิชาการ:</label>
                                                <input type="text" name="title" value="<?php echo (!empty($item) ? $item['title']:''); ?>" maxlength="50" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- รายละเอียด -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">รายละเอียด:</label>
                                                <div class="box-body pad">
                                                    <textarea name="description" id="editor" rows="20" cols="80" class="form-control"><?php echo (!empty($item) ? $item['description']:''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!--ปีที่เผยแพร่ -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ปีที่เผยแพร่ :</label>
                                                <input type="number" name="published_year" value="<?php echo (!empty($item) ? $item['published_year']:''); ?>" maxlength="4" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!--ชื่อโครงการวิจัยและวิชาการ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อผู้ทำวิจัย:</label>
                                                <input type="text" name="researcher_name" value="<?php echo (!empty($item) ? $item['researcher_name']:''); ?>" maxlength="100" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!--ชื่อโครงการวิจัยและวิชาการ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">แหล่งทุนวิจัย:</label>
                                                <input type="text" name="research_funding_source" value="<?php echo (!empty($item) ? $item['research_funding_source']:''); ?>" maxlength="150" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    
                            </div>
                            <!-- /.box -->
     
                            <!-- ไฟล์แนบ -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ไฟล์แนบ
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

                                    <!--ไฟล์แนบ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group" id="document">
                                                        <div id="DropzoneDocument" class="dropzone">
    
                                                        </div>
                                                        <div>
                                                        <?php 
                                                            if(!empty($document)):
                                                                foreach ($document as $key => $value):
                                                                $path_document = pathUrl()."static/media/".$value['file_name'];
                                                                //echo $path_document;
                                                                //exit();
                                                        ?>  
                                                                <?php echo ($key+1) ?>.<a href="<?php echo $path_document; ?>" download><?php echo $value['file_name']; ?></a><a data-id="<?php echo $value['id']; ?>" data-filename="<?php echo $value['file_name']; ?>" class="btn btn-info btn-remove-documents" style="margin-left:35px">ลบ</a>
                                                                <br>
                                                        <?php
                                                                endforeach;
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

                                    <!--ไฮไลท์-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputPlace">ไฮไลท์:</label>
                                                <div class="input-group">
                                                    <input name="featured" type="radio" id="featured_1" value="1" <?php echo (!empty($item) ? ($item['featured'] == '1' ? 'checked':''):''); ?> > ไฮไลท์
                                                    <input name="featured" type="radio" id="featured_2" value="2" <?php echo (!empty($item) ? ($item['featured'] == '2' ? 'checked':''):'checked'); ?> > ไม่ไฮไลท์
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
        <input name="file_type" type="hidden" value="research_and_academic">
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

            $("#DropzoneDocument").dropzone({
                
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 50, // MB
                maxFiles:1,
                acceptedFiles: ".xlsx,.xls,.docx,.pdf",
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

                    html = '<input name="document_file_name[]" type="hidden" value="'+file.custom_file_name+'">';
                    html += '<input name="document_name[]" type="hidden" value="'+file.custom_name+'" >';
                    html += '<input name="document_size[]" type="hidden" value="'+file.size+'" >';
                    html += '<input name="document_type[]" type="hidden" value="'+file.type+'" >';
                    $('#document').append(html);

                },
                accept: function(file, done) {
                    //#add_galleryconsole.log(file,file.myCustomName);
                    ///console.log(file);
                    done();
                    //if (file.type != "image/jpeg" && file.type != "image/png") {}
                }

            });
            

            $("#formAddResearchAndAcademic").validate({
                rules:{
                    title: {
                    required: true,
                    minlength: 3
                    }
                },
                messages: {
                    title: {
                    required: "ต้องระบุชื่อโครงการวิจัยและวิชาการ",
                    minlength: "ชื่อโครงการวิจัยและวิชาการ ควรมีความยาวอยู่ระหว่าง 3-50 ตัวอักษร"
                    }
                },
                submitHandler: function(form) {
                    //console.log("Testdasdadsa");
                    doAddResearchAndAcademic();
                }
            });


            $('.btn-remove-documents').click(function(){

                id = $(this).attr('data-id');
                file_name = $(this).attr('data-filename');
                //delete_media
                //console.log(id,file_name);

                $.ajax({
                    type: "POST",
                    url: '../api/api.php/delete_document',
                    dataType: 'json',
                    data: {id:id,file_name:file_name},
                    timeout: 600000,
                    success: function (data) {

                        //console.log(data);

                        if (data.error_code === 0) {

                            BootstrapDialog.show({
                                title: 'โครงการวิจัยและวิชาการ',
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
                                title: 'โครงการวิจัยและวิชาการ - ผิดพลาด',
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

        function doAddResearchAndAcademic() {

            $('textarea[name="description"]').text(CKEDITOR.instances.editor.getData());
            var form = $('#formAddResearchAndAcademic')[0];
            var data = new FormData(form);
            //console.log(data.entries());



            $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: '../api/api.php/<?php echo(isset($itemId) ? 'update_research_and_academic' : 'add_research_and_academic'); ?>',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                //console.log(data);

                if (data.error_code === 0) {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขโครงการวิจัยและวิชาการ' : 'เพิ่มโครงการวิจัยและวิชาการ'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });

                    window.location.href = 'research_and_academic.php';

                } else {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขโครงการวิจัยและวิชาการ' : 'เพิ่มโครงการวิจัยและวิชาการ'); ?> - ผิดพลาด',
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