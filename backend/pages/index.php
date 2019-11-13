<?php
require_once '../api/global.php';

error_reporting(E_ERROR | E_PARSE);
header('Content-type: text/html; charset=utf-8');
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

session_start();

require_once '../include/db_config.inc';
$db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($db->connect_errno) {
    echo 'การเชื่อมต่อฐานข้อมูลล้มเหลว!';
    exit();
}
$db->set_charset("utf8");
?>

    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">
        <?php
        if (isset($_SESSION[KEY_SESSION_USER_ID])) {
            require_once('../include/header.inc');
            require_once('../include/sidebar.inc');
        }
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div style="text-align: center; padding-top: 100px">
                    <img src="../images/logo_tu.png" width="180px">
                    <div>
                        <h2>สถาบันเสริมศึกษาและทรัพยากรมนุษย์</h2>
                        <h3>INSTITUTE FOR CONTINUING EDUCATION AND HUMAN RESOURCES</h3>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div style="text-align: center">
                    <?php
                    if (!isset($_SESSION[KEY_SESSION_USER_ID])) {
                        ?>
                        <button id="loginButton" type="submit" class="btn btn-info"
                                style="padding: 15px 25px; margin-top: 50px">
                            <span class="fa fa-sign-in"></span>&nbsp;
                            เข้าสู่ระบบ
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php require_once('../include/footer.inc'); ?>
    </div>
    <!-- ./wrapper -->

    <script>
        $(document).ready(function () {
            $('#loginButton').click(function (e) {
                window.location.href = 'login.php';
            });
        });
    </script>

    <?php require_once('../include/foot.inc'); ?>
    </body>
    </html>
<?php
require_once '../include/foot_php.inc';
?>