<?php
require_once '../include/head_php.inc';

$itemId = $_POST['itemId'];
$item = array();
$media_cover = array();
$media_gallery = array();
$document = array();
$embed_example= "<iframe width='560' height='315' src='https://www.youtube.com/embed/9WZ-bO-wBxw' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";

// echo $embed_example;
// exit();

if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);
    /* Item */
    $sql = "SELECT * FROM article WHERE id=$itemId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item['id'] = (int)$row['id'];
            $item['title'] = $row['title'];
            $item['description'] = $row['description'];
            $item['short_description'] = $row['short_description'];
            $item['featured'] = (int)$row['featured'];
            $item['start_date'] = $row['start_date'];
            $item['end_date'] = $row['end_date'];
            $item['status'] = $row['status'];
            $item['meta_title'] = $row['meta_title'];
            $item['meta_keywords'] = $row['meta_keywords'];
            $item['meta_description'] = $row['meta_description'];
            $item['video_type'] = $row['video_type'];
            $item['video_path'] = $row['video_path'];
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }

    /* Media Cover */
    $sql_media_cover = "SELECT * FROM media WHERE model_id=$itemId AND collection_name ='cover_desktop' AND model_type='activity'";    
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

    /* Media Gallery */
    $sql_media_gallery = "SELECT * FROM media WHERE model_id=$itemId AND collection_name ='gallery_desktop' AND model_type='activity'";    
    if ($result = $db->query($sql_media_gallery)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($row as $key => $value) {
                $media_gallery[$key]['id'] = (int)$value['id'];
                $media_gallery[$key]['name'] = $value['name'];
                $media_gallery[$key]['file_name'] = $value['file_name'];
                $media_gallery[$key]['mime_type'] = $value['mime_type'];
                $media_gallery[$key]['collection_name'] = $value['collection_name'];
            }
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
        $db->close();
        exit();
    }

    /* Document */
    $sql_documents = "SELECT * FROM documents_download WHERE article_id=$itemId";    
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
    <body class="hold-transition skin-blue sidebar-mini fixed">

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>ภาพกิจกรรม
                    <small>ภาพกิจกรรม</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddActivity" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียดภาพกิจกรรม-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดภาพกิจกรรม</h3>

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

                                    <!--ชื่อภาพกิจกรรม-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อกิจกรรม:</label>
                                                <input type="text" name="title" value="<?php echo (!empty($item) ? $item['title']:''); ?>" maxlength="50" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- รายละเอียดแบบย่อ -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">รายละเอียดแบบย่อ:</label>
                                                <textarea name="short_description" rows="5" class="form-control"><?php echo (!empty($item) ? $item['short_description']:''); ?></textarea>
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

                                    <!--วันที่-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputBeginDate">วันที่เริ่มต้น:</label>
                                                    <?php
                                                    if (!empty($item)) {
                                                        $beginDatePart = explode('-',date('Y-m-d',strtotime($item['start_date'])));
                                                        $year = $beginDatePart[0];
                                                        $month = $beginDatePart[1];
                                                        $day = $beginDatePart[2];
                                                        $beginDate = "$day/$month/$year";
                                                    }
                                                    ?>
                                                    <input type="text" class="form-control"
                                                           id="inputBeginDate"
                                                           name="inputBeginDate"
                                                           value="<?php echo(!empty($item) ? $beginDate : ''); ?>"
                                                           placeholder="เลือกวันที่เริ่มต้น" >
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputEndDate">วันที่สิ้นสุด:</label>
                                                    <?php
                                                    if (!empty($item)) {
                                                        $endDatePart = explode('-',date('Y-m-d',strtotime($item['end_date'])));
                                                        $year = $endDatePart[0];
                                                        $month = $endDatePart[1];
                                                        $day = $endDatePart[2];
                                                        $endDate = "$day/$month/$year";
                                                    }
                                                    ?>
                                                    <input type="text" class="form-control"
                                                           id="inputEndDate"
                                                           name="inputEndDate"
                                                           value="<?php echo(!empty($item) ? $endDate : ''); ?>"
                                                           placeholder="เลือกวันที่สิ้นสุด" dateCheck="dateCheck">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->


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

                            <!-- รูปภาพ -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รูปภาพ
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

                                    <!--รูปภาพ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group" id="gallery_desktop">
                                                    <div id="DropzoneGalleryDesktop" class="dropzone">
  
                                                    </div>
                                                    <div>
                                                    <?php 
                                                            if(!empty($media_gallery)):
                                                                foreach ($media_gallery as $key => $value):
                                                                $path_gallery = pathUrl()."static/media/".$value['file_name'];
                                                                //echo $path_gallery;
                                                                //exit();
                                                        ?>  
                                                                <?php echo ($key+1) ?>.<img src="<?php echo $path_gallery; ?>" style="width:50%;height:100%;padding:20px"><a data-id="<?php echo $value['id']; ?>" data-filename="<?php echo $value['file_name']; ?>" class="btn btn-info btn-remove-images" style="margin-left:35px">ลบ</a>
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

                            <!-- วีดีโอ -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">วีดีโอ
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

                                    <!--วีดีโอ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ประเภทวีดีโอ:</label>
                                                <select name="video_type" class="form-control">
                                                    <option value=""> เลือกประเภทวีดีโอ </option>
                                                    <option value="mp4" <?php echo (!empty($item) ? ($item['video_type'] == 'mp4' ? 'selected':''):''); ?> >MP4</option>
                                                    <option value="youtube_embed" <?php echo (!empty($item) ? ($item['video_type'] == 'youtube_embed' ? 'selected':''):''); ?> >Youtube Embed</option>
                                                </select>
                                            </div>
                                            <div class="form-group" id="video_case_embed"  <?php echo (!empty($item) ? ($item['video_type'] == 'youtube_embed' ? '':'style="display:none;"'):'style="display:none;"'); ?>  >
                                                <label for="inputArticle">ฝังวิดีโอ:</label>
                                                <textarea name="video_case_embed" class="form-control" placeholder="<?php echo $embed_example; ?>"><?php echo (!empty($item) ? ($item['video_type'] == 'youtube_embed' ? $item['video_path']:''):''); ?></textarea>
                                                <div style="margin:15px;">
                                                <?php echo (!empty($item) ? ($item['video_type'] == 'youtube_embed' ? $item['video_path']:''):''); ?>
                                                </div>
                                            </div>
                                            <div class="input-group" id="video_case_mp4" <?php echo (!empty($item) ? ($item['video_type'] == 'mp4' ? '':'style="display:none;"'):'style="display:none;"'); ?>>
                                                <div id="DropzoneMp4" class="dropzone">
                                                </div>

                                                <?php 
                                                    if(!empty($item) && $item['video_type'] == 'mp4' && $item['video_path'] !=''):
                                                                $path_mp4 = pathUrl().$item['video_path'];
                                                                //echo $path_mp4;
                                                                //exit();
                                                        ?>      
                                                                <br>
                                                                <video width="320" height="240" controls>
                                                                    <source src="<?php echo $path_mp4; ?>" type="video/mp4">
                                                                </video>
                                                                <a data-id="<?php echo $itemId;  ?>" data-filename="<?php echo $item['video_path']; ?>" class="btn btn-info btn-remove-mp4" style="margin-left:35px">ลบ</a>
                                                                <br>
                                                        <?php
                                                    endif; 
                                                ?>
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
            $('#inputBeginDate').datepicker({
                format: 'dd/mm/yyyy',
                orientation: 'bottom',
                autoclose: true
            }).on('changeDate', e => {
                e.target.setCustomValidity('');
            });

            $('#inputEndDate').datepicker({
                format: 'dd/mm/yyyy',
                orientation: 'bottom',
                autoclose: true
            }).on('changeDate', e => {
                e.target.setCustomValidity('');
            });
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


            $("#DropzoneGalleryDesktop").dropzone({
                
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 2, // MB
                maxFiles:10,
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

                    html = '<input name="gallery_desktop_file_name[]" type="hidden" value="'+file.custom_file_name+'">';
                    html += '<input name="gallery_desktop_name[]" type="hidden" value="'+file.custom_name+'" >';
                    html += '<input name="gallery_desktop_size[]" type="hidden" value="'+file.size+'" >';
                    html += '<input name="gallery_desktop_type[]" type="hidden" value="'+file.type+'" >';
                    $('#gallery_desktop').append(html);

                },
                accept: function(file, done) {
                    //#add_galleryconsole.log(file,file.myCustomName);
                    ///console.log(file);
                    done();
                    //if (file.type != "image/jpeg" && file.type != "image/png") {}
                }

            });


            $("#DropzoneDocument").dropzone({
                
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 50, // MB
                maxFiles:10,
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

            $("#DropzoneMp4").dropzone({
                
                paramName: "file", // The name that will be used to transfer the file
                maxFilesize: 50, // MB
                maxFiles:1,
                acceptedFiles: ".mp4",
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

                    html = '<input name="mp4_file_name[]" type="hidden" value="'+file.custom_file_name+'">';
                    html += '<input name="mp4_name[]" type="hidden" value="'+file.custom_name+'" >';
                    html += '<input name="mp4_size[]" type="hidden" value="'+file.size+'" >';
                    html += '<input name="mp4_type[]" type="hidden" value="'+file.type+'" >';
                    $('#video_case_mp4').append(html);

                },
                accept: function(file, done) {
                    //#add_galleryconsole.log(file,file.myCustomName);
                    ///console.log(file);
                    done();
                    //if (file.type != "image/jpeg" && file.type != "image/png") {}
                }

            });

    

            $("#formAddActivity").validate({
                rules:{
                    title: {
                    required: true,
                    minlength: 3
                    },
                    inputBeginDate:"required",
                    inputEndDate:"required"
                },
                messages: {
                    title: {
                    required: "ต้องระบุชื่อภาพกิจกรรม",
                    minlength: "ชื่อภาพกิจกรรม ควรมีความยาวอยู่ระหว่าง 3-50 ตัวอักษร"
                    },
                    inputBeginDate:{
                    required: "ต้องระบุวันที่เริ่มต้น",
                    },
                    inputEndDate:{
                    required: "ต้องระบุวันที่สิ้นสุด",
                    }
                },
                submitHandler: function(form) {
                    //console.log("Testdasdadsa");
                    doAddActivity();
                }
            });


            $.validator.addMethod("dateCheck",
                function(value, element, param) {
                    
                    date_start = $('#inputBeginDate').val();
                    date_end  = $('#inputEndDate').val();
                    
                    if(date_start !='' && date_end !=''){

                        let beginDate = $('#inputBeginDate').val();
                        let beginDatePart = beginDate.split('/');
                        beginDate = beginDatePart[2] + '-' + beginDatePart[1] + '-' + beginDatePart[0];

                        let endDate = $('#inputEndDate').val();
                        let endDatePart = endDate.split('/');
                        endDate = endDatePart[2] + '-' + endDatePart[1] + '-' + endDatePart[0];

                       //return false;

                        let date_start = Date.parse(beginDate);
                        let date_end = Date.parse(endDate);
                        
                        //console.log(date_start,date_end);

                        if(date_end <= date_start){
                            //console.log("Case False");
                            return false;
                        }else{
                            return true;
                        }
			
                    }else{
                        return true;
                    }
                    
                    //return true;
                },
            "ต้องระบุวันที่สิ้นสุดมากกว่าวันที่เริ่มต้น");


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
                                title: 'ภาพกิจกรรม',
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
                                title: 'ภาพกิจกรรม - ผิดพลาด',
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
                                title: 'ภาพกิจกรรม',
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
                                title: 'ภาพกิจกรรม - ผิดพลาด',
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


            $('.btn-remove-mp4').click(function(){

                id = $(this).attr('data-id');
                file_name = $(this).attr('data-filename');
                //delete_media
                //console.log(id,file_name);

                $.ajax({
                    type: "POST",
                    url: '../api/api.php/delete_mp4',
                    dataType: 'json',
                    data: {id:id,file_name:file_name},
                    timeout: 600000,
                    success: function (data) {

                        //console.log(data);

                        if (data.error_code === 0) {

                            BootstrapDialog.show({
                                title: 'วีดีโอ',
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
                                title: 'วีดีโอ - ผิดพลาด',
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


            $('select[name="video_type"]').change(function(){

                val =$(this).val();
                //console.log(val);
                switch (val) {
                    case 'mp4':
                            $('#video_case_mp4').show();
                            $('#video_case_embed').hide();
                        break;
                    case 'youtube_embed':
                            $('#video_case_embed').show();
                            $('#video_case_mp4').hide();                        
                        break;                
                    default:
                            $('#video_case_embed').hide();
                            $('#video_case_mp4').hide();
                        break;
                }

            })



        });

        function doAddActivity() {

            $('textarea[name="description"]').text(CKEDITOR.instances.editor.getData());
            var form = $('#formAddActivity')[0];
            var data = new FormData(form);
            //console.log(data.entries());



            $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: '../api/api.php/<?php echo(isset($itemId) ? 'update_activity' : 'add_activity'); ?>',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                console.log(data);

                if (data.error_code === 0) {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขภาพกิจกรรม' : 'เพิ่มภาพกิจกรรม'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });

                    window.location.href = 'activity_pictures.php';

                } else {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขภาพกิจกรรม' : 'เพิ่มภาพกิจกรรม'); ?> - ผิดพลาด',
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