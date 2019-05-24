import Link from 'next/link';
import {formatCourseDateLong, numberWithCommas} from "../../etc/utils";
import {SERVICE_SOCIAL, SERVICE_TRAINING} from "../../etc/constants";

export default class CourseList extends React.Component {
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

    render() {
        const {course, errorMessage} = this.state;

        return (
            <div>
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
                    <hr/>
                    <div className="row">
                        <div className="col-12 col-md-6">
                            <div className="content mCustomScrollbar"
                                 dangerouslySetInnerHTML={{__html: course.details}}>
                            </div>
                        </div>
                        <div className="col-12 col-md-6">
                            <div id="slider" className="flexslider">
                                <ul className="slides">
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li>
                                        <div className="display-slide video" rel="5">
                                            <div className="video-bg">
                                                <video width="100%" className="video_product play">
                                                    <source src="/static/video/1_1.mp4" type="video/mp4"/>
                                                </video>
                                            </div>
                                            <div className="video-overlay"></div>
                                        </div>
                                    </li>
                                    {/*items mirrored twice, total of 12*/}
                                </ul>
                            </div>
                            <div id="carousel" className="flexslider">
                                <ul className="slides  select-display-slide">
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li><img src="/static/images/image-test.png" className="img-fluid"/></li>
                                    <li rel="5" className="video"><img src="/static/images/image-test.png" className="img-responsive"/></li>
                                    {/*items mirrored twice, total of 12*/}
                                </ul>
                            </div>
                            <div className="border-project">
                                <h3><img src="/static/images/icon-ppl.svg"/> ผู้รับผิดชอบโครงการ</h3>
                                <div className="row">
                                    <div className="col-md-4  text-black"> ชื่อ-นามสกุล
                                        <br/> เบอร์โทร
                                        <br/> อีเมล
                                    </div>
                                    <div className="col-md-8">{course.responsibleUser.firstName + ' ' + course.responsibleUser.lastName}
                                        <br/>{course.responsibleUser.phoneOffice}
                                        <br/>{course.responsibleUser.email}
                                    </div>
                                </div>
                            </div>
                            <div className="title-download-inside mt-3">
                                <h3>เอกสารดาวน์โหลด</h3>
                                <a href="#"><img src="/static/images/pdf-icon.svg"/> &nbsp;เอกสารดาวน์โหลด1</a>
                                <a href="#"><img src="/static/images/doc-icon.svg"/> &nbsp;เอกสารดาวน์โหลด2</a>
                                <a href="#"><img src="/static/images/pdf-icon.svg"/> &nbsp;เอกสารดาวน์โหลด3</a>
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

                `}</style>
            </div>
        );
    }
}