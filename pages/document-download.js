import MainLayout from "../layouts/MainLayout";
import NextHead from 'next/head';
import fetch from 'isomorphic-unfetch';
import {HOST_BACKEND, LIMIT_PER_PAGE} from "../etc/constants";
import Modal from "react-bootstrap/Modal";
import '../pages/pagination.css';
import ReactPaginate from "react-paginate";
import {Element, scroller} from "react-scroll/modules";

const DOCUMENT_TYPE_TRAINING = 'training';
const DOCUMENT_TYPE_REPORT = 'report';
const DOCUMENT_TYPE_KM = 'km';
const DOCUMENT_TYPE_QA = 'qa';
const DOCUMENT_TYPE_BOOKLET = 'booklet';

class DocumentDownloadItem_1 extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            documentType: null,
        };
    }

    handleClickDownload = () => {
        const {data} = this.props;
        window.open(`${HOST_BACKEND}/uploads/document_downloads/${data.file_name}`, '_blank');

        this.props.onClickDownload(data.id);
    };

    render() {
        const {data} = this.props;

        return (
            <React.Fragment>
                <div className="row mt-4">
                    <div className="col-sm-8"><span className="docicon"><img src="/static/images/doc-icon.svg"/></span>
                        <div className="downloadname">
                            <h4>{data.title}</h4>
                            <p>{data.short_description}</p>
                        </div>
                    </div>
                    <div className="col-sm-4">
                        <a href="javascript:void(0)"
                           onClick={this.handleClickDownload}
                           className="btn btn-light">
                            ดาวน์โหลด <img src="/static/images/download-icon.svg" className="icon-dm"/>
                        </a>
                    </div>
                </div>
                <div className="border-bott"/>

                <style jsx>{`
                    .btn-light {
                        font-family: 'DBHeavent-Med';
                        float: right;
                    }
                    
                    .border-bott {
                        margin-bottom: 20px;
                    }
                    
                    @media (max-width: 767.98px) {
                        .btn-light {
                            float: left;
                            margin-bottom: 20px;
                        }
                    }
                `}</style>
            </React.Fragment>
        );
    }
}

class DocumentDownloadItem_2 extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            documentType: null,
        };
    }

    handleClickDownload = () => {
        const {data} = this.props;
        window.open(`${HOST_BACKEND}/uploads/document_downloads/${data.file_name}`, '_blank');

        this.props.onClickDownload(data.id);
    };

    render() {
        const {data} = this.props;

        return (
            <React.Fragment>
                <div className="row">
                    <div className="col-sm-4">
                        <img src={`${HOST_BACKEND}/uploads/document_downloads/${data.image_file_name}`}
                             className="img-fluid"/>
                    </div>
                    <div className="col-sm-8">
                        <div className="detail-download">
                            <h5>{data.title}</h5>
                            {data.short_description}
                        </div>
                        <div className="row">
                            <div className="col-12 col-md-6">
                                <div className="countview">
                                    <span><i className="far fa-eye"/> {data.download}</span>
                                </div>
                            </div>
                            <div className="col-12 col-md-6">
                                <a href="javascript:void(0)"
                                   className="download"
                                   onClick={this.handleClickDownload}>
                                    ดาวน์โหลด <img src="/static/images/download-red.svg" className="icon-dm-2"/>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    
                `}</style>
            </React.Fragment>
        );
    }
}

export default class DocumentDownload extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            documentType: null,
            documentDownloadList: null,
            errorMessage: null,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
    }

    static getInitialProps = context => {
        const {documentType} = context.query;
        return {documentType};
    };

    componentDidMount() {

    }

    doGetDocumentDownload() {
        console.log('doGetDocumentDownload() - ' + Math.random());

        const {documentType} = this.props;

        fetch('/api/get_document_download', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                documentType,
                limit: LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        documentDownloadList: result.dataList,
                        pageCount: Math.ceil(result.totalCount / LIMIT_PER_PAGE),
                        errorMessage: null,
                    }, () => {
                        if (!this.state.firstLoad) {
                            scroller.scrollTo('topOfTable', {
                                duration: 500,
                                smooth: true,
                                offset: -80,
                            });
                        } else {
                            this.setState({
                                firstLoad: false,
                            });
                        }
                    });
                } else {
                    this.setState({
                        documentDownloadList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            })
            .catch(error => {
                //alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
                this.setState({
                    documentDownloadList: null,
                    errorMessage: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error,
                });
            });
    }

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetDocumentDownload();
        });
    };

    handleDownloadClick = (id) => {
        this.doUpdateDocumentDownloadCount(id);
    };

    doUpdateDocumentDownloadCount(id) {
        //const {data} = this.props;

        fetch('/api/update_document_download_count', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    /*this.setState({
                    });*/
                } else {
                    console.log(result['error']['message']);
                }
            })
            .catch(error => {
                console.log('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
            });
    }

    render() {
        const {documentType} = this.props;
        const {documentDownloadList, errorMessage} = this.state;
        const pageTitleList = {
            [DOCUMENT_TYPE_TRAINING]: 'เอกสารการอบรม',
            [DOCUMENT_TYPE_REPORT]: 'รายงานผลการดำเนินงาน',
            [DOCUMENT_TYPE_KM]: 'KM',
            [DOCUMENT_TYPE_QA]: 'ประกันคุณภาพ (QA)',
            [DOCUMENT_TYPE_BOOKLET]: 'จุลสาร/อินโฟกราฟิค',
        };
        const pageTitle = pageTitleList[this.props.documentType];

        let rows = [];
        let row = null;
        if (documentDownloadList) {
            for (let i = 0; i < documentDownloadList.length; i++) {
                if (i % 2 === 0) {
                    row = [];
                    row.push(documentDownloadList[i]);

                    if (i === documentDownloadList.length - 1) {
                        row.push(null);
                        rows.push(row);
                    }
                } else {
                    row.push(documentDownloadList[i]);
                    rows.push(row);
                }
            }
        }

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-download-top">
                        <h3>Download</h3>
                        <h2>เอกสารดาวน์โหลด</h2>
                    </div>
                    <div className="container mt-4">
                        <div className="row">
                            <div className="col">
                                <Element name={'topOfTable'}>
                                    <h4 className="text-black">{pageTitle}</h4>
                                </Element>
                            </div>
                        </div>

                        {!documentDownloadList &&
                        <div style={{textAlign: 'center', color: 'red'}}>
                            {errorMessage}
                        </div>
                        }

                        {documentDownloadList && (documentDownloadList.length === 0) &&
                        <div style={{textAlign: 'center', color: 'red'}}>
                            ไม่มีข้อมูล
                        </div>
                        }

                        {documentDownloadList && (documentDownloadList.length > 0)
                        && (documentType === DOCUMENT_TYPE_KM || documentType === DOCUMENT_TYPE_QA || documentType === DOCUMENT_TYPE_BOOKLET)
                        && rows.map(row => (
                            <React.Fragment>
                                <div className="row mt-3">
                                    <div className="col">
                                        <div className="row">
                                            {
                                                row.map(col => {
                                                    return (
                                                        <div className="col">
                                                            {col != null &&
                                                            <DocumentDownloadItem_2
                                                                data={col}
                                                                onClickDownload={this.handleDownloadClick}
                                                            />
                                                            }
                                                            {col == null &&
                                                            <div/>
                                                            }
                                                        </div>
                                                    ); //return
                                                })
                                            }
                                        </div>
                                    </div>
                                </div>
                                <div className="border-bott"/>
                            </React.Fragment>
                        ))
                        }

                        {documentDownloadList && (documentDownloadList.length > 0)
                        && (documentType === DOCUMENT_TYPE_TRAINING || documentType === DOCUMENT_TYPE_REPORT)
                        && documentDownloadList.map(entry => (
                            <DocumentDownloadItem_1
                                data={entry}
                                onClickDownload={this.handleDownloadClick}
                            />
                        ))
                        }
                    </div>

                    <div style={{textAlign: 'center', marginTop: '30px'}}>
                        <ReactPaginate
                            initialPage={this.state.initialPage}
                            previousLabel={'<'}
                            nextLabel={'>'}
                            breakLabel={'...'}
                            breakClassName={'break-me'}
                            pageCount={this.state.pageCount}
                            marginPagesDisplayed={2}
                            pageRangeDisplayed={5}
                            onPageChange={this.handlePageClick}
                            containerClassName={'pagination'}
                            activeClassName={'pagination-active'}
                            previousClassName={'pagination-older'}
                            nextClassName={'pagination-newer'}
                        />
                    </div>
                </React.Fragment>

                <style jsx>{`
                    .border-bott {
                        margin-bottom: 20px;
                    }
                `}</style>
            </MainLayout>
        );
    }
}