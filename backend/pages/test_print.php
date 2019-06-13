<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once('../include/head.inc'); ?>
    <style>
        @media print {
            @page {
                margin: 0;
            }

            html {
                font-size: 16px;
            }

            @font-face {
                font-family: 'db-heavent';
                src: url('../fonts/DBHeavent.eot');
                font-weight: 100;
                font-style: normal;
            }

            #paymentDate {
                font-family: 'db-heavent';
                font-size: 0.8rem;
                position: absolute;
                top: 2.7cm;
                left: 9cm;
            }

            #receiptName {
                font-family: 'db-heavent';
                font-size: 0.8rem;
                position: absolute;
                top: 3.2cm;
                left: 8.5cm;
            }

            #address {
                font-family: 'db-heavent';
                font-size: 0.8rem;
                position: absolute;
                top: 3.8cm;
                left: 6.8cm;
            }

            #province {
                font-family: 'db-heavent';
                font-size: 0.8rem;
                position: absolute;
                top: 4.3cm;
                left: 6.8cm;
            }

            #listContainer {
                font-family: 'db-heavent';
                font-size: 1rem;
                position: absolute;
                top: 5.5cm;
                left: 6cm;
                width: 14.2cm;
            }

            .listContainerRow {
                padding: 0.2cm;
            }

            #courseName {
                display: inline-block;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 10.6cm;
            }

            #courseFeeLabel {
                display: inline-block;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 10.6cm;
            }

            #traineeName {
                display: inline-block;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 10.6cm;
            }

            #courseFee {
                display: inline-block;
                text-align: right;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 2.8cm;
            }

            #totalText {
                display: inline-block;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 10.6cm;
            }

            #total {
                display: inline-block;
                text-align: right;
                font-family: 'db-heavent';
                font-size: 0.95rem;
                width: 2.8cm;
            }
        }
    </style>
    <script type="text/javascript">
        window.onload = function () {
            window.print();
        }
    </script>
</head>
<body>
<div id="paymentDate">11/06/62</div>
<div id="receiptName">นายพร้อมเลิศ หล่อวิจิตร</div>
<div id="address">11/13 วรวรรณ พาร์ค คอนโดมิเนียม ซ.งามวงศ์วาน 59 แขวงลาดยาว เขตจตุจักร</div>
<div id="province">กรุงเทพมหานคร</div>
<div id="listContainer">
    <div class="listContainerRow">
        <div id="courseName">การบริหารความหลากหลายของบุคลากรภายในองค์การ (Diversity Management) รุ่นที่ 1</div>
    </div>
    <div class="listContainerRow">
        <div id="courseFeeLabel">ค่าลงทะเบียนอบรม</div>
        <div id="courseFee">6,200.00</div>
    </div>
    <div class="listContainerRow">
        <div id="traineeName">นายพร้อมเลิศ หล่อวิจิตร</div>
    </div>
    <div class="listContainerRow" style="position: absolute; top: 9cm">
        <div id="totalText">(หกพันสองร้อยบาทถ้วน)</div>
        <div id="total">6,200.00</div>
    </div>
</div>
</body>
</html>