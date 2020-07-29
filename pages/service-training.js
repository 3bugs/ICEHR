import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import Link from 'next/link';
import CourseList from '../components/service-training/CourseList';
import CourseDetails from '../components/service-training/CourseDetails';
import {SERVICE_SOCIAL, SERVICE_TRAINING, longMonthNames} from "../etc/constants";
import Modal from "react-bootstrap/Modal";
import {Element} from "react-scroll/modules";
import {formatCourseDateShort, numberWithCommas} from "../etc/utils";
import ReactPaginate from "react-paginate";
import SearchResultTable from '../components/SearchResultTable';

const SEARCH_INPUT_TITLE = 'searchTitle';
const SEARCH_SELECT_CATEGORY = 'searchCategory';
const SEARCH_SELECT_PLACE_TYPE = 'searchPlaceType';
const SEARCH_SELECT_MONTH = 'searchMonth';
const SEARCH_SELECT_YEAR = 'searchYear';

export default class ServiceTraining extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {
      searchFields: {},
      searchResultList: null,
      showSearchResultModal: false,
    };
  }

  //https://nextjs.org/learn/basics/fetching-data-for-pages/fetching-data-for-pages

  static getInitialProps = async ({req, query}) => {
    const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
    const {courseId} = query;

    const res = await fetch(baseUrl + '/api/get_training_course_category', {
      method: 'post',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({}),
    });
    const result = await res.json();
    let trainingCourseCategoryList = [];
    if (result.error.code === 0) {
      trainingCourseCategoryList = result.dataList;
    }

    if (courseId === undefined) {
      let result = {showList: true};
      return {result, trainingCourseCategoryList};
    } else {
      let result = {showList: false, courseId: courseId};

      const courseRes = await fetch(baseUrl + '/api/get_course', {
        method: 'post',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          serviceType: SERVICE_TRAINING,
          courseId: courseId,
        }),
      });
      const courseResult = await courseRes.json();
      let course = null;
      if (courseResult.error.code === 0) {
        course = courseResult.dataList[0];
      }

      return {result, trainingCourseCategoryList, course};
    }
  };

  componentDidMount() {
    let currentYear = new Date().getFullYear() + 543;
    const yearList = [];
    for (let i = 0; i < 3; i++) {
      yearList.push(currentYear + i);
    }
    this.setState({yearList});
  }

  handleInputChange = (field, e) => {
    let {searchFields} = this.state;
    searchFields[field] = e.target.value;
    this.setState({searchFields});
  };

  handleClickSearch = e => {
    let {searchFields} = this.state;

    fetch('/api/search_course', {
      method: 'post',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        serviceType: SERVICE_TRAINING,
        searchFields
      }),
    })
      .then(result => result.json())
      .then(result => {
        if (result['error']['code'] === 0) {
          this.setState({
            searchResultList: result.dataList,
            showSearchResultModal: true,
          });
        } else {
          alert(result['error']['message']);
        }
      })
      .catch(error => {
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
      });
  };

  handleCloseSearchResultModal = (serviceType, courseId) => {
    // ถ้ามีการคลิกแถวหลักสูตรในผลการค้นหา จะ redirect ไปยังหน้ารายละเอียดหลักสูตรนั้น
    if (courseId) {
      window.location.href = `/service-${serviceType}/${courseId}`;
    } else { // กรณีปิด dialog ผลการค้นหา โดยไม่ได้คลิกหลักสูตรไหน
      this.setState({
        showSearchResultModal: false
      });
    }
  };

  render() {
    const {searchFields, searchResultList} = this.state;

    return (
      <MainLayout>
        <NextHead>
        </NextHead>

        {/*Modal ผลการค้นหา*/}
        <Modal
          dialogClassName={'modal-search-result'}
          show={this.state.showSearchResultModal}
          onHide={this.handleCloseSearchResultModal}
          centered>
          <Modal.Body>
            <div className="redtxt" style={{marginTop: '10px', marginBottom: '0px'}}>
              <h3 style={{textAlign: 'center'}}>ผลการค้นหา</h3>
            </div>

            <div id="data5" className="detail-inside-box" style={{textAlign: 'center'}}>
              {searchResultList != null &&
              <SearchResultTable
                serviceType={SERVICE_TRAINING}
                courseList={searchResultList}
                handleCloseSearchResultModal={this.handleCloseSearchResultModal}
              />
              }
              {searchResultList == null &&
              <div style={{textAlign: 'center'}}>ไม่พบข้อมูล</div>
              }
              <a href="javascript:void(0)"
                 onClick={this.handleCloseSearchResultModal}
                 className="btn-submit mt-2 mb-3">
                ปิด
              </a>
            </div>
          </Modal.Body>
        </Modal>

        {/*หัวข้อ "บริการฝึกอบรม"*/}
        <div className="container">
          <div className="row">
            <div className="col">
              <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '40px', width: '100%'}}>บริการฝึกอบรม</h3></div>
          </div>
        </div>

        {/*แถบค้นหาหลักสูตรฝึกอบรม*/}
        <div className="bg-search-service1" style={{height: '170px'}}>
          <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
            <div className="row">
              <div className="col" style={{paddingTop: 0, paddingBottom: 0}}>
                <input
                  value={searchFields[SEARCH_INPUT_TITLE] || ''}
                  onChange={this.handleInputChange.bind(this, SEARCH_INPUT_TITLE)}
                  type="text" placeholder="คำค้น"
                  className="form-control input-md"/>
              </div>
              <div className="col" style={{paddingTop: 0, paddingBottom: 0}}>
                <select
                  value={searchFields[SEARCH_SELECT_CATEGORY] || '0'}
                  onChange={this.handleInputChange.bind(this, SEARCH_SELECT_CATEGORY)}
                  className="form-control">
                  <option value="0" disabled selected>หมวดหมู่</option>
                  {
                    this.props.trainingCourseCategoryList.map(category => (
                      <option value={category.id}>{category.title}</option>
                    ))
                  }
                  <option value="9999">( ไม่ระบุหมวดหมู่ )</option>
                </select>
              </div>
              <div className="col" style={{paddingTop: 0, paddingBottom: 0}}>
                <select
                  value={searchFields[SEARCH_SELECT_PLACE_TYPE] || '0'}
                  onChange={this.handleInputChange.bind(this, SEARCH_SELECT_PLACE_TYPE)}
                  className="form-control">
                  <option value="0" disabled selected>สถานที่</option>
                  <option value="1">กรุงเทพมหานคร</option>
                  <option value="2">ภูมิภาค</option>
                  <option value="99">อื่นๆ</option>
                  <option value="9999">( ไม่ระบุสถานที่ )</option>
                </select>
              </div>
              <div className="col" style={{paddingTop: 0, paddingBottom: 0}}>
                <select
                  value={searchFields[SEARCH_SELECT_YEAR] || '0'}
                  onChange={this.handleInputChange.bind(this, SEARCH_SELECT_YEAR)}
                  className="form-control boxbox">
                  <option value="0" disabled selected>ปี พ.ศ.</option>
                  {
                    this.state.yearList &&
                    this.state.yearList.map((year, index) => (
                      <option value={year - 543}>{year}</option>
                    ))
                  }
                  <option value="9999">( ไม่ระบุปี )</option>
                </select>
              </div>
              <div className="col" style={{paddingTop: 0, paddingBottom: 0}}>
                <select
                  value={searchFields[SEARCH_SELECT_MONTH] || '0'}
                  onChange={this.handleInputChange.bind(this, SEARCH_SELECT_MONTH)}
                  className="form-control boxbox">
                  <option value="0" disabled selected>เดือน</option>
                  {
                    longMonthNames.map((month, index) => (
                      <option value={index + 1}>{month}</option>
                    ))
                  }
                  <option value="9999">( ไม่ระบุเดือน )</option>
                </select>
              </div>
            </div>
            <div className="submitbox" style={{marginTop: '15px'}}><a href="#" className="btn-submit" onClick={this.handleClickSearch}>ค้นหา</a></div>
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

        {/*รายการหลักสูตร*/
          this.props.result.showList &&
          <CourseList
            serviceType={SERVICE_TRAINING}
            onClickCourseRow={this.onClickCourseRow}
          />
        }

        {/*รายละเอียดหลักสูตร*/
          !this.props.result.showList &&
          <CourseDetails
            serviceType={SERVICE_TRAINING}
            courseId={this.props.result.courseId}
            course={this.props.course}
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
