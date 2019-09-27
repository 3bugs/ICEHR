import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import './mission.css';
import fetch from "isomorphic-unfetch";
import {HOST_BACKEND} from '../etc/constants';

export default class Mission extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async function ({req, query}) {
        let missionList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const result = await fetch(baseUrl + '/api/get_intro', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'mission',
            }),
        });
        const resultJson = await result.json();
        if (resultJson['error']['code'] === 0) {
            missionList = resultJson['dataList'];
            errorMessage = null;
        } else {
            missionList = null;
            errorMessage = resultJson['error']['message'];
        }

        return {missionList, errorMessage};
    };

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
                    <div className="container mt-2">
                        <div className="row">
                            <div className="col text-title-top">
                                <h3>ภารกิจ</h3>
                            </div>
                        </div>
                        {
                            this.props.missionList.map((item, index) => (
                                <div className="row mt-3">
                                    <div className="col-md-6">
                                        <img src={`${HOST_BACKEND}/uploads/intro_assets/${item.image_file_name}`} className="img-fluid"/>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="mission-detail">
                                            <h4>{item.title}</h4>
                                            <div dangerouslySetInnerHTML={{__html: item.details}}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </React.Fragment>

                <style jsx>{`
                    .text-title-top h3 {
                        width: 100%;
                        font-size: 2em;
                        text-align: center;
                        margin-top: 40px;
                    }
                `}</style>
            </MainLayout>
        );
    }
}