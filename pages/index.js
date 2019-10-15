import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
//import $ from 'jquery';
import fetch from 'isomorphic-unfetch';
import {HOST_BACKEND, SERVICE_TRAINING, SERVICE_IN_HOUSE, SERVICE_SOCIAL, SERVICE_DRIVING_LICENSE, SERVICE_ACADEMIC_PAPER, SERVICE_HR_INTELLIGENCE} from '../etc/constants';
import {getDateFormatFromDateObject, getDateDisplayFromDateObject} from '../etc/utils';
import './index.css';

export class NewsItem extends React.Component {
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
        console.log('test');

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

                const serviceRes = await fetch(baseUrl + '/api/get_service', {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({}),
                });

                const serviceResult = await serviceRes.json();
                if (serviceResult.error.code === 0) {
                    const {serviceList} = serviceResult;

                    const bannerRes = await fetch(baseUrl + '/api/get_intro', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            type: ['banner', 'intro'], // banner กับ intro page
                        }),
                    });

                    const bannerResult = await bannerRes.json();
                    if (bannerResult.error.code === 0) {
                        const bannerList = bannerResult.dataList.filter(item => {
                            return item.type === 'banner';
                        });
                        const introPageList = bannerResult.dataList.filter(item => {
                            return item.type === 'intro';
                        });

                        return {
                            trainingNewsList,
                            publicRelationsNewsList,
                            activityList,
                            serviceList,
                            bannerList,
                            introPageList,
                        };
                    } else {
                        return {
                            trainingNewsList: null,
                            publicRelationsNewsList: null,
                            activityList: null,
                            serviceList: null,
                            bannerList: null,
                            introPageList: null,
                        };
                    }
                } else {
                    return {
                        trainingNewsList: null,
                        publicRelationsNewsList: null,
                        activityList: null,
                        serviceList: null,
                        bannerList: null,
                        introPageList: null,
                    };
                }
            } else {
                return {
                    trainingNewsList: null,
                    publicRelationsNewsList: null,
                    activityList: null,
                    serviceList: null,
                    bannerList: null,
                    introPageList: null,
                };
            }
        } else {
            return {
                trainingNewsList: null,
                publicRelationsNewsList: null,
                activityList: null,
                serviceList: null,
                bannerList: null,
                introPageList: null,
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

        /*const {serviceList} = this.props;
        const services = {};
        serviceList.map(item => {
            services[item.slug] = {
                title: item.title,
                details: item.details,
                url: item.url,
            };
        });
        this.setState({
            serviceList,
            services,
        });*/

        /*$('.myLink').on('click', () => {
            alert('test');
        });*/

        const {introPageList} = this.props;
        if (introPageList && introPageList.length > 0) {
            $.fancybox({
                'autoScale': true,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'speedIn': 500,
                'speedOut': 300,
                'autoDimensions': true,
                'centerOnScroll': true,
                'href': '#divIntroPage'
            });
        }
    }

    render() {
        const {trainingNewsList, publicRelationsNewsList, activityList, serviceList, bannerList, introPageList} = this.props;
        const {services, activeNewsTab} = this.state;

        const settings = {
            dots: true
        };

        const serviceRows = [];
        let row = [];
        if (serviceList) {
            for (let i = 0; i < serviceList.length; i++) {
                if (i % 3 === 0) {
                    row = [];
                    row.push(serviceList[i]);

                    if (i === serviceList.length - 1) {
                        row.push(null);
                        row.push(null);
                        serviceRows.push(row);
                    }
                } else if (i % 3 === 1) {
                    row.push(serviceList[i]);

                    if (i === serviceList.length - 1) {
                        row.push(null);
                        serviceRows.push(row);
                    }
                } else {
                    row.push(serviceList[i]);
                    serviceRows.push(row);
                }
            }
        }

        return (
            <MainLayout>
                <NextHead>
                    <script src="/static/fancybox/source/jquery.fancybox.js?v=2.1.5"/>
                    <link rel="stylesheet" href="/static/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>
                </NextHead>

                <div id="divIntroPage" style={{display: 'none'}}>
                    {introPageList && introPageList.length > 0 && (introPageList[0].url && introPageList[0].url.trim() !== '') &&
                    <a href={introPageList[0].url}
                       target="_blank">
                        <img src={HOST_BACKEND + '/uploads/intro_assets/' + introPageList[0].image_file_name}
                             style={{cursor: 'pointer', maxWidth: '100%'}}/>
                    </a>
                    }
                    {introPageList && introPageList.length > 0 && (!introPageList[0].url || introPageList[0].url.trim() === '') &&
                    <img src={HOST_BACKEND + '/uploads/intro_assets/' + introPageList[0].image_file_name}
                         style={{maxWidth: '100%'}}/>
                    }
                </div>

                <div className="container-fluid">
                    <div className="row wow fadeInUp">
                        <div className="col-12">
                            <div className="owl-topbanner owl-carousel owl-theme">
                                {bannerList && bannerList.map(banner => (
                                    <a href={banner.url}>
                                        <div style={{border: '0px solid #ccc'}}>
                                            <img src={HOST_BACKEND + '/uploads/intro_assets/' + banner.image_file_name}
                                                 style={{cursor: 'pointer'}}/>
                                            <div className="text_banner">
                                                <div className="border-left-1"></div>
                                                <h1>{banner.title}</h1>
                                                <h4>{banner.sub_title}</h4>
                                                <div dangerouslySetInnerHTML={{__html: banner.details}}/>
                                            </div>
                                        </div>
                                    </a>
                                ))
                                }
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

                                    {serviceList &&
                                    serviceRows.map(row => (
                                        <div className="row border-bottom mt-3">
                                            {
                                                row.map(service => {
                                                    return (
                                                        <div className="col-sm-4 service-index">
                                                            {service != null &&
                                                            <a href={service.url} target="_parent">
                                                                <div className="service-index"><img src={HOST_BACKEND + '/uploads/service_icons/' + service.icon_file_name} className="icon-dm-big"/>
                                                                    <h4>{service.title}</h4>
                                                                    <p>{service.details}</p>
                                                                </div>
                                                            </a>
                                                            }
                                                            {service == null &&
                                                            <div/>
                                                            }
                                                        </div>
                                                    );
                                                })
                                            }
                                        </div>
                                    ))
                                    }
                                </div>

                                <div className="service-crop d-block d-sm-none d-md-block d-lg-none d-xl-none">
                                    <div className="owl-news owl-carousel owl-theme mobilespec">
                                        {serviceList
                                        && serviceList.map(service => {
                                            return (
                                                <div className="item">
                                                    <a href={service.url}>
                                                        <div className="service-index">
                                                            <div className="border-right-service"><img src={HOST_BACKEND + '/uploads/service_icons/' + service.icon_file_name} className="icon-dm-big"/>
                                                                <h4>{service.title}</h4>
                                                                <p>{service.details}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            );
                                        })
                                        }
                                    </div>
                                </div>

                            </div>
                        </div>

                        {/*ข่าว*/}
                        <div className="row wow fadeInUp"
                             style={{border: '0px solid red'}}>
                            <div className="col">
                                <div className="tab">
                                    <ul className="tabs"
                                        style={{border: '0px solid red'}}>
                                        <li className={activeNewsTab === 0 ? 'after-text current' : 'after-text'}>
                                            <a href="javascript:void(0)"
                                               onClick={this.handleClickNewsHeader.bind(this, 0)}>
                                                ข่าวการฝึกอบรม
                                            </a>
                                        </li>
                                        <li className={activeNewsTab === 1 ? 'current' : ''}>
                                            <a href="javascript:void(0)"
                                               onClick={this.handleClickNewsHeader.bind(this, 1)}>
                                                ข่าวประชาสัมพันธ์
                                            </a>
                                        </li>
                                        <a href={activeNewsTab === 0 ? '/news/training' : '/news/public-relations'}
                                           className="readmore-text" style={{marginTop: '15px'}}>
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
                        <div className="row wow fadeInUp"
                             style={{border: '0px solid red'}}>
                            <div className="col text-title-top"
                                 style={{marginBottom: 0, paddingBottom: 0}}>
                                <h3 style={{marginBottom: 0, paddingBottom: 0}}>ภาพกิจกรรม</h3>
                            </div>
                            {/*<div className="col">
                                <a href="javascript:void(0)" className="viewmoreindex"> ดูทั้งหมด <i className="fas fa-plus"/></a>
                            </div>*/}
                            <div className="col">
                                <a href={'/news/activity'} className="viewmoreindex" style={{marginRight: '16px'}}>
                                    ดูทั้งหมด <i className="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {/*ภาพกิจกรรม*/}
                <div className="activity" style={{border: '0px solid blue'}}>
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