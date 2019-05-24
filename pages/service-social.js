import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import CourseList from "../components/service-training/CourseList";
import CourseDetails from "../components/service-training/CourseDetails";
import {SERVICE_SOCIAL} from "../etc/constants";

export default class ServiceSocial extends React.Component {
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

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                {/*หัวข้อ "บริการสังคม"*/}
                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 onClick={this.test}>บริการสังคม</h3></div>
                    </div>
                </div>

                {/*แถบค้นหาบริการสังคม*/}
                <div className="bg-search-service3">
                    <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
                        <div className="row">
                            <div className="col-md-5 col-lg-6">
                                <input id="textinput" name="textinput" type="text" placeholder="คำค้น" className="form-control input-md"/>
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

                {
                    this.props.result.showList &&
                    <CourseList
                        serviceType={SERVICE_SOCIAL}
                        onClickCourseRow={this.onClickCourseRow}
                    />
                }

                {
                    !this.props.result.showList &&
                    <CourseDetails
                        serviceType={SERVICE_SOCIAL}
                        courseId={this.props.result.courseId}
                    />
                }

                <style jsx>{`
                    [data-href] {
                        cursor: pointer;
                    }
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    /* Responsive Table Style */
                    .responsive-table {
                        text-align: left;
                        background-color: #fff;
                        border-collapse: collapse;
                        margin: 40px auto;
                    }
                    
                    .responsive-table tr:hover {
                        background-color: #ffedda;
                    }
                    
                    .responsive-table th,
                    .responsive-table td {
                        padding: 3px 10px;
                    }
                    
                    @media(max-width: 480px) {
                        .responsive-table {
                            width: 100%;
                        }
                        .responsive-table thead {
                            display: none;
                        }
                        .responsive-table tbody tr:nth-of-type(even) {
                            background-color: #ffedda;
                        }
                        .responsive-table tbody td {
                            display: block;
                            font-size: 1rem;
                        }
                        .responsive-table tbody td:before {
                            content: attr(data-table);
                            display: block;
                            float: left;
                            width: 40%;
                            margin-right: 10px;
                            padding-right: 10px;
                            font-size: 1rem;
                            border-right: 1px solid #ccc;
                        }
                        .responsive-table tbody td:after {
                            content: '';
                            display: block;
                            clear: both;
                        }
                        .responsive-table tr {
                            border: 1px solid #ccc;
                        }
                    }
                    /* End Responsive Table Style */                
                `}</style>
            </MainLayout>
        );
    }
}