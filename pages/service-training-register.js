import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail, isPositiveInteger, getDateFormatFromDateObject} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {SERVICE_TRAINING} from "../etc/constants";
import Dialog from "../components/Dialog";
import DatePicker from "react-datepicker";
import {Modal, Button} from 'react-bootstrap';

const TOP_OF_FORM = 'topOfForm';
const ORGANIZATION_TYPE_OTHER = 9999;

const REGISTER_TRAINEE_TITLE = 'registerTraineeTitle';
const REGISTER_TRAINEE_FIRST_NAME = 'registerTraineeFirstName';
const REGISTER_TRAINEE_LAST_NAME = 'registerTraineeLastName';
const REGISTER_TRAINEE_BIRTH_DATE = 'registerTraineeBirthDate';
const REGISTER_TRAINEE_JOB_POSITION = 'registerTraineeJobPosition';
const REGISTER_TRAINEE_ORGANIZATION_NAME = 'registerTraineeOrganizationName';
const REGISTER_TRAINEE_ORGANIZATION_TYPE = 'registerTraineeOrganizationType';
const REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM = 'registerTraineeOrganizationTypeCustom';
const REGISTER_TRAINEE_PHONE = 'registerTraineePhone';
const REGISTER_TRAINEE_EMAIL = 'registerTraineeEmail';

const REGISTER_COORDINATOR_TITLE = 'registerCoordinatorTitle';
const REGISTER_COORDINATOR_FIRST_NAME = 'registerCoordinatorFirstName';
const REGISTER_COORDINATOR_LAST_NAME = 'registerCoordinatorLastName';
const REGISTER_COORDINATOR_BIRTH_DATE = 'registerCoordinatorBirthDate';
const REGISTER_COORDINATOR_JOB_POSITION = 'registerCoordinatorJobPosition';
const REGISTER_COORDINATOR_ORGANIZATION_NAME = 'registerCoordinatorOrganizationName';
const REGISTER_COORDINATOR_ORGANIZATION_TYPE = 'registerCoordinatorOrganizationType';
const REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM = 'registerCoordinatorOrganizationTypeCustom';
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
                    <li className={activeStep === 3 ? 'active' : (activeStep > 3 ? 'complete' : '')}>{text[2]}</li>
                    <li className={activeStep === 4 ? 'active' : ''}>{text[3]}</li>
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
                        width: 24%;
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
        this.state = {
            red: false,
            showOrganizationTypeCustom: false,
        };
        this.organizationTypeCustomInput = React.createRef();
    }

    focusOrganizationTypeCustomInput = () => {
        this.organizationTypeCustomInput.current.focus();
    };

    componentWillReceiveProps(nextProps) {
        if (nextProps.showOrganizationTypeCustom !== this.state.showOrganizationTypeCustom) {
            // You don't have to do this check first, but it can help prevent an unneeded render
            this.setState({
                showOrganizationTypeCustom: nextProps.showOrganizationTypeCustom
            }, () => {
                if (nextProps.showOrganizationTypeCustom) {
                    this.focusOrganizationTypeCustomInput();
                }
            });
        }
    }

    componentDidMount() {
        this.setState({showOrganizationTypeCustom: this.props.showOrganizationTypeCustom});
    }

    handleChange(formId, field, e) {
        /*if (field === REGISTER_TRAINEE_ORGANIZATION_TYPE) {
            if (e.target.value === ORGANIZATION_TYPE_OTHER) {
                this.setState({
                    showOrganizationTypeCustom: true,
                }, () => {
                    this.focusOrganizationTypeCustomInput();
                });
            } else {
                this.setState({
                    showOrganizationTypeCustom: false,
                });
            }
        }*/

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

    setDatePickerMinDate = () => {
        const d = new Date();
        const year = 1900;
        const month = 0;
        const day = 1;
        return new Date(year, month, day)
    };

    render() {
        let {formData, isReadOnly} = this.props;

        return (
            <Element name={formData.id}>
                <div className="container_reg" style={{border: '0px solid red', position: 'relative', top: '-10px'}}>
                    <div className="row field_reg">
                        <div className="col">
                            <div className={this.state.red ? 'border-inside-red' : 'border-inside'}>
                                {!isReadOnly &&
                                <a href="javascript:void(0);" onClick={this.onClickRemove}>
                                    <div className="deletenumber">x</div>
                                </a>
                                }
                                <div className="row">
                                    <div className="col-12 col-md-1 d-block d-sm-block d-md-block d-lg-block d-xl-block">
                                        <div className="list-add">ลำดับที่ <span id="sequence">{formData.id}</span></div>
                                    </div>

                                    <div className="col-12 col-md-11">
                                        <div className="row">
                                            <div className="col">
                                                <div className="regisfo">

                                                    {/*คำนำหน้า*/}
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
                                                                            style={{marginBottom: 0}}
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
                                                                        value={formData.errors[REGISTER_TRAINEE_TITLE]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-6">

                                                        </div>
                                                    </div>

                                                    {/*ชื่อ-นามสกุล*/}
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
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
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
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_LAST_NAME]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/*วันเกิด, ตำแหน่งงาน*/}
                                                    <div className="row">
                                                        <div className="col-md-6">
                                                            <div className="row">
                                                                <div className="col-md-4">
                                                                    <label className="label required-label">วันเกิด</label>
                                                                </div>
                                                                <div className="col-md-8">
                                                                    <DatePicker
                                                                        selected={formData.fields[REGISTER_TRAINEE_BIRTH_DATE] || ''}
                                                                        onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_BIRTH_DATE)}
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
                                                                        className="form-control input-md my-react-date-picker"
                                                                        disabled={isReadOnly}/>
                                                                    {/*<input value={formData.fields[REGISTER_TRAINEE_BIRTH_DATE] || ''}
                                                                           onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_BIRTH_DATE)}
                                                                           type="number"
                                                                           placeholder="กรอกอายุ"
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>*/}
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_BIRTH_DATE]}/>
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
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_JOB_POSITION]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/*ชื่อ-ประเภทหน่วยงาน*/}
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
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
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
                                                                            className="form-control"
                                                                            disabled={isReadOnly}>
                                                                        <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                        {
                                                                            this.props.organizationTypeList.map((organizationType, index) =>
                                                                                <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                            )
                                                                        }
                                                                        <option value={ORGANIZATION_TYPE_OTHER}>อื่นๆ (ระบุ)</option>
                                                                    </select>
                                                                    <ErrorLabel
                                                                        value={formData.errors[REGISTER_TRAINEE_ORGANIZATION_TYPE]}/>
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
                                                                    <div style={{display: this.state.showOrganizationTypeCustom ? 'block' : 'none'}}>
                                                                        <input value={formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM] || ''}
                                                                               onChange={this.handleChange.bind(this, formData.id, REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM)}
                                                                               type="text"
                                                                               placeholder="กรอกประเภทหน่วยงาน"
                                                                               className="form-control input-md"
                                                                               disabled={isReadOnly}
                                                                               ref={this.organizationTypeCustomInput}/>
                                                                        <ErrorLabel
                                                                            value={formData.errors[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM]}/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {/*เบอร์โทร, อีเมล*/}
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
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
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
                                                                           onKeyDown={e => {
                                                                               if (e.key === ' ') {
                                                                                   e.preventDefault();
                                                                               }
                                                                           }}
                                                                           type="email"
                                                                           placeholder="กรอกอีเมล"
                                                                           className="form-control input-md"
                                                                           disabled={isReadOnly}/>
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
        };
        this.organizationTypeCustomInput = React.createRef();
    }

    static getInitialProps = async function ({req, query}) {
        let course = null, nameTitleList = null, organizationTypeList = null;
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
                    serviceType: SERVICE_TRAINING,
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
                    //errorMessage = null;

                    const resOrganizationType = await fetch(baseUrl + '/api/get_organization_type', {
                        method: 'post'
                    });
                    const resultOrganizationType = await resOrganizationType.json();
                    if (resultOrganizationType['error']['code'] === 0) {
                        organizationTypeList = resultOrganizationType['dataList'];
                        errorMessage = null;
                    } else {
                        nameTitleList = null;
                        errorMessage = resultOrganizationType['error']['message'];
                    }
                } else {
                    nameTitleList = null;
                    errorMessage = resultNameTitle['error']['message'];
                }
            } else {
                course = null;
                errorMessage = result['error']['message'];
            }
            return {course, nameTitleList, organizationTypeList, errorMessage};
        }
    };

    focusOrganizationTypeCustomInput = () => {
        this.organizationTypeCustomInput.current.focus();
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

        //this.doGetNameTitle();
        //this.doGetOrganizationType();

        let user = getLoginUser();
        let initialTraineeFields = {};
        let initialCoordinatorFields = {};
        let initialReceiptFields = {};

        if (user !== null) {
            initialTraineeFields[REGISTER_TRAINEE_TITLE] = user.title;
            initialTraineeFields[REGISTER_TRAINEE_FIRST_NAME] = user.firstName;
            initialTraineeFields[REGISTER_TRAINEE_LAST_NAME] = user.lastName;
            initialTraineeFields[REGISTER_TRAINEE_BIRTH_DATE] = new Date(user.birthDate);
            initialTraineeFields[REGISTER_TRAINEE_JOB_POSITION] = user.jobPosition;
            initialTraineeFields[REGISTER_TRAINEE_ORGANIZATION_NAME] = user.organizationName;
            initialTraineeFields[REGISTER_TRAINEE_ORGANIZATION_TYPE] = user.organizationType;
            initialTraineeFields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM] = user.organizationTypeCustom;
            initialTraineeFields[REGISTER_TRAINEE_PHONE] = user.phone;
            initialTraineeFields[REGISTER_TRAINEE_EMAIL] = user.email;

            initialCoordinatorFields[REGISTER_COORDINATOR_TITLE] = user.title;
            initialCoordinatorFields[REGISTER_COORDINATOR_FIRST_NAME] = user.firstName;
            initialCoordinatorFields[REGISTER_COORDINATOR_LAST_NAME] = user.lastName;
            initialCoordinatorFields[REGISTER_COORDINATOR_BIRTH_DATE] = new Date(user.birthDate);
            initialCoordinatorFields[REGISTER_COORDINATOR_JOB_POSITION] = user.jobPosition;
            initialCoordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_NAME] = user.organizationName;
            initialCoordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE] = user.organizationType;
            initialCoordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM] = user.organizationTypeCustom;
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

        if (field === REGISTER_TRAINEE_BIRTH_DATE || field === REGISTER_COORDINATOR_BIRTH_DATE) {
            /*let d = e; //new Date();
            let yyyy = d.getFullYear();
            let mm = d.getMonth() + 1;
            let dd = d.getDate();*/
            //alert(`${yyyy}-${mm}-${dd}`);

            fields[field] = e;
        } else if (field === REGISTER_TRAINEE_EMAIL) {
            fields[field] = e.target.value.trim();
        } else {
            fields[field] = e.target.value;
        }
        //fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        this.setState({traineeForms});
    };

    handleChangeCoordinator = (field, e) => {
        let {coordinatorForm} = this.state;
        let {fields} = coordinatorForm;
        //fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        fields[field] = e.target.value;

        //ถ้าหากเลือก "อื่นๆ" ในช่อง "ประเภทหน่วยงาน" ก็จะ focus ไปที่ช่องกรอกประเภทหน่วยงานที่อยู่ถัดลงไป (ต้องรอ setState ทำงานก่อน)
        let setFocus = (field === REGISTER_COORDINATOR_ORGANIZATION_TYPE) && (parseInt(e.target.value) === ORGANIZATION_TYPE_OTHER);

        this.setState({coordinatorForm}, () => {
            if (setFocus) {
                this.focusOrganizationTypeCustomInput();
            }
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

    handleChangeSelectCoordinator = (e) => {
        const traineeId = parseInt(e.target.value);
        const {coordinatorForm, traineeForms} = this.state;
        const selectedTraineeForm = traineeForms[traineeId - 1];

        coordinatorForm.fields[REGISTER_COORDINATOR_TITLE] = selectedTraineeForm.fields[REGISTER_TRAINEE_TITLE];
        coordinatorForm.fields[REGISTER_COORDINATOR_FIRST_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_FIRST_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_LAST_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_LAST_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_BIRTH_DATE] = selectedTraineeForm.fields[REGISTER_TRAINEE_BIRTH_DATE];
        coordinatorForm.fields[REGISTER_COORDINATOR_JOB_POSITION] = selectedTraineeForm.fields[REGISTER_TRAINEE_JOB_POSITION];
        coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_NAME] = selectedTraineeForm.fields[REGISTER_TRAINEE_ORGANIZATION_NAME];
        coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE] = selectedTraineeForm.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE];
        coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM] = selectedTraineeForm.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM];
        coordinatorForm.fields[REGISTER_COORDINATOR_PHONE] = selectedTraineeForm.fields[REGISTER_TRAINEE_PHONE];
        coordinatorForm.fields[REGISTER_COORDINATOR_EMAIL] = selectedTraineeForm.fields[REGISTER_TRAINEE_EMAIL];

        this.setState({coordinatorForm});
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (this.validateFormReceipt()) {
            this.doRegister();
        } else {
            /*this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                scroller.scrollTo('receipt-form', {
                    duration: 500,
                    smooth: true,
                    offset: -80,
                });
                this.dismissDialog();
            });*/
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
            case 3:
                valid = this.validateFormReceipt();
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
                errors[REGISTER_TRAINEE_LAST_NAME] = 'กรุณากรอกนามสกุล';
                currentFormIsValid = false;
            }
            if (!fields[REGISTER_TRAINEE_BIRTH_DATE]) {
                errors[REGISTER_TRAINEE_BIRTH_DATE] = 'กรุณาระบุวันเกิด';
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
                errors[REGISTER_TRAINEE_ORGANIZATION_TYPE] = 'กรุณาเลือกประเภทหน่วยงาน';
                currentFormIsValid = false;
            } else if (parseInt(fields[REGISTER_TRAINEE_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER
                && (!fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM] || fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM].trim().length === 0)) {
                errors[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM] = 'กรุณากรอกประเภทหน่วยงาน';
                formIsValid = false;
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
            if (!formIsValid) {
                this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                    this.dismissDialog();
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
            if (!coordinatorFields[REGISTER_COORDINATOR_BIRTH_DATE]) {
                coordinatorErrors[REGISTER_COORDINATOR_BIRTH_DATE] = 'กรุณาระบุวันเกิด';
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
            } else if (parseInt(coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER
                && (!coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM] || coordinatorFields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM].trim().length === 0)) {
                coordinatorErrors[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM] = 'กรุณากรอกประเภทหน่วยงาน';
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
                this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                    scroller.scrollTo('coordinator-form', {
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
        if (!receiptFields[REGISTER_RECEIPT_POSTAL_CODE]
            || receiptFields[REGISTER_RECEIPT_POSTAL_CODE].trim().length !== 5
            || !isPositiveInteger(receiptFields[REGISTER_RECEIPT_POSTAL_CODE])) {
            receiptErrors[REGISTER_RECEIPT_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
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
                this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                    scroller.scrollTo('receipt-form', {
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
        const {traineeForms, coordinatorForm, receiptForm} = this.state;
        const trainees = [];
        for (let i = 0; i < traineeForms.length; i++) {
            let formData = traineeForms[i];

            trainees.push({
                traineeTitle: formData.fields[REGISTER_TRAINEE_TITLE],
                traineeFirstName: formData.fields[REGISTER_TRAINEE_FIRST_NAME],
                traineeLastName: formData.fields[REGISTER_TRAINEE_LAST_NAME],
                traineeBirthDate: getDateFormatFromDateObject(formData.fields[REGISTER_TRAINEE_BIRTH_DATE]),
                traineeJobPosition: formData.fields[REGISTER_TRAINEE_JOB_POSITION],
                traineeOrganizationName: formData.fields[REGISTER_TRAINEE_ORGANIZATION_NAME],
                traineeOrganizationType: formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE],
                traineeOrganizationTypeCustom: formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM],
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
                    coordinatorBirthDate: getDateFormatFromDateObject(coordinatorForm.fields[REGISTER_COORDINATOR_BIRTH_DATE]),
                    coordinatorJobPosition: coordinatorForm.fields[REGISTER_COORDINATOR_JOB_POSITION],
                    coordinatorOrganizationName: coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_NAME],
                    coordinatorOrganizationType: coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE],
                    coordinatorOrganizationTypeCustom: coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM],
                    coordinatorPhone: coordinatorForm.fields[REGISTER_COORDINATOR_PHONE],
                    coordinatorEmail: coordinatorForm.fields[REGISTER_COORDINATOR_EMAIL],
                } : {
                    /*coordinatorTitle: traineeForms[0].fields[REGISTER_TRAINEE_TITLE],
                    coordinatorFirstName: traineeForms[0].fields[REGISTER_TRAINEE_FIRST_NAME],
                    coordinatorLastName: traineeForms[0].fields[REGISTER_TRAINEE_LAST_NAME],
                    coordinatorBirthDate: getDateFormatFromDateObject(traineeForms[0].fields[REGISTER_TRAINEE_BIRTH_DATE]),
                    coordinatorJobPosition: traineeForms[0].fields[REGISTER_TRAINEE_JOB_POSITION],
                    coordinatorOrganizationName: traineeForms[0].fields[REGISTER_TRAINEE_ORGANIZATION_NAME],
                    coordinatorOrganizationType: traineeForms[0].fields[REGISTER_TRAINEE_ORGANIZATION_TYPE],
                    coordinatorOrganizationTypeCustom: traineeForms[0].fields[REGISTER_TRAINEE_ORGANIZATION_TYPE_CUSTOM],
                    coordinatorPhone: traineeForms[0].fields[REGISTER_TRAINEE_PHONE],
                    coordinatorEmail: traineeForms[0].fields[REGISTER_TRAINEE_EMAIL],*/

                    coordinatorTitle: null,
                    coordinatorFirstName: null,
                    coordinatorLastName: null,
                    coordinatorBirthDate: null,
                    coordinatorJobPosition: null,
                    coordinatorOrganizationName: null,
                    coordinatorOrganizationType: null,
                    coordinatorOrganizationTypeCustom: null,
                    coordinatorPhone: null,
                    coordinatorEmail: null,
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
                const msg = result['error']['message'];
                if (result['error']['code'] === 0) {
                    /*this.showDialog(msg, 'success', () => {
                        Router.back();
                    });*/
                    const courseRegId = result['courseRegId'];
                    const registerSuccessDialog = {
                        show: true,
                        title: 'ลงทะเบียนสำเร็จ'
                    };
                    this.setState({
                        courseRegId,
                        registerSuccessDialog,
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

    setDatePickerMinDate = () => {
        const d = new Date();
        const year = 1900;
        const month = 0;
        const day = 1;
        return new Date(year, month, day)
    };

    handleDownloadTraineeFormPdf = () => {
        fetch('/api/download_trainee_form_pdf', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                courseRegId: this.state.courseRegId //todo:
            }),
        });
        /*.then(result => result.json())
        .then(result => {
            if (result['error']['code'] === 0) {
                this.setState({
                    nameTitleList: result['dataList'],
                });
            } else {
                /!*let errors = {};
                errors[RESULT_ERROR] = result['error_message'];
                this.setState({errors: errors});*!/
            }
        });*/
    };

    render() {
        let {traineeForms, coordinatorForm, receiptForm, step, dialog} = this.state;

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
                                <h3>แบบฟอร์มลงทะเบียนอบรม</h3></div>
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
                                    'ข้อมูลผู้สมัคร', 'ข้อมูลผู้ประสานงาน' + '\n' + '(กรณีผู้สมัครมากกว่า 1 ท่าน)', 'ข้อมูลการออกใบเสร็จ', 'ตรวจสอบข้อมูล'
                                ]}
                                activeStep={step}
                            /><br/>
                        </Element>

                        {/*ฟอร์มสมัครอบรม*/}
                        <form id="applicationFormGroup" method="post" noValidate={true} onSubmit={this.handleSubmit}>
                            {/*ขั้นตอน 1 กรอกข้อมูลผู้สมัครอบรม*/}
                            {((step === 1) || (step === 4)) &&
                            <div>
                                {/*หัวข้อ*/}
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <h4 className="text-black" style={{marginTop: '20px'}}>
                                            <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัคร
                                        </h4>
                                    </div>
                                </div>

                                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                                {
                                    this.state.traineeForms.map(formData => (
                                        <TraineeRegisterForm
                                            formData={formData}
                                            showOrganizationTypeCustom={parseInt(formData.fields[REGISTER_TRAINEE_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER}
                                            isReadOnly={step === 4}
                                            nameTitleList={this.props.nameTitleList}
                                            organizationTypeList={this.props.organizationTypeList}
                                            handleChangeCallback={this.handleChange}
                                            onClickRemoveCallback={this.onClickRemoveTrainee}
                                        />
                                    ))
                                }

                                {/*ปุ่มเพิ่มผู้สมัครอบรม*/}
                                {step === 1 &&
                                <div style={{border: '0px solid blue', marginTop: '15px', textAlign: 'center', marginBottom: '30px'}}>
                                    <a href="javascript:void(0);" className="addmoreuser" onClick={this.onClickAddTrainee}>
                                        <i className="fas fa-plus-circle" style={{fontSize: '0.8rem'}}/>&nbsp;&nbsp;&nbsp;เพิ่มผู้สมัคร
                                    </a>
                                </div>
                                }
                            </div>
                            }

                            {/*ขั้นตอน 2 กรอกข้อมูลผู้ประสานงาน*/}
                            {((step === 2) || (step === 4 && traineeForms.length > 1)) &&
                            <React.Fragment>
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <Element name={'coordinator-form'}>
                                            <h4 className="text-black" style={{marginTop: '20px'}}>
                                                <i className="fas fa-user-friends" style={{fontSize: '1rem', marginBottom: '15px'}}/>&nbsp;ข้อมูลผู้ประสานงาน
                                            </h4>
                                        </Element>
                                        {step !== 4 &&
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
                                        }
                                    </div>
                                </div>
                                < div className="border-inside" style={{marginTop: '15px', marginBottom: '30px'}}>
                                    <div className="row">
                                        <div className="col">
                                            <div className="regisfo">

                                                {/*คำนำหน้า*/}
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
                                                                        style={{marginBottom: 0}}
                                                                        disabled={step === 4}>
                                                                    <option value="0" disabled selected>เลือกคำนำหน้า</option>
                                                                    {
                                                                        this.props.nameTitleList.map((nameTitle, index) =>
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

                                                {/*ชื่อ-นามสกุล*/}
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_LAST_NAME]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/*วันเกิด, ตำแหน่งงาน*/}
                                                <div className="row">
                                                    <div className="col-md-6">
                                                        <div className="row">
                                                            <div className="col-md-4">
                                                                <label className="label required-label">วันเกิด</label>
                                                            </div>
                                                            <div className="col-md-8">
                                                                <DatePicker
                                                                    selected={coordinatorForm.fields[REGISTER_COORDINATOR_BIRTH_DATE] || ''}
                                                                    onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_BIRTH_DATE)}
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
                                                                    className="form-control input-md my-react-date-picker"
                                                                    disabled={step === 4}/>
                                                                {/*<input value={coordinatorForm.fields[REGISTER_COORDINATOR_BIRTH_DATE] || ''}
                                                                       onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_BIRTH_DATE)}
                                                                       type="number"
                                                                       placeholder="ระบุวันเกิด"
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>*/}
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_BIRTH_DATE]}/>
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_JOB_POSITION]}/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/*ชื่อ-ประเภทหน่วยงาน*/}
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
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
                                                                        className="form-control"
                                                                        disabled={step === 4}>
                                                                    <option value="0" disabled>เลือกประเภทหน่วยงาน</option>
                                                                    {
                                                                        this.props.organizationTypeList.map((organizationType, index) =>
                                                                            <option key={index} value={organizationType.id}>{organizationType.name}</option>
                                                                        )
                                                                    }
                                                                    <option value={ORGANIZATION_TYPE_OTHER}>อื่นๆ (ระบุ)</option>
                                                                </select>
                                                                <ErrorLabel
                                                                    value={coordinatorForm.errors[REGISTER_COORDINATOR_ORGANIZATION_TYPE]}/>
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
                                                                    style={{display: parseInt(coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE]) === ORGANIZATION_TYPE_OTHER ? 'block' : 'none'}}>
                                                                    <input value={coordinatorForm.fields[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM] || ''}
                                                                           onChange={this.handleChangeCoordinator.bind(this, REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM)}
                                                                           type="text"
                                                                           placeholder="กรอกประเภทหน่วยงาน"
                                                                           className="form-control input-md"
                                                                           disabled={step === 4}
                                                                           ref={this.organizationTypeCustomInput}/>
                                                                    <ErrorLabel
                                                                        value={coordinatorForm.errors[REGISTER_COORDINATOR_ORGANIZATION_TYPE_CUSTOM]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/*เบอร์โทร, อีเมล*/}
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
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
                                                                       onKeyDown={e => {
                                                                           if (e.key === ' ') {
                                                                               e.preventDefault();
                                                                           }
                                                                       }}
                                                                       type="email"
                                                                       placeholder="กรอกอีเมล"
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
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
                            </React.Fragment>
                            }

                            {/*ขั้นตอน 3 กรอกข้อมูลการออกใบเสร็จ*/}
                            {((step === 3) || (step === 4)) &&
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
                                                                               className="form-control input-md"
                                                                               disabled={step === 4}/>
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
                                                                               disabled={step === 4}/>
                                                                        <ErrorLabel
                                                                            value={receiptForm.errors[REGISTER_RECEIPT_SUB_DISTRICT]}/>
                                                                    </div>
                                                                    <div className="col-12 col-md-6 nopadleft">
                                                                        <input value={receiptForm.fields[REGISTER_RECEIPT_DISTRICT] || ''}
                                                                               onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_DISTRICT)}
                                                                               type="text"
                                                                               placeholder="เขต / อำเภอ"
                                                                               className="form-control input-md"
                                                                               disabled={step === 4}/>
                                                                        <ErrorLabel
                                                                            value={receiptForm.errors[REGISTER_RECEIPT_DISTRICT]}/>
                                                                    </div>
                                                                    <div className="w-100"></div>
                                                                    <div className="col-12 col-md-6">
                                                                        <input value={receiptForm.fields[REGISTER_RECEIPT_PROVINCE] || ''}
                                                                               onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_PROVINCE)}
                                                                               type="text" placeholder="จังหวัด"
                                                                               className="form-control input-md"
                                                                               disabled={step === 4}/>
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
                                                                               disabled={step === 4}/>
                                                                        <ErrorLabel
                                                                            value={receiptForm.errors[REGISTER_RECEIPT_POSTAL_CODE]}/>
                                                                    </div>
                                                                    <div className="w-100"></div>
                                                                    <div className="col-12 col-md-6">
                                                                        <input value={receiptForm.fields[REGISTER_RECEIPT_ORGANIZATION_PHONE] || ''}
                                                                               onChange={this.handleChangeReceipt.bind(this, REGISTER_RECEIPT_ORGANIZATION_PHONE)}
                                                                               type="text"
                                                                               placeholder="เบอร์โทรศัพท์หน่วยงาน"
                                                                               className="form-control input-md"
                                                                               disabled={step === 4}/>
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
                                                                       className="form-control input-md"
                                                                       disabled={step === 4}/>
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

                                {/*ปุ่มลงทะเบียน*/}
                                {step === 4 &&
                                <div className="row mt-3 mb-3">
                                    <div className="col">
                                        <div className="btn-red-submit">
                                            <button type="submit" value="submit" className="btn btn-danger">
                                                ยืนยันลงทะเบียนอบรม
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                }
                            </div>
                            }

                            {/*ปุ่มย้อนกลับ/ถัดไป*/}
                            <div className="container mt-4 mb-4" style={{marginBottom: '0px'}}>
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
                                            {step !== 4 &&
                                            <button type="button" className="btn btn-normal" onClick={this.onClickNext}>
                                                &nbsp;ถัดไป&nbsp;&nbsp;&nbsp;<i className="fas fa-arrow-right" style={{fontSize: '0.8rem'}}/>&nbsp;
                                            </button>
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        {/*<button type="button"
                                className="btn btn-dark"
                                style={{marginLeft: 0, padding: '5px 20px'}}
                                onClick={this.handleDownloadTraineeFormPdf}>
                            Save ใบสมัคร <i className="far fa-save" style={{color: '#aaa'}}></i>
                        </button>*/}
                        <a className="btn btn-dark"
                           style={{marginLeft: 0, padding: '5px 20px'}}
                           href={`/api/get_trainee_form_pdf?courseRegId=${this.state.courseRegId}&download=true`}>
                            Save ใบสมัคร{/*<i className="far fa-save" style={{color: '#aaa'}}></i>*/}
                        </a>
                        <a className="btn btn-dark"
                           style={{marginRight: 0, padding: '5px 20px'}}
                           href={`/api/get_trainee_form_pdf?courseRegId=${this.state.courseRegId}`}
                           target="_blank">
                            แสดงใบสมัคร{/*<i className="fa fa-print" style={{color: '#aaa'}}></i>*/}
                        </a>
                    </div>
                </RegisterSuccessDialog>

                < style
                    jsx> {`
                    .btn-normal {
                    color: black;
                    background-color: #d0d0d0;
                    }

                    .btn-normal:hover {
                    color: white;
                    background-color: #999999;
                    }
                `
                }</style>
            </MainLayout>
        );
    }
}