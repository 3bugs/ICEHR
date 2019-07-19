<?
//==================================
// TT&T CLASS
//==================================
class DT
{
    var $dToday;
    var $sYear;
    var $sMon;
    var $sMday;

    //Constructor
    function DT()
    {
        $dToday = null;
        $sYear = null;
        $sMon = null;
        $sMday = null;
    }

    function diff_days($start_date, $end_date, $time = 1)
    {
        return floor(abs(strtotime($start_date) - strtotime($end_date)) / 86400);
    }

    function DateTimeShort($strdate)
    {
        $arrDatetime = explode(" ", $strdate);      //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);   //Date
        $arrTime = explode(":", $arrDatetime[1]);   //Time

        //mktime(hh, mm, ss, month day year)
        //Date

        $day = date('j', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $month = date('m', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $year = date('Y', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));    //$year += 543;

        $date = $day . "/" . $month . "/" . ($year);

        $strTime = "$arrTime[0]:$arrTime[1]";

        if ($time == 0)
            return "$date, $strTime";
        else
            return $date;
    }

    //รับค่าของปีเข้ามาเป็น พ.ศ.
    function DateTimeShortFormat($strdate, $type = 0, $time = 0, $lan = "En")
    { //type =0 ย่อ 1 เต็ม, time 0 มีเวลา ---1 ไม่มี
        $monthNameTh = array("01" => "ม.ค.", "02" => "ก.พ.", "03" => "มี.ค", "04" => "เม.ย", "05" => "พ.ค.", "06" => "มิ.ย", "07" => "ก.ค.", "08" => "ส.ค.", "09" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug.", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

        $arrDatetime = explode(" ", $strdate);            //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);        //Date
        $arrTime = explode(":", $arrDatetime[1]);        //Time

        //mktime(hh, mm, ss, month day year)
        $day = $arrDate[2];
        $month = $arrDate[1];
        $year = $arrDate[0];    //$year += 543;

        //Time
        $hh = $arrTime[0];
        $mm = $arrTime[1];
        $ampm = 0;
        /*
        //Date
        $date = date('D', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $day = date('j', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $month = date('m', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $year = date('Y', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));	//$year += 543;

        //Time
        $hh = date('H', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $mm = date('i', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $ampm = date('a', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        */

        //String Date & String Time
        if ($type == 0) {
            if ($lan == "Th")
                $strDate = "$day $monthNameTh[$month]  " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEn[$month] $day, $year";
        } elseif ($type == 1) {
            if ($lan == "Th")
                $strDate = "$day $monthNameThFull[$month]  " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEnFull[$month] $day, $year";
        }

        $strTime = "$hh:$mm"; //เปิดเมื่อต้องการเวลาแสดงในหน้าแสดงผล

        if ($time == 0)
            return "$strDate $strTime";
        elseif ($time == 1)
            return "$strDate";
    }

    function DateTimeShortFormat2($strdate, $type = 0, $time = 0, $lan = "En")
    { //type =0 ย่อ 1 เต็ม, time 0 มีเวลา ---1 ไม่มี
        $monthNameTh = array("01" => "ม.ค.", "02" => "ก.พ.", "03" => "มี.ค", "04" => "เม.ย", "05" => "พ.ค.", "06" => "มิ.ย", "07" => "ก.ค.", "08" => "ส.ค.", "09" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug.", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

        $arrDatetime = explode(" ", $strdate);            //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);        //Date
        $arrTime = explode(":", $arrDatetime[1]);        //Time

        //mktime(hh, mm, ss, month day year)
        $day = $arrDate[2];
        $month = $arrDate[1];
        $year = $arrDate[0];    //$year += 543;

        //Time
        $hh = $arrTime[0];
        $mm = $arrTime[1];
        $ampm = 0;

        //String Date & String Time
        if ($type == 0) {
            if ($lan == "Th")
                $strDate = "$day $monthNameTh[$month] " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEn[$month] $day, $year";
        } elseif ($type == 1) {
            if ($lan == "Th")
                $strDate = "$day $monthNameThFull[$month] " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEnFull[$month] $day, $year";
        }

        $strTime = "$hh:$mm"; //เปิดเมื่อต้องการเวลาแสดงในหน้าแสดงผล

        if ($time == 0)
            return "$strDate";
        elseif ($time == 1)
            return "$strDate";
    }

    function DateTimeShortFormat3($strdate, $type = 0, $time = 0, $lan = "En")
    { //type =0 ย่อ 1 เต็ม, time 0 มีเวลา ---1 ไม่มี
        $monthNameTh = array("01" => "ม.ค.", "02" => "ก.พ.", "03" => "มี.ค", "04" => "เม.ย", "05" => "พ.ค.", "06" => "มิ.ย", "07" => "ก.ค.", "08" => "ส.ค.", "09" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug.", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

        $arrDatetime = explode(" ", $strdate);            //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);        //Date
        $arrTime = explode(":", $arrDatetime[1]);        //Time

        //mktime(hh, mm, ss, month day year)
        $day = $arrDate[2];
        $month = $arrDate[1];
        $year = $arrDate[0] += 543;    //$year += 543;
        $yy = substr($year, 2, 2);

        //Time
        $hh = $arrTime[0];
        $mm = $arrTime[1];
        $ampm = 0;
        /*
        //Date
        $date = date('D', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $day = date('j', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $month = date('m', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $year = date('Y', mktime(0,0,0, $arrDate[1], $arrDate[2], $arrDate[0]));	//$year += 543;

        //Time
        $hh = date('H', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $mm = date('i', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $ampm = date('a', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        */


        //String Date & String Time
        if ($type == 0) {
            if ($lan == "Th")
                $strDate = "$day $monthNameTh[$month]  " . $yy;
            elseif ($lan == "En")
                $strDate = "$monthNameEn[$month] $day, $year";
        } elseif ($type == 1) {
            if ($lan == "Th")
                $strDate = "$day $monthNameThFull[$month]  " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEnFull[$month] $day, $year";
        }

        $strTime = "$hh:$mm"; //เปิดเมื่อต้องการเวลาแสดงในหน้าแสดงผล

        if ($time == 0)
            return "$strDate";
        elseif ($time == 1)
            return "$strDate";
    }


    function DateTimeFullFormat($strdate, $type = 0, $time = 0, $lan = "En")
    { //type =0 ย่อ 1 เต็ม, time 0 มีเวลา ---1 ไม่มี
        $monthNameTh = array("01" => "ม.ค.", "02" => "ก.พ.", "03" => "มี.ค", "04" => "เม.ย", "05" => "พ.ค.", "06" => "มิ.ย", "07" => "ก.ค.", "08" => "ส.ค.", "09" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug.", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

        $arrDatetime = explode(" ", $strdate);            //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);        //Date
        $arrTime = explode(":", $arrDatetime[1]);        //Time

        //mktime(hh, mm, ss, month day year)
        //Date
        $date = date('D', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $day = date('j', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $month = date('m', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $year = date('Y', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));    //$year += 543;

        //Time
        $hh = date('H', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $mm = date('i', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $ampm = date('a', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));

        //String Date & String Time
        if ($type == 0) {
            if ($lan == "Th")
                $strDate = "วัน" . DT::getDay($date) . "ที่ $day $monthNameTh[$month] พ.ศ. " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEn[$month] $day, $year";
        } elseif ($type == 1) {
            if ($lan == "Th")
                $strDate = "วัน" . DT::getDay($date) . "ที่ $day $monthNameThFull[$month] พ.ศ. " . $year += 543;
            elseif ($lan == "En")
                $strDate = "$monthNameEnFull[$month] $day, $year";
        }

        $strTime = "$hh:$mm";

        if ($time == 0)
            return "$strDate $strTime";
        elseif ($time == 1)
            return "$strDate";
    }

    function DateTimeFullFormat2($strdate, $type = 0, $time = 0, $lan = "En")
    { //type =0 ย่อ 1 เต็ม, time 0 มีเวลา ---1 ไม่มี
        $monthNameTh = array("01" => "ม.ค.", "02" => "ก.พ.", "03" => "มี.ค.", "04" => "เม.ย.", "05" => "พ.ค.", "06" => "มิ.ย.", "07" => "ก.ค.", "08" => "ส.ค.", "09" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน", "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม", "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug.", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

        $arrDatetime = explode(" ", $strdate);            //DateTime yyyy-mm-dd  2545-12-20 13:31:00.000
        $arrDate = explode("-", $arrDatetime[0]);        //Date
        $arrTime = explode(":", $arrDatetime[1]);        //Time

        //mktime(hh, mm, ss, month day year)
        //Date
        $date = date('D', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $day = date('j', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $month = date('m', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
        $year = date('Y', mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));    //$year += 543;
//
//			//Time
//			$hh = date('H', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
//			$mm = date('i', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));
//			$ampm = date('a', mktime($arrTime[0], $arrTime[1], 0, $arrDate[1], $arrDate[2], $arrDate[0]));

        //String Date & String Time
        if ($type == 0) {
            if ($lan == "Th")
                $strDate = "วัน" . DT::getDay($date) . "ที่ $day $monthNameTh[$month] พ.ศ. " . thainumDigit($year += 543);
            elseif ($lan == "En")
                $strDate = "$monthNameEn[$month] $day, $year";
        } elseif ($type == 1) {
            if ($lan == "Th")
                $strDate = thainumDigit($day) . " เดือน$monthNameThFull[$month] พ.ศ. " . thainumDigit($year += 543);
            elseif ($lan == "En")
                $strDate = "$monthNameEnFull[$month] $day, $year";
        }

        $strTime = "$hh:$mm";

        if ($time == 0)
            return "$strDate $strTime";
        elseif ($time == 1)
            return "$strDate";
    }

    function monthName($monthnum, $type = 0, $lan = "En")
    { //0 ย่อ  1 เต็ม
        $monthNameThShort = array("1" => "ม.ค.", "2" => "ก.พ.", "3" => "มี.ค", "4" => "เม.ย", "5" => "พ.ค.", "6" => "มิ.ย", "7" => "ก.ค.", "8" => "ส.ค.", "9" => "ก.ย.", "10" => "ต.ค.", "11" => "พ.ย.", "12" => "ธ.ค.");
        $monthNameThFull = array("1" => "มกราคม", "2" => "กุมภาพันธ์", "3" => "มีนาคม", "4" => "เมษายน", "5" => "พฤษภาคม", "6" => "มิถุนายน", "7" => "กรกฎาคม", "8" => "สิงหาคม", "9" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม");

        $monthNameEn = array("1" => "Jan", "2" => "Feb", "3" => "Mar", "4" => "Apr", "5" => "May", "6" => "Jun", "7" => "Jul", "8" => "Aug.", "9" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
        $monthNameEnFull = array("1" => "January", "2" => "February", "3" => "March", "4" => "April", "5" => "May", "6" => "June", "7" => "July", "8" => "August", "9" => "September", "10" => "October", "11" => "November", "12" => "December");

        if ($type == 0) {
            if ($lan == "Th")
                return $monthNameThShort[$monthnum];
            elseif ($lan == "En")
                return $monthNameEnShort[$monthnum];
        } elseif ($type == 1) {
            if ($lan == "Th")
                return $monthNameThFull[$monthnum];
            elseif ($lan == "En")
                return $monthNameEnFull[$monthnum];
        }
    }

    function getDay($dateEn)
    {
        if ($dateEn == "Mon") {
            return "จันทร์";
        } elseif ($dateEn == "Tue") {
            return "อังคาร";
        } elseif ($dateEn == "Wed") {
            return "พุธ";
        } elseif ($dateEn == "Thu") {
            return "พฤหัสบดี";
        } elseif ($dateEn == "Fri") {
            return "ศุกร์";
        } elseif ($dateEn == "Sat") {
            return "เสาร์";
        } elseif ($dateEn == "Sun") {
            return "อาทิตย์";
        }
    }

    //ใช้ในการสร้าง option ที่เป็นการรับเดือนเข้าสู่ระบบ โดยที่ $sel จะเป็นการชี้ไปที่เดือนที่เราได้เลือกไว้
    function SelectMonth($sel = 0, $lan)
    {
        $strOption = "";
        for ($i = 1; $i <= 12; $i++) {
            $selected = ($sel == $i) ? " selected" : "";

            $strOption .= "<option value=\"$i\"$selected>" . DT::monthName($i, 1, $lan) . "</option>\r\n";
        }
        return $strOption;
    }

    function date2db($date, $delimiter = "-")
    {
        $d = array();
        $d[year] = substr($date, 0, 4);
        $d[month] = substr($date, 4, 2);
        $d[day] = substr($date, 6, 2);

        return $d[year] . $delimiter . $d[month] . $delimiter . $d[day];  //yyyy-mm-dd
    }

    /**
     * Returns formated date according to current local and adds time offset
     * @param string date in datetime format
     * @param string format optional format for strftime
     * @param offset time offset if different than global one
     * @returns formated date
     */
    function mosFormatDate($date, $format = "", $offset = "")
    {
        global $mosConfig_offset;
        if ($format == '') {
            // %Y-%m-%d %H:%M:%S
            $format = _DATE_FORMAT_LC;
        }
        if ($offset == '') {
            $offset = $mosConfig_offset;
        }
        if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs)) {
            $date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
            $date = $date > -1 ? strftime($format, $date + ($offset * 60 * 60)) : '-';
        }
        return $date;
    }

    /**
     * Returns current date according to current local and time offset
     * @param string format optional format for strftime
     * @returns current date
     */
    function mosCurrentDate($format = "")
    {
        global $mosConfig_offset;
        if ($format == "") {
            $format = _DATE_FORMAT_LC;
        }
        $date = strftime($format, time() + ($mosConfig_offset * 60 * 60));
        return $date;
    }

}

?>