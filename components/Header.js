import Link from 'next/link';
import Modal from 'react-bootstrap/Modal';
import $ from 'jquery';
import './Header.css';
import {getLoginUser, setLoginUser, isString, isValidEmail, isPositiveInteger, getDateFormatFromDateObject} from "../etc/utils";
import ErrorLabel from './ErrorLabel';
import Dialog from "./Dialog";
import DatePicker from "react-datepicker";
import {subDays} from "react-datepicker";
import "../css/react-datepicker.css";

import th from 'date-fns/locale/th';
import {registerLocale, setDefaultLocale} from "react-datepicker";

registerLocale('th', th);
setDefaultLocale('th');

const ORGANIZATION_TYPE_OTHER = 9999;

class CustomToggle extends React.Component {
    constructor(props, context) {
        super(props, context);
    }

    handleClick = e => {
        e.preventDefault();
        this.props.onClick(e);
    };

    render() {
        return (
            <label onClick={this.handleClick}>
                <i className="far fa-user"/>&nbsp;&nbsp;เข้าสู่ระบบ
            </label>
        );
    }
}

const REGISTER_TYPE_PERSON = 'personal';
const REGISTER_TYPE_ORGANIZATION = 'organization';

const RESULT_ERROR = 'resultError';
const LOGIN_EMAIL = 'loginEmail';
const LOGIN_PASSWORD = 'loginPassword';
const REGISTER_PERSON_TITLE = 'registerPersonTitle';
const REGISTER_PERSON_FIRST_NAME = 'registerPersonFirstName';
const REGISTER_PERSON_LAST_NAME = 'registerPersonLastName';
const REGISTER_PERSON_BIRTH_DATE = 'registerPersonBirthDate';
const REGISTER_PERSON_JOB_POSITION = 'registerPersonJobPosition';
const REGISTER_PERSON_ORGANIZATION_NAME = 'registerPersonOrganizationName';
const REGISTER_PERSON_ORGANIZATION_TYPE = 'registerPersonOrganizationType';
const REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM = 'registerPersonOrganizationTypeCustom';
const REGISTER_PERSON_PHONE = 'registerPersonPhone';
const REGISTER_PERSON_EMAIL = 'registerPersonEmail';
const REGISTER_PERSON_PASSWORD = 'registerPersonPassword';
const REGISTER_PERSON_CONFIRM_PASSWORD = 'registerPersonConfirmPassword';

const REGISTER_ORGANIZATION_TITLE = 'registerOrganizationTitle';
const REGISTER_ORGANIZATION_FIRST_NAME = 'registerOrganizationFirstName';
const REGISTER_ORGANIZATION_LAST_NAME = 'registerOrganizationLastName';
const REGISTER_ORGANIZATION_BIRTH_DATE = 'registerOrganizationBirthDate';
const REGISTER_ORGANIZATION_JOB_POSITION = 'registerOrganizationJobPosition';
const REGISTER_ORGANIZATION_ORGANIZATION_NAME = 'registerOrganizationOrganizationName';
const REGISTER_ORGANIZATION_ORGANIZATION_TYPE = 'registerOrganizationOrganizationType';
const REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM = 'registerOrganizationOrganizationTypeCustom';
const REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS = 'registerOrganizationOrganizationAddress';
const REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT = 'registerOrganizationOrganizationSubDistrict';
const REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT = 'registerOrganizationOrganizationDistrict';
const REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE = 'registerOrganizationOrganizationProvince';
const REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE = 'registerOrganizationOrganizationPostalCode';
const REGISTER_ORGANIZATION_ORGANIZATION_PHONE = 'registerOrganizationOrganizationPhone';
const REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID = 'registerOrganizationOrganizationTaxId';
const REGISTER_ORGANIZATION_PHONE = 'registerOrganizationPhone';
const REGISTER_ORGANIZATION_EMAIL = 'registerOrganizationEmail';
const REGISTER_ORGANIZATION_PASSWORD = 'registerOrganizationPassword';
const REGISTER_ORGANIZATION_CONFIRM_PASSWORD = 'registerOrganizationConfirmPassword';

class LoginForm extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            showRegisterModal: false,
            showLoginModal: false,
            showProfileModal: false,
            fields: {},
            errors: {},
            registerType: 1,
            nameTitleList: [],
            organizationTypeList: [],
            dialog: {
                isOpen: false,
                message: '',
                textColor: '#000',
                onCloseCallback: null,
            },
        };
        this.personForm_organizationTypeCustomInput = React.createRef();
        this.organizationForm_organizationTypeCustomInput = React.createRef();

        //registerLocale('th', th);
    }

    focusPersonFormOrganizationTypeCustomInput = () => {
        this.personForm_organizationTypeCustomInput.current.focus();
    };
    focusOrganizationFormOrganizationTypeCustomInput = () => {
        this.organizationForm_organizationTypeCustomInput.current.focus();
    };

    componentDidMount() {
        const loginUser = getLoginUser();
        this.setState({loginUser});

        this.doGetNameTitle(); //todo: เปลี่ยนเป็น server-side render (คิดว่าต้องทำใน getInitialProps)
        this.doGetOrganizationType();
    }

    doGetNameTitle = () => {
        fetch('/api/get_name_title', {
            method: 'post'
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        nameTitleList: result['dataList'],
                    });
                } else {
                    /*let errors = {};
                    errors[RESULT_ERROR] = result['error_message'];
                    this.setState({errors: errors});*/
                }
            });
    };

    doGetOrganizationType = () => {
        fetch('/api/get_organization_type', {
            method: 'post'
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        organizationTypeList: result['dataList'],
                    });
                } else {
                    /*let errors = {};
                    errors[RESULT_ERROR] = result['error_message'];
                    this.setState({errors: errors});*/
                }
            });
    };

    /*เมื่อคลิกชื่อ (หรือข้อความ "เข้าสู่ระบบ") ที่มุมบนขวา*/
    onClickDisplayName = e => {
        if (this.state.loginUser == null) {
            // แสดง Login modal
            this.setState({
                errors: {},
                showLoginModal: true,
            });
        } else {
            // แสดง Profile modal
            this.setState({
                errors: {},
                showProfileModal: true,
            });
        }
    };

    onClickLogoutButton = e => {
        setLoginUser(null);
        this.setState({
            showProfileModal: false,
            loginUser: null,
        });
    };

    handleCloseLoginModal = e => {
        this.setState({
            showLoginModal: false
        });
    };

    handleCloseProfileModal = e => {
        this.setState({
            showProfileModal: false
        });
    };

    handleChange(field, allowSpace, e) {
        let {fields} = this.state;

        if (field === REGISTER_PERSON_BIRTH_DATE || field === REGISTER_ORGANIZATION_BIRTH_DATE) {
            /*let d = e; //new Date();
            let yyyy = d.getFullYear();
            let mm = d.getMonth() + 1;
            let dd = d.getDate();*/
            //alert(`${yyyy}-${mm}-${dd}`);

            fields[field] = e;
        } else if (!allowSpace) {
            fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        } else {
            fields[field] = e.target.value;
        }

        //ถ้าหากเลือก "อื่นๆ" ในช่อง "ประเภทหน่วยงาน" ก็จะ focus ไปที่ช่องกรอกประเภทหน่วยงานที่อยู่ถัดลงไป (ต้องรอ setState ทำงานก่อน)
        let setFocus1 = (field === REGISTER_PERSON_ORGANIZATION_TYPE) && (parseInt(e.target.value) === ORGANIZATION_TYPE_OTHER);
        let setFocus2 = (field === REGISTER_ORGANIZATION_ORGANIZATION_TYPE) && (parseInt(e.target.value) === ORGANIZATION_TYPE_OTHER);

        this.setState({fields}, () => {
            if (setFocus1) {
                this.personForm_organizationTypeCustomInput.current.focus();
            }
            if (setFocus2) {
                this.organizationForm_organizationTypeCustomInput.current.focus();
            }
        });
    }

    handleSubmitLogin = event => {
        event.preventDefault();
        if (this.validateLoginForm()) {
            let fields = this.state.fields;
            this.doLogin(fields[LOGIN_EMAIL], fields[LOGIN_PASSWORD]);
        } else {
            //alert('กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง');
        }
    };

    validateLoginForm() {
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        if (!fields[LOGIN_EMAIL] || fields[LOGIN_EMAIL].trim().length === 0) {
            errors[LOGIN_EMAIL] = 'กรุณากรอกอีเมล';
            formIsValid = false;
        } /*else if (!isValidEmail(fields['email'])) {
            errors['email'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            formIsValid = false;
        }*/

        if (!fields[LOGIN_PASSWORD] || fields[LOGIN_PASSWORD].trim().length === 0) {
            errors[LOGIN_PASSWORD] = 'กรุณากรอกรหัสผ่าน';
            formIsValid = false;
        }

        this.setState({errors: errors});
        return formIsValid;
    }

    handleSubmitRegister = (registerType, event) => {
        event.preventDefault();
        if (this.validateRegisterForm(registerType)) {
            this.doRegister(registerType);
        } else {
            /*this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                this.dismissDialog();
            });*/
        }
    };

    validateRegisterForm(registerType) {
        let fields = this.state.fields;
        let errors = {};
        let formIsValid = true;

        switch (registerType) {
            case REGISTER_TYPE_PERSON:
                if (!fields[REGISTER_PERSON_TITLE]) {
                    errors[REGISTER_PERSON_TITLE] = 'กรุณาเลือกคำนำหน้า';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_FIRST_NAME] || fields[REGISTER_PERSON_FIRST_NAME].trim().length === 0) {
                    errors[REGISTER_PERSON_FIRST_NAME] = 'กรุณากรอกชื่อ';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_LAST_NAME] || fields[REGISTER_PERSON_LAST_NAME].trim().length === 0) {
                    errors[REGISTER_PERSON_LAST_NAME] = 'กรุณากรอกนามสกุล';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_BIRTH_DATE]) {
                    errors[REGISTER_PERSON_BIRTH_DATE] = 'กรุณาระบุวันเกิด';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_PHONE] || fields[REGISTER_PERSON_PHONE].trim().length === 0) {
                    errors[REGISTER_PERSON_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
                    formIsValid = false;
                }
                if (parseInt(fields[REGISTER_PERSON_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER
                    && (!fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM] || fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM].trim().length === 0)) {
                    errors[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM] = 'กรุณากรอกประเภทหน่วยงาน';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_EMAIL] || fields[REGISTER_PERSON_EMAIL].trim().length === 0) {
                    errors[REGISTER_PERSON_EMAIL] = 'กรุณากรอกอีเมล';
                    formIsValid = false;
                } else if (!isValidEmail(fields[REGISTER_PERSON_EMAIL])) {
                    errors[REGISTER_PERSON_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_PASSWORD] || fields[REGISTER_PERSON_PASSWORD].trim().length < 6) {
                    errors[REGISTER_PERSON_PASSWORD] = 'กรุณากรอกรหัสผ่าน อย่างน้อย 6 ตัวอักษร';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_CONFIRM_PASSWORD] || fields[REGISTER_PERSON_CONFIRM_PASSWORD].trim().length === 0) {
                    errors[REGISTER_PERSON_CONFIRM_PASSWORD] = 'กรุณากรอกรหัสผ่านอีกครั้งเพื่อยืนยัน';
                    formIsValid = false;
                } else if (fields[REGISTER_PERSON_PASSWORD] !== fields[REGISTER_PERSON_CONFIRM_PASSWORD]) {
                    errors[REGISTER_PERSON_CONFIRM_PASSWORD] = 'การยืนยันรหัสผ่านไม่ตรงกัน';
                    formIsValid = false;
                }
                break;

            case REGISTER_TYPE_ORGANIZATION:
                if (!fields[REGISTER_ORGANIZATION_TITLE]) {
                    errors[REGISTER_ORGANIZATION_TITLE] = 'กรุณาเลือกคำนำหน้า';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_FIRST_NAME] || fields[REGISTER_ORGANIZATION_FIRST_NAME].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_FIRST_NAME] = 'กรุณากรอกชื่อ';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_LAST_NAME] || fields[REGISTER_ORGANIZATION_LAST_NAME].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_LAST_NAME] = 'กรุณากรอกนามสกุล';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_BIRTH_DATE]) {
                    errors[REGISTER_ORGANIZATION_BIRTH_DATE] = 'กรุณาระบุวันเกิด';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_JOB_POSITION] || fields[REGISTER_ORGANIZATION_JOB_POSITION].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_JOB_POSITION] = 'กรุณากรอกตำแหน่งงาน';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME] || fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_NAME] = 'กรุณากรอกชื่อหน่วยงาน';
                    formIsValid = false;
                }

                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE]) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_TYPE] = 'กรุณาเลือกประเภทหน่วยงาน';
                    formIsValid = false;
                } else if (parseInt(fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER
                    && (!fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM] || fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM].trim().length === 0)) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM] = 'กรุณากรอกประเภทหน่วยงาน';
                    formIsValid = false;
                }

                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS] || fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS] = 'กรุณากรอกเลขที่ / อาคาร / หมู่ / ซอย / ถนน';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT] || fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT] = 'กรุณากรอกแขวง / ตำบล';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT] || fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT] = 'กรุณากรอกเขต / อำเภอ';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE] || fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE] = 'กรุณากรอกจังหวัด';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE]
                    || fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE].trim().length !== 5
                    || !isPositiveInteger(fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE])) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE] || fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์หน่วยงาน';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID] || fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID] = 'กรุณากรอกเลขประจำตัวผู้เสียภาษี';
                    formIsValid = false;
                }

                if (!fields[REGISTER_ORGANIZATION_PHONE] || fields[REGISTER_ORGANIZATION_PHONE].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_EMAIL] || fields[REGISTER_ORGANIZATION_EMAIL].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_EMAIL] = 'กรุณากรอกอีเมล';
                    formIsValid = false;
                } else if (!isValidEmail(fields[REGISTER_ORGANIZATION_EMAIL])) {
                    errors[REGISTER_ORGANIZATION_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_PASSWORD] || fields[REGISTER_ORGANIZATION_PASSWORD].trim().length < 6) {
                    errors[REGISTER_ORGANIZATION_PASSWORD] = 'กรุณากรอกรหัสผ่าน อย่างน้อย 6 ตัวอักษร';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] || fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] = 'กรุณากรอกรหัสผ่านอีกครั้งเพื่อยืนยัน';
                    formIsValid = false;
                } else if (fields[REGISTER_ORGANIZATION_PASSWORD] !== fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD]) {
                    errors[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] = 'การยืนยันรหัสผ่านไม่ตรงกัน';
                    formIsValid = false;
                }
                break;
        }

        this.setState({errors: errors});
        return formIsValid;
    }

    doRegister = (registerType) => {
        let fields = this.state.fields;

        let params = {};
        switch (registerType) {
            case REGISTER_TYPE_PERSON:
                params['title'] = fields[REGISTER_PERSON_TITLE];
                params['firstName'] = fields[REGISTER_PERSON_FIRST_NAME];
                params['lastName'] = fields[REGISTER_PERSON_LAST_NAME];
                params['birthDate'] = getDateFormatFromDateObject(fields[REGISTER_PERSON_BIRTH_DATE]);
                params['jobPosition'] = fields[REGISTER_PERSON_JOB_POSITION];
                params['organizationName'] = fields[REGISTER_PERSON_ORGANIZATION_NAME];
                params['organizationType'] = fields[REGISTER_PERSON_ORGANIZATION_TYPE];
                params['organizationTypeCustom'] = fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM];
                params['phone'] = fields[REGISTER_PERSON_PHONE];
                params['email'] = fields[REGISTER_PERSON_EMAIL];
                params['password'] = fields[REGISTER_PERSON_PASSWORD];
                break;
            case REGISTER_TYPE_ORGANIZATION:
                params['title'] = fields[REGISTER_ORGANIZATION_TITLE];
                params['firstName'] = fields[REGISTER_ORGANIZATION_FIRST_NAME];
                params['lastName'] = fields[REGISTER_ORGANIZATION_LAST_NAME];
                params['birthDate'] = getDateFormatFromDateObject(fields[REGISTER_ORGANIZATION_BIRTH_DATE]);
                params['jobPosition'] = fields[REGISTER_ORGANIZATION_JOB_POSITION];
                params['organizationName'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME];
                params['organizationType'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE];
                params['organizationTypeCustom'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM];
                params['phone'] = fields[REGISTER_ORGANIZATION_PHONE];
                params['email'] = fields[REGISTER_ORGANIZATION_EMAIL];
                params['password'] = fields[REGISTER_ORGANIZATION_PASSWORD];
                params['address'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS];
                params['subDistrict'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT];
                params['district'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT];
                params['province'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE];
                params['postalCode'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE];
                params['organizationPhone'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE];
                params['taxId'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID];
                break;
        }

        fetch('/api/register_member', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(params),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    alert(result['error']['message']);
                    this.setState({
                        fields: {},
                        showRegisterModal: false,
                        showLoginModal: true,
                    });
                } else {
                    let errors = {};
                    errors[RESULT_ERROR] = result['error']['message'];
                    //this.setState({errors: errors});
                    alert(result['error']['message']);
                }
            });
    };

    doLogin = (email, password) => {
        fetch('/api/login_member', {
            method: 'post',
            headers: {
                //'Content-Type': 'application/x-www-form-urlencoded'
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    let memberData = result['memberData'];

                    const {
                        loginToken, id, title, firstName, lastName, birthDate, jobPosition,
                        organizationName, organizationType, organizationTypeCustom, phone, email,
                        address, subDistrict, district, province, postalCode, organizationPhone, taxId
                    } = memberData;
                    const loginUser = {
                        loginToken, id, title, firstName, lastName, birthDate, jobPosition,
                        organizationName, organizationType, organizationTypeCustom, phone, email,
                        address, subDistrict, district, province, postalCode, organizationPhone, taxId
                    };
                    setLoginUser(loginUser);

                    this.setState({
                        fields: {},
                        loginUser,
                        showLoginModal: false,
                    }, () => {
                        let msg = `ยินดีต้อนรับคุณ ${memberData.firstName} ${memberData.lastName}`;
                        this.showDialog(msg, "ok", () => {
                            this.dismissDialog();
                        });
                    });
                } else {
                    let errors = {};
                    errors[RESULT_ERROR] = result['error']['message'];
                    //this.setState({errors: errors});
                    alert(result['error']['message']);
                }
            });
    };

    onClickRegister = () => {
        this.setState({
            showLoginModal: false,
            showRegisterModal: true,
        });
    };

    handleCloseRegisterModal = e => {
        this.setState({
            showRegisterModal: false
        });
    };

    onClickForgotPassword = () => {
        this.setState({
            showLoginModal: false,
        });
    };

    handleClickRadio(e) {
        if (e.target.id === 'radio-1') {
            this.setState({
                registerType: 1
            });
        } else if (e.target.id === 'radio-2') {
            this.setState({
                registerType: 2
            });
        }
    }

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

    setDatePickerMinDate = () => {
        const d = new Date();
        const year = 1900;
        const month = 0;
        const day = 1;
        return new Date(year, month, day)
    };

    /*setDatePickerMaxDate = () => {
        const d = new Date();
        const year = d.getFullYear();
        const month = d.getMonth();
        const day = d.getDate();
        return new Date(year, month, day)
    };*/

    render() {
        const {loginUser, dialog} = this.state;
        let displayName = loginUser == null ? 'เข้าสู่ระบบ' : loginUser.firstName + ' ' + loginUser.lastName;

        return (
            <div className="icon_top">
                <div>
                    <div style={{border: '0px solid red'}}>
                        <a href="#" className="link" onClick={this.onClickDisplayName}>
                            <i className="far fa-user"/>&nbsp;&nbsp;{displayName}
                        </a>

                        <Modal
                            size={'md'}
                            show={this.state.showLoginModal}
                            onHide={this.handleCloseLoginModal}
                            centered>
                            <Modal.Body>
                                <div style={{padding: '10px'}}>
                                    <div className="orlog">เข้าสู่ระบบ</div>

                                    <form id="loginForm" method="post" onSubmit={this.handleSubmitLogin} noValidate={true}>
                                        <label style={{marginTop: '15px', marginBottom: '3px', marginLeft: '3px'}}>อีเมล
                                            : </label>
                                        <input value={this.state.fields[LOGIN_EMAIL] || ''}
                                               onChange={this.handleChange.bind(this, LOGIN_EMAIL, false)}
                                               onKeyDown={e => {
                                                   if (e.key === ' ') {
                                                       e.preventDefault();
                                                   }
                                               }}
                                               placeholder="กรอกอีเมล"
                                               type="email" className="form-control input-md"
                                               style={{marginBottom: 0}}/>
                                        <ErrorLabel value={this.state.errors[LOGIN_EMAIL]}/>

                                        <label style={{marginTop: '15px', marginBottom: '3px', marginLeft: '3px'}}>รหัสผ่าน
                                            : </label>
                                        <input value={this.state.fields[LOGIN_PASSWORD] || ''}
                                               onChange={this.handleChange.bind(this, LOGIN_PASSWORD, false)}
                                               placeholder="กรอกรหัสผ่าน"
                                               type="password" className="form-control input-md"
                                               style={{marginBottom: 0}}/>
                                        <ErrorLabel value={this.state.errors[LOGIN_PASSWORD]}/>

                                        <ErrorLabel value={this.state.errors[RESULT_ERROR]}
                                                    textAlign={'center'}
                                                    marginTop={'25px'}/>

                                        <button type="submit" value="submit" className="btn btn-ss"
                                                style={{
                                                    width: '100%',
                                                    marginTop: '25px',
                                                    marginBottom: '10px'
                                                }}>
                                            ลงชื่อเข้าสู่ระบบ
                                        </button>
                                    </form>

                                    <div className="row"
                                         style={{border: '0px solid blue', marginTop: '10px'}}>
                                        <div className="col-xs-6 col-sm-6"
                                             style={{border: '0px solid red'}}>
                                            <a href="#" className="link inline-top" onClick={this.onClickRegister}>
                                                สมัครสมาชิก
                                            </a>
                                        </div>
                                        <div className="col-xs-6 col-sm-6"
                                             style={{border: '0px solid red', textAlign: 'right'}}>
                                            <a href="#" className="link" onClick={this.onClickForgotPassword}>
                                                ลืมรหัสผ่าน
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </Modal.Body>
                        </Modal>

                        <Modal
                            size={'sm'}
                            show={this.state.showProfileModal}
                            onHide={this.handleCloseProfileModal}
                            centered>
                            <Modal.Body>
                                <div style={{padding: '10px'}}>
                                    <div className="orlog">{displayName}</div>

                                    <button type="button" className="btn btn-ss" onClick={this.onClickLogoutButton}
                                            style={{
                                                width: '100%',
                                                marginTop: '25px',
                                                marginBottom: '10px'
                                            }}>
                                        ออกจากระบบ
                                    </button>
                                </div>
                            </Modal.Body>
                        </Modal>

                        <Modal
                            dialogClassName={'modal-register-form'}
                            show={this.state.showRegisterModal}
                            onHide={this.handleCloseRegisterModal}
                            centered>
                            <Modal.Body>
                                <div id="registerForm">
                                    <div id="data1" className="detail-inside-box">
                                        <div className="content-popup">
                                            <h4 style={{
                                                marginTop: '20px',
                                                marginBottom: '15px'
                                            }}>สมัครสมาชิกเว็บไซต์</h4>
                                            <div className="c-tab js-tab">
                                                <div className="tabcropcenter">
                                                    <div className="c-tab__trigger">
                                                        <div className="md-radio md-radio-inline radiocheck">
                                                            <input type="radio" id="radio-1"
                                                                   onClick={e => {
                                                                       this.handleClickRadio(e);
                                                                   }}
                                                                   checked={this.state.registerType === 1}/>
                                                            <label htmlFor="radio-1">บุคคลทั่วไป</label>
                                                        </div>

                                                        <div className="md-radio md-radio-inline radiocheck">
                                                            <input type="radio" id="radio-2"
                                                                   onClick={e => {
                                                                       this.handleClickRadio(e);
                                                                   }}
                                                                   checked={this.state.registerType === 2}/>
                                                            <label htmlFor="radio-2">องค์กร / บริษัท</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="c-tab__content js-tab-group-1 js-tab-radio-1"
                                                     style={{
                                                         display: this.state.registerType === 1 ? 'block' : 'none',
                                                         textAlign: 'center'
                                                     }}>
                                                    <form id="personalRegisterForm" method="post"
                                                          onSubmit={this.handleSubmitRegister.bind(this, REGISTER_TYPE_PERSON)}
                                                          noValidate={true}>
                                                        <div className="container">
                                                            <div className="row">
                                                                <div className="col">
                                                                    <div className="regisfo">

                                                                        {/*คำนำหน้าชื่อ*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*คำนำหน้าชื่อ*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">คำนำหน้าชื่อ</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <select
                                                                                            value={this.state.fields[REGISTER_PERSON_TITLE] || '0'}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_TITLE, false)}
                                                                                            className="form-control">
                                                                                            <option value="0" disabled
                                                                                                    selected>เลือกคำนำหน้า
                                                                                            </option>
                                                                                            {
                                                                                                this.state.nameTitleList.map((nameTitle, index) =>
                                                                                                    <option key={index} value={nameTitle.title}>{nameTitle.title}</option>
                                                                                                )
                                                                                            }
                                                                                        </select>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_TITLE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">

                                                                            </div>
                                                                        </div>

                                                                        {/*ชื่อ, นามสกุล*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*ชื่อ*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ชื่อ</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_FIRST_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_FIRST_NAME, true)}
                                                                                            type="text"
                                                                                            placeholder="กรอกชื่อ"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_FIRST_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*นามสกุล*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">นามสกุล</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_LAST_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_LAST_NAME, true)}
                                                                                            type="text"
                                                                                            placeholder="กรอกนามสกุล"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_LAST_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*วันเกิด, ตำแหน่งงาน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*วันเกิด*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">วันเกิด</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <DatePicker
                                                                                            selected={this.state.fields[REGISTER_PERSON_BIRTH_DATE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_BIRTH_DATE, true)}
                                                                                            onKeyDown={e => {
                                                                                                //if (e.key === ' ') {
                                                                                                e.preventDefault();
                                                                                                //}
                                                                                            }}
                                                                                            showMonthDropdown
                                                                                            showYearDropdown
                                                                                            dropdownMode="select"
                                                                                            placeholderText="ระบุวันเกิด"
                                                                                            dateFormat="dd/MM/yyyy"
                                                                                            minDate={this.setDatePickerMinDate()}
                                                                                            maxDate={new Date()}
                                                                                            className="form-control input-md my-react-date-picker"/>

                                                                                        {/*<input
                                                                                            value={this.state.fields[REGISTER_PERSON_BIRTH_DATE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_BIRTH_DATE, false)}
                                                                                            type="number"
                                                                                            placeholder="กรอกอายุ"
                                                                                            className="form-control input-md"/>*/}
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_BIRTH_DATE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ตำแหน่งงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label">ตำแหน่งงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_JOB_POSITION] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_JOB_POSITION, true)}
                                                                                            type="text"
                                                                                            placeholder="กรอกตำแหน่งงาน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_JOB_POSITION]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*ชื่อหน่วยงาน, ประเภทหน่วยงาน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*ชื่อหน่วยงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label">ชื่อหน่วยงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_ORGANIZATION_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_ORGANIZATION_NAME, true)}
                                                                                            type="text"
                                                                                            placeholder="กรอกชื่อหน่วยงาน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_ORGANIZATION_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ประเภทหน่วยงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label">ประเภทหน่วยงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <select
                                                                                            value={this.state.fields[REGISTER_PERSON_ORGANIZATION_TYPE] || '0'}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_ORGANIZATION_TYPE, false)}
                                                                                            className="form-control">
                                                                                            <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                                            {
                                                                                                this.state.organizationTypeList.map((organizationType, index) =>
                                                                                                    <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                                                )
                                                                                            }
                                                                                            <option value={ORGANIZATION_TYPE_OTHER}>อื่นๆ (ระบุ)</option>
                                                                                        </select>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_ORGANIZATION_TYPE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*ประเภทหน่วยงาน (user กรอกเอง)*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <div
                                                                                            style={{display: parseInt(this.state.fields[REGISTER_PERSON_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER ? 'block' : 'none'}}>
                                                                                            <input value={this.state.fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM] || ''}
                                                                                                   onChange={this.handleChange.bind(this, REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM, true)}
                                                                                                   type="text"
                                                                                                   placeholder="กรอกประเภทหน่วยงาน"
                                                                                                   className="form-control input-md"
                                                                                                   ref={this.personForm_organizationTypeCustomInput}/>
                                                                                            <ErrorLabel
                                                                                                value={this.state.errors[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM]}/>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*เบอร์โทร, อีเมล*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*เบอร์โทร*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">เบอร์โทรศัพท์</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_PHONE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_PHONE, true)}
                                                                                            type="tel"
                                                                                            placeholder="กรอกเบอร์โทรศัพท์"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_PHONE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*อีเมล*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">อีเมล
                                                                                            <span
                                                                                                className="engtxt">(ใช้เป็น Username)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_EMAIL] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_EMAIL, false)}
                                                                                            onKeyDown={e => {
                                                                                                if (e.key === ' ') {
                                                                                                    e.preventDefault();
                                                                                                }
                                                                                            }}
                                                                                            type="email"
                                                                                            placeholder="กรอกอีเมล"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_EMAIL]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*รหัสผ่าน, ยืนยันรหัสผ่าน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*รหัสผ่าน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">รหัสผ่าน
                                                                                            <span
                                                                                                className="engtxt">(Password)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_PASSWORD] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_PASSWORD, false)}
                                                                                            type="password"
                                                                                            placeholder="กรอกรหัสผ่าน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_PASSWORD]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ยืนยันรหัสผ่าน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ยืนยันรหัสผ่าน
                                                                                            <span
                                                                                                className="engtxt">(Confirm password)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_PERSON_CONFIRM_PASSWORD] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_PERSON_CONFIRM_PASSWORD, false)}
                                                                                            type="password"
                                                                                            placeholder="กรอกรหัสผ่านอีกครั้งเพื่อยืนยัน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_PERSON_CONFIRM_PASSWORD]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <br/>
                                                                        <button type="submit" value="submit"
                                                                                className="btn btn-ss"
                                                                                style={{marginBottom: '15px'}}>
                                                                            สมัครสมาชิก
                                                                        </button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <div className="c-tab__content js-tab-group-1 js-tab-radio-2"
                                                     style={{
                                                         display: this.state.registerType === 2 ? 'block' : 'none',
                                                         textAlign: 'center'
                                                     }}>
                                                    <form id="organizationRegisterForm" method="post"
                                                          onSubmit={this.handleSubmitRegister.bind(this, REGISTER_TYPE_ORGANIZATION)}
                                                          noValidate={true}>
                                                        <div className="container">
                                                            <div className="row">
                                                                <div className="col">
                                                                    <div className="regisfo2">

                                                                        {/*คำนำหน้า*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*คำนำหน้า*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">คำนำหน้าชื่อ</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <select
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_TITLE] || '0'}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_TITLE, false)}
                                                                                            className="form-control">
                                                                                            <option value="0" disabled
                                                                                                    selected>เลือกคำนำหน้า
                                                                                            </option>
                                                                                            {
                                                                                                this.state.nameTitleList.map((nameTitle, index) =>
                                                                                                    <option key={index} value={nameTitle.title}>{nameTitle.title}</option>
                                                                                                )
                                                                                            }
                                                                                        </select>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_TITLE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                            </div>
                                                                        </div>

                                                                        {/*ชื่อ, นามสกุล*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*ชื่อ*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ชื่อ</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_FIRST_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_FIRST_NAME, true)}
                                                                                            type="text" placeholder="กรอกชื่อ"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_FIRST_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*นามสกุล*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">นามสกุล</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_LAST_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_LAST_NAME, true)}
                                                                                            type="text" placeholder="กรอกนามสกุล"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_LAST_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*อายุ, ตำแหน่งงาน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*อายุ*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">วันเกิด</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <DatePicker
                                                                                            selected={this.state.fields[REGISTER_ORGANIZATION_BIRTH_DATE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_BIRTH_DATE, true)}
                                                                                            onKeyDown={e => {
                                                                                                //if (e.key === ' ') {
                                                                                                e.preventDefault();
                                                                                                //}
                                                                                            }}
                                                                                            showMonthDropdown
                                                                                            showYearDropdown
                                                                                            dropdownMode="select"
                                                                                            placeholderText="ระบุวันเกิด"
                                                                                            dateFormat="dd/MM/yyyy"
                                                                                            minDate={this.setDatePickerMinDate()}
                                                                                            maxDate={new Date()}
                                                                                            className="form-control input-md my-react-date-picker"/>
                                                                                        {/*<input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_BIRTH_DATE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_BIRTH_DATE, false)}
                                                                                            type="number" placeholder="กรอกอายุ"
                                                                                            className="form-control input-md"/>*/}
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_BIRTH_DATE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ตำแหน่งงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ตำแหน่งงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_JOB_POSITION] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_JOB_POSITION, true)}
                                                                                            type="text" placeholder="กรอกตำแหน่งงาน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_JOB_POSITION]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*ชื่อหน่วยงาน, ประเภทหน่วยงาน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*ชื่อหน่วยงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ชื่อหน่วยงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_NAME, true)}
                                                                                            type="text" placeholder="กรอกชื่อหน่วยงาน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_NAME]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ประเภทหน่วยงาน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ประเภทหน่วยงาน</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <select
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE] || '0'}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_TYPE, false)}
                                                                                            className="form-control">
                                                                                            <option value="0" disabled
                                                                                                    selected>เลือกประเภทหน่วยงาน
                                                                                            </option>
                                                                                            <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                                            {
                                                                                                this.state.organizationTypeList.map((organizationType, index) =>
                                                                                                    <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                                                )
                                                                                            }
                                                                                            <option value={ORGANIZATION_TYPE_OTHER}>อื่นๆ (ระบุ)</option>
                                                                                        </select>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_TYPE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*ประเภทหน่วยงาน (user กรอกเอง)*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <div
                                                                                            style={{display: parseInt(this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER ? 'block' : 'none'}}>
                                                                                            <input value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM] || ''}
                                                                                                   onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM, true)}
                                                                                                   type="text"
                                                                                                   placeholder="กรอกประเภทหน่วยงาน"
                                                                                                   className="form-control input-md"
                                                                                                   ref={this.organizationForm_organizationTypeCustomInput}/>
                                                                                            <ErrorLabel
                                                                                                value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM]}/>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*ที่อยู่หน่วยงาน*/}
                                                                        <div className="row">
                                                                            <div className="col-12 col-md-2"
                                                                                 style={{paddingRight: 0}}>
                                                                                <label
                                                                                    className="label required-label">ที่อยู่หน่วยงาน</label>
                                                                            </div>
                                                                            <div className="col-12 col-md-9">
                                                                                <div className="row">
                                                                                    {/*เลขที่ / อาคาร / หมู่ / ซอย / ถนน*/}
                                                                                    <div
                                                                                        className="col-12 col-md-12">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS, true)}
                                                                                            type="text"
                                                                                            placeholder="เลขที่ / อาคาร / หมู่ / ซอย / ถนน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS]}/>
                                                                                    </div>
                                                                                    <div className="w-100"></div>
                                                                                    {/*แขวง / ตำบล*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT, true)}
                                                                                            type="text"
                                                                                            placeholder="แขวง / ตำบล"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT]}/>
                                                                                    </div>
                                                                                    {/*เขต / อำเภอ*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6 nopadleft">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT, true)}
                                                                                            type="text"
                                                                                            placeholder="เขต / อำเภอ"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT]}/>
                                                                                    </div>
                                                                                    <div className="w-100"></div>
                                                                                    {/*จังหวัด*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE, false)}
                                                                                            type="text"
                                                                                            placeholder="จังหวัด"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE]}/>
                                                                                    </div>
                                                                                    {/*รหัสไปรษณีย์*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6 nopadleft">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE, false)}
                                                                                            type="number"
                                                                                            maxLength={5}
                                                                                            placeholder="รหัสไปรษณีย์"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE]}/>
                                                                                    </div>
                                                                                    <div className="w-100"></div>
                                                                                    {/*เบอร์โทรหน่วยงาน*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_PHONE, true)}
                                                                                            type="text"
                                                                                            placeholder="เบอร์โทรศัพท์หน่วยงาน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_PHONE]}/>
                                                                                    </div>
                                                                                    {/*เลขประจำตัวผู้เสียภาษี*/}
                                                                                    <div
                                                                                        className="col-12 col-md-6 nopadleft">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*<div className="row" style={{height: '15px'}}>
                                                                        </div>*/}

                                                                        {/*เลขประจำตัวผู้เสียภาษี*/}
                                                                        <div className="row mt-2 mb-2">
                                                                            <div className="col-12 col-md-2"
                                                                                 style={{paddingRight: 0}}>
                                                                                <label
                                                                                    className="label required-label">เลขประจำตัวผู้เสียภาษี</label>
                                                                            </div>
                                                                            <div className="col-12 col-md-9">
                                                                                <input
                                                                                    value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID] || ''}
                                                                                    onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID, true)}
                                                                                    type="tel"
                                                                                    placeholder="เลขประจำตัวผู้เสียภาษี"
                                                                                    className="form-control input-md"/>
                                                                                <ErrorLabel
                                                                                    value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID]}/>
                                                                            </div>
                                                                        </div>

                                                                        {/*เบอร์โทรศัพท์, อีเมล*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*เบอร์โทรศัพท์*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">เบอร์โทรศัพท์</label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_PHONE] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_PHONE, true)}
                                                                                            type="tel"
                                                                                            placeholder="กรอกเบอร์โทรศัพท์"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_PHONE]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*อีเมล*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">อีเมล
                                                                                            <span
                                                                                                className="engtxt">(ใช้เป็น Username)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_EMAIL] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_EMAIL, false)}
                                                                                            onKeyDown={e => {
                                                                                                if (e.key === ' ') {
                                                                                                    e.preventDefault();
                                                                                                }
                                                                                            }}
                                                                                            type="email"
                                                                                            placeholder="กรอกอีเมล"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_EMAIL]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {/*รหัสผ่าน, ยืนยันรหัสผ่าน*/}
                                                                        <div className="row">
                                                                            <div className="col-md-6">
                                                                                {/*รหัสผ่าน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">รหัสผ่าน
                                                                                            <span
                                                                                                className="engtxt">(Password)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_PASSWORD] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_PASSWORD, false)}
                                                                                            type="password"
                                                                                            placeholder="กรอกรหัสผ่าน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_PASSWORD]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="col-md-6">
                                                                                {/*ยืนยันรหัสผ่าน*/}
                                                                                <div className="row">
                                                                                    <div className="col-md-4">
                                                                                        <label
                                                                                            className="label required-label">ยืนยันรหัสผ่าน
                                                                                            <span
                                                                                                className="engtxt">(Confirm password)</span></label>
                                                                                    </div>
                                                                                    <div className="col-md-8">
                                                                                        <input
                                                                                            value={this.state.fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] || ''}
                                                                                            onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_CONFIRM_PASSWORD, false)}
                                                                                            type="password"
                                                                                            placeholder="กรอกรหัสผ่านอีกครั้งเพื่อยืนยัน"
                                                                                            className="form-control input-md"/>
                                                                                        <ErrorLabel
                                                                                            value={this.state.errors[REGISTER_ORGANIZATION_CONFIRM_PASSWORD]}/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <br/>
                                                                        <button type="submit" value="submit"
                                                                                className="btn btn-ss"
                                                                                style={{marginBottom: '15px'}}>
                                                                            สมัครสมาชิก
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Modal.Body>
                        </Modal>
                    </div>
                </div>

                <Dialog message={dialog.message}
                        textColor={dialog.textColor}
                        isOpen={dialog.isOpen}
                        onCloseCallback={dialog.onCloseCallback}/>

                <style jsx>{`
                    .icon_top {
                        display: inline-block;
                        padding-left: 7px;
                        margin-left: 12px;
                        position: relative;
                        vertical-align: top;
                        padding-bottom: 8px;
                        padding-top: 8px;
                        position: relative;
                        height: 20px;
                    }
                    
                    .icon_top::before {
                        content: "";
                        border-left: 1px solid black;
                        position: absolute;
                        left: 0;
                        opacity: 0.4;
                        height: 15px;
                        top: 15px;
                    }
                    
                    .icon_top a {
                        display: inline-block;
                        margin-left: 7px;
                        vertical-align: middle;
                    }
                    
                    .icon_top i {
                        font-size: 0.8em;
                    }
                    
                    .link {
                        color: #666;
                        font-size: 1.0em !important;
                        letter-spacing: 0 !important;
                        text-decoration: none;
                    }
                    
                    .link:hover {
                        color: black;
                        text-decoration: none;
                    }
                    
                    .c-tab {
                        display: flex;
                        flex-wrap: wrap;
                    }
                    
                    .c-tab__trigger {
                        order: -1;
                        margin-bottom: 20px;
                    }
                    
                    .c-tab__content {
                        display: none;
                        flex: 1 0 100%;
                    }
                    
                    .c-tab__content.is-active {
                        display: block;
                        text-align: center;
                    }
                `}</style>
            </div>
        );
    }
}

export default class Header extends React.Component {
    constructor(props, context) {
        super(props, context);

        this.state = {};
    }

    componentDidMount() {
        /*$(document).ready(function () {
            $('input:not(:checked)').parent().find(".contentpay").slideUp();
            $('input:checked').parent().find(".contentpay").slideDown();
            $('input').click(function () {
                $('input:not(:checked)').parent().find(".contentpay").slideUp();
                $('input:checked').parent().find(".contentpay").slideDown();
            });
            $(".modal-signin-up-next").click(function () {
                var step = $(this).attr("step");
                if (!isNaN(step)) {
                    if (step == 2 || step == 3) {
                        if ($(window).width() >= 900) {
                            $('#myModal').find(".modal-dialog").css("width", "900");
                        }
                    } else {
                        $('#myModal').find(".modal-dialog").css("width", "");
                    }
                    $(".modal-signin-up").addClass("hide");
                    $(".modal-signin-up[step='" + step + "']").removeClass("hide");
                } else {
                    $('#myModal').modal("toggle");
                    setTimeout(function () {
                        $(".modal-signin-up").addClass("hide");
                        $(".modal-signin-up[step='1']").removeClass("hide");
                    }, 2000);
                }
            });
        });*/

        $(window).scroll(function () {
            if ($(this).scrollTop() > 25) {
                $('.wrap_menu').addClass("sticky");
            } else {
                $('.wrap_menu').removeClass("sticky");
            }
        });
    }

    render() {
        return (
            <div>
                <div className="mainnavbar">
                    <div className="container">
                        <div className="row">
                            <div className="col">
                                <div className="top_bar">
                                    <div className="fontsize_btn">
                                        <div className="fontsize_s">A</div>
                                        <div className="fontsize_m">A</div>
                                        <div className="fontsize_l">A</div>
                                    </div>
                                    <LoginForm/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="container-fluid wrap_menu">
                        <div className="row">
                            <div className="col-8 col-sm-4 logo">
                                <a href="/">
                                    <img src="/static/images/logo_icess_LOGO%20ICT.svg" className="img-fluid"/>
                                </a>
                            </div>
                            <div className="col-4 col-sm-8 mainmenu">
                                <div className="btn_menu">
                                    <div className="btn_menu_line"></div>
                                    <div className="btn_menu_text">MENU</div>
                                </div>
                                <ul>
                                    {/*บริการ*/}
                                    <li className="hassub"><a href="#">บริการ</a>
                                        <div className="submenu">
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-xs-12 col-sm-3 submenu_left">
                                                        <hgroup>
                                                            <h1>SERVICES</h1>
                                                            <h2>บริการ</h2>
                                                        </hgroup>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-6 submenu_mid">
                                                        <ul className="submenu_mid_list">
                                                            <li><a href="/service-training"
                                                                   target="_parent">บริการฝึกอบรม</a>
                                                            </li>
                                                            <li><a href="service-4.php" target="_parent">IN-HOUSE
                                                                Training</a></li>
                                                            <li><a href="/service-social" target="_parent">บริการสังคม</a>
                                                            </li>
                                                            <li><a href="/service-driving-license"
                                                                   target="_parent">บริการอบรมภาคทฤษฎีเพื่อขอใบอนุญาตขับขี่</a>
                                                            </li>
                                                            <li><a href="service-5.php"
                                                                   target="_parent">วิจัยและวิชาการ</a>
                                                            </li>
                                                            <li><a href="#" target="_parent">วารสาร HR Intelligence</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-3 submenu_right"><img
                                                        src="/static/images/mission4.jpg"/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    {/*แนะนำหน่วยงาน*/}
                                    <li className="hassub"><a href="#">แนะนำหน่วยงาน</a>
                                        <div className="submenu">
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-xs-12 col-sm-3 submenu_left">
                                                        <hgroup>
                                                            <h1>INSTITUTE</h1>
                                                            <h2>แนะนำหน่วยงาน</h2></hgroup>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-6 submenu_mid">
                                                        <ul className="submenu_mid_list">
                                                            <li><a href="vision.php"
                                                                   target="_parent">เกี่ยวกับองค์กร</a>
                                                            </li>
                                                            <li><a href="mission.php" target="_parent">ภารกิจ </a></li>
                                                            <li><a href="structure.php"
                                                                   target="_parent"> โครงสร้างองค์กร </a></li>
                                                            <li><a href="executive.php"
                                                                   target="_parent"> ผู้บริหารและบุคลากร</a></li>
                                                        </ul>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-3 submenu_right"><img
                                                        src="/static/images/mission2.jpg"/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    {/*เอกสารดาวน์โหลด*/}
                                    <li className="hassub"><a href="#">เอกสารดาวน์โหลด</a>
                                        <div className="submenu">
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-xs-12 col-sm-3 submenu_left">
                                                        <hgroup>
                                                            <h1>DOWNLOAD</h1>
                                                            <h2>เอกสารดาวน์โหลด</h2></hgroup>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-6 submenu_mid">
                                                        <ul className="submenu_mid_list">
                                                            <li><a href="download-page.php">เอกสารการอบรม </a></li>
                                                            <li><a href="download-page2.php">รายงานผลการดำเนินงาน</a>
                                                            </li>
                                                            <li><a href="download-page3.php">KM</a></li>
                                                            <li><a href="download-page5.php">ประกันคุณภาพ(QA)</a></li>
                                                            <li><a href="download-page4.php"> จุลสาร/อินโฟกราฟิค</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div className="col-xs-12 col-sm-3 submenu_right"><img
                                                        src="/static/images/mission1.jpg"/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    {/*FAQ*/}
                                    <li className="hassub"><a href="faq.php">FAQ</a></li>
                                    {/*ติดต่อ*/}
                                    <li className="hassub"><a href="contact.php">ติดต่อ</a></li>
                                    {/*Link*/}
                                    <li className="hassub"><a href="#"> Link</a>
                                        <div className="submenu">
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-xs-12 col-sm-3 submenu_left">
                                                        <hgroup>
                                                            <h1>INNOVATION</h1>
                                                            <h2>STRATEGIC</h2></hgroup>
                                                        <p>Lorem Ipsum is simply dummy text of the printing and
                                                            typesetting
                                                            industry.</p> <a href="#">รายละเอียด</a></div>
                                                    <div className="col-xs-12 col-sm-6 submenu_mid">
                                                        <ul className="submenu_mid_list">
                                                            <li><a href="#">Area-baseb Innovation</a></li>
                                                            <li><a href="#">Value Chain Innovation</a>
                                                                <ul>
                                                                    <li><a href="#">Innovation for Economy</a></li>
                                                                    <li><a href="#">Innovation for Society</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="#">Innovation Capability</a></li>
                                                            <li><a href="#">Innovation Network</a></li>
                                                            <li><a href="#">Market Innovation</a></li>
                                                            <li><a href="#">Innovation Informatics</a></li>
                                                        </ul>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    {/*สถานะการลงทะเบียน*/}
                                    <li><a href="checkstatus.php" className="bgyellow_btn">สถานะการลงทะเบียน </a></li>
                                    {/*ส่งหลักฐานการโอนเงิน*/}
                                    <Link href="/upload-slip">
                                        <li><a href="javascript:void(0)" className="bgyellow_btn">ส่งหลักฐานโอนเงิน</a></li>
                                    </Link>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr/>
                </div>

                <style jsx>{`
                    .modal-open {
                        overflow: hidden;
                        position: fixed;
                    }
                    
                    .wrap_menu {
                        background-color: #fff;
                        padding: 0px 10%;
                        position: relative;
                        x-webkit-transition: all 0.5s ease-in-out;
                        x-moz-transition: all 0.5s ease-in-out;
                        x-o-transition: all 0.5s ease-in-out;
                        xtransition: all 0.5s ease-in-out;
                        width: 100%;
                    }
                    
                    .wrap_menu.sticky {
                        position: fixed;
                        z-index: 999;
                        background-color: rgba(255, 255, 255, 0.95);
                        right: 0;
                        top: 0;
                        left: 0;
                        padding: 8px 5% 0;
                        border-bottom: 1px solid #eee;
                        height: 80px;
                    }
                    
                    .wrap_menu.sticky .logo img {
                        margin-top: 6px;
                        width: 60%;
                    }
                                        
                    .dropdown-menu a {
                        width: 100%;
                    }
                    
                    .dropdown {
                        position: relative;
                        display: inline-block;
                        vertical-align: top;
                        margin-left: 7px;
                    }
                    
                    .dropdown-inner {
                        position: absolute;
                        opacity: 0;
                        visibility: hidden;
                        z-index: 2;
                    }
                    
                    .dropdown-menu:after {
                        bottom: 100%;
                        right: 40px;
                        border: solid transparent;
                        content: " ";
                        height: 0;
                        width: 0;
                        position: absolute;
                        pointer-events: none;
                        border-bottom-color: #b40303;
                        border-width: 10px;
                        margin-left: -8px;
                    }
                    
                    .dropdown-hover:hover .dropdown-inner,
                    .dropdown-open:checked ~ .dropdown-inner {
                        opacity: 1;
                        visibility: visible;
                    }
                    
                    .dropdown-hover:hover ~ .dropdown-overlay,
                    .dropdown-open:checked ~ .dropdown-overlay {
                        display: block;
                    }
                    
                    .dropdown-register {
                        position: absolute;
                        width: 300px;
                        border: 2px solid #b40303;
                        background: #fff;
                        transition: opacity .3s, visibility .3s;
                        font-size: 1em;
                        letter-spacing: 0px;
                        z-index: 99999;
                        left: -100px;
                        padding: 20px;
                        border-radius: 15px;
                        top: -2px;
                    }
                    
                    .btn-top {
                        float: left;
                    }
                    
                    .btn-primary {
                        background-color: #ffd440;
                        border: none;
                        border-radius: 0px;
                        color: #252525;
                    }
                    
                    .mainnavbar {
                        position: relative;
                    }
                    
                    .top_bar {
                        height: 45px;
                        text-align: right;
                        float: right;
                    }
                    
                    .logo > a img {
                        width: 85%;
                    }
                    
                    .logo {
                        text-align: left;
                    }
                    
                    .logo a {
                        display: inline-block;
                        position: relative;
                        margin-top: 0px;
                    }
                    
                    .logo a img {
                        max-width: 100%;
                        max-height: 100px;
                        height: auto;
                        display: block;
                    }
                    
                    .mainmenu {
                        text-align: right;
                        padding-right: 0;
                        position: static;
                    }
                    
                    .mainmenu ul {
                        display: block;
                        padding: 0;
                        margin-bottom: 0;
                        margin-top: 15px;
                    }
                    
                    .mainmenu ul li {
                        display: inline-block;
                        margin-left: 15px;
                        padding-bottom: 32px;
                    }
                    
                    .fz_l .mainmenu ul li {
                        margin-left: 18px;
                    }
                    
                    .mainmenu ul li a {
                        font-size: 1rem;
                        display: block;
                        color: #000;
                        text-decoration: none;
                        x-webkit-transition: all 0.5s ease-in-out;
                        x-moz-transition: all 0.5s ease-in-out;
                        x-o-transition: all 0.5s ease-in-out;
                        xtransition: all 0.5s ease-in-out;
                    }
                    
                    .mainmenu ul li a:hover {
                        color: #b40303;
                    }
                    
                    .btn_menu {
                        display: none;
                    }
                    
                    .submenu {
                        position: absolute;
                        left: 0;
                        width: 100%;
                        top: 100%;
                        border-top: 4px solid #b40303;
                        border-bottom: 4px solid #b40303;
                        background-color: #FFF;
                        text-align: left;
                        color: #000;
                    }
                    
                    .submenu_left {
                        border-right: 1px solid #9d9b9b;
                        margin-top: 40px;
                        margin-bottom: 15px;
                    }
                    
                    .submenu_left h1 {
                        font-size: 2.5rem;
                        line-height: 0.7;
                        margin-bottom: 0;
                    }
                    
                    .submenu_left h2 {
                        font-weight: 300;
                        font-size: 2rem;
                        line-height: 0.7;
                        margin-top: 0;
                    }
                    
                    .submenu_left p {
                        color: #aeacac;
                        font-size: 0.8rem;
                        line-height: 0.9;
                    }
                    
                    .mainmenu ul li .submenu_left a {
                        display: inline-block;
                        font-size: 0.8rem;
                        border: 1px solid #9b9b9b;
                        color: #858484;
                        padding: 5px 20px 3px 20px;
                        margin-top: 15px;
                    }
                    
                    .mainmenu ul li .submenu_left a:hover {
                        color: #b40303;
                        border: 1px solid #b40303;
                    }
                    
                    .submenu_right img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }
                    
                    .submenu_mid ul.submenu_mid_list {
                        columns: 2;
                        -webkit-columns: 2;
                        -moz-columns: 2;
                    }
                    
                    .submenu_mid ul.submenu_mid_list > li {
                        padding: 0;
                        background-repeat: no-repeat;
                        background-position: left 9px;
                        padding-left: 17px;
                        display: block;
                        -webkit-column-break-inside: avoid;
                        page-break-inside: avoid;
                        break-inside: avoid-column;
                    }
                    
                    .submenu_mid ul.submenu_mid_list > li > a {
                        color: black;
                    }
                    
                    .submenu_mid {
                        margin-bottom: 15px;
                    }
                    
                    .submenu_mid ul li a {
                        font-weight: 500;
                        color: #000;
                        text-decoration: none;
                    }
                    
                    .submenu_mid ul li ul {
                        margin: 0;
                        list-style: disc;
                        padding-left: 0px;
                    }
                    
                    .submenu_mid ul li ul li {
                        color: #818181;
                        display: list-item;
                        padding: 0;
                    }
                    
                    .submenu_mid ul li ul li a {
                        font-size: 0.9rem;
                        font-weight: normal;
                        color: #000;
                    }
                    
                    .submenu_mid ul li a:hover {
                        color: #b40303;
                    }
                    
                    .fontsize_btn {
                        display: inline-block;
                        vertical-align: middle;
                        margin-top: 5px;
                        padding-top: 5px;
                        position: relative;
                        padding-left: 7px;
                        margin-left: 12px;
                        float: left;
                    }
                    
                    .fontsize_btn::before {
                        content: "";
                        border-left: 1px solid #fff;
                        position: absolute;
                        left: 0;
                        opacity: 0.4;
                        height: 15px;
                        top: 8px;
                    }
                    
                    .fontsize_btn div {
                        display: inline-block;
                        color: #252525;
                        font-weight: 500;
                        cursor: pointer;
                        line-height: 26px;
                        padding: 0 2px;
                        margin-left: 1px;
                    }
                    
                    .fontsize_s {
                        font-size: 18px;
                    }
                    
                    .fontsize_m {
                        font-size: 24px;
                    }
                    
                    .fontsize_l {
                        font-size: 35px;
                    }
                    
                    @media (min-width: 992px) {
                        .submenu {
                            z-index: -9;
                            opacity: 0;
                            visibility: hidden;
                            x-webkit-transition: all 0.5s ease-in-out;
                            x-moz-transition: all 0.5s ease-in-out;
                            x-o-transition: all 0.5s ease-in-out;
                            xtransition: all 0.5s ease-in-out;
                        }
                    
                        .hassub:hover .submenu {
                            opacity: 1.0;
                            z-index: 99998;
                            visibility: visible;
                        }
                    }
                    
                    @media (max-width: 1600px) {
                        .wrap_menu {
                            padding: 0px 5%;
                        }
                    }
                    
                    @media (max-width: 1440px) {
                        .wrap_menu {
                            padding: 0px 5%;
                        }
                    }

                    @media (max-width: 1280px) {
                        .wrap_menu {
                            padding: 0px 5%;
                        }
                    }
                    
                    @media (max-width: 1199px) {
                        .mainmenu {
                            padding-right: 15px;
                        }
                    
                        .mainmenu ul li a {
                            font-size: 0.9rem;
                        }
                    
                        .mainmenu ul li {
                            margin-left: 10px;
                        }

                        .logo a {
                            margin-top: 5px;
                        }
                    
                        .wrap_menu.sticky .logo img {
                            width: 80%;
                        }
                    
                        .wrap_menu {
                            padding: 0px 4%;
                        }
                    }
                    
                    @media (max-width: 991px) {
                        .logo a {
                            margin-top: 10px;
                        }
                    
                        .wrap_menu.sticky .logo img {
                            width: 100%;
                        }
                    
                        .dropdown-inner {
                            right: 0;
                            width: auto;
                        }
                    
                        .btn_menu {
                            display: inline-block;
                            margin-top: 20px;
                        }
                    
                        .btn_menu .btn_menu_line {
                            width: 25px;
                            display: inline-block;
                            padding-right: 5px;
                            padding-top: 1px;
                            vertical-align: middle;
                        }
                    
                        .btn_menu .btn_menu_line span {
                            display: block;
                            height: 3px;
                            background-color: #3a3a3a;
                            margin-bottom: 3px;
                        }
                    
                        .btn_menu .btn_menu_text {
                            display: inline-block;
                            font-size: 0.9rem;
                            font-weight: 500;
                            letter-spacing: 1px;
                            vertical-align: middle;
                        }
                    
                        .logo a img {
                            max-height: 70px;
                        }
                    
                        .mainmenu > ul {
                            position: absolute;
                            left: 0;
                            background-color: #FFF;
                            margin-top: 0;
                            top: 100%;
                            width: 100%;
                            padding-top: 15px;
                            padding-bottom: 15px;
                            border-top: 2px solid #b40303;
                            border-bottom: 2px solid #b40303;
                            display: none;
                        }
                    
                        .mainmenu > ul > li {
                            padding-bottom: 0;
                            display: block;
                            margin: 0;
                            text-align: left;
                        }
                    
                        .mainmenu > ul > li > a {
                            font-size: 1.2rem;
                            padding-left: 15px;
                            padding-right: 15px;
                        }
                    
                        .submenu {
                            position: static;
                            display: none;
                        }
                    
                        .submenu_left h1 {
                            font-size: 1.3rem;
                        }
                    
                        .submenu_left h2 {
                            font-size: 1.1rem;
                        }
                    
                        .submenu_mid ul.submenu_mid_list > li {
                            margin-left: 0;
                        }
                    
                        .submenu_left {
                            margin-top: 0;
                        }
                    }
                    
                    @media (max-width: 767px) {
                        .wrap_menu {
                            padding: 0px 4%;
                        }
                    
                        .wrap_menu.sticky .btn_menu {
                            margin-top: 15px;
                        }
                    
                        .wrap_menu.sticky .logo {
                            margin-top: 10px;
                        }
                    
                        .logo a {
                            margin-top: 0px;
                        }
                    
                        .wrap_menu.sticky .logo img {
                            width: 100%;
                        }
                    
                        .btn_menu {
                            margin-top: 5px;
                        }
                    
                        .logo a img {
                            width: 100%;
                        }
                    
                        .dropdown-inner {
                            right: 0;
                            width: auto;
                        }
                    
                        .btn-primary {
                            padding: 2px 10px;
                            font-size: 0.9rem;
                        }
                    
                        .submenu_mid ul.submenu_mid_list {
                            margin-top: 0;
                        }
                    
                        .submenu_right img {
                            max-width: 400px;
                        }
                    
                        .member-signin,
                        .fontsize_btn {
                            display: inline-block;
                            vertical-align: top;
                        }
                    
                        .fontsize_btn {
                            padding-top: 1px;
                            margin-left: 5px;
                            margin-top: 0px;
                        }
                    
                        .submenu_mid ul.submenu_mid_list {
                            columns: 1;
                            -webkit-columns: 1;
                            -moz-columns: 1;
                        }
                    
                        .submenu_right {
                            display: none;
                        }
                    
                        .top_bar {
                            height: auto;
                            x-webkit-transition: all 0.5s ease-in-out;
                            x-moz-transition: all 0.5s ease-in-out;
                            x-o-transition: all 0.5s ease-in-out;
                            xtransition: all 0.5s ease-in-out;
                        }
                    
                        .top_bar.active {
                            padding-bottom: 30px;
                        }
                    
                        .submenu_left {
                            display: none;
                        }
                    
                        .submenu_mid {
                            padding-top: 10px;
                        }
                    
                        .icon_top {
                            padding-top: 0px !important;
                        }
                    }
                    
                    @media (max-width: 425px) {
                        /*
                                .icon_top {
                                    margin-top: -35px;
                                }
                        */
                    }
                    
                    @media (max-width: 375px) {
                        .icon_top {
                            margin-top: 0px;
                        }
                    }
                `}</style>
            </div>
        );
    }
}