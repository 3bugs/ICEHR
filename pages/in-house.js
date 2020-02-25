import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, isString, isValidEmail, isPositiveInteger, getDateFormatFromDateObject, getDateDisplayFromDateObject} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {HOST_BACKEND, SERVICE_SOCIAL} from "../etc/constants";
import Dialog from "../components/Dialog";
import DatePicker from "react-datepicker";
import ReactPaginate from "react-paginate";
import '../pages/pagination.css';

const IN_HOUSE_LIMIT_PER_PAGE = 3;

const TOP_OF_FORM = 'topOfForm';
//const ORGANIZATION_TYPE_OTHER = '9999';

const FIELD_TITLE = 'fieldTitle';
const FIELD_FIRST_NAME = 'fieldFirstName';
const FIELD_LAST_NAME = 'fieldLastName';
const FIELD_ORGANIZATION_NAME = 'fieldOrganizationName';
const FIELD_PHONE = 'fieldPhone';
const FIELD_EMAIL = 'fieldEmail';
const FIELD_COURSE = 'fieldCourse';
const FIELD_NUM_DAY = 'fieldNumDay';
const FIELD_NUM_TRAINEE = 'fieldNumTrainee';
const FIELD_PLACE = 'fieldPlace';
const FIELD_REMARK = 'fieldRemark';

class RegisterForm extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    handleChange(field, e) {
        this.props.handleChangeCallback(field, e);
    }

    render() {
        let {formData} = this.props;

        return (
            <React.Fragment>

                <style jsx>{`

                `}</style>
            </React.Fragment>
        );
    }
}

class InHouseItem extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {

    }

    render() {
        const {data} = this.props;

        return (
            <div className="col-12 col-md-4">
                <div className="pic-inhouse">
                    <figure>
                        <img src={`${HOST_BACKEND}/uploads/news_assets/${data.image_file_name}`} className="img-fluid"/>
                    </figure>
                </div>
                <div className="inhouse-detail">
                    <h3>{data.title}</h3>
                    <p>{data.short_description}</p>
                    <div className="row">
                        <div className="col date">
                            <img src="/static/images/calendar.svg"/> {getDateDisplayFromDateObject(new Date(data.news_date))}
                        </div>
                        <div className="col">
                            <Link href={`/news?id=${data.id}`}
                                  as={`/news/${data.id}`}>
                                <a href="javascript:void(0)" className="readmore-red">
                                    อ่านต่อ<img src="/static/images/arrow-more.svg"/>
                                </a>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default class InHouse extends React.Component {
    constructor(props, context) {
        super(props, context);

        this.state = {
            step: 1,
            formData: {
                fields: {},
                errors: {}
            },
            dialog: {
                isOpen: false,
                message: '',
                textColor: '#000',
                onCloseCallback: null,
            },
            inHouseList: [],
            errorMessage: null,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
    }

    static getInitialProps = async function ({req, query}) {
        let nameTitleList = null;
        //let inHouseList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const resNameTitle = await fetch(baseUrl + '/api/get_name_title', {
            method: 'post'
        });
        const resultNameTitle = await resNameTitle.json();
        if (resultNameTitle['error']['code'] === 0) {
            nameTitleList = resultNameTitle['dataList'];

            /*const resInHouse = await fetch(baseUrl + '/api/get_in_house_latest', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                }),
            });
            const resultInHouse = await resInHouse.json();
            if (resultInHouse['error']['code'] === 0) {
                inHouseList = resultInHouse['dataList'];
                errorMessage = null;
            } else {
                nameTitleList = null;
                inHouseList = null;
                errorMessage = resultNameTitle['error']['message'];
            }*/
        } else {
            nameTitleList = null;
            //inHouseList = null;
            errorMessage = resultNameTitle['error']['message'];
        }

        return {nameTitleList, errorMessage};
    };

    componentDidMount() {
    }

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * IN_HOUSE_LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetInHouse();
        });
    };

    async doGetInHouse() {
        let inHouseList = null;
        let errorMessage = null;
        let pageCount = 0;

        const resInHouse = await fetch('/api/get_in_house', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: IN_HOUSE_LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        });
        const resultInHouse = await resInHouse.json();
        if (resultInHouse['error']['code'] === 0) {
            inHouseList = resultInHouse['dataList'];
            pageCount = Math.ceil(resultInHouse.totalCount / IN_HOUSE_LIMIT_PER_PAGE),
            errorMessage = null;
        } else {
            inHouseList = null;
            errorMessage = resultInHouse['error']['message'];
        }
        this.setState({inHouseList, errorMessage, pageCount});
    }

    handleChange = (field, e) => {
        let {formData} = this.state;
        let {fields} = formData;
        let {target} = e;

        if (field === FIELD_EMAIL) {
            fields[field] = target.value.trim();
        } else {
            fields[field] = target.type === 'checkbox' ? target.checked : target.value;
        }
        this.setState({formData}, () => {
        });
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (this.validateForm()) {
            this.doSubmitForm();
        } else {
            this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
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

    /*validate ฟอร์ม*/
    validateForm = () => {
        let formIsValid = true;

        let {formData} = this.state;
        let {fields} = formData;
        let errors = {};

        if (!fields[FIELD_TITLE]) {
            errors[FIELD_TITLE] = 'กรุณาเลือกคำนำหน้า';
            formIsValid = false;
        }
        if (!fields[FIELD_FIRST_NAME] || fields[FIELD_FIRST_NAME].trim().length === 0) {
            errors[FIELD_FIRST_NAME] = 'กรุณากรอกชื่อ';
            formIsValid = false;
        }
        if (!fields[FIELD_LAST_NAME] || fields[FIELD_LAST_NAME].trim().length === 0) {
            errors[FIELD_LAST_NAME] = 'กรุณากรอกนามสกุล';
            formIsValid = false;
        }
        if (!fields[FIELD_ORGANIZATION_NAME] || fields[FIELD_ORGANIZATION_NAME].trim().length === 0) {
            errors[FIELD_ORGANIZATION_NAME] = 'กรุณากรอกชื่อหน่วยงาน';
            formIsValid = false;
        }
        if (!fields[FIELD_PHONE] || fields[FIELD_PHONE].trim().length === 0) {
            errors[FIELD_PHONE] = 'กรุณากรอกเบอร์โทรศัพท์';
            formIsValid = false;
        }
        if (!fields[FIELD_EMAIL] || fields[FIELD_EMAIL].trim().length === 0) {
            errors[FIELD_EMAIL] = 'กรุณากรอกอีเมล';
            formIsValid = false;
        } else if (!isValidEmail(fields[FIELD_EMAIL])) {
            errors[FIELD_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
            formIsValid = false;
        }

        if (!fields[FIELD_COURSE] || fields[FIELD_COURSE].trim().length === 0) {
            errors[FIELD_COURSE] = 'กรุณากรอกหลักสูตร / หัวข้อ';
            formIsValid = false;
        }
        if (!fields[FIELD_NUM_DAY]) {
            errors[FIELD_NUM_DAY] = 'กรุณากรอกจำนวนวัน';
            formIsValid = false;
        } else if (fields[FIELD_NUM_DAY] <= 0) {
            errors[FIELD_NUM_DAY] = 'กรุณากรอกจำนวนวันตั้งแต่ 1 ขึ้นไป';
            formIsValid = false;
        }
        if (!fields[FIELD_NUM_TRAINEE]) {
            errors[FIELD_NUM_TRAINEE] = 'กรุณากรอกจำนวนผู้เข้าอบรม';
            formIsValid = false;
        } else if (fields[FIELD_NUM_TRAINEE] <= 0) {
            errors[FIELD_NUM_TRAINEE] = 'กรุณากรอกจำนวนผู้เข้าอบรมตั้งแต่ 1 ขึ้นไป';
            formIsValid = false;
        }
        if (!fields[FIELD_PLACE] || fields[FIELD_PLACE].trim().length === 0) {
            errors[FIELD_PLACE] = 'กรุณากรอกสถานที่';
            formIsValid = false;
        }

        formData.errors = errors;

        this.setState({
            formData,
        });

        return formIsValid;
    };

    doSubmitForm = () => {
        const user = getLoginUser();
        const loginToken = user === null ? null : getLoginUser().loginToken;
        const {formData} = this.state;

        fetch('/api/register_in_house', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                loginToken,
                formData: formData.fields,
            }),
        })
            .then(result => result.json())
            .then(result => {
                let msg = result['error']['message'];
                //alert(msg);
                if (result['error']['code'] === 0) {
                    this.showDialog(msg, "success", () => {
                        this.dismissDialog();
                        //Router.back();
                    });
                } else {
                    this.showDialog(msg, "error", () => {
                        this.dismissDialog();
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
        let {formData, dialog, inHouseList, errorMessage} = this.state;
        //const {inHouseList} = this.props;

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '40px', width: '100%'}}>IN-HOUSE TRAINING</h3>
                        </div>
                    </div>
                    <div className="row mt-3">
                        <div className="col-12 col-md-12">
                            <h4 className="text-black"><img src="/static/images/inhouse-icon.svg" className="ih-icon"/> หลักสูตร In-house Training ที่ผ่านมาของสถาบัน</h4>
                        </div>
                        {/*<div className="col-12 col-md-3"><a href="#" className="readmore">ดูทั้งหมด</a></div>*/}
                    </div>
                    <div className="row mt-3">
                        {inHouseList &&
                        inHouseList.map(inHouse => (
                            <InHouseItem data={inHouse}/>
                        ))
                        }
                        {inHouseList && inHouseList.length === 0 &&
                        <div className="col-12" style={{textAlign: 'center'}}>
                            ไม่มีข้อมูลหลักสูตร In-house Training ที่ผ่านมา
                        </div>
                        }
                        {!inHouseList &&
                        <div style={{color: 'red', textAlign: 'center'}}>
                            {errorMessage}
                        </div>
                        }
                    </div>

                    <div style={{textAlign: 'center', marginTop: '30px'}}>
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
                    </div>

                    <div className="row mt-5">
                        <div className="col">
                            <h4 className="text-black" style={{marginBottom: 0}}>
                                <img src="/static/images/identity-icon.svg" className="identi-con"/> ช่องทางการติดต่อ (ผู้ประสานงาน)
                            </h4>
                            <div style={{marginTop: '10px', marginLeft: '0px'}}>ผู้ที่สนใจให้ทางสถาบันฯ จัดฝึกอบรม In-House Training กรุณากรอกข้อมูล</div>
                        </div>
                    </div>
                    <Element name={TOP_OF_FORM}>
                        <form noValidate={true} onSubmit={this.handleSubmit}>
                            <div className="row">
                                <div className="col">
                                    <div className="border-inside">
                                        <div className="row">
                                            <div className="col-md-9 offset-md-1">
                                                <div className="required">
                                                    {/*คำนำหน้า*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">คำนำหน้าชื่อ</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <select value={formData.fields[FIELD_TITLE] || '0'}
                                                                    onChange={this.handleChange.bind(this, FIELD_TITLE)}
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
                                                                value={formData.errors[FIELD_TITLE]}/>
                                                        </div>
                                                    </div>

                                                    {/*ชื่อ*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">ชื่อ</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <input value={formData.fields[FIELD_FIRST_NAME] || ''}
                                                                   onChange={this.handleChange.bind(this, FIELD_FIRST_NAME)}
                                                                   type="text"
                                                                   placeholder="กรอกชื่อ"
                                                                   className="form-control-2 input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={formData.errors[FIELD_FIRST_NAME]}/>
                                                        </div>
                                                    </div>

                                                    {/*นามสกุล*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">นามสกุล</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <input value={formData.fields[FIELD_LAST_NAME] || ''}
                                                                   onChange={this.handleChange.bind(this, FIELD_LAST_NAME)}
                                                                   type="text"
                                                                   placeholder="กรอกนามสกุล"
                                                                   className="form-control-2 input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={formData.errors[FIELD_LAST_NAME]}/>
                                                        </div>
                                                    </div>

                                                    {/*ชื่อหน่วยงาน*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">ชื่อหน่วยงาน</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <input value={formData.fields[FIELD_ORGANIZATION_NAME] || ''}
                                                                   onChange={this.handleChange.bind(this, FIELD_ORGANIZATION_NAME)}
                                                                   type="text"
                                                                   placeholder="กรอกชื่อหน่วยงาน"
                                                                   className="form-control-2 input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={formData.errors[FIELD_ORGANIZATION_NAME]}/>
                                                        </div>
                                                    </div>

                                                    {/*เบอร์โทร*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">เบอร์โทรศัพท์</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <input value={formData.fields[FIELD_PHONE] || ''}
                                                                   onChange={this.handleChange.bind(this, FIELD_PHONE)}
                                                                   type="text"
                                                                   placeholder="กรอกเบอร์โทรศัพท์"
                                                                   className="form-control-2 input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={formData.errors[FIELD_PHONE]}/>
                                                        </div>
                                                    </div>

                                                    {/*อีเมล*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">อีเมล</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <input value={formData.fields[FIELD_EMAIL] || ''}
                                                                   onChange={this.handleChange.bind(this, FIELD_EMAIL)}
                                                                   type="text"
                                                                   placeholder="กรอกอีเมล"
                                                                   className="form-control-2 input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={formData.errors[FIELD_EMAIL]}/>
                                                        </div>
                                                    </div>

                                                    {/*หลักสูตรที่ต้องการ*/}
                                                    <div className="row">
                                                        <div className="col-md-3">
                                                            <label className="mt-2">หลักสูตรที่ท่านต้องการ</label>
                                                        </div>
                                                        <div className="col-12 col-md-9">
                                                            <div className="row">
                                                                {/*หลักสูตร/หัวข้อ*/}
                                                                <div className="col-12 col-md-12">
                                                                    <input value={formData.fields[FIELD_COURSE] || ''}
                                                                           onChange={this.handleChange.bind(this, FIELD_COURSE)}
                                                                           type="text"
                                                                           placeholder="หลักสูตร / หัวข้อ"
                                                                           className="form-control input-md mt-2"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[FIELD_COURSE]}/>
                                                                </div>
                                                                <div className="w-100"/>
                                                                {/*จำนวนวัน*/}
                                                                <div className="col-12 col-md-6 nopadright">
                                                                    <input value={formData.fields[FIELD_NUM_DAY] || ''}
                                                                           onChange={this.handleChange.bind(this, FIELD_NUM_DAY)}
                                                                           type="number"
                                                                           placeholder="จำนวนวัน"
                                                                           className="form-control input-md mt-2"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[FIELD_NUM_DAY]}/>
                                                                </div>
                                                                {/*จำนวนผู้เข้าอบรม*/}
                                                                <div className="col-12 col-md-6 nopadleft">
                                                                    <input value={formData.fields[FIELD_NUM_TRAINEE] || ''}
                                                                           onChange={this.handleChange.bind(this, FIELD_NUM_TRAINEE)}
                                                                           type="number"
                                                                           placeholder="จำนวนผู้เข้าอบรม"
                                                                           className="form-control input-md mt-2"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[FIELD_NUM_TRAINEE]}/>
                                                                </div>
                                                                <div className="w-100"/>
                                                                {/*สถานที่*/}
                                                                <div className="col-12 col-md-12">
                                                                    <input value={formData.fields[FIELD_PLACE] || ''}
                                                                           onChange={this.handleChange.bind(this, FIELD_PLACE)}
                                                                           type="text"
                                                                           placeholder="สถานที่"
                                                                           className="form-control input-md mt-2"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[FIELD_PLACE]}/>
                                                                </div>
                                                                <div className="w-100"/>
                                                                {/*อื่นๆ*/}
                                                                <div className="col-12 col-md-12">
                                                                    <input value={formData.fields[FIELD_REMARK] || ''}
                                                                           onChange={this.handleChange.bind(this, FIELD_REMARK)}
                                                                           type="text"
                                                                           placeholder="อื่นๆ"
                                                                           className="form-control input-md mt-2 mb-3"/>
                                                                    <ErrorLabel
                                                                        value={formData.errors[FIELD_REMARK]}/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="btn-red-submit">
                                            <button type="submit" className="btn btn-danger">ส่งข้อมูล</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </Element>
                </div>

                <Dialog message={dialog.message}
                        textColor={dialog.textColor}
                        isOpen={dialog.isOpen}
                        onCloseCallback={dialog.onCloseCallback}/>

                <style jsx>{`
                    .form-control {
                        display: inline-block;
                        width: 100%;
                        margin-bottom: 0px;
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
                        margin-bottom: 0px;
                        border-radius: 0.25rem;
                        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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
                    
                    .ih-icon {
                        width: 40px !important;
                    }
                    
                    .identi-con {
                        width: 28px !important;
                    }
                    
                    @media (max-width: 991px) {
                        .form-control-2 {
                            width: 100%;
                        }
                    }
                    @media (max-width: 767px) {
                        .form-control-2 {
                            width: 100%;
                        }
                    }
                `}</style>
            </MainLayout>
        );
    }
}
