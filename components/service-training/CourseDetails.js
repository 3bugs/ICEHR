import NextHead from 'next/head';
import Link from 'next/link';
//import $ from 'jquery';
import {formatCourseDateLong, numberWithCommas} from "../../etc/utils";
import {SERVICE_SOCIAL, SERVICE_TRAINING, HOST_BACKEND} from "../../etc/constants";
/*import "react-responsive-carousel/lib/styles/carousel.min.css";*/
import './CourseDetails.css'
import {Carousel} from 'react-responsive-carousel';
import "video-react/dist/video-react.css";
import { Scrollbars } from 'react-custom-scrollbars';
import {Player} from 'video-react';
import {
    FacebookShareButton,
    TwitterShareButton,
    LineShareButton,
    EmailShareButton,
} from 'react-share';

export default class CourseDetails extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {
        console.log('CourseDetails componentDidMount() - ' + Math.random());

        fetch('/api/get_course', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                serviceType: this.props.serviceType,
                courseId: this.props.courseId
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    const courseList = result['dataList'];
                    if (courseList.length > 0) {
                        this.setState({
                            course: courseList[0],
                            errorMessage: null,
                        });
                    } else {
                        this.setState({
                            course: null,
                            errorMessage: 'ไม่พบข้อมูล',
                        });
                    }
                } else {
                    this.setState({
                        course: null,
                        errorMessage: result['error']['message'],
                    });
                }
            });
    }

    onChange = () => {
    };
    onClickItem = () => {
    };
    onClickThumb = () => {
    };

    render() {
        const {course, errorMessage} = this.state;

        return (
            <div>
                <NextHead>
                </NextHead>

                {course != null &&
                <div className="container">
                    <div className="row text-default">
                        <div className="col-md-3 text-bold"> ชื่อหลักสูตร / รุ่นที่
                            <br/> วันที่อบรม
                            <br/> สถานที่อบรม
                        </div>
                        <div className="col-md-6">{course.name}
                            <br/>{formatCourseDateLong(course.beginDate, course.endDate)}
                            <br/>{course.place}
                        </div>
                        <div className="col-md-3">
                            <div className="row">
                                <div className="col">
                                    <Link
                                        as={`/service-${course.serviceType}-register/${course.id}`}
                                        href={`/service-${course.serviceType}-register?courseId=${course.id}`}
                                    >
                                        <a className="btn btn-regis">ลงทะเบียน <i className="far fa-edit"></i></a>
                                    </Link>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col">
                                    <div className="social-inside">
                                        <a href="#"><i className="fab fa-facebook-f"></i></a>
                                        <a href="#"><i className="fab fa-twitter"></i></a>
                                        <a href="#"><i className="fab fa-google-plus-g"></i></a>
                                        <a href="#"><i className="fab fa-line"></i></a>
                                    </div>
                                </div>
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
                            <Scrollbars style={{ height: 450 }}>
                                <div className="content mCustomScrollbar_"
                                     style={{background: '#f8f8f8', margin: '0px', paddingTop: '15px', paddingBottom: '10px', paddingLeft: '15px', paddingRight: '5px'}}
                                     dangerouslySetInnerHTML={{__html: course.details}}>
                                </div>
                            </Scrollbars>

                            <div className="title-download-inside mt-4">
                                <h3>ค่าลงทะเบียน</h3>
                            </div>
                            {
                                course.fees.length === 0 &&
                                <div style={{color: 'red'}}>ไม่มีข้อมูล</div>
                            }
                            <table className="table table-price table-bordered" style={{width: '85%'}}>
                                <tbody>
                                {
                                    course.fees.map(fee => {
                                        let feeAmountText = fee.amount == null ? null : `${numberWithCommas(fee.amount)} บาท`;
                                        return (
                                            <tr>
                                                <td colSpan={feeAmountText == null ? 2 : 1} style={{}}>{fee.title}</td>
                                                {
                                                    feeAmountText != null &&
                                                    <td style={{whiteSpace: 'nowrap'}}>{feeAmountText}</td>
                                                }
                                            </tr>
                                        )
                                    })
                                }
                                </tbody>
                            </table>
                        </div>
                        <div className="col-12 col-md-6">
                            <Carousel showArrows={true}
                                      showIndicators={true}
                                      onChange={this.onChange}
                                      onClickItem={this.onClickItem}
                                      onClickThumb={this.onClickThumb}
                                      style={{border: '1px solid blue'}}>
                                {
                                    course.assets.filter(asset => {
                                        return asset.type === 'image';
                                    }).map(asset => (
                                        <div>
                                            <img src={`${HOST_BACKEND}/uploads/course_assets/${asset.fileName}`}/>
                                        </div>
                                    ))
                                }
                                {/*<div>*/}
                                {/*<video width="100%" height="auto" autoPlay controls>
                                        <img src="/static/images/banner.jpg"/>
                                        <source src="https://media.w3.org/2010/05/sintel/trailer_hd.mp4" type="video/mp4"/>
                                        บราวเซอร์ของคุณไม่รองรับการเล่นวิดีโอ
                                    </video>*/}
                                {/*<Player
                                        playsInline
                                        poster="/assets/poster.png"
                                        src="https://media.w3.org/2010/05/sintel/trailer_hd.mp4"
                                    />*/}
                                {/*</div>*/}
                                {
                                    /*[1, 2, 3, 4, 5, 6, 7].map((value) => {
                                        return value % 2 === 0 ?
                                        (<div>
                                            <img src="/static/images/banner.jpg" className="img-fluid"/>
                                            {/!*<p className="legend">Test 1</p>*!/}
                                        </div>) :
                                        (<div>
                                            <img src="/static/images/image-test.png" className="img-fluid"/>
                                            {/!*<p className="legend">Test 1</p>*!/}
                                        </div>);
                                    })*/
                                }
                            </Carousel>

                            {/*<div id="slider" className="flexslider">
                                <ul className="slides">
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    {<li>
                                        <div className="display-slide video" rel="5">
                                            <div className="video-bg">
                                                <video width="100%" className="video_product play">
                                                    <source src="/static/video/1_1.mp4" type="video/mp4"/>
                                                </video>
                                            </div>
                                            <div className="video-overlay"></div>
                                        </div>
                                    </li>}
                                </ul>
                            </div>
                            <div id="carousel" className="flexslider">
                                <ul className="slides select-display-slide">
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li rel="5" className="video"><img src="/static/images/image-test.png" className="img-responsive"/></li>
                                </ul>
                            </div>*/}

                            <div className="border-project">
                                <h3><img src="/static/images/icon-ppl.svg"/> ผู้รับผิดชอบโครงการ</h3>
                                <div className="row">
                                    <div className="col-md-4  text-black">ชื่อ-นามสกุล</div>
                                    <div className="col-md-8">
                                        {course.responsibleUser.firstName + ' ' + course.responsibleUser.lastName}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-4  text-black">เบอร์โทร</div>
                                    <div className="col-md-8">
                                        {course.responsibleUser.phoneOffice}
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-4  text-black">อีเมล</div>
                                    <div className="col-md-8">
                                        <a href={`mailto:${course.responsibleUser.email}`}>{course.responsibleUser.email}</a>
                                    </div>
                                </div>
                            </div>
                            <div className="title-download-inside mt-3">
                                <h3>เอกสารดาวน์โหลด</h3>
                                {
                                    course.assets.filter(asset => {
                                        return asset.type === 'pdf';
                                    }).map(asset => (
                                        <div>
                                            <a href={`${HOST_BACKEND}/uploads/course_assets/${asset.fileName}`}
                                               target="_blank">
                                                <img src="/static/images/pdf-icon.svg"/>&nbsp;&nbsp;{asset.title}
                                            </a>
                                        </div>
                                    ))
                                }
                                {
                                    course.assets.filter(asset => {
                                        return asset.type === 'pdf';
                                    }).length === 0 &&
                                    <div>ไม่มีเอกสาร</div>
                                }
                            </div>
                        </div>
                    </div>
                </div>
                }

                {/*กรณีเกิดข้อผิดพลาด หรือไม่มีข้อมูล (เช่น user แก้ตัวเลขที่ URL เอง)*/}
                {course == null &&
                <div style={{textAlign: 'center', marginTop: '20px', color: 'red'}}>{errorMessage}</div>
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
            </div>
        );
    }
}