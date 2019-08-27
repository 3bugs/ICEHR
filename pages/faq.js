import MainLayout from '../layouts/MainLayout.js';
import NextHead from 'next/head';
import Link from 'next/link';
import './faq.css';
import fetch from "isomorphic-unfetch";
import {LIMIT_PER_PAGE} from "../etc/constants";
import '../pages/pagination.css';
import ReactPaginate from "react-paginate";
import {Element, scroller} from "react-scroll/modules";

class FaqItem extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    render() {
        const {data, index} = this.props;

        return (
            <React.Fragment>
                <div className="card">
                    <div className="card-header" id={'heading' + data.id}>
                        <h5 className="mb-0">
                            <button className="btn-link" data-toggle="collapse" data-target={'#collapse' + data.id} aria-expanded="true" aria-controls={'collapse' + data.id}>
                                <i className="fa" aria-hidden="true"></i>
                                <a><span className="ask">Q</span>{data.title}</a>
                            </button>
                        </h5>
                    </div>
                    <div id={'collapse' + data.id} className={'collapse ' + (index === 0 ? 'show' : 'hide')} aria-labelledby={'heading' + data.id} data-parent="#accordion">
                        <div className="card-body"
                             dangerouslySetInnerHTML={{__html: data.details}}>
                        </div>
                    </div>
                </div>

                <style jsx>{`
                    
                `}</style>
            </React.Fragment>
        );
    }
}

export default class Faq extends React.Component {
    constructor(props, context) {
        super(props, context);
        this.state = {
            faqList: null,
            errorMessage: null,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
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
        } else {
            document.getElementById(d).style.display = "none";
        }
    }

    doGetFaq() {
        console.log('doGetFaq() - ' + Math.random());

        fetch('/api/get_faq', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                limit: LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        faqList: result.dataList,
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
                        faqList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            })
            .catch(error => {
                //alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
                this.setState({
                    faqList: null,
                    errorMessage: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error,
                });
            });
    }

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetFaq();
        });
    };

    render() {
        const {faqList, errorMessage} = this.state;

        return (
            <MainLayout>
                <NextHead>
                </NextHead>

                <React.Fragment>
                    <div className="banner-faq-top"/>
                    <div className="container mt-4">
                        <div className="row">
                            <div className="col">
                                <Element name={'topOfTable'}>
                                    <h4 className="text-black">คำถามที่พบบ่อย</h4>
                                </Element>
                            </div>
                        </div>

                        {!faqList &&
                        <div style={{textAlign: 'center', color: 'red'}}>
                            {errorMessage}
                        </div>
                        }

                        {faqList && (faqList.length === 0) &&
                        <div style={{textAlign: 'center', color: 'red'}}>
                            ไม่มีข้อมูล
                        </div>
                        }

                        {faqList && (faqList.length > 0) &&
                        <div className="row">
                            <div className="col">
                                <div id="accordion">
                                    {
                                        faqList.map((entry, index) => (
                                            <FaqItem
                                                data={entry}
                                                index={index}
                                            />
                                        ))
                                    }
                                </div>
                            </div>
                        </div>
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
                `}</style>
            </MainLayout>
        );
    }
}