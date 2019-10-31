import NextHead from 'next/head';
import Router from 'next/router';
import fetch from 'isomorphic-unfetch';
import MainLayout from "../layouts/MainLayout";
import {getLoginUser, formatCourseDateLong, formatCourseDateLongEn, isString, isValidEmail, isValidPid, isPositiveInteger} from "../etc/utils";
import ErrorLabel from '../components/ErrorLabel';
//import { Link, DirectLink, Element, Events, animateScroll as scroll, scrollSpy, scroller } from 'react-scroll';
import {Element, scroller} from 'react-scroll';
import Link from "next/link";
import {HOST_BACKEND, SERVICE_DRIVING_LICENSE, SERVICE_SOCIAL} from "../etc/constants";
import Dialog from "../components/Dialog";
import InputMask from 'react-input-mask';
import Dropzone from 'react-dropzone';

const TOP_OF_FORM = 'topOfForm';

const REGISTER_TRAINEE_TITLE = 'traineeTitle';
const REGISTER_TRAINEE_TITLE_CUSTOM = 'traineeTitleCustom';
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
const REGISTER_TRAINEE_NATIONALITY = 'traineeNationality';

class TraineeRegisterForm extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            fileList: [],
            fileDataUrlList: [],
        };
        this.customTitleInput = React.createRef();
        //this.handleChange = this.handleChange.bind(this);
    }

    handleChange(field, e) {
        this.props.handleChangeCallback(field, e);

        const customTitleInput = this.customTitleInput;
        if (field === REGISTER_TRAINEE_TITLE && e.target.value === '-1') {
            setTimeout(function () {
                customTitleInput.current.focus();
            }, 200);
        }
    }

    handleDropFiles = acceptedFiles => {
        console.log(acceptedFiles);

        const count = this.state.fileDataUrlList.length + acceptedFiles.length;
        if (count > 5) {
            alert('เลือกไฟล์ได้สูงสุดไม่เกิน 5 ไฟล์');
            return;
        }

        acceptedFiles.forEach(file => {
            const {fileList} = this.state;
            fileList.push(file);
            this.setState({fileList});

            const reader = new FileReader();
            reader.onabort = () => console.log('file reading was aborted');
            reader.onerror = () => console.log('file reading has failed');
            reader.onload = () => {
                // Do whatever you want with the file contents
                //console.log(reader);
                const fileUrl = reader.result; // Base64 data
                console.log('URL คือ ' + fileUrl);

                const {fileDataUrlList} = this.state;
                fileDataUrlList.push(fileUrl);
                this.setState({fileDataUrlList});
            };
            reader.readAsDataURL(file);
        });
    };

    handleRejectedFiles = rejectedFiles => {
        let msg = '';
        rejectedFiles.forEach(file => {
            msg = msg.concat(`- ${file.name}\n`);
        });

        alert(`ผิดพลาด: ไฟล์ดังรายชื่อต่อไปนี้มีขนาดใหญ่เกินไป หรือเป็นประเภทไฟล์ที่ไม่รองรับ\n\n${msg}\n*รองรับรูปภาพและ PDF เท่านั้น และขนาดแต่ละไฟล์ต้องไม่เกิน 3 MB`);
    };

    handleClickFilePreview = (index, e) => {
        e.stopPropagation();
        const {fileDataUrlList, fileList} = this.state;
        fileDataUrlList.splice(index, 1);
        fileList.splice(index, 1);
        this.setState({fileDataUrlList, fileList});
    };

    render() {
        let {traineeForm, isReadOnly} = this.props;

        return (
            <React.Fragment>
                <div className="row">
                    <div className="col">
                        <div className="border-inside" style={{marginTop: '10px'}}>
                            <div className="row">
                                <div className="col-md-11 offset-md-1">
                                    <div className="required">

                                        {/*เลือกคนไทย/ต่างชาติ*/}
                                        <div className="row" style={{textAlign: 'center'}}>
                                            <div className="col-md-12">
                                                <div className=""
                                                     onChange={this.handleChange.bind(this, REGISTER_TRAINEE_NATIONALITY)}>
                                                    <div className="md-radio md-radio-inline radiocheck">
                                                        <input id="nationality-thai" name="nationality" type="radio"
                                                               value={1}
                                                               defaultChecked={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1}/>
                                                        <label htmlFor="nationality-thai"> คนไทย / Thai Person</label>
                                                    </div>
                                                    <div className="md-radio md-radio-inline radiocheck">
                                                        <input id="nationality-foreigner" name="nationality" type="radio"
                                                               value={2}
                                                               defaultChecked={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2}/>
                                                        <label htmlFor="nationality-foreigner"> ชาวต่างชาติ / Foreigner</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] &&
                                        <div>
                                            <div className="row mt-3">
                                                <div className="offset-md-4 col-md-8" style={{color: 'red'}}>
                                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'ต้องกรอกข้อมูลทุกช่อง' : 'ต้องกรอกข้อมูลทุกช่อง / All fields are required.'}
                                                </div>
                                            </div>

                                            {/*คำนำหน้า*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                    <label className="mt-2">
                                                        คำนำหน้าชื่อ (ตามบัตรประชาชน)
                                                    </label>
                                                    }
                                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                    <label className="">
                                                        คำนำหน้าชื่อ (ตามหนังสือเดินทาง) /<br/>
                                                        Name Title (as appear on passport)
                                                    </label>
                                                    }
                                                </div>
                                                <div className="col-md-8">
                                                    <div className="row">
                                                        {/*List คำนำหน้า*/}
                                                        <div className="col-12 col-md-6">
                                                            <select value={traineeForm.fields[REGISTER_TRAINEE_TITLE] || '0'}
                                                                    onChange={this.handleChange.bind(this, REGISTER_TRAINEE_TITLE)}
                                                                    className="form-control mt-2">
                                                                <option value="0" disabled>
                                                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เลือกคำนำหน้า' : 'เลือกคำนำหน้า / Select name title'}
                                                                </option>
                                                                {
                                                                    this.props.nameTitleList.map((nameTitle, index) =>
                                                                        <option key={index} value={nameTitle.title}>{nameTitle.title}</option>
                                                                    )
                                                                }
                                                                <option value="-1">
                                                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'อื่นๆ' : 'อื่นๆ / As specified'}
                                                                </option>
                                                            </select>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_TITLE]}/>
                                                        </div>

                                                        {/*คำนำหน้า กรอกเอง*/}
                                                        {traineeForm.fields[REGISTER_TRAINEE_TITLE] === '-1' &&
                                                        <div className="col nopadleft">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_TITLE_CUSTOM] || ''}
                                                                   ref={this.customTitleInput}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_TITLE_CUSTOM)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรอกคำนำหน้า' : 'กรอกคำนำหน้า / Enter name title'}
                                                                   className="form-control-2 input-md mt-3" style={{width: '100%'}}/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_TITLE_CUSTOM]}/>
                                                        </div>
                                                        }
                                                    </div>
                                                </div>
                                            </div>

                                            {/*ชื่อ*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <label className={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'mt-2' : ''}>
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'ชื่อ' : 'ชื่อ / First name'}
                                                    </label>
                                                </div>
                                                <div className="col-md-8">
                                                    <input value={traineeForm.fields[REGISTER_TRAINEE_FIRST_NAME] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_FIRST_NAME)}
                                                           type="text"
                                                           placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'ชื่อ' : 'ชื่อ / First name'}
                                                           className={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'form-control-2 input-md mt-2' : 'form-control-2 input-md'}/>
                                                    <ErrorLabel
                                                        value={traineeForm.errors[REGISTER_TRAINEE_FIRST_NAME]}/>
                                                </div>
                                            </div>

                                            {/*นามสกุล*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <label className="mt-2">
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'นามสกุล' : 'นามสกุล / Last name'}
                                                    </label>
                                                </div>
                                                <div className="col-md-8">
                                                    <input value={traineeForm.fields[REGISTER_TRAINEE_LAST_NAME] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_LAST_NAME)}
                                                           type="text"
                                                           placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'นามสกุล' : 'นามสกุล / Last name'}
                                                           className="form-control-2 input-md mt-2"/>
                                                    <ErrorLabel
                                                        value={traineeForm.errors[REGISTER_TRAINEE_LAST_NAME]}/>
                                                </div>
                                            </div>

                                            {/*เลขประจำตัวประชาชน*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <label
                                                        className="mt-2">{traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เลขประจำตัวประชาชน' : 'เลขที่หนังสือเดินทาง / Passport Number'}</label>
                                                </div>
                                                <div className="col-md-8">
                                                    {/*<InputMask mask="9-9999-99999-99-9"
                                                           value={traineeForm.fields[REGISTER_TRAINEE_PID] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PID)}
                                                           maxLength={13}
                                                           placeholder="กรอกเลขประจำตัวประชาชน 13 หลัก"
                                                           className="form-control-2 input-md mt-2"/>*/}
                                                    <input value={traineeForm.fields[REGISTER_TRAINEE_PID] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PID)}
                                                           type="text"
                                                           placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เลขประจำตัวประชาชน 13 หลัก' : 'เลขที่หนังสือเดินทาง / Passport number'}
                                                           className="form-control-2 input-md mt-2"/>
                                                    <ErrorLabel
                                                        value={traineeForm.errors[REGISTER_TRAINEE_PID]}/>
                                                </div>
                                            </div>

                                            {/*ที่อยู่*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <label className="mt-2">
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ?
                                                            'ที่อยู่ตามบัตรประชาชน' :
                                                            'ที่อยู่ / Address'
                                                        }<br/>
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ?
                                                            '(ข้อมูลใดไม่มี ให้กรอก -)' :
                                                            '(ข้อมูลใดไม่มี ให้กรอก -'
                                                        }
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                        <br/>
                                                        }
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 ?
                                                            'Enter - if not available)' : ''
                                                        }
                                                    </label>
                                                </div>
                                                <div className="col-md-8">
                                                    <div className="row">
                                                        <div className="col-12 col-md-6">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_ADDRESS] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_ADDRESS)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'บ้านเลขที่' : 'บ้านเลขที่ / House number'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_ADDRESS]}/>
                                                        </div>
                                                        <div className="col nopadleft">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_MOO] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_MOO)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'หมู่ที่' : 'หมู่ที่ / Village number'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_MOO]}/>
                                                        </div>
                                                        <div className="w-100"></div>

                                                        <div className="col-12 col-md-6">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_SOI] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SOI)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'ซอย' : 'ซอย / Lane'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_SOI]}/>
                                                        </div>
                                                        <div className="col nopadleft">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_ROAD] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_ROAD)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'ถนน' : 'ถนน / Road'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_ROAD]}/>
                                                        </div>
                                                        <div className="w-100"></div>

                                                        <div className="col-12 col-md-6">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SUB_DISTRICT)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'แขวง/ตำบล' : 'แขวง/ตำบล / Sub-district'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_SUB_DISTRICT]}/>
                                                        </div>
                                                        <div className="col nopadleft">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_DISTRICT] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_DISTRICT)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เขต/อำเภอ' : 'เขต/อำเภอ / District'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_DISTRICT]}/>
                                                        </div>
                                                        <div className="w-100"></div>

                                                        <div className="col-12 col-md-6">
                                                            <input value={traineeForm.fields[REGISTER_TRAINEE_PROVINCE] || ''}
                                                                   onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PROVINCE)}
                                                                   type="text"
                                                                   placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'จังหวัด' : 'จังหวัด / Province'}
                                                                   className="form-control input-md mt-2"/>
                                                            <ErrorLabel
                                                                value={traineeForm.errors[REGISTER_TRAINEE_PROVINCE]}/>
                                                        </div>
                                                        <div className="col nopadleft">
                                                            {/*<input value={traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE] || ''}
                                                               onChange={this.handleChange.bind(this, REGISTER_TRAINEE_POSTAL_CODE)}
                                                               type="text"
                                                               maxLength={5}
                                                               placeholder="รหัสไปรษณีย์"
                                                               className="form-control input-md mt-2"/>
                                                        <ErrorLabel
                                                            value={traineeForm.errors[REGISTER_TRAINEE_POSTAL_CODE]}/>*/}
                                                        </div>
                                                        {/*<div className="w-100"></div>
                                                    <div className="col-12 col-md-6">
                                                        <input id="textinput" name="textinput" type="text" placeholder="เบอร์โทรศัพท์" className="form-control input-md"/>
                                                    </div>*/}
                                                    </div>
                                                </div>
                                            </div>

                                            {/*เบอร์โทร*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <label className="mt-2">
                                                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เบอร์โทรศัพท์' : 'เบอร์โทร / Phone number'}
                                                    </label>
                                                </div>
                                                <div className="col-md-8">
                                                    <input value={traineeForm.fields[REGISTER_TRAINEE_PHONE] || ''}
                                                           onChange={this.handleChange.bind(this, REGISTER_TRAINEE_PHONE)}
                                                           type="text"
                                                           placeholder={traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'เบอร์โทรศัพท์' : 'เบอร์โทร / Phone number'}
                                                           className="form-control-2 input-md mt-2"/>
                                                    <ErrorLabel
                                                        value={traineeForm.errors[REGISTER_TRAINEE_PHONE]}/>
                                                </div>
                                            </div>

                                            <div className="row">
                                                <div className="col-md-4">&nbsp;
                                                </div>
                                                <div className="col-md-8 mt-2" style={{padding: '0px 20px'}}>
                                                    <span style={{color: 'orangered'}}>กรุณาแนบไฟล์สำเนาเอกสารทั้งหมดก่อนกดปุ่มลงทะเบียน หลังจากกดลงทะเบียนแล้วจะไม่สามารถส่งเอกสารเพิ่มได้อีก / Please attach all required documents before submitting the form. Once submitted, you won't be able to submit more documents.</span>
                                                </div>
                                            </div>

                                            {/*รูปภาพ*/}
                                            <div className="row">
                                                <div className="col-md-4">
                                                    <div className="row">
                                                        <div className="col-12">
                                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                            <label className="mt-2">
                                                                เอกสารที่ต้องใช้ในการสมัคร
                                                            </label>
                                                            }
                                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                            <label className="mt-2">
                                                                เอกสารที่ต้องใช้ในการสมัคร<br/>
                                                                Required Documents
                                                            </label>
                                                            }
                                                        </div>
                                                    </div>
                                                    <div className="row">
                                                        <div className="col-12">
                                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                            <ol>
                                                                <li>สำเนาบัตรประชาชน</li>
                                                                <li>สำเนาใบขับขี่เดิม (กรณีต่ออายุใบอนุญาต)</li>
                                                            </ol>
                                                            }
                                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                            <ol>
                                                                <li>สำเนาหนังสือเดินทาง / Copy of passport</li>
                                                                <li>สำเนาใบอนุญาตทำงาน <u>ซึ่งมีรายละเอียดที่อยู่ครบถ้วน</u> / Copy of work permit <u>with complete detailed address</u></li>
                                                                <li>กรณีใบอนุญาตทำงานมีรายละเอียดที่อยู่ไม่ครบถ้วน ให้แนบสำเนาเอกสารอื่นๆ ที่มีรายละเอียดที่อยู่ครบถ้วนมาด้วย / In case address on work
                                                                    permit
                                                                    is incomplete, attach copy of another documents that have complete detailed address.
                                                                </li>
                                                            </ol>
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="col-md-8">
                                                    {<Dropzone onDrop={this.handleDropFiles}
                                                               onDropRejected={this.handleRejectedFiles}
                                                               accept="image/jpeg, image/png, application/pdf"
                                                               minSize={0}
                                                               maxSize={3145728}>
                                                        {({getRootProps, getInputProps}) => (
                                                            <section>
                                                                <div {...getRootProps()}
                                                                     id="fileDocument"
                                                                     className="form-control mt-2">
                                                                    <input {...getInputProps()}/>
                                                                    <div>
                                                                        {this.state.fileDataUrlList.length === 0 &&
                                                                        <div>
                                                                            {'กดเพื่อเลือกไฟล์ หรือลากไฟล์มาปล่อยที่นี่'}<br/>
                                                                            {'(ไม่เกิน 5 ไฟล์, รองรับรูปภาพและ PDF, ขนาดแต่ละไฟล์ไม่เกิน 1 MB)'}<br/>
                                                                            {'Click or drop files here to attach files.'}<br/>
                                                                            {'(5 files maximum, image & PDF supported, 1 MB max size for each)'}
                                                                        </div>
                                                                        }
                                                                        {this.state.fileDataUrlList.length !== 0 &&
                                                                        <div>
                                                                            {'กดที่รูปเพื่อลบ หรือกดที่พื้นที่ว่างหรือลากไฟล์มาปล่อยเพื่อเพิ่มไฟล์'}<br/>
                                                                            {'Click image to remove, or drop another files to add more files.'}
                                                                        </div>
                                                                        }
                                                                    </div>
                                                                    {
                                                                        this.state.fileDataUrlList.map((fileData, index) => {
                                                                            if (fileData.indexOf('data:application/pdf') !== -1) {
                                                                                return (
                                                                                    <div>
                                                                                        <div className="preview-container">
                                                                                            <img className="preview-image"
                                                                                                 src="/static/images/pdf.png"
                                                                                                 style={{width: '80px', margin: '5px', padding: '3px', border: '1px solid #ccc'}}
                                                                                                 onClick={this.handleClickFilePreview.bind(this, index)}/>
                                                                                            <div className="middle"
                                                                                                 onClick={this.handleClickFilePreview.bind(this, index)}>
                                                                                                <i className="fa fa-times-circle" style={{color: '#3c0000'}}/>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span>{this.state.fileList[index].name}</span>
                                                                                    </div>
                                                                                );
                                                                            } else {
                                                                                return (
                                                                                    <div>
                                                                                        <div className="preview-container">
                                                                                            <img className="preview-image"
                                                                                                 src={fileData}
                                                                                                 style={{width: '150px', margin: '5px', padding: '3px', border: '1px solid #ccc'}}
                                                                                                 onClick={this.handleClickFilePreview.bind(this, index)}/>
                                                                                            <div className="middle"
                                                                                                 onClick={this.handleClickFilePreview.bind(this, index)}>
                                                                                                <i className="fa fa-times-circle" style={{color: '#3c0000'}}/>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span>{this.state.fileList[index].name}</span>
                                                                                    </div>
                                                                                );
                                                                            }
                                                                        })
                                                                    }
                                                                </div>
                                                            </section>
                                                        )}
                                                    </Dropzone>}
                                                    <ErrorLabel
                                                        value={traineeForm.errors[REGISTER_TRAINEE_IMAGE_FILE_PID]}/>
                                                </div>
                                            </div>
                                        </div>
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>

                        {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] &&
                        <div>
                            <div className="row">
                                <div className="col">
                                    <div className="bg-gray">
                                        <h2>
                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ?
                                                'มีความประสงค์ขอสมัครเข้ารับการอบรมภาคทฤษฎีในหลักสูตร ดังนี้' :
                                                'มีความประสงค์ขอสมัครเข้ารับการอบรมภาคทฤษฎีในหลักสูตร ดังนี้ / Select Course'
                                            }
                                        </h2>
                                        <ErrorLabel
                                            value={traineeForm.errors[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]}/>
                                        <div className="check-box-2"
                                             onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_COURSE_TYPE)}>
                                            <div className="md-radio md-radio-inline radiocheck">
                                                <input id="course-type-5-hours" name="course-type" type="radio"
                                                       value={1}
                                                       defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 1}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="course-type-5-hours">
                                                    {this.props.courseTypeList[0].title}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="course-type-5-hours">
                                                    {this.props.courseTypeList[0].title}<br/>{this.props.courseTypeList[0].titleEn}
                                                </label>
                                                }
                                            </div>
                                            <br/>
                                            <div className="md-radio md-radio-inline radiocheck">
                                                <input id="course-type-2-hours" name="course-type" type="radio"
                                                       value={2}
                                                       defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 2}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="course-type-2-hours">
                                                    {this.props.courseTypeList[1].title}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="course-type-2-hours">
                                                    {this.props.courseTypeList[1].title}<br/>{this.props.courseTypeList[1].titleEn}
                                                </label>
                                                }
                                            </div>
                                            <br/>
                                            <div className="md-radio md-radio-inline radiocheck">
                                                <input id="course-type-1-hour" name="course-type" type="radio"
                                                       value={3}
                                                       defaultChecked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === 3}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="course-type-1-hour">
                                                    {this.props.courseTypeList[2].title}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="course-type-1-hour">
                                                    {this.props.courseTypeList[2].title}<br/>{this.props.courseTypeList[2].titleEn}
                                                </label>
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="row">
                                <div className="col">
                                    <div className="title-sm">
                                        <h2>
                                            {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ?
                                                'ประเภทใบอนุญาตขับรถ' :
                                                'ประเภทใบอนุญาตขับรถ / Select License Type'
                                            }
                                        </h2>
                                        <ErrorLabel
                                            value={traineeForm.errors[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]}/>
                                        <div className="row">
                                            <div className="col-12 col-md-4">
                                                <input type="checkbox" id="license-type-car"
                                                       checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR)}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="license-type-car">
                                                    รถยนต์ส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="license-type-car">
                                                    รถยนต์ส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''} /
                                                    <br/>Private Automobile{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? ' (Temporary)' : ''}
                                                </label>
                                                }
                                            </div>
                                            <div className="col-12 col-md-4">
                                                <input type="checkbox" id="license-type-bicycle"
                                                       checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE]}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE)}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="license-type-bicycle">
                                                    รถจักรยานยนต์ส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="license-type-bicycle">
                                                    รถจักรยานยนต์ส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''} /
                                                    <br/>Private Motorcycle{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? ' (Temporary)' : ''}
                                                </label>
                                                }
                                            </div>
                                            <div className="col-12 col-md-4">
                                                <input type="checkbox" id="license-type-tricycle"
                                                       checked={traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE]}
                                                       onChange={this.handleChange.bind(this, REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE)}/>
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 &&
                                                <label htmlFor="license-type-tricycle">
                                                    รถสามล้อส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''}
                                                </label>
                                                }
                                                {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 2 &&
                                                <label htmlFor="license-type-tricycle">
                                                    รถสามล้อส่วนบุคคล{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? 'ชั่วคราว' : ''} /
                                                    <br/>Private Motor Tricycle{traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] == 1 ? ' (Temporary)' : ''}
                                                </label>
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="border-red"></div>

                            <div className="btn-red-submit mb-4 mt-4" style={{_marginTop: '20px'}}>
                                <button type="submit" className="btn btn-danger">
                                    {traineeForm.fields[REGISTER_TRAINEE_NATIONALITY] === 1 ?
                                        'ลงทะเบียน' :
                                        'ลงทะเบียน / Register'
                                    }
                                </button>
                            </div>
                        </div>
                        }
                    </div>
                </div>

                <style jsx>{`
                    .preview-container {
                        position: relative;
                        display: inline;
                    }
                    .preview-container img {
                        opacity: 1;
                        transition: .3s ease;
                    }
                    .preview-container:hover img {
                        opacity: 0.3;
                        transition: .3s ease;
                    }
                    .middle {
                        transition: .3s ease;
                        opacity: 0;
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        -ms-transform: translate(-50%, -50%);
                        text-align: center;
                    }
                    .preview-container:hover .middle {
                        opacity: 1;
                        transition: .3s ease;
                    }
                    #fileDocument  {
                        outline: 2px dashed #92b0b3;
                        outline-offset: -10px;
                        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
                        transition: outline-offset .15s ease-in-out, background-color .15s linear;
                        padding: 100px 0 30px;
                        text-align: center !important;
                        margin: 0;
                        width: 100% !important;
                        cursor: pointer;
                    }
                    #fileDocument:after {  
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
                    
                    .files input  {
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

        this.RegisterForm = React.createRef();

        this.state = {
            step: 1,
            traineeForm: {
                fields: {},
                errors: {},
            },

            fileDataUrlList: [],

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

        //this.setupDropZone();

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

    setupDropZone() {
        Dropzone.options.dropzone = {
            url: 'upload.php',
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 5,
            maxFilesize: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            init: function () {
                dzClosure = this; // Makes sure that 'this' is understood inside the functions below.

                // for Dropzone to process the queue (instead of default form behavior):
                document.getElementById("submit-all").addEventListener("click", function (e) {
                    // Make sure that the form isn't actually being sent.
                    e.preventDefault();
                    e.stopPropagation();
                    dzClosure.processQueue();
                });

                //send all the form data along with the files:
                this.on("sendingmultiple", function (data, xhr, formData) {
                    formData.append("firstname", jQuery("#firstname").val());
                    formData.append("lastname", jQuery("#lastname").val());
                });
            }
        }
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

    //https://stackoverflow.com/questions/35940290/how-to-convert-base64-string-to-javascript-file-object-like-as-from-file-input-f

    handleChange = (field, e) => {
        let {traineeForm} = this.state;
        let {fields} = traineeForm;
        let {target} = e;

        if (field === REGISTER_TRAINEE_TITLE && target.value === "-1") {
            //alert('ok');
        }

        if (field === REGISTER_TRAINEE_PROVINCE || field === REGISTER_TRAINEE_POSTAL_CODE) {
            fields[field] = target.value.trim();
        } else if (field === REGISTER_TRAINEE_PID) {
            if (fields[REGISTER_TRAINEE_NATIONALITY] === 1) { // คนไทย
                fields[field] = target.value.trim().substring(0, 13);
            } else if (fields[REGISTER_TRAINEE_NATIONALITY] === 2) { // ชาวต่างชาติ
                fields[field] = target.value.trim();
            }
        } else {
            if (target.type === 'checkbox') {
                if (target.checked
                    && (fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === '2' || fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] === '3')
                    && (fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR]
                        || fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE]
                        || fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE])) {
                    alert('การอบรมสำหรับผู้ขอ \'ต่อ\' ใบอนุญาต สามารถเลือกประเภทใบอนุญาตได้ 1 ประเภทเท่านั้น\n\nหมายเหตุ: ถ้าหากต้องการใบอนุญาตมากกว่า 1 ประเภท จะต้องสมัครใหม่อีกครั้งสำหรับแต่ละประเภทใบอนุญาต');
                    fields[field] = false;
                } else {
                    fields[field] = target.checked;
                }
            } else if (target.type === 'file') {
                fields[field] = target.files[0];
            } else if (field === REGISTER_TRAINEE_NATIONALITY) {
                fields[field] = parseInt(target.value);
                if (parseInt(target.value) === 1) {
                    //alert('คนไทย');
                } else if (parseInt(target.value) === 2) {
                    //alert('ชาวต่างขาติ');
                }
            } else {
                fields[field] = target.value;
            }
        }
        this.setState({traineeForm}, () => {
            //this.validateForm();
        });
    };

    handleDropFiles = acceptedFiles => {
    };

    handleClickFilePreview = (index) => {
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
            errors[REGISTER_TRAINEE_TITLE] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณาเลือกคำนำหน้า' : 'เลือกคำนำหน้า / Select name title';
            formIsValid = false;
        } else if (fields[REGISTER_TRAINEE_TITLE] === '-1' && (!fields[REGISTER_TRAINEE_TITLE_CUSTOM] || fields[REGISTER_TRAINEE_TITLE_CUSTOM].trim().length === 0)) {
            errors[REGISTER_TRAINEE_TITLE_CUSTOM] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกคำนำหน้า' : 'กรอกคำนำหน้า / Enter name title';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_FIRST_NAME] || fields[REGISTER_TRAINEE_FIRST_NAME].trim().length === 0) {
            errors[REGISTER_TRAINEE_FIRST_NAME] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกชื่อ' : 'กรอกชื่อ / Enter first name';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_LAST_NAME] || fields[REGISTER_TRAINEE_LAST_NAME].trim().length === 0) {
            errors[REGISTER_TRAINEE_LAST_NAME] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกนามสกุล' : 'กรอกนามสกุล / Enter last name';
            formIsValid = false;
        }

        if (fields[REGISTER_TRAINEE_NATIONALITY] === 1) {
            if ((!fields[REGISTER_TRAINEE_PID] || fields[REGISTER_TRAINEE_PID].trim().length !== 13)) {
                errors[REGISTER_TRAINEE_PID] = 'กรุณากรอกเลขประจำตัวประชาชน 13 หลัก';
                formIsValid = false;
            } else if (!isValidPid(fields[REGISTER_TRAINEE_PID].trim())) {
                errors[REGISTER_TRAINEE_PID] = 'เลขประจำตัวประชาชนไม่ถูกต้อง';
                formIsValid = false;
            }
        }
        if (!fields[REGISTER_TRAINEE_PID] && (fields[REGISTER_TRAINEE_NATIONALITY] === 2)) {
            errors[REGISTER_TRAINEE_PID] = 'กรอกเลขที่หนังสือเดินทาง / Enter passport number';
            formIsValid = false;
        }

        if (!fields[REGISTER_TRAINEE_ADDRESS] || fields[REGISTER_TRAINEE_ADDRESS].trim().length === 0) {
            errors[REGISTER_TRAINEE_ADDRESS] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกบ้านเลขที่' : 'กรอกบ้านเลขที่ / Enter house number';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_MOO] || fields[REGISTER_TRAINEE_MOO].trim().length === 0) {
            errors[REGISTER_TRAINEE_MOO] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกหมู่' : 'กรอกหมู่ / Enter village number';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_SOI] || fields[REGISTER_TRAINEE_SOI].trim().length === 0) {
            errors[REGISTER_TRAINEE_SOI] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกซอย' : 'กรอกซอย / Enter lane';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_ROAD] || fields[REGISTER_TRAINEE_ROAD].trim().length === 0) {
            errors[REGISTER_TRAINEE_ROAD] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกถนน' : 'กรอกถนน / Enter road';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_SUB_DISTRICT] || fields[REGISTER_TRAINEE_SUB_DISTRICT].trim().length === 0) {
            errors[REGISTER_TRAINEE_SUB_DISTRICT] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกแขวง/ตำบล' : 'กรอกแขวง/ตำบล / Enter sub-district';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_DISTRICT] || fields[REGISTER_TRAINEE_DISTRICT].trim().length === 0) {
            errors[REGISTER_TRAINEE_DISTRICT] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกเขต/อำเภอ' : 'กรอกเขต/อำเภอ / Enter district';
            formIsValid = false;
        }
        if (!fields[REGISTER_TRAINEE_PROVINCE] || fields[REGISTER_TRAINEE_PROVINCE].trim().length === 0) {
            errors[REGISTER_TRAINEE_PROVINCE] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกจังหวัด' : 'กรอกจังหวัด / Enter province';
            formIsValid = false;
        }
        /*if (!fields[REGISTER_TRAINEE_POSTAL_CODE]
            || fields[REGISTER_TRAINEE_POSTAL_CODE].trim().length !== 5
            || !isPositiveInteger(fields[REGISTER_TRAINEE_POSTAL_CODE])) {
            errors[REGISTER_TRAINEE_POSTAL_CODE] = 'กรุณากรอกเลขรหัสไปรษณีย์ 5 หลัก';
            formIsValid = false;
        }*/
        if (!fields[REGISTER_TRAINEE_PHONE] || fields[REGISTER_TRAINEE_PHONE].trim().length === 0) {
            errors[REGISTER_TRAINEE_PHONE] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณากรอกเบอร์โทรศัพท์' : 'กรอกเบอร์โทรศัพท์ / Enter phone number';
            formIsValid = false;
        }
        /*if (!fields[REGISTER_TRAINEE_IMAGE_FILE_PID]) {
            errors[REGISTER_TRAINEE_IMAGE_FILE_PID] = 'กรุณาใส่รูปภาพสำเนาบัตรประชาชน (สำหรับคนไทย) หรือสำเนาหนังสือเดินทาง (สำหรับชาวต่างชาติ)';
            formIsValid = false;
        }*/
        if (!fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]) {
            errors[REGISTER_TRAINEE_SELECTED_COURSE_TYPE] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณาเลือกประเภทหลักสูตร' : 'เลือกประเภทหลักสูตร / Select course type';
            formIsValid = false;
        }

        /* https://stackoverflow.com/questions/27864951/how-to-access-childs-state-in-react */
        const registerForm = this.RegisterForm.current;
        if (registerForm.state.fileList.length === 0) {
            errors[REGISTER_TRAINEE_IMAGE_FILE_PID] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณาแนบไฟล์เอกสารที่ต้องใช้ในการสมัคร' : 'แนบไฟล์เอกสารที่ต้องใช้ในการสมัคร / Attach required documents';
            formIsValid = false;
        }

        const fieldLicenseTypeCar = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR];
        const fieldLicenseTypeBicycle = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE];
        const fieldLicenseTypeTricycle = fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE];

        if (!fieldLicenseTypeCar && !fieldLicenseTypeBicycle && !fieldLicenseTypeTricycle) {
            errors[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR] = fields[REGISTER_TRAINEE_NATIONALITY] === 1 ? 'กรุณาเลือกประเภทใบอนุญาตขับรถ' : 'เลือกประเภทใบอนุญาตขับรถ / Select driving license type';
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
        // ถ้าไม่ได้ login จะส่ง loginToken เป็นสตริงว่าง ('') ซึ่งต่างจากวิชาการและสังคม ที่จะส่ง null
        // ทั้งนี้เพราะถ้ากำหนดเป็น null เมื่อเก็บลง FormData แล้วจะถูกแปลงเป็นสตริง 'null'
        const loginToken = user === null ? '' : getLoginUser().loginToken;

        const {traineeForm} = this.state;
        //const traineeData = traineeForm.fields;

        const formData = new FormData();
        formData.append('loginToken', loginToken);
        formData.append('courseId', this.props.course.id);
        formData.append(REGISTER_TRAINEE_TITLE, traineeForm.fields[REGISTER_TRAINEE_TITLE] === '-1' ? traineeForm.fields[REGISTER_TRAINEE_TITLE_CUSTOM] : traineeForm.fields[REGISTER_TRAINEE_TITLE]);
        formData.append(REGISTER_TRAINEE_FIRST_NAME, traineeForm.fields[REGISTER_TRAINEE_FIRST_NAME]);
        formData.append(REGISTER_TRAINEE_LAST_NAME, traineeForm.fields[REGISTER_TRAINEE_LAST_NAME]);
        formData.append(REGISTER_TRAINEE_PID, traineeForm.fields[REGISTER_TRAINEE_PID]);
        formData.append(REGISTER_TRAINEE_ADDRESS, traineeForm.fields[REGISTER_TRAINEE_ADDRESS]);
        formData.append(REGISTER_TRAINEE_MOO, traineeForm.fields[REGISTER_TRAINEE_MOO]);
        formData.append(REGISTER_TRAINEE_SOI, traineeForm.fields[REGISTER_TRAINEE_SOI]);
        formData.append(REGISTER_TRAINEE_ROAD, traineeForm.fields[REGISTER_TRAINEE_ROAD]);
        formData.append(REGISTER_TRAINEE_SUB_DISTRICT, traineeForm.fields[REGISTER_TRAINEE_SUB_DISTRICT]);
        formData.append(REGISTER_TRAINEE_DISTRICT, traineeForm.fields[REGISTER_TRAINEE_DISTRICT]);
        formData.append(REGISTER_TRAINEE_PROVINCE, traineeForm.fields[REGISTER_TRAINEE_PROVINCE]);
        //formData.append(REGISTER_TRAINEE_POSTAL_CODE, traineeForm.fields[REGISTER_TRAINEE_POSTAL_CODE]);
        formData.append(REGISTER_TRAINEE_PHONE, traineeForm.fields[REGISTER_TRAINEE_PHONE]);
        formData.append(REGISTER_TRAINEE_SELECTED_COURSE_TYPE, traineeForm.fields[REGISTER_TRAINEE_SELECTED_COURSE_TYPE]);
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_CAR] ? '1' : '0');
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_BICYCLE] ? '1' : '0');
        formData.append(REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE,
            traineeForm.fields[REGISTER_TRAINEE_SELECTED_LICENSE_TYPE_TRICYCLE] ? '1' : '0');

        const registerForm = this.RegisterForm.current;
        for (let i = 0; i < registerForm.state.fileList.length; i++) {
            formData.append(REGISTER_TRAINEE_IMAGE_FILE_PID, registerForm.state.fileList[i]);
        }

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
                    const courseRegId = result['courseRegId'];

                    alert(msg);
                    window.open(
                        `${HOST_BACKEND}/pages/print_dl_registration_form.php?trainee_id=${courseRegId}&payment=1&user=1`,
                        '_blank'
                    );
                    Router.back();

                    /*this.showDialog(msg, 'success', () => {
                        this.dismissDialog();
                        Router.back();
                    });*/
                } else {
                    alert(msg);
                }
            })
            .catch(err => {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server: ' + err);
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

    /*Select Course
    Training course for requesting a driver's license.
    Training course for extending a driver's license. (the license has expired 1-3 years ago)
    Training course for extending a driver's license. (the license has expired up to 1 year ago)

    Select Driver's License Type
    Private car
    Private motorcycle
    Private motor tricycle*/

    render() {
        let {traineeForm, step, dialog} = this.state;

        return (
            <MainLayout>
                <NextHead>
                    <style>{'body_ { background-color: red; }'}</style>
                    {/*<script src="/static/dropzone/dropzone.js"/>
                    <link rel="stylesheet" href="/static/dropzone/dropzone.css"/>*/}
                </NextHead>

                {
                    this.props.course && this.state.courseTypeList &&
                    <div className="container">
                        <div className="row">
                            <div className="col">
                                {/*<p>โครงการบริการวิชาการ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>*/}
                                <h3 style={{fontSize: '2em', textAlign: 'center', marginTop: '40px', width: '100%'}}>
                                    ใบสมัครเข้ารับการอบรมหลักสูตรด้านใบอนุญาตขับรถภาคทฤษฎี<br/>Registration Form
                                </h3>
                            </div>
                        </div>
                        {/*ชื่อหลักสูตร, วันที่อบรม, สถานที่อบรม*/}
                        <div className="row">
                            <div className="col">
                                <div className="border-project">
                                    <div className="row">
                                        <div className="col-12">
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">หลักสูตร /<br/>Course</div>
                                                <div className="col-sm-9 text-gray">{this.props.course.name} /<br/>Training Course For Requesting/Extending a Driving License.</div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-3 text-black ">วันที่อบรม /<br/>Training Day</div>
                                                <div className="col-sm-9 text-gray">{formatCourseDateLong(this.props.course.beginDate, this.props.course.endDate)} /<br/>{formatCourseDateLongEn(this.props.course.beginDate, this.props.course.endDate)}</div>
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
                        <form id="applicationFormGroup"
                              method="post"
                              noValidate={true}
                              onSubmit={this.handleSubmit}>
                            {/*ขั้นตอน 1 กรอกข้อมูลผู้สมัครอบรม*/}
                            {((step === 1) || (step === 2)) &&
                            <div style={{marginTop: '15px'}}>
                                {/*หัวข้อ*/}
                                <div className="row" style={{border: '0px solid red', clear: 'both'}}>
                                    <div className="col">
                                        <h4 className="text-black" style={{marginTop: '20px'}}>
                                            <img src="/static/images/title-detail-icon.svg"/>&nbsp;ข้อมูลผู้สมัครอบรม / Registrant Information
                                        </h4>
                                    </div>
                                </div>

                                {/*ฟอร์มข้อมูลผู้สมัครอบรม*/}
                                <Element name={TOP_OF_FORM}>
                                    <TraineeRegisterForm
                                        ref={this.RegisterForm}
                                        traineeForm={traineeForm}
                                        nameTitleList={this.props.nameTitleList}
                                        courseTypeList={this.state.courseTypeList}
                                        handleChangeCallback={this.handleChange}
                                        handleDropFilesCallback={this.handleDropFiles}
                                        handleClickFilePreviewCallback={this.handleClickFilePreview}
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