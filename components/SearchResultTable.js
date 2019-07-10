import {Element} from "react-scroll/modules";
import {SERVICE_TRAINING, SERVICE_SOCIAL} from "../etc/constants";
import Link from "next/link";
import {formatCourseDateShort, numberWithCommas} from "../etc/utils";

export default class SearchResultTable extends React.Component {

    render() {
        const {serviceType, courseList, handleCloseSearchResultModal} = this.props;

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
                                        <th scope="col" style={{width: serviceType === SERVICE_TRAINING ? '35%' : '40%'}}>ชื่อหลักสูตร / รุ่นที่</th>
                                        {serviceType === SERVICE_TRAINING &&
                                        <th scope="col" style={{width: '10%'}}>ค่าลงทะเบียน</th>
                                        }
                                        <th scope="col" style={{width: serviceType === SERVICE_TRAINING ? '30%' : '35%'}}>สถานที่อบรม</th>
                                        <th scope="col" style={{width: '10%'}}>สถานะ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {
                                        courseList && courseList.length > 0 &&
                                        courseList.map((course, index) => {
                                            return (
                                                <Link
                                                    key={index}
                                                    as={`/service-${serviceType}/${course.id}`}
                                                    href={`/service-${serviceType}?courseId=${course.id}`}
                                                >
                                                    <tr className={'course-row'}
                                                        onClick={handleCloseSearchResultModal}>
                                                        <td>{formatCourseDateShort(course.beginDate, course.endDate)}</td>
                                                        <td>{course.name}</td>
                                                        {serviceType === SERVICE_TRAINING &&
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
                                        courseList && courseList.length === 0 &&
                                        <tr>
                                            <td colSpan={20} style={{color: 'red', textAlign: 'center'}}>ไม่มีข้อมูล</td>
                                        </tr>
                                    }
                                    </tbody>
                                </table>
                            </Element>
                        </div>
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
        )
    }
}