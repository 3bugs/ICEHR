import Link from 'next/link';
import AutoSuggest from "react-autosuggest";
import {HOST_BACKEND, SERVICE_TRAINING, SERVICE_IN_HOUSE, SERVICE_SOCIAL, SERVICE_DRIVING_LICENSE, SERVICE_ACADEMIC_PAPER, SERVICE_HR_INTELLIGENCE} from '../etc/constants';

export default class Footer extends React.Component {
    constructor(props, context) {
        super(props, context);

        this.state = {};
    }

    componentDidMount() {
        $(".set > a").on("click", function () {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).siblings(".content-footer").slideUp(200);
                $(".set > a i").removeClass("fa-minus").addClass("fa-plus");
            } else {
                $(".set > a i").removeClass("fa-minus").addClass("fa-plus");
                $(this).find("i").removeClass("fa-plus").addClass("fa-minus");
                $(".set > a").removeClass("active");
                $(this).addClass("active");
                $(".content-footer").slideUp(200);
                $(this).siblings(".content-footer").slideDown(200);
            }
        });
    }

    handleClickPlusSign = e => {

    };

    render() {
        const {services, serviceList, contactList, socialList} = this.props;

        return (
            <React.Fragment>
                <div className="footerbg wow fadeInUp">
                    <div className="container">
                        <div className="row">
                            <div className="col-12 col-md-3 col-xl-3">
                                <div className="logo-footer">
                                    <img src="/static/images/logo_icess_LOGO%20ICT.svg" className="img-fluid"/>
                                </div>
                                <div className="social-footer">
                                    <h3>สถาบันเสริมศึกษาและทรัพยากรมนุษย์</h3>
                                    {socialList &&
                                        socialList.map((social, index) => {
                                            return (
                                                <li>
                                                    <a href={social.url}>
                                                        <i className={'fab fa-' + social.slug + (social.slug === 'facebook' ? '-f' : '')}/>
                                                    </a>
                                                </li>
                                            );
                                        })
                                    }
                                </div>
                                <div className="accordion-container">
                                    {/*{contactList &&
                                    contactList.map((contact, index) => {
                                        return (
                                            <div className="set"><a href="javascript:void(0)">
                                                ศูนย์{contact.title}
                                                <i className="fa fa-plus"/>
                                            </a>
                                                <div className="content-footer">
                                                    <div dangerouslySetInnerHTML={{__html: contact.sub_title}}/>
                                                    <p>99 หมู่ 18, ถ.พหลโยธิน, คลองหลวง, รังสิต, ปทุมธานี, 12121
                                                        <br/> Tel. +66 (0) 564 4440-79
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })
                                    }*/}
                                    <div className="set"><a href="javascript:void(0)">
                                        ศูนย์ท่าพระจันทร์
                                        <i className="fa fa-plus"/>
                                    </a>
                                        <div className="content-footer">
                                            {contactList &&
                                            <div dangerouslySetInnerHTML={{__html: contactList[0].sub_title}}></div>
                                            }
                                        </div>
                                    </div>
                                    <div className="set"><a href="javascript:void(0)">
                                        ศูนย์รังสิต
                                        <i className="fa fa-plus"/>
                                    </a>
                                        <div className="content-footer">
                                            {contactList &&
                                            <div dangerouslySetInnerHTML={{__html: contactList[1].sub_title}}></div>
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-12 col-md-9 col-xl-9 footer-crop">
                                <div className="row">
                                    <div className="col">
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">บริการ</h4>
                                            <ul>
                                                {serviceList
                                                && serviceList.map(service => {
                                                    return (
                                                        <li><a href={service.url} target="_parent">{service.title}</a></li>
                                                    );
                                                })
                                                }
                                                {/*<li><a href="/service-training" target="_parent">{services ? services[SERVICE_TRAINING].title : ''}</a></li>
                                                <li><a href="/in-house" target="_parent">{services ? services[SERVICE_IN_HOUSE].title : ''}</a></li>
                                                <li><a href="/service-social" target="_parent">{services ? services[SERVICE_SOCIAL].title : ''}</a></li>
                                                <li><a href="/service-driving-license" target="_parent">{services ? services[SERVICE_DRIVING_LICENSE].title : ''}</a></li>
                                                <li><a href="/academic-paper" target="_parent">{services ? services[SERVICE_ACADEMIC_PAPER].title : ''}</a></li>
                                                <li><a href={services ? services[SERVICE_HR_INTELLIGENCE].url : ''} target="_parent">{services ? services[SERVICE_HR_INTELLIGENCE].title : ''}</a></li>*/}
                                            </ul>
                                        </div>
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">แนะนำหน่วยงาน</h4>
                                            <ul>
                                                <li><a href="/about" target="_parent">เกี่ยวกับองค์กร</a></li>
                                                <li><a href="/mission" target="_parent">ภารกิจ </a></li>
                                                <li><a href="/organization" target="_parent"> โครงสร้างองค์กร </a></li>
                                                <li><a href="/personnel" target="_parent"> ผู้บริหารและบุคลากร</a></li>
                                            </ul>
                                        </div>
                                        <div className="footer-menugroup">
                                            <h4 className="title-footer">เอกสารดาวน์โหลด</h4>
                                            <ul>
                                                <li><a href="/document-download/training">เอกสารการอบรม </a></li>
                                                <li><a href="/document-download/report">รายงานผลการดำเนินงาน</a></li>
                                                <li><a href="/document-download/km">KM</a></li>
                                                <li><a href="/document-download/qa">ประกันคุณภาพ (QA)</a></li>
                                                <li><a href="/document-download/booklet"> จุลสาร/อินโฟกราฟิค</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-12 col-md-6">
                                        <div className="footer-link-list">
                                            <ul>
                                                <li><a href="javascript:void(0)">Site map</a></li>
                                                <li><a href="https://tu.ac.th/">Thammasat University</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="col-12 col-md-6 tu-copyr"> © Thammasat University</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    .footerbg {
                        background-color: #F1F1F3;
                        width: 100%;
                        margin-top: 20px;
                        padding-top: 40px;
                        padding-bottom: 40px;
                    }
                    
                    .footer-crop {
                        padding-left: 10%;
                    }
                    
                    .footer-crop:before {
                        background-color: #d4d4d4;
                    }
                    
                    .footer-crop:before {
                        content: "";
                        position: absolute;
                        width: 1px;
                        top: 0;
                        left: 0;
                        bottom: 0;
                    }
                    
                    .logo-footer {
                        margin-left: 20px;
                    }
                    
                    .footer-crop,
                    .logo-footer {
                        margin-top: 20px;
                    }
                    
                    .logo-footer > img {
                        width: 80%;
                    }
                    
                    .social-footer {
                        margin-left: 20px;
                    }
                    
                    .social-footer h3 {
                        font-family: 'DBHeavent-Med';
                        font-size: 1.1rem;
                        padding-top: 20px;
                    }
                    
                    .social-footer li {
                        list-style: none;
                        display: inline-block;
                        padding-right: 10px;
                    }
                    
                    .social-footer li a {
                        color: #1f1f1f;
                        font-size: 0.9rem;
                    }
                    
                    .social-footer li i:hover {
                        color: #b40303;
                        transition: all .3s ease-in-out;
                        -moz-transition: all .3s ease-in-out;
                        -webkit-transition: all .3s ease-in-out;
                    }
                    
                    .footer-menugroup {
                        display: inline-block;
                        float: left;
                        width: 33.333333%;
                        vertical-align: top;
                    }
                    
                    .footer-link-list ul,
                    .footer-menugroup ul {
                        margin-left: -38px;
                    }
                    
                    .footer-menugroup li {
                        list-style: none;
                        font-size: 1rem;
                    }
                    
                    .footer-menugroup li a {
                        color: #646469;
                    }
                    
                    .title-footer {
                        font-size: 1.1rem;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .footer-link-list li {
                        list-style: none;
                        display: inline-block;
                        padding-right: 10px;
                        font-size: 1rem;
                    }
                    
                    .footer-link-list ul li a {
                        color: #c00832;
                        text-decoration: underline;
                        -webkit-transition: all .3s ease;
                        transition: all .3s ease;
                    }
                    
                    .tu-copyr {
                        text-align: right;
                        font-size: 1rem;
                        color: #646469;
                    }
                    
                    .accordion-container {
                        position: relative;
                        height: auto;
                        margin: 10px auto;
                    }
                    
                    .accordion-container > h2 {
                        text-align: center;
                        color: #fff;
                        padding-bottom: 5px;
                        margin-bottom: 20px;
                        padding-bottom: 15px;
                        border-bottom: 1px solid #ddd;
                    }
                    
                    .set {
                        position: relative;
                        width: 100%;
                        height: auto;
                    }
                    
                    .set > a {
                        display: block;
                        padding: 10px 15px;
                        text-decoration: none;
                        color: black;
                        border-bottom: 1px solid #ddd;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        font-family: 'DBHeavent-Med';
                    }
                    
                    .set > a i {
                        float: right;
                        color: #a7a6a6;
                        margin-top: 10px;
                        font-size: 0.6rem;
                    }
                    
                    .content-footer {
                        border-bottom: 1px solid #ddd;
                        display: none;
                    }
                    
                    .content-footer div {
                        padding: 10px 15px;
                        margin: 0;
                        color: #646469;
                    }
                    
                    @media (max-width: 1199px) {
                        .footer-crop {
                            padding-left: 4%;
                        }
                    }
                    
                    @media (max-width: 991px) {
                        .footer-menugroup {
                            width: 50%;
                        }
                        .footer-crop {
                            padding-left: 3%;
                        }
                        .logo-footer > img {
                            width: 40%;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .social-footer {
                            margin-left: 10px;
                        }
                    }
                    
                    @media (max-width: 767px) {
                        .logo-footer {
                            margin-left: 0px;
                        }
                        .footer-menugroup {
                            width: 100%;
                        }
                        .tu-copyr {
                            text-align: left;
                            float: left;
                        }
                        .social-footer {
                            margin-left: 10px;
                        }
                        .footer-crop {
                            padding-left: 10%;
                        }
                        .footer-crop:before {
                            display: none;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .logo-footer > img {
                            width: 70%;
                        }
                    }
                    
                    @media (max-width: 575px) {
                        .footer-crop {
                            padding-left: 9%;
                        }
                        .footer-crop:before {
                            display: none;
                        }
                        .container {
                            max-width: 100%;
                        }
                        .logo-footer > img {
                            width: 70%;
                        }
                    }
                `}</style>
            </React.Fragment>
        );
    }
}