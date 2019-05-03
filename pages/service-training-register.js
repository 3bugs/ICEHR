import NextHead from "next/head";
import MainLayout from "../layouts/MainLayout";

export default class ServiceTrainingRegister extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <div className="container">
                    <div className="row">
                        <div className="col text-title-top">
                            <p>โครงการบริการวิชาการ สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์</p>
                            <h3>แบบฟอร์มลงทะเบียน</h3></div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <div className="border-project">
                                <div className="row">
                                    <div className="col-12">
                                        <div className="row">
                                            <div className="col-sm-3 text-black ">ชื่อหลักสูตร / รุ่นที่</div>
                                            <div className="col-sm-9 text-gray">{}</div>
                                        </div>
                                        <div className="row">
                                            <div className="col-sm-3 text-black ">วันที่อบรม</div>
                                            <div className="col-sm-9 text-gray">{}</div>
                                        </div>
                                        <div className="row">
                                            <div className="col-sm-3 text-black ">สถานที่อบรม</div>
                                            <div className="col-sm-9 text-gray">{}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="applicationFormGroup" method="post">
                        <div className="row mt-4" style={{border: '0px solid red'}}>
                            <div className="col">
                                <h4 className="text-black" style={{marginTop: '20px'}}><img src="/static/images/title-detail-icon.svg"/>
                                    รายละเอียดผู้สมัครอบรม</h4>
                            </div>
                        </div>

                        <div className="container_reg" style={{border: '0px solid red', position: 'relative', top: '-10px'}}>
                            <div className="row field_reg">
                                <div className="col">
                                    <div className="border-inside">
                                        <a href="#">
                                            <div className="deletenumber">x</div>
                                        </a>
                                        <div className="row">
                                            <div className="col-12 col-md-1 d-block d-sm-block d-md-block d-lg-block d-xl-block">
                                                <div className="list-add">ลำดับที่ <span id="sequence">1</span></div>
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
                                                                            <select id="title_g" name="title"
                                                                                    className="form-control"
                                                                                    style={{marginBottom: 0}}>
                                                                                <option value="" disabled selected>
                                                                                    เลือกคำนำหน้า
                                                                                </option>
                                                                                <option value="1">นาย</option>
                                                                                <option value="2">นาง</option>
                                                                                <option value="3">นางสาว</option>
                                                                            </select>
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
                                                                            <input id="firstName_g" name="firstName"
                                                                                   type="text"
                                                                                   placeholder="กรอกชื่อ"
                                                                                   className="form-control input-md"/></div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-6">
                                                                    <div className="row">
                                                                        <div className="col-md-4">
                                                                            <label className="label required-label">นามสกุล</label>
                                                                        </div>
                                                                        <div className="col-md-8">
                                                                            <input id="lastName_g" name="lastName"
                                                                                   type="text"
                                                                                   placeholder="กรอกนามสกุล"
                                                                                   className="form-control input-md"/></div>
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
                                                                            <input id="age_g" name="age"
                                                                                   type="number"
                                                                                   placeholder="กรอกอายุ"
                                                                                   className="form-control input-md"/></div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-6">
                                                                    <div className="row">
                                                                        <div className="col-md-4">
                                                                            <label className="label">ตำแหน่งงาน</label>
                                                                        </div>
                                                                        <div className="col-md-8">
                                                                            <input id="jobPosition_g"
                                                                                   name="jobPosition"
                                                                                   type="text"
                                                                                   placeholder="กรอกตำแหน่งงาน"
                                                                                   className="form-control input-md"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div className="row">
                                                                <div className="col-md-6">
                                                                    <div className="row">
                                                                        <div className="col-md-4">
                                                                            <label className="label">ชื่อหน่วยงาน</label>
                                                                        </div>
                                                                        <div className="col-md-8">
                                                                            <input id="organizationName_g"
                                                                                   name="organizationName"
                                                                                   type="text"
                                                                                   placeholder="กรอกชื่อหน่วยงาน"
                                                                                   className="form-control input-md"/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-6">
                                                                    <div className="row">
                                                                        <div className="col-md-4">
                                                                            <label className="label">ประเภทหน่วยงาน</label>
                                                                        </div>
                                                                        <div className="col-md-8">
                                                                            <select id="organizationType_g"
                                                                                    name="organizationType"
                                                                                    className="form-control">
                                                                                <option value="0" disabled selected>เลือกประเภทหน่วยงาน</option>
                                                                                <option value="1">ราชการ</option>
                                                                                <option value="2">รัฐวิสาหกิจ</option>
                                                                                <option value="3">บริษัทเอกชน</option>
                                                                            </select>
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
                                                                            <input id="phone_g" name="phone"
                                                                                   type="tel"
                                                                                   placeholder="กรอกเบอร์โทรศัพท์"
                                                                                   className="form-control input-md"/></div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-6">
                                                                    <div className="row">
                                                                        <div className="col-md-4">
                                                                            <label className="label required-label">อีเมล</label>
                                                                        </div>
                                                                        <div className="col-md-8">
                                                                            <input id="email_g" name="email"
                                                                                   type="email"
                                                                                   placeholder="กรอกอีเมล"
                                                                                   className="form-control input-md"/></div>
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

                        <div style={{border: '0px solid blue', marginTop: '15px', textAlign: 'center'}}>
                            <a href="javascript:void(0);" className="addmoreuser"><i className="fas fa-plus-circle"></i> เพิ่มผู้สมัครอบรม</a>
                        </div>

                        <div className="row mt-5" style={{border: '0px solid red'}}>
                            <div className="col">
                                <h4 className="text-black"><i className="fas fa-user-friends"></i> ผู้ประสานงาน</h4>
                            </div>
                        </div>
                        <div className="border-inside" style={{marginTop: '15px'}}>
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
                                                        <select id="title_p" name="title"
                                                                className="form-control"
                                                                style={{marginBottom: 0}}>
                                                            <option value="" disabled selected>เลือกคำนำหน้า</option>
                                                            <option value="1">นาย</option>
                                                            <option value="2">นาง</option>
                                                            <option value="3">นางสาว</option>
                                                        </select>
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
                                                        <input id="firstName_p" name="firstName"
                                                               type="text"
                                                               placeholder="กรอกชื่อ"
                                                               className="form-control input-md"/></div>
                                                </div>
                                            </div>
                                            <div className="col-md-6">
                                                <div className="row">
                                                    <div className="col-md-4">
                                                        <label className="label required-label">นามสกุล</label>
                                                    </div>
                                                    <div className="col-md-8">
                                                        <input id="lastName_p" name="lastName"
                                                               type="text"
                                                               placeholder="กรอกนามสกุล"
                                                               className="form-control input-md"/></div>
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
                                                        <input id="age_p" name="age"
                                                               type="number"
                                                               placeholder="กรอกอายุ"
                                                               className="form-control input-md"/></div>
                                                </div>
                                            </div>
                                            <div className="col-md-6">
                                                <div className="row">
                                                    <div className="col-md-4">
                                                        <label className="label">ตำแหน่งงาน</label>
                                                    </div>
                                                    <div className="col-md-8">
                                                        <input id="jobPosition_p"
                                                               name="jobPosition"
                                                               type="text"
                                                               placeholder="กรอกตำแหน่งงาน"
                                                               className="form-control input-md"/>
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
                                                        <input id="organizationName_p"
                                                               name="organizationName"
                                                               type="text"
                                                               placeholder="กรอกชื่อหน่วยงาน"
                                                               className="form-control input-md"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="col-md-6">
                                                <div className="row">
                                                    <div className="col-md-4">
                                                        <label className="label required-label">ประเภทหน่วยงาน</label>
                                                    </div>
                                                    <div className="col-md-8">
                                                        <select id="organizationType_p"
                                                                name="organizationType"
                                                                className="form-control">
                                                            <option value="0" disabled>เลือกประเภทหน่วยงาน</option>
                                                            <option value="1">ราชการ</option>
                                                            <option value="2">รัฐวิสาหกิจ</option>
                                                            <option value="3">บริษัทเอกชน</option>
                                                        </select>
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
                                                        <input id="phone_p" name="phone"
                                                               type="tel"
                                                               placeholder="กรอกเบอร์โทรศัพท์"
                                                               className="form-control input-md"/></div>
                                                </div>
                                            </div>
                                            <div className="col-md-6">
                                                <div className="row">
                                                    <div className="col-md-4">
                                                        <label className="label required-label">อีเมล</label>
                                                    </div>
                                                    <div className="col-md-8">
                                                        <input id="email_p" name="email"
                                                               type="email"
                                                               placeholder="กรอกอีเมล"
                                                               className="form-control input-md"/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div></div>
                                </div>
                            </div>

                        </div>
                        <div className="row mt-5" style={{border: '0 solid red'}}>
                            <div className="col">
                                <h4 className="text-black">ข้อมูลการออกใบเสร็จ</h4>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <div className="border-inside" style={{marginTop: '15px'}}>
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
                                                                <input id="organizationAddress" name="organizationAddress"
                                                                       type="text"
                                                                       placeholder="บ้านเลขที่ / อาคาร / หมู่ / ซอย / ถนน"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="w-100"></div>
                                                            <div className="col-12 col-md-6">
                                                                <input id="organizationSubDistrict" name="organizationSubDistrict"
                                                                       type="text"
                                                                       placeholder="แขวง / ตำบล"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="col-12 col-md-6 nopadleft">
                                                                <input id="organizationDistrict" name="organizationDistrict"
                                                                       type="text"
                                                                       placeholder="เขต / อำเภอ"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="w-100"></div>
                                                            <div className="col-12 col-md-6">
                                                                <input id="organizationProvince" name="organizationProvince"
                                                                       type="text" placeholder="จังหวัด"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="col-12 col-md-6 nopadleft">
                                                                <input id="organizationPostalCode" name="organizationPostalCode"
                                                                       type="number"
                                                                       placeholder="รหัสไปรษณีย์"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="w-100"></div>
                                                            <div className="col-12 col-md-6">
                                                                <input id="organizationPhone" name="organizationPhone"
                                                                       type="text"
                                                                       placeholder="เบอร์โทรศัพท์หน่วยงาน"
                                                                       className="form-control input-md"/>
                                                            </div>
                                                            <div className="col-12 col-md-6 nopadleft">
                                                                <input id="organizationTaxId" name="organizationTaxId"
                                                                       type="text"
                                                                       placeholder="เลขประจำตัวผู้เสียภาษี"
                                                                       className="form-control input-md"/>
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
                        <div className="row mt-5 mb-5">
                            <div className="col">
                                <div className="btn-red-submit">
                                    <button type="submit" value="submit" className="btn btn-danger">
                                        ลงทะเบียน
                                    </button>
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

                <style jsx>{`

                `}</style>
            </MainLayout>
        );
    }
}