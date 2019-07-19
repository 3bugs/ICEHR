import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import TraineeFormAutoSuggest from "../components/TraineeFormAutoSuggest";
import {SERVICE_TRAINING} from "../etc/constants";
import {getLoginUser, formatCourseDateShort, formatCourseDateLong, getDateFormatFromDateObject} from "../etc/utils";
import DatePicker from "react-datepicker";
import ErrorLabel from '../components/ErrorLabel';
import {scroller} from "react-scroll/modules";
import Dialog from "../components/Dialog";

export default class UploadSlip extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            inputFormNumber: '',
            traineeFormData: null,
            errorMessage: null,
            fields: {},
            errors: {},
            dialog: {
                isOpen: false,
                message: '',
                textColor: '#000',
                onCloseCallback: null,
            },
        };
    }

    handleInputFormNumberChange = (event, {newValue, method}) => {
        this.setState({
            inputFormNumber: newValue.trim(),
        }, () => {

        });
    };

    handleSubmitInputFormNumber = event => {
        if (this.state.inputFormNumber !== '') {
            /*this.setState({
                traineeFormData: null
            });*/
            fetch('/api/get_trainee_by_form_number', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    formNumber: this.state.inputFormNumber
                }),
            })
                .then(result => result.json())
                .then(result => {
                    if (result['error']['code'] === 0) {
                        this.setState({
                            traineeFormData: result['data'],
                            errorMessage: null,
                        });
                    } else {
                        this.setState({
                            traineeFormData: null,
                            errorMessage: result['error']['message'],
                        });
                    }
                });
        } else {
            //todo: แสดง error บอกให้กรอกข้อมูล
        }
    };

    handleChangeAmount = e => {
        const {fields} = this.state;
        fields.amount = e.target.value;
        this.setState({fields})
    };

    handleChangeTransferDate = e => {
        const {fields} = this.state;
        fields.transferDate = e;
        this.setState({fields})
    };

    handleInputFileChange = e => {
        console.log(event.target.files[0]);
        const {fields} = this.state;
        fields.selectedFile = e.target.files[0];
        this.setState({fields});
    };

    handleSubmitTransferNotification = e => {
        e.preventDefault();
        if (this.validateForm()) {
            this.doSubmitTransferNotification();
        } else {
            this.showDialog("กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง", "error", () => {
                this.dismissDialog();
            });
        }
    };

    validateForm = () => {
        let formIsValid = true;
        const {fields} = this.state;
        const errors = {};

        if (!fields.amount) {
            errors.amount = 'กรุณากรอกยอดเงินที่โอน';
            formIsValid = false;
        } else if (fields.amount <= 0) {
            errors.amount = 'กรุณากรอกจำนวนเงินมากกว่า 0';
            formIsValid = false;
        }
        if (!fields.transferDate) {
            errors.transferDate = 'กรุณาระบุวันที่โอนเงิน';
            formIsValid = false;
        }
        if (!fields.selectedFile) {
            errors.selectedFile = 'กรุณาเลือกไฟล์รูปภาพ Slip';
            formIsValid = false;
        }

        this.setState({errors});
        return formIsValid;
    };

    doSubmitTransferNotification = e => {
        const user = getLoginUser();
        const {fields, traineeFormData} = this.state;
        const formData = new FormData();
        formData.append('formNumber', traineeFormData.formNumber); //ส่ง form number เพื่อไปแยกประเภท service
        formData.append('memberId', user ? user.id : 0);
        formData.append('traineeId', traineeFormData.trainee.id);
        formData.append('amount', fields.amount);
        formData.append('transferDate', getDateFormatFromDateObject(fields.transferDate));
        formData.append('file', fields.selectedFile);

        fetch('/api/add_transfer_notification', {
            method: 'post',
            body: formData,
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    //alert(result['debug']);
                    this.showDialog("ส่งข้อมูลสำเร็จ", "success", () => {
                        this.dismissDialog();
                        this.setState({
                            inputFormNumber: '',
                            traineeFormData: null,
                            errorMessage: null,
                            fields: {},
                            errors: {},
                        });
                    });
                } else {
                    const errorMessage = result['error']['message'];
                    this.showDialog(errorMessage, "error", () => {
                        this.dismissDialog();
                    });
                }
            });
    };

    setDatePickerMinDate = () => {
        const d = new Date();
        const year = 2019;
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
        const {traineeFormData, errorMessage, fields, errors, dialog} = this.state;

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3>ส่งหลักฐานการโอนเงิน</h3></div>
                    </div>
                    <div className="row form-other mt-3">
                        <div className="col-md-2">
                            <label>เลขที่ใบสมัคร</label>
                        </div>
                        <div className="col-md-6">
                            <TraineeFormAutoSuggest
                                value={this.state.inputFormNumber}
                                onChange={this.handleInputFormNumberChange}
                            />
                        </div>
                        <div className="col-md-4 nopad">
                            <a href="javascript:void(0)"
                               className="btn-submit"
                               onClick={this.handleSubmitInputFormNumber}
                               style={{padding: '5px 0px', width: '100px', textAlign: 'center'}}>
                                ค้นหา
                            </a>
                        </div>
                    </div>

                    {!traineeFormData && !errorMessage &&
                    <div style={{height: '60px'}}/>
                    }

                    {errorMessage &&
                    <div style={{color: 'red', margin: '30px 0', textAlign: 'center'}}>
                        {this.state.errorMessage}
                    </div>
                    }

                    {traineeFormData &&
                    <div className="row">
                        <div className="col">
                            <div className="bg-gray">
                                <div className="number-check">
                                    เลขที่ใบสมัคร<br/>{traineeFormData.formNumber}
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        ชื่อผู้สมัคร
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block " style={{border: '0px solid red'}}>
                                        {`${traineeFormData.trainee.title} ${traineeFormData.trainee.firstName} ${traineeFormData.trainee.lastName}`}
                                    </div>
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        ชื่อหลักสูตร / รุ่นที่
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        {traineeFormData.course.name}
                                    </div>
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        วันที่จัด
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        {formatCourseDateLong(traineeFormData.course.beginDate, traineeFormData.course.endDate)}
                                    </div>
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        สถานที่อบรม
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        {traineeFormData.course.place}
                                    </div>
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        ยอดเงิน (บาท)
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        <input value={fields.amount || ''}
                                               onChange={this.handleChangeAmount}
                                               type="number"
                                               placeholder="กรอกยอดเงินที่โอน"
                                               className="form-control input-md my-react-date-picker-2"/>
                                        <ErrorLabel
                                            value={errors.amount}/>
                                    </div>
                                </div>
                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        วันที่โอนเงิน
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        {/*<input autoComplete="off" className="datepicker form-control-4 boxbox" placeholder="DD/MM/YY"/>*/}
                                        <DatePicker
                                            selected={fields.transferDate || ''}
                                            onChange={this.handleChangeTransferDate}
                                            onKeyDown={e => {
                                                //if (e.key === ' ') {
                                                e.preventDefault();
                                                //}
                                            }}
                                            showMonthDropdown
                                            showYearDropdown
                                            dropdownMode="select"
                                            placeholderText="ระบุวันที่โอนเงิน"
                                            dateFormat="dd/MM/yyyy"
                                            minDate={this.setDatePickerMinDate()}
                                            maxDate={new Date()}
                                            className="form-control input-md my-react-date-picker-2"/>
                                        <ErrorLabel
                                            value={errors.transferDate}/>
                                    </div>
                                </div>
                                <div className="row text-default mt-2">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        รูปภาพใบสลิปโอนเงิน<br/><span className="smsize">* ขนาดไฟล์ไม่เกิน 5 MB</span>
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        <div>
                                            {/*<label htmlFor="file-upload" className="custom-file-upload">
                                                <i className="fa fa-upload"></i>&nbsp;&nbsp;เลือกไฟล์
                                            </label>
                                            <input id="file-upload" type="file"/>
                                            <div style={{marginTop: '10px'}}>
                                                <img src="../images/image-test.png" style={{width: '200px', margin: '0 0 10px 10px'}}/>
                                            </div>*/}

                                            <div className="row">
                                                <div className="col-md-9">
                                                    {/*<form method="post" action="#" id="#">*/}
                                                        <div className="form-group files" style={{margin: 0}}>
                                                            <input type="file" name="file"
                                                                   accept="image/*"
                                                                   onChange={this.handleInputFileChange}
                                                                   className="form-control" multiple=""/>
                                                            {fields.selectedFile &&
                                                            <img src={fields.selectedFile ? URL.createObjectURL(fields.selectedFile) : null}
                                                                 style={{position: 'absolute', width: '100px', top: '20px', left: '20px'}}/>
                                                            }
                                                        </div>
                                                        <ErrorLabel
                                                            value={errors.selectedFile}/>
                                                    {/*</form>*/}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="row text-default">
                                    <div className="col-md-3 text-bold d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        &nbsp;
                                    </div>
                                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                                        <div className="sendbox" style={{marginTop: '10px', marginBottom: '5px'}}>
                                            <a href="javascript:void(0)"
                                               className="btn btn-danger"
                                               onClick={this.handleSubmitTransferNotification}>
                                                ส่ง
                                            </a>
                                            {/*<a href="#" className="btn btn-light">ยกเลิก</a>*/}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    }
                </div>

                <Dialog message={dialog.message}
                        textColor={dialog.textColor}
                        isOpen={dialog.isOpen}
                        onCloseCallback={dialog.onCloseCallback}/>

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