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

export default class CheckStatus extends React.Component {
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

  handleInputFormNumberChange = newText => {
    this.setState({
      inputFormNumber: newText,
    }, () => {

    });
  };

  handleSubmitInputFormNumber = formNumber => {
    if (this.validateSuggestForm(formNumber)) {
      /*this.setState({
          traineeFormData: null
      });*/
      fetch('/api/get_trainee_by_form_number', {
        method: 'post',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          formNumber: formNumber
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
    }
  };

  validateSuggestForm = (inputFormNumber) => {
    //const {inputFormNumber} = this.state;
    let valid = true;
    if (inputFormNumber.length === 0) {
      alert("กรุณากรอกเลขที่ใบสมัคร\n-----\n\nเลขที่ใบสมัคร จะขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' เท่านั้น แล้วตามด้วยตัวเลข 8 หรือ 12 หลัก");
      valid = false;
    } else if (inputFormNumber.length !== 12 && inputFormNumber.length !== 17) {
      alert("เลขที่ใบสมัคร ต้องขึ้นต้นด้วย 'AC' หรือ 'SO' หรือ 'DL' เท่านั้น แล้วตามด้วยตัวเลข 8 หรือ 12 หลัก");
      valid = false;
    } else if (inputFormNumber.substring(0, 2).toUpperCase() === 'AC' && inputFormNumber.length != 17) {
      alert("เลขที่ใบสมัครที่ขึ้นต้นด้วย 'AC' จะต้องตามด้วยตัวเลข 12 หลัก");
      valid = false;
    } else if ((inputFormNumber.substring(0, 2).toUpperCase() === 'SO' || inputFormNumber.substring(0, 2).toUpperCase() === 'DL') && inputFormNumber.length != 12) {
      alert("เลขที่ใบสมัครที่ขึ้นต้นด้วย 'SO' หรือ 'DL' จะต้องตามด้วยตัวเลข 8 หลัก");
      valid = false;
    }
    return valid;
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

  handleClickSuggestItem = (formNumber) => {
    this.handleSubmitInputFormNumber(formNumber);
  }

  render() {
    const {traineeFormData, errorMessage, fields, errors, dialog} = this.state;

    let statusImage = '';
    let statusText = '';
    if (traineeFormData) {
      switch (traineeFormData.registerStatus) {
        case 'start':
          statusImage = 'step1.svg';
          statusText = 'รอการชำระเงิน';
          break;
        case 'wait-approve':
          statusImage = 'step2.svg';
          statusText = 'เจ้าหน้าที่กำลังตรวจสอบข้อมูลการชำระเงิน';
          break;
        case 'complete':
          statusImage = 'step4.svg';
          statusText = 'การลงทะเบียนสมบูรณ์';
          break;
        /*case 'cancel':
            statusImage = 'step1.svg';
            break;*/
      }
    }

    return (
      <MainLayout>
        <NextHead>
        </NextHead>

        <div className="container">
          <div className="row">
            <div className="col text-title-top">
              <h3>ตรวจสอบสถานะการลงทะเบียน</h3>
            </div>
          </div>
          <div className="row form-other mt-3">
            <div className="col-md-2">
              <label>เลขที่ใบสมัคร</label>
            </div>
            <div className="col-md-6">
              <TraineeFormAutoSuggest
                value={this.state.inputFormNumber}
                onChange={this.handleInputFormNumberChange}
                onClickSuggestItem={this.handleClickSuggestItem}
              />
            </div>
            <div className="col-md-4 nopad">
              <a href="javascript:void(0)"
                 className="btn-submit"
                 onClick={() => this.handleSubmitInputFormNumber(this.state.inputFormNumber)}
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
          <React.Fragment>
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
                      สถานะการลงทะเบียน
                    </div>
                    <div className="col-md-7 d-none d-sm-block d-md-block d-lg-block d-xl-block ">
                      <u>{statusText}</u>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            {/*<div className="row">
                            <div className="col">
                                <h4>สถานะการลงทะเบียน</h4>
                            </div>
                        </div>*/}
            <div className="row">
              <div className="col">
                <img src={`/static/images/${statusImage}`} className="stepimg"/>
              </div>
            </div>
          </React.Fragment>
          }
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
