import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import CourseList from "../components/service-training/CourseList";
import CourseDetails from "../components/service-training/CourseDetails";
import {SERVICE_DRIVING_LICENSE, SERVICE_SOCIAL, SERVICE_TRAINING} from "../etc/constants";
import CalendarView from "../components/CalendarView";
import Router from 'next/router';

export default class ServiceDrivingLicense extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = context => {
        const {courseId} = context.query;

        if (courseId === undefined) {
            let result = {showList: true};
            return {result};
        } else {
            let result = {showList: false, courseId: courseId};
            return {result};
        }
    };

    componentDidMount() {
        const today = new Date();
        const day = today.getDate();
        const month = today.getMonth() + 1;
        const year = today.getFullYear();

        this.updateCalendar(month, year);
    }

    updateCalendar = (month, year) => {
        fetch('/api/get_course', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                serviceType: SERVICE_DRIVING_LICENSE,
                month, year
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        month, year,
                        courseList: result['dataList'],
                        errorMessage: null,
                    });
                } else {
                    this.setState({
                        courseList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            });
    };

    showCourseDetails = (courseId) => {
        Router.push('/service-driving-license/' + courseId);
    };

    /***************************************
     https://medium.com/the-web-tub/managing-your-react-state-with-redux-affab72de4b1
    ****************************************/

    render() {
        const {month, year, courseList, errorMessage} = this.state;

        return (
            <MainLayout>
                <NextHead>
                    {/*<link rel="stylesheet" href="/static/css/jquery-ui.css"/>
                    <script src="/static/js/jquery-ui.js"/>*/}
                </NextHead>

                {/*หัวข้อ "บริการอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่"*/}
                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 onClick={this.test}>บริการอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่</h3></div>
                    </div>
                </div>

                {/*แถบค้นหาบริการสังคม*/}
                <div className="bg-search-service3">
                    <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
                        <div className="row">
                            <div className="col-md-5 col-lg-6">
                                <select id="selectbasic" name="selectbasic" className="form-control">
                                    <option value="" disabled selected>ประเภทหลักสูตร</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col-md-2 col-lg-2">
                                <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                    <option value="" disabled selected>ปี พ.ศ.</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col-md-2 col-lg-2">
                                <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                    <option value="" disabled selected>เดือน</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col-md-3 col-lg-2">
                                <div className="submitbox"><a href="#" className="btn-submit">ค้นหา</a></div>
                            </div>
                        </div>
                    </div>
                    <div className="container d-block d-sm-none d-md-none d-lg-none d-xl-none">
                        <div className="row">
                            <div className="col">
                                <div id="uniquename" style={{display: 'none'}}>
                                    <div className="row">
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <input id="textinput" name="textinput" type="text" placeholder="คำค้น" className="form-control input-md"/>
                                        </div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                                <option value="" disabled selected>ปี พ.ศ.</option>
                                                <option value="1">Option one</option>
                                                <option value="2">Option two</option>
                                            </select>
                                        </div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                                <option value="" disabled selected>เดือน</option>
                                                <option value="1">Option one</option>
                                                <option value="2">Option two</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div className="submitbox" style={{paddingBottom: '50px'}}>
                                        <a href="#" className="btn-submit">ค้นหา</a>
                                    </div>
                                </div>
                                <a href="javascript:ReverseDisplay('uniquename')" className="btn btn-success">
                                    แสดงตัวกรองค้นหา
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {/*ปฏิทินหลักสูตร*/
                    this.props.result.showList && (courseList !== null) &&
                    <CalendarView month={month} year={year}
                                  courseList={courseList}
                                  handlePreviousNextMonthCallback={this.updateCalendar}
                                  handleClickCourseCallback={this.showCourseDetails}
                    />
                }
                {/*กรณีเกิดข้อผิดพลาดในการโหลดข้อมูลหลักสูตรจาก server*/
                    this.props.result.showList && courseList === null &&
                    <div className="mt-3" style={{textAlign: 'center', color: 'red'}}>{errorMessage}</div>
                }

                {/*รายละเอียดหลักสูตร*/
                    !this.props.result.showList &&
                    <CourseDetails
                        serviceType={SERVICE_DRIVING_LICENSE}
                        courseId={this.props.result.courseId}
                    />
                }

                <style jsx>{`
                    .submitbox {
                        margin-top: 0px;
                    }                    
                `}</style>
            </MainLayout>
        );
    }
}