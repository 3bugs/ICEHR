import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import fetch from "isomorphic-unfetch";
import {HOST_BACKEND} from '../etc/constants';
import {Element, scroller} from "react-scroll";

export default class Organization extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    static getInitialProps = async function ({req, query}) {
        let committeeList = null;
        let errorMessage = null;

        const baseUrl = req ? `${req.protocol}://${req.get('Host')}` : '';
        const result = await fetch(baseUrl + '/api/get_committee', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'about',
            }),
        });
        const resultJson = await result.json();
        if (resultJson['error']['code'] === 0) {
            committeeList = resultJson['dataList'];
            errorMessage = null;
        } else {
            committeeList = null;
            errorMessage = resultJson['error']['message'];
        }

        return {committeeList, errorMessage};
    };

    componentDidMount() {
    }

    render() {
        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-vision-top"/>
                    <div className="bgnav-abt">
                        <div className="container d-none d-sm-block d-md-block d-lg-block d-xl-block">
                            <div className="row">
                                <div className="col">
                                    <ul className="aboutmenu">
                                        <li><a href="/about">เกี่ยวกับองค์กร </a></li>
                                        <li><a href="/mission">ภารกิจ </a></li>
                                        <li><a href="/organization">โครงสร้างองค์กร </a></li>
                                        <li><a href="/personnel">ผู้บริหารและบุคลากร </a></li>
                                        <li><a href="/committee" className="active-menu">คณะกรรมการ </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="container  d-block d-sm-none d-md-none d-lg-none d-xl-none">
                            <div className="row">
                                <div className="col">
                                    <div className="menu-wrapper">
                                        <ul className="menu">
                                            <li className="menu-item">
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
                                            <li className="menu-item active">
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
                                <h3>คณะกรรมการ</h3>
                            </div>
                        </div>
                        <div className="row mt-3">
                            {
                                this.props.committeeList.map((committee, index) => (
                                    <div className="col-6 col-sm-3 executive-detail">
                                        <div className="pic-executive">
                                            <img src={`${HOST_BACKEND}/uploads/user_assets/${committee.image_file_name}`}
                                                 className="img-fluid"
                                                 style={{borderRadius: '50%'}}/>
                                        </div>
                                        <h6>{`${committee.title}${committee.first_name} ${committee.last_name}`}</h6>
                                        <p>{committee.department_name}</p>
                                    </div>
                                ))
                            }
                        </div>
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