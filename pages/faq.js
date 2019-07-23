import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import './faq.css';

export default class About extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {
        $('.collapse.in').prev('.panel-heading').addClass('active');
        $('#accordion, #bs-collapse').on('show.bs.collapse', function (a) {
            $(a.target).prev('.panel-heading').addClass('active');
        }).on('hide.bs.collapse', function (a) {
            $(a.target).prev('.panel-heading').removeClass('active');
        });
    }

    HideContent(d) {
        document.getElementById(d).style.display = "none";
    }

    ShowContent(d) {
        document.getElementById(d).style.display = "block";
    }

    ReverseDisplay(d) {
        if (document.getElementById(d).style.display == "none") {
            document.getElementById(d).style.display = "block";
        }
        else {
            document.getElementById(d).style.display = "none";
        }
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-faq-top"/>
                    <div className="container mt-4">
                        <div className="row">
                            <div className="col">
                                <h4 className="text-black">คำถามที่พบบ่อย</h4></div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <div id="accordion">
                                    <div className="card">
                                        <div className="card-header" id="headingOne">
                                            <h5 className="mb-0">
                                                <button className="btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><i className="fa"
                                                                                                                                                                                   aria-hidden="true"></i>
                                                    <a><span className="ask">Q</span>การขอใช้อีเมล์ของมหาวิทยาลัย</a>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" className="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div className="card-body"> นักศึกษา
                                                <br/> - มหาวิทยาลัยได้มีการกำหนดบัญชื่อผู้ใช้สำหรับทุกท่านตั้งแต่เริ่มเข้าบรรจุเป็นนักศึกษา -
                                                หากมีปัญหากรุณาติดต่อกับฝ่ายทะเบียนประวัติสำนักงานทะเบียนนักศึกษา มธ. โทร 02-564- 4441-79 ต่อ 1602-1607 เพื่อตรวจสอบสถานะและบญัชีผู้ใช้ก่อน
                                                <br/>
                                                <br/> บุคลากร
                                                <br/> - สำหรับบุคคลากรที่เข้าบรรจุใหม่จะไดร้ับบญัชีผู้ใช้โดยอัตโนมติจากหน่วยงานกองการเจ้าหน้าที่ -
                                                กรุณาติดต่อกับหน่วยงานทะเบียนประวัติกองทรัพยากรมนุษย์โทร. 02-5644440-79 ต่อ1886,1887
                                                <br/>
                                                <br/> กรณีได้รับ User E-mail แล้วทำหายหรือได้ทำการเปลี่ยนรหัสผ่านแล้วลืม ให้ติดต่อที่สำนักงานศูนย์เทคโนโลยีฯ โทร. 1965 หรือ helpdesk
                                                หรือ line หรือที่ห้องบริการคอมพิวเตอร์ 113 (สทศ.) อาคารวิทยบริการ มธ. (ศูนย์รังสิต) โทร. 1182-1184
                                            </div>
                                        </div>
                                    </div>
                                    <div className="card">
                                        <div className="card-header" id="headingTwo">
                                            <h5 className="mb-0">
                                                <button className="btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"><i className="fa"
                                                                                                                                                                                   aria-hidden="true"></i>
                                                    <a><span className="ask">Q</span>การขอใช้อีเมล์ของมหาวิทยาลัย</a>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" className="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div className="card-body"> นักศึกษา
                                                <br/> - มหาวิทยาลัยได้มีการกำหนดบัญชื่อผู้ใช้สำหรับทุกท่านตั้งแต่เริ่มเข้าบรรจุเป็นนักศึกษา -
                                                หากมีปัญหากรุณาติดต่อกับฝ่ายทะเบียนประวัติสำนักงานทะเบียนนักศึกษา มธ. โทร 02-564- 4441-79 ต่อ 1602-1607 เพื่อตรวจสอบสถานะและบญัชีผู้ใช้ก่อน
                                                <br/>
                                                <br/> บุคลากร
                                                <br/> - สำหรับบุคคลากรที่เข้าบรรจุใหม่จะไดร้ับบญัชีผู้ใช้โดยอัตโนมติจากหน่วยงานกองการเจ้าหน้าที่ -
                                                กรุณาติดต่อกับหน่วยงานทะเบียนประวัติกองทรัพยากรมนุษย์โทร. 02-5644440-79 ต่อ1886,1887
                                                <br/>
                                                <br/> กรณีได้รับ User E-mail แล้วทำหายหรือได้ทำการเปลี่ยนรหัสผ่านแล้วลืม ให้ติดต่อที่สำนักงานศูนย์เทคโนโลยีฯ โทร. 1965 หรือ helpdesk
                                                หรือ line หรือที่ห้องบริการคอมพิวเตอร์ 113 (สทศ.) อาคารวิทยบริการ มธ. (ศูนย์รังสิต) โทร. 1182-1184
                                            </div>
                                        </div>
                                    </div>
                                    <div className="card">
                                        <div className="card-header" id="headingThree">
                                            <h5 className="mb-0">
                                                <button className="btn-link" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree"><i className="fa"
                                                                                                                                                                                       aria-hidden="true"></i>
                                                    <a><span className="ask">Q</span>การขอใช้อีเมล์ของมหาวิทยาลัย</a>
                                                </button>
                                            </h5>
                                        </div>
                                        <div id="collapseThree" className="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                            <div className="card-body"> นักศึกษา
                                                <br/> - มหาวิทยาลัยได้มีการกำหนดบัญชื่อผู้ใช้สำหรับทุกท่านตั้งแต่เริ่มเข้าบรรจุเป็นนักศึกษา -
                                                หากมีปัญหากรุณาติดต่อกับฝ่ายทะเบียนประวัติสำนักงานทะเบียนนักศึกษา มธ. โทร 02-564- 4441-79 ต่อ 1602-1607 เพื่อตรวจสอบสถานะและบญัชีผู้ใช้ก่อน
                                                <br/>
                                                <br/> บุคลากร
                                                <br/> - สำหรับบุคคลากรที่เข้าบรรจุใหม่จะไดร้ับบญัชีผู้ใช้โดยอัตโนมติจากหน่วยงานกองการเจ้าหน้าที่ -
                                                กรุณาติดต่อกับหน่วยงานทะเบียนประวัติกองทรัพยากรมนุษย์โทร. 02-5644440-79 ต่อ1886,1887
                                                <br/>
                                                <br/> กรณีได้รับ User E-mail แล้วทำหายหรือได้ทำการเปลี่ยนรหัสผ่านแล้วลืม ให้ติดต่อที่สำนักงานศูนย์เทคโนโลยีฯ โทร. 1965 หรือ helpdesk
                                                หรือ line หรือที่ห้องบริการคอมพิวเตอร์ 113 (สทศ.) อาคารวิทยบริการ มธ. (ศูนย์รังสิต) โทร. 1182-1184
                                            </div>
                                        </div>
                                    </div>
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