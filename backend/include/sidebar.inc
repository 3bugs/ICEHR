<?php
require_once '../api/global.php';
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-mortar-board"></i> <span><?php echo SERVICE_TYPE_TRAINING_TEXT; ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li style="margin-left: 20px"><a href="../pages/course_master.php?service_type=<?php echo SERVICE_TYPE_TRAINING; ?>"><i class="fa fa-font"></i>ชื่อหลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course.php?service_type=<?php echo SERVICE_TYPE_TRAINING; ?>"><i class="fa fa-server"></i>หลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course_add_edit.php?service_type=<?php echo SERVICE_TYPE_TRAINING; ?>"><i class="fa fa-plus"></i>เพิ่มหลักสูตร</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-male"></i> <span><?php echo SERVICE_TYPE_SOCIAL_TEXT; ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li style="margin-left: 20px"><a href="../pages/course_master.php?service_type=<?php echo SERVICE_TYPE_SOCIAL; ?>"><i class="fa fa-font"></i>ชื่อหลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course.php?service_type=<?php echo SERVICE_TYPE_SOCIAL; ?>"><i class="fa fa-server"></i>หลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course_add_edit.php?service_type=<?php echo SERVICE_TYPE_SOCIAL; ?>"><i class="fa fa-plus"></i>เพิ่มหลักสูตร</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-car"></i> <span><?php echo SERVICE_TYPE_DRIVING_LICENSE_TEXT; ?></span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li style="margin-left: 20px"><a href="../pages/course_master.php?service_type=<?php echo SERVICE_TYPE_DRIVING_LICENSE; ?>"><i class="fa fa-font"></i>ชื่อหลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course.php?service_type=<?php echo SERVICE_TYPE_DRIVING_LICENSE; ?>"><i class="fa fa-server"></i>หลักสูตร</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course_add_edit.php?service_type=<?php echo SERVICE_TYPE_DRIVING_LICENSE; ?>"><i class="fa fa-plus"></i>เพิ่มหลักสูตร</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i> <span>ใบสมัคร</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li style="margin-left: 20px"><a href="../pages/course_registration.php?service_type=<?php echo SERVICE_TYPE_TRAINING; ?>"><i class="fa fa-mortar-board"></i>&nbsp;&nbsp;วิชาการ</a></li>
                    <li style="margin-left: 20px"><a href="../pages/course_registration.php?service_type=<?php echo SERVICE_TYPE_DRIVING_LICENSE; ?>"><i class="fa fa-car"></i>&nbsp;&nbsp;ใบขับขี่</a></li>
                </ul>
            </li>
            <li>
                <a href="../pages/member.php">
                    <i class="fa fa-users"></i> <span>สมาชิกเว็บไซท์</span>
                </a>
            </li>
            <li>
                <a href="../pages/user.php" id="linkUser">
                    <i class="fa fa-user-circle"></i> <span>ผู้ใช้งานระบบ</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-money"></i> <span>การแจ้งโอนเงิน</span>
                </a>
            </li>
            <li class="header" style="color: yellow; text-align: center">จัดการเนื้อหา</li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-text-o"></i> <span>บริการ/กิจกรรม</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i> บริการวิชาการ</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> บริการ In-house Training</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> บริการสังคม</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> บริการอบรมใบขับขี่</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> โครงการวิจัยและวิชาการ</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> วารสาร HR INTELLIGENCE</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-newspaper-o"></i> <span>ข่าวประชาสัมพันธ์</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i> ข่าวโครงการฝึกอบรม</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> ข่าวประชาสัมพันธ์</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-image"></i> <span>ภาพกิจกรรม</span>
                </a>
            </li>
            <!--<li>
                <a href="#">
                    <i class="fa fa-link"></i> <span>WEB LINKS</span>
                </a>
            </li>-->
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
<script>
    $(document).ready(function () {
        // Sets active link in Bootstrap menu
        // Add this code in a central place used\shared by all pages
        // like your _Layout.cshtml in ASP.NET MVC for example
        const url = (location.pathname + location.search).substr(1);
        $('a[href="' + url + '"]').parents('li,ul').addClass('active');
        //alert(url);
    });
</script>