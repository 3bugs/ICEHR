import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import './mission.css';

export default class About extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
        };
    }

    componentDidMount() {
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-mission-top"/>
                    <div className="bgnav-abt">
                        <div className="container d-none d-sm-block d-md-block d-lg-block d-xl-block">
                            <div className="row">
                                <div className="col">
                                    <ul className="aboutmenu">
                                        <li><a href="/about">เกี่ยวกับองค์กร </a></li>
                                        <li><a href="/mission" className="active-menu">ภารกิจ </a></li>
                                        <li><a href="/organization">โครงสร้างองค์กร </a></li>
                                        <li><a href="/personnel">ผู้บริหารและบุคลากร </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="container  d-block d-sm-none d-md-none d-lg-none d-xl-none">
                            <div className="row">
                                <div className="col">
                                    <div className="menu-wrapper">
                                        <ul className="menu">
                                            <li className="menu-item active">
                                                <a href="/about">เกี่ยวกับองค์กร </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/mission"> ภารกิจ </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/organization"> โครงสร้างองค์กร </a>
                                            </li>
                                            <li className="menu-item">
                                                <a href="/personnel"> ผู้บริหารและบุคลากร </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="container mt-5">
                        <div className="row">
                            <div className="col text-title-top">
                                <h3>ภารกิจ</h3></div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-md-6"><img src="/static/images/mission1.jpg" className="img-fluid"/></div>
                            <div className="col-md-6">
                                <div className="mission-detail">
                                    <h4>วิจัยและวิชาการ</h4> มีอำนาจหน้าที่ดำเนินการจัดทำข้อเสนอโครงการวิจัยเพื่อของบประมาณจากแหล่งทุนต่างๆ ดำเนินโครงการวิจัยที่ได้รับอนุมัติงบประมาณ หรือได้รับมอบหมาย
                                    จัดสรรทุนวิจัยจากกองทุนวิจัยของหน่วยงาน การทำสัญญารับทุนวิจัย การติดตามงานวิจัยที่ได้รับทุน การสังเคราะห์องค์ความรู้จากงานวิจัยหรือวิชาการ
                                    การสัมมนาเผยแพร่ผลงานวิจัยหรือวิชาการ การเห็บรวบรวมผลงานวิจัย การผลิตสื่อสิ่งพิมพ์ทางวิชาการ โครงการวารสาร HR intelligence และปฎิบัติหน้าที่อื่นตามที่ได้รับมอบหมาย
                                </div>
                            </div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-md-6"><img src="/static/images/mission2.jpg" className="img-fluid"/></div>
                            <div className="col-md-6">
                                <div className="mission-detail">
                                    <h4>พัฒนาคนและองค์กร</h4> มีอำนาจหน้าที่ดำเนินการจัดทำโครงการ การวิเคราะห์หาความต้องการอบรม (Training Need) และการพัฒนาหลักสูตรต่างๆ
                                    ด้านการบริหารและพัฒนาทรัพยากรมนุษย์ ทั้งหลักสูตรระยะสั้นและระยะยาว การจัดโครงการพัฒนาบุคลากรให้แก่มหาวิทยาลัยธรรมศาสตร์
                                    รวมทั้งจัดโครงการอบรมให้แก่หน่วยงานภายนอกทั้งภาครัฐและเอกชน (In-house Training)
                                    จัดโครงการฝึกอบรมด้านการบริหารและการพัฒนาทรัพยากรมนุษย์ให้แก่บุคคลทั่วไปทั้งใน2งานวิเคราะห์และติดตามประเมินผลในลักษณะของโครงการเลี้ยงตัวเอง
                                    และปฏิบัติหน้าที่อื่นๆตามที่ได้รับมอบหมาย
                                </div>
                            </div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-md-6"><img src="/static/images/mission3.jpg" className="img-fluid"/></div>
                            <div className="col-md-6">
                                <div className="mission-detail">
                                    <h4>บริการสังคมและชุมชน</h4> มีอำนาจหน้าที่ให้บริการความรู้แก่ประชาชนทุกระดับโดยไม่เลือกเพศและวัยเป็นการให้บริการแบบไม่หวังผลกำไร ในลักษณะโครงการฝึกอบรม สัมมนา
                                    การบรรยายพิเศษ หรือการอภิปราย โดยจัดเองและร่วมมือกับหน่วยงานภายในหรือหน่วยงานภายนอกมหาวิทยาลัย ทั้งภาครัฐและเอกชน หรือเครือข่ายต่างๆ ที่มีข้อตกลงร่วมกับสถาบัน
                                    การให้บริการความรู้ทางวิชาการผ่านสื่อต่างๆ รวมทั้งการติดตามผลการนำไปใช้ประโยชน์ ในการพัฒนาคุณภาพชีวิตของผู้รับบริการ และปฎิบัติหน้าที่อื่นๆตามที่ได้รับมอบหมาย
                                </div>
                            </div>
                        </div>
                        <div className="row mt-3">
                            <div className="col-md-6"><img src="/static/images/mission4.jpg" className="img-fluid"/></div>
                            <div className="col-md-6">
                                <div className="mission-detail">
                                    <h4>การบริหารจัดการ</h4> ดำเนินการด้านงานธุรการและบุคคล ด้านแผนและงบประมาณ ด้านการเงิน บัญชี และพัสดุ ด้านสารสนเทศและการประชาสัมพันธ์
                                    และปฎิบัติหน้าที่อื่นๆตามที่ได้รับมอบหมาย
                                </div>
                            </div>
                        </div>
                    </div>
                </React.Fragment>
                
                <style jsx>{`
                `}</style>
            </MainLayout>
        );
    }
}