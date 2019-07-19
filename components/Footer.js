import Link from 'next/link';
import AutoSuggest from "react-autosuggest";

export default class Footer extends React.Component {
    constructor(props, context) {
        super(props, context);

        this.state = {};
    }

    componentDidMount() {
    }

    render() {
        return (
            <React.Fragment>
                <div className="footerbg wow fadeInUp">
                    <div className="container">
                        <div className="row">
                            <div className="col-12 col-md-3 col-xl-3">
                                <div className="logo-footer"><img src="/static/images/logo_icess_LOGO%20ICT.svg" className="img-fluid"/></div>
                                <div className="social-footer">
                                    <h3>สถาบันเสริมศึกษาและทรัพยากรมนุษย์</h3>
                                    <li><a href="#"><i className="fab fa-facebook-f"/></a></li>
                                    <li><a href="#"><i className="fab fa-twitter"/></a></li>
                                    <li><a href="#"><i className="fab fa-youtube"/></a></li>
                                    <li><a href="#"><i className="fab fa-instagram"/></a></li>
                                    <li><a href="#"><i className="fab fa-line"/></a></li>
                                </div>
                                <div className="accordion-container">
                                    <div className="set"><a href="javascript:void(0)">
                                        ศูนย์ท่าพระจันทร์
                                        <i className="fa fa-plus"/>
                                    </a>
                                        <div className="content-footer">
                                            <p>2 ถนนพระจันทร์ แขวงพระบรมมหาราชวัง, เขตพระนคร, กรุงเทพฯ 10200
                                                <br/> Tel. +66 (0) 261 3333
                                            </p>
                                        </div>
                                    </div>
                                    <div className="set"><a href="javascript:void(0)">
                                        ศูนย์รังสิต
                                        <i className="fa fa-plus"/>
                                    </a>
                                        <div className="content-footer">
                                            <p>99 หมู่ 18, ถ.พหลโยธิน, คลองหลวง, รังสิต, ปทุมธานี, 12121
                                                <br/> Tel. +66 (0) 564 4440-79
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-12 col-md-9 col-xl-9 footer-crop">
                                <div className="row">
                                    <div className="col">
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">บริการ</h4>
                                            <ul>
                                                <li><a href="service-1.php" target="_parent">บริการฝึกอบรม</a></li>
                                                <li><a href="service-4.php" target="_parent">IN-HOUSE Training</a></li>
                                                <li><a href="service-3.php" target="_parent">บริการสังคม</a></li>
                                                <li><a href="service-2.php" target="_parent">บริการอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่</a></li>
                                                <li><a href="service-5.php" target="_parent">วิจัยและวิชาการ</a></li>
                                                <li><a href="#" target="_parent">วารสาร HR Intelligence</a></li>
                                            </ul>
                                        </div>
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">แนะนำหน่วยงาน</h4>
                                            <ul>
                                                <li><a href="vision.php" target="_parent">เกี่ยวกับองค์กร</a></li>
                                                <li><a href="mission.php" target="_parent">ภารกิจ </a></li>
                                                <li><a href="structure.php" target="_parent"> โครงสร้างองค์กร </a></li>
                                                <li><a href="executive.php" target="_parent"> ผู้บริหารและบุคลากร</a></li>
                                            </ul>
                                        </div>
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">เอกสารดาวน์โหลด</h4>
                                            <ul>
                                                <li><a href="download-page.php">เอกสารการอบรม </a></li>
                                                <li><a href="download-page2.php">รายงานผลการดำเนินงาน</a></li>
                                                <li><a href="download-page3.php">KM</a></li>
                                                <li><a href="download-page5.php">ประกันคุณภาพ (QA)</a></li>
                                                <li><a href="download-page4.php"> จุลสาร/อินโฟกราฟิค</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-12 col-md-6">
                                        <div className="footer-link-list">
                                            <ul>
                                                <li><a href="#">Site map</a></li>
                                                <li><a href="#">Thammasat University</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="col-12 col-md-6 tu-copyr"> © Thammasat University</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .footerbg {
                        background-color: #F1F1F3;
                        width: 100%;
                        margin-top: 20px;
                        padding-top: 40px;
                        padding-bottom: 40px;
                    }
                    
                    .footer-crop {
                        padding-left: 10%;
                    }
                    
                    .footer-crop:before {
                        background-color: #d4d4d4;
                    }
                    
                    .footer-crop:before {
                        content: "";
                        position: absolute;
                        width: 1px;
                        top: 0;
                        left: 0;
                        bottom: 0;
                    }
                    
                    .logo-footer {
                        margin-left: 20px;
                    }
                    
                    .footer-crop,
                    .logo-footer {
                        margin-top: 20px;
                    }
                    
                    .logo-footer > img {
                        width: 80%;
                    }
                    
                    .social-footer {
                        margin-left: 20px;
                    }
                    
                    .social-footer h3 {
                        font-family: 'DBHeavent-Med';
                        font-size: 1.1rem;
                        padding-top: 20px;
                    }
                    
                    .social-footer li {
                        list-style: none;
                        display: inline-block;
                        padding-right: 10px;
                    }
                    
                    .social-footer li a {
                        color: #1f1f1f;
                        font-size: 0.9rem;
                    }
                    
                    .social-footer li i:hover {
                        color: #b40303;
                        transition: all .3s ease-in-out;
                        -moz-transition: all .3s ease-in-out;
                        -webkit-transition: all .3s ease-in-out;
                    }
                    
                    .footer-menugroup {
                        display: inline-block;
                        float: left;
                        width: 33.333333%;
                        vertical-align: top;
                    }
                    
                    .footer-link-list ul,
                    .footer-menugroup ul {
                        margin-left: -38px;
                    }
                    
                    .footer-menugroup li {
                        list-style: none;
                        font-size: 1rem;
                    }
                    
                    .footer-menugroup li a {
                        color: #646469;
                    }
                    
                    .title-footer {
                        font-size: 1.1rem;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .footer-link-list li {
                        list-style: none;
                        display: inline-block;
                        padding-right: 10px;
                        font-size: 1rem;
                    }
                    
                    .footer-link-list ul li a {
                        color: #c00832;
                        text-decoration: underline;
                        -webkit-transition: all .3s ease;
                        transition: all .3s ease;
                    }
                    
                    .tu-copyr {
                        text-align: right;
                        font-size: 1rem;
                        color: #646469;
                    }
                    
                    .accordion-container {
                        position: relative;
                        height: auto;
                        margin: 10px auto;
                    }
                    
                    .accordion-container > h2 {
                        text-align: center;
                        color: #fff;
                        padding-bottom: 5px;
                        margin-bottom: 20px;
                        padding-bottom: 15px;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    .set {
                        position: relative;
                        width: 100%;
                        height: auto;
                    }
                    
                    .set > a {
                        display: block;
                        padding: 10px 15px;
                        text-decoration: none;
                        color: black;
                        border-bottom: 1px solid #ddd;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .set > a i {
                        float: right;
                        color: #a7a6a6;
                        margin-top: 10px;
                        font-size: 0.6rem;
                    }
                    
                    .content-footer {
                        border-bottom: 1px solid #ddd;
                        display: none;
                    }
                    
                    .content-footer p {
                        padding: 10px 15px;
                        margin: 0;
                        color: #646469;
                    }
                    
                    @media (max-width: 1199px) {
                        .footer-crop {
                            padding-left: 4%;
                        }
                    }
                    
                    @media (max-width: 991px) {
                        .footer-menugroup {
                            width: 50%;
                        }
                        .footer-crop {
                            padding-left: 3%;
                        }
                        .logo-footer > img {
                            width: 40%;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .social-footer {
                            margin-left: 10px;
                        }
                    }
                    
                    @media (max-width: 767px) {
                        .logo-footer {
                            margin-left: 0px;
                        }
                        .footer-menugroup {
                            width: 100%;
                        }
                        .tu-copyr {
                            text-align: left;
                            float: left;
                        }
                        .social-footer {
                            margin-left: 10px;
                        }
                        .footer-crop {
                            padding-left: 10%;
                        }
                        .footer-crop:before {
                            display: none;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .logo-footer > img {
                            width: 70%;
                        }
                    }
                    
                    @media (max-width: 575px) {
                        .footer-crop {
                            padding-left: 9%;
                        }
                        .footer-crop:before {
                            display: none;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .logo-footer > img {
                            width: 70%;
                        }
                    }
                `}</style>
            </React.Fragment>
        );
    }
}