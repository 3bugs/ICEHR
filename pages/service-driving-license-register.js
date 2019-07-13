import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail, isValidPid, isPositiveInteger} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {SERVICE_DRIVING_LICENSE, SERVICE_SOCIAL} from "../etc/constants";
import Dialog from "../components/Dialog";
import InputMask from 'react-input-mask';

const TOP_OF_FORM = 'topOfForm';

const REGISTER_TRAINEE_TITLE = 'traineeTitle';
const REGISTER_TRAINEE_FIRST_NAME = 'traineeFirstName';
const REGISTER_TRAINEE_LAST_NAME = 'traineeLastName';
const REGISTER_TRAINEE_PID = 'traineePid';
const REGISTER_TRAINEE_ADDRESS = 'traineeAddress';
const REGISTER_TRAINEE_MOO = 'traineeMoo';
const REGISTER_TRAINEE_SOI = 'traineeSoi';
const REGISTER_TRAINEE_ROAD = 'traineeRoad';
const REGISTER_TRAINEE_SUB_DISTRICT = 'traineeSubDistrict';
const REGISTER_TRAINEE_DISTRICT = 'traineeDistrict';
const REGISTER_TRAINEE_PROVINCE = 'traineeProvince';
const REGISTER_TRAINEE_POSTAL_CODE = 'traineePostalCode';
const REGISTER_TRAINEE_PHONE = 'traineePhone';
const REGISTER_TRAINEE_IMAGE_FILE_PID = 'traineeImageFilePid';
const REGISTER_TRAINEE_IMAGE_FILE_PASSPORT = 'traineeImageFilePassport';
const REGISTER_TRAINEE_IMAGE_FILE_WORK_PERMIT = 'traineeImageFileWorkPermit';
const REGISTER_TRAINEE_SELECTED_COURSE_TYPE = 'traineeSelectedCourseType';
const REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR = 'traineeSelectedLicenseTypeCar';
const REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE = 'traineeSelectedLicenseTypeBicycle';
const REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE = 'traineeSelectedLicenseTypeTricycle';

class TraineeRegisterForm extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    handleChange(field, e) {
        this.props.handleChangeCallback(field, e);
    }

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
                                            <div className="col-md-3">
                                                <label className="mt-2">คำนำหน้าชื่อ</label>
                                            </div>
                                            <div className="col-md-9">
                                                <select value={traineeForm.fields[REGISTER_TRAINEE_TITLE] || '0'}
                                                        onChange={this.handleChange.bind(this, REGISTER_TRAINEE_TITLE)}
                                                        className="form-control-2 mt-2">
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
                                                       className="form-control-2 input-md mt-2"/>
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
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_LAST_NAME]}/>
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-md-3">
                                                <label className="mt-2">เลขประจำตัวประชาชน</label>
                                            </div>
                                            <div className="col-md-9">
                                                {/*<InputMask mask="9-9999-99999-99-9"
                                                           value={traineeForm.fields[REGISTER_TRAINEE_PID] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PID)}
                                                           maxLength={13}
                                                           placeholder="กรอกเลขประจำตัวประชาชน 13 หลัก"
                                                           className="form-control-2 input-md mt-2"/>*/}
                                                <input value={traineeForm.fields[REGISTER_TRAINEE_PID] || ''}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PID)}
                                                       type="text"
                                                       maxLength={13}
                                                       placeholder="กรอกเลขประจำตัวประชาชน 13 หลัก"
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_PID]}/>
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-md-3">
                                                <label className="mt-2">ที่อยู่ตามบัตรประชาชน<br/>(ข้อมูลใดไม่มี ให้กรอก -)</label>
                                            </div>
                                            <div className="col-md-9">
                                                <div className="row">
                                                    <div className="col-12 col-md-6">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_ADDRESS] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_ADDRESS)}
                                                               type="text"
                                                               placeholder="บ้านเลขที่"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_ADDRESS]}/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_MOO] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_MOO)}
                                                               type="text"
                                                               placeholder="หมู่"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_MOO]}/>
                                                    </div>
                                                    <div className="w-100"></div>

                                                    <div className="col-12 col-md-6">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_SOI] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SOI)}
                                                               type="text"
                                                               placeholder="ซอย"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_SOI]}/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_ROAD] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_ROAD)}
                                                               type="text"
                                                               placeholder="ถนน"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_ROAD]}/>
                                                    </div>
                                                    <div className="w-100"></div>

                                                    <div className="col-12 col-md-6">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SUB_DISTRICT)}
                                                               type="text"
                                                               placeholder="แขวง / ตำบล"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_SUB_DISTRICT]}/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_DISTRICT] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_DISTRICT)}
                                                               type="text"
                                                               placeholder="เขต / อำเภอ"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_DISTRICT]}/>
                                                    </div>
                                                    <div className="w-100"></div>

                                                    <div className="col-12 col-md-6">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_PROVINCE] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PROVINCE)}
                                                               type="text"
                                                               placeholder="จังหวัด"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_PROVINCE]}/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_POSTAL_CODE)}
                                                               type="text"
                                                               maxLength={5}
                                                               placeholder="รหัสไปรษณีย์"
                                                               className="form-control input-md mt-2"/>
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
                                        <div className="row">
                                            <div className="col-md-3">
                                                <label className="mt-2">เบอร์โทรศัพท์</label>
                                            </div>
                                            <div className="col-md-9">
                                                <input value={traineeForm.fields[REGISTER_TRAINEE_PHONE] || ''}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PHONE)}
                                                       type="text"
                                                       placeholder="เบอร์โทรศัพท์"
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_PHONE]}/>
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-md-3">
                                                <label className="mt-2">รูปภาพสำเนาบัตรประชาชน (สำหรับคนไทย) <br/>หรือสำเนาหนังสือเดินทาง (สำหรับชาวต่างชาติ)</label>
                                            </div>
                                            <div className="col-md-9">
                                                {/*<form method="post" action="#" id="#">*/}
                                                <div className="form-group files" style={{margin: 0}}>
                                                    <input type="file" name="file"
                                                           accept="image/*"
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_IMAGE_FILE_PID)}
                                                           className="form-control mt-2" multiple=""/>
                                                    {traineeForm.fields[REGISTER_TRAINEE_IMAGE_FILE_PID] &&
                                                    <img src={traineeForm.fields[REGISTER_TRAINEE_IMAGE_FILE_PID] ? URL.createObjectURL(traineeForm.fields[REGISTER_TRAINEE_IMAGE_FILE_PID]) : null}
                                                         style={{position: 'absolute', width: '100px', top: '20px', left: '20px'}}/>
                                                    }
                                                </div>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_IMAGE_FILE_PID]}/>
                                                {/*</form>*/}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col">
                                <div className="bg-gray">
                                    <h2>มีความประสงค์ขอสมัครเข้ารับการอบรมภาคทฤษฎีในหลักสูตร ดังนี้</h2>
                                    <ErrorLabel
                                        value={traineeForm.errors[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]}/>
                                    <div className="check-box-2"
                                         onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_COURSE_TYPE)}>
                                        <div className="md-radio md-radio-inline radiocheck">
                                            <input id="course-type-5-hours" name="course-type" type="radio"
                                                   value={1}
                                                   defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 1}/>
                                            <label htmlFor="course-type-5-hours"> {this.props.courseTypeList[0].title}</label>
                                        </div>
                                        <br/>
                                        <div className="md-radio md-radio-inline radiocheck">
                                            <input id="course-type-2-hours" name="course-type" type="radio"
                                                   value={2}
                                                   defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 2}/>
                                            <label htmlFor="course-type-2-hours"> {this.props.courseTypeList[1].title}</label>
                                        </div>
                                        <br/>
                                        <div className="md-radio md-radio-inline radiocheck">
                                            <input id="course-type-1-hour" name="course-type" type="radio"
                                                   value={3}
                                                   defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 3}/>
                                            <label htmlFor="course-type-1-hour"> {this.props.courseTypeList[2].title}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <div className="title-sm">
                                    <h2>ประเภทใบอนุญาตขับรถ</h2>
                                    <ErrorLabel
                                        value={traineeForm.errors[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]}/>
                                    <div className="row">
                                        <div className="col-12 col-md-4">
                                            <input type="checkbox" id="license-type-car"
                                                   checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR)}/>
                                            <label htmlFor="license-type-car">รถยนต์ส่วนบุคคลชั่วคราว</label>
                                        </div>
                                        <div className="col-12 col-md-4">
                                            <input type="checkbox" id="license-type-bicycle"
                                                   checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE)}/>
                                            <label htmlFor="license-type-bicycle">รถจักรยานยนต์ส่วนบุคคลชั่วคราว</label>
                                        </div>
                                        <div className="col-12 col-md-4">
                                            <input type="checkbox" id="license-type-tricycle"
                                                   checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE)}/>
                                            <label htmlFor="license-type-tricycle">รถสามล้อส่วนบุคคลชั่วคราว</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="border-red"></div>

                        <div className="btn-red-submit mb-4 mt-4" style={{_marginTop: '20px'}}>
                            <button type="submit" className="btn btn-danger">ลงทะเบียน</button>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .files input {
                        outline: 2px dashed #92b0b3;
                        outline-offset: -10px;
                        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
                        transition: outline-offset .15s ease-in-out, background-color .15s linear;
                        padding: 120px 0 85px 40%;
                        text-align: center !important;
                        margin: 0;
                        width: 100% !important;
                    }
                    .files input:focus {     
                        outline: 2px dashed #92b0b3;  
                        outline-offset: -10px;
                        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
                        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
                     }
                    .files { position: relative }
                    .files:after {  
                        pointer-events: none;
                        position: absolute;
                        top: 40px;
                        left: 0;
                        width: 50px;
                        right: 0;
                        height: 56px;
                        content: "";
                        background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
                        display: block;
                        margin: 0 auto;
                        background-size: 100%;
                        background-repeat: no-repeat;
                    }
                    .color input{ background-color:#f1f1f1;}
                    .files:before {
                        position: absolute;
                        bottom: 10px;
                        left: 0;  
                        pointer-events: none;
                        width: 100%;
                        right: 0;
                        height: 57px;
                        content: " หรือลากไฟล์มาปล่อยที่นี่ ";
                        display: block;
                        margin: 0 auto;
                        color: #2ea591;
                        text-transform: capitalize;
                        text-align: center;
                    }
                
                    input[type="file"] {
                    }
                    .custom-file-upload {
                        border: 1px solid #ccc;
                        display: inline-block;
                        padding: 6px 12px;
                        cursor: pointer;
                    }
                    .custom-file-upload:hover {
                        background: #f4f4f4;
                    }

                    .form-control {
                        display: inline-block;
                        width: 100%;
                        margin-top: 0px;
                    }
                    
                    .form-control-2 {
                        display: block;
                        width: 48.5%;
                        padding: 0.375rem 0.75rem;
                        line-height: 1.5;
                        color: #252525;
                        background-color: #fff;
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

export default class ServiceDrivingLicenseRegister extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            step: 1,
            traineeForm: {
                fields: {},
                errors: {}
            },
            nameTitleList: [],
            courseTypeList: null,
            errorMessage: null,
            dialog: {
                isOpen: false,
                message: '',
                textColor: '#000',
                onCloseCallback: null,
            },
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
                    serviceType: SERVICE_DRIVING_LICENSE,
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
            initialTraineeFields[REGISTER_TRAINEE_PHONE] = user.phone;
        }

        let {traineeForm} = this.state;
        traineeForm.fields = initialTraineeFields;
        this.setState({traineeForm});

        this.getCourseTypeList();
    }

    getCourseTypeList() {
        fetch('/api/get_driving_license_course_type', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            /*body: JSON.stringify({
                serviceType: SERVICE_DRIVING_LICENSE,
                month, year
            }),*/
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        courseTypeList: result['dataList'],
                        errorMessage: null,
                    });
                } else {
                    this.setState({
                        courseTypeList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            });
    }

    handleChange = (field, e) => {
        let {traineeForm} = this.state;
        let {fields} = traineeForm;
        let {target} = e;

        if (field === REGISTER_TRAINEE_PID || field === REGISTER_TRAINEE_PROVINCE || field === REGISTER_TRAINEE_POSTAL_CODE) {
            fields[field] = target.value.trim();
        } else {
            if (target.type === 'checkbox') {
                if (target.checked
                    && (fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === '2' || fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === '3')
                    && (fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]
                        || fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE]
                        || fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE])) {
                    alert('การอบรมสำหรับผู้ขอ \'ต่อ\' ใบอนุญาต สามารถเลือกประเภทใบอนุญาตได้ 1 ประเภทเท่านั้น\n\nหมายเหตุ: ถ้าหากต้องการใบอนุญาตมากกว่า 1 ประเภท จะต้องกรอกข้อมูลลงทะเบียนอบรมใหม่อีกครั้ง');
                    fields[field] = false;
                } else {
                    fields[field] = target.checked;
                }
            } else if (target.type === 'file') {
                fields[field] = target.files[0];
            } else {
                fields[field] = target.value;
            }
        }
        this.setState({traineeForm}, () => {
            //this.validateForm();
        });
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (this.validateForm()) {
            this.doRegister();
        } else {
            /*const {errors} = this.state.traineeForm;
            let errorListText = 'กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง\n\n';
            Object.keys(errors).forEach(function(key, index) {
                // key: the name of the object key
                // index: the ordinal position of the key within the object
                errorListText += errors[key] + '\n';
            });*/

            this.showDialog('กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง', "error", () => {
                scroller.scrollTo(TOP_OF_FORM, {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                    //delay: 100,
                    //containerId: 'ContainerElementID',
                    //offset: 50, // Scrolls to element + 50 pixels down the page
                });
                this.dismissDialog();
            });
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
        if (!fields[REGISTER_TRAINEE_PID] || fields[REGISTER_TRAINEE_PID].trim().length !== 13) {
            errors[REGISTER_TRAINEE_PID] = 'กรุณากรอกเลขประจำตัวประชาชน 13 หลัก';
            formIsValid = false;
        } else if (!isValidPid(fields[REGISTER_TRAINEE_PID].trim())) {
            errors[REGISTER_TRAINEE_PID] = 'เลขประจำตัวประชาชนไม่ถูกต้อง';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_ADDRESS] || fields[REGISTER_TRAINEE_ADDRESS].trim().length === 0) {
            errors[REGISTER_TRAINEE_ADDRESS] = 'กรุณากรอกบ้านเลขที่';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_MOO] || fields[REGISTER_TRAINEE_MOO].trim().length === 0) {
            errors[REGISTER_TRAINEE_MOO] = 'กรุณากรอกหมู่';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_SOI] || fields[REGISTER_TRAINEE_SOI].trim().length === 0) {
            errors[REGISTER_TRAINEE_SOI] = 'กรุณากรอกซอย';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_ROAD] || fields[REGISTER_TRAINEE_ROAD].trim().length === 0) {
            errors[REGISTER_TRAINEE_ROAD] = 'กรุณากรอกถนน';
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
        if (!fields[REGISTER_TRAINEE_POSTAL_CODE]
            || fields[REGISTER_TRAINEE_POSTAL_CODE].trim().length !== 5
            || !isPositiveInteger(fields[REGISTER_TRAINEE_POSTAL_CODE])) {
            errors[REGISTER_TRAINEE_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_PHONE] || fields[REGISTER_TRAINEE_PHONE].trim().length === 0) {
            errors[REGISTER_TRAINEE_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_IMAGE_FILE_PID]) {
            errors[REGISTER_TRAINEE_IMAGE_FILE_PID] = 'กรุณาใส่รูปภาพสำเนาบัตรประชาชน (สำหรับคนไทย) หรือสำเนาหนังสือเดินทาง (สำหรับชาวต่างชาติ)';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]) {
            errors[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] = 'กรุณาเลือกประเภทหลักสูตร';
            formIsValid = false;
        }

        const fieldLicenseTypeCar = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR];
        const fieldLicenseTypeBicycle = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE];
        const fieldLicenseTypeTricycle = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE];

        if (!fieldLicenseTypeCar && !fieldLicenseTypeBicycle && !fieldLicenseTypeTricycle) {
            errors[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR] = 'กรุณาเลือกประเภทใบอนุญาตขับรถ';
            formIsValid = false;
        }
        if (fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] && fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] !== '1'
            && (fieldLicenseTypeCar || fieldLicenseTypeBicycle || fieldLicenseTypeTricycle)) {
            if ((fieldLicenseTypeCar && fieldLicenseTypeBicycle)
                || (fieldLicenseTypeCar && fieldLicenseTypeTricycle)
                || (fieldLicenseTypeTricycle && fieldLicenseTypeBicycle)) {
                errors[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR] = 'การอบรมสำหรับผู้ขอ \'ต่อ\' ใบอนุญาต สามารถเลือกประเภทใบอนุญาตได้ 1 ประเภทเท่านั้น ถ้าหากต้องการใบอนุญาตมากกว่า 1 ประเภท จะต้องกรอกข้อมูลลงทะเบียนอบรมใหม่อีกครั้ง';
                formIsValid = false;
            }
        }

        traineeForm.errors = errors;

        this.setState({
            traineeForm: traineeForm,
        }, () => {
            if (!formIsValid) {
                /*scroller.scrollTo(TOP_OF_FORM, {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                    //delay: 100,
                    //containerId: 'ContainerElementID',
                    //offset: 50, // Scrolls to element + 50 pixels down the page
                });*/
            }
        });

        return formIsValid;
    };

    doRegister = () => {
        const user = getLoginUser();
        const loginToken = user === null ? null : getLoginUser().loginToken;

        const {traineeForm} = this.state;
        //const traineeData = traineeForm.fields;

        const formData = new FormData();
        formData.append('loginToken', loginToken);
        formData.append('courseId', this.props.course.id);
        formData.append(REGISTER_TRAINEE_TITLE, traineeForm.fields[REGISTER_TRAINEE_TITLE]);
        formData.append(REGISTER_TRAINEE_FIRST_NAME, traineeForm.fields[REGISTER_TRAINEE_FIRST_NAME]);
        formData.append(REGISTER_TRAINEE_LAST_NAME, traineeForm.fields[REGISTER_TRAINEE_LAST_NAME]);
        formData.append(REGISTER_TRAINEE_PID, traineeForm.fields[REGISTER_TRAINEE_PID]);
        formData.append(REGISTER_TRAINEE_ADDRESS, traineeForm.fields[REGISTER_TRAINEE_ADDRESS]);
        formData.append(REGISTER_TRAINEE_SUB_DISTRICT, traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT]);
        formData.append(REGISTER_TRAINEE_DISTRICT, traineeForm.fields[REGISTER_TRAINEE_DISTRICT]);
        formData.append(REGISTER_TRAINEE_PROVINCE, traineeForm.fields[REGISTER_TRAINEE_PROVINCE]);
        formData.append(REGISTER_TRAINEE_POSTAL_CODE, traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE]);
        formData.append(REGISTER_TRAINEE_PHONE, traineeForm.fields[REGISTER_TRAINEE_PHONE]);
        formData.append(REGISTER_TRAINEE_SELECTED_COURSE_TYPE, traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]);
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR] ? '1' : '0');
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE] ? '1' : '0');
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE] ? '1' : '0');
        formData.append(REGISTER_TRAINEE_IMAGE_FILE_PID, traineeForm.fields[REGISTER_TRAINEE_IMAGE_FILE_PID]);

        fetch('/api/register_course_driving_license', {
            method: 'post',
            body: formData,
            /*headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                loginToken,
                courseId: this.props.course.id,
                traineeData,
            }),*/
        })
            .then(result => result.json())
            .then(result => {
                const msg = result['error']['message'];
                if (result['error']['code'] === 0) {
                    this.showDialog(msg, 'success', () => {
                        this.dismissDialog();
                        Router.back();
                    });
                }
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

    render() {
        let {traineeForm, step, dialog} = this.state;

        return (
            <MainLayout>
                <NextHead>
                    <style>{'body_ { background-color: red; }'}</style>
                </NextHead>

                {
                    this.props.course && this.state.courseTypeList &&
                    <div className="container">
                        <div className="row">
                            <div className="col text-title-top">
                                {/*<p>โครงการบริการวิชาการ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>*/}
                                <h3>แบบฟอร์มสมัครอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่</h3></div>
                        </div>
                        {/*ชื่อหลักสูตร, วันที่อบรม, สถานที่อบรม*/}
                        <div className="row">
                            <div className="col">
                                <div className="border-project">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">ชื่อหลักสูตร / รุ่นที่</div>
                                                <div className="col-sm-9 text-gray">{this.props.course.name}</div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">วันที่จัด</div>
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

                        {/*ฟอร์มสมัครอบรม*/}
                        <form id="applicationFormGroup" method="post" noValidate={true} onSubmit={this.handleSubmit}>
                            {/*ขั้นตอน 1 กรอกข้อมูลผู้สมัครอบรม*/}
                            {((step === 1) || (step === 2)) &&
                            <div style={{marginTop: '15px'}}>
                                {/*หัวข้อ*/}
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <h4 className="text-black" style={{marginTop: '20px'}}>
                                            <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัครอบรม&nbsp;
                                        </h4>
                                    </div>
                                </div>

                                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                                <Element name={TOP_OF_FORM}>
                                    <TraineeRegisterForm
                                        traineeForm={traineeForm}
                                        nameTitleList={this.props.nameTitleList}
                                        courseTypeList={this.state.courseTypeList}
                                        handleChangeCallback={this.handleChange}
                                    />
                                </Element>
                            </div>
                            }

                            {/* //todo: ปุ่ม สมัคร ควรไว้ตรงนี้ */}
                        </form>
                    </div>
                }
                {
                    !this.props.course &&
                    <div style={{textAlign: 'center', color: 'red'}}>{this.props.errorMessage}</div>
                }
                {
                    !this.state.courseTypeList &&
                    <div style={{textAlign: 'center', color: 'red'}}>{this.state.errorMessage}</div>
                }

                <Dialog message={dialog.message}
                        textColor={dialog.textColor}
                        isOpen={dialog.isOpen}
                        onCloseCallback={dialog.onCloseCallback}/>

                <style jsx>{`
                    
                `}</style>
            </MainLayout>
        );
    }
}