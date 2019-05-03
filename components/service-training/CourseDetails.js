import Link from 'next/link';
import {formatCourseDateLong, numberWithCommas} from "../../etc/utils";

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
                courseId: this.props.courseId
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        course: result['dataList'][0],
                        errorMessage: null,
                    });
                } else {
                    this.setState({
                        course: null,
                        errorMessage: result['error']['message'],
                    });
                }
            });
    }

    render() {
        let course = this.state.course;

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
                                    <Link href="/service-training-register">
                                        <a className="btn btn-regis">ลงทะเบียน <i className="far fa-edit"></i></a>
                                    </Link>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col">
                                    <div className="social-inside"><a href="#"><i className="fab fa-facebook-f"></i></a> <a href="#"><i
                                        className="fab fa-twitter"></i></a> <a href="#"><i className="fab fa-google-plus-g"></i></a>
                                        <a href="#"><i className="fab fa-line"></i></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div className="row">
                        <div className="col-12 col-md-6">
                            <div className="content mCustomScrollbar"><b>หลักการและเหตุผล</b>
                                <br/>ระบบเรตติ้ง หรือ Rating System for Audit เป็นเครื่องมือที่
                                สำคัญอย่างหนึ่งของงานการตรวจสอบภายในสมัยใหม่ เพราะสามารถ
                                สะท้อนระดับความรุนแรงของผลที่พบจากการตรวจสอบได้อย่างชัดเจน มีเกณฑ์เงื่อนไขที่ชัดเจน
                                ซึ่งจะช่วยให้เกิดประสิทธิภาพของงานตรวจสอบ ทั้งในมุมมอง
                                ของผู้ตรวจสอบที่มีประสบการณ์ทักษะที่แตกต่างกันและทั้งหน่วยรับ
                                ตรวจในการรับรู้ล่วงหน้าว่าจะปรับปรุงพัฒนาผลดำเนินงานของตนอย่างไรให้ระดับ Rating
                                ดีขึ้นกว่าการตรวจสอบครั้งก่อนหน้า
                                <br/>
                                <br/> <b>วัตถุประสงค์ </b>
                                <br/> 1. เพื่อให้ผู้เข้าอบรมเข้าใจมาตรฐานการพัฒนา Audit Risk-Based Rating System เพื่อนำไปใช้ในการพัฒนา
                                ใช้งานตรวจสอบภายใน 2. เพื่อให้ผู้เข้าอบรมได้ทดสอบกรอกข้อมูลความเห็นตาม checklist
                                เพื่อนำไปใช้เป็นต้นแบบการพัฒนา Audit Risk-Based Rating System ในอนาคต
                                <br/>
                                <br/><b>เนื้อหาการฝึกอบรม</b>
                                <br/>1) มาตรฐานการพัฒนา Audit Risk-Based Rating System ในการให้ความเห็นในรายงานผลการตรวจสอบภายใน 2)
                                เหตุผลและความจ าเป็นของการมี Audit Risk-Based Rating System 3) นิยามของ Audit Risk-Based Rating
                                Systemในมุมของ Composite Rating ที่มาจากRisk Assessment และ Risk-Control Matrix 4) วิธีการด
                                าเนินงานในการพัฒนา Audit Risk-Based Rating System เชิงคุณภาพ 5) ระดับของ Audit Risk-Based Rating Scale
                                ที่เหมาะสม
                            </div>
                            <br/>
                            <table className="table table-price table-bordered">
                                <tbody>
                                <tr>
                                    <td style={{width: '220px'}}>ราคาเต็ม</td>
                                    <td>{numberWithCommas(course.applicationFee)} บาท</td>
                                </tr>
                                <tr>
                                    <td style={{width: '220px'}}>Early Bird</td>
                                    <td>{numberWithCommas(course.applicationFee)} บาท</td>
                                </tr>
                                <tr>
                                    <td style={{width: '220px'}}>บุคลากร มธ. ลด 20%</td>
                                    <td>{numberWithCommas(course.applicationFee)} บาท</td>
                                </tr>
                                <tr>
                                    <td colSpan="2">10 แถม 1</td>
                                </tr>
                                </tbody>
                            </table>
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
                                    <div className="col-md-8"> สุวิมล คุ้มเขต
                                        <br/> 02-6133820-5 ต่อ 0 หรือ 100
                                        <br/> Sermtham@tu.ac.th
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

                <style jsx>{`

                `}</style>
            </div>
        );
    }
}