import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import ErrorLabel from '../components/ErrorLabel';
import Dialog from "../components/Dialog";
import {isString} from "../etc/utils";

const RESULT_ERROR = 'resultError';

const NEW_PASSWORD = 'newPassword';
const CONFIRM_NEW_PASSWORD = 'confirmNewPassword';

export default class ResetPassword extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            errorMessage: null,
            successMessage: null,
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

    static getInitialProps = async ({req, query}) => {
        //const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const {token} = query;
        return {token};
    };

    handleChange(field, allowSpace, e) {
        let {fields} = this.state;

        if (!allowSpace) {
            fields[field] = isString(e.target.value) ? e.target.value.trim() : e.target.value;
        } else {
            fields[field] = e.target.value;
        }

        this.setState({fields});
    }

    handleSubmit = event => {
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

    render() {
        const {token} = this.props;
        const {successMessage, errorMessage, fields, errors, dialog} = this.state;

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <h3>ตั้งรหัสผ่านใหม่</h3>
                        </div>
                    </div>
                    {successMessage &&
                    <div style={{textAlign: 'center', color: 'green'}}>{successMessage}</div>
                    }

                    {!token && !successMessage &&
                    <div style={{textAlign: 'center', color: 'red'}}>Error: ไม่ได้ระบุ Token !!!</div>
                    }

                    {token && !successMessage &&
                    <div className="row form-other mt-3">
                        <div className="col-md-4 offset-md-4">
                            <form id="loginForm" method="post" onSubmit={this.handleSubmit} noValidate={true}>
                                <label style={{marginTop: '0', marginBottom: '3px', marginLeft: '3px', float: 'left'}}>รหัสผ่านใหม่
                                    : </label>
                                <input value={this.state.fields[NEW_PASSWORD] || ''}
                                       onChange={this.handleChange.bind(this, NEW_PASSWORD, false)}
                                       placeholder="กรอกรหัสผ่านใหม่ที่ต้องการ"
                                       type="password" className="form-control input-md"
                                       style={{marginBottom: 0}}/>
                                <ErrorLabel value={this.state.errors[NEW_PASSWORD]}/>

                                <label style={{marginTop: '15px', marginBottom: '3px', marginLeft: '3px', float: 'left'}}>ยืนยันรหัสผ่านใหม่
                                    : </label>
                                <input value={this.state.fields[CONFIRM_NEW_PASSWORD] || ''}
                                       onChange={this.handleChange.bind(this, CONFIRM_NEW_PASSWORD, false)}
                                       placeholder="กรอกรหัสผ่านใหม่อีกครั้งเพื่อยืนยัน"
                                       type="password" className="form-control input-md"
                                       style={{marginBottom: 0}}/>
                                <ErrorLabel value={this.state.errors[CONFIRM_NEW_PASSWORD]}/>

                                <ErrorLabel value={this.state.errors[RESULT_ERROR]}
                                            textAlign={'center'}
                                            marginTop={'25px'}/>

                                <button type="submit" value="submit" className="btn btn-ss"
                                        style={{
                                            width: '100%',
                                            marginTop: '25px',
                                            marginBottom: '10px'
                                        }}>
                                    ตั้งรหัสผ่านใหม่
                                </button>
                            </form>
                        </div>
                    </div>
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