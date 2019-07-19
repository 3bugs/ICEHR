<?php
function getCourseDetailsForm()
{
    ?>
    <!--รายละเอียดหลักสูตร-->
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">รายละเอียดหลักสูตร</h3>

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

            <!--ชื่อและรุ่นหลักสูตร-->
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label for="selectCourseMaster">ชื่อหลักสูตร:</label>
                        <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-font"></i>
                                                </span>
                            <select id="selectCourseMaster" class="form-control" required
                                    oninvalid="this.setCustomValidity('เลือกชื่อหลักสูตร')"
                                    oninput="this.setCustomValidity('')">
                                <option value="" disabled selected>-- เลือกชื่อหลักสูตร --</option>
                                <?php
                                foreach ($courseMasterList as $cm) {
                                    $courseMasterId = $cm['id'];
                                    $courseMasterTitle = $cm['title'];
                                    $selected = '';
                                    if (!empty($course) && ($course['course_master_id'] === $courseMasterId)) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $courseMasterId; ?>" <?php echo $selected ?>>
                                        <?php echo $courseMasterTitle ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="inputBatchNumber">รุ่นที่:</label>
                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-hashtag"></i>
                                            </span>
                            <input type="number" class="form-control"
                                   id="inputBatchNumber"
                                   value="<?php echo(!empty($course) ? $course['batch_number'] : ''); ?>"
                                   placeholder="กรอกเลขรุ่น" required
                                   oninvalid="this.setCustomValidity('กรอกเลขรุ่น')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
            </div>

            <!--ค่าสมัครและวันอบรม-->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="inputTraineeLimit">รับผู้เข้าอบรมจำนวน (ท่าน):</label>
                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-users"></i>
                                            </span>
                            <input type="number" class="form-control"
                                   id="inputTraineeLimit"
                                   value="<?php echo(!empty($course) ? $course['trainee_limit'] : ''); ?>"
                                   placeholder="กรอกจำนวนผู้เข้าอบรมที่รับได้" required
                                   oninvalid="this.setCustomValidity('กรอกจำนวนผู้เข้าอบรมที่รับได้')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="inputApplicationFee">ค่าสมัคร (บาท):</label>
                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-dollar"></i>
                                            </span>
                            <input type="number" class="form-control"
                                   id="inputApplicationFee"
                                   value="<?php echo(!empty($course) ? $course['application_fee'] : ''); ?>"
                                   placeholder="กรอกค่าสมัคร" required
                                   oninvalid="this.setCustomValidity('กรอกค่าสมัคร')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="inputBeginDate">วันอบรมวันแรก:</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php
                            if (!empty($course)) {
                                $beginDatePart = explode('-', $course['begin_date']);
                                $year = $beginDatePart[0];
                                $month = $beginDatePart[1];
                                $day = $beginDatePart[2];
                                $beginDate = "$day/$month/$year";
                            }
                            ?>
                            <input type="text" class="form-control pull-right"
                                   id="inputBeginDate"
                                   value="<?php echo(!empty($course) ? $beginDate : ''); ?>"
                                   placeholder="เลือกวันอบรมวันแรก" required
                                   oninvalid="this.setCustomValidity('เลือกวันอบรมวันแรก')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="inputEndDate">วันอบรมวันสุดท้าย:</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <?php
                            if (!empty($course)) {
                                $endDatePart = explode('-', $course['end_date']);
                                $year = $endDatePart[0];
                                $month = $endDatePart[1];
                                $day = $endDatePart[2];
                                $endDate = "$day/$month/$year";
                            }
                            ?>
                            <input type="text" class="form-control pull-right"
                                   id="inputEndDate"
                                   value="<?php echo(!empty($course) ? $endDate : ''); ?>"
                                   placeholder="เลือกวันอบรมวันสุดท้าย" required
                                   oninvalid="this.setCustomValidity('เลือกวันอบรมวันสุดท้าย')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
            </div>

            <!--สถานที่อบรม-->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="inputPlace">สถานที่อบรม:</label>
                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-map-marker"></i>
                                            </span>
                            <input type="text" class="form-control"
                                   id="inputPlace"
                                   value="<?php echo(!empty($course) ? $course['place'] : ''); ?>"
                                   placeholder="กรอกสถานที่อบรม" required
                                   oninvalid="this.setCustomValidity('กรอกสถานที่อบรม')"
                                   oninput="this.setCustomValidity('')">
                        </div>
                    </div>
                </div>
            </div>

            <!--ผู้รับผิดชอบโครงการ-->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="selectResponsibleUser">ผู้รับผิดชอบโครงการ:</label>
                        <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-user-secret"></i>
                                                </span>
                            <select id="selectResponsibleUser" class="form-control" required
                                    oninvalid="this.setCustomValidity('เลือกผู้รับผิดชอบโครงการ')"
                                    oninput="this.setCustomValidity('')">
                                <option value="" disabled selected>-- เลือกผู้รับผิดชอบโครงการ --</option>
                                <?php
                                foreach ($userList as $u) {
                                    $userId = $u['id'];
                                    $userFirstName = $u['first_name'];
                                    $userLastName = $u['last_name'];
                                    $userEmail = $u['email'];
                                    $userPhoneOffice = $u['phone_office'];

                                    $selected = '';
                                    if (!empty($course) && ($course['responsible_user_id'] === $userId)) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo $userId; ?>" <?php echo $selected; ?>>
                                        <?php echo "$userFirstName $userLastName  |  $userEmail  |  $userPhoneOffice"; ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <?php
}
?>
