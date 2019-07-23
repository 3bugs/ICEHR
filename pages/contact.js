import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import './contact.css';

export default class About extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {
        $(".single_image").fancybox();
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                    <script src="/static/fancybox/source/jquery.fancybox.js?v=2.1.5"/>
                    <link rel="stylesheet" href="/static/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen"/>
                </NextHead>

                <React.Fragment>
                    <div className="banner-contact-top"/>
                    <div className="container mt-4">
                        <div className="row">
                            <div className="col-12 col-md-6">
                                <div className="title-branch"><span className="pin"><img src="/static/images/pin-contact.svg" className="img-fluid"/></span> ท่าพระจันทร์</div>
                                <div className="branch-detail">
                                    <h4>สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์ (ท่าพระจันทร์)</h4>
                                    อาคารอเนกประสงค์1 ชั้น 5 และชั้น 8 เลขที่ 2 ถนนพระจันทร์
                                    <br/> แขวงพระบรมมหาราชวัง เขตพระนคร กรุงเทพมหานคร 1020
                                    <h4> เบอร์ติดต่อ สำนักงานชั้น 5 / สมัครอบรม</h4>
                                    <img src="/static/images/tel-icon.svg"/> โทรศัพท์ : 02-6133820 , 3822 - 3825 <br/>
                                    <img src="/static/images/fax-icon.svg"/> โทรสาร : 02-2257517, 02-2264395
                                    <h4> เบอร์ติดต่อ สำนักงานชั้น 8 </h4>
                                    <img src="/static/images/tel-icon.svg"/> โทรศัพท์ 02 - 6133302-6 <br/>
                                    <img src="/static/images/fax-icon.svg"/> โทรสาร 02-2265324
                                </div>
                                <div className="map">
                                    <a href="/static/images/prachan_thai_s.jpg" className="single_image"><img src="/static/images/map-thaprachan.png" className="img-fluid"/></a>
                                    <br/><br/>
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.355324860988!2d100.4877940143117!3d13.75743310087075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e2990c9fa08423%3A0x27bfab08c0bfe007!2z4Lih4Lir4Liy4Lin4Li04LiX4Lii4Liy4Lil4Lix4Lii4LiY4Lij4Lij4Lih4Lio4Liy4Liq4LiV4Lij4LmMIOC4l-C5iOC4suC4nuC4o-C4sOC4iOC4seC4meC4l-C4o-C5jA!5e0!3m2!1sth!2sth!4v1551195242789"
                                        width="400" height="300" frameBorder="0" style={{border: 0}} allowFullScreen/>
                                </div>
                            </div>
                            <div className="col-12 col-md-6">
                                <div className="title-branch"><span className="pin"><img src="/static/images/pin-contact.svg" className="img-fluid"/></span> รังสิต</div>
                                <div className="branch-detail">
                                    <h4>สถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์ (ศูนย์รังสิต)
                                    </h4>
                                    ห้อง 207 ชั้น 2 ตึกโดมบริหาร เลขที่ 99 หมู่ที่ 18 ตำบลคลองหนึ่ง <br/>
                                    อำเภอคลองหลวง จังหวัดปทุมธานี 12121
                                    <h4> เบอร์ติดต่อ</h4>
                                    <img src="/static/images/tel-icon.svg"/> โทรศัพท์ : 02-5644440 ต่อ 1667 - 8 <br/>
                                    <img src="/static/images/fax-icon.svg"/> โทรสาร : 02-5642972
                                </div>
                                <div className="map mt-top">z
                                    <a href="/static/images/rangsit_thai_s.jpg" className="single_image"><img src="/static/images/map2-rangsit.png" className="img-fluid"/></a>
                                    <br/><br/>
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3870.131907454392!2d100.6011518143148!3d14.069387093646421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e27fec036d9a8b%3A0x77cb5576ae292ac9!2z4Lih4Lir4Liy4Lil4Lix4Lii4LiY4Lij4Lij4Lih4Lio4Liy4Liq4LiV4Lij4LmMIOC4qOC4ueC4meC4ouC5jOC4o-C4seC4h-C4quC4tOC4lQ!5e0!3m2!1sth!2sth!4v1551195179357"
                                        width="400" height="300" frameBorder="0" style={{border: 0}} allowFullScreen/>
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