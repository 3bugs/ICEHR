import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import {Element, scroller} from 'react-scroll';
import './about.css';

export default class About extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async function ({req, query}) {
        let aboutList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const result = await fetch(baseUrl + '/api/get_intro', {
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
            aboutList = resultJson['dataList'];
            errorMessage = null;
        } else {
            aboutList = null;
            errorMessage = resultJson['error']['message'];
        }

        return {aboutList, errorMessage};
    };

    componentDidMount() {
        var WFw = $(".wrap_wb_fixed").width();
        var Wdh = $(window).height();
        $(document).on('scroll', function () {
            if ($('.wb_fixed')[0].offsetTop < $(document).scrollTop()) {
                $(".wb_fixed").css({position: "fixed", top: 90, width: WFw, 'max-height': Wdh - 100});
            }
            if ($(document).scrollTop() < $(".wrap_wb_fixed")[0].offsetTop) {
                $(".wb_fixed").css({position: "static", top: 0, 'max-height': 999});
            }
        });

        $('a[href^="#"]').on('click', function (e) {
            e.preventDefault();

            var target = this.hash;
            var $target = $(target);

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top - 90
            }, 900, 'swing', function () {
                //window.location.hash = target;
            });
            window.location.hash = target;
        });

        const {aboutList} = this.props;

        $(window).scroll(function () {
            var scroll = $(window).scrollTop();

            const sectionList = [];
            for (let i = 0; i < aboutList.length; i++) {
                sectionList.push(
                    $(`.about${aboutList[i].id}`).offset().top - 90
                );
            }

            for (let i = sectionList - 1; i >= 0; i--) {
                if (scroll >= sectionList[i]) {
                    $('.link').removeClass('active');
                    $(`.link[href="#${aboutList[i].id}"]`).addClass('active');
                    break;
                }
            }

            /*var sec2 = $('.history').offset().top - 90;
            var sec3 = $('.vision').offset().top - 90;
            var sec4 = $('.mission').offset().top - 90;
            var sec5 = $('.value').offset().top - 90;
            var sec6 = $('.policy').offset().top - 90;

            if (scroll >= sec6) {
                $('.link').removeClass('active');
                $('.link[href="#policy"]').addClass('active');
            } else if (scroll >= sec5) {
                $('.link').removeClass('active');
                $('.link[href="#value"]').addClass('active');
            } else if (scroll >= sec4) {
                $('.link').removeClass('active');
                $('.link[href="#mission"]').addClass('active');
            } else if (scroll >= sec3) {
                $('.link').removeClass('active');
                $('.link[href="#vision"]').addClass('active');
            } else if (scroll >= sec2) {
                $('.link').removeClass('active');
                $('.link[href="#history"]').addClass('active');
            } else {
                $('.link').removeClass('active');
            }*/
        });

        /*$("select").click(function () {
            var open = $(this).data("isopen");
            if (open) {
                window.location.href = $(this).val()
            }
            //set isopen to opposite so next time when use clicked select box
            //it wont trigger this event
            $(this).data("isopen", !open);
        });*/
    }

    handleSelect = event => {
        //window.location.href = event.target.value;
        scroller.scrollTo(event.target.value, {
            duration: 500,
            smooth: true,
            offset: -90,
        });
    };

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-vision-top"/>
                    <div className="bgnav-abt">
                        <div className="container d-none d-sm-block d-md-block d-lg-block d-xl-block">
                            <div className="row">
                                <div className="col">
                                    <ul className="aboutmenu">
                                        <li><a href="/about" className="active-menu">เกี่ยวกับองค์กร </a></li>
                                        <li><a href="/mission">ภารกิจ </a></li>
                                        <li><a href="/organization">โครงสร้างองค์กร </a></li>
                                        <li><a href="/personnel">ผู้บริหารและบุคลากร </a></li>
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
                                            this.props.aboutList.map((item, index) => (
                                                <li style={{cursor: 'pointer'}}>
                                                    <a className="link" onClick={() => {
                                                        scroller.scrollTo(item.id, {
                                                            duration: 500,
                                                            smooth: true,
                                                            offset: -90,
                                                        });
                                                    }}>
                                                        <img src="/static/images/chat-icon.svg"/> {item.title}
                                                    </a>
                                                </li>
                                            ))
                                        }
                                        {/*<li><a className="link" href="#history"><img src="/static/images/chat-icon.svg"/> ประวัติและความเป็นมา (History)</a></li>
                                        <li><a className="link" href="#vision"><img src="/static/images/chat-icon.svg"/> วิสัยทัศน์ (Vision)</a></li>
                                        <li><a className="link" href="#mission"><img src="/static/images/chat-icon.svg"/> พันธกิจ (Mission)</a></li>
                                        <li><a className="link" href="#value"><img src="/static/images/chat-icon.svg"/> ค่านิยมหลัก (Core Value)</a></li>
                                        <li><a className="link" href="#policy"><img src="/static/images/chat-icon.svg"/> นโยบาย แนวทาง และ การดำเนินงานของสถาบัน</a></li>*/}
                                    </ul>
                                </div>
                            </div>
                            <div className="col-12 mb-3 d-block d-sm-none d-md-none d-lg-none d-xl-none">
                                <select id="selectbasic" name="selectbasic" className="form-control"
                                        onChange={this.handleSelect}>
                                    {
                                        this.props.aboutList.map((item, index) => (
                                            <option value={`${item.id}`}>{item.title}</option>
                                        ))
                                    }
                                    {/*<option value="#history">ประวัติและความเป็นมา (History)</option>
                                    <option value="#vision">วิสัยทัศน์ (Vision)</option>
                                    <option value="#mission">พันธกิจ (Mission)</option>
                                    <option value="#value">ค่านิยมหลัก (Core Value)</option>
                                    <option value="#policy">นโยบาย แนวทาง และ การดำเนินงานของสถาบัน</option>*/}
                                </select>
                            </div>
                            <div className="col-md-7 col-lg-8">
                                <div className="vision-text">
                                    {
                                        this.props.aboutList.map((item, index) => (
                                            <Element name={item.id}>
                                                <h5>{item.title}</h5>
                                                <section id={`about${item.id}`} className={`about${item.id}`}
                                                         dangerouslySetInnerHTML={{__html: item.details}}>
                                                </section>
                                            </Element>
                                        ))
                                    }

                                    {/*<section id="history" className="history">

                                    </section>
                                    <section id="vision" className="vision">

                                    </section>
                                    <section id="mission" className="mission">

                                    </section>
                                    <section id="value" className="value">

                                    </section>
                                    <section id="policy" className="policy">

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