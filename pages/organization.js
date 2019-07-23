import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';

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
                                <p>โครงสร้างองค์กร</p>
                                <h3>โครงสร้างสถาบัน</h3></div>
                        </div>
                        <div className="row">
                            <div className="col text-center">
                                <a href="/static/images/structure-pic.jpg" className="single_image">
                                    <img src="/static/images/structure-pic.jpg" className="img-fluid"/>
                                </a>
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