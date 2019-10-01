<header class="main-header">
    <!-- Logo -->
    <a class="logo" href="../pages/index.php">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>TU</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>ICEHR</b> Backend</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a class="sidebar-toggle" data-toggle="push-menu" href="#" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <?php
        if (isset($_SESSION[KEY_SESSION_USER_ID])) {
            $username = $_SESSION[KEY_SESSION_USER_USERNAME];
            $firstName = $_SESSION[KEY_SESSION_USER_FIRST_NAME];
            $lastName = $_SESSION[KEY_SESSION_USER_LAST_NAME];
            $displayName = "$firstName $lastName";
            $profileImageUrl = $_SESSION[KEY_SESSION_USER_PROFILE_IMAGE_URL];
            switch ($_SESSION[KEY_SESSION_USER_ROLE]) {
                case ROLE_USER:
                    $role = 'User';
                    break;
                case ROLE_ADMIN:
                    $role = 'Administrator';
                    break;
                case ROLE_SUPER_ADMIN:
                    $role = 'Super Administrator';
                    break;
            }
            ?>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt="User Image" class="user-image" src="<?= $profileImageUrl; ?>">
                            <span class="hidden-xs"><?= $displayName; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img alt="User Image" class="img-circle" src="<?= $profileImageUrl; ?>">
                                <p>
                                    <?= $displayName; ?>
                                    <small>(<?= $username; ?>)</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <!--<div class="pull-left">
                                    <a id="changePasswordButton" class="btn btn-default btn-flat" href="#">เปลี่ยนรหัสผ่าน</a>
                                </div>-->
                                <div class="pull-right">
                                    <a id="logoutButton" class="btn btn-default btn-flat" href="#">ออกจากระบบ</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php
        }
        ?>
    </nav>
</header>

<script>
    function doLogout() {
        $.post(
            '../api/api.php/logout_user'
        ).done(function (data) {
            if (data.error_code === 0) {
                window.location.href = "index.php";
            } else {
                BootstrapDialog.show({
                    title: 'Logout',
                    message: data.error_message,
                    buttons: [{
                        label: 'ปิด',
                        action: function(self){
                            self.close();
                        }
                    }]
                });
            }
        }).fail(function () {
            BootstrapDialog.show({
                title: 'Logout',
                message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                buttons: [{
                    label: 'ปิด',
                    action: function(self){
                        self.close();
                    }
                }]
            });
        });
    }

    $(document).ready(function () {
        $('#logoutButton').click(function (e) {
            BootstrapDialog.show({
                title: 'Confirm Logout',
                message: 'ยืนยันออกจากระบบ?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                        }
                    },
                    {
                        label: 'ออกจากระบบ',
                        action: function (self) {
                            self.close();
                            doLogout();
                        }
                    }
                ]
            });
        });

        /*$('#changePasswordButton').click(function (e) {

        });*/
    });
</script>