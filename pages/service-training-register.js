import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";

const TOP_OF_FORM = 'topOfForm';

const REGISTER_TRAINEE_TITLE = 'registerTraineeTitle';
const REGISTER_TRAINEE_FIRST_NAME = 'registerTraineeFirstName';
const REGISTER_TRAINEE_LAST_NAME = 'registerTraineeLastName';
const REGISTER_TRAINEE_AGE = 'registerTraineeAge';
const REGISTER_TRAINEE_JOB_POSITION = 'registerTraineeJobPosition';
const REGISTER_TRAINEE_ORGANIZATION_NAME = 'registerTraineeOrganizationName';
const REGISTER_TRAINEE_ORGANIZATION_TYPE = 'registerTraineeOrganizationType';
const REGISTER_TRAINEE_PHONE = 'registerTraineePhone';
const REGISTER_TRAINEE_EMAIL = 'registerTraineeEmail';

const REGISTER_COORDINATOR_TITLE = 'registerCoordinatorTitle';
const REGISTER_COORDINATOR_FIRST_NAME = 'registerCoordinatorFirstName';
const REGISTER_COORDINATOR_LAST_NAME = 'registerCoordinatorLastName';
const REGISTER_COORDINATOR_AGE = 'registerCoordinatorAge';
const REGISTER_COORDINATOR_JOB_POSITION = 'registerCoordinatorJobPosition';
const REGISTER_COORDINATOR_ORGANIZATION_NAME = 'registerCoordinatorOrganizationName';
const REGISTER_COORDINATOR_ORGANIZATION_TYPE = 'registerCoordinatorOrganizationType';
const REGISTER_COORDINATOR_PHONE = 'registerCoordinatorPhone';
const REGISTER_COORDINATOR_EMAIL = 'registerCoordinatorEmail';

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

class TraineeRegisterForm extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            red: false,
        };
    }

    handleChange(formId, field, e) {
        this.props.handleChangeCallback(formId, field, e);
    }

    onClickRemove = () => {
        this.setState({
            //red: true,
        });
        this.props.onClickRemoveCallback(this.props.formData.id, (isRemoved) => {
            if (!isRemoved) {
                this.setState({
                    red: false,
                });
            }
        });
    };

    render() {
        let {formData} = this.props;
        return (
            <Element name={formData.id}>
                <div className="container_reg" style={{border: '0px solid red', position: 'relative', top: '-10px'}}>
                    <div className="row field_reg">
                        <div className="col">
                            <div className={this.state.red ? 'border-inside-red' : 'border-inside'}>
                                <a href="javascript:void(0);" onClick={this.onClickRemove}>
                                    <div className="deletenumber">x</div>
                                </a>
                                <div className="row">
                                    <div className="col-12 col-md-1 d-block d-sm-block d-md-block d-lg-block d-xl-block">
                                        <div className="list-add">ลำดับที่ <span id="sequence">{formData.id}</span></div>
                                    </div>

                                    <div className="col-12 col-md-11">
                                        <div className="row">
                                            <div className="col">
                                                <div className="regisfo">
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">คำนำหน้าชื่อ</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <select value={formData.fields[REGISTER_TRAINEE_TITLE] || '0'}
                                                                            onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_TITLE)}
                                                                            className="form-control"
                                                                            style={{marginBottom: 0}}>
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
                                                                        value={formData.errors[REGISTER_TRAINEE_TITLE]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">

                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">ชื่อ</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_FIRST_NAME] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_FIRST_NAME)}
                                                                           type="text"
                                                                           placeholder="กรอกชื่อ"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_FIRST_NAME]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">นามสกุล</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_LAST_NAME] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_LAST_NAME)}
                                                                           type="text"
                                                                           placeholder="กรอกนามสกุล"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_LAST_NAME]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">อายุ</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_AGE] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_AGE)}
                                                                           type="number"
                                                                           placeholder="กรอกอายุ"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_AGE]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">ตำแหน่งงาน</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_JOB_POSITION] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_JOB_POSITION)}
                                                                           type="text"
                                                                           placeholder="กรอกตำแหน่งงาน"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_JOB_POSITION]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">ชื่อหน่วยงาน</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_ORGANIZATION_NAME] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_ORGANIZATION_NAME)}
                                                                           type="text"
                                                                           placeholder="กรอกชื่อหน่วยงาน"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_ORGANIZATION_NAME]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">ประเภทหน่วยงาน</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <select value={formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE] || '0'}
                                                                            onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_ORGANIZATION_TYPE)}
                                                                            className="form-control">
                                                                        <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                        {
                                                                            this.props.organizationTypeList.map((organizationType, index) =>
                                                                                <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                            )
                                                                        }
                                                                    </select>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_ORGANIZATION_TYPE]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">เบอร์โทรศัพท์</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_PHONE] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_PHONE)}
                                                                           type="tel"
                                                                           placeholder="กรอกเบอร์โทรศัพท์"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_PHONE]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">อีเมล</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <input value={formData.fields[REGISTER_TRAINEE_EMAIL] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_EMAIL)}
                                                                           type="email"
                                                                           placeholder="กรอกอีเมล"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_EMAIL]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Element>
        );
    }
}

export default class ServiceTrainingRegister extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            step: 1,
            traineeForms: [
                {
                    id: 1,
                    fields: {},
                    errors: {}
                }
            ],
            coordinatorForm: {
                fields: {},
                errors: {}
            },
            receiptForm: {
                fields: {},
                errors: {}
            },
            nameTitleList: [],
            organizationTypeList: [],
        };
    }

    static getInitialProps = async function ({req, query}) {
        let course = null;
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
                    courseId: query.courseId
                }),
            });
            const result = await res.json();
            if (result['error']['code'] === 0) {
                course = result['dataList'][0];
                errorMessage = null;
            } else {
                course = null;
                errorMessage = result['error']['message'];
            }
            return {course, errorMessage};
        }
    };

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

    componentDidMount() {
        console.log('ServiceTrainingRegister componentDidMount() - ' + Math.random());

        this.doGetNameTitle();
        this.doGetOrganizationType();

        let user = getLoginUser();
        let initialTraineeFields = {};
        let initialCoordinatorFields = {};
        let initialReceiptFields = {};

        if (user !== null) {
            initialTraineeFields[REGISTER_TRAINEE_TITLE] = user.title;
            initialTraineeFields[REGISTER_TRAINEE_FIRST_NAME] = user.firstName;
            initialTraineeFields[REGISTER_TRAINEE_LAST_NAME] = user.lastName;
            initialTraineeFields[REGISTER_TRAINEE_AGE] = user.age;
            initialTraineeFields[REGISTER_TRAINEE_JOB_POSITION] = user.jobPosition;
            initialTraineeFields[REGISTER_TRAINEE_ORGANIZATION_NAME] = user.organizationName;
            initialTraineeFields[REGISTER_TRAINEE_ORGANIZATION_TYPE] = user.organizationType;
            initialTraineeFields[REGISTER_TRAINEE_PHONE] = user.phone;
            initialTraineeFields[REGISTER_TRAINEE_EMAIL] = user.email;

            initialCoordinatorFields[REGISTER_COORDINATOR_TITLE] = user.title;
            initialCoordinatorFields[REGISTER_COORDINATOR_FIRST_NAME] = user.firstName;
            initialCoordinatorFields[REGISTER_COORDINATOR_LAST_NAME] = user.lastName;
            initialCoordinatorFields[REGISTER_COORDINATOR_AGE] = user.age;
            initialCoordinatorFields[REGISTER_COORDINATOR_JOB_POSITION] = user.jobPosition;
            initialCoordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_NAME] = user.organizationName;
            initialCoordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE] = user.organizationType;
            initialCoordinatorFields[REGISTER_COORDINATOR_PHONE] = user.phone;
            initialCoordinatorFields[REGISTER_COORDINATOR_EMAIL] = user.email;

            initialReceiptFields[REGISTER_RECEIPT_ADDRESS] = user.address;
            initialReceiptFields[REGISTER_RECEIPT_SUB_DISTRICT] = user.subDistrict;
            initialReceiptFields[REGISTER_RECEIPT_DISTRICT] = user.district;
            initialReceiptFields[REGISTER_RECEIPT_PROVINCE] = user.province;
            initialReceiptFields[REGISTER_RECEIPT_POSTAL_CODE] = user.postalCode;
            initialReceiptFields[REGISTER_RECEIPT_ORGANIZATION_PHONE] = user.organizationPhone;
            initialReceiptFields[REGISTER_RECEIPT_TAX_ID] = user.taxId;
        }

        let {traineeForms, coordinatorForm, receiptForm} = this.state;
        traineeForms[0].fields = initialTraineeFields;
        coordinatorForm.fields = initialCoordinatorFields;
        receiptForm.fields = initialReceiptFields;
        this.setState({traineeForms, coordinatorForm});

        //alert('Course ID: ' + this.props.course.id + ', Step: ' + this.props.step);

        /*if (this.props.query.courseId === undefined) {
            this.setState({
                course: null,
                errorMessage: 'ไม่มีข้อมูล',
            });
            return;
        }*/
    }

    onClickAddTrainee = () => {
        let {traineeForms} = this.state;
        traineeForms.push(
            {
                id: traineeForms.length + 1,
                fields: {},
                errors: {}
            }
        );
        this.setState({traineeRegisterForms: traineeForms});
    };

    onClickRemoveTrainee = (id, callback) => {
        let {traineeForms} = this.state;
        if (traineeForms.length === 1) {
            //alert('');
            return;
        }
        if (confirm('ยืนยันลบลำดับที่ ' + id + ' ?')) {
            for (let i = 0; i < traineeForms.length; i++) {
                if (traineeForms[i].id === id) {
                    traineeForms.splice(i, 1);
                }
            }
            for (let i = 0; i < traineeForms.length; i++) {
                traineeForms[i].id = i + 1;
            }
            this.setState({traineeRegisterForms: traineeForms});
        } else {
            callback(false);
        }
    };

    handleChange = (formId, field, e) => {
        let {traineeForms} = this.state;
        let {fields} = traineeForms[formId - 1];
        fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        this.setState({traineeRegisterForms: traineeForms});
    };

    handleChangeCoordinator = (field, e) => {
        let {coordinatorForm} = this.state;
        let {fields} = coordinatorForm;
        fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        this.setState({coordinatorForm});
    };

    handleChangeReceipt = (field, e) => {
        let {receiptForm} = this.state;
        let {fields} = receiptForm;
        fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        this.setState({receiptForm});
    };

    handleChangeSelectCoordinator = (e) => {
        const traineeId = parseInt(e.target.value);
        const {coordinatorForm, traineeForms} = this.state;
        const selectedTraineeForm = traineeForms[traineeId - 1];

        coordinatorForm.fields[REGISTER_COORDINATOR_TITLE] = selectedTraineeForm.fields[REGISTER_TRAINEE_TITLE];
        coordinatorForm.fields[REGISTER_COORDINATOR_FIRST_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_FIRST_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_LAST_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_LAST_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_AGE] = selectedTraineeForm.fields[REGISTER_TRAINEE_AGE];
        coordinatorForm.fields[REGISTER_COORDINATOR_JOB_POSITION] = selectedTraineeForm.fields[REGISTER_TRAINEE_JOB_POSITION];
        coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_ORGANIZATION_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE] = selectedTraineeForm.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE];
        coordinatorForm.fields[REGISTER_COORDINATOR_PHONE] = selectedTraineeForm.fields[REGISTER_TRAINEE_PHONE];
        coordinatorForm.fields[REGISTER_COORDINATOR_EMAIL] = selectedTraineeForm.fields[REGISTER_TRAINEE_EMAIL];

        this.setState({coordinatorForm});
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (this.validateFormReceipt()) {
            this.doRegister();
        } else {
            //alert('กรุณากรอกข้อมูลในหน้านี้ให้ครบถ้วน');
        }
    };

    onClickPrevious = () => {
        const {step} = this.state;
        if (step === 3 && this.state.traineeForms.length === 1) {
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
        const {step} = this.state;
        let valid = true;
        switch (step) {
            case 1:
                valid = this.validateForm();
                break;
            case 2:
                valid = this.validateFormCoordinator();
                break;
        }
        if (valid) {
            if (step === 1 && this.state.traineeForms.length === 1) {
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

    /*validate ฟอร์ม ผู้สมัครอบรม*/
    validateForm = () => {
        let formIsValid = true;
        let {traineeForms} = this.state;
        let firstErrorFormId = 0;

        for (let i = traineeForms.length - 1; i >= 0; i--) {
            let regForm = traineeForms[i];
            let {fields} = regForm;
            let errors = {};
            let currentFormIsValid = true;

            if (!fields[REGISTER_TRAINEE_TITLE]) {
                errors[REGISTER_TRAINEE_TITLE] = 'กรุณาเลือกคำนำหน้า';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_FIRST_NAME] || fields[REGISTER_TRAINEE_FIRST_NAME].trim().length === 0) {
                errors[REGISTER_TRAINEE_FIRST_NAME] = 'กรุณากรอกชื่อ';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_LAST_NAME] || fields[REGISTER_TRAINEE_LAST_NAME].trim().length === 0) {
                errors[REGISTER_TRAINEE_LAST_NAME] = 'กรุณากรอกชื่อ';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_AGE]) {
                errors[REGISTER_TRAINEE_AGE] = 'กรุณากรอกอายุ';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_JOB_POSITION] || fields[REGISTER_TRAINEE_JOB_POSITION].trim().length === 0) {
                errors[REGISTER_TRAINEE_JOB_POSITION] = 'กรุณากรอกตำแหน่งงาน';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_ORGANIZATION_NAME] || fields[REGISTER_TRAINEE_ORGANIZATION_NAME].trim().length === 0) {
                errors[REGISTER_TRAINEE_ORGANIZATION_NAME] = 'กรุณากรอกชื่อหน่วยงาน';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_ORGANIZATION_TYPE]) {
                errors[REGISTER_TRAINEE_ORGANIZATION_TYPE] = 'กรุณากรอกเลือกประเภทหน่วยงาน';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_PHONE] || fields[REGISTER_TRAINEE_PHONE].trim().length === 0) {
                errors[REGISTER_TRAINEE_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_EMAIL] || fields[REGISTER_TRAINEE_EMAIL].trim().length === 0) {
                errors[REGISTER_TRAINEE_EMAIL] = 'กรุณากรอกอีเมล';
                currentFormIsValid = false;
            } else if (!isValidEmail(fields[REGISTER_TRAINEE_EMAIL])) {
                errors[REGISTER_TRAINEE_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
                currentFormIsValid = false;
            }

            regForm.errors = errors;
            formIsValid = currentFormIsValid && formIsValid;

            if (!currentFormIsValid) {
                firstErrorFormId = regForm.id;
            }
        }

        this.setState({
            traineeForms: traineeForms,
        }, () => {
            if (firstErrorFormId > 0) {
                scroller.scrollTo(firstErrorFormId.toString(), {
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

    /*validate ฟอร์ม ผู้ประสานงาน*/
    validateFormCoordinator = () => {
        let valid = true;
        let {coordinatorForm} = this.state;
        let coordinatorFields = coordinatorForm.fields;
        let coordinatorErrors = {};

        if (this.state.traineeForms.length > 1) {
            if (!coordinatorFields[REGISTER_COORDINATOR_TITLE]) {
                coordinatorErrors[REGISTER_COORDINATOR_TITLE] = 'กรุณาเลือกคำนำหน้า';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_FIRST_NAME] || coordinatorFields[REGISTER_COORDINATOR_FIRST_NAME].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_FIRST_NAME] = 'กรุณากรอกชื่อ';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_LAST_NAME] || coordinatorFields[REGISTER_COORDINATOR_LAST_NAME].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_LAST_NAME] = 'กรุณากรอกนามสกุล';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_AGE]) {
                coordinatorErrors[REGISTER_COORDINATOR_AGE] = 'กรุณากรอกอายุ';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_JOB_POSITION] || coordinatorFields[REGISTER_COORDINATOR_JOB_POSITION].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_JOB_POSITION] = 'กรุณากรอกตำแหน่งงาน';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_NAME] || coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_NAME].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_ORGANIZATION_NAME] = 'กรุณากรอกชื่อหน่วยงาน';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE]) {
                coordinatorErrors[REGISTER_COORDINATOR_ORGANIZATION_TYPE] = 'กรุณาเลือกประเภทหน่วยงาน';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_PHONE] || coordinatorFields[REGISTER_COORDINATOR_PHONE].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
                valid = false;
            }
            if (!coordinatorFields[REGISTER_COORDINATOR_EMAIL] || coordinatorFields[REGISTER_COORDINATOR_EMAIL].trim().length === 0) {
                coordinatorErrors[REGISTER_COORDINATOR_EMAIL] = 'กรุณากรอกอีเมล';
                valid = false;
            }
        }
        coordinatorForm.errors = coordinatorErrors;

        this.setState({
            coordinatorForm: coordinatorForm,
        }, () => {
            if (!valid) {
                scroller.scrollTo('coordinator-form', {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                });
            }
        });

        return valid;
    };

    /*validate ฟอร์ม ข้อมูลการออกใบเสร็จ*/
    validateFormReceipt = () => {
        let valid = true;
        let {receiptForm} = this.state;
        let receiptFields = receiptForm.fields;
        let receiptErrors = {};

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
        if (!receiptFields[REGISTER_RECEIPT_POSTAL_CODE] || receiptFields[REGISTER_RECEIPT_POSTAL_CODE].trim().length === 0) {
            receiptErrors[REGISTER_RECEIPT_POSTAL_CODE] = 'กรุณากรอกรหัสไปรษณีย์';
            valid = false;
        }
        if (!receiptFields[REGISTER_RECEIPT_ORGANIZATION_PHONE] || receiptFields[REGISTER_RECEIPT_ORGANIZATION_PHONE].trim().length === 0) {
            receiptErrors[REGISTER_RECEIPT_ORGANIZATION_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์หน่วยงาน';
            valid = false;
        }
        if (!receiptFields[REGISTER_RECEIPT_TAX_ID] || receiptFields[REGISTER_RECEIPT_TAX_ID].trim().length === 0) {
            receiptErrors[REGISTER_RECEIPT_TAX_ID] = 'กรุณากรอกเลขประจำตัวผู้เสียภาษี';
            valid = false;
        }
        receiptForm.errors = receiptErrors;

        this.setState({
            receiptForm: receiptForm,
        }, () => {
            if (!valid) {
                scroller.scrollTo('receipt-form', {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                });
            }
        });

        return valid;
    };

    doRegister = () => {
        const user = getLoginUser();
        const loginToken = user === null ? null : getLoginUser().loginToken;
        const {traineeForms, coordinatorForm, receiptForm} = this.state;
        const trainees = [];
        for (let i = 0; i < traineeForms.length; i++) {
            let formData = traineeForms[i];
            trainees.push({
                traineeTitle: formData.fields[REGISTER_TRAINEE_TITLE],
                traineeFirstName: formData.fields[REGISTER_TRAINEE_FIRST_NAME],
                traineeLastName: formData.fields[REGISTER_TRAINEE_LAST_NAME],
                traineeAge: formData.fields[REGISTER_TRAINEE_AGE],
                traineeJobPosition: formData.fields[REGISTER_TRAINEE_JOB_POSITION],
                traineeOrganizationName: formData.fields[REGISTER_TRAINEE_ORGANIZATION_NAME],
                traineeOrganizationType: formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE],
                traineePhone: formData.fields[REGISTER_TRAINEE_PHONE],
                traineeEmail: formData.fields[REGISTER_TRAINEE_EMAIL],
            });
        }

        fetch('/api/register_course', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                loginToken,
                courseId: this.props.course.id,
                trainees,
                coordinator: traineeForms.length > 1 ? {
                    coordinatorTitle: coordinatorForm.fields[REGISTER_COORDINATOR_TITLE],
                    coordinatorFirstName: coordinatorForm.fields[REGISTER_COORDINATOR_FIRST_NAME],
                    coordinatorLastName: coordinatorForm.fields[REGISTER_COORDINATOR_LAST_NAME],
                    coordinatorAge: coordinatorForm.fields[REGISTER_COORDINATOR_AGE],
                    coordinatorJobPosition: coordinatorForm.fields[REGISTER_COORDINATOR_JOB_POSITION],
                    coordinatorOrganizationName: coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_NAME],
                    coordinatorOrganizationType: coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE],
                    coordinatorPhone: coordinatorForm.fields[REGISTER_COORDINATOR_PHONE],
                    coordinatorEmail: coordinatorForm.fields[REGISTER_COORDINATOR_EMAIL],
                } : {
                    coordinatorTitle: traineeForms[0].fields[REGISTER_TRAINEE_TITLE],
                    coordinatorFirstName: traineeForms[0].fields[REGISTER_TRAINEE_FIRST_NAME],
                    coordinatorLastName: traineeForms[0].fields[REGISTER_TRAINEE_LAST_NAME],
                    coordinatorAge: traineeForms[0].fields[REGISTER_TRAINEE_AGE],
                    coordinatorJobPosition: traineeForms[0].fields[REGISTER_TRAINEE_JOB_POSITION],
                    coordinatorOrganizationName: traineeForms[0].fields[REGISTER_TRAINEE_ORGANIZATION_NAME],
                    coordinatorOrganizationType: traineeForms[0].fields[REGISTER_TRAINEE_ORGANIZATION_TYPE],
                    coordinatorPhone: traineeForms[0].fields[REGISTER_TRAINEE_PHONE],
                    coordinatorEmail: traineeForms[0].fields[REGISTER_TRAINEE_EMAIL],
                },
                receipt: {
                    receiptAddress: receiptForm.fields[REGISTER_RECEIPT_ADDRESS],
                    receiptSubDistrict: receiptForm.fields[REGISTER_RECEIPT_SUB_DISTRICT],
                    receiptDistrict: receiptForm.fields[REGISTER_RECEIPT_DISTRICT],
                    receiptProvince: receiptForm.fields[REGISTER_RECEIPT_PROVINCE],
                    receiptPostalCode: receiptForm.fields[REGISTER_RECEIPT_POSTAL_CODE],
                    receiptOrganizationPhone: receiptForm.fields[REGISTER_RECEIPT_ORGANIZATION_PHONE],
                    receiptTaxId: receiptForm.fields[REGISTER_RECEIPT_TAX_ID],
                }
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
        let {coordinatorForm, receiptForm, step} = this.state;

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
                                <p>โครงการบริการวิชาการ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>
                                <h3>แบบฟอร์มลงทะเบียน</h3></div>
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
                                                <div className="col-sm-3 text-black ">วันที่อบรม</div>
                                                <div className="col-sm-9 text-gray">{formatCourseDateLong(this.props.course.beginDate, this.props.course.endDate)}</div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">สถานที่อบรม</div>
                                                <div className="col-sm-9 text-gray">{this.props.course.place}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Element name={TOP_OF_FORM}>
                            <RegisterProgress
                                text={[
                                    'ข้อมูลผู้สมัครอบรม', 'ข้อมูลผู้ประสานงาน' + '\n' + '(กรณีผู้สมัครมากกว่า 1 ท่าน)', 'ข้อมูลการออกใบเสร็จ'
                                ]}
                                activeStep={step}
                            /><br/>
                        </Element>

                        {/*ฟอร์มสมัครอบรม*/}
                        <form id="applicationFormGroup" method="post" noValidate={true} onSubmit={this.handleSubmit}>
                            {/*ขั้นตอน 1 กรอกข้อมูลผู้สมัครอบรม*/}
                            {step === 1 &&
                            <div>
                                {/*หัวข้อ*/}
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <h4 className="text-black" style={{marginTop: '20px'}}>
                                            <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัครอบรม
                                        </h4>
                                    </div>
                                </div>

                                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                                {
                                    this.state.traineeForms.map(formData => (
                                        <TraineeRegisterForm
                                            formData={formData}
                                            nameTitleList={this.state.nameTitleList}
                                            organizationTypeList={this.state.organizationTypeList}
                                            handleChangeCallback={this.handleChange}
                                            onClickRemoveCallback={this.onClickRemoveTrainee}
                                        />
                                    ))
                                }

                                {/*ปุ่มเพิ่มผู้สมัครอบรม*/}
                                <div style={{border: '0px solid blue', marginTop: '15px', textAlign: 'center', marginBottom: '30px'}}>
                                    <a href="javascript:void(0);" className="addmoreuser" onClick={this.onClickAddTrainee}>
                                        <i className="fas fa-plus-circle" style={{fontSize: '0.8rem'}}/>&nbsp;&nbsp;&nbsp;เพิ่มผู้สมัครอบรม
                                    </a>
                                </div>
                            </div>
                            }

                            {/*ขั้นตอน 2 กรอกข้อมูลผู้ประสานงาน*/}
                            {step === 2 &&
                            <Element name={'coordinator-form'}>
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <h4 className="text-black" style={{marginTop: '20px'}}>
                                            <i className="fas fa-user-friends" style={{fontSize: '1rem', marginBottom: '15px'}}/>&nbsp;ข้อมูลผู้ประสานงาน
                                        </h4>
                                        <select
                                            onChange={this.handleChangeSelectCoordinator}
                                            className="form-control"
                                            style={{marginBottom: 0}}>
                                            <option disabled selected>เลือกผู้ประสานงานจากรายชื่อผู้สมัครอบรม หรือกรอกข้อมูลผู้ประสานงาน</option>
                                            {
                                                this.state.traineeForms.map(traineeForm => {
                                                    let {fields} = traineeForm;
                                                    let firstName = fields[REGISTER_TRAINEE_FIRST_NAME] === undefined ? '' : fields[REGISTER_TRAINEE_FIRST_NAME];
                                                    let lastName = fields[REGISTER_TRAINEE_LAST_NAME] === undefined ? '' : fields[REGISTER_TRAINEE_LAST_NAME];
                                                    let displayName = 'ผู้สมัครอบรม ลำดับที่ ' + traineeForm.id + ': ' + firstName + ' ' + lastName;
                                                    return (
                                                        <option value={traineeForm.id}>{displayName}</option>
                                                    );
                                                })
                                            }
                                        </select>
                                    </div>
                                </div>
                                <div className="border-inside" style={{marginTop: '15px', marginBottom: '30px'}}>
                                    <div className="row">
                                        <div className="col">
                                            <div className="regisfo">
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">คำนำหน้าชื่อ</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <select value={coordinatorForm.fields[REGISTER_COORDINATOR_TITLE] || '0'}
                                                                        onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_TITLE)}
                                                                        className="form-control"
                                                                        style={{marginBottom: 0}}>
                                                                    <option value="0" disabled selected>เลือกคำนำหน้า</option>
                                                                    {
                                                                        this.state.nameTitleList.map((nameTitle, index) =>
                                                                            <option key={index} value={nameTitle.title}>{nameTitle.title}</option>
                                                                        )
                                                                    }
                                                                </select>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_TITLE]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-6">
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">ชื่อ</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_FIRST_NAME] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_FIRST_NAME)}
                                                                       type="text"
                                                                       placeholder="กรอกชื่อ"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_FIRST_NAME]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">นามสกุล</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_LAST_NAME] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_LAST_NAME)}
                                                                       type="text"
                                                                       placeholder="กรอกนามสกุล"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_LAST_NAME]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">อายุ</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_AGE] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_AGE)}
                                                                       type="number"
                                                                       placeholder="กรอกอายุ"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_AGE]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">ตำแหน่งงาน</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_JOB_POSITION] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_JOB_POSITION)}
                                                                       type="text"
                                                                       placeholder="กรอกตำแหน่งงาน"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_JOB_POSITION]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">ชื่อหน่วยงาน</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_NAME] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_ORGANIZATION_NAME)}
                                                                       type="text"
                                                                       placeholder="กรอกชื่อหน่วยงาน"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_ORGANIZATION_NAME]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">ประเภทหน่วยงาน</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <select value={coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE] || '0'}
                                                                        onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_ORGANIZATION_TYPE)}
                                                                        className="form-control">
                                                                    <option value="0" disabled>เลือกประเภทหน่วยงาน</option>
                                                                    {
                                                                        this.state.organizationTypeList.map((organizationType, index) =>
                                                                            <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                        )
                                                                    }
                                                                </select>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_ORGANIZATION_TYPE]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">เบอร์โทรศัพท์</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_PHONE] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_PHONE)}
                                                                       type="tel"
                                                                       placeholder="กรอกเบอร์โทรศัพท์"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_PHONE]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">อีเมล</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <input value={coordinatorForm.fields[REGISTER_COORDINATOR_EMAIL] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_EMAIL)}
                                                                       type="email"
                                                                       placeholder="กรอกอีเมล"
                                                                       className="form-control input-md"/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_EMAIL]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Element>
                            }

                            {/*ขั้นตอน 3 กรอกข้อมูลการออกใบเสร็จ*/}
                            {step === 3 &&
                            <div>
                                {/*ฟอร์มข้อมูลการออกใบเสร็จ*/}
                                <Element name={'receipt-form'}>
                                    <div className="row" style={{border: '0 solid red', clear: 'both'}}>
                                        <div className="col">
                                            <h4 className="text-black" style={{marginTop: '20px'}}>
                                                ข้อมูลการออกใบเสร็จ
                                            </h4>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col">
                                            <div className="border-inside" style={{marginTop: '10px'}}>
                                                <div className="regisfo">
                                                    <div className="row">
                                                        <div className="col-md-12">
                                                            <div className="row">
                                                                <div className="col-12 col-md-2" style={{paddingRight: 0}}>
                                                                    <label className="label required-label">ที่อยู่หน่วยงาน</label>
                                                                </div>
                                                                <div className="col-12 col-md-9">
                                                                    <div className="row">
                                                                        <div className="col-12 col-md-12">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_ADDRESS] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_ADDRESS)}
                                                                                   type="text"
                                                                                   placeholder="เลขที่ / อาคาร / หมู่ / ซอย / ถนน"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_ADDRESS]}/>
                                                                        </div>
                                                                        <div className="w-100"></div>
                                                                        <div className="col-12 col-md-6">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_SUB_DISTRICT] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_SUB_DISTRICT)}
                                                                                   type="text"
                                                                                   placeholder="แขวง / ตำบล"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_SUB_DISTRICT]}/>
                                                                        </div>
                                                                        <div className="col-12 col-md-6 nopadleft">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_DISTRICT] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_DISTRICT)}
                                                                                   type="text"
                                                                                   placeholder="เขต / อำเภอ"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_DISTRICT]}/>
                                                                        </div>
                                                                        <div className="w-100"></div>
                                                                        <div className="col-12 col-md-6">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_PROVINCE] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_PROVINCE)}
                                                                                   type="text" placeholder="จังหวัด"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_PROVINCE]}/>
                                                                        </div>
                                                                        <div className="col-12 col-md-6 nopadleft">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_POSTAL_CODE] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_POSTAL_CODE)}
                                                                                   type="number"
                                                                                   placeholder="รหัสไปรษณีย์"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_POSTAL_CODE]}/>
                                                                        </div>
                                                                        <div className="w-100"></div>
                                                                        <div className="col-12 col-md-6">
                                                                            <input value={receiptForm.fields[REGISTER_RECEIPT_ORGANIZATION_PHONE] || ''}
                                                                                   onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_ORGANIZATION_PHONE)}
                                                                                   type="text"
                                                                                   placeholder="เบอร์โทรศัพท์หน่วยงาน"
                                                                                   className="form-control input-md"/>
                                                                            <ErrorLabel
                                                                                value={receiptForm.errors[REGISTER_RECEIPT_ORGANIZATION_PHONE]}/>
                                                                        </div>
                                                                        <div className="col-12 col-md-6 nopadleft">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div className="row mt-2 mb-1">
                                                                <div className="col-12 col-md-2" style={{paddingRight: 0}}>
                                                                    <label className="label required-label">เลขประจำตัวผู้เสียภาษี</label>
                                                                </div>
                                                                <div className="col-12 col-md-9">
                                                                    <input value={receiptForm.fields[REGISTER_RECEIPT_TAX_ID] || ''}
                                                                           onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_TAX_ID)}
                                                                           type="text"
                                                                           placeholder="เลขประจำตัวผู้เสียภาษี"
                                                                           className="form-control input-md"/>
                                                                    <ErrorLabel
                                                                        value={receiptForm.errors[REGISTER_RECEIPT_TAX_ID]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </Element>

                                {/*ปุ่มลงทะเบียน*/}
                                <div className="row mt-3 mb-3">
                                    <div className="col">
                                        <div className="btn-red-submit">
                                            <button type="submit" value="submit" className="btn btn-danger">
                                                ลงทะเบียน
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            }

                            {/*ปุ่มย้อนกลับ/ถัดไป*/}
                            <div className="container" style={{marginBottom: '40px'}}>
                                <div className="row">
                                    <div className="col-6 nopad">
                                        <div className="text-left">
                                            {step !== 1 &&
                                            <button type="button" className="btn btn-normal" onClick={this.onClickPrevious}>
                                                <i className="fas fa-arrow-left" style={{fontSize: '0.8rem'}}/>&nbsp;&nbsp;&nbsp;ย้อนกลับ
                                            </button>
                                            }
                                        </div>
                                    </div>
                                    <div className="col-6 nopad">
                                        <div className="text-right">
                                            {step !== 3 &&
                                            <button type="button" className="btn btn-normal" onClick={this.onClickNext}>
                                                &nbsp;ถัดไป&nbsp;&nbsp;&nbsp;<i className="fas fa-arrow-right" style={{fontSize: '0.8rem'}}/>&nbsp;
                                            </button>
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div id="normalModal" className="modal fade">
                            <div className="modal-dialog">
                                <div className="modal-content">
                                    <div className="modal-body">
                                        <div className="required">
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>ชื่อ</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกชื่อ"
                                                           className="form-control input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>นามสกุล</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกนามสกุล"
                                                           className="form-control input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>เบอร์โทรหน่วยงาน</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกเบอร์หน่วยงาน"
                                                           className="form-control input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>เบอร์มือถือ</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกเบอร์มือถือ"
                                                           className="form-control input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>อีเมล</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกอีเมล"
                                                           className="form-control input-md"/></div>
                                            </div>
                                        </div>
                                        <br/><a href="#" className="btn-submit">ตกลง</a></div>
                                </div>
                                {/*/.modal-content*/}
                            </div>
                            {/*/.modal-dialog*/}
                        </div>
                        {/*/.modal*/}
                    </div>
                }
                {
                    !this.props.course &&
                    <div style={{textAlign: 'center', color: 'red'}}>{this.props.errorMessage}</div>
                }

                <style jsx>{`
                    .btn-normal {
                        color: black;
                        background-color: #d0d0d0;
                    }
                    
                    .btn-normal:hover {
                        color: white;
                        background-color: #999999;
                    }
                `}</style>
            </MainLayout>
        );
    }
}