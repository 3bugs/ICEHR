import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import Link from 'next/link';
import CourseDetails from '../components/service-training/CourseDetails';
import {SERVICE_SOCIAL, SERVICE_TRAINING, longMonthNames, HOST_BACKEND} from "../etc/constants";
import {formatCourseDateLong, formatCourseDateShort, getDateDisplayFromDateObject, numberWithCommas} from "../etc/utils";
import Modal from "react-bootstrap/Modal";
import {Element} from "react-scroll/modules";
import {Scrollbars} from "react-custom-scrollbars";
import {Carousel} from "react-responsive-carousel";

export default class News extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const {id} = query;

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
        return {news};
    };

    componentDidMount() {

    }

    render() {
        const {news} = this.props;
        const pageTitles = {
            'training': 'ข่าวการฝึกอบรม',
            'public-relations': 'ข่าวประชาสัมพันธ์',
            'in-house': 'หลักสูตร In-House ที่ผ่านมา',
            'activity': 'ภาพกิจกรรม',
        };

        const pageTitle = pageTitles[news.newsType];

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                {/*หัวข้อ "บริการฝึกอบรม"*/}
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '40px'}}>{pageTitle}</h3>
                        </div>
                    </div>
                </div>

                <div className="container">
                    <div className="row text-default">
                        <div className="col-md-8 text-bold">
                            <h4>{news.title}</h4>
                            <span style={{color: '#999'}}>{getDateDisplayFromDateObject(new Date(news.newsDate))}</span>
                        </div>
                        <div className="col-md-4">
                            <div className="social-inside" style={{marginTop: '30px'}}>
                                <a href="#"><i className="fab fa-facebook-f"></i></a>
                                <a href="#"><i className="fab fa-twitter"></i></a>
                                <a href="#"><i className="fab fa-google-plus-g"></i></a>
                                <a href="#"><i className="fab fa-line"></i></a>
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
        )
    }
}