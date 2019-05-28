import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {SERVICE_SOCIAL} from "../etc/constants";

const TOP_OF_FORM = 'topOfForm';
//const ORGANIZATION_TYPE_OTHER = '9999';

const REGISTER_TRAINEE_TITLE = 'traineeTitle';
const REGISTER_TRAINEE_FIRST_NAME = 'traineeFirstName';
const REGISTER_TRAINEE_LAST_NAME = 'traineeLastName';
const REGISTER_TRAINEE_AGE = 'traineeAge';
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
                                                <label className="mt-2">อายุ</label>
                                            </div>
                                            <div className="col-md-9">
                                                <input value={traineeForm.fields[REGISTER_TRAINEE_AGE] || ''}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_AGE)}
                                                       type="number"
                                                       placeholder="กรอกอายุ"
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_AGE]}/>
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
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_OCCUPATION]}/>
                                            </div>
                                        </div>
                                        <div className="row">
                                            <div className="col-md-3">
                                                <label className="mt-2">สถานที่ทำงาน (ถ้ามี)</label>
                                            </div>
                                            <div className="col-md-9">
                                                <input value={traineeForm.fields[REGISTER_TRAINEE_WORK_PLACE] || ''}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_WORK_PLACE)}
                                                       type="text"
                                                       placeholder="กรอกสถานทีทำงาน"
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_WORK_PLACE]}/>
                                            </div>
                                        </div>
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
                                                               className="form-control input-md mt-2"/>
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
                                                <label className="mt-2">อีเมล</label>
                                            </div>
                                            <div className="col-md-9">
                                                <input value={traineeForm.fields[REGISTER_TRAINEE_EMAIL] || ''}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_EMAIL)}
                                                       type="email"
                                                       onKeyDown={e => {
                                                           if (e.key === ' ') {
                                                               e.preventDefault();
                                                           }
                                                       }}
                                                       placeholder="อีเมล"
                                                       className="form-control-2 input-md mt-2"/>
                                                <ErrorLabel
                                                    value={traineeForm.errors[REGISTER_TRAINEE_EMAIL]}/>
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
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_CONTACT_PERSON_NAME]}/>
                                                    </div>
                                                    <div className="col nopadleft">
                                                        <input value={traineeForm.fields[REGISTER_TRAINEE_CONTACT_PERSON_PHONE] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_CONTACT_PERSON_PHONE)}
                                                               type="text"
                                                               placeholder="เบอร์โทรศัพท์มือถือ"
                                                               className="form-control input-md mt-2"/>
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
                                                       placeholder="กรอกโรคประจำตัว"
                                                       className="form-control-2 input-md mt-2"/>
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
                                                   type="checkbox" id="test1"/><label for="test1">เว็บไซต์</label><br/>
                                            <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_EMAIL]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_EMAIL)}
                                                   type="checkbox" id="test2"/><label for="test2">อีเมล</label><br/>
                                            <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE)}
                                                   type="checkbox" id="test3"/><label for="test3">แผ่นประชาสัมพันธ์</label><br/>
                                            <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_ONLINE]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_ONLINE)}
                                                   type="checkbox" id="test4"/><label for="test4">สื่อสังคมออนไลน์</label><br/>
                                            <input checked={traineeForm.fields[REGISTER_TRAINEE_NEWS_SOURCE_MOUTH]}
                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NEWS_SOURCE_MOUTH)}
                                                   type="checkbox" id="test5"/><label for="test5">บอกต่อ</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="btn-red-submit" style={{marginTop: '20px'}}>
                                <button type="submit" className="btn btn-danger">ลงทะเบียน</button>
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

export default class ServiceSocialRegister extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            step: 1,
            traineeForm: {
                fields: {},
                errors: {}
            },
            nameTitleList: [],
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
            initialTraineeFields[REGISTER_TRAINEE_AGE] = user.age;
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

        if (field === REGISTER_TRAINEE_EMAIL || field === REGISTER_TRAINEE_PROVINCE) {
            fields[field] = e.target.value.trim();
        } else if (field === REGISTER_TRAINEE_NEWS_SOURCE_WEB || field === REGISTER_TRAINEE_NEWS_SOURCE_BROCHURE
            || field === REGISTER_TRAINEE_NEWS_SOURCE_EMAIL || field === REGISTER_TRAINEE_NEWS_SOURCE_ONLINE
            || field === REGISTER_TRAINEE_NEWS_SOURCE_MOUTH) {
            fields[field] = e.target.checked;
        } else {
            fields[field] = e.target.value;
        }
        this.setState({traineeForm});
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (this.validateForm()) {
            this.doRegister();
        } else {
            //alert('กรุณากรอกข้อมูลในหน้านี้ให้ครบถ้วน');
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
        if (!fields[REGISTER_TRAINEE_AGE]) {
            errors[REGISTER_TRAINEE_AGE] = 'กรุณากรอกอายุ';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_OCCUPATION] || fields[REGISTER_TRAINEE_OCCUPATION].trim().length === 0) {
            errors[REGISTER_TRAINEE_OCCUPATION] = 'กรุณากรอกอาชีพ';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_WORK_PLACE] || fields[REGISTER_TRAINEE_WORK_PLACE].trim().length === 0) {
            errors[REGISTER_TRAINEE_WORK_PLACE] = 'กรุณากรอกสถานที่ทำงาน';
            formIsValid = false;
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
        if (!fields[REGISTER_TRAINEE_POSTAL_CODE] || fields[REGISTER_TRAINEE_POSTAL_CODE].trim().length === 0) {
            errors[REGISTER_TRAINEE_POSTAL_CODE] = 'กรุณากรอกรหัสไปรษณีย์';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_PHONE] || fields[REGISTER_TRAINEE_PHONE].trim().length === 0) {
            errors[REGISTER_TRAINEE_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_EMAIL] || fields[REGISTER_TRAINEE_EMAIL].trim().length === 0) {
            errors[REGISTER_TRAINEE_EMAIL] = 'กรุณากรอกอีเมล';
            formIsValid = false;
        } else if (!isValidEmail(fields[REGISTER_TRAINEE_EMAIL])) {
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
            errors[REGISTER_TRAINEE_DISEASE] = 'กรุณากรอกเบอร์มือถือของบุคคลที่สามารถติดต่อได้';
            formIsValid = false;
        }

        traineeForm.errors = errors;

        this.setState({
            traineeForm: traineeForm,
        }, () => {
            if (!formIsValid) {
                scroller.scrollTo(TOP_OF_FORM, {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                    //delay: 100,
                    //containerId: 'ContainerElementID',
                    //offset: 50, // Scrolls to element + 50 pixels down the page
                });
            }
        });

        return formIsValid;
    };

    doRegister = () => {
        const user = getLoginUser();
        const loginToken = user === null ? null : getLoginUser().loginToken;
        const {traineeForm} = this.state;
        const traineeData = traineeForm.fields;

        fetch('/api/register_course_social', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                loginToken,
                courseId: this.props.course.id,
                traineeData,
            }),
        })
            .then(result => result.json())
            .then(result => {
                let msg = result['error']['message'];
                alert(msg);
                if (result['error']['code'] === 0) {
                    Router.back();
                }
            });
    };

    render() {
        let {traineeForm, step} = this.state;

        return (
            <MainLayout>
                <NextHead>
                    <style>{'body_ { background-color: red; }'}</style>
                </NextHead>

                {
                    this.props.course &&
                    <div className="container">
                        <div className="row">
                            <div className="col text-title-top">
                                {/*<p>โครงการบริการวิชาการ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>*/}
                                <h3>สมัครเข้าอบรมบริการสังคม</h3></div>
                        </div>
                        {/*ชื่อหลักสูตร, วันที่อบรม, สถานที่อบรม*/}
                        <div className="row">
                            <div className="col">
                                <div className="border-project">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">ชื่อโครงการ</div>
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
                                            <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัครอบรม
                                        </h4>
                                    </div>
                                </div>

                                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                                <Element name={TOP_OF_FORM}>
                                    <TraineeRegisterForm
                                        traineeForm={traineeForm}
                                        nameTitleList={this.props.nameTitleList}
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

                <style jsx>{`
                    
                `}</style>
            </MainLayout>
        );
    }
}