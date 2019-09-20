import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import Link from 'next/link';
import CourseDetails from '../components/service-training/CourseDetails';
import {SERVICE_SOCIAL, SERVICE_TRAINING, longMonthNames, HOST_BACKEND, LIMIT_PER_PAGE} from "../etc/constants";
import {formatCourseDateLong, formatCourseDateShort, getDateDisplayFromDateObject, numberWithCommas} from "../etc/utils";
import Modal from "react-bootstrap/Modal";
import {Element, scroller} from "react-scroll/modules";
import {Scrollbars} from "react-custom-scrollbars";
import {Carousel} from "react-responsive-carousel";
import {FacebookShareButton, TwitterShareButton, LineShareButton, EmailShareButton} from 'react-share';
import {NewsItem} from "./index";
import ReactPaginate from "react-paginate";
import '../pages/pagination.css';

//import window from 'global';
//const window = require("global/window")

const NEWS_LIMIT_PER_PAGE = 8;

export default class News extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            newsList: null,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
    }

    static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const {id} = query;

        if (id === undefined) {
            return {news: null}
        } else {
            switch (id) {
                case 'training':
                case 'public-relations':
                    return {news: null, newsListType: id};
            }

            const res = await fetch(baseUrl + '/api/get_news_by_id', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id
                }),
            });
            const result = await res.json();
            let news = null;
            if (result.error.code === 0) {
                news = result.news;
            }
            return {news, newsListType: null};
        }
    };

    doGetNews() {
        fetch('/api/get_news', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                newsType: this.props.newsListType,
                limit: NEWS_LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        newsList: result.dataList,
                        pageCount: Math.ceil(result.totalCount / NEWS_LIMIT_PER_PAGE),
                        errorMessage: null,
                    }, () => {
                        if (!this.state.firstLoad) {
                            scroller.scrollTo('topOfTable', {
                                duration: 500,
                                smooth: true,
                                offset: -100,
                            });
                        } else {
                            this.setState({
                                firstLoad: false,
                            });
                        }
                    });
                } else {
                    // todo: handle error
                }
            })
            .catch(error => {
                //alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
                // todo: handle error
            });
    }

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * NEWS_LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetNews();
        });
    };

    render() {
        const {news, newsListType} = this.props;
        const {newsList} = this.state;
        const pageTitles = {
            'training': 'ข่าวการฝึกอบรม',
            'public-relations': 'ข่าวประชาสัมพันธ์',
            'in-house': 'หลักสูตร In-House ที่ผ่านมา',
            'activity': 'ภาพกิจกรรม',
        };

        let pageTitle = null;
        if (news) {
            pageTitle = pageTitles[news.newsType];
        } else if (newsListType) {
            pageTitle = pageTitles[newsListType];
        }

        const newsRows = [];
        let row = [];
        if (newsList) {
            for (let i = 0; i < newsList.length; i++) {
                if (i % 4 === 0) {
                    row = [];
                    row.push(newsList[i]);

                    if (i === newsList.length - 1) {
                        row.push(null);
                        row.push(null);
                        row.push(null);
                        newsRows.push(row);
                    }
                } else if (i % 4 === 1) {
                    row.push(newsList[i]);

                    if (i === newsList.length - 1) {
                        row.push(null);
                        row.push(null);
                        newsRows.push(row);
                    }
                } else if (i % 4 === 2) {
                    row.push(newsList[i]);

                    if (i === newsList.length - 1) {
                        row.push(null);
                        newsRows.push(row);
                    }
                } else {
                    row.push(newsList[i]);
                    newsRows.push(row);
                }
            }
        }

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                {/*หัวข้อ*/}
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '40px'}}>{pageTitle}</h3>
                        </div>
                    </div>
                </div>

                {/*ลิสต์ข่าว*/
                    newsListType &&
                    <React.Fragment>
                        <Element name={'topOfTable'}>
                            <div className="container">
                                {newsList &&
                                newsRows.map(row => (
                                    <React.Fragment>
                                        <div className="row mt-4">
                                            {
                                                row.map(newsItem => {
                                                    if (newsItem) {
                                                        return (
                                                            <NewsItem data={newsItem}/>
                                                        );
                                                    } else {
                                                        return (
                                                            <div/>
                                                        );
                                                    }
                                                })
                                            }
                                        </div>
                                        <div className="border-bott"/>
                                    </React.Fragment>
                                ))
                                }
                            </div>
                            <div style={{textAlign: 'center', marginTop: '30px'}}>
                                <ReactPaginate
                                    initialPage={this.state.initialPage}
                                    previousLabel={'<'}
                                    nextLabel={'>'}
                                    breakLabel={'...'}
                                    breakClassName={'break-me'}
                                    pageCount={this.state.pageCount}
                                    marginPagesDisplayed={2}
                                    pageRangeDisplayed={5}
                                    onPageChange={this.handlePageClick}
                                    containerClassName={'pagination'}
                                    activeClassName={'pagination-active'}
                                    previousClassName={'pagination-older'}
                                    nextClassName={'pagination-newer'}
                                />
                            </div>
                        </Element>
                    </React.Fragment>
                }

                {/*รายละเอียดข่าว*/
                    news &&
                    <div className="container">
                        <div className="row text-default">
                            <div className="col-md-8 text-bold">
                                <h4>{news.title}</h4>
                                <span style={{color: '#999'}}>{getDateDisplayFromDateObject(new Date(news.newsDate))}</span>
                            </div>
                            <div className="col-md-4">
                                <div className="social-inside" style={{marginTop: '30px'}}>
                                    <FacebookShareButton
                                        style={{display: 'inline'}}
                                        url={typeof window !== 'undefined' ? window.location.href : ''}>
                                        <a href="#"><i className="fab fa-facebook-f"/></a>
                                    </FacebookShareButton>
                                    <TwitterShareButton
                                        style={{display: 'inline'}}
                                        url={typeof window !== 'undefined' ? window.location.href : ''}>
                                        <a href="#"><i className="fab fa-twitter"/></a>
                                    </TwitterShareButton>
                                    <LineShareButton
                                        style={{display: 'inline'}}
                                        url={typeof window !== 'undefined' ? window.location.href : ''}>
                                        <a href="#"><i className="fab fa-line"/></a>
                                    </LineShareButton>
                                    <EmailShareButton
                                        style={{display: 'inline'}}
                                        url={typeof window !== 'undefined' ? window.location.href : ''}>
                                        <a href="#"><i className="fa fa-envelope"/></a>
                                    </EmailShareButton>
                                </div>
                            </div>
                        </div>
                        <hr style={{
                            border: 0,
                            clear: 'both',
                            display: 'block',
                            width: '100%',
                            backgroundColor: '#f0f0f0',
                            height: '1px',
                        }}/>
                        <div className="row">
                            <div className="col-12 col-md-6">
                                <Scrollbars style={{height: 450}}>
                                    <div className="content mCustomScrollbar_"
                                         style={{background: '#f8f8f8', margin: '0px', paddingTop: '15px', paddingBottom: '10px', paddingLeft: '15px', paddingRight: '5px'}}
                                         dangerouslySetInnerHTML={{__html: news.details}}>
                                    </div>
                                </Scrollbars>
                            </div>
                            <div className="col-12 col-md-6">
                                <Carousel showArrows={true}
                                          showIndicators={true}
                                          onChange={this.onChange}
                                          onClickItem={this.onClickItem}
                                          onClickThumb={this.onClickThumb}
                                          style={{border: '1px solid blue'}}>
                                    {
                                        news.imageList.map(imageFileName => (
                                            <div>
                                                <img src={`${HOST_BACKEND}/uploads/news_assets/${imageFileName}`}/>
                                            </div>
                                        ))
                                    }
                                </Carousel>
                            </div>
                        </div>
                    </div>
                }

                <style jsx>{`
                    .bg-search-service1 .col {
                        padding: 5px;
                    }
                    
                    body {
                        background-image: url(../icehr/images/bg-service1.png);
                        width: 100%;
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: center;
                    }
                    
                    .video_product {
                        width: 100%;
                        padding: 0px;
                        margin-top: 0px;
                    }
                    
                    .sm-carousel li {
                        list-style: none;
                    }
                    
                    .sm-carousel {
                        width: 10%;
                        position: absolute;
                        top: 0;
                    }
                    
                    .sm-carousel li > img {
                        padding-bottom: 10px;
                    }
                    
                    .full-slide-ca {
                        display: inline-block;
                        width: 80%;
                        margin-left: 80px;
                    }
                    
                    .full-slide-ca li > img {
                        margin: 0 auto;
                        display: table;
                    }
                    
                    .display-slide {
                        display: none;
                    }
                    
                    .video > img {
                        width: 100%;
                    }
                    
                    .table-bordered {
                        width: 80%;
                    }
                `}</style>
            </MainLayout>
        );
    }
}