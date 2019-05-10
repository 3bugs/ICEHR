import Link from 'next/link';
import {formatCourseDateShort, numberWithCommas} from "../../etc/utils";

export default class CourseList extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            courseList: [],
            errorMessage: null
        };
    }

    componentDidMount() {
        console.log('CourseList componentDidMount() - ' + Math.random());

        fetch('/api/get_course', {
            method: 'post'
        })
            .then(result => result.json())
            .then(result => {
                if (result['error']['code'] === 0) {
                    this.setState({
                        courseList: result['dataList'],
                        errorMessage: null,
                    });
                } else {
                    this.setState({
                        courseList: null,
                        errorMessage: result['error']['message'],
                    });
                }
            });
    }

    render() {
        return (
            <div>
                <div className="container">
                    <div className="row">
                        <div className="col">
                            <table className="table responsive-table table-forservice">
                                <thead>
                                <tr>
                                    <th scope="col">วันที่อบรม</th>
                                    <th scope="col">ชื่อหลักสูตร</th>
                                    <th scope="col">ค่าลงทะเบียน</th>
                                    <th scope="col">สถานที่อบรม</th>
                                    <th scope="col">สถานะ</th>
                                </tr>
                                </thead>
                                <tbody>
                                {
                                    this.state.courseList &&
                                    this.state.courseList.map((course, index) => {
                                        return (
                                            <Link
                                                key={index}
                                                as={`/service-training/${course.id}`}
                                                href={`/service-training?courseId=${course.id}`}
                                            >
                                                <tr className={'course-row'}>
                                                    <td>{formatCourseDateShort(course.beginDate, course.endDate)}</td>
                                                    <td>{course.name}</td>
                                                    <td>{numberWithCommas(course.applicationFee)}</td>
                                                    <td>{course.place}</td>
                                                    <td>เปิดรับสมัคร</td>
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
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <div className="pagination_bot">
                                <nav className="pagination-container">
                                    <div className="pagination"><a className="pagination-newer" href="#"><i className="fas fa-angle-left"></i></a>
                                        <span className="pagination-inner">
											<a href="#">1</a>
											<a className="pagination-active" href="#">2</a>
											<a href="#">3</a>
											<a href="#">4</a>
											<a href="#">5</a>
											<a href="#">6</a>
										</span> <a className="pagination-older" href="#"><i className="fas fa-angle-right"></i></a>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <style jsx>{`
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