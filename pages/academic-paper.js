import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import fetch from 'isomorphic-unfetch';
import Modal from 'react-bootstrap/Modal';
import {isString, isValidEmail, nl2br} from '../etc/utils';
import {HOST_BACKEND, LIMIT_PER_PAGE} from "../etc/constants";
import $ from 'jquery';
import './academic-paper.css';
import './pagination.css';
import ErrorLabel from "../components/ErrorLabel";
import ReactPaginate from 'react-paginate';
import {Element, scroller} from 'react-scroll';

const INPUT_FIRST_NAME = 'firstName';
const INPUT_LAST_NAME = 'lastName';
const INPUT_ORGANIZATION_NAME = 'organizationName';
const INPUT_JOB_POSITION = 'jobPosition';
const INPUT_OCCUPATION = 'occupation';
const INPUT_EMAIL = 'email';
const INPUT_USE = 'use';

const SEARCH_INPUT_TITLE = 'searchTitle';
const SEARCH_INPUT_YEAR_PUBLISHED = 'searchYearPublished';
const SEARCH_INPUT_NAME = 'searchName';

const KEY_ACADEMIC_PAPER_FORM_SUBMIT_LIST = 'academicPaperStatusList';

class AcademicPaperListItem extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    render() {
        const {show, isSearchResult, accordionId} = this.props;
        const {id, title, firstName, lastName, yearPublished} = this.props.data;

        return (
            <React.Fragment>
                <div className="card">
                    <div className="card-header" id={`heading${accordionId + id}` + (isSearchResult ? 'search' : 'normal')}>
                        <h5 className="mb-0">
                            <button className={'btn-link ' + (show ? '' : 'collapsed')}
                                    data-toggle="collapse"
                                    data-target={`#collapse${accordionId + id}` + (isSearchResult ? 'search' : 'normal')}
                                    aria-expanded={show}
                                    aria-controls={`collapse${accordionId + id}` + (isSearchResult ? 'search' : 'normal')}>
                                <i className="fa" aria-hidden="true"/>
                                <div className="academicPaperTitle">{title}</div>
                                <span className="yeartop">ปีที่เผยแพร่ {yearPublished}</span>
                            </button>
                        </h5>
                    </div>
                    <div id={`collapse${accordionId + id}` + (isSearchResult ? 'search' : 'normal')}
                         className={'collapse ' + (show ? 'show' : '')}
                         aria-labelledby={`heading${accordionId + id}` + (isSearchResult ? 'search' : 'normal')}
                         data-parent={'#' + this.props.accordionId}>
                        <div className="card-body">
                            <div className="row">
                                <div className="col-6 col-sm-4"> ปีที่เผยแพร่
                                    <br/>ชื่อผู้ทำวิจัย
                                </div>
                                <div className="col-6 col-sm-4"> {yearPublished}
                                    <br/>{firstName} {lastName}
                                </div>
                                <div className="col-sm-4">
                                    <Link
                                        as={`/academic-paper/${id}`}
                                        href={`/academic-paper?id=${id}`}
                                    >
                                        <a href="javascript:void(0)" className="btn btn-outline-success" onClick={this.props.handleCloseSearchResultModal}>View</a>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .bg-search-service1 .col {
                        padding: 5px;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .bg-search-service1 {
                        height: 120px;
                    }
                    
                    .wrap {
                        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
                        border-radius: 4px;
                    }
                    
                    [data-toggle="collapse"] .fa:before {
                        content: "\\f139";
                    }
                    
                    [data-toggle="collapse"].collapsed .fa:before {
                        content: "\\f13a";
                    }
                    
                    .card {
                        background-color: #b40303;
                        border-radius: 0px;
                        margin-top: 10px;
                        border: 1px solid #eeeeee;
                    }
                    
                    .card-body {
                        background-color: white;
                        border: 1px solid #eeeeee;
                    }
                    
                    .btn-link:hover, .btn-link {
                        color: white;
                        text-decoration: none;
                        width: 100%;
                        text-align: left;
                        border: none;
                        cursor: pointer;
                    }
                    
                    .mb-0 i {
                        position: absolute;
                        right: 20px;
                        top: 20px;
                    }
                    
                    @media only screen and (max-width: 991px) {
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                    }
                    
                    @media only screen and (max-width: 767px) {
                        .mb-0 i {
                            display: none;
                        }
                        .bg-search-service1 {
                            height: auto;
                        }
                        .panel-title a {
                            font-size: 1rem;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                    }                           
                `}</style>
            </React.Fragment>
        );
    }
}

class AcademicPaperList extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    render() {
        const randomId = Math.random().toString(36).substring(7);

        return (
            <React.Fragment>
                {this.props.dataList.length > 0 &&
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <div className="container">
                                <div id={`accordion${randomId}`}>
                                    {
                                        this.props.dataList.map((data, index) => (
                                            <AcademicPaperListItem
                                                data={data}
                                                show={!this.props.isSearchResult && index === 0}
                                                isSearchResult={this.props.isSearchResult}
                                                handleCloseSearchResultModal={this.props.handleCloseSearchResultModal}
                                                accordionId={`accordion${randomId}`}
                                            />
                                        ))
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                }

                {this.props.dataList.length === 0 &&
                <div style={{textAlign: 'center'}}>ไม่พบข้อมูล</div>
                }

                <style jsx>{`
                    .bg-search-service1 .col {
                        padding: 5px;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .bg-search-service1 {
                        height: 120px;
                    }
                    
                    .wrap {
                        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
                        border-radius: 4px;
                    }
                    
                    [data-toggle="collapse"] .fa:before {
                        content: "\\f139";
                    }
                    
                    [data-toggle="collapse"].collapsed .fa:before {
                        content: "\\f13a";
                    }
                    
                    .card {
                        background-color: #b40303;
                        border-radius: 0px;
                        margin-top: 10px;
                        border: 1px solid #eeeeee;
                    }
                    
                    .card-body {
                        background-color: white;
                        border: 1px solid #eeeeee;
                    }
                    
                    .btn-link:hover,
                        .btn-link {
                        color: white;
                        text-decoration: none;
                        width: 100%;
                        text-align: left;
                        border: none;
                        cursor: pointer;
                    }
                    
                    .mb-0 i {
                        position: absolute;
                        right: 20px;
                        top: 20px;
                    }
                    
                    @media only screen and (max-width: 991px) {
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                    }
                    
                    @media only screen and (max-width: 767px) {
                        .mb-0 i {
                            display: none;
                        }
                        .bg-search-service1 {
                            height: auto;
                        }
                        .panel-title a {
                            font-size: 1rem;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                    }                           
                `}</style>
            </React.Fragment>
        );
    }
}

class AcademicPaperDetails extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            showFormModal: false,
            fields: {},
            errors: {},
        };
    }

    static isFormSubmitForAcademicPaper = (id) => {
        if (id == null) {
            console.log('No ID specified');
            return false;
        }

        let json = localStorage.getItem(KEY_ACADEMIC_PAPER_FORM_SUBMIT_LIST);

        if (json != null) {
            const academicPaperStatusList = JSON.parse(json);
            return academicPaperStatusList.includes(id);
        } else {
            return false;
        }
    };

    static addFormSubmitForAcademicPaper = (id) => {
        if (id == null) {
            console.log('No ID specified');
            return false;
        }

        let json = localStorage.getItem(KEY_ACADEMIC_PAPER_FORM_SUBMIT_LIST);

        if (json != null) {
            const academicPaperStatusList = JSON.parse(json);
            if (!academicPaperStatusList.includes(id)) {
                academicPaperStatusList.push(id);
                localStorage.setItem(
                    KEY_ACADEMIC_PAPER_FORM_SUBMIT_LIST,
                    JSON.stringify(academicPaperStatusList)
                );
            }
        } else {
            localStorage.setItem(
                KEY_ACADEMIC_PAPER_FORM_SUBMIT_LIST,
                JSON.stringify([id])
            );
        }
    };

    openPdf() {
        const {fileName} = this.props.data;
        window.open(`${HOST_BACKEND}/uploads/academic_papers/${fileName}`, '_blank');
    }

    handleClickDownload = () => {
        const {id} = this.props.data;
        if (AcademicPaperDetails.isFormSubmitForAcademicPaper(id)) {
            this.openPdf();
        } else {
            this.setState({
                showFormModal: true,
            });
        }
    };

    handleCloseFormModal = e => {
        this.setState({
            showFormModal: false
        });
    };

    handleInputChange = (field, e) => {
        let {fields} = this.state;

        if (field === INPUT_EMAIL) {
            fields[field] = e.target.value.trim();
        } else {
            fields[field] = e.target.value;
        }
        this.setState({fields});
    };

    handleFormSubmit = e => {
        e.preventDefault();
        if (this.validateForm()) {
            this.doSubmitForm();
        } else {
            //alert('กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง');
        }
    };

    validateForm = () => {
        const {fields} = this.state;
        const errors = {};
        let formIsValid = true;

        if (!fields[INPUT_FIRST_NAME] || fields[INPUT_FIRST_NAME].trim().length === 0) {
            errors[INPUT_FIRST_NAME] = 'กรุณากรอกชื่อ';
            formIsValid = false;
        }
        if (!fields[INPUT_LAST_NAME] || fields[INPUT_LAST_NAME].trim().length === 0) {
            errors[INPUT_LAST_NAME] = 'กรุณากรอกนามสกุล';
            formIsValid = false;
        }
        if (!fields[INPUT_ORGANIZATION_NAME] || fields[INPUT_ORGANIZATION_NAME].trim().length === 0) {
            errors[INPUT_ORGANIZATION_NAME] = 'กรุณากรอกชื่อหน่วยงาน';
            formIsValid = false;
        }
        if (!fields[INPUT_JOB_POSITION] || fields[INPUT_JOB_POSITION].trim().length === 0) {
            errors[INPUT_JOB_POSITION] = 'กรุณากรอกตำแหน่ง';
            formIsValid = false;
        }
        if (!fields[INPUT_OCCUPATION] || fields[INPUT_OCCUPATION].trim().length === 0) {
            errors[INPUT_OCCUPATION] = 'กรุณากรอกอาชีพ';
            formIsValid = false;
        }
        if (!fields[INPUT_EMAIL] || fields[INPUT_EMAIL].trim().length === 0) {
            errors[INPUT_EMAIL] = 'กรุณากรอกอีเมล';
            formIsValid = false;
        } else if (!isValidEmail(fields[INPUT_EMAIL])) {
            errors[INPUT_EMAIL] = 'รูปแบบอีเมลไม่ถูกต้อง';
            formIsValid = false;
        }
        if (!fields[INPUT_USE] || fields[INPUT_USE].trim().length === 0) {
            errors[INPUT_USE] = 'กรุณากรอกการนำไปใช้ประโยชน์';
            formIsValid = false;
        }

        this.setState({errors});
        return formIsValid;
    };

    doSubmitForm = () => {
        const {fields} = this.state;
        const {id} = this.props.data;

        fetch('/api/add_academic_paper_download', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id, fields}),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    //จำว่าเคยกรอกฟอร์มแล้วสำหรับ paper นี้
                    AcademicPaperDetails.addFormSubmitForAcademicPaper(id);

                    this.openPdf();
                    this.setState({
                        fields: {},
                        errors: {},
                        showFormModal: false,
                    });
                } else {
                    alert(result['error']['message']);
                }
            });
    };

    render() {
        const {id, title, firstName, lastName, yearPublished, fundSource, abstract} = this.props.data;
        const {fields, errors} = this.state;

        return (
            <React.Fragment>
                {/*Modal ฟอร์มกรอกข้อมูล*/}
                <Modal
                    dialogClassName={'modal-form'}
                    show={this.state.showFormModal}
                    onHide={this.handleCloseFormModal}
                    centered>
                    <Modal.Body>
                        <div className="redtxt" style={{margin: '10px auto'}}>
                            <h3 style={{textAlign: 'center'}}>กรุณากรอกข้อมูล</h3>
                        </div>

                        <div id="data5" className="detail-inside-box">
                            <div className="required">
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>ชื่อ</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_FIRST_NAME]}
                                               onChange={this.handleInputChange.bind(this, INPUT_FIRST_NAME)}
                                               type="text"
                                               placeholder="กรอกชื่อ"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_FIRST_NAME]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>นามสกุล</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_LAST_NAME]}
                                               onChange={this.handleInputChange.bind(this, INPUT_LAST_NAME)}
                                               type="text" placeholder="กรอกนามสกุล"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_LAST_NAME]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>ชื่อหน่วยงาน</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_ORGANIZATION_NAME]}
                                               onChange={this.handleInputChange.bind(this, INPUT_ORGANIZATION_NAME)}
                                               type="text" placeholder="กรอกชื่อหน่วยงาน"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_ORGANIZATION_NAME]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>ตำแหน่ง</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_JOB_POSITION]}
                                               onChange={this.handleInputChange.bind(this, INPUT_JOB_POSITION)}
                                               type="text" placeholder="กรอกตำแหน่ง"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_JOB_POSITION]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>อาชีพ</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_OCCUPATION]}
                                               onChange={this.handleInputChange.bind(this, INPUT_OCCUPATION)}
                                               type="text" placeholder="กรอกอาชีพ"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_OCCUPATION]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>อีเมล</label>
                                    </div>
                                    <div className="col-md-9">
                                        <input value={fields[INPUT_EMAIL]}
                                               onChange={this.handleInputChange.bind(this, INPUT_EMAIL)}
                                               onKeyDown={e => {
                                                   if (e.key === ' ') {
                                                       e.preventDefault();
                                                   }
                                               }}
                                               type="email" placeholder="กรอกอีเมล"
                                               className="form-control-2 input-md"/>
                                        <ErrorLabel
                                            value={errors[INPUT_EMAIL]}/>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-md-3 nopad">
                                        <label>การนำไปใช้ประโยชน์</label>
                                    </div>
                                    <div className="col-md-9">
                                        <textarea value={fields[INPUT_USE]}
                                                  onChange={this.handleInputChange.bind(this, INPUT_USE)}
                                                  rows={4} placeholder="กรอกการนำไปใช้ประโยชน์"
                                                  className="form-control-2"/>
                                        <ErrorLabel
                                            value={errors[INPUT_USE]}/>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:void(0)"
                               onClick={this.handleFormSubmit}
                               className="btn-submit mt-4 mb-3">
                                ตกลง
                            </a>
                        </div>
                    </Modal.Body>
                </Modal>

                {/*รายละเอียดงานวิจัย/วิชาการ*/}
                <div className="container">
                    <div className="row mt-5">
                        <div className="col">
                            <div className="redtxt">
                                <h3>{title}</h3>
                            </div>
                        </div>
                    </div>
                    <div className="row text-default">
                        <div className="col-6 col-md-3 text-bold">ปีที่เผยแพร่
                            <br/>ชื่อผู้ทำวิจัย
                            <br/>แหล่งทุนวิจัย
                        </div>
                        <div className="col-6 col-md-9">{yearPublished}
                            <br/>{firstName} {lastName}
                            <br/>{fundSource}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <div className="border-inside">
                                <p dangerouslySetInnerHTML={{__html: nl2br(abstract)}}/>
                            </div>
                        </div>
                    </div>
                    <div className="row mt-4">
                        <div className="col">
                            <div className="moredetail btn-red-submit">
                                <a href="javascript:void(0)"
                                   onClick={this.handleClickDownload}
                                   className="btn btn-danger inline"
                                   style={{marginBottom: '20px'}}>Download</a>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .bg-search-service1 .col {
                        padding: 5px;
                    }
                    
                    .modal-dialog {
                        max-width: 700px;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .bg-search-service1 {
                        height: 120px;
                    }
                    
                    .modal-content {
                        border: 3px solid #b50303;
                        border-radius: 0px;
                    }
                    
                    .modal.modal-wide .modal-dialog {
                        width: 90%;
                    }
                    
                    .modal-wide .modal-body {
                        overflow-y: auto;
                    }
                    
                    #tallModal .modal-body p {
                        margin-bottom: 900px
                    }
                    
                    label {
                        margin-top: 15px;
                        margin-bottom: 0;
                    }
                    
                    .form-control-2 {
                        display: block;
                        width: 80%;
                        padding: 0.375rem 0.75rem;
                        line-height: 1.5;
                        color: #252525;
                        background-color: #fff;
                        background-clip: padding-box;
                        border: 1px solid #ced4da;
                        margin-top: 15px;
                        margin-bottom: 0;
                        border-radius: 0.25rem;
                        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                    }
                    
                    .btn-submit {
                        margin: 0 auto;
                        display: table;
                    }
                `}</style>
            </React.Fragment>
        );
    }
}

export default class AcademicPaper extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            dataList: null,
            searchFields: {},
            searchResultList: null,
            showSearchResultModal: false,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
    }

    //https://nextjs.org/learn/basics/fetching-data-for-pages/fetching-data-for-pages

    static getInitialProps = async function ({req, query}) {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        console.log(`baseUrl: ${baseUrl}`);

        const {id} = query;

        if (id == null) {
            return {id, dataList: null};
        }

        const res = await fetch(baseUrl + '/api/get_academic_paper', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id
            }),
        });
        const result = await res.json();
        if (result.error.code === 0) {
            const {dataList} = result;
            return {id, dataList};
        } else {
            return {id, dataList: null};
            //todo: จัดการกรณี error ให้แสดง error message ด้วย
        }
    };

    componentDidMount() {
        //this.doGetAcademicPaper();
    }

    handleInputChange = (field, e) => {
        let {searchFields} = this.state;
        searchFields[field] = e.target.value;
        this.setState({searchFields});
    };

    doGetAcademicPaper = () => {
        fetch('/api/get_academic_paper', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    console.log(result.dataList);
                    this.setState({
                        dataList: result.dataList,
                        pageCount: Math.ceil(result.totalCount / LIMIT_PER_PAGE)
                    }, () => {
                        if (!this.state.firstLoad) {
                            scroller.scrollTo('topOfTable', {
                                duration: 500,
                                smooth: true,
                                offset: -80,
                            });
                        } else {
                            this.setState({
                                firstLoad: false,
                            });
                        }
                    });
                } else {
                    alert(result['error']['message']);
                }
            })
            .catch(error => {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
            });
    };

    handleClickSearch = e => {
        let {searchFields} = this.state;

        if ((searchFields[SEARCH_INPUT_TITLE] && searchFields[SEARCH_INPUT_TITLE].trim().length !== 0)
            || (searchFields[SEARCH_INPUT_YEAR_PUBLISHED] && searchFields[SEARCH_INPUT_YEAR_PUBLISHED].trim().length !== 0)
            || (searchFields[SEARCH_INPUT_NAME] && searchFields[SEARCH_INPUT_NAME].trim().length !== 0)) {

            fetch('/api/search_academic_paper', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({searchFields}),
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
        } else {
            alert('กรุณากรอกชื่อเรื่อง, ปีที่เผยแพร่ หรือชื่อผู้วิจัยของงานวิจัย/วิชาการที่ต้องการค้นหา');
        }
    };

    handleCloseSearchResultModal = e => {
        this.setState({
            showSearchResultModal: false
        });
    };

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetAcademicPaper();
        });
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
                        <div className="redtxt" style={{margin: '10px auto'}}>
                            <h3 style={{textAlign: 'center'}}>ผลการค้นหา</h3>
                        </div>

                        <div id="data5" className="detail-inside-box">
                            {searchResultList != null &&
                            <AcademicPaperList
                                dataList={searchResultList}
                                isSearchResult={true}
                                handleCloseSearchResultModal={this.handleCloseSearchResultModal}
                            />
                            }
                            {searchResultList == null &&
                            <div style={{textAlign: 'center'}}>ไม่พบข้อมูล</div>
                            }
                            <a href="javascript:void(0)"
                               onClick={this.handleCloseSearchResultModal}
                               className="btn-submit mt-4 mb-3">
                                ปิด
                            </a>
                        </div>
                    </Modal.Body>
                </Modal>

                {/*หัวข้อ "งานวิจัยและวิชาการ"*/}
                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 onClick={this.test}>งานวิจัยและวิชาการ</h3>
                        </div>
                    </div>
                </div>

                {/*แถบค้นหางานวิจัยและวิชาการ*/}
                <div className="bg-search-service1">
                    <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
                        <div className="row">
                            <div className="col">
                                <input
                                    value={searchFields[SEARCH_INPUT_TITLE] || ''}
                                    onChange={this.handleInputChange.bind(this, SEARCH_INPUT_TITLE)}
                                    type="text" placeholder="ชื่อเรื่อง"
                                    className="form-control input-md"/>
                            </div>
                            <div className="col">
                                <input
                                    value={searchFields[SEARCH_INPUT_YEAR_PUBLISHED] || ''}
                                    onChange={this.handleInputChange.bind(this, SEARCH_INPUT_YEAR_PUBLISHED)}
                                    type="text" placeholder="ปีที่เผยแพร่"
                                    maxLength={4}
                                    className="form-control input-md"/>
                            </div>
                            <div className="col">
                                <input
                                    value={searchFields[SEARCH_INPUT_NAME] || ''}
                                    onChange={this.handleInputChange.bind(this, SEARCH_INPUT_NAME)}
                                    type="text" placeholder="ชื่อผู้วิจัย"
                                    className="form-control input-md"/>
                            </div>

                            <div className="col">
                                <div className="submitbox">
                                    <a href="javascript:void(0)" className="btn-submit" onClick={this.handleClickSearch}>ค้นหา</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container d-block d-sm-none d-md-none d-lg-none d-xl-none">
                        <div className="row">
                            <div className="col">
                                <div id="uniquename" style={{display: 'none'}}>
                                    <div className="row">
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <input id="textinput" name="textinput" type="text" placeholder="ชื่อเรื่อง" className="form-control input-md"/></div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <input id="textinput" name="textinput" type="text" placeholder="ปีที่เผยแพร่" className="form-control input-md"/></div>
                                        <div className="col-12" style={{paddingTop: '10px'}}>
                                            <input id="textinput" name="textinput" type="text" placeholder="ชื่อผู้วิจัย" className="form-control input-md"/></div>

                                    </div>
                                    <br/>
                                    <div className="submitbox" style={{paddingBottom: '50px'}}><a href="#" className="btn-submit">ค้นหา</a></div>
                                </div>
                                <a href="javascript:ReverseDisplay('uniquename')" className="btn btn-success">
                                    แสดงตัวกรองค้นหา
                                </a></div>
                        </div>
                    </div>
                </div>

                {/*List-งานวิจัยและวิชาการ*/}

                <Element name={'topOfTable'}>
                    {!this.props.id && (this.state.dataList != null) &&
                    <div className="mt-5 mb-5">
                        <AcademicPaperList
                            dataList={this.state.dataList}
                            isSearchResult={false}
                        />
                    </div>
                    }
                    {!this.props.id &&
                    <div style={{textAlign: 'center'}}>
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
                    }
                </Element>


                {/*<ul className="pagination">
                    <li className="pagination-older disabled">
                        <a tabIndex="0" role="button" aria-disabled="true">&lt;</a>
                    </li>
                    <li className="pagination-active">
                        <a role="button" tabIndex="0" aria-label="Page 1 is your current page" aria-current="page">1</a>
                    </li>
                    <li>
                        <a role="button" tabIndex="0" aria-label="Page 2">2</a>
                    </li>
                    <li>
                        <a role="button" tabIndex="0" aria-label="Page 3">3</a>
                    </li>
                    <li className="next">
                        <a tabIndex="0" role="button" aria-disabled="false">&gt;</a>
                    </li>
                </ul>*/}

                {/*Details-งานวิจัยและวิชาการ*/
                    this.props.id &&
                    <AcademicPaperDetails
                        data={this.props.dataList[0]}
                    />
                }

                <style jsx>{`
                    .text-title-top h3 {
                        width: 100%;
                        font-size: 2em;
                        text-align: center;
                        margin-top: 40px;
                    }
                    
                    .btn-submit {
                        margin: 0 auto;
                        display: table;
                    }
                    
                    .bg-search-service1 .col {
                        padding: 5px;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .bg-search-service1 {
                        height: 120px;
                    }
                    
                    .wrap {
                        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
                        border-radius: 4px;
                    }
                    
                    [data-toggle="collapse"] .fa:before {
                        content: "\\f139";
                    }
                    
                    [data-toggle="collapse"].collapsed .fa:before {
                        content: "\\f13a";
                    }
                    
                    .card {
                        background-color: #b40303;
                        border-radius: 0px;
                        margin-top: 10px;
                        border: 1px solid #eeeeee;
                    }
                    
                    .card-body {
                        background-color: white;
                        border: 1px solid #eeeeee;
                    }
                    
                    .btn-link:hover, .btn-link {
                        color: white;
                        text-decoration: none;
                        width: 100%;
                        text-align: left;
                        border: none;
                        cursor: pointer;
                    }
                    
                    .mb-0 i {
                        position: absolute;
                        right: 20px;
                        top: 20px;
                    }
                    
                    @media only screen and (max-width: 991px) {
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                    }
                    
                    @media only screen and (max-width: 767px) {
                        .mb-0 i {
                            display: none;
                        }
                        .bg-search-service1 {
                            height: auto;
                        }
                        .panel-title a {
                            font-size: 1rem;
                        }
                        .btn-link {
                            font-size: 0.9em;
                            text-align: left;
                        }
                        .card-header {
                            padding: 5px;
                        }
                        .btn-link {
                            border: none;
                        }
                    }                           
                `}</style>
            </MainLayout>
        )
    }
}