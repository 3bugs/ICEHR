import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import {HOST_BACKEND} from "../etc/constants";
import fetch from "isomorphic-unfetch";

export default class Organization extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
        };
    }

    static getInitialProps = async function ({req, query}) {
        let organizationList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const result = await fetch(baseUrl + '/api/get_intro', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'organization',
            }),
        });
        const resultJson = await result.json();
        if (resultJson['error']['code'] === 0) {
            organizationList = resultJson['dataList'];
            errorMessage = null;
        } else {
            organizationList = null;
            errorMessage = resultJson['error']['message'];
        }

        return {organizationList, errorMessage};
    };

    componentDidMount() {
    }

    render() {
        const {organizationList} = this.props;
        let organization = null;
        if (organizationList.length > 0) {
            organization = organizationList[0];
        }

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-structure-top"/>
                    <div className="bgnav-abt">
                        <div className="container d-none d-sm-block d-md-block d-lg-block d-xl-block">
                            <div className="row">
                                <div className="col">
                                    <ul className="aboutmenu">
                                        <li><a href="/about">เกี่ยวกับองค์กร </a></li>
                                        <li><a href="/mission">ภารกิจ </a></li>
                                        <li><a href="/organization" className="active-menu">โครงสร้างองค์กร </a></li>
                                        <li><a href="/personnel">ผู้บริหารและบุคลากร </a></li>
                                        <li><a href="/committee">คณะกรรมการ </a></li>
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
                                            <li className="menu-item">
                                                <a href="/committee"> คณะกรรมการ </a>
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
                                <h3>โครงสร้างองค์กร</h3>
                            </div>
                        </div>
                        <div className="row">
                            <div className="col text-center">
                                {!organization &&
                                    <div style={{textAlign: 'center', color: 'red'}}>ไม่มีข้อมูล</div>
                                }
                                {organization &&
                                <a href={`${HOST_BACKEND}/uploads/intro_assets/${organization.image_file_name}`} className="single_image">
                                    <img src={`${HOST_BACKEND}/uploads/intro_assets/${organization.image_file_name}`} className="img-fluid"/>
                                </a>
                                }
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