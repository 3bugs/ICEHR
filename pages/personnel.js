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
                                                                    <div className="pic-executive" style={{marginTop: '0px'}}>
                                                                        <img src={`${HOST_BACKEND}/uploads/user_assets/${user.image_file_name}`}
                                                                             className="img-fluid"
                                                                             style={{borderRadius: '50%', height: '200px'}}/>
                                                                    </div>
                                                                    <div style={{marginBottom: '25px'}}>
                                                                        <h6>{`${user.title}${user.first_name} ${user.last_name}`}</h6>
                                                                        <p style={{marginBottom: 10}}>{user.position}</p>
                                                                        {user.phone_office.length > 3 &&
                                                                        <p><i className="fa fa-phone-square fa-rotate-90"/>&nbsp;&nbsp;&nbsp;{user.phone_office} (ต่อ {user.phone_extension})</p>
                                                                        }

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
                                                                </div>
                                                            ))
                                                        }
                                                    </div>
                                                </section>
                                            </Element>
                                        ))
                                    }
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