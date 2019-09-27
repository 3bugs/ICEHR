import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import './contact.css';
import fetch from "isomorphic-unfetch";
import {HOST_BACKEND} from '../etc/constants';

export default class Contact extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async ({req, query}) => {
        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';

        const res = await fetch(baseUrl + '/api/get_intro', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'contact',
            }),
        });

        const result = await res.json();
        if (result.error.code === 0) {
            const contactList = result.dataList;

            return {
                contactList,
            };
        } else {
            return {
                contactList: null,
            };
        }
    };

    componentDidMount() {
        $(".single_image").fancybox();
    }

    render() {
        const {contactList} = this.props;

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
                            {contactList &&
                            contactList.map((contact, index) => {
                                let details = contact.details.replace(/\(tel\)/gi, '<img width="25px" src="/static/images/tel-icon.svg"/>');
                                details = details.replace(/\(fax\)/gi, '<img width="25px" src="/static/images/fax-icon.svg"/>');

                                return (
                                    <div className="col-12 col-md-6">
                                        <div className="title-branch">
                                        <span className="pin">
                                            <img src="/static/images/pin-contact.svg" className="img-fluid"/>
                                        </span> {index === 0 ? 'ท่าพระจันทร์' : 'รังสิต'}
                                        </div>
                                        <div className="branch-detail" dangerouslySetInnerHTML={{__html: details}}/>
                                    </div>
                                );
                            })
                            }

                            {/*<div className="col-12 col-md-6">
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
                                    <a href="/static/images/rangsit_thai_s.jpg" className="single_image">
                                        <img src="/static/images/map2-rangsit.png" className="img-fluid"/>
                                    </a>
                                    <br/><br/>
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3870.131907454392!2d100.6011518143148!3d14.069387093646421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e27fec036d9a8b%3A0x77cb5576ae292ac9!2z4Lih4Lir4Liy4Lil4Lix4Lii4LiY4Lij4Lij4Lih4Lio4Liy4Liq4LiV4Lij4LmMIOC4qOC4ueC4meC4ouC5jOC4o-C4seC4h-C4quC4tOC4lQ!5e0!3m2!1sth!2sth!4v1551195179357"
                                        width="400" height="300" frameBorder="0" style={{border: 0}} allowFullScreen/>
                                </div>
                            </div>*/}
                        </div>
                        <div className="row">
                            {contactList &&
                            contactList.map((contact, index) => {
                                return (
                                    <div className="col-12 col-md-6">
                                        <div className="map">
                                            <a href={`${HOST_BACKEND}/uploads/intro_assets/${contact.image_file_name}`} className="single_image">
                                                <img src={`${HOST_BACKEND}/uploads/intro_assets/${contact.image_file_name}`}
                                                     className="img-fluid"
                                                     style={{width: '400px'}}/>
                                            </a>
                                            <br/><br/>
                                            <iframe
                                                src={`${contact.url}`}
                                                width="400px" height="300px" frameBorder="0"
                                                style={{border: 0}} allowFullScreen/>
                                        </div>
                                    </div>
                                );
                            })
                            }
                        </div>
                    </div>
                </React.Fragment>

                <style jsx>{`
                `}</style>
            </MainLayout>
        );
    }
}