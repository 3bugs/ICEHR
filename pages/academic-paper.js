import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import fetch from 'isomorphic-unfetch';
import {nl2br} from "../etc/utils";
import $ from 'jquery';

class AcademicPaperListItem extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    render() {
        const {show} = this.props;
        const {id, title, firstName, lastName, yearPublished} = this.props.data;

        return (
            <React.Fragment>
                <div className="card">
                    <div className="card-header" id={`heading${id}`}>
                        <h5 className="mb-0">
                            <button className="btn-link" data-toggle="collapse" data-target={`#collapse${id}`} aria-expanded="true" aria-controls={`collapse${id}`}>
                                <i className="fa" aria-hidden="true"/>
                                {title}<span className="yeartop">ปีที่เผยแพร่ {yearPublished}</span>
                            </button>
                        </h5>
                    </div>
                    <div id={`collapse${id}`} className={'collapse ' + (show ? 'show' : '')} aria-labelledby={`heading${id}`} data-parent="#accordion">
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
                                        <a href="javascript:void(0)" className="btn btn-outline-success">View</a>
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
        return (
            <React.Fragment>
                <div className="container mt-5 mb-5">
                    <div className="row">
                        <div className="col">
                            <div className="container">
                                <div id="accordion">
                                    {
                                        this.props.dataList.map((data, index) => (
                                            <AcademicPaperListItem
                                                data={data}
                                                show={index === 0}
                                            />
                                        ))
                                    }
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
        this.state = {};
    }

    render() {
        const {id, title, firstName, lastName, yearPublished, fundSource, abstract} = this.props.data;

        return (
            <React.Fragment>
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
                                <a href="#data5" className="btn btn-danger inline" style={{marginBottom: '20px'}}>Download</a>

                                <div style={{display: 'none'}}>
                                    <div id="data5" className="detail-inside-box">
                                        <div className="required">
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>ชื่อ</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกชื่อ" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>นามสกุล</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกนามสกุล" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>ชื่อหน่วยงาน</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกชื่อหน่วยงาน" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>ตำแหน่ง</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกตำแหน่ง" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>อาชีพ</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกอาชีพ" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>อีเมล</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <input id="textinput" name="textinput" type="text" placeholder="กรอกอีเมล" className="form-control-2 input-md"/></div>
                                            </div>
                                            <div className="row">
                                                <div className="col-md-3">
                                                    <label>การนำไปใช้ประโยชน์</label>
                                                </div>
                                                <div className="col-md-9">
                                                    <textarea className="form-control-2" id="textarea" name="textarea" rows="4"/>
                                                </div>
                                            </div>
                                        </div>
                                        <br/><a href="#" className="btn-submit">ตกลง</a></div>
                                </div>
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
                    
                    .form-control-2 {
                        display: block;
                        width: 80%;
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
        this.state = {};
    }

    //https://nextjs.org/learn/basics/fetching-data-for-pages/fetching-data-for-pages

    static getInitialProps = async function ({req, query}) {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const {id} = query;

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
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                {/*หัวข้อ "งานวิจัยและวิชาการ"*/}
                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3 onClick={this.test}>งานวิจัยและวิชาการ</h3></div>
                    </div>
                </div>

                {/*แถบค้นหางานวิจัยและวิชาการ*/}
                <div className="bg-search-service1">
                    <div className="container form-search d-none d-sm-block d-md-block d-lg-block d-xl-block">
                        <div className="row">
                            <div className="col">
                                <input id="textinput" name="textinput" type="text" placeholder="ชื่อเรื่อง" className="form-control input-md"/></div>
                            <div className="col">
                                <input id="textinput" name="textinput" type="text" placeholder="ปีที่เผยแพร่" className="form-control input-md"/></div>
                            <div className="col">
                                <input id="textinput" name="textinput" type="text" placeholder="ชื่อผู้วิจัย" className="form-control input-md"/></div>

                            <div className="col">
                                <div className="submitbox"><a href="#" className="btn-submit">ค้นหา</a></div>
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

                {/*List-งานวิจัยและวิชาการ*/
                    !this.props.id &&
                    <AcademicPaperList
                        dataList={this.props.dataList}
                    />
                }

                {/*Details-งานวิจัยและวิชาการ*/
                    this.props.id &&
                    <AcademicPaperDetails
                        data={this.props.dataList[0]}
                    />
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