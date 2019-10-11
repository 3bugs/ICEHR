import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import fetch from "isomorphic-unfetch";
import {HOST_BACKEND} from '../etc/constants';
import {Element, scroller} from "react-scroll";

export default class Personnel extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async function ({req, query}) {
        let departmentList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const result = await fetch(baseUrl + '/api/get_user', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'about',
            }),
        });
        const resultJson = await result.json();
        if (resultJson['error']['code'] === 0) {
            departmentList = resultJson['dataList'];
            errorMessage = null;
        } else {
            aboutList = null;
            errorMessage = resultJson['error']['message'];
        }

        return {departmentList, errorMessage};
    };

    componentDidMount() {
        var WFw = $(".wrap_wb_fixed").width();
        var Wdh = $(window).height();
        $(document).on('scroll', function () {
            if ($('.wb_fixed')[0].offsetTop < $(document).scrollTop()) {
                $(".wb_fixed").css({
                    position: "fixed"
                    , top: 90
                    , width: WFw
                    , 'max-height': Wdh - 100
                });
            }
            if ($(document).scrollTop() < $(".wrap_wb_fixed")[0].offsetTop) {
                $(".wb_fixed").css({
                    position: "static"
                    , top: 0
                    , 'max-height': 999
                });
            }
        });

        $(".single_image").fancybox();
        $("a.inline").fancybox({
            'hideOnContentClick': true
        });

        $("select").click(function () {
            var open = $(this).data("isopen");
            if (open) {
                window.location.href = $(this).val()
            }
            //set isopen to opposite so next time when use clicked select box
            //it wont trigger this event
            $(this).data("isopen", !open);
        });

        /*
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top - 80
            }, 900, 'swing', function() {
                //window.location.hash = target;
            });
            //sidebar-menu
        });
        */

        /*$(window).scroll(function() {
            var scroll = $(window).scrollTop();

            var sec2 = $('.content1').offset().top - 80;
            var sec3 = $('.content2').offset().top - 80;
            var sec4 = $('.content3').offset().top - 80;
            var sec5 = $('.content4').offset().top - 80;
            var sec6 = $('.content5').offset().top - 80;
            var sec7 = $('.content6').offset().top - 80;

            if (scroll >= sec7) {
                $('.link').removeClass('active');
                $('.link[href="#content6"]').addClass('active');
            } else if (scroll >= sec6) {
                $('.link').removeClass('active');
                $('.link[href="#content5"]').addClass('active');
            } else if (scroll >= sec5) {
                $('.link').removeClass('active');
                $('.link[href="#content4"]').addClass('active');
            } else if (scroll >= sec4) {
                $('.link').removeClass('active');
                $('.link[href="#content3"]').addClass('active');
            } else if (scroll >= sec3) {
                $('.link').removeClass('active');
                $('.link[href="#content2"]').addClass('active');
            } else if (scroll >= sec2) {
                $('.link').removeClass('active');
                $('.link[href="#content1"]').addClass('active');
            } else {
                $('.link').removeClass('active');
            }
        });*/
    }

    scrollToDepartment = departmentId => {
        scroller.scrollTo('department' + departmentId, {
            duration: 500,
            smooth: true,
            offset: -90,
        });
    };

    render() {
        return (
            <MainLayout>
                <NextHead>
                    <script src="/static/fancybox/source/jquery.fancybox.js?v=2.1.5"/>
                    <link rel="stylesheet" href="/static/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>
                </NextHead>

                <React.Fragment>
                    <div className="banner-executive-top"></div>
                    <div className="bgnav-abt">
                        <div className="container d-none d-sm-block d-md-block d-lg-block d-xl-block">
                            <div className="row">
                                <div className="col">
                                    <ul className="aboutmenu">
                                        <li><a href="/about">เกี่ยวกับองค์กร </a></li>
                                        <li><a href="/mission">ภารกิจ </a></li>
                                        <li><a href="/organization">โครงสร้างองค์กร </a></li>
                                        <li><a href="/personnel" className="active-menu">ผู้บริหารและบุคลากร </a></li>
                                        <li><a href="/committee">คณะกรรมการ </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="container  d-block d-sm-none d-md-none d-lg-none d-xl-none">
                            <div className="row">
                                <div className="col">
                                    <div className="menu-wrapper">
                                        <ul className="menu">
                                            <li className="menu-item active">
                                                <a href="/about">เกี่ยวกับองค์กร </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/mission"> ภารกิจ </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/organization"> โครงสร้างองค์กร </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/personnel"> ผู้บริหารและบุคลากร </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/committee"> คณะกรรมการ </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container mt-5">
                        <div className="row">
                            <div className="col-md-5 col-lg-4 wrap_wb_fixed d-none d-sm-block d-md-block d-lg-block d-xl-block">
                                <div className="sidebar-menu wb_fixed" style={{overflow: 'hidden'}}>
                                    <ul>
                                        {
                                            this.props.departmentList.map((department, index) => (
                                                <li>
                                                    <a className="link"
                                                       href={'#department' + department.departmentId}
                                                       onClick={() => {
                                                           this.scrollToDepartment(department.departmentId)
                                                       }}>
                                                        <img src="/static/images/chat-icon.svg"/> {department.departmentName}
                                                    </a>
                                                </li>
                                            ))
                                        }
                                    </ul>
                                </div>
                            </div>
                            <div className="col-12 mb-3 d-block d-sm-none d-md-none d-lg-none d-xl-none">
                                <select id="selectbasic" name="selectbasic" className="form-control">
                                    {
                                        this.props.departmentList.map((department, index) => (
                                            <option value={'#department' + department.departmentId}>{department.departmentName}</option>
                                        ))
                                    }
                                </select>
                            </div>
                            <div className="col-md-7 col-lg-8">
                                <div className="vision-text">
                                    {
                                        this.props.departmentList.map((department, index) => (
                                            <Element name={'department' + department.departmentId}>
                                                <section id={'#department' + department.departmentId}
                                                         className={'#department' + department.departmentId}>
                                                    <h5>{department.departmentName}</h5>
                                                    <div className="row">
                                                        {
                                                            department.userList.map((user, index) => (
                                                                <div className="col-6 col-sm-4 executive-detail">
                                                                    <div className="pic-executive">
                                                                        <img src={`${HOST_BACKEND}/uploads/user_assets/${user.image_file_name}`}
                                                                             className="img-fluid"
                                                                             style={{borderRadius: '50%', height: '200px'}}/>
                                                                    </div>
                                                                    <h6>{`${user.title}${user.first_name} ${user.last_name}`}</h6>
                                                                    <p>{user.position}</p>

                                                                    {user.show_details === 1 &&
                                                                    <div className="moredetail">
                                                                        <a href={'#user' + user.user_id} className="btn btn-outline-primary inline">รายละเอียด</a>
                                                                        <div style={{display: 'none'}}>
                                                                            <div id={'user' + user.user_id} className="detail-inside-box">
                                                                                <h4>ผลงานที่ผ่านมา </h4>
                                                                                <div dangerouslySetInnerHTML={{__html: user.details}}/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    }
                                                                </div>
                                                            ))
                                                        }
                                                    </div>
                                                </section>
                                            </Element>
                                        ))
                                    }

                                    {/*<section id="content1" className="content1">
                                        <h5>ผู้บริหาร</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_07.jpg" className="img-fluid"/></div>
                                                <h6>ผศ.ดร.ศุภชัย ศรีสุชาติ </h6>
                                                <p>ผู้อำนวยการ</p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_09.jpg" className="img-fluid"/></div>
                                                <h6>ดร.สันทณี เครือขอน</h6>
                                                <p>รองผู้อำนวยการฝ่ายบริการสังคม</p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_11.jpg" className="img-fluid"/></div>
                                                <h6>ผศ.ดร.แก้วขวัญ ตั้งติพงศ์กูล</h6>
                                                <p>ผู้ช่วยผู้อำนวยการฝ่ายวิจัย และวิเทศสัมพันธ์</p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_16.jpg" className="img-fluid"/></div>
                                                <h6> รศ.ดร.ชนินทร์ มีโภคี</h6>
                                                <p>ที่ปรึกษาด้านบริการวิชาการ </p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_17.jpg" className="img-fluid"/></div>
                                                <h6> ผศ.ดร.อนิณ อรุณเรืองสวัสดิ์</h6>
                                                <p>ที่ปรึกษาด้านบริหาร</p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_18.jpg" className="img-fluid"/></div>
                                                <h6>นางฉันทิชา ศรีโยธิน</h6>
                                                <p>เลขานุการสถาบัน และ รักษาการหัวหน้างานวิจัยและวิชาการ </p>
                                            </div>
                                        </div>
                                    </section>
                                    <section id="content2" className="content2">
                                        <h5>หัวหน้างาน</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_22.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.สดศรี ทับสุทธิ์</h6>
                                                <p>หัวหน้างานบริหารสำนักงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_23.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.ลัดดา ลีลาศวัฒนกิจ</h6>
                                                <p>หัวหน้างานบริการสังคมและชุมชน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_24.jpg" className="img-fluid"/></div>
                                                <h6>นางวีนัส ลำทา</h6>
                                                <p>หัวหน้างานพัฒนาคนและองค์กร
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_28.jpg" className="img-fluid"/></div>
                                                <h6> นางศรีรัตน์ ขันธมาลัย</h6>
                                                <p>หัวหน้างานทั่วไป
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                        </div>
                                    </section>
                                    <section id="content3" className="content3">
                                        <h5>งานวิจัยและวิชาการ</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_30.jpg" className="img-fluid"/></div>
                                                <h6> นายชินกฤต คงเจริญพร</h6>
                                                <p>นักวิชาการศึกษาปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                                <div className="moredetail"><a href="#data7" className="btn btn-outline-primary inline">รายละเอียด</a>
                                                    <div style={{display: 'none'}}>
                                                        <div id="data7" className="detail-inside-box">
                                                            <h4>ผลงานที่ผ่านมา </h4>
                                                            <p>1. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 2. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 3. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_31.jpg" className="img-fluid"/></div>
                                                <h6> นายกิตติภูมิ วิเศษศักดิ์</h6>
                                                <p>นักวิจัยชำนาญการพิเศษ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                                <div className="moredetail"><a href="#data2" className="btn btn-outline-primary inline">รายละเอียด</a>
                                                    <div style={{display: 'none'}}>
                                                        <div id="data2" className="detail-inside-box">
                                                            <h4>ผลงานที่ผ่านมา </h4>
                                                            <p>1. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 2. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 3. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_32.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ยุรนันท์ ตามกาล</h6>
                                                <p>นักวิจัยปฏิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                                <div className="moredetail"><a href="#data3" className="btn btn-outline-primary inline">รายละเอียด</a>
                                                    <div style={{display: 'none'}}>
                                                        <div id="data3" className="detail-inside-box">
                                                            <h4>ผลงานที่ผ่านมา </h4>
                                                            <p>1. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 2. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_36.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.อุไรวรรณ รุ่งไหรัญ</h6>
                                                <p>นักวิจัยปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                                <div className="moredetail"><a href="#data4" className="btn btn-outline-primary inline">รายละเอียด</a>
                                                    <div style={{display: 'none'}}>
                                                        <div id="data4" className="detail-inside-box">
                                                            <h4>ผลงานที่ผ่านมา </h4>
                                                            <p>1. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_37.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.คริษฐา อ่อนแก้ว</h6>
                                                <p>นักวิจัยปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                                <div className="moredetail"><a href="#data5" className="btn btn-outline-primary inline">รายละเอียด</a>
                                                    <div style={{display: 'none'}}>
                                                        <div id="data5" className="detail-inside-box">
                                                            <h4>ผลงานที่ผ่านมา </h4>
                                                            <p>1. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 2. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 3. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 4. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.
                                                                <br/> 5. Lorem ipsum dolor sit amet, consecteur adipiscing elit. Cras vitae est a sem faycubys oretuyn.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <section id="content4" className="content4">
                                        <h5>งานพัฒนาคนและองค์กร</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_40.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.ศิริพร ยังธินะ</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_41.jpg" className="img-fluid"/></div>
                                                <h6> นายวัลลภ วิชชุเดชา</h6>
                                                <p>นักวิชาการศึกษาชำนาญการพิเศษ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_42.jpg" className="img-fluid"/></div>
                                                <h6>นางชญานันท์ พรหมมาศ</h6>
                                                <p>นักวิชาการศึกษาปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_46.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.จิราวรรณ์ ทศไกร</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_47.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.วิมลรัตน์ ส่องแสงวิจิตร</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_48.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.นวรัตน์ เทียนขาว</h6>
                                                <p>เจ้าหน้าที่บริหารงานทั่วไป
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_52.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ดารารัตน์ โชติรัตน์</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_53.jpg" className="img-fluid"/></div>
                                                <h6>นายพีรณัฐ ศิริวัชราวงศ์</h6>
                                                <p>นักวิชาการศึกษาปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_54.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.อารียา จันทร์หอมกุล</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_58.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.สุดาวดี หลิมเลิศรัตน์</h6>
                                                <p>นักวิชาการศึกษาปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_59.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.อัญชลี แก้วตา</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                        </div>
                                    </section>
                                    <section id="content5" className="content5">
                                        <h5>งานบริการสังคมและชุมชน</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_62.jpg" className="img-fluid"/></div>
                                                <h6>นายอิศรานุวัฒน์ ศรีคุณ</h6>
                                                <p>วิทยากร
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_63.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.พิชญาภา ขาวนิ่ม</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_64.jpg" className="img-fluid"/></div>
                                                <h6>นายอรรถกร เฉยทิม</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_68.jpg" className="img-fluid"/></div>
                                                <h6> นายภัทรพล เศียรแสง</h6>
                                                <p>เจ้าหน้าที่ประจำสำนักงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_69.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ปวีณา จันทราภิรมย์</h6>
                                                <p>เจ้าหน้าที่ประจำสำนักงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_70.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.วิปัศยา เบาสันเทียะ</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                        </div>
                                    </section>
                                    <section id="content6" className="content6">
                                        <h5>งานบริหารสำนักงาน</h5>
                                        <div className="row">
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_74.jpg" className="img-fluid"/></div>
                                                <h6>นางศิริรัตน์ ศรีสกุลวิวัฒน์</h6>
                                                <p>นักวิชาการเงินและบัญชีปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_75.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.กีรัตยา ดาบทอง</h6>
                                                <p>นักวิชาการเงินและบัญชีปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_76.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.อรุณี สุจริตในธรรม</h6>
                                                <p>ผู้ปฎิบัติงานบริหารชำนาญงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_80.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.นันท์ณภัสร ธัชภัทร์ปกรณ์</h6>
                                                <p>นักวิชาการเงินและบัญชีปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_81.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.พรนลิน ไชยเรศ</h6>
                                                <p>นักวิชาการเงินและบัญชี
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_82.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ชิดชนก ตลึงจิตร</h6>
                                                <p>นักวิชาการเงินและบัญชี
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_86.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ชลิตา ผลไม้</h6>
                                                <p>นักวิชาการเงินและบัญชี
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_87.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.ณัฐพัชร์ วรพัฒน์พิมุข</h6>
                                                <p>นักวิชาการเงินและบัญชี
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_88.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.สมสุข จันทศร</h6>
                                                <p>นักวิชาการพัสดุชำนาญการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_92.jpg" className="img-fluid"/></div>
                                                <h6> นางวัลลา จันทร์เปีย</h6>
                                                <p>บุคลากรชำนาญการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_93.jpg" className="img-fluid"/></div>
                                                <h6> น.ส.เพ็ญศรี แก้วหาวงษ์</h6>
                                                <p>เจ้าหน้าที่บริหารงานทั่วไปชำนาญการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_94.jpg" className="img-fluid"/></div>
                                                <h6> นายสุรัตน์ ขำดี</h6>
                                                <p>ผู้ปฎิบัติงานบริหารชำนาญงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_98.jpg" className="img-fluid"/></div>
                                                <h6>นางภัทรา ขำดี</h6>
                                                <p>ผู้ปฎิบัติงานบริหารชำนาญงาน
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_99.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.วรรณา รอดพ้นภัย</h6>
                                                <p>ผู้ปฎิบัติงานบริหาร
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_100.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.นุชนาถ ศรีสวัสดิ์</h6>
                                                <p>ผู้ปฎิบัติงานบริหาร
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_104.jpg" className="img-fluid"/></div>
                                                <h6>นายศุภชัย นาสมวาส</h6>
                                                <p>นักวิชาการศึกษา
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_105.jpg" className="img-fluid"/></div>
                                                <h6>น.ส.สกุลลักษณ์ จันทร์สุวรรณ</h6>
                                                <p>นักวิชาคอมพิวเตอร์
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_106.jpg" className="img-fluid"/></div>
                                                <h6>นายสงกรานต์ อมาตยกุล</h6>
                                                <p>นักวิชาการโสตทัศนศึกษาปฎิบัติการ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_110.jpg" className="img-fluid"/></div>
                                                <h6>นายศุภชัย ตั๋นก้อน</h6>
                                                <p>นักวิชาการเผยแพร่
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_111.jpg" className="img-fluid"/></div>
                                                <h6>นายนิพนธ์ วงศ์จันทร์</h6>
                                                <p>พนักงานขับรถ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_112.jpg" className="img-fluid"/></div>
                                                <h6>นายวิชัย น่าชม</h6>
                                                <p>พนักงานขับรถ
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                            <div className="col-6 col-sm-4 executive-detail">
                                                <div className="pic-executive"><img src="/static/images/images/about-4_116.png" className="img-fluid"/></div>
                                                <h6>นางอารีย์ ภักดี</h6>
                                                <p>นักการภารโรง
                                                    <br/> <span className="tel-inside">เบอร์ภายใน 121</span></p>
                                            </div>
                                        </div>
                                    </section>*/}
                                </div>
                            </div>
                        </div>
                    </div>
                </React.Fragment>

                <style jsx>{`
                `}</style>
            </MainLayout>
        );
    }
}