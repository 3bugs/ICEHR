<?php
require_once '../include/head_php.inc';

$newsId = $_GET['news_id'];

$newsType = $_GET['news_type'];
$newsTypeList = array(
    'training', 'public-relations', 'in-house', 'activity'
);
if (!isset($newsType) || !in_array($newsType, $newsTypeList)) {
    echo "Invalid news type '$newsType' - ระบุประเภทข่าวไม่ถูกต้อง";
    $db->close();
    exit();
}

$pageTitles['training'] = 'ข่าวการฝึกอบรม';
$pageTitles['public-relations'] = 'ข่าวประชาสัมพันธ์';
$pageTitles['in-house'] = 'หลักสูตร In-House ที่ผ่านมา';
$pageTitles['activity'] = 'ภาพกิจกรรม';

$pageTitle = $pageTitles[$newsType];

$news = array();
if (isset($newsId)) {
    $newsId = $db->real_escape_string($newsId);

    $sql = "SELECT * FROM news WHERE id = $newsId";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $news = $result->fetch_assoc();
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

$imageList = array();
$pdfList = array();
if (isset($newsId)) {
    $sql = "SELECT id, title, file_name, type, created_at 
            FROM news_asset 
            WHERE news_id = $newsId";
    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $asset = array();
            $asset['id'] = (int)$row['id'];
            $asset['title'] = $row['title'];
            $asset['file_name'] = $row['file_name'];
            $asset['type'] = $row['type'];
            $asset['created_at'] = $row['created_at'];

            $prefixPosition = strpos($row['file_name'], '-');
            $extensionPosition = strpos($row['file_name'], '.');
            $asset['title'] = substr(
                $row['file_name'],
                $prefixPosition + 1,
                $extensionPosition - ($prefixPosition + 1)
            );
            
            if ($row['type'] === 'image') {
                array_push($imageList, $asset);
            } else if ($row['type'] === 'pdf') {
                array_push($pdfList, $asset);
            }
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

            .preview-container {
                position: relative;
                display: inline;
                border: 0px solid red;
            }

            .preview-container .selFile {
                opacity: 1;
                transition: .3s ease;
            }

            .preview-container:hover .selFile {
                opacity: 0.3;
                transition: .3s ease;
            }

            .middle {
                transition: .3s ease;
                opacity: 0;
                position: absolute;
                top: 50%;
                left: 80px;
                transform: translate(-50%, -50%);
                -ms-transform: translate(-50%, -50%);
                text-align: center;
            }

            .middle:hover {
                cursor: pointer;
            }

            .preview-container:hover .middle {
                opacity: 1;
                transition: .3s ease;
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
    <body class="hold-transition skin-blue sidebar-mini fixed">

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?= (isset($newsId) ? 'แก้ไข' : 'เพิ่ม') . $pageTitle; ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddNews"
                      autocomplete="off"
                      action="../api/api.php/<?= (isset($newsId) ? 'update_news' : 'add_news'); ?>"
                      method="post">

                    <input type="hidden" name="newsId" value="<?php echo $newsId; ?>"/>
                    <input type="hidden" name="newsType" value="<?php echo $newsType; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

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
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="inputTitle">หัวข้อ/เรื่อง:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle"
                                                           name="title"
                                                           value="<?php echo(!empty($news) ? $news['title'] : ''); ?>"
                                                           placeholder="กรอกหัวข้อ/เรื่อง" required
                                                           oninvalid="this.setCustomValidity('กรอกหัวข้อ/เรื่อง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--วันที่ข่าว-->
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputNewsDate">วันที่ของข่าว/กิจกรรม:</label>
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right"
                                                           id="inputNewsDate"
                                                           name="newsDate"
                                                           placeholder="เลือกวันที่ของข่าว/กิจกรรม" required
                                                           oninvalid="this.setCustomValidity('เลือกวันที่ของข่าว/กิจกรรม')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--รายละเอียดแบบย่อ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputTitle">รายละเอียดแบบย่อ (สำหรับแสดงในหน้า List):</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputShortDescription" name="shortDescription"
                                                           value="<?= (!empty($news) ? $news['short_description'] : ''); ?>"
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

                            <!--content editor-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รายละเอียดข่าว/กิจกรรม
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
                                          name="details">
                                    <?= (!empty($news) ? $news['details'] : ''); ?>
                                </textarea>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--รูปภาพหน้าปก-->
                            <div class="box box-warning">
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
                                <div class="box-body pad" style="background_: #f8f8f8">
                                    <?php
                                    if (!empty($news)) {
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
                                                        <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_NEWS_ASSETS . $news['image_file_name']); */?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                    </div>
                                                    <a href="<?= (UPLOAD_DIR_NEWS_ASSETS . $news['image_file_name']); ?>"
                                                       data-lightbox="coverImage" data-title="<?= $news['title']; ?>">
                                                        <img src="<?= (UPLOAD_DIR_NEWS_ASSETS . $news['image_file_name']); ?>"
                                                             width="600px">
                                                    </a>
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="cover_image_tab_2" style="padding: 0px">
                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                        <li style="margin-bottom: 10px">รูปภาพหน้าปกนี้จะถูกใช้ตอนแชร์ Facebook, ให้ใช้ภาพขนาด 600x312 pixel (กว้าง 600 pixel, สูง 312 pixel)</li>
                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                        <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                    </ul>
                                                    <input id="cover-image-file-upload" name="coverImageFile"
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
                                            <li style="margin-bottom: 10px">รูปภาพหน้าปกนี้จะถูกใช้ตอนแชร์ Facebook, ให้ใช้ภาพ JPG ขนาด 600x312 pixel (กว้าง 600 pixel, สูง 312 pixel)</li>
                                            <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                            <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                        </ul>
                                        <input id="cover-image-file-upload" name="coverImageFile" required
                                               type="file" accept="image/*"
                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
                                               oninvalid="this.setCustomValidity('เลือกรูปภาพหน้าปก')"
                                               oninput="this.setCustomValidity('')"/>
                                        <div id="cover-image-upload-preview"
                                             style="background: #efffd1; padding: 10px;"></div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--รูปภาพข่าว/กิจกรรม-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">รูปภาพข่าว/กิจกรรม
                                        <!--<small>อัพโหลดรูปภาพ</small>-->
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
                                <div class="box-body pad" style="background: #f8f8f8">
                                    <!-- Custom Tabs -->
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <?php
                                            if (isset($newsId)) {
                                                ?>
                                                <li class="active"><a href="#image_tab_1" data-toggle="tab">รูปภาพปัจจุบัน <strong>(<?php echo sizeof($imageList); ?>)</strong></a></li>
                                                <?php
                                            }
                                            ?>
                                            <li <?php echo(!isset($newsId) ? 'class="active"' : ''); ?>><a href="#image_tab_2" data-toggle="tab">เพิ่มรูปภาพ</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <?php
                                            if (isset($newsId)) {
                                                ?>
                                                <!--ตารางรูปภาพ-->
                                                <div class="tab-pane active" id="image_tab_1">
                                                    <?php
                                                    if (sizeof($imageList) === 0) {
                                                        ?>
                                                        <div class="callout callout-danger" style="margin-top: 10px">
                                                            <p>ยังไม่มีรูปภาพ!</p>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <table id="tableImage" class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <!--<th style="text-align: center; width: 40%;">ชื่อ</th>-->
                                                            <th style="text-align: center; width: 80%;">รูปภาพ</th>
                                                            <th style="text-align: center; width: 20%;">อัพโหลดเมื่อ</th>
                                                            <th style="text-align: center;">จัดการ</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach ($imageList as $image) {
                                                            $createdAt = $image['created_at'];
                                                            $dateTimePart = explode(' ', $createdAt);
                                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                                            $timePart = explode(':', $dateTimePart[1]);
                                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                                            $displayDateTime = "$displayDate<br>$displayTime";
                                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                                            ?>
                                                            <tr>
                                                                <!--<td><?php /*echo $image['title']; */ ?></td>-->
                                                                <td style="text-align: center">
                                                                    <a href="<?php echo(UPLOAD_DIR_NEWS_ASSETS . $image['file_name']); ?>" data-lightbox="newsImage">
                                                                        <img src="<?php echo(UPLOAD_DIR_NEWS_ASSETS . $image['file_name']); ?>"
                                                                             height="120px">
                                                                    </a>
                                                                </td>
                                                                <td style="text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger"
                                                                            style="margin-left: 6px; margin-right: 6px"
                                                                            onClick="onClickDeleteAsset(this, <?php echo $image['id']; ?>, 'รูปภาพ')">
                                                                        <span class="fa fa-remove"></span>&nbsp;
                                                                        ลบ
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!--เพิ่มรูปภาพใหม่-->
                                            <div class="tab-pane <?= (!isset($newsId) ? 'active' : ''); ?>" id="image_tab_2">
                                                <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                    <li style="margin-bottom: 10px">ให้ใช้ภาพ JPG ขนาด 600x312 pixel (กว้าง 600 pixel, สูง 312 pixel) <u>ทุกภาพ</u></li>
                                                    <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                    <li>สามารถเลือกได้หลายไฟล์พร้อมกัน</li>
                                                    <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                </ul>

                                                <!--<div>
                                                    <button onclick="test()" type="button">Test</button>
                                                </div>-->

                                                <input id="image-upload" type="file" accept="image/*" multiple
                                                       style="color: transparent; width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                <!--<label for="image-upload"
                                                       style="background-color: #ffffff; width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 60px 10px"/>-->
                                                <div id="image-upload-preview"
                                                     style="background: #efffd1; padding: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /.box -->

                            <!--ไฟล์ดาวน์โหลด-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ไฟล์สำหรับดาวน์โหลด
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
                                <div class="box-body pad" style="background: #f8f8f8">
                                    <!--<label for="file-upload" class="custom-file-upload">
                                        <i class="fa fa-upload"></i>&nbsp;&nbsp;อัพโหลด PDF
                                    </label>-->

                                    <!-- Custom Tabs -->
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <?php
                                            if (isset($newsId)) {
                                                ?>
                                                <li class="active"><a href="#pdf_tab_1" data-toggle="tab">ไฟล์ปัจจุบัน <strong>(<?php echo sizeof($pdfList); ?>)</strong></a></li>
                                                <?php
                                            }
                                            ?>
                                            <li <?php echo(!isset($newsId) ? 'class="active"' : ''); ?>><a href="#pdf_tab_2" data-toggle="tab">เพิ่มไฟล์</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <?php
                                            if (isset($newsId)) {
                                                ?>
                                                <!--ตาราง PDF-->
                                                <div class="tab-pane active" id="pdf_tab_1">
                                                    <?php
                                                    if (sizeof($pdfList) === 0) {
                                                        ?>
                                                        <div class="callout callout-danger" style="margin-top: 10px">
                                                            <p>ยังไม่มีเอกสารดาวน์โหลด!</p>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <table id="tablePdf" class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <!--<th style="text-align: center; width: 40%;">ชื่อ</th>-->
                                                            <th style="text-align: center; width: 50%;">ชื่อ</th>
                                                            <th style="text-align: center; width: 30%;">Link ไฟล์</th>
                                                            <th style="text-align: center; width: 20%;">อัพโหลดเมื่อ</th>
                                                            <th style="text-align: center;">จัดการ</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach ($pdfList as $pdf) {
                                                            /*$fileName = $pdf['file_name'];
                                                            $prefixPosition = strpos($fileName, '-');
                                                            $extensionPosition = strpos($fileName, '.');
                                                            $title = substr($fileName, $prefixPosition + 1, $extensionPosition - ($prefixPosition + 1));*/

                                                            $createdAt = $pdf['created_at'];
                                                            $dateTimePart = explode(' ', $createdAt);
                                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                                            $timePart = explode(':', $dateTimePart[1]);
                                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                                            $displayDateTime = "$displayDate<br>$displayTime";
                                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';
                                                            ?>
                                                            <tr>
                                                                <!--<td><?php /*echo $image['title']; */ ?></td>-->
                                                                <td>
                                                                    <?php echo $pdf['title']; ?>
                                                                </td>
                                                                <td style="text-align: center; cursor: pointer"
                                                                    onClick="window.open('<?php echo(UPLOAD_DIR_NEWS_ASSETS . $pdf['file_name']); ?>', '_blank')">
                                                                    <a href="javascript:void(0)">
                                                                        <span style="font-size: 25px"><i class="fa fa-file-pdf-o"></i></span>
                                                                    </a>
                                                                </td>
                                                                <td style="text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger"
                                                                            style="margin-left: 6px; margin-right: 6px"
                                                                            onClick="onClickDeleteAsset(this, <?php echo $pdf['id']; ?>, 'ไฟล์ดาวน์โหลด')">
                                                                        <span class="fa fa-remove"></span>&nbsp;
                                                                        ลบ
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!--เพิ่มไฟล์ดาวน์โหลดใหม่-->
                                            <div class="tab-pane <?php echo(!isset($newsId) ? 'active' : ''); ?>" id="pdf_tab_2">
                                                <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                    <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                    <li>สามารถเลือกได้หลายไฟล์พร้อมกัน</li>
                                                    <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                </ul>
                                                <input id="pdf-upload" type="file" accept="application/pdf" multiple
                                                       name="pdfFiles[]" style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
                                                <ul id="pdf-upload-preview"
                                                    style="background: #efffd1; padding-top: 10px; padding-bottom: 10px"></ul>
                                            </div>
                                        </div>
                                    </div>
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
        //https://www.raymondcamden.com/2014/04/14/MultiFile-Uploads-and-Multiple-Selects-Part-2/

        /*function test() {
            $('#image-upload').prop('files').splice(0, 1);
            alert($('#image-upload').prop('files').length());
        }*/

        $(() => {
            <?php
            if (!empty($news)) {
                $newsDatePart = explode('-', $news['news_date']);
                $year = $newsDatePart[0];
                $month = $newsDatePart[1];
                $day = $newsDatePart[2];
                $newsDate = "$day/$month/$year";
            }
            ?>

            //Date picker
            let inputNewsDate = $('#inputNewsDate');
            inputNewsDate.datepicker({
                language: 'th',
                thaiyear: true,
                format: 'dd/mm/yyyy',
                orientation: 'bottom',
                autoclose: true
            }).on('changeDate', e => {
                e.target.setCustomValidity('');
            }).datepicker('update', '<?php echo(!empty($news) ? $newsDate : ''); ?>');

            CKEDITOR.replace('editor');
        });

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });
            
            $('#tableImage').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
                order: [[1, 'desc']],
                language: {
                    lengthMenu: "แสดงหน้าละ _MENU_ แถวข้อมูล",
                    zeroRecords: "ไม่มีข้อมูล",
                    emptyTable: "ไม่มีข้อมูล",
                    info: "หน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
                    infoEmpty: "แสดง 0 แถวข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ แถวข้อมูล)",
                    search: "ค้นหา:",
                    thousands: ",",
                    loadingRecords: "รอสักครู่...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                }
            });

            $('#tablePdf').DataTable({
                stateSave: false,
                //stateDuration: -1, // sessionStorage
                order: [[2, 'desc']],
                language: {
                    lengthMenu: "แสดงหน้าละ _MENU_ แถวข้อมูล",
                    zeroRecords: "ไม่มีข้อมูล",
                    emptyTable: "ไม่มีข้อมูล",
                    info: "หน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
                    infoEmpty: "แสดง 0 แถวข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ แถวข้อมูล)",
                    search: "ค้นหา:",
                    thousands: ",",
                    loadingRecords: "รอสักครู่...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                }
            });

            $('#formAddNews #divLoading').hide();

            $('#formAddNews').submit(event => {
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

        /*function readURL(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-upload-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }*/

        const storedImageFiles = [];
        const storedPdfFiles = [];

        $(function () {
            $("body").on("click", ".selFile", removeFile);
            $("body").on("click", ".middle", removeFile);

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

            $('#image-upload').on('change', function (e) {
                const selDiv = $("div#image-upload-preview");
                selDiv.show();

                let files = e.target.files;
                let filesArr = Array.prototype.slice.call(files);

                filesArr.forEach(function(f) {
                    if (!f.type.match("image.*")) {
                        return;
                    }
                    storedImageFiles.push(f);

                    let reader = new FileReader();
                    reader.onload = function (e) {
                        //let html = "<div class=\"preview-container\"><img style=\"width: 160px; height: auto; margin: 3px; cursor: pointer;\" src=\"" + e.target.result + "\" data-file='" + f.name + "' class='selFile' title='คลิกเพื่อลบ'>&nbsp;&nbsp;" + f.name + "<br clear=\"left\"/><div class=\"middle\" data-file='" + f.name + "'><i class=\"fa fa-times-circle\" title=\"คลิกเพื่อลบ\" style=\"color: #000000; font-size: 30px\"/></div></div>";
                        let html = `<div class="preview-container">
                                        <img style="width: 160px; height: auto; margin: 3px; cursor: pointer;" src="${e.target.result}" data-file="${f.name}" class="selFile" title="คลิกเพื่อลบ">
                                            &nbsp;&nbsp;${f.name}<br clear="left"/>
                                        <div class="middle" data-file="${f.name}">
                                            <i class="fa fa-times-circle" title="คลิกเพื่อลบ" style="color: #000000; font-size: 30px"/>
                                        </div>
                                    </div>`;
                        selDiv.append(html);
                    };
                    reader.readAsDataURL(f);
                });

                //imagesPreview(this, 'div#image-upload-preview');
            });
        });

        function removeFile(e) {
            let file = $(this).data("file");
            for (let i = 0; i < storedImageFiles.length; i++) {
                if (storedImageFiles[i].name === file) {
                    storedImageFiles.splice(i, 1);
                    break;
                }
            }
            if (storedImageFiles.length === 0) {
                const selDiv = $("div#image-upload-preview");
                selDiv.hide();
            }

            //alert(storedFiles.length);

            $(this).parent().remove();
        }

        $(function () {
            $('#pdf-upload-preview').hide();

            const pdfPreview = function (input, placeToInsertPdfPreview) {
                $(placeToInsertPdfPreview).empty();
                $(placeToInsertPdfPreview).hide();

                if (input.files) {
                    let fileCount = input.files.length;

                    for (let i = 0; i < fileCount; i++) {
                        $(placeToInsertPdfPreview).show();
                        $($.parseHTML('<li style="">' + input.files[i].name + '</li>')).appendTo(placeToInsertPdfPreview);
                    }
                }
            };

            $('#pdf-upload').on('change', function () {
                pdfPreview(this, 'ul#pdf-upload-preview');
            });
        });

        function doAddEditNews() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            CKEDITOR.instances.editor.updateElement();

            $('#formAddNews #buttonSave').prop('disabled', true);
            $('#formAddNews #divLoading').show();

            const form = $('#formAddNews')[0];
            const formData = new FormData(form);

            for (let i = 0, len = storedImageFiles.length; i < len; i++) {
                formData.append('imageFiles[]', storedImageFiles[i]);
            }

            $.ajax({
                url: '../api/api.php/<?= (isset($newsId) ? 'update_news' : 'add_news'); ?>',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(data) {
                    $('#formAddNews #buttonSave').prop('disabled', false);
                    $('#formAddNews #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($newsId)) {
                                    ?>
                                    window.location.href = 'news.php?news_type=<?php echo $newsType; ?>';
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
                            title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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
                error: function() {
                    $('#formAddNews #buttonSave').prop('disabled', false);
                    $('#formAddNews #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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

            $('#formAddNews_NotUsed').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddNews #buttonSave').prop('disabled', false);
                    $('#formAddNews #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($newsId)) {
                                    ?>
                                    window.location.href = 'news.php?news_type=<?php echo $newsType; ?>';
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
                            title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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
                    $('#formAddNews #buttonSave').prop('disabled', false);
                    $('#formAddNews #divLoading').hide();

                    BootstrapDialog.show({
                        title: '<?php echo(isset($newsId) ? 'แก้ไข' : 'เพิ่ม'); ?> - ผิดพลาด',
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

        function onClickDeleteAsset(element, assetId, assetType) {
            BootstrapDialog.show({
                title: 'ลบ' + assetType,
                message: `การลบ${assetType}จะมีผลกับฐานข้อมูลทันที!\n\nยืนยันลบ${assetType}นี้?`,
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteAsset(assetId, assetType);
                        self.close();
                    },
                    cssClass: 'btn-primary'
                }, {
                    label: 'ยกเลิก',
                    action: function (self) {
                        self.close();
                    }
                }]
            });
        }

        function doDeleteAsset(assetId, assetType) {
            $.post(
                '../api/api.php/delete_news_asset',
                {
                    assetId: assetId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบ' + assetType + ' - ผิดพลาด',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });
                }
            }).fail(function () {
                BootstrapDialog.show({
                    title: 'ลบ' + assetType + ' - ผิดพลาด',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                        }
                    }]
                });
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