<?php
require_once '../include/head_php.inc';

$itemId = $_POST['itemId'];
$item = array();
$media_cover = array();
if (isset($itemId)) {
    $itemId = $db->real_escape_string($itemId);
    /* Item */
    $sql = "SELECT * FROM menus WHERE id=$itemId";
    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $item['id'] = (int)$row['id'];
            $item['title'] = $row['name'];
            $item['description'] = $row['description'];
            $item['layout'] = $row['layout'];
            $item['target'] = (int)$row['target'];
            $item['link_type'] = $row['link_type'];
            $item['url_external'] = $row['url_external'];
            $item['ordering'] = $row['ordering'];
            $item['parent_id'] = $row['parent_id'];
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
    $sql_media_cover = "SELECT * FROM media WHERE model_id=$itemId AND collection_name ='cover_desktop' AND model_type='menu'";    
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
                    <?php echo(isset($itemId) ? 'แก้ไข' : 'เพิ่ม'); ?>เมนูด้านบน
                    <small>เมนูด้านบน</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formMenu" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xs-12">

                            <!--รายละเอียดเมนูด้านบน-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดเมนูด้านบน</h3>

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

                                    <!--ชื่อเมนูด้านบน-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputArticle">ชื่อเมนูด้านบน:</label>
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


                            <!-- ประเภทของลิงก์ * -->
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ประเภทของลิงก์
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
                                    
                                    <!--ประเภทของลิงก์ *-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group" >
                                                    <select name="link_type" class="form-control">
                                                            <option value=""> เลือกประเภทของลิงก์ </option>
                                                            <option value="internal" <?php echo (!empty($item) ? ($item['link_type'] == 'internal' ? 'selected':''):''); ?> >ลิงก์ภายใน</option>
                                                            <option value="external" <?php echo (!empty($item) ? ($item['link_type'] == 'external' ? 'selected':''):''); ?> >ลิงก์ภายนอก</option>
                                                    </select>
                                                </div>
                                                <div class="input-group" id="url_external">
                                                    <label for="inputArticle">ลิงก์:</label>
                                                    <input type="text" name="url_external" value="<?php echo (!empty($item) ? $item['url_external']:''); ?>" maxlength="150" class="form-control">
                                                </div>
                                                <div class="input-group" id="layout">
                                                    <label for="inputArticle">แบบแสดงผล:</label>
                                                    <select class="form-control" name="layout">
                                                            <option value="home" <?php echo (!empty($item) ? ($item['layout'] == 'home' ? 'selected':''):''); ?> >Home</option>
                                                            <option value="contact_us" <?php echo (!empty($item) ? ($item['layout'] == 'contact_us' ? 'selected':''):''); ?> >Contact - Us</option>
                                                            <option value="news_information_list" <?php echo (!empty($item) ? ($item['layout'] == 'news_information_list' ? 'selected':''):''); ?>  >News Information List</option>
                                                            <option value="document" <?php echo (!empty($item) ? ($item['layout'] == 'document' ? 'selected':''):''); ?> >Document</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
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


                                    <!--เมนูย่อย-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="selectStatus">เมนูย่อย:</label>
                                                <div class="input-group">
                                                    <select name="parent_id" class="form-control" >
                                                        <option value="0">Root</option>
                                                        <?php
                                                            $sql = "SELECT  a.id,
                                                                a.name,
                                                                (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
                                                                  "
                                                                . " FROM menus a "
                                                                . " WHERE a.position ='header' AND parent_id ='0'"
                                                                . " ORDER BY a.ordering ASC";
                                                            // echo $sql;
                                                            // exit();
                                                            //$sql = "SELECT * FROM course ORDER BY begin_date";
                                                            if ($result = $db->query($sql)) {
                                                                $itemsList = array();
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $data = array();
                                                                    $data['id'] = (int)$row['id'];
                                                                    $data['name'] = $row['name'];
                                                                    $data['count_parent_id'] = $row['count_parent_id'];
                                                                    array_push($itemsList, $data);
                                                                }
                                                                $result->close();
                                                            } else {
                                                                echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                                                                $db->close();
                                                                exit();
                                                            }

                                                            if (sizeof($itemsList) > 0) {
                                                                foreach ($itemsList as $item_level1) {
                                                                    $itemId_level1 = $item_level1['id'];
                                                                    $itemName = $item_level1['name'];
                                                                    $count_parent_id = $item_level1['count_parent_id'];
                                                        ?>
                                                                <option value="<?php echo $itemId_level1; ?>" <?php echo (!empty($item) ? ($item['parent_id'] == $itemId_level1 ? 'selected':''):''); ?>  <?php echo (!empty($item) ? ($itemId == $itemId_level1 ? 'disabled':''):''); ?> ><?php echo $itemName; ?></option>
                                                                <?php
                                                                    /* Level2 */
                                                                    if($count_parent_id >0){
                                                                        $sql_level2 = "SELECT   a.id,
                                                                                                a.name,
                                                                                                (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
                                                                                        "
                                                                                    . " FROM menus a"
                                                                                    . " WHERE a.position ='header' AND parent_id ='$itemId_level1'"
                                                                                    . " ORDER BY a.ordering ASC";
                                                                                if ($result_level2 = $db->query($sql_level2)) {
                                                                                        $itemsList_level2 = array();
                                                                                        while ($row_level2 = $result_level2->fetch_assoc()) {
                                                                                            $data_level2 = array();
                                                                                            $data_level2['id'] = (int)$row_level2['id'];
                                                                                            $data_level2['name'] = $row_level2['name'];
                                                                                            $data_level2['count_parent_id'] = $row_level2['count_parent_id'];
                                                                                            array_push($itemsList_level2, $data_level2);
                                                                                        }
                                                                                        $result_level2->close();
                                                                                } else {
                                                                                        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                                                                                        $db->close();
                                                                                        exit();
                                                                                }
                                                                        foreach ($itemsList_level2 as $item_level2) {
                                                                                    $itemId_level2 = $item_level2['id'];
                                                                                    $itemName_level2 = $item_level2['name'];
                                                                                    $count_parent_id_level2 = $item_level2['count_parent_id'];
                                                                ?>
                                                                        <option value="<?php echo $itemId_level2; ?>" <?php echo (!empty($item) ? ($item['parent_id'] == $itemId_level2 ? 'selected':''):''); ?>  <?php echo (!empty($item) ? ($itemId == $itemId_level2 ? 'disabled':''):''); ?> ><?php echo "&nbsp;&nbsp;- ".$itemName_level2; ?></option>
                                                                        <?php
                                                                            /* Level3 */
                                                                            if($count_parent_id_level2 >0){
                                                                                $sql_level3 = "SELECT   a.id,
                                                                                        a.name,
                                                                                        (SELECT count(id) FROM menus AS b WHERE b.parent_id = a.id) AS count_parent_id
                                                                                "
                                                                                    . " FROM menus a"
                                                                                    . " WHERE a.position ='header' AND parent_id ='$itemId_level2'"
                                                                                    . " ORDER BY a.ordering ASC";
                                                                                if ($result_level3 = $db->query($sql_level3)) {
                                                                                        $itemsList_level3 = array();
                                                                                        while ($row_level3 = $result_level3->fetch_assoc()) {
                                                                                            $data_level3 = array();
                                                                                            $data_level3['id'] = (int)$row_level3['id'];
                                                                                            $data_level3['name'] = $row_level3['name'];
                                                                                            $data_level3['count_parent_id'] = $row_level3['count_parent_id'];
                                                                                            array_push($itemsList_level3, $data_level3);
                                                                                        }
                                                                                        $result_level3->close();
                                                                                } else {
                                                                                        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
                                                                                        $db->close();
                                                                                        exit();
                                                                                }
                                                                                foreach ($itemsList_level3 as $item_level3) {
                                                                                    $itemId_level3 = $item_level3['id'];
                                                                                    $itemName_level3 = $item_level3['name'];
                                                                                    $count_parent_id_level3 = $item_level3['count_parent_id'];

                                                                        ?>
                                                                                    <option value="<?php echo $itemId_level3; ?>" <?php echo (!empty($item) ? ($item['parent_id'] == $itemId_level3 ? 'selected':''):''); ?>  <?php echo (!empty($item) ? ($itemId == $itemId_level3 ? 'disabled':''):''); ?> ><?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- ".$itemName_level3; ?></option>
                                                                        <?php
                                                                                }/*Endforeach Level3 */
                                                                            }/* Endif Level3 */
                                                                        ?>
                                                                <?php
                                                                        }/* Endforeach Level */
                                                                    }/* Endif Level2 */
                                                                ?>
                                                        <?php
                                                                }/* Endforeach Level 1 */
                                                            }/*Endif Level1 */
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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

                                    <!--เปิดหน้าต่างใหม่-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="selectStatus">เปิดหน้าต่างใหม่:</label>
                                                <div class="input-group">
                                                <input name="target" type="radio" id="target_1" value="1" <?php echo (!empty($item) ? ($item['target'] == '1' ? 'checked':''):''); ?> > เปิดหน้าต่าง
                                                <input name="target" type="radio" id="target_2" value="2" <?php echo (!empty($item) ? ($item['target'] == '2' ? 'checked':''):'checked'); ?> > ไม่เปิดหน้าต่าง
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--ลำดับ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputPlace">ลำดับ:</label>
                                                <div class="input-group">
                                            
                                                <input type="number" name="ordering" value="<?php echo (!empty($item) ? $item['ordering']:''); ?>"  class="form-control">
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
        <input name="position" type="hidden" value="header">
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

            $("select[name=link_type]").on('change', function(event) {
            var link_type = $(this).val();
            if (link_type == 'internal') {
                $("#url_external").hide();
                $("#layout").show();
            }else {
                $("#url_external").show();
                $("#layout").hide();
            }
            });
            $("select[name=link_type]").trigger('change');

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


        
            $("#formMenu").validate({
                rules:{
                    title: {
                    required: true,
                    minlength: 3
                    }
                },
                messages: {
                    title: {
                    required: "ต้องระบุชื่อเมนูด้านบน",
                    minlength: "ชื่อเมนูด้านบน ควรมีความยาวอยู่ระหว่าง 3-50 ตัวอักษร"
                    }
                },
                submitHandler: function(form) {
                    //console.log("Testdasdadsa");
                    doAddMenu();
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
                                title: 'เมนูด้านบน',
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
                                title: 'เมนูด้านบน - ผิดพลาด',
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

        function doAddMenu() {

            $('textarea[name="description"]').text(CKEDITOR.instances.editor.getData());
            var form = $('#formMenu')[0];
            var data = new FormData(form);
            //console.log(data.entries());



            $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: '../api/api.php/<?php echo(isset($itemId) ? 'update_menu' : 'add_menu'); ?>',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                console.log(data);

                if (data.error_code === 0) {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขเมนูด้านบน' : 'เพิ่มเมนูด้านบน'); ?>',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });

                    window.location.href = 'top_menu.php';

                } else {

                    BootstrapDialog.show({
                        title: '<?php echo(isset($itemId) ? 'แก้ไขเมนูด้านบน' : 'เพิ่มเมนูด้านบน'); ?> - ผิดพลาด',
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