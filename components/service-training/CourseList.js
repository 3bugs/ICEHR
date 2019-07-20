import Link from 'next/link';
import {formatCourseDateShort, numberWithCommas} from "../../etc/utils";
import {SERVICE_SOCIAL, SERVICE_TRAINING, LIMIT_PER_PAGE} from "../../etc/constants";
import ReactPaginate from "react-paginate";
import '../../pages/pagination.css';
import {Element, scroller} from 'react-scroll';

export default class CourseList extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            courseList: [],
            errorMessage: null,
            offset: 0,
            initialPage: 0,
            firstLoad: true,
        };
    }

    componentDidMount() {
        //this.doGetCourse(false);
    }

    doGetCourse = () => {
        console.log('CourseList componentDidMount() - ' + Math.random());

        const {serviceType} = this.props;

        fetch('/api/get_course', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                serviceType: serviceType,
                limit: LIMIT_PER_PAGE,
                offset: this.state.offset,
            }),
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        courseList: result.dataList,
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
                        courseList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            })
            .catch(error => {
                //alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error);
                this.setState({
                    courseList: null,
                    errorMessage: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server\n\n' + error,
                });
            });
    };

    handlePageClick = data => {
        let selected = data.selected;
        let offset = Math.ceil(selected * LIMIT_PER_PAGE);

        this.setState({offset, initialPage: selected}, () => {
            this.doGetCourse();
        });
    };

    render() {
        const {serviceType} = this.props;
        /*let endPoint = 'service-';
        switch (serviceType) {
            case SERVICE_TRAINING:
                endPoint += 'training';
                break;
            case SERVICE_SOCIAL:
                endPoint += 'social';
                break;
        }*/

        return (
            <div>
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <Element name={'topOfTable'}>
                                <table className="table responsive-table table-forservice">
                                    <thead>
                                    <tr>
                                        <th scope="col" style={{width: '15%'}}>วันที่อบรม</th>
                                        <th scope="col" style={{width: this.props.serviceType === SERVICE_TRAINING ? '35%' : '40%'}}>ชื่อหลักสูตร / รุ่นที่</th>
                                        {this.props.serviceType === SERVICE_TRAINING &&
                                        <th scope="col" style={{width: '10%'}}>ค่าลงทะเบียน</th>
                                        }
                                        <th scope="col" style={{width: this.props.serviceType === SERVICE_TRAINING ? '30%' : '35%'}}>สถานที่อบรม</th>
                                        <th scope="col" style={{width: '10%'}}>สถานะ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {
                                        this.state.courseList &&
                                        this.state.courseList.map((course, index) => {
                                            return (
                                                <Link
                                                    key={index}
                                                    as={`/service-${serviceType}/${course.id}`}
                                                    href={`/service-${serviceType}?courseId=${course.id}`}
                                                >
                                                    <tr className={'course-row'}>
                                                        <td>{formatCourseDateShort(course.beginDate, course.endDate)}</td>
                                                        <td>{course.name}</td>
                                                        {this.props.serviceType === SERVICE_TRAINING &&
                                                        <td style={{textAlign: 'right'}}>{numberWithCommas(course.applicationFee)}</td>
                                                        }
                                                        <td>{course.place}</td>
                                                        <td style={{textAlign: 'center'}}>เปิดรับสมัคร</td>
                                                    </tr>
                                                </Link>
                                            );
                                        })
                                    }
                                    {
                                        !this.state.courseList &&
                                        <tr className={'course-row'}>
                                            <td colSpan={5} style={{textAlign: 'center', color: 'red', padding: '20px'}}>
                                                {this.state.errorMessage}
                                            </td>
                                        </tr>
                                    }
                                    </tbody>
                                </table>
                            </Element>
                        </div>
                    </div>

                    <div style={{textAlign: 'center'}}>
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
                </div>

                <style jsx>{`
                    td {
                        text-align: left;
                    }
                    
                    .course-row {
                        cursor: pointer;
                    }
                    
                    /* Responsive Table Style */
                    
                    .responsive-table {
                        text-align: left;
                        background-color: #fff;
                        border-collapse: collapse;
                        margin: 40px auto;
                    }
                    
                    .responsive-table tr:hover {
                        background-color: #ffedda;
                    }
                    
                    .responsive-table th,
                    .responsive-table td {
                        padding: 3px 10px;
                    }
                    
                    @media (max-width: 480px) {
                        .responsive-table {
                            width: 100%;
                        }
                    
                        .responsive-table thead {
                            display: none;
                        }
                    
                        .responsive-table tbody tr:nth-of-type(even) {
                            background-color: #ffedda;
                        }
                    
                        .responsive-table tbody td {
                            display: block;
                            font-size: 1rem;
                        }
                    
                        .responsive-table tbody td:before {
                            content: attr(data-table);
                            display: block;
                            float: left;
                            width: 40%;
                            margin-right: 10px;
                            padding-right: 10px;
                            font-size: 1rem;
                            border-right: 1px solid #ccc;
                        }
                    
                        .responsive-table tbody td:after {
                            content: '';
                            display: block;
                            clear: both;
                        }
                    
                        .responsive-table tr {
                            border: 1px solid #ccc;
                        }
                    }
                    
                    /* End Responsive Table Style */                
                `}</style>
            </div>
        );
    }
}