import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail, isPositiveInteger, getDateFormatFromDateObject} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {HOST_BACKEND, SERVICE_SOCIAL} from "../etc/constants";
import Dialog from "../components/Dialog";
//import {RegisterSuccessDialog} from './service-training-register';
import DatePicker from "react-datepicker";
import {Modal} from "react-bootstrap";

const TOP_OF_FORM = 'topOfForm';
//const ORGANIZATION_TYPE_OTHER = '9999';

const REGISTER_TRAINEE_TITLE = 'traineeTitle';
const REGISTER_TRAINEE_FIRST_NAME = 'traineeFirstName';
const REGISTER_TRAINEE_LAST_NAME = 'traineeLastName';
const REGISTER_TRAINEE_BIRTH_DATE = 'traineeBirthDate';
const REGISTER_TRAINEE_BIRTH_DATE_TEMP = 'traineeBirthDateTemp';
const REGISTER_TRAINEE_OCCUPATION = 'traineeOccupation';
const REGISTER_TRAINEE_WORK_PLACE = 'traineeWorkPlace';
const REGISTER_TRAINEE_ADDRESS = 'traineeAddress';
const REGISTER_TRAINEE_SUB_DISTRICT = 'traineeSubDistrict';
const REGISTER_TRAINEE_DISTRICT = 'traineeDistrict';
const REGISTER_TRAINEE_PROVINCE = 'traineeProvince';
const REGISTER_TRAINEE_POSTAL_CODE = 'traineePostalCode';
const REGISTER_TRAINEE_PHONE = 'traineePhone';
const REGISTER_TRAINEE_EMAIL = 'traineeEmail';
const REGISTER_TRAINEE_CONTACT_PERSON_NAME = 'traineeContactPersonName'; //ชื่อบุคคลที่สามารถติดต่อได้ในกรณีฉุกเฉิน
const REGISTER_TRAINEE_CONTACT_PERSON_PHONE = 'traineeContactPersonPhone'; //เบอร์โทรบุคคลที่สามารถติดต่อได้ในกรณีฉุกเฉิน
const REGISTER_TRAINEE_DISEASE = 'traineeDisease'; //โรคประจำตัว
const REGISTER_TRAINEE_NEWS_SOURCE_WEB = 'traineeNewsSourceWeb'; // 1
const REGISTER_TRAINEE_NEWS_SOURCE_EMAIL = 'traineeNewsSourceEmail'; // 2
const REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE = 'traineeNewsSourceBrochure'; // 4
const REGISTER_TRAINEE_NEWS_SOURCE_ONLINE = 'traineeNewsSourceOnline'; // 8
const REGISTER_TRAINEE_NEWS_SOURCE_MOUTH = 'traineeNewsSourceMouth'; // 16ne

const REGISTER_RECEIPT_NAME = 'registerReceiptName';
const REGISTER_RECEIPT_ADDRESS = 'registerReceiptAddress';
const REGISTER_RECEIPT_SUB_DISTRICT = 'registerReceiptSubDistrict';
const REGISTER_RECEIPT_DISTRICT = 'registerReceiptDistrict';
const REGISTER_RECEIPT_PROVINCE = 'registerReceiptProvince';
const REGISTER_RECEIPT_POSTAL_CODE = 'registerReceiptPostalCode';
const REGISTER_RECEIPT_ORGANIZATION_PHONE = 'registerReceiptOrganizationPhone';
const REGISTER_RECEIPT_TAX_ID = 'registerReceiptTaxId';

class RegisterProgress extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {};
  }

  render() {
    let {activeStep, text} = this.props;

    return (
      <div className="container">
        <ul className="progressbar">
          <li className={activeStep === 1 ? 'active' : 'complete'}>{text[0]}</li>
          <li className={activeStep === 2 ? 'active' : (activeStep > 2 ? 'complete' : '')}>{text[1]}</li>
          <li className={activeStep === 3 ? 'active' : ''}>{text[2]}</li>
        </ul>

        <style jsx>{`
                    .container {
                        width: 100%;
                        margin: 50px auto;
                    }
                    .progressbar {
                        counter-reset: step;
                    }
                    .progressbar li {
                        list-style-type: none;
                        width: 32%;
                        float: left;
                        font-size: 0.8rem;
                        position: relative;
                        text-align: center;
                        text-transform: uppercase;
                        color: #7d7d7d;
                    }
                    .progressbar li:before {
                        width: 40px;
                        height: 40px;
                        content: counter(step);
                        counter-increment: step;
                        line-height: 36px;
                        border: 3px solid #ccc;
                        display: block;
                        font-size: 1.2rem;
                        text-align: center;
                        margin: 0 auto 5px auto;
                        border-radius: 50%;
                        background-color: white;
                    }
                    .progressbar li:after {
                        width: 100%;
                        height: 2px;
                        content: '';
                        position: absolute;
                        background-color: #ccc;
                        top: 18px;
                        left: -50%;
                        z-index: -1;
                    }
                    .progressbar li:first-child:after {
                        content: none;
                    }
                    .progressbar li.active, .progressbar li.complete {
                        color: green;
                    }
                    .progressbar li.active:before {
                        border-color: #55b776;
                    }
                    .progressbar li.complete:before {
                        border-color: #55b776;
                        background-color: #55b776;
                        color: white;
                    }
                    .progressbar_not_used li.active + li:after {
                        background-color: #55b776;
                    }
                    .progressbar li.complete + li:after {
                        background-color: #55b776;
                    }
                `}</style>
      </div>
    );
  }
}

class RegisterProgress2Step extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {};
  }

  render() {
    let {activeStep, text} = this.props;

    return (
      <div className="container">
        <ul className="progressbar">
          <li className={activeStep === 1 ? 'active' : 'complete'}>{text[0]}</li>
          <li className={activeStep === 3 ? 'active' : ''}>{text[1]}</li>
        </ul>

        <style jsx>{`
                    .container {
                        width: 100%;
                        margin: 50px auto;
                    }
                    .progressbar {
                        counter-reset: step;
                    }
                    .progressbar li {
                        list-style-type: none;
                        width: 48%;
                        float: left;
                        font-size: 0.8rem;
                        position: relative;
                        text-align: center;
                        text-transform: uppercase;
                        color: #7d7d7d;
                    }
                    .progressbar li:before {
                        width: 40px;
                        height: 40px;
                        content: counter(step);
                        counter-increment: step;
                        line-height: 36px;
                        border: 3px solid #ccc;
                        display: block;
                        font-size: 1.2rem;
                        text-align: center;
                        margin: 0 auto 5px auto;
                        border-radius: 50%;
                        background-color: white;
                    }
                    .progressbar li:after {
                        width: 100%;
                        height: 2px;
                        content: '';
                        position: absolute;
                        background-color: #ccc;
                        top: 18px;
                        left: -50%;
                        z-index: -1;
                    }
                    .progressbar li:first-child:after {
                        content: none;
                    }
                    .progressbar li.active, .progressbar li.complete {
                        color: green;
                    }
                    .progressbar li.active:before {
                        border-color: #55b776;
                    }
                    .progressbar li.complete:before {
                        border-color: #55b776;
                        background-color: #55b776;
                        color: white;
                    }
                    .progressbar_not_used li.active + li:after {
                        background-color: #55b776;
                    }
                    .progressbar li.complete + li:after {
                        background-color: #55b776;
                    }
                `}</style>
      </div>
    );
  }
}

class RegisterSuccessDialog extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {};
  }

  render() {
    return (
      <Modal
        {...this.props}
        size="md"
        aria-labelledby="contained-modal-title-vcenter"
        centered
      >
        <Modal.Header closeButton>
          <Modal.Title id="contained-modal-title-vcenter">
            {this.props.title}
          </Modal.Title>
        </Modal.Header>
        <Modal.Body style={{textAlign: 'center'}}>
          {this.props.children}
        </Modal.Body>
        <Modal.Footer>
          <button className="btn btn-danger" onClick={this.props.onHide}>ปิด</button>
        </Modal.Footer>
      </Modal>
    );
  }
}

class TraineeRegisterForm extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {};
  }

  handleChange(field, e) {
    this.props.handleChangeCallback(field, e);
  }

  setDatePickerMinDate = () => {
    const d = new Date();
    const year = 1900;
    const month = 0;
    const day = 1;
    return new Date(year, month, day)
  };

  render() {
    let {traineeForm, isReadOnly} = this.props;

    return (
      <React.Fragment>
        <div className="row">
          <div className="col">
            <div className="border-inside" style={{marginTop: '10px'}}>
              <div className="row">
                <div className="col-md-9 offset-md-1">
                  <div className="required">
                    <div className="row">
                      <div className="offset-md-3 col-md-9" style={{color: 'red'}}>
                        {isReadOnly ? '* กรุณาตรวจสอบความถูกต้องของข้อมูลที่ท่านกรอก หากต้องการแก้ไขให้กดปุ่ม \'ย้อนกลับ\' ด้านล่าง' : 'ช่องที่มี * คือต้องกรอกข้อมูล'}
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">คำนำหน้าชื่อ</label>
                      </div>
                      <div className="col-md-9">
                        <select value={traineeForm.fields[REGISTER_TRAINEE_TITLE] || '0'}
                                onChange={this.handleChange.bind(this, REGISTER_TRAINEE_TITLE)}
                                className="form-control-2 mt-2"
                                style={{background: isReadOnly ? '#E9EBE2' : '#fff'}}
                                disabled={isReadOnly}>
                          <option value="0" disabled selected>
                            เลือกคำนำหน้า
                          </option>
                          {
                            this.props.nameTitleList.map((nameTitle, index) =>
                              <option key={index} value={nameTitle.title}>{nameTitle.title}</option>
                            )
                          }
                        </select>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_TITLE]}/>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">ชื่อ</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_FIRST_NAME] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_FIRST_NAME)}
                               type="text"
                               placeholder="กรอกชื่อ"
                               className="form-control-2 input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_FIRST_NAME]}/>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">นามสกุล</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_LAST_NAME] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_LAST_NAME)}
                               type="text"
                               placeholder="กรอกนามสกุล"
                               className="form-control-2 input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_LAST_NAME]}/>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="not-required mt-2">วันเกิด</label>
                      </div>
                      <div className="col-md-9">
                        <div style={{width: '47.5%'}} className="mt-2">
                          <DatePicker
                            selected={traineeForm.fields[REGISTER_TRAINEE_BIRTH_DATE_TEMP] || ''}
                            onChange={this.handleChange.bind(this, REGISTER_TRAINEE_BIRTH_DATE_TEMP)}
                            onKeyDown={e => {
                              //if (e.key === ' ') {
                              e.preventDefault();
                              //}
                            }}
                            showMonthDropdown
                            showYearDropdown
                            dropdownMode="select"
                            placeholderText={isReadOnly ? '' : "ระบุวันเกิด"}
                            dateFormat="dd/MM/yyyy"
                            minDate={this.setDatePickerMinDate()}
                            maxDate={new Date()}
                            className="form-control my-react-date-picker"
                            disabled={isReadOnly}/>

                          {/*<input value={traineeForm.fields[REGISTER_TRAINEE_BIRTH_DATE_TEMP] || ''}
                                 onChange={this.handleChange.bind(this, REGISTER_TRAINEE_BIRTH_DATE_TEMP)}
                                 type="number"
                                 placeholder="กรอกอายุ"
                                 className="form-control-2 input-md mt-2"/>*/}

                          <ErrorLabel
                            value={traineeForm.errors[REGISTER_TRAINEE_BIRTH_DATE_TEMP]}/>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">อาชีพ</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_OCCUPATION] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_OCCUPATION)}
                               type="text"
                               placeholder="กรอกอาชีพ"
                               className="form-control-2 input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_OCCUPATION]}/>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="not-required mt-2">สถานที่ทำงาน (ถ้ามี)</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_WORK_PLACE] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_WORK_PLACE)}
                               type="text"
                               placeholder={isReadOnly ? '' : "กรอกสถานทีทำงาน"}
                               className="form-control-2 input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_WORK_PLACE]}/>
                      </div>
                    </div>

                    {!isReadOnly &&
                    <div className="row" style={{marginTop: 20}}>
                      <div className="offset-md-3 col-md-9" style={{color: 'red'}}>
                        กรณีที่ไม่มีข้อมูลที่อยู่ ให้ใส่เครื่องหมาย -
                      </div>
                    </div>
                    }

                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">ที่อยู่ปัจจุบัน</label>
                      </div>
                      <div className="col-md-9">
                        <div className="row">
                          <div className="col-12">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_ADDRESS] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_ADDRESS)}
                                   type="text"
                                   placeholder="บ้านเลขที่ / หมู่ / ซอย / ถนน"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_ADDRESS]}/>
                          </div>
                          {/*<div className="col nopadleft">
                                                        <input id="textinput" name="textinput" type="text" placeholder="หมู่" className="form-control input-md"/>
                                                    </div>
                                                    <div className="w-100"></div>
                                                    <div className="col-12 col-md-6">
                                                        <input id="textinput" name="textinput" type="text" placeholder="ซอย" className="form-control input-md"/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input id="textinput" name="textinput" type="text" placeholder="ถนน" className="form-control input-md"/>
                                                    </div>*/}
                          <div className="w-100"></div>
                          <div className="col-12 col-md-6">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SUB_DISTRICT)}
                                   type="text"
                                   placeholder="แขวง / ตำบล"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_SUB_DISTRICT]}/>
                          </div>
                          <div className="col nopadleft">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_DISTRICT] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_DISTRICT)}
                                   type="text"
                                   placeholder="เขต / อำเภอ"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_DISTRICT]}/>
                          </div>
                          <div className="w-100"></div>
                          <div className="col-12 col-md-6">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_PROVINCE] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PROVINCE)}
                                   type="text"
                                   placeholder="จังหวัด"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_PROVINCE]}/>
                          </div>
                          <div className="col nopadleft">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_POSTAL_CODE)}
                                   type="text"
                                   maxLength={5}
                                   placeholder="รหัสไปรษณีย์"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_POSTAL_CODE]}/>
                          </div>
                          {/*<div className="w-100"></div>
                                                    <div className="col-12 col-md-6">
                                                        <input id="textinput" name="textinput" type="text" placeholder="เบอร์โทรศัพท์" className="form-control input-md"/>
                                                    </div>*/}
                        </div>
                      </div>
                    </div>

                    <div className="row" style={{marginTop: 20}}>
                      <div className="col-md-3">
                        <label className="mt-2">เบอร์โทรศัพท์</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_PHONE] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PHONE)}
                               type="text"
                               placeholder="เบอร์โทรศัพท์"
                               className="form-control-2 input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_PHONE]}/>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="not-required mt-2">อีเมล</label>
                      </div>
                      <div className="col-md-9" style={{border: '0px solid red'}}>
                        <div className="row">
                          <div className="col-md-6">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_EMAIL] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_EMAIL)}
                                   type="email"
                                   onKeyDown={e => {
                                     if (e.key === ' ') {
                                       e.preventDefault();
                                     }
                                   }}
                                   placeholder={isReadOnly ? '' : "อีเมล"}
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_EMAIL]}/>
                          </div>
                          <div className="col-md-6" style={{color: 'red', paddingTop: '20px', paddingBottom: '20px'}}>
                            กรณีที่ไม่มีอีเมล ไม่ต้องใส่ข้อความใดๆ
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label>บุคคลที่สามารถติดต่อได้ในกรณีฉุกเฉิน</label>
                      </div>
                      <div className="col-md-9">
                        <div className="row">
                          <div className="col-12 col-md-6">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_CONTACT_PERSON_NAME] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_CONTACT_PERSON_NAME)}
                                   type="text"
                                   placeholder="ชื่อบุคคลที่สามารถติดต่อได้"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_CONTACT_PERSON_NAME]}/>
                          </div>
                          <div className="col nopadleft">
                            <input value={traineeForm.fields[REGISTER_TRAINEE_CONTACT_PERSON_PHONE] || ''}
                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_CONTACT_PERSON_PHONE)}
                                   type="text"
                                   placeholder="เบอร์โทรศัพท์มือถือ"
                                   className="form-control input-md mt-2"
                                   disabled={isReadOnly}/>
                            <ErrorLabel
                              value={traineeForm.errors[REGISTER_TRAINEE_CONTACT_PERSON_PHONE]}/>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-md-3">
                        <label className="mt-2">โรคประจำตัว</label>
                      </div>
                      <div className="col-md-9">
                        <input value={traineeForm.fields[REGISTER_TRAINEE_DISEASE] || ''}
                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_DISEASE)}
                               type="text"
                               placeholder="กรอกโรคประจำตัว, กรณีไม่มีโรคประจำตัว ให้ใส่เครื่องหมาย -"
                               className="form-control input-md mt-2"
                               disabled={isReadOnly}/>
                        <ErrorLabel
                          value={traineeForm.errors[REGISTER_TRAINEE_DISEASE]}/>
                      </div>
                    </div>
                  </div>
                  <div className="row">
                    <div className="col-md-3">
                      <label className="required-2 mt-2">ทราบข่าวการอบรมจากที่ไหน (ตอบได้มากกว่า 1 ข้อ)</label>
                    </div>
                    <div className="col-md-9 mt-3">
                      <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_WEB]}
                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_WEB)}
                             type="checkbox" id="news-source-web"
                             disabled={isReadOnly}/>
                      <label htmlFor="news-source-web">เว็บไซต์</label><br/>

                      <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_EMAIL]}
                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_EMAIL)}
                             type="checkbox" id="news-source-email"
                             disabled={isReadOnly}/>
                      <label htmlFor="news-source-email">อีเมล</label><br/>

                      <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE]}
                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE)}
                             type="checkbox" id="news-source-brochure"
                             disabled={isReadOnly}/>
                      <label htmlFor="news-source-brochure">แผ่นประชาสัมพันธ์</label><br/>

                      <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_ONLINE]}
                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_ONLINE)}
                             type="checkbox" id="news-source-online"
                             disabled={isReadOnly}/>
                      <label htmlFor="news-source-online">สื่อสังคมออนไลน์ (ไลน์, เฟซบุ๊ค, ฯลฯ)</label><br/>

                      <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_MOUTH]}
                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_MOUTH)}
                             type="checkbox" id="news-source-mouth"
                             disabled={isReadOnly}/>
                      <label htmlFor="news-source-mouth">บอกต่อ</label>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <style jsx>{`
                    .form-control {
                        display: inline-block;
                        width: 100%;
                        margin-top: 0px;
                    }
                    
                    .form-control-2 {
                        display: block;
                        width: 47.5%;
                        padding: 0.375rem 0.75rem;
                        line-height: 1.5;
                        color: #252525;
                        background-clip: padding-box;
                        border: 1px solid #ced4da;
                        margin-top: 0px;
                        border-radius: 0.25rem;
                        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                    }
                    
                    label.input {
                        margin-top: 0px;
                    }
                    
                    @media (max-width: 767px) {
                        .form-control-2{
                            width: 100%;
                        }
                    }
                    
                    /* Base for label styling */
                    
                    [type="checkbox"]:not(:checked),
                    [type="checkbox"]:checked {
                        position: absolute;
                        left: -9999px;
                    }
                    
                    [type="checkbox"]:not(:checked) + label,
                        [type="checkbox"]:checked + label {
                        position: relative;
                        padding-left: 30px;
                        cursor: pointer;
                    }
                    /* checkbox aspect */
                    
                    [type="checkbox"]:not(:checked) + label:before,
                        [type="checkbox"]:checked + label:before {
                        content: '';
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 20px;
                        height: 20px;
                        border: 2px solid #b40303;
                        background: #fff;
                        border-radius: 4px;
                        box-shadow: inset 0 1px 3px rgba(0, 0, 0, .1);
                        margin-top: 5px;
                    }
                    /* checked mark aspect */
                    
                    [type="checkbox"]:not(:checked) + label:after,
                        [type="checkbox"]:checked + label:after {
                        content: '\\2713\\0020';
                        position: absolute;
                        top: 0;
                        left: .22em;
                        font-size: 1em;
                        /*  line-height: 0.8;*/
                        color: #b40303;
                        transition: all .2s;
                    }
                    /* checked mark aspect changes */
                    
                    [type="checkbox"]:not(:checked) + label:after {
                        opacity: 0;
                        transform: scale(0);
                    }
                    
                    [type="checkbox"]:checked + label:after {
                        opacity: 1;
                        transform: scale(1);
                    }
                    /* disabled checkbox */
                    
                    [type="checkbox"]:disabled:not(:checked) + label:before,
                        [type="checkbox"]:disabled:checked + label:before {
                        box-shadow: none;
                        border-color: #bbb;
                        background-color: #ddd;
                    }
                    
                    [type="checkbox"]:disabled:checked + label:after {
                        color: #999;
                    }
                    
                    [type="checkbox"]:disabled + label {
                        color: #aaa;
                    }                    
                `}</style>
      </React.Fragment>
    );
  }
}

export default class ServiceSocialRegister extends React.Component {

  constructor(props, context) {
    super(props, context);

    this.state = {
      step: 1,
      traineeForm: {
        fields: {},
        errors: {}
      },
      receiptForm: {
        fields: {},
        errors: {}
      },
      nameTitleList: [],
      dialog: {
        isOpen: false,
        message: '',
        textColor: '#000',
        onCloseCallback: null,
      },
      registerSuccessDialog: {
        show: false,
        title: null,
      },
      endRegisterFlow: false,
      isSubmitting: false,
    };
  }

  static getInitialProps = async function ({req, query}) {
    let course = null;
    let nameTitleList = null;
    let errorMessage = 'ผิดพลาด';

    if (query.courseId === undefined) {
      return {course, errorMessage};
    } else {
      const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
      const res = await fetch(baseUrl + '/api/get_course', {
        method: 'post',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          serviceType: SERVICE_SOCIAL,
          courseId: query.courseId
        }),
      });
      const result = await res.json();
      if (result['error']['code'] === 0) {
        course = result['dataList'][0];
        //errorMessage = null;

        const resNameTitle = await fetch(baseUrl + '/api/get_name_title', {
          method: 'post'
        });
        const resultNameTitle = await resNameTitle.json();
        if (resultNameTitle['error']['code'] === 0) {
          nameTitleList = resultNameTitle['dataList'];
          errorMessage = null;
        } else {
          nameTitleList = null;
          errorMessage = resultNameTitle['error']['message'];
        }
      } else {
        course = null;
        errorMessage = result['error']['message'];
      }

      return {course, nameTitleList, errorMessage};
    }
  };

  componentDidMount() {
    console.log('ServiceSocialRegister componentDidMount() - ' + Math.random());

    let user = getLoginUser();
    let initialTraineeFields = {};

    if (user !== null) {
      initialTraineeFields[REGISTER_TRAINEE_TITLE] = user.title;
      initialTraineeFields[REGISTER_TRAINEE_FIRST_NAME] = user.firstName;
      initialTraineeFields[REGISTER_TRAINEE_LAST_NAME] = user.lastName;
      initialTraineeFields[REGISTER_TRAINEE_BIRTH_DATE_TEMP] = new Date(user.birthDate);
      initialTraineeFields[REGISTER_TRAINEE_PHONE] = user.phone;
      initialTraineeFields[REGISTER_TRAINEE_EMAIL] = user.email;
    }

    let {traineeForm} = this.state;
    traineeForm.fields = initialTraineeFields;
    this.setState({traineeForm});
  }

  handleChange = (field, e) => {
    let {traineeForm} = this.state;
    let {fields} = traineeForm;
    let {target} = e;

    if (field === REGISTER_TRAINEE_BIRTH_DATE_TEMP) {
      /*let d = e; //new Date();
      let yyyy = d.getFullYear();
      let mm = d.getMonth() + 1;
      let dd = d.getDate();*/
      //alert(`${yyyy}-${mm}-${dd}`);

      fields[field] = e;
    } else if (field === REGISTER_TRAINEE_EMAIL || field === REGISTER_TRAINEE_PROVINCE || field === REGISTER_TRAINEE_POSTAL_CODE) {
      fields[field] = target.value.trim();
      if (field === REGISTER_TRAINEE_EMAIL && fields[field] === '') {
        fields[field] = undefined;
      }
    } else if (field === REGISTER_TRAINEE_WORK_PLACE) {
      fields[field] = target.value;
      if (target.value.trim() === '') {
        fields[field] = undefined;
      }
    } else {
      fields[field] = target.type === 'checkbox' ? target.checked : target.value;
    }
    this.setState({traineeForm}, () => {
      //this.validateForm();
    });
  };

  handleChangeReceipt = (field, e) => {
    let {receiptForm} = this.state;
    let {fields} = receiptForm;

    if (field === REGISTER_RECEIPT_PROVINCE || field === REGISTER_RECEIPT_POSTAL_CODE) {
      fields[field] = e.target.value.trim();
    } else {
      fields[field] = e.target.value;
    }

    this.setState({receiptForm});
  };

  handleSubmit = (event) => {
    event.preventDefault();
    if (this.validateForm()) {
      this.doRegister();
    } else {
      /*this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
          scroller.scrollTo(TOP_OF_FORM, {
              duration: 500,
              smooth: true,
              offset: -80,
          });
          this.dismissDialog();
      });*/
    }
  };

  /*validate ฟอร์ม ผู้สมัครอบรม*/
  validateForm = () => {
    let formIsValid = true;

    let {traineeForm} = this.state;

    let {fields} = traineeForm;
    let errors = {};

    if (!fields[REGISTER_TRAINEE_TITLE]) {
      errors[REGISTER_TRAINEE_TITLE] = 'กรุณาเลือกคำนำหน้า';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_FIRST_NAME] || fields[REGISTER_TRAINEE_FIRST_NAME].trim().length === 0) {
      errors[REGISTER_TRAINEE_FIRST_NAME] = 'กรุณากรอกชื่อ';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_LAST_NAME] || fields[REGISTER_TRAINEE_LAST_NAME].trim().length === 0) {
      errors[REGISTER_TRAINEE_LAST_NAME] = 'กรุณากรอกนามสกุล';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_BIRTH_DATE_TEMP]) {
      //errors[REGISTER_TRAINEE_BIRTH_DATE_TEMP] = 'กรุณาระบุวันเกิด';
      //formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_OCCUPATION] || fields[REGISTER_TRAINEE_OCCUPATION].trim().length === 0) {
      errors[REGISTER_TRAINEE_OCCUPATION] = 'กรุณากรอกอาชีพ';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_WORK_PLACE] || fields[REGISTER_TRAINEE_WORK_PLACE].trim().length === 0) {
      //errors[REGISTER_TRAINEE_WORK_PLACE] = 'กรุณากรอกสถานที่ทำงาน';
      //formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_ADDRESS] || fields[REGISTER_TRAINEE_ADDRESS].trim().length === 0) {
      errors[REGISTER_TRAINEE_ADDRESS] = 'กรุณากรอกบ้านเลขที่ / หมู่ / ซอย / ถนน';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_SUB_DISTRICT] || fields[REGISTER_TRAINEE_SUB_DISTRICT].trim().length === 0) {
      errors[REGISTER_TRAINEE_SUB_DISTRICT] = 'กรุณากรอกแขวง / ตำบล';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_DISTRICT] || fields[REGISTER_TRAINEE_DISTRICT].trim().length === 0) {
      errors[REGISTER_TRAINEE_DISTRICT] = 'กรุณากรอกเขต / อำเภอ';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_PROVINCE] || fields[REGISTER_TRAINEE_PROVINCE].trim().length === 0) {
      errors[REGISTER_TRAINEE_PROVINCE] = 'กรุณากรอกจังหวัด';
      formIsValid = false;
    }

    if (!((fields[REGISTER_TRAINEE_POSTAL_CODE]
      && (fields[REGISTER_TRAINEE_POSTAL_CODE].trim().length === 5)
      && isPositiveInteger(fields[REGISTER_TRAINEE_POSTAL_CODE]))
      || (fields[REGISTER_TRAINEE_POSTAL_CODE]
        && fields[REGISTER_TRAINEE_POSTAL_CODE].trim() === '-'))) {
      errors[REGISTER_TRAINEE_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
      formIsValid = false;
    }

    if (!fields[REGISTER_TRAINEE_PHONE] || fields[REGISTER_TRAINEE_PHONE].trim().length === 0) {
      errors[REGISTER_TRAINEE_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
      formIsValid = false;
    }
    /*if (!fields[REGISTER_TRAINEE_EMAIL] || fields[REGISTER_TRAINEE_EMAIL].trim().length === 0) {
        errors[REGISTER_TRAINEE_EMAIL] = 'กรุณากรอกอีเมล';
        formIsValid = false;
    } else if (!isValidEmail(fields[REGISTER_TRAINEE_EMAIL])) {
        errors[REGISTER_TRAINEE_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
        formIsValid = false;
    }*/
    if (fields[REGISTER_TRAINEE_EMAIL] && !isValidEmail(fields[REGISTER_TRAINEE_EMAIL])) {
      errors[REGISTER_TRAINEE_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_CONTACT_PERSON_NAME] || fields[REGISTER_TRAINEE_CONTACT_PERSON_NAME].trim().length === 0) {
      errors[REGISTER_TRAINEE_CONTACT_PERSON_NAME] = 'กรุณากรอกชื่อบุคคลที่สามารถติดต่อได้';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_CONTACT_PERSON_PHONE] || fields[REGISTER_TRAINEE_CONTACT_PERSON_PHONE].trim().length === 0) {
      errors[REGISTER_TRAINEE_CONTACT_PERSON_PHONE] = 'กรุณากรอกเบอร์มือถือของบุคคลที่สามารถติดต่อได้';
      formIsValid = false;
    }
    if (!fields[REGISTER_TRAINEE_DISEASE] || fields[REGISTER_TRAINEE_DISEASE].trim().length === 0) {
      errors[REGISTER_TRAINEE_DISEASE] = 'กรอกโรคประจำตัว, กรณีไม่มีโรคประจำตัว ให้ใส่เครื่องหมาย -';
      formIsValid = false;
    }

    traineeForm.errors = errors;

    this.setState({
      traineeForm: traineeForm,
    }, () => {
      if (!formIsValid) {
        this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
          scroller.scrollTo(TOP_OF_FORM, {
            duration: 500,
            smooth: true,
            offset: -80,
          });
          this.dismissDialog();
        });
      }
    });

    return formIsValid;
  };

  /*validate ฟอร์ม ข้อมูลการออกใบเสร็จ*/
  validateFormReceipt = () => {
    let valid = true;

    let {receiptForm} = this.state;
    let receiptFields = receiptForm.fields;
    let receiptErrors = {};

    if (!receiptFields[REGISTER_RECEIPT_NAME] || receiptFields[REGISTER_RECEIPT_NAME].trim().length === 0) {
      receiptErrors[REGISTER_RECEIPT_NAME] = 'กรุณากรอกชื่อสำหรับการออกใบเสร็จ';
      valid = false;
    }
    if (!receiptFields[REGISTER_RECEIPT_ADDRESS] || receiptFields[REGISTER_RECEIPT_ADDRESS].trim().length === 0) {
      receiptErrors[REGISTER_RECEIPT_ADDRESS] = 'กรุณากรอกเลขที่ / อาคาร / หมู่ / ซอย / ถนน';
      valid = false;
    }
    if (!receiptFields[REGISTER_RECEIPT_SUB_DISTRICT] || receiptFields[REGISTER_RECEIPT_SUB_DISTRICT].trim().length === 0) {
      receiptErrors[REGISTER_RECEIPT_SUB_DISTRICT] = 'กรุณากรอกแขวง / ตำบล';
      valid = false;
    }
    if (!receiptFields[REGISTER_RECEIPT_DISTRICT] || receiptFields[REGISTER_RECEIPT_DISTRICT].trim().length === 0) {
      receiptErrors[REGISTER_RECEIPT_DISTRICT] = 'กรุณากรอกเขต / อำเภอ';
      valid = false;
    }
    if (!receiptFields[REGISTER_RECEIPT_PROVINCE] || receiptFields[REGISTER_RECEIPT_PROVINCE].trim().length === 0) {
      receiptErrors[REGISTER_RECEIPT_PROVINCE] = 'กรุณากรอกจังหวัด';
      valid = false;
    }
    if (!receiptFields[REGISTER_RECEIPT_POSTAL_CODE]
      || receiptFields[REGISTER_RECEIPT_POSTAL_CODE].trim().length !== 5
      || !isPositiveInteger(receiptFields[REGISTER_RECEIPT_POSTAL_CODE])) {
      receiptErrors[REGISTER_RECEIPT_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
      valid = false;
    }
    /*if (!receiptFields[REGISTER_RECEIPT_ORGANIZATION_PHONE] || receiptFields[REGISTER_RECEIPT_ORGANIZATION_PHONE].trim().length === 0) {
        receiptErrors[REGISTER_RECEIPT_ORGANIZATION_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์หน่วยงาน';
        valid = false;
    }*/
    /*if (!receiptFields[REGISTER_RECEIPT_TAX_ID] || receiptFields[REGISTER_RECEIPT_TAX_ID].trim().length === 0) {
        receiptErrors[REGISTER_RECEIPT_TAX_ID] = 'กรุณากรอกเลขประจำตัวผู้เสียภาษี';
        valid = false;
    }*/
    receiptForm.errors = receiptErrors;

    this.setState({
      receiptForm: receiptForm,
    }, () => {
      if (!valid) {
        this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
          scroller.scrollTo(TOP_OF_FORM, {
            duration: 500,
            smooth: true,
            offset: -80,
          });
          this.dismissDialog();
        });
      }
    });

    return valid;
  };

  doRegister = () => {
    const user = getLoginUser();
    const loginToken = user === null ? null : getLoginUser().loginToken;
    const {traineeForm, receiptForm} = this.state;
    const traineeData = traineeForm.fields;
    const receipt = receiptForm.fields;

    if (traineeData[REGISTER_TRAINEE_BIRTH_DATE_TEMP]) {
      const dateString = getDateFormatFromDateObject(traineeData[REGISTER_TRAINEE_BIRTH_DATE_TEMP]);
      traineeData[REGISTER_TRAINEE_BIRTH_DATE] = dateString;
    }

    this.setState({
      isSubmitting: true,
    });

    fetch('/api/register_course_social', {
      method: 'post',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        loginToken,
        courseId: this.props.course.id,
        traineeData,
        receipt,
      }),
    })
      .then(result => result.json())
      .then(result => {
        const msg = result['error']['message'];
        if (result['error']['code'] === 0) {
          const courseRegId = result['courseRegId'];
          const registerSuccessDialog = {
            show: true,
            title: 'ลงทะเบียนสำเร็จ'
          };
          this.setState({
            isSubmitting: false,
            courseRegId,
            registerSuccessDialog,
          });

          /*alert(msg);
          window.open(
            `${HOST_BACKEND}/pages/print_ac_registration_form.php?service_type=${SERVICE_SOCIAL}&trainee_id=${courseRegId}&payment=1&user=1`,
            '_blank'
          );
          Router.back();*/

          /*this.showDialog(msg, "success", () => {
              this.dismissDialog();
              Router.back();
          });*/

          /*const courseRegId = result['courseRegId'];
          this.setState({
              courseRegId,
              endRegisterFlow: true,
          });*/

          /*const courseRegId = result['courseRegId'];
          const registerSuccessDialog = {
              show: true,
              title: 'ลงทะเบียนสำเร็จ'
          };
          this.setState({
              courseRegId,
              registerSuccessDialog,
          });*/
        } else {
          this.setState({
            isSubmitting: false,
          });
          alert(msg);
          /*this.showDialog(msg, "error", () => {
              this.dismissDialog();
          });*/
        }
      })
      .catch(err => {
        this.setState({
          isSubmitting: false,
        });
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server: ' + err);
        /*this.showDialog('เกิดข้อผิดพลาดในการเชื่อมต่อ Server', "error", () => {
            this.dismissDialog();
        });*/
      });
  };

  showDialog = (message, textColor, onCloseCallback) => {
    const dialog = {
      isOpen: true,
      message, textColor,
      onCloseCallback
    };
    this.setState({dialog});
  };

  dismissDialog = () => {
    const dialog = {
      isOpen: false,
      message: '',
      textColor: '#000',
      onCloseCallback: null,
    };
    this.setState({dialog});
  };

  onClickPrevious = () => {
    const {step} = this.state;
    const {applicationFee} = this.props.course;

    if (step === 3 && (applicationFee == null || applicationFee === 0)) {
      this.setState({
        step: 1
      }, () => {
        scroller.scrollTo(TOP_OF_FORM, {
          duration: 500,
          smooth: true,
          offset: -80,
        });
      });
    } else {
      this.setState({
        step: step - 1
      }, () => {
        scroller.scrollTo(TOP_OF_FORM, {
          duration: 500,
          smooth: true,
          offset: -80,
        });
      });
    }
  };

  onClickNext = () => {
    const {step, traineeForm, receiptForm} = this.state;
    const {applicationFee} = this.props.course;
    let valid = true;

    switch (step) {
      case 1:
        valid = this.validateForm();
        break;
      case 2:
        valid = this.validateFormReceipt();
        break;
    }
    if (valid) {
      if (step === 1 && (applicationFee == null || applicationFee === 0)) {
        this.setState({
          step: 3
        }, () => {
          scroller.scrollTo(TOP_OF_FORM, {
            duration: 500,
            smooth: true,
            offset: -80,
          });
        });
      } else {
        if (step === 1) {
          receiptForm.fields = {
            registerReceiptName: `${traineeForm.fields[REGISTER_TRAINEE_FIRST_NAME]} ${traineeForm.fields[REGISTER_TRAINEE_LAST_NAME]}`,
            registerReceiptAddress: traineeForm.fields[REGISTER_TRAINEE_ADDRESS],
            registerReceiptSubDistrict: traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT],
            registerReceiptDistrict: traineeForm.fields[REGISTER_TRAINEE_DISTRICT],
            registerReceiptProvince: traineeForm.fields[REGISTER_TRAINEE_PROVINCE],
            registerReceiptPostalCode: traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE],
          };
          this.setState({
            receiptForm
          });
        }

        this.setState({
          step: step + 1
        }, () => {
          scroller.scrollTo(TOP_OF_FORM, {
            duration: 500,
            smooth: true,
            offset: -80,
          });
        });
      }
    } else {
      //alert('กรุณากรอกข้อมูลในหน้านี้ให้ครบถ้วน');
    }
  };

  test = (courseRegId) => {
    const registerSuccessDialog = {
      show: true,
      title: 'ทดสอบ'
    };
    this.setState({
      courseRegId,
      registerSuccessDialog,
    });
  };

  render() {
    let {traineeForm, receiptForm, step, dialog} = this.state;

    return (
      <MainLayout>
        <NextHead>
          <style>{'body_ { background-color: red; }'}</style>
        </NextHead>

        {
          this.props.course &&
          <div className="container">
            <div className="row">
              <div className="col">
                <p style={{fontSize: '1.3rem', lineHeight: '30px', textAlign: 'center'}}>โครงการบริการสังคม สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>
                <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '5px', width: '100%'}}>
                  แบบฟอร์มลงทะเบียนอบรม
                </h3>
              </div>
            </div>
            {/*ชื่อหลักสูตร, วันที่อบรม, สถานที่อบรม*/}
            <div className="row">
              <div className="col">
                <div className="border-project">
                  <div className="row">
                    <div className="col-12">
                      <div className="row">
                        <div className="col-sm-3 text-black ">ชื่อหลักสูตร / รุ่นที่</div>
                        <div className="col-sm-9 text-gray">
                          {this.props.course.name}&nbsp;&nbsp;
                          {this.props.course.isOnline === 1 &&
                          <span style={{display: 'inline-block'}}>
                            <small style={{color: 'white', backgroundColor: '#b50303', padding: '2px 6px', borderRadius: 3}}>อบรมออนไลน์</small>
                          </span>
                          }
                        </div>
                      </div>
                      <div className="row">
                        <div className="col-sm-3 text-black ">วันที่อบรม</div>
                        <div className="col-sm-9 text-gray">{formatCourseDateLong(this.props.course.beginDate, this.props.course.endDate)}</div>
                      </div>
                      {/*<div className="row">
                        <div className="col-sm-3 text-black ">สถานที่อบรม</div>
                        <div className="col-sm-9 text-gray">{this.props.course.place}</div>
                      </div>*/}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {this.state.endRegisterFlow &&
            <div style={{textAlign: 'center', fontSize: '1.5rem', marginTop: '50px', color: '#008000'}}>
              ลงทะเบียนสำเร็จ<br/>
              <a className="btn btn-dark"
                 style={{marginTop: '10px', marginRight: 0, padding: '5px 20px'}}
                 href={`${HOST_BACKEND}/pages/print_ac_registration_form.php?service_type=${SERVICE_SOCIAL}&trainee_id=${this.state.courseRegId}&payment=1&user=1`}
                 target="_blank">
                {this.props.course.applicationFee > 0 ? 'ใบสมัคร/รายละเอียดการชำระเงิน' : 'ใบสมัคร'}
              </a>
            </div>
            }

            <Element name={TOP_OF_FORM}>
              {this.props.course.applicationFee && (this.props.course.applicationFee > 0) &&
              !this.state.endRegisterFlow &&
              <RegisterProgress
                text={[
                  'ข้อมูลผู้สมัคร', 'ข้อมูลการออกใบเสร็จ', 'ตรวจสอบข้อมูล'
                ]}
                activeStep={step}
              />
              }
              {(!this.props.course.applicationFee || this.props.course.applicationFee === 0) &&
              !this.state.endRegisterFlow &&
              <RegisterProgress2Step
                text={[
                  'ข้อมูลผู้สมัคร', 'ตรวจสอบข้อมูล'
                ]}
                activeStep={step}
              />
              }
              <br/>
            </Element>

            {/*ฟอร์มสมัครอบรม*/}
            <form id="applicationFormGroup" method="post" noValidate={true} onSubmit={this.handleSubmit}>
              {/*ขั้นตอน 1 กรอกข้อมูลผู้สมัครอบรม*/}
              {((step === 1) || (step === 3)) &&
              !this.state.endRegisterFlow &&
              <div style={{marginTop: '15px'}}>
                {/*หัวข้อ*/}
                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                  <div className="col">
                    <h4 className="text-black" style={{marginTop: '20px'}}>
                      <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัคร&nbsp;
                    </h4>
                  </div>
                </div>

                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                <TraineeRegisterForm
                  traineeForm={traineeForm}
                  isReadOnly={step === 3}
                  nameTitleList={this.props.nameTitleList}
                  handleChangeCallback={this.handleChange}
                />
              </div>
              }

              {/*ขั้นตอน 2 กรอกข้อมูลการออกใบเสร็จ*/}
              {((step === 2) || (step === 3 && (this.props.course.applicationFee != null && this.props.course.applicationFee > 0))) &&
              !this.state.endRegisterFlow &&
              <div>
                {/*ฟอร์มข้อมูลการออกใบเสร็จ*/}
                <div className="row" style={{border: '0 solid red', clear: 'both'}}>
                  <div className="col">
                    <Element name={'receipt-form'}>
                      <h4 className="text-black" style={{marginTop: '20px'}}>
                        ข้อมูลการออกใบเสร็จ
                      </h4>
                    </Element>
                  </div>
                </div>
                <div className="row">
                  <div className="col">
                    <div className="border-inside" style={{marginTop: '10px'}}>
                      <div className="regisfo">
                        <div className="row">
                          <div className="col-md-12">

                            <div className="row">
                              <div className="col-12 offset-md-2 col-md-9" style={{color: 'red'}}>
                                {step === 2 ? 'ต้องกรอกข้อมูลทุกช่อง' : '* กรุณาตรวจสอบความถูกต้องของข้อมูลที่ท่านกรอก หากต้องการแก้ไขให้กดปุ่ม \'ย้อนกลับ\' ด้านล่าง'}
                              </div>
                            </div>

                            <div className="row">
                              <div className="col-12 col-md-2" style={{paddingRight: 0}}>
                                <label className="label required-label">ชื่อสำหรับออกใบเสร็จ</label>
                              </div>
                              <div className="col-12 col-md-9">
                                <input value={receiptForm.fields[REGISTER_RECEIPT_NAME] || ''}
                                       onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_NAME)}
                                       type="text"
                                       placeholder="ชื่อ"
                                       className="form-control input-md"
                                       disabled={step === 3}/>
                                <ErrorLabel
                                  value={receiptForm.errors[REGISTER_RECEIPT_NAME]}/>
                              </div>
                            </div>

                            <div className="row mt-2">
                              <div className="col-12 col-md-2" style={{paddingRight: 0}}>
                                <label className="label required-label">ที่อยู่</label>
                              </div>
                              <div className="col-12 col-md-9">
                                <div className="row">
                                  <div className="col-12 col-md-12">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_ADDRESS] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_ADDRESS)}
                                           type="text"
                                           placeholder="เลขที่ / อาคาร / หมู่ / ซอย / ถนน"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                      value={receiptForm.errors[REGISTER_RECEIPT_ADDRESS]}/>
                                  </div>
                                  <div className="w-100"></div>
                                  <div className="col-12 col-md-6">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_SUB_DISTRICT] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_SUB_DISTRICT)}
                                           type="text"
                                           placeholder="แขวง / ตำบล"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                      value={receiptForm.errors[REGISTER_RECEIPT_SUB_DISTRICT]}/>
                                  </div>
                                  <div className="col-12 col-md-6 nopadleft">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_DISTRICT] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_DISTRICT)}
                                           type="text"
                                           placeholder="เขต / อำเภอ"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                      value={receiptForm.errors[REGISTER_RECEIPT_DISTRICT]}/>
                                  </div>
                                  <div className="w-100"></div>
                                  <div className="col-12 col-md-6">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_PROVINCE] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_PROVINCE)}
                                           type="text" placeholder="จังหวัด"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                      value={receiptForm.errors[REGISTER_RECEIPT_PROVINCE]}/>
                                  </div>
                                  <div className="col-12 col-md-6 nopadleft">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_POSTAL_CODE] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_POSTAL_CODE)}
                                           type="text"
                                           maxLength={5}
                                           placeholder="รหัสไปรษณีย์"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                      value={receiptForm.errors[REGISTER_RECEIPT_POSTAL_CODE]}/>
                                  </div>
                                  {/*<div className="w-100"></div>
                                      <div className="col-12 col-md-6">
                                          <input value={receiptForm.fields[REGISTER_RECEIPT_ORGANIZATION_PHONE] || ''}
                                                 onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_ORGANIZATION_PHONE)}
                                                 type="text"
                                                 placeholder="เบอร์โทรศัพท์หน่วยงาน"
                                                 className="form-control input-md"
                                                 disabled={step === 3}/>
                                          <ErrorLabel
                                              value={receiptForm.errors[REGISTER_RECEIPT_ORGANIZATION_PHONE]}/>
                                      </div>
                                      <div className="col-12 col-md-6 nopadleft">
                                      </div>*/}
                                </div>
                              </div>
                            </div>

                            {/*<div className="row mt-2 mb-1">
                                <div className="col-12 col-md-2" style={{paddingRight: 0}}>
                                    <label className="label required-label">เลขประจำตัวผู้เสียภาษี</label>
                                </div>
                                <div className="col-12 col-md-9">
                                    <input value={receiptForm.fields[REGISTER_RECEIPT_TAX_ID] || ''}
                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_TAX_ID)}
                                           type="text"
                                           placeholder="เลขประจำตัวผู้เสียภาษี"
                                           className="form-control input-md"
                                           disabled={step === 3}/>
                                    <ErrorLabel
                                        value={receiptForm.errors[REGISTER_RECEIPT_TAX_ID]}/>
                                </div>
                            </div>*/}

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              }

              {/*ปุ่มลงทะเบียน*/}
              {step === 3 &&
              !this.state.endRegisterFlow &&
              <div className="row mt-3 mb-3">
                <div className="col">
                  <div className="btn-red-submit">
                    {/*<button type="button" value="submit" className="btn btn-danger" onClick={this.handleSubmit}>
                      ยืนยันลงทะเบียนอบรม
                    </button>*/}

                    {this.state.isSubmitting &&
                    <div className="spinner-border text-dark" role="status">
                      <span className="sr-only">Loading...</span>
                    </div>
                    }
                    {!this.state.isSubmitting &&
                    <button type="submit" value="submit" className="btn btn-danger">
                      ยืนยันลงทะเบียนอบรม
                    </button>
                    }
                  </div>
                </div>
              </div>
              }

              {/*ปุ่มย้อนกลับ/ถัดไป*/}
              <div className="container mt-4 mb-4" style={{marginBottom: '0px'}}>
                <div className="row">
                  <div className="col-6 nopad">
                    <div className="text-left">
                      {step !== 1 &&
                      !this.state.endRegisterFlow &&
                      <button type="button" className="btn btn-normal" onClick={this.onClickPrevious}>
                        <i className="fas fa-arrow-left" style={{fontSize: '0.8rem'}}/>&nbsp;&nbsp;&nbsp;ย้อนกลับ
                      </button>
                      }
                    </div>
                  </div>
                  <div className="col-6 nopad">
                    <div className="text-right">
                      {step !== 3 &&
                      !this.state.endRegisterFlow &&
                      <button type="button" className="btn btn-normal" onClick={this.onClickNext}>
                        &nbsp;ถัดไป&nbsp;&nbsp;&nbsp;<i className="fas fa-arrow-right" style={{fontSize: '0.8rem'}}/>&nbsp;
                      </button>
                      }
                    </div>
                  </div>
                </div>
              </div>

              {/*<div className="btn-red-submit" style={{marginTop: '20px'}}>
                                <button type="submit" className="btn btn-danger">ลงทะเบียน</button>
                            </div>*/}

            </form>

          </div>
        }
        {
          !this.props.course &&
          <div style={{textAlign: 'center', color: 'red'}}>{this.props.errorMessage}</div>
        }

        <Dialog message={dialog.message}
                textColor={dialog.textColor}
                isOpen={dialog.isOpen}
                onCloseCallback={dialog.onCloseCallback}/>

        <RegisterSuccessDialog
          title={this.state.registerSuccessDialog.title}
          show={this.state.registerSuccessDialog.show}
          onHide={() => {
            const registerSuccessDialog = {
              show: false,
            };
            this.setState({registerSuccessDialog}, () => {
              Router.back();
            });
          }}>
          <div>
            <div style={{marginBottom: 15}}>ท่านได้ทำการลงทะเบียนอบรมเรียบร้อยแล้ว</div>
            <a className="btn btn-dark"
               style={{padding: '5px 20px'}}
               href={`${HOST_BACKEND}/pages/print_ac_registration_form.php?service_type=${SERVICE_SOCIAL}&trainee_id=${this.state.courseRegId}&payment=1&user=1`}
               target="_blank">
              {this.props.course.applicationFee > 0 ? 'พิมพ์ใบสมัคร / รายละเอียดการชำระเงิน' : 'พิมพ์ใบสมัคร'}
            </a>
          </div>
        </RegisterSuccessDialog>

        <style jsx>{`
                    
                `}</style>
      </MainLayout>
    );
  }
}
