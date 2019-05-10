import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import CourseList from '../components/service-training/CourseList';
import CourseDetails from '../components/service-training/CourseDetails';

export default class ServiceTraining extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    //https://nextjs.org/learn/basics/fetching-data-for-pages/fetching-data-for-pages

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

                {/*หัวข้อ "บริการฝึกอบรม"*/}
                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 onClick={this.test}>บริการฝึกอบรม</h3></div>
                    </div>
                </div>

                {/*แถบค้นหาหลักสูตรฝึกอบรม*/}
                <div className="bg-search-service1">
                    <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
                        <div className="row">
                            <div className="col">
                                <input id="textinput" name="textinput" type="text" placeholder="คำค้น" className="form-control input-md"/>
                            </div>
                            <div className="col">
                                <select id="selectbasic" name="selectbasic" className="form-control">
                                    <option value="" disabled selected>หมวดหมู่</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col">
                                <select id="selectbasic" name="selectbasic" className="form-control">
                                    <option value="" disabled selected>กรุงเทพ/ภูมิภาค</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col">
                                <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                    <option value="" disabled selected>ปี พ.ศ.</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                            <div className="col">
                                <select id="selectbasic" name="selectbasic" className="form-control boxbox">
                                    <option value="" disabled selected>เดือน</option>
                                    <option value="1">Option one</option>
                                    <option value="2">Option two</option>
                                </select>
                            </div>
                        </div>
                        <div className="submitbox"><a href="#" className="btn-submit">ค้นหา</a></div>
                    </div>
                    <div className="container d-block d-sm-none d-md-none d-lg-none d-xl-none">
                        <div className="row">
                            <div className="col">
                                <div id="uniquename" style={{display: 'none'}}>
                                    <div className="row">
                                        <div className="col-12">
                                            <input id="textinput" name="textinput" type="text" placeholder="คำค้น"
                                                   className="form-control input-md"/>
                                        </div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <select id="selectbasic" name="selectbasic" className="form-control">
                                                <option value="" disabled selected>หมวดหมู่</option>
                                                <option value="1">Option one</option>
                                                <option value="2">Option two</option>
                                            </select>
                                        </div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <select id="selectbasic" name="selectbasic" className="form-control">
                                                <option value="" disabled selected>กรุงเทพ/ภูมิภาค</option>
                                                <option value="1">Option one</option>
                                                <option value="2">Option two</option>
                                            </select>
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
                                    <div className="submitbox" style={{paddingBottom: '50px'}}><a href="#" className="btn-submit">ค้นหา</a></div>
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
                        onClickCourseRow={this.onClickCourseRow}
                    />
                }

                {
                    !this.props.result.showList &&
                    <CourseDetails
                        courseId={this.props.result.courseId}
                    />
                }

                <style jsx>{`
                    .bg-search-service1 .col {
                        padding: 5px;
                    }                    
                `}</style>
            </MainLayout>
        )
    }
}