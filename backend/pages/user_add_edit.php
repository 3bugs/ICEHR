<?php
require_once '../include/head_php.inc';

$userId = $_GET['user_id'];

$accessByOwnerUser = false;
if (isset($userId)) {
    $accessByOwnerUser = (int)$_SESSION[KEY_SESSION_USER_ID] === (int)$userId;
}

$user = array();
if (isset($userId)) {
    $userId = $db->real_escape_string($userId);

    $sql = "SELECT * FROM user WHERE id = $userId";

    if ($result = $db->query($sql)) {
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        } else {
            echo 'ไม่พบข้อมูลผู้ใช้งาน';
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

$sql = "SELECT id, name FROM user_department";
if ($result = $db->query($sql)) {
    $departmentList = array();
    while ($department = $result->fetch_assoc()) {
        array_push($departmentList, $department);
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

    <!-- Change password modal -->
    <div class="modal fade" id="changePasswordModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เปลี่ยนรหัสผ่านของบัญชีผู้ใช้ <?= $user['email']; ?> <small><?= "({$user['first_name']} {$user['last_name']})"; ?></small></h4>
                </div>
                <div class="modal-body">
                    <div id="divLoading" style="text-align: center">
                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                    </div>
                    <div id="alertSuccess" class="alert alert-success alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertSuccess').hide()">&times;</button>
                        <i class="icon fa fa-check"></i><span id="alertSuccessText"></span>
                    </div>
                    <div id="alertError" class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertError').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertErrorText"></span>
                    </div>

                    <form id="formChangePassword" role="form"
                          action="../api/api.php/change_user_password"
                          method="post"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputUserId" name="userId" value="<?= $userId; ?>">

                            <!--<div id="alertCanNotPrint" class="alert alert-danger alert-dismissible">
                                <i class="icon fa fa-warning"></i>ไม่สามารถพิมพ์ใบเสร็จรับเงินของใบสมัครนี้ได้ เนื่องจากสถานะการชำระเงินยังไม่สมบูรณ์
                            </div>-->

                            <?php
                            //if (!currentUserHasPermission(PERMISSION_USER_UPDATE)) {
                            if ($accessByOwnerUser) {
                                ?>
                                <!--รหัสผ่านเดิม-->
                                <div class="form-group">
                                    <label for="inputOldPassword">รหัสผ่านเดิม:</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                        <input type="password" class="form-control"
                                               id="inputOldPassword" name="oldPassword"
                                               placeholder="กรอกรหัสผ่านเดิม" required
                                               oninvalid="this.setCustomValidity('กรอกรหัสผ่านเดิม')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                            <!--รหัสผ่านใหม่-->
                            <div class="form-group">
                                <label for="inputNewPassword">รหัสผ่านใหม่ <span style="color: orangered">(อย่างน้อย 6 ตัวอักษร)</span>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control"
                                           id="inputNewPassword" name="newPassword"
                                           minlength="6"
                                           placeholder="กรอกรหัสผ่านใหม่ ความยาวอย่างน้อย 6 ตัวอักษร" required
                                           oninvalid="this.setCustomValidity('กรอกรหัสผ่านใหม่ ความยาวอย่างน้อย 6 ตัวอักษร')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--ยืนยันรหัสผ่านใหม่-->
                            <div class="form-group">
                                <label for="inputNewPassword">ยืนยันรหัสผ่านใหม่ <span style="color: orangered">(กรอกให้ตรงกับช่องข้างบน)</span>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control"
                                           id="inputConfirmNewPassword" name="confirmNewPassword"
                                           minlength="6"
                                           placeholder="กรอกรหัสผ่านใหม่อีกครั้งเพื่อยืนยัน" required
                                           oninvalid="this.setCustomValidity('กรอกรหัสผ่านใหม่อีกครั้งเพื่อยืนยัน')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--<div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>-->
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                            <button id="buttonChangePassword" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                เปลี่ยนรหัสผ่าน
                            </button>

                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!--<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>-->
            </div>
        </div>
    </div>

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo(isset($userId) ? 'แก้ไขข้อมูล' : 'เพิ่ม'); ?>ผู้ใช้งานระบบ
                    <small>&nbsp;</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <form id="formAddUser"
                      autocomplete="off"
                      action="../api/api.php/<?php echo(isset($userId) ? 'update_user' : 'add_user'); ?>"
                      method="post">

                    <input type="hidden" name="userId"
                           value="<?php echo $userId; ?>"/>

                    <div class="row">
                        <div class="col-xs-12">

                            <!--บัญชีผู้ใช้-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">บัญชีผู้ใช้งาน
                                        <small>&nbsp;</small>
                                    </h3>

                                    <?php
                                    if (isset($userId) && ($accessByOwnerUser || currentUserHasPermission(PERMISSION_USER_UPDATE))) {
                                        ?>
                                        <button type="button" class="btn btn-success pull-right"
                                                onclick="onClickChangePassword(this)">
                                            <span class="fa fa-lock"></span>&nbsp;
                                            เปลี่ยนรหัสผ่าน
                                        </button>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">

                                    <!--email password-->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputEmail">อีเมล:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-envelope"></i>
                                                        </span>
                                                    <input type="email" class="form-control" required
                                                        <?= empty($user) ? '' : 'readonly'; ?>
                                                           id="inputEmail"
                                                           name="email"
                                                           value="<?php echo(!empty($user) ? $user['email'] : ''); ?>"
                                                           placeholder="กรอกอีเมล"
                                                           oninvalid="this.setCustomValidity('กรอกอีเมล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                                <?php
                                                if (isset($userId)) {
                                                    ?>
                                                    <div style="margin-top: 5px; margin-left: 50px; color: orangered">ไม่อนุญาตให้แก้ไขอีเมล</div>
                                                    <?php
                                                }
                                                ?>
                                            </div>

                                            <!--<div class="form-group">
                                                <label for="inputUsername">ชื่อผู้ใช้ (Username):</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-user"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                        <?/*= empty($user) ? '' : 'readonly'; */?>
                                                           id="inputUsername"
                                                           name="username"
                                                           value="<?php /*echo(!empty($user) ? $user['username'] : ''); */?>"
                                                           placeholder="กรอก Username" required
                                                           oninvalid="this.setCustomValidity('กรอก Username')"
                                                           oninput="this.setCustomValidity('')"
                                                           autocomplete="off"
                                                           style="font-family: monospace,serif">
                                                </div>
                                            </div>-->

                                        </div>

                                        <?php
                                        if (!isset($userId)) {
                                            ?>
                                            <!--password-->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="inputPassword">รหัสผ่าน (Password):</label>
                                                    &nbsp;&nbsp;<a href="javascript:void(0)" onclick="alert($('#inputPassword').val())">แสดงรหัสผ่าน</a>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-lock"></i>
                                                        </span>
                                                        <input type="password" class="form-control"
                                                               id="inputPassword"
                                                               name="password"
                                                               value=""
                                                               placeholder="กรอก Password" required
                                                               oninvalid="this.setCustomValidity('กรอก Password')"
                                                               oninput="this.setCustomValidity('')"
                                                               autocomplete="off"
                                                               style="font-family: monospace,serif">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                            <!-- /.box -->

                            <!--ข้อมูลผู้ใช้งาน-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ข้อมูลผู้ใช้งาน</h3>

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

                                    <!--คำนำหน้า ชื่อ นามสกุล-->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="inputTitle">คำนำหน้าชื่อ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTitle"
                                                           name="title"
                                                           value="<?php echo(!empty($user) ? $user['title'] : ''); ?>"
                                                           placeholder="กรอกคำนำหน้าชื่อ" required
                                                           oninvalid="this.setCustomValidity('กรอกคำนำหน้าชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputFirstName">ชื่อ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputFirstName"
                                                           name="firstName"
                                                           value="<?php echo(!empty($user) ? $user['first_name'] : ''); ?>"
                                                           placeholder="กรอกชื่อ" required
                                                           oninvalid="this.setCustomValidity('กรอกชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputLastName">นามสกุล:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-font"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputLastName"
                                                           name="lastName"
                                                           value="<?php echo(!empty($user) ? $user['last_name'] : ''); ?>"
                                                           placeholder="กรอกนามสกุล" required
                                                           oninvalid="this.setCustomValidity('กรอกนามสกุล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--ตำแหน่ง ฝ่าย-->
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label for="inputPosition">ตำแหน่ง:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-address-book"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPosition"
                                                           name="position"
                                                           value="<?php echo(!empty($user) ? $user['position'] : ''); ?>"
                                                           placeholder="กรอกตำแหน่ง" required
                                                           oninvalid="this.setCustomValidity('กรอกตำแหน่ง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="selectDepartment">ฝ่าย:</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-folder-o"></i>
                                                </span>
                                                    <select id="selectDepartment" class="form-control" required
                                                            name="department"
                                                            oninvalid="this.setCustomValidity('เลือกฝ่าย')"
                                                            oninput="this.setCustomValidity('')">
                                                        <option value="" disabled selected>-- เลือกฝ่าย --</option>
                                                        <?php
                                                        foreach ($departmentList as $department) {
                                                            $departmentId = (int)$department['id'];
                                                            $departmentName = $department['name'];
                                                            $selected = '';
                                                            if (!empty($user) && ((int)$user['department_id'] === $departmentId)) {
                                                                $selected = 'selected';
                                                            }
                                                            ?>
                                                            <option value="<?= $departmentId; ?>" <?= $selected; ?>>
                                                                <?= $departmentName; ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--อีเมล เบอร์โทร-->
                                    <div class="row">
                                        <!--<div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputEmail">อีเมล:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-envelope"></i>
                                                        </span>
                                                    <input type="email" class="form-control" required
                                                           id="inputEmail"
                                                           name="email"
                                                           value="<?php /*echo(!empty($user) ? $user['email'] : ''); */?>"
                                                           placeholder="กรอกอีเมล"
                                                           oninvalid="this.setCustomValidity('กรอกอีเมล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputPhone">เบอร์โทรมือถือส่วนตัว:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-mobile"></i>
                                                        </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputPhone"
                                                           name="phone"
                                                           value="<?php echo(!empty($user) ? $user['phone'] : ''); ?>"
                                                           placeholder="กรอกเบอร์โทรมือถือส่วนตัว"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์โทรมือถือส่วนตัว')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputPhoneOffice">เบอร์โทรออฟฟิศ:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-phone"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPhoneOffice"
                                                           name="phoneOffice"
                                                           value="<?php echo(!empty($user) ? $user['phone_office'] : ''); ?>"
                                                           placeholder="กรอกเบอร์โทรออฟฟิศ"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์โทรออฟฟิศ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="inputPhoneExtension">เบอร์ต่อภายใน:</label>
                                                <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-hashtag"></i>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPhoneExtension"
                                                           name="phoneExtension"
                                                           value="<?php echo(!empty($user) ? $user['phone_extension'] : ''); ?>"
                                                           placeholder="กรอกเบอร์ต่อภายใน"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์ต่อภายใน')"
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
                            $disablePermissionUpdate = isset($userId) && !currentUserHasPermission(PERMISSION_USER_UPDATE);
                            ?>
                            <input type="hidden" name="canUpdatePermission" value="<?= $disablePermissionUpdate ? '0' : '1'; ?>"/>

                            <!--สิทธิ์การใช้งาน-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">สิทธิ์การใช้งาน
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
                                <div class="box-body pad" style="background: #f8f8f8">
                                    <!--จัดการผู้ใช้, จัดการฝ่าย-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>จัดการผู้ใช้:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionUserCreate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_USER_CREATE) ? 'checked' : '' ?>>
                                                                เพิ่ม
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionUserUpdate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_USER_UPDATE) ? 'checked' : '' ?>>
                                                                แก้ไข
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionUserDelete" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_USER_DELETE) ? 'checked' : '' ?>>
                                                                ลบ
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /.box -->
                                        </div>
                                        <!-- /.col (left) -->
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>จัดการฝ่าย:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionUserDepartmentManage" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_USER_DEPARTMENT_MANAGE) ? 'checked' : '' ?>>
                                                                เพิ่ม, แก้ไข, ลบ, เรียงลำดับฝ่าย/ผู้ใช้ในฝ่าย
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /.box -->
                                        </div>
                                        <!-- /.col (right) -->
                                    </div>
                                    <!-- /.row -->

                                    <!--บริการวิชาการ-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>บริการวิชาการ:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingCreate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_CREATE) ? 'checked' : '' ?>>
                                                                เพิ่มหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingUpdate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_UPDATE) ? 'checked' : '' ?>>
                                                                แก้ไขหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingDelete" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_DELETE) ? 'checked' : '' ?>>
                                                                ลบหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingManageRegistration" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION) ? 'checked' : '' ?>>
                                                                จัดการใบสมัคร
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingManageCourseMaster" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER) ? 'checked' : '' ?>>
                                                                จัดการข้อมูลพื้นฐาน
                                                            </label>
                                                        </div>
                                                        <!--<div class="col-md-9">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseTrainingManageCategory" type="checkbox"
                                                                       data-toggle="toggle"
                                                                    <? /*= userHasPermission($user['permissions'], PERMISSION_COURSE_TRAINING_MANAGE_CATEGORY) ? 'checked' : '' */ ?>>
                                                                จัดการหมวดหมู่หลักสูตร
                                                            </label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /.box -->
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->

                                    <!--บริการสังคม-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>บริการสังคม:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseSocialCreate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_SOCIAL_CREATE) ? 'checked' : '' ?>>
                                                                เพิ่มหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseSocialUpdate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_SOCIAL_UPDATE) ? 'checked' : '' ?>>
                                                                แก้ไขหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseSocialDelete" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_SOCIAL_DELETE) ? 'checked' : '' ?>>
                                                                ลบหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseSocialManageRegistration" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION) ? 'checked' : '' ?>>
                                                                จัดการใบสมัคร
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseSocialManageCourseMaster" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_SOCIAL_MANAGE_COURSE_MASTER) ? 'checked' : '' ?>>
                                                                จัดการข้อมูลพื้นฐาน
                                                            </label>
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

                                    <!--บริการใบขับขี่-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>บริการใบขับขี่:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseCreate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_CREATE) ? 'checked' : '' ?>>
                                                                เพิ่มหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseUpdate" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_UPDATE) ? 'checked' : '' ?>>
                                                                แก้ไขหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseDelete" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_DELETE) ? 'checked' : '' ?>>
                                                                ลบหลักสูตร
                                                            </label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseManageRegistration" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION) ? 'checked' : '' ?>>
                                                                จัดการใบสมัคร
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseManageCourseMaster" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER) ? 'checked' : '' ?>>
                                                                จัดการข้อมูลพื้นฐาน
                                                            </label>
                                                        </div>
                                                        <!--<div class="col-md-9">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionCourseDrivingLicenseManageCategory" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <? /*= userHasPermission($user['permissions'], PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_CATEGORY) ? 'checked' : '' */ ?>>
                                                                จัดการประเภทหลักสูตร
                                                            </label>
                                                        </div>-->
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /.box -->
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->

                                    <!--In-house-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>In-House Training:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-12">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionManageInHouse" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_MANAGE_IN_HOUSE) ? 'checked' : '' ?>>
                                                                จัดการสถานะการติดต่อกลับ และเนื้อหาข่าวหลักสูตรที่ผ่านมา
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /.box -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>งานวิจัยและวิชาการ:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-12">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionManageAcademicPapers" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_MANAGE_ACADEMIC_PAPERS) ? 'checked' : '' ?>>
                                                                เพิ่ม/แก้ไข/ลบงานวิจัยและวิชาการ
                                                            </label>
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

                                    <!--เนื้อหาเว็บไซต์-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <label>เนื้อหาเว็บไซต์:</label>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="row" style="margin-bottom: 5px">
                                                        <div class="col-md-12">
                                                            <label class="checkbox-inline">
                                                                <input name="permissionManageWebContent" type="checkbox"
                                                                       data-toggle="toggle" <?= $disablePermissionUpdate ? 'disabled' : ''; ?>
                                                                    <?= userHasPermission($user['permissions'], PERMISSION_MANAGE_WEB_CONTENT) ? 'checked' : '' ?>>
                                                                จัดการเนื้อหาเว็บไซต์และข้อมูลพื้นฐานอื่นๆ
                                                            </label>
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
                            </div>
                            <!-- /.box -->

                            <!--ผลงานที่ผ่านมา-->
                            <div class="box box-warning">
                                <div class="box-header with-border">
                                    <h3 class="box-title">ผลงานที่ผ่านมา
                                        <small>แสดงในหน้าเว็บ สำหรับนักวิจัย/นักวิชาการ</small>
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
                                    <div class="checkbox">
                                        <label>
                                            <input name="showDetails" type="checkbox"
                                                   data-toggle="toggle"
                                                <?= $user['show_details'] == 1 ? 'checked' : '' ?>>
                                            แสดงในหน้าเว็บ
                                        </label>
                                    </div>
                                    <textarea id="editor" rows="5" cols="80"
                                              name="details">
                                    <?php echo(!empty($user) ? $user['details'] : ''); ?>
                                </textarea>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!--รูปภาพ-->
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
                                <div class="box-body pad">
                                    <?php
                                    if (!empty($user)) {
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
                                                        <!--<a target="_blank" href="<?php /*echo(UPLOAD_DIR_DOCUMENT_DOWNLOADS . $documentDownload['image_file_name']); */ ?>">แสดงรูปภาพในหน้าจอใหม่</a>-->
                                                    </div>
                                                    <a href="<?= (UPLOAD_DIR_USER_ASSETS . $user['image_file_name']); ?>"
                                                       data-lightbox="coverImage" data-title="<?= "{$user['title']} {$user['first_name']} {$user['last_name']}"; ?>">
                                                        <img src="<?= (UPLOAD_DIR_USER_ASSETS . $user['image_file_name']); ?>"
                                                             width="200px" style="border-radius: 50%;">
                                                    </a>
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="image_tab_2" style="padding: 0px">
                                                    <ul style="color: orangered; margin-top: 10px; margin-bottom: 15px">
                                                        <li>คลิกในกรอบสี่เหลี่ยมเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยในกรอบสี่เหลี่ยม</li>
                                                        <li>รูปภาพที่อัพโหลดใหม่ จะแทนที่รูปภาพปัจจุบัน</li>
                                                        <li>ไฟล์จะถูกบันทึกเข้าสู่ระบบ หลังจากกดปุ่ม "บันทึก"</li>
                                                    </ul>
                                                    <input id="image-file-upload" name="imageFile"
                                                           type="file" accept="image/*"
                                                           style="width: 500px; margin-top: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"/>
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
                                        <input id="image-file-upload" name="imageFile" required
                                               type="file" accept="image/*"
                                               style="width: 500px; margin-top: 10px; margin-bottom: 10px; border: 2px dotted #ccc; padding: 10px 10px 50px 10px"
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

                            <!--ปุ่ม "บันทึก"-->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div id="divLoading" style="text-align: center; margin-bottom: 10px;">
                                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                    </div>
                                    <?php
                                    if ((isset($userId) && $accessByOwnerUser) ||
                                        (isset($userId) && currentUserHasPermission(PERMISSION_USER_UPDATE)) ||
                                        (!isset($userId) && currentUserHasPermission(PERMISSION_USER_CREATE))) {
                                        ?>
                                        <button id="buttonSave" type="submit"
                                                class="btn btn-info">
                                            <span class="fa fa-save"></span>&nbsp;
                                            บันทึก
                                        </button>
                                        <?php
                                    } else {
                                        ?>
                                        <button id="buttonCanNotSave" type="button"
                                                class="btn btn-danger">
                                            <span class="fa fa-ban"></span>&nbsp;
                                            คุณไม่มีสิทธิ์<?= isset($userId) ? 'แก้ไขข้อมูลผู้ใช้งาน' : 'เพิ่มผู้ใช้งาน'; ?>
                                        </button>
                                        <?php
                                    }
                                    ?>
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
        let shouldReload = false;

        $(() => {
            CKEDITOR.replace('editor');

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
                            $($.parseHTML('<img style="width: auto; height: 200px; margin: 3px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        };
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };

            $('#image-file-upload').on('change', function () {
                imagesPreview(this, 'div#image-upload-preview');
            });
        });

        $(document).ready(function () {
            lightbox.option({
                fadeDuration: 500,
                imageFadeDuration: 500,
                resizeDuration: 500,
            });

            $('#formAddUser #divLoading').hide();
            $('#formAddUser').submit(event => {
                event.preventDefault();
                doAddEditUser();
            });

            $('#formChangePassword #divLoading').hide();
            $('#formChangePassword').submit(event => {
                event.preventDefault();
                if (validateChangePasswordForm()) {
                    if (confirm('ยืนยันเปลี่ยนรหัสผ่าน?')) {
                        doChangePassword();
                    }
                }
            });

            $('#changePasswordModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    //location.reload(true);
                    window.location.href = 'login.php';
                }
            });
        });

        function onClickChangePassword(element) {
            shouldReload = false;
            $('#changePasswordModal #divLoading').hide();
            $('#changePasswordModal #alertSuccess').hide();
            $('#changePasswordModal #alertError').hide();

            $('#formChangePassword #inputOldPassword').val('');
            $('#formChangePassword #inputNewPassword').val('');
            $('#formChangePassword #inputConfirmNewPassword').val('');

            $('#changePasswordModal').modal('show');
        }

        function validateChangePasswordForm() {
            let valid = true;

            const newPassword = $('#formChangePassword #inputNewPassword').val().trim();
            const confirmNewPassword = $('#formChangePassword #inputConfirmNewPassword').val().trim();

            if (newPassword !== confirmNewPassword) {
                alert('รหัสผ่านใหม่ กับยืนยันรหัสผ่านใหม่ ไม่ตรงกัน');
                valid = false;
            }

            return valid;
        }

        function doChangePassword() {
            $('#changePasswordModal #alertSuccess').hide();
            $('#changePasswordModal #alertError').hide();
            $('#formChangePassword #buttonChangePassword').prop('disabled', true);
            $('#changePasswordModal #divLoading').show();

            $('#formChangePassword').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formChangePassword #buttonChangePassword').prop('disabled', false);
                    $('#changePasswordModal #divLoading').hide();

                    if (data.error_code === 0) {
                        let reLoginText = '';

                        <?php
                        if ($accessByOwnerUser) {
                        ?>
                        reLoginText = ', หลังจากปิดหน้าต่างนี้ คุณจะต้องเข้าสู่ระบบใหม่';
                        <?php
                        }
                        ?>

                        $('#formChangePassword #buttonChangePassword').prop('disabled', false);
                        $('#changePasswordModal #divLoading').hide();
                        $('#changePasswordModal #alertSuccess #alertSuccessText').text(data.error_message + reLoginText);
                        $('#changePasswordModal #alertSuccess').show();

                        <?php
                        if ($accessByOwnerUser) {
                        ?>
                        shouldReload = true;
                        <?php
                        }
                        ?>
                    } else {
                        $('#formChangePassword #buttonChangePassword').prop('disabled', false);
                        $('#changePasswordModal #divLoading').hide();
                        $('#changePasswordModal #alertError #alertErrorText').text(data.error_message);
                        $('#changePasswordModal #alertError').show();
                    }
                },
                error: (e) => {
                    $('#formChangePassword #buttonChangePassword').prop('disabled', false);
                    $('#changePasswordModal #divLoading').hide();
                    $('#changePasswordModal #alertError #alertErrorText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#changePasswordModal #alertError').show();
                }
            });
        }

        function doAddEditUser() {
            // อัพเดท content ของ ckeditor ไปยัง textarea
            CKEDITOR.instances.editor.updateElement();

            $('#formAddUser #buttonSave').prop('disabled', true);
            $('#formAddUser #divLoading').show();

            $('#formAddUser').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    //alert(data.error_message);
                    $('#formAddUser #buttonSave').prop('disabled', false);
                    $('#formAddUser #divLoading').hide();

                    if (data.error_code === 0) {
                        BootstrapDialog.show({
                            title: '<?php echo(isset($userId) ? 'แก้ไขข้อมูลผู้ใช้งานระบบ' : 'เพิ่มผู้ใช้งานระบบ'); ?>',
                            message: data.error_message,
                            buttons: [{
                                label: 'ปิด',
                                action: function (self) {
                                    self.close();
                                    <?php
                                    if (!isset($userId)) {
                                    ?>
                                    window.location.href = 'user.php';
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
                            title: '<?php echo(isset($userId) ? 'แก้ไขข้อมูลผู้ใช้งานระบบ' : 'เพิ่มผู้ใช้งานระบบ'); ?> - ผิดพลาด',
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
                error: (e) => {
                    $('#formAddUser #buttonSave').prop('disabled', false);
                    $('#formAddUser #divLoading').hide();

                    console.log(e);

                    BootstrapDialog.show({
                        title: '<?php echo(isset($userId) ? 'แก้ไขข้อมูลผู้ใช้งานระบบ' : 'เพิ่มผู้ใช้งานระบบ'); ?> - ผิดพลาด',
                        message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server: ' + e,
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

    <!-- MDB core JavaScript -->
    <!--<script type="text/javascript" src="../lib/mdb/mdb.min.js"></script>-->
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>