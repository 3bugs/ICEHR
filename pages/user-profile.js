import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import ErrorLabel from '../components/ErrorLabel';
import Dialog from "../components/Dialog";
import {getLoginUser, setLoginUser, isString, isValidEmail, isPositiveInteger, getDateFormatFromDateObject, formatCourseDateShort, numberWithCommas} from "../etc/utils";
import DatePicker from "react-datepicker";
import {Element, scroller} from "react-scroll";
import {SERVICE_TRAINING, SERVICE_SOCIAL, SERVICE_DRIVING_LICENSE, LIMIT_PER_PAGE} from "../etc/constants";
import Link from "next/link";
import ReactPaginate from "react-paginate";

const KEY_LOCAL_STORAGE_ACTIVE_TAB = 'key-local-storage-active-tab';
const ORGANIZATION_TYPE_OTHER = 9999;

const REGISTER_TYPE_PERSON = 'person';
const REGISTER_TYPE_ORGANIZATION = 'organization';

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

export default class UserProfile extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            activeTab: 1,
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
    }

    static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';

        const res = await fetch(baseUrl + '/api/get_name_title', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({}),
        });

        const result = await res.json();
        if (result.error.code === 0) {
            const nameTitleList = result.dataList;

            const resOrganizationType = await fetch(baseUrl + '/api/get_organization_type', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({}),
            });

            const resultOrganizationType = await resOrganizationType.json();
            if (resultOrganizationType.error.code === 0) {
                const organizationTypeList = resultOrganizationType.dataList;

                return {
                    nameTitleList,
                    organizationTypeList,
                };
            } else {
                return {
                    nameTitleList: null,
                    organizationTypeList: null,
                };
            }
        } else {
            return {
                nameTitleList: null,
                organizationTypeList: null,
            };
        }
    };

    componentDidMount() {
        const loginUser = getLoginUser();
        if (!loginUser) {
            window.location.href = "/";
            return;
        }

        const activeTab = localStorage.getItem(KEY_LOCAL_STORAGE_ACTIVE_TAB);
        if (activeTab) {
            this.setState({
                activeTab: parseInt(activeTab),
            });
        }

        fetch('/api/get_registration_by_member_id', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                memberId: loginUser.id,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        registrationList: result.dataList,
                        errorMessage: null,
                    });
                } else {
                    this.setState({
                        registrationList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            })
            .catch(error => {
                //alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
                this.setState({
                    registrationList: null,
                    errorMessage: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error,
                });
            });

        const fields = {};

        switch (loginUser.memberType) {
            case REGISTER_TYPE_PERSON:
                fields[REGISTER_PERSON_TITLE] = loginUser.title;
                fields[REGISTER_PERSON_FIRST_NAME] = loginUser.firstName;
                fields[REGISTER_PERSON_LAST_NAME] = loginUser.lastName;
                fields[REGISTER_PERSON_BIRTH_DATE] = new Date(loginUser.birthDate);
                fields[REGISTER_PERSON_JOB_POSITION] = loginUser.jobPosition;
                fields[REGISTER_PERSON_ORGANIZATION_NAME] = loginUser.organizationName;
                fields[REGISTER_PERSON_ORGANIZATION_TYPE] = loginUser.organizationType;
                fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM] = loginUser.organizationTypeCustom;
                fields[REGISTER_PERSON_PHONE] = loginUser.phone;
                fields[REGISTER_PERSON_EMAIL] = loginUser.email;
                break;
            case REGISTER_TYPE_ORGANIZATION:
                fields[REGISTER_ORGANIZATION_TITLE] = loginUser.title;
                fields[REGISTER_ORGANIZATION_FIRST_NAME] = loginUser.firstName;
                fields[REGISTER_ORGANIZATION_LAST_NAME] = loginUser.lastName;
                fields[REGISTER_ORGANIZATION_BIRTH_DATE] = new Date(loginUser.birthDate);
                fields[REGISTER_ORGANIZATION_JOB_POSITION] = loginUser.jobPosition;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME] = loginUser.organizationName;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE] = loginUser.organizationType;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM] = loginUser.organizationTypeCustom;
                fields[REGISTER_ORGANIZATION_PHONE] = loginUser.phone;
                fields[REGISTER_ORGANIZATION_EMAIL] = loginUser.email;

                fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS] = loginUser.address;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT] = loginUser.subDistrict;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT] = loginUser.district;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE] = loginUser.province;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE] = loginUser.postalCode;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE] = loginUser.organizationPhone;
                fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID] = loginUser.taxId;
                break;
        }

        this.setState({loginUser, fields});
    }

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

    /*handleSubmit = event => {
        event.preventDefault();

        if (this.validateForm()) {
            fetch('/api/reset_password', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: this.props.token,
                    newPassword: this.state.fields[NEW_PASSWORD],
                }),
            })
                .then(result => result.json())
                .then(result => {
                    if (result['error']['code'] === 0) {
                        this.setState({
                            successMessage: result['error']['message'],
                        });
                    } else {
                        alert(result['error']['message']);
                    }
                });
        } else {
            //todo: แสดง error บอกให้กรอกข้อมูล
        }
    };

    validateForm() {
        let {fields} = this.state;
        let errors = {};
        let formIsValid = true;

        if (!fields[NEW_PASSWORD] || fields[NEW_PASSWORD].trim().length < 6) {
            errors[NEW_PASSWORD] = 'กรอกรหัสผ่านใหม่ที่ต้องการ ความยาวอย่างน้อย 6 ตัวอักษร';
            formIsValid = false;
        } else if (fields[NEW_PASSWORD] !== fields[CONFIRM_NEW_PASSWORD]) {
            errors[NEW_PASSWORD] = 'รหัสผ่านใหม่ กับยืนยันรหัสผ่านใหม่ ไม่ตรงกัน';
            errors[CONFIRM_NEW_PASSWORD] = 'รหัสผ่านใหม่ กับยืนยันรหัสผ่านใหม่ ไม่ตรงกัน';
            formIsValid = false;
        }

        if (!fields[CONFIRM_NEW_PASSWORD] || fields[CONFIRM_NEW_PASSWORD].trim().length === 0) {
            errors[CONFIRM_NEW_PASSWORD] = 'กรอกรหัสผ่านใหม่อีกครั้งเพื่อยืนยัน';
            formIsValid = false;
        }

        this.setState({errors: errors});
        return formIsValid;
    }*/

    handleSubmitUpdateMember = (registerType, event) => {
        event.preventDefault();
        if (this.validateMemberForm(registerType)) {
            this.doUpdateMember(registerType);
        } else {
            /*this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                this.dismissDialog();
            });*/
        }
    };

    validateMemberForm(registerType) {
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
                /*if (!fields[REGISTER_PERSON_EMAIL] || fields[REGISTER_PERSON_EMAIL].trim().length === 0) {
                    errors[REGISTER_PERSON_EMAIL] = 'กรุณากรอกอีเมล';
                    formIsValid = false;
                } else if (!isValidEmail(fields[REGISTER_PERSON_EMAIL])) {
                    errors[REGISTER_PERSON_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
                    formIsValid = false;
                }*/
                /*if (!fields[REGISTER_PERSON_PASSWORD] || fields[REGISTER_PERSON_PASSWORD].trim().length < 6) {
                    errors[REGISTER_PERSON_PASSWORD] = 'กรุณากรอกรหัสผ่าน อย่างน้อย 6 ตัวอักษร';
                    formIsValid = false;
                }
                if (!fields[REGISTER_PERSON_CONFIRM_PASSWORD] || fields[REGISTER_PERSON_CONFIRM_PASSWORD].trim().length === 0) {
                    errors[REGISTER_PERSON_CONFIRM_PASSWORD] = 'กรุณากรอกรหัสผ่านอีกครั้งเพื่อยืนยัน';
                    formIsValid = false;
                } else if (fields[REGISTER_PERSON_PASSWORD] !== fields[REGISTER_PERSON_CONFIRM_PASSWORD]) {
                    errors[REGISTER_PERSON_CONFIRM_PASSWORD] = 'การยืนยันรหัสผ่านไม่ตรงกัน';
                    formIsValid = false;
                }*/
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
                /*if (!fields[REGISTER_ORGANIZATION_EMAIL] || fields[REGISTER_ORGANIZATION_EMAIL].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_EMAIL] = 'กรุณากรอกอีเมล';
                    formIsValid = false;
                } else if (!isValidEmail(fields[REGISTER_ORGANIZATION_EMAIL])) {
                    errors[REGISTER_ORGANIZATION_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
                    formIsValid = false;
                }*/
                /*if (!fields[REGISTER_ORGANIZATION_PASSWORD] || fields[REGISTER_ORGANIZATION_PASSWORD].trim().length < 6) {
                    errors[REGISTER_ORGANIZATION_PASSWORD] = 'กรุณากรอกรหัสผ่าน อย่างน้อย 6 ตัวอักษร';
                    formIsValid = false;
                }
                if (!fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] || fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD].trim().length === 0) {
                    errors[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] = 'กรุณากรอกรหัสผ่านอีกครั้งเพื่อยืนยัน';
                    formIsValid = false;
                } else if (fields[REGISTER_ORGANIZATION_PASSWORD] !== fields[REGISTER_ORGANIZATION_CONFIRM_PASSWORD]) {
                    errors[REGISTER_ORGANIZATION_CONFIRM_PASSWORD] = 'การยืนยันรหัสผ่านไม่ตรงกัน';
                    formIsValid = false;
                }*/
                break;
        }

        this.setState({errors: errors});
        return formIsValid;
    }

    doUpdateMember = (registerType) => {
        let {loginUser, fields} = this.state;

        let params = {};
        params['id'] = loginUser.id;

        switch (registerType) {
            case REGISTER_TYPE_PERSON:
                params['memberType'] = 'person';
                params['title'] = fields[REGISTER_PERSON_TITLE];
                params['firstName'] = fields[REGISTER_PERSON_FIRST_NAME];
                params['lastName'] = fields[REGISTER_PERSON_LAST_NAME];
                params['birthDate'] = getDateFormatFromDateObject(fields[REGISTER_PERSON_BIRTH_DATE]);
                params['jobPosition'] = fields[REGISTER_PERSON_JOB_POSITION];
                params['organizationName'] = fields[REGISTER_PERSON_ORGANIZATION_NAME];
                params['organizationType'] = fields[REGISTER_PERSON_ORGANIZATION_TYPE];
                params['organizationTypeCustom'] = fields[REGISTER_PERSON_ORGANIZATION_TYPE_CUSTOM];
                params['phone'] = fields[REGISTER_PERSON_PHONE];
                //params['email'] = fields[REGISTER_PERSON_EMAIL];
                //params['password'] = fields[REGISTER_PERSON_PASSWORD];
                break;
            case REGISTER_TYPE_ORGANIZATION:
                params['memberType'] = 'organization';
                params['title'] = fields[REGISTER_ORGANIZATION_TITLE];
                params['firstName'] = fields[REGISTER_ORGANIZATION_FIRST_NAME];
                params['lastName'] = fields[REGISTER_ORGANIZATION_LAST_NAME];
                params['birthDate'] = getDateFormatFromDateObject(fields[REGISTER_ORGANIZATION_BIRTH_DATE]);
                params['jobPosition'] = fields[REGISTER_ORGANIZATION_JOB_POSITION];
                params['organizationName'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_NAME];
                params['organizationType'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE];
                params['organizationTypeCustom'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TYPE_CUSTOM];
                params['phone'] = fields[REGISTER_ORGANIZATION_PHONE];
                //params['email'] = fields[REGISTER_ORGANIZATION_EMAIL];
                //params['password'] = fields[REGISTER_ORGANIZATION_PASSWORD];
                params['address'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_ADDRESS];
                params['subDistrict'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_SUB_DISTRICT];
                params['district'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_DISTRICT];
                params['province'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_PROVINCE];
                params['postalCode'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_POSTAL_CODE];
                params['organizationPhone'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_PHONE];
                params['taxId'] = fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID];
                break;
        }

        fetch('/api/update_member', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(params),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    let {memberData} = result;
                    setLoginUser(memberData);
                    alert(result['error']['message']);
                    location.reload();

                    /*this.setState({
                        fields: {},
                        showRegisterModal: false,
                        showLoginModal: true,
                    });*/
                } else {
                    //let errors = {};
                    //errors[RESULT_ERROR] = result['error']['message'];
                    //this.setState({errors: errors});
                    alert(result['error']['message']);
                }
            });
    };

    setDatePickerMinDate = () => {
        const d = new Date();
        const year = 1900;
        const month = 0;
        const day = 1;
        return new Date(year, month, day)
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
        const {nameTitleList, organizationTypeList} = this.props;
        const {activeTab, loginUser, fields, errors, dialog, errorMessage, registrationList} = this.state;

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3>
                                <a href="javascript:void(0)" onClick={() => {
                                    this.setState({activeTab: 1});
                                    localStorage.setItem(KEY_LOCAL_STORAGE_ACTIVE_TAB, '1');
                                }}><span style={{color: activeTab === 1 ? '#000' : '#ccc'}}>ข้อมูลสมาชิก</span></a>&nbsp;&nbsp;&nbsp;
                                <span style={{color: '#b40303'}}>|</span>&nbsp;&nbsp;&nbsp;
                                <a href="javascript:void(0)" onClick={() => {
                                    this.setState({activeTab: 2});
                                    localStorage.setItem(KEY_LOCAL_STORAGE_ACTIVE_TAB, '2');
                                }}><span style={{color: activeTab === 2 ? '#000' : '#aaa'}}>หลักสูตรที่สมัคร</span></a>
                            </h3>
                        </div>
                    </div>

                    <div className="mt-3">
                        {activeTab === 1 && loginUser && loginUser.memberType === REGISTER_TYPE_PERSON &&
                        <form id="personalRegisterForm" method="post"
                              onSubmit={this.handleSubmitUpdateMember.bind(this, REGISTER_TYPE_PERSON)}
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
                                                                    nameTitleList.map((nameTitle, index) =>
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
                                                                    organizationTypeList.map((organizationType, index) =>
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
                                                                disabled={true}
                                                                value={this.state.fields[REGISTER_PERSON_EMAIL] || ''}
                                                                onChange={this.handleChange.bind(this, REGISTER_PERSON_EMAIL, false)}
                                                                onKeyDown={e => {
                                                                    if (e.key === ' ') {
                                                                        e.preventDefault();
                                                                    }
                                                                }}
                                                                type="email"
                                                                placeholder="ไม่อนุญาตให้แก้ไขอีเมล"
                                                                className="form-control input-md"/>
                                                            <ErrorLabel
                                                                value={'ไม่อนุญาตให้แก้ไขอีเมล'}/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/*รหัสผ่าน, ยืนยันรหัสผ่าน*/}
                                            {/*<div className="row">
                                                <div className="col-md-6">
                                                    รหัสผ่าน
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
                                                    ยืนยันรหัสผ่าน
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
                                            </div>*/}

                                            <br/>
                                            <div style={{textAlign: 'center'}}>
                                                <button type="submit" value="submit"
                                                        className="btn btn-ss"
                                                        style={{marginTop: '5px', marginBottom: '15px'}}>
                                                    บันทึกการแก้ไข
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                        }

                        {activeTab === 1 && loginUser && loginUser.memberType === REGISTER_TYPE_ORGANIZATION &&
                        <form id="organizationRegisterForm" method="post"
                              onSubmit={this.handleSubmitUpdateMember.bind(this, REGISTER_TYPE_ORGANIZATION)}
                              noValidate={true}>
                            <div className="container">
                                <div className="row">
                                    <div className="col">
                                        <div className="regisfo2">

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
                                                                <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                {
                                                                    organizationTypeList.map((organizationType, index) =>
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
                                                                type="text"
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

                                            <div className="row" style={{height: '15px'}}>
                                            </div>

                                            {/*เลขประจำตัวผู้เสียภาษี*/}
                                            <div className="row mb-3">
                                                <div className="col-12 col-md-2"
                                                     style={{paddingRight: 0}}>
                                                    <label
                                                        className="label required-label">เลขประจำตัวผู้เสียภาษี</label>
                                                </div>
                                                <div className="col-12 col-md-9">
                                                    <input
                                                        value={this.state.fields[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID] || ''}
                                                        onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID, true)}
                                                        type="text"
                                                        maxLength={13}
                                                        placeholder="เลขประจำตัวผู้เสียภาษี"
                                                        className="form-control input-md"/>
                                                    <ErrorLabel
                                                        value={this.state.errors[REGISTER_ORGANIZATION_ORGANIZATION_TAX_ID]}/>
                                                </div>
                                            </div>

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
                                                                    nameTitleList.map((nameTitle, index) =>
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
                                                                disabled={true}
                                                                value={this.state.fields[REGISTER_ORGANIZATION_EMAIL] || ''}
                                                                onChange={this.handleChange.bind(this, REGISTER_ORGANIZATION_EMAIL, false)}
                                                                onKeyDown={e => {
                                                                    if (e.key === ' ') {
                                                                        e.preventDefault();
                                                                    }
                                                                }}
                                                                type="email"
                                                                placeholder="ไม่อนุญาตให้แก้ไขอีเมล"
                                                                className="form-control input-md"/>
                                                            <ErrorLabel
                                                                value={'ไม่อนุญาตให้แก้ไขอีเมล'}/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/*รหัสผ่าน, ยืนยันรหัสผ่าน*/}
                                            {/*<div className="row">
                                                <div className="col-md-6">
                                                    รหัสผ่าน
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
                                                    ยืนยันรหัสผ่าน
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
                                            </div>*/}

                                            <br/>
                                            <div style={{textAlign: 'center'}}>
                                                <button type="submit" value="submit"
                                                        className="btn btn-ss"
                                                        style={{marginTop: '5px', marginBottom: '15px'}}>
                                                    บันทึกการแก้ไข
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        }

                        {activeTab === 2 &&
                        <div className="container">
                            <div className="row">
                                <div className="col">
                                    <Element name={'topOfTable'}>
                                        <table className="table responsive-table table-forservice" style={{marginTop: '0px', fontSize: '1rem'}}>
                                            <thead>
                                            <tr>
                                                <th scope="col" style={{width: '14%'}}>เลขที่ใบสมัคร</th>
                                                <th scope="col" style={{width: '8%'}}>วันที่สมัคร</th>
                                                <th scope="col" style={{width: '16%'}}>ชื่อผู้สมัคร</th>
                                                <th scope="col" style={{width: '27%'}}>หลักสูตรที่สมัคร</th>
                                                <th scope="col" style={{width: '13%'}}>วันที่อบรม</th>
                                                <th scope="col" style={{width: '12%'}}>สถานะ</th>
                                                <th scope="col" style={{width: '10%'}}>แจ้งชำระเงิน</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {registrationList && registrationList.length > 0 &&
                                            registrationList.map((reg, index) => {
                                                let courseName = reg.courseTitle;
                                                if (reg.serviceType === SERVICE_TRAINING || reg.serviceType === SERVICE_SOCIAL) {
                                                    courseName += ` รุ่นที่ ${reg.courseBatchNumber}`;
                                                }
                                                let statusText = null;
                                                let statusTextColor = null;
                                                switch (reg.registerStatus) {
                                                    case 'start':
                                                        if (reg.serviceType === SERVICE_SOCIAL && reg.courseApplicationFee === 0) {
                                                            statusText = 'การลงทะเบียนสมบูรณ์';
                                                            statusTextColor = 'darkgreen';
                                                        } else {
                                                            statusText = 'รอชำระเงิน';
                                                            statusTextColor = 'orangered';
                                                        }
                                                        break;
                                                    case 'wait-approve':
                                                        statusText = 'เจ้าหน้าที่กำลังตรวจสอบข้อมูลการชำระเงิน';
                                                        statusTextColor = 'mediumblue';
                                                        break;
                                                    case 'complete':
                                                        statusText = 'การลงทะเบียนสมบูรณ์';
                                                        statusTextColor = 'darkgreen';
                                                        //const x = '<span style="color: mediumblue"></span>';
                                                        break;
                                                }
                                                return (
                                                    <tr className={'course-row'}>
                                                        <td>{reg.formNumber}</td>
                                                        <td style={{textAlign: 'center'}}>{formatCourseDateShort(reg.registerDate, reg.registerDate)}</td>
                                                        <td>{`${reg.traineeTitle}${reg.traineeFirstName} ${reg.traineeLastName}`}</td>
                                                        <td>
                                                            <Link
                                                                key={index}
                                                                as={`/service-${reg.serviceType}/${reg.courseId}`}
                                                                href={`/service-${reg.serviceType}?courseId=${reg.courseId}`}
                                                            >
                                                                {courseName}
                                                            </Link>
                                                        </td>
                                                        <td style={{textAlign: 'center'}}>{formatCourseDateShort(reg.courseBeginDate, reg.courseEndDate)}</td>
                                                        <td style={{textAlign: 'center'}}><span style={{color: statusTextColor}}>{statusText}</span></td>
                                                        <td style={{textAlign: 'center'}}>
                                                            <Link
                                                                key={index}
                                                                as={`/upload-slip/${reg.formNumber}`}
                                                                href={`/upload-slip?formNumber=${reg.formNumber}`}
                                                            >
                                                                แจ้งชำระเงิน
                                                            </Link>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                            }
                                            {registrationList && registrationList.length === 0 &&
                                            <tr className={'course-row'}>
                                                <td colSpan={20} style={{textAlign: 'center', padding: '20px'}}>
                                                    {/*{this.state.errorMessage}*/}
                                                    ไม่มีข้อมูล
                                                </td>
                                            </tr>
                                            }
                                            {!registrationList &&
                                            <tr className={'course-row'}>
                                                <td colSpan={20} style={{textAlign: 'center', color: 'red', padding: '20px'}}>
                                                    {this.state.errorMessage}
                                                </td>
                                            </tr>
                                            }
                                            </tbody>
                                        </table>
                                    </Element>
                                </div>
                            </div>

                            {/*<div style={{textAlign: 'center'}}>
                                <ReactPaginate
                                    initialPage={this.state.initialPage}
                                    previousLabel={'<'}
                                    nextLabel={'>'}
                                    breakLabel={'...'}
                                    breakClassName={'break-me'}
                                    pageCount={this.state.pageCount}
                                    marginPagesDisplayed={2}
                                    pageRangeDisplayed={5}
                                    onPageChange={this.handlePageClick}
                                    containerClassName={'pagination'}
                                    activeClassName={'pagination-active'}
                                    previousClassName={'pagination-older'}
                                    nextClassName={'pagination-newer'}
                                />
                            </div>*/}
                        </div>
                        }
                    </div>
                </div>

                <Dialog message={dialog.message}
                        textColor={dialog.textColor}
                        isOpen={dialog.isOpen}
                        onCloseCallback={dialog.onCloseCallback}/>

                <style jsx>{`
                    .text-title-top h3 {
                        width: 100%;
                        font-size: 2em;
                        text-align: center;
                        margin-top: 40px;
                    }
                    
                    td {
                        text-align: left;
                    }
                    
                    .course-row {
                        //cursor: pointer;
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
                    
                    @media (max-width: 480px) {
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

                    .btn-outline-secondary:hover {
                        background: #eee
                    }
                    
                    .ui-datepicker-div {
                        width: 100%;
                        left: 0px;
                    }
                    
                    .ui-widget {
                        font-size: 1em;
                    }
                    
                    .ui-widget .ui-widget {
                        font-size: 1em;
                    }
                    
                    .ui-widget input,
                        .ui-widget select,
                        .ui-widget textarea,
                        .ui-widget button {
                        font-size: 1em;
                    }
                    
                    .ui-widget-content {
                        border: 1px solid #555555;
                        background: white;
                        color: black;
                    }
                    
                    .ui-widget-content a {
                        color: black;
                    }
                    
                    .ui-datepicker .ui-datepicker-title {
                        color: white;
                    }
                    
                    .ui-widget-header {
                        border: 1px solid #fac4c7;
                        background-color: black;
                        font-weight: bold;
                        background-image: none;
                    }
                    
                    .ui-widget-header a {
                        color: black;
                    }
                    
                    .ui-state-default,
                    .ui-widget-content .ui-state-default,
                    .ui-widget-header .ui-state-default {
                        border: 1px solid #444444;
                        background: white;
                        font-weight: normal;
                        color: black;
                    }
                    
                    .ui-state-default a,
                        .ui-state-default a:link,
                        .ui-state-default a:visited {
                        color: black;
                        text-decoration: none;
                    }
                    
                    .ui-state-hover,
                    .ui-widget-content .ui-state-hover,
                    .ui-widget-header .ui-state-hover,
                    .ui-state-focus,
                    .ui-widget-content .ui-state-focus,
                    .ui-widget-header .ui-state-focus {
                        border: 1px solid #858585;
                        background: #858585;
                        font-weight: normal;
                        color: #ffffff;
                    }
                    
                    .ui-state-hover a,
                    .ui-state-hover a:hover {
                        color: black;
                        text-decoration: none;
                    }
                    
                    .ui-state-active,
                    .ui-widget-content .ui-state-active,
                    .ui-widget-header .ui-state-active {
                        border: 1px solid #858585;
                        background: #858585 font-weight: normal;
                        color: black;
                    }
                    
                    .ui-state-active a,
                    .ui-state-active a:link,
                    .ui-state-active a:visited {
                        color: black;
                        text-decoration: none;
                    }
                    
                    .ui-widget:active {
                        outline: none;
                    }
                    
                    .form-control-4,
                    .form-control-2 {
                        display: block;
                        width: 48.5%;
                        padding: 0.375rem 0.75rem;
                        line-height: 1.5;
                        color: #252525;
                        background-color: #fff;
                        background-clip: padding-box;
                        border: 1px solid #ced4da;
                        margin-bottom: 20px;
                        border-radius: 0.25rem;
                        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                    }
                    
                    .form-control-4 {
                        margin-bottom: 0px;
                    }
                    
                    .text-default {
                        margin-top: 5px;
                    }
                    
                    @media (max-width: 767.98px) {
                        .form-control-2 {
                            width: 100%;
                        }
                        .bg-gray {
                            padding: 0px;
                        }
                        .text-default {
                            padding: 20px;
                            margin-top: -25px;
                        }
                    }
                `}</style>
            </MainLayout>
        );
    }
}