import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
//import $ from 'jquery';
import fetch from 'isomorphic-unfetch';
import {HOST_BACKEND} from '../etc/constants';
import {getDateFormatFromDateObject, getDateDisplayFromDateObject} from '../etc/utils';
import './index.css';

class NewsItem extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    render() {
        const {data} = this.props;

        return (
            <React.Fragment>
                <div className="col-6 col-sm-3">
                    <div className="pic-inhouse">
                        <figure>
                            <img src={`${HOST_BACKEND}/uploads/news_assets/${data.image_file_name}`} className="img-fluid"/>
                        </figure>
                    </div>
                    <div className="inhouse-detail">
                        <h3>{data.title}</h3>
                        <p className="dotmaster">
                            {data.short_description}
                        </p>
                        <div className="row">
                            <div className="col-12 col-sm-6 col-md-12 col-lg-6  date">
                                <img src="/static/images/calendar.svg"/> {getDateDisplayFromDateObject(new Date(data.news_date))}
                            </div>
                            <div className="col-12 col-sm-6 col-md-12 col-lg-6 ">
                                <Link href={`/news?id=${data.id}`}
                                      as={`/news/${data.id}`}>
                                    <a href="javascript:void(0)" className="readmore-red">
                                        อ่านต่อ<img src="/static/images/arrow-more.svg"/>
                                    </a>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .viewmoreindex {
                        padding: 5px 0px !important;
                        color: #949494 !important;
                        position: absolute;
                        right: 0;
                        top: 35px;
                    }
                    
                    .viewmoreindex i {
                        font-size: 0.5em;
                    }
                    
                    .owl-newsbottom.owl-carousel .owl-item .list-event li a img {
                        width: 100px;
                    }
                    
                    .owl-newsbottom.owl-carousel .owl-item .readmore-red > img,
                    .owl-newsbottom.owl-carousel .owl-item .date img {
                        width: 20px;
                        display: inline-block;
                    }
                    
                    .flexslider {
                        margin: 0;
                        padding: 0;
                        border: none;
                        box-shadow: none;
                        background: none;
                        top: 0;
                    }
                    
                    .flex-direction-nav {
                        display: none;
                    }
                    
                    .flex-control-nav {
                        position: relative;
                        margin-top: 30px;
                        display: none;
                    }
                    
                    .flex-control-paging li a.flex-active {
                        background-color: #b40303;
                    }
                    
                    .flex-control-paging li a {
                        background-color: #b4b4b4;
                        width: 15px;
                        height: 15px;
                        box-shadow: none;
                    }
                    
                    .flex-control-paging li a:hover {
                        background: white;
                        transition: ease .5s;
                    }
                    
                    .flexslider {
                        background-color: transparent;
                    }
                    
                    .inhouse-detail h3 {
                        color: black;
                        font-size: 1.2rem;
                    }
                    
                    .pic-inhouse figure {
                        height: 170px;
                    }
                    
                    .tab {
                        padding-top: 50px;
                        margin-bottom: 20px;
                        position: relative;
                        overflow: hidden;
                        background: #fff;
                        width: 100%;
                        margin: 0 auto;
                        line-height: 1.5;
                        font-weight: 300;
                        color: #888;
                        -webkit-font-smoothing: antialiased;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .tabs {
                        display: table;
                        position: relative;
                        overflow: hidden;
                        margin: 0;
                        width: 100%;
                        /*\t\tborder-bottom: 2px solid #b50303;*/
                    }
                    
                    .tabs li {
                        float: left;
                        line-height: 38px;
                        overflow: hidden;
                        padding: 0;
                        position: relative;
                        font-size: 1.4rem;
                        margin-left: -40px;
                        margin-right: 40px;
                    }
                    
                    .tabs a {
                        background-color: transparent;
                        border-bottom: 1px solid #fff;
                        color: black;
                        font-weight: 500;
                        display: block;
                        letter-spacing: 0;
                        outline: none;
                        padding: 0px 20px 0px 0px;
                        text-decoration: none;
                        -webkit-transition: all 0.2s ease-in-out;
                        -moz-transition: all 0.2s ease-in-out;
                        transition: all 0.2s ease-in-out;
                    }
                    
                    .tabs > li:first-child > a:after {
                        content: '|';
                        padding-left: 25px;
                    }
                    
                    .tabs > li > a {
                        content: '';
                    }
                    
                    .tabs_item {
                        display: none;
                        padding: 30px 0;
                    }
                    
                    .tabs_item h4 {
                        font-weight: bold;
                        color: #b50303;
                        font-size: 20px;
                    }
                    
                    .tabs_item:first-child {
                        display: block;
                    }
                    
                    .current a {
                        color: #b50303;
                    }
                    
                    .banner-style {
                        position: relative;
                    }
                    
                    .owl-banner .owl-item.active > div > .banner-style::after {
                        background-image: linear-gradient(to left, rgba(000, 000, 000, 0) 0, #000 150%);
                        width: 36%;
                        height: 100%;
                        content: '';
                        position: absolute;
                        top: 0;
                    }
                    
                    .owl-banner .owl-item.active {
                        opacity: 1;
                    }
                    
                    .owl-banner .owl-item {
                        opacity: 0.5;
                    }
                    
                    hr {
                        display: none;
                    }
                    
                    .owl-banner.owl-theme .owl-nav [class*=owl-] {
                        position: static;
                    }
                    
                    .customowlnext {
                        position: absolute;
                        bottom: 38px;
                        background-color: #FFF;
                        width: 80px;
                        background-image: url(images/arrow_right1.png);
                        background-repeat: no-repeat;
                        background-position: center;
                        height: 40px;
                    }
                    
                    .customowlnext:hover {
                        background-image: url(images/arrow_right.png);
                    }
                    
                    .customowlprev {
                        position: absolute;
                        bottom: 38px;
                        background-color: #FFF;
                        width: 80px;
                        background-image: url(images/arrow_left.png);
                        background-repeat: no-repeat;
                        background-position: center;
                        height: 40px;
                    }
                    
                    .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                        margin-top: -40px;
                        position: absolute;
                        right: 440px;
                    }
                    
                    .customowlprev:hover {
                        background-image: url(images/arrow_leftt-hover.png);
                    }
                    
                    .owl-topbanner.owl-theme .owl-dots .owl-dot.active span,
                    .owl-theme .owl-dots .owl-dot:hover span {
                        background-color: #b40303;
                    }
                    
                    .owl-topbanner.owl-theme .owl-dots .owl-dot span {
                        background-color: #c4c4c4;
                    }
                    
                    .owl-topbanner.owl-theme .owl-nav [class*=owl-] {
                        background-color: transparent;
                        color: #b40303;
                        font-size: 45px;
                        outline: 0;
                        margin: 0;
                        position: absolute;
                        z-index: 9;
                    }
                    
                    .owl-topbanner.owl-theme .owl-nav [class*=owl-] span {
                        padding: 0 10px;
                        opacity: 0;
                    }
                    
                    .owl-banner .owl-stage {
                        padding-left: 0px !important;
                    }
                    
                    .text-title-top h3 {
                        float: left;
                        text-align: left;
                        font-size: 1.5rem;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .owl-topbanner .owl-item {
                        opacity: 0.62;
                        transition: all .3s ease-in-out;
                        -moz-transition: all .3s ease-in-out;
                        -webkit-transition: all .3s ease-in-out;
                        position: relative;
                    }
                    
                    .owl-topbanner .owl-item::before {
                        content: "";
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        background: -moz-linear-gradient(left, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0) 100%);
                        background: -webkit-linear-gradient(left, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0) 100%);
                        background: linear-gradient(to right, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0) 100%);
                        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#a6000000', endColorstr='#00000000', GradientType=1);
                        z-index: 2;
                        opacity: 0;
                    }
                    
                    .owl-topbanner .owl-item.last {
                        opacity: 0;
                    }
                    
                    .owl-topbanner .owl-item.active {
                        opacity: 1;
                    }
                    
                    .owl-topbanner .owl-item .text_banner {
                        opacity: 0;
                        position: absolute;
                        z-index: 4;
                        top: 0;
                        left: 0;
                        font-size: 1em;
                        color: #FFF;
                        padding: 50px;
                    }
                    
                    .owl-topbanner .owl-item.active .text_banner {
                        opacity: 1;
                    }
                    
                    .owl-topbanner .owl-item.active::before {
                        opacity: 1;
                    }
                    
                    .text_banner h1 {
                        font-size: 4em;
                    }
                    
                    .text_banner h4 {
                        font-size: 1em;
                        margin-top: -30px;
                        letter-spacing: 3px;
                        font-weight: 700;
                    }
                    
                    .text_banner p {
                        font-size: 1em;
                        letter-spacing: 0px;
                        line-height: normal;
                    }
                    
                    .border-left-1 {
                        border-left: 5px solid white;
                        height: 80px;
                        position: absolute;
                        top: 80px;
                        left: 30px;
                    }
                    
                    @media (max-width: 1440px) {
                        .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 300px;
                        }
                    }
                    
                    @media (max-width: 1366px) {
                        .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 250px;
                        }
                    }
                    
                    @media (max-width: 1280px) {
                        .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 180px;
                        }
                    }
                    
                    @media (max-width: 1199px) {
                        .pic-inhouse figure {
                            height: auto;
                        }
                    
                        .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 150px;
                        }
                    }
                    
                    @media (max-width: 991px) {
                        .owl-news.owl-carousel .owl-item img {
                            width: 40px;
                        }
                    
                        .title-service-index h2 {
                            font-size: 1.5rem;
                        }
                    
                        .pic-inhouse figure {
                            height: auto;
                        }
                    
                        .inhouse-detail h3 {
                            font-size: 1.1rem;
                        }
                    
                        .readmore-red {
                            float: left;
                        }
                    
                        .owl-topbanner .owl-item .text_banner {
                            padding: 50px 90px;
                        }
                    
                        .owl-topbanner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 80px;
                        }
                    
                        .border-left-1 {
                            left: 75px;
                        }
                    }
                    
                    @media (max-width: 767px) {
                        .readmore-text {
                            margin-top: 30px;
                            position: absolute !important;
                            left: 0;
                        }
                    
                        .tabs_item {
                            padding-top: 0px;
                        }
                    
                        .tabs {
                            height: 80px;
                        }
                    
                        .tabs li {
                            margin-right: 20px;
                        }
                    
                        .tabs > li:first-child > a:after {
                            padding-left: 15px;
                            padding-right: 15px;
                        }
                    
                        .owl-banner .owl-stage {
                            padding-left: 15px;
                        }
                    
                        .pic-inhouse figure {
                            height: auto;
                        }
                    
                        .inhouse-detail h3 {
                            font-size: 1rem;
                        }
                    
                        .readmore-red {
                            float: left;
                        }
                    
                        .icon-dm-big {
                            width: 40px !important;
                        }
                    
                        .current a {
                            font-size: 1.1rem;
                        }
                    
                        .tabs a {
                            font-size: 1.1rem;
                        }
                    
                        .owl-banner.owl-theme .owl-nav.disabled + .owl-dots {
                            right: 0;
                            left: 0;
                        }
                    
                        .text_banner h1 {
                            font-size: 2em;
                        }
                    
                        .text_banner h4 {
                            font-size: 0.8em;
                            letter-spacing: 1px;
                            margin-top: -10px;
                        }
                    
                        .border-left-1,
                        .text_banner p {
                            display: none;
                        }
                    
                        .owl-topbanner .owl-item .text_banner {
                            padding: 0px 70px;
                        }
                    }
                    
                    @media (max-width: 320px) {
                        .tabs li {
                            margin-right: 9px;
                        }
                    
                        .tabs > li:first-child > a:after {
                            padding-left: 5px;
                            padding-right: 15px;
                        }
                    }     
                `}</style>
            </React.Fragment>
        );
    }
}

class ActivityItem extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {
        const {data} = this.props;

        function slideshow() {
            if ($('.slideshow,.flexslider').size()) {
                $('.slideshow .flexslider').flexslider({
                    animation: 'slide'
                    , slideshowSpeed: 5000
                    , animationDuration: 1000
                });
                $('.slideshow .flexslider').flexslider(1);
            }
        }

        $('.eventPic').click(function () {
            var rel = parseInt($(this).attr('rel'));
            console.log(rel);
            $(this).parent().parent().parent().parent().parent().parent().parent().find('.slideshow .flexslider').flexslider(rel);
            //$('.slideshow .flexslider').flexslider($(this).index(".eventPic"));
        });
        slideshow();
    }

    render() {
        const {data} = this.props;

        return (
            <React.Fragment>
                <div className="item">
                    <div className="row">
                        <div className="col-12 col-sm-6">
                            <section className={`slideshow wow fadeInLeft`}>
                                <div className={`flexslider`}>
                                    <ul className="slides">
                                        {
                                            data.image_list.map(image => (
                                                <li>
                                                    <img src={`${HOST_BACKEND}/uploads/news_assets/${image}`}
                                                         className="img-fluid"/>
                                                </li>
                                            ))
                                        }
                                    </ul>
                                </div>
                            </section>
                        </div>
                        <div className="col-12 col-sm-6 wow fadeInRight">
                            <div className="inhouse-detail">
                                <h3>{data.title}</h3>
                                <p className="dotmaster">{data.short_description}</p>
                                <div className="row">
                                    <div className="col date"><img src="/static/images/calendar.svg"/> {getDateDisplayFromDateObject(new Date(data.news_date))}</div>
                                    <div className="col">
                                        <Link href={`/news?id=${data.id}`}
                                              as={`/news/${data.id}`}>
                                            <a href="javascript:void(0)" className="readmore-red">
                                                อ่านต่อ<img src="/static/images/arrow-more.svg"/>
                                            </a>
                                        </Link>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <div className="list-event">
                                            {
                                                data.image_list.map((image, index) => (
                                                    <li>
                                                        <a href="javascript:void(0)" className="eventPic" rel={index}>
                                                            <img src={`${HOST_BACKEND}/uploads/news_assets/${image}`}
                                                                 className="img-fluid"/>
                                                        </a>
                                                    </li>
                                                ))
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                         
                `}</style>
            </React.Fragment>
        );
    }
}

export default class Index extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            activeNewsTab: 0,
        };
    }

    static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';

        const res = await fetch(baseUrl + '/api/get_news_latest', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}),
        });

        const result = await res.json();
        if (result.error.code === 0) {
            const {trainingNewsList, publicRelationsNewsList} = result;

            const activityRes = await fetch(baseUrl + '/api/get_activity', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    offset: 0,
                    limit: 4,
                }),
            });

            const activityResult = await activityRes.json();
            if (activityResult.error.code === 0) {
                const {activityList} = activityResult;
                return {
                    trainingNewsList,
                    publicRelationsNewsList,
                    activityList,
                };
            } else {
                return {
                    trainingNewsList: null,
                    publicRelationsNewsList: null,
                    activityList: null,
                };
            }
        } else {
            return {
                trainingNewsList: null,
                publicRelationsNewsList: null,
                activityList: null,
            };
        }
    };

    handleClickNewsHeader = (tabIndex, e) => {
        this.setState({
            activeNewsTab: tabIndex,
        })
    };

    componentDidMount() {
        $('.owl-news').on('initialized.owl.carousel', function (event) {
            $(".dotmaster").trigger("update.dot");
        });
        $(".owl-news").owlCarousel({
            loop: false
            , margin: 20
            , nav: true
            , dots: false
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , responsive: {
                0: {
                    items: 1
                    , nav: false
                    , dots: true
                }
                , 500: {
                    items: 2
                    , nav: false
                    , dots: true
                }
                , 768: {
                    margin: 30
                    , items: 3
                    , nav: false
                    , dots: true
                }
                , 992: {
                    margin: 80
                    , items: 3
                }
                , 1025: {
                    margin: 80
                    , items: 3
                }
            }
        });
        $(".owl-newsbottom").owlCarousel({
            loop: false
            , margin: 20
            , nav: false
            , dots: true
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , responsive: {
                0: {
                    items: 1
                    , nav: false
                    , dots: true
                }
                , 500: {
                    items: 1
                    , nav: false
                    , dots: true
                }
                , 768: {
                    margin: 30
                    , items: 1
                    , nav: false
                    , dots: true
                }
                , 992: {
                    margin: 80
                    , items: 1
                }
                , 1025: {
                    margin: 80
                    , items: 1
                }
            }
        });
        $(".owl-newstop").owlCarousel({
            loop: false
            , margin: 20
            , nav: true
            , dots: false
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , responsive: {
                0: {
                    items: 1
                    , margin: 15, //slideBy: 3
                }
                , 500: {
                    items: 2
                }
                , 768: {
                    margin: 20
                    , items: 4
                }
                , 992: {
                    margin: 10
                    , items: 4
                }
                , 1025: {
                    margin: 10
                    , items: 4
                }
            }
        });
        $('.owl-newsmobile').on('initialized.owl.carousel', function (event) {
            $(".dotmaster").trigger("update.dot");
        });
        $(".owl-newsmobile").owlCarousel({
            loop: false
            , margin: 20
            , nav: true
            , dots: false
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , items: 1
        });
        $(".owl-link").owlCarousel({
            loop: false
            , margin: 20
            , nav: false
            , dots: false
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , responsive: {
                0: {
                    items: 1
                    , margin: 15
                    , slideBy: 1
                }
                , 500: {
                    items: 2
                    , slideBy: 2
                }
                , 768: {
                    margin: 30
                    , items: 3
                    , slideBy: 3
                }
                , 992: {
                    margin: 30
                    , items: 4
                    , slideBy: 4
                }
                , 1025: {
                    margin: 35
                    , items: 4
                    , slideBy: 4
                }
            }
        });
        $(".owl-media").owlCarousel({
            loop: false
            , margin: 20
            , nav: false
            , dots: false
            , autoplay: false
            , autoplayTimeout: 6000
            , slideBy: 1
            , responsive: {
                0: {
                    items: 2
                    , margin: 15
                    , slideBy: 2
                }
                , 500: {
                    items: 2
                    , slideBy: 2
                }
                , 768: {
                    margin: 30
                    , items: 3
                    , slideBy: 3
                }
                , 992: {
                    margin: 30
                    , items: 4
                    , slideBy: 4
                }
                , 1025: {
                    margin: 35
                    , items: 4
                    , slideBy: 4
                }
            }
        });
        var owlstagepad = $('.container').offset().left / 1;
        $('.banner-txt-slide').css('left', owlstagepad * 2 - 1);
        $('.social-fixed').css('left', owlstagepad * 2 - 1);
        $('.customowlnext').css('right', owlstagepad);
        //$('.owl-banner').on('initialized.owl.carousel', function (event) {
        //    $('.customowlnext').css('right', owlstagepad);
        //});
        var owlright = $('.container').offset().left;
        var owlleft = $('.container').offset().left + 60;
        if (Modernizr.mq('(max-width: 991px)')) {
            var bannersp = $('.container').offset().left;
            $('.bg_search').css('right', bannersp + 60);
        } else {
            var bannersp = $('.container').offset().left + 50;
            $('.bg_search').css('right', bannersp + 90);
        }
        $(".owl-topbanner").on('changed.owl.carousel initialized.owl.carousel', function (event) {
            $(event.target).find('.owl-item').removeClass('last').eq(event.item.index + event.page.size - 2).addClass('last');
            $('.owl-topbanner.owl-carousel .owl-stage').css('left', '-50px');
        }).owlCarousel({
            loop: true, //margin:20,
            //navText: ["<img src='images/chevron_leftgrey.png'>","<img src='images/chevron_rightgrey.png'>"],
            nav: false
            , dots: true
            , rewind: true
            , autoplay: false
            , autoplayTimeout: 5000
            , smartSpeed: 800
            , stagePadding: bannersp
            , slideBy: 1
            , responsive: {
                0: {
                    items: 1
                    , margin: 10
                    //slideBy: 3
                }
                , 500: {
                    items: 1
                }
                , 768: {
                    margin: 15
                    , items: 1
                }
                , 992: {
                    margin: 15
                    , items: 1
                }
                , 1025: {
                    margin: 15
                    , items: 1
                }
            }
        });

        //////////////////////////////////////////////////////////////////////////////////////////////
        $(window).on("load resize", function () {
            var offsetctn = $('.container').offset();
            var home_news_item = $('.home_news_item').width();
            var home_head = $('.home_head').offset();
            var container_width = $('.container').width();
            console.log(offsetctn);
            $(' .bg_news > span').css('width', offsetctn.left + (container_width / 2) - 30);
            var blogh = $('.padding_blog .home_head').outerHeight() + $('.blog_item img').outerHeight() + 80;
            $('.bg_blog > span').css('height', blogh);
        });

        //////////////////////////////////////////////////////////////////////////////////////////////
        /*function slideshow() {
            if ($('.slideshow,.flexslider').size()) {
                $('.slideshow .flexslider').flexslider({
                    animation: 'slide'
                    , slideshowSpeed: 5000
                    , animationDuration: 1000
                });
                $('.slideshow .flexslider').flexslider(1);
            }
        }

        $('.eventPic').click(function () {
            var rel = parseInt($(this).attr('rel'));
            console.log(rel);
            $(this).parent().parent().parent().parent().parent().parent().parent().find('.slideshow .flexslider').flexslider(rel);
            //$('.slideshow .flexslider').flexslider($(this).index(".eventPic"));
        });
        slideshow();*/
    }

    render() {
        const {trainingNewsList, publicRelationsNewsList, activityList} = this.props;

        const settings = {
            dots: true
        };

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container-fluid">
                    <div className="row wow fadeInUp">
                        <div className="col-12">
                            <div className="owl-topbanner owl-carousel owl-theme">
                                <div><img src="/static/images/banner.jpg"/>
                                    <div className="text_banner">
                                        <div className="border-left-1"></div>
                                        <h1>INSTITUTE</h1>
                                        <h4>CONTINUING EDUCDTION AND HUMAN RESIURCES</h4>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                            <br/> industry There are many variations of passages of Lorem Ipsum
                                            <br/> available, but the majority have suffered alteration in some .</p>
                                    </div>
                                </div>
                                <div><img src="/static/images/banner.jpg"/>
                                    <div className="text_banner">
                                        <div className="border-left-1"></div>
                                        <h1>SERVICES</h1>
                                        <h4>CONTINUING EDUCDTION AND HUMAN RESIURCES</h4>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                            <br/> industry There are many variations of passages of Lorem Ipsum
                                            <br/> available, but the majority have suffered alteration in some .</p>
                                    </div>
                                </div>
                                <div><img src="/static/images/banner.jpg"/>
                                    <div className="text_banner">
                                        <div className="border-left-1"></div>
                                        <h1>TRAINING</h1>
                                        <h4>CONTINUING EDUCDTION AND HUMAN RESIURCES</h4>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting
                                            <br/> industry There are many variations of passages of Lorem Ipsum
                                            <br/> available, but the majority have suffered alteration in some .</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container">
                        <div className="row">
                            <div className="col-sm-3 wow fadeInLeft">
                                <div className="bg-yellow"><img src="/static/images/service-icon.svg" className="icon-dm-big"/>
                                    <div className="title-service-index">
                                        <h5>บริการของหน่วยงาน</h5>
                                        <h2>OUR SERVICES</h2>
                                    </div>
                                </div>
                            </div>
                            <div className="col-sm-9 wow fadeInRight">
                                <div className="service-crop d-none d-sm-block d-md-none d-lg-block d-xl-block">
                                    <div className="row border-bottom mt-3">
                                        {/*บริการฝึกอบรม*/}
                                        <div className="col-sm-4">
                                            <Link href="/service-training">
                                                <div className="service-index"><img src="/static/images/icon1.svg" className="icon-dm-big"/>
                                                    <h4>บริการฝึกอบรม</h4>
                                                    <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                        Ipsum has been of the printing and typesetting industry. </p>
                                                </div>
                                            </Link>
                                        </div>
                                        {/*In-house Training*/}
                                        <div className="col-sm-4">
                                            <Link href="/in-house">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon2.svg" className="icon-dm-big"/>
                                                        <h4>IN-HOUSE Training</h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                        {/*บริการสังคม*/}
                                        <div className="col-sm-4 service-index">
                                            <Link href="/service-social">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon3.svg" className="icon-dm-big"/>
                                                        <h4>บริการสังคม </h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                    <div className="row mt-3">
                                        {/*อบรมสอบใบขับขี่*/}
                                        <div className="col-sm-4 ">
                                            <Link href="/service-driving-license">
                                                <div className="service-index"><img src="/static/images/icon4.svg" className="icon-dm-big"/>
                                                    <h4>บริการอบรมภาคทฤษฎีเพื่อ<br/>ขอใบอนุญาตขับขี่</h4>
                                                    <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                        Ipsum has been of the printing and typesetting industry. </p>
                                                </div>
                                            </Link>
                                        </div>
                                        {/*งานวิจัยและวิชาการ*/}
                                        <div className="col-sm-4">
                                            <Link href="/academic-paper">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon5.svg" className="icon-dm-big"/>
                                                        <h4>งานวิจัยและวิชาการ</h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                        {/*วารสาร HR Intelligence*/}
                                        <div className="col-sm-4 service-index">
                                            <div className="border-right-service"><img src="/static/images/inhouse-icon.svg" className="icon-dm-big"/>
                                                <h4>วารสาร HR Intelligence </h4>
                                                <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem Ipsum
                                                    has been of the printing and typesetting industry. </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="service-crop d-block d-sm-none d-md-block d-lg-none d-xl-none">
                                    <div className="owl-news owl-carousel owl-theme mobilespec">
                                        <div className="item">
                                            <a href="/service-training">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon1.svg" className="icon-dm-big"/>
                                                        <h4>บริการฝึกอบรม </h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div className="item">
                                            <a href="/in-house">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon2.svg" className="icon-dm-big"/>
                                                        <h4>IN-HOUSE Training</h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div className="item">
                                            <a href="/service-social">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon3.svg" className="icon-dm-big"/>
                                                        <h4>บริการสังคม </h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div className="item">
                                            <a href="/service-driving-license">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon4.svg" className="icon-dm-big"/>
                                                        <h4>บริการอบรมภาคทฤษฎีเพื่อ <br/>
                                                            ขอใบอนุญาตขับขี่</h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div className="item">
                                            <a href="/academic-paper">
                                                <div className="service-index">
                                                    <div className="border-right-service"><img src="/static/images/icon5.svg" className="icon-dm-big"/>
                                                        <h4>งานวิจัยและวิชาการ</h4>
                                                        <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                            Ipsum has been of the printing and typesetting industry. </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div className="item">
                                            <div className="service-index">
                                                <div className="border-right-service"><img src="/static/images/inhouse-icon.svg" className="icon-dm-big"/>
                                                    <h4>วารสาร HR Intelligence </h4>
                                                    <p>Lorem Ipsum is simply dummy text of the printing typesetting industry. Lorem
                                                        Ipsum has been of the printing and typesetting industry. </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/*ข่าว*/}
                        <div className="row wow fadeInUp">
                            <div className="col">
                                <div className="tab">
                                    <ul className="tabs">
                                        <li className={this.state.activeNewsTab === 0 ? 'after-text current' : 'after-text'}>
                                            <a href="javascript:void(0)"
                                               onClick={this.handleClickNewsHeader.bind(this, 0)}>
                                                ข่าวการฝึกอบรม
                                            </a>
                                        </li>
                                        <li className={this.state.activeNewsTab === 1 ? 'current' : ''}>
                                            <a href="javascript:void(0)"
                                               onClick={this.handleClickNewsHeader.bind(this, 1)}>
                                                ข่าวประชาสัมพันธ์
                                            </a>
                                        </li>
                                        <a href="javascript:void(0)" className="readmore-text">
                                            อ่านทั้งหมด <i className="fas fa-plus"></i>
                                        </a>
                                    </ul>
                                    <div className="tab_content">
                                        {this.state.activeNewsTab === 0 &&
                                        <div className="tabs_item">
                                            <div className="row">
                                                {trainingNewsList &&
                                                trainingNewsList.map(newsItem => (
                                                    <NewsItem data={newsItem}/>
                                                ))
                                                }
                                                {trainingNewsList && trainingNewsList.length === 0 &&
                                                <div className="col-12" style={{textAlign: 'center'}}>
                                                    ไม่มีข่าวการฝึกอบรม
                                                </div>
                                                }
                                            </div>
                                        </div>
                                        }
                                        {this.state.activeNewsTab === 1 &&
                                        <div className="tabs_item">
                                            <div className="row">
                                                {publicRelationsNewsList &&
                                                publicRelationsNewsList.map(newsItem => (
                                                    <NewsItem data={newsItem}/>
                                                ))
                                                }
                                                {publicRelationsNewsList && publicRelationsNewsList.length === 0 &&
                                                <div className="col-12" style={{textAlign: 'center'}}>
                                                    ไม่มีข่าวประชาสัมพันธ์
                                                </div>
                                                }
                                            </div>
                                        </div>
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/*หัวข้อภาพกิจกรรม*/}
                        <div className="row wow fadeInUp">
                            <div className="col text-title-top" style={{marginBottom: 0, paddingBottom: 0}}>
                                <h3 style={{marginBottom: 0, paddingBottom: 0}}>ภาพกิจกรรม</h3>
                            </div>
                            <div className="col">
                                <a href="javascript:void(0)" className="viewmoreindex"> ดูทั้งหมด <i className="fas fa-plus"/></a>
                            </div>
                        </div>
                    </div>
                </div>

                {/*ภาพกิจกรรม*/}
                <div className="activity">
                    <div className="container" style={{position: 'relative'}}><span className="bgyellow1"/>
                        <div className="owl-newsbottom owl-carousel owl-theme">
                            {activityList &&
                            activityList.map(item => (
                                <ActivityItem data={item}/>
                            ))
                            }
                            {activityList && activityList.length === 0 &&
                            <div className="col-12" style={{textAlign: 'center'}}>
                                ไม่มีภาพกิจกรรม
                            </div>
                            }
                        </div>
                    </div>
                </div>

                <style jsx>{`
                         
                `}</style>
            </MainLayout>
        );
    }
}