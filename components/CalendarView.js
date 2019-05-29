import MainLayout from "../layouts/MainLayout";
import {getDaysInMonth} from "../backend/bower_components/moment/src/lib/units/month";
import {longDayNames, longMonthNames} from "../etc/constants";

class DayCell extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {

    }

    render() {
        return (
            <React.Fragment>
                {this.props.active &&
                <div className="day col-sm p-2 border border-left-0 border-top-0 ">
                    <h5 className="row align-items-center">
                        <span className="date col-1">{this.props.number}</span>
                        <small className="col d-sm-none text-center text-muted">Wednesday</small>
                        <span className="col-1"/>
                    </h5>
                    {
                        this.props.children
                    }
                    {/*<p className="d-sm-none"></p>*/}
                </div>
                }
                {!this.props.active &&
                <div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                    <h5 className="row align-items-center">
                        <span className="date col-1">{this.props.number}</span>
                        <small className="col d-sm-none text-center text-muted">Monday</small>
                        <span className="col-1"/>
                    </h5>
                    {/*<p className="d-sm-none"></p>*/}
                </div>
                }
                <style jsx>{`
                    .book-btn {
                        margin: 0 auto;
                        text-align: center;
                        border: 1px solid #6c6c6c;
                        padding: 0px 20px;
                        color: #6c6c6c;
                        display: table;
                        margin-top: 10px;
                    }
                    
                    .book_detail {
                        text-align: center;
                        line-height: 20px;
                    }
                    
                    .book_detail p {
                        margin-bottom: 0px;
                        font-size: 0.95em;
                    }
                    
                    .book_detail i {
                        color: #1cc951;
                        font-size: 2rem;
                        margin-bottom: 5px;
                        margin-top: -30px;
                    }
                    
                    .fullbook i {
                        color: #dddddd;
                    }
                    
                    .date-event {
                        margin-bottom: 10px;
                    }
                    
                    .month {
                        font-family: 'DBHeavent-Med';
                        font-size: 2.5em;
                    }
                    
                    .year-right {
                        color: #b50303;
                        //float: right;
                        font-size: 1em;
                    }
                    
                    .bg-info {
                        background-color: transparent !important;
                        color: black;
                    }
                    
                    .bg-dark {
                        background-color: #b50303 !important;
                        border-right: 1px solid white;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .book_detail:hover, .book_detail {
                        color: #6c6c6c;
                    }
                    
                    @media (max-width:991px) {
                        .day {
                            height: auto !important;
                        }
                    }
                    
                    @media (max-width:767px) {
                        .display-4 {
                            font-size: 1rem;
                        }
                        .day h5 {
                            background-color: #f8f9fa;
                            padding: 3px 5px 5px;
                            margin: -8px -8px 8px -8px;
                        }
                        .date {
                            padding-left: 4px;
                        }
                            .book_detail i {
                            margin-top: 0px;
                        }
                    }
                    
                    @media (min-width: 576px) {
                        .day {
                            height: 200px;
                        }
                    }                    
                `}</style>
            </React.Fragment>
        );
    }
}

export default class CalendarView extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {};
    }

    componentDidMount() {

    }

    isLeapYear = (year) => {
        return (year % 100 === 0) ? (year % 400 === 0) : (year % 4 === 0);
    };

    /*หาจำนวนวันของเดือน*/
    daysInMonth = (month, year) => {
        const daysInMonthList = [
            31, (this.isLeapYear(year) ? 29 : 28), 31,
            30, 31, 30,
            31, 31, 30,
            31, 30, 31
        ];
        return daysInMonthList[month - 1];
    };

    getPreviousMonth = (month, year) => {
        let previousMonth = month - 1;
        let previousYear = year;
        if (previousMonth < 1) {
            previousMonth = 12;
            --previousYear;
        }
        return {previousMonth, previousYear};
    };

    getNextMonth = (month, year) => {
        let nextMonth = month + 1;
        let nextYear = year;
        if (nextMonth > 12) {
            nextMonth = 1;
            ++nextYear;
        }
        return {nextMonth, nextYear};
    };

    onClickChevronLeft = () => {
        const {month, year} = this.props;
        const {previousMonth, previousYear} = this.getPreviousMonth(month, year);
        this.props.handlePreviousNextMonthCallback(previousMonth, previousYear);
    };

    onClickChevronRight = () => {
        const {month, year} = this.props;
        const {nextMonth, nextYear} = this.getNextMonth(month, year);
        this.props.handlePreviousNextMonthCallback(nextMonth, nextYear);
    };

    render() {
        const {month, year} = this.props;
        const currentMonthString = ('0' + month).slice(-2);
        const currentYearString = String(year);
        const daysInCurrentMonth = this.daysInMonth(month, year);

        const {nextMonth, nextYear} = this.getNextMonth(month, year);
        const nextMonthString = ('0' + nextMonth).slice(-2);
        const nextYearString = String(nextYear);
        const daysInNextMonth = this.daysInMonth(nextMonth, nextYear);

        const {previousMonth, previousYear} = this.getPreviousMonth(month, year);
        const previousMonthString = ('0' + previousMonth).slice(-2);
        const previousYearString = String(previousYear);
        const daysInPreviousMonth = this.daysInMonth(previousMonth, previousYear);

        //alert(`เดือนที่แล้ว ${previousYearString}-${previousMonthString} จำนวน ${daysInPreviousMonth} วัน\nเดือนหน้า ${nextYearString}-${nextMonthString} จำนวน ${daysInNextMonth} วัน`);

        const firstDayOfMonthString = `${currentYearString}-${currentMonthString}-01`; // yyyy-mm-dd
        const dayIndexOfFirstDate = new Date(firstDayOfMonthString).getDay();

        const days = [];
        let dayNumber = 1;
        let i;
        for (i = 0; dayNumber <= daysInCurrentMonth; i++) {
            if (i < dayIndexOfFirstDate) { // ช่องวันที่ของเดือนก่อนหน้า (ถ้ามี, เติมให้เต็มแถว)
                days.push((daysInPreviousMonth - (dayIndexOfFirstDate - i)) + 1);
            } else { // ช่องวันที่ของเดือนปัจจุบัน
                days.push(dayNumber++);
            }
            if (i % 7 === 6) { // ใส่ tag ให้ขึ้นแถวใหม่
                days.push(0);
            }
        }

        // ช่องวันที่ของเดือนถัดไป (ถ้ามี, เติมให้เต็มแถว)
        for (let j = 0; (i % 7 !== 0) && (j < 7 - i % 7); j++) {
            days.push(j + 1);
        }

        let currentMonthStatus = false;

        return (
            <React.Fragment>
                <div className="container mt-4">
                    <div className="row mt-3">
                        <div className="col">
                            <div className="container-fluid">
                                <header>
                                    <div style={{display: 'flex', flexDiection: 'row', alignItems: 'center'}}>
                                        <div style={{flex: 1, textAlign: 'left'}}>
                                            <i className="fa fa-chevron-left mb-3"
                                               onClick={this.onClickChevronLeft}/>
                                        </div>
                                        <div style={{flex: 1}}>
                                            <h4 className="display-4 mb-3 text-center month">{longMonthNames[month - 1]}&nbsp;&nbsp;
                                                <span className="year-right">{year + 543}</span>
                                            </h4>
                                        </div>
                                        <div style={{flex: 1, textAlign: 'right'}}>
                                            <i className="fa fa-chevron-right mb-3"
                                               onClick={this.onClickChevronRight}/>
                                        </div>
                                    </div>

                                    <div className="row d-none d-sm-flex p-1 bg-dark text-white">
                                        {/*ชื่อวันอาทิตย์-เสาร์*/}
                                        {
                                            longDayNames.map(dayName => (
                                                <h5 className="col-sm p-1 text-center">{dayName}</h5>
                                            ))
                                        }
                                    </div>
                                </header>
                                <div className="row border border-right-0 border-bottom-0">
                                    {
                                        days.map(day => {
                                            if (day === 0) {
                                                return (<div className="w-100"/>);
                                            } else {
                                                if (day === 1) {
                                                    currentMonthStatus = !currentMonthStatus;
                                                }
                                                return (
                                                    <DayCell number={day} active={currentMonthStatus}>
                                                        <a href="#" className="event d-block book_detail" title="Test Event 1">
                                                            <i className="far fa-check-circle"></i>
                                                            <p>หลักสูตรการอบรม สำหรับผู้ขอรับใบอนุญาตขับรถ</p>
                                                        </a>
                                                        <a href="service-2-1.php" className="book-btn">จอง</a>
                                                    </DayCell>
                                                );
                                            }
                                        })
                                    }
                                    {/*<div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">29</span>
                                            <small className="col d-sm-none text-center text-muted">Sunday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">30</span>
                                            <small className="col d-sm-none text-center text-muted">Monday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">31</span>
                                            <small className="col d-sm-none text-center text-muted">Tuesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 ">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">1</span>
                                            <small className="col d-sm-none text-center text-muted">Wednesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>

                                    <div className="day col-sm p-2 border border-left-0 border-top-0 ">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">2</span>
                                            <small className="col d-sm-none text-center text-muted">Thursday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>

                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row date-event align-items-center">
                                            <span className="date col-1">3</span>
                                            <small className="col d-sm-none text-center text-muted">Friday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <a href="#" className="event d-block book_detail" title="Test Event 1">
                                            <i className="far fa-check-circle"></i>
                                            <p>หลักสูตรการอบรม สำหรับผู้ขอรับใบอนุญาตขับรถ จำนวน 5 ชั่วโมง</p>
                                        </a>
                                        <a href="service-2-1.php" className="book-btn">จอง</a>
                                    </div>

                                    <div className="day col-sm p-2 border border-left-0 border-top-0 ">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">4</span>
                                            <small className="col d-sm-none text-center text-muted">Saturday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>

                                    <div className="w-100"></div>

                                    <div className="day col-sm p-2 border border-left-0 border-top-0 ">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">5</span>
                                            <small className="col d-sm-none text-center text-muted">Sunday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <a href="#" className="event d-block book_detail" title="Test Event 1"><i className="far fa-check-circle"></i>
                                            <p>หลักสูตรการอบรม สำหรับผู้ขอรับ
                                                ใบอนุญาตขับรถ จำนวน 5 ชั่วโมง</p></a> <a href="#" className="book-btn">จอง</a>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">6</span>
                                            <small className="col d-sm-none text-center text-muted">Monday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">7</span>
                                            <small className="col d-sm-none text-center text-muted">Tuesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">8</span>
                                            <small className="col d-sm-none text-center text-muted">Wednesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <a href="#" className="event d-block book_detail fullbook" title="Test Event 1"><i className="fas fa-minus-circle"></i>
                                            <p>หลักสูตรการอบรม สำหรับผู้ขอรับ
                                                ใบอนุญาตขับรถ จำนวน 5 ชั่วโมง</p></a> <a href="#" className="book-btn">เต็ม</a></div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 text-truncate ">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">9</span>
                                            <small className="col d-sm-none text-center text-muted">Thursday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">10</span>
                                            <small className="col d-sm-none text-center text-muted">Friday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">11</span>
                                            <small className="col d-sm-none text-center text-muted">Saturday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>

                                    <div className="w-100"></div>

                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">12</span>
                                            <small className="col d-sm-none text-center text-muted">Sunday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">13</span>
                                            <small className="col d-sm-none text-center text-muted">Monday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">14</span>
                                            <small className="col d-sm-none text-center text-muted">Tuesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">15</span>
                                            <small className="col d-sm-none text-center text-muted">Wednesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">16</span>
                                            <small className="col d-sm-none text-center text-muted">Thursday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">17</span>
                                            <small className="col d-sm-none text-center text-muted">Friday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">18</span>
                                            <small className="col d-sm-none text-center text-muted">Saturday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="w-100"></div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">19</span>
                                            <small className="col d-sm-none text-center text-muted">Sunday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">20</span>
                                            <small className="col d-sm-none text-center text-muted">Monday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <a href="#" className="event d-block book_detail" title="Test Event 1"><i className="far fa-check-circle"></i>
                                            <p>หลักสูตรการอบรม สำหรับผู้ขอรับ
                                                ใบอนุญาตขับรถ จำนวน 5 ชั่วโมง</p></a> <a href="#" className="book-btn">จอง</a></div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">21</span>
                                            <small className="col d-sm-none text-center text-muted">Tuesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">22</span>
                                            <small className="col d-sm-none text-center text-muted">Wednesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">23</span>
                                            <small className="col d-sm-none text-center text-muted">Thursday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">24</span>
                                            <small className="col d-sm-none text-center text-muted">Friday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">25</span>
                                            <small className="col d-sm-none text-center text-muted">Saturday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="w-100"></div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">26</span>
                                            <small className="col d-sm-none text-center text-muted">Sunday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">27</span>
                                            <small className="col d-sm-none text-center text-muted">Monday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">28</span>
                                            <small className="col d-sm-none text-center text-muted">Tuesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">29</span>
                                            <small className="col d-sm-none text-center text-muted">Wednesday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1">30</span>
                                            <small className="col d-sm-none text-center text-muted">Thursday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1"></span>
                                            <small className="col d-sm-none text-center text-muted">Friday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>
                                    <div className="day col-sm p-2 border border-left-0 border-top-0 d-none d-sm-inline-block bg-light text-muted">
                                        <h5 className="row align-items-center">
                                            <span className="date col-1"></span>
                                            <small className="col d-sm-none text-center text-muted">Saturday</small>
                                            <span className="col-1"></span>
                                        </h5>
                                        <p className="d-sm-none"></p>
                                    </div>*/}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br/><br/><br/>

                <style jsx>{`
                    .fa-chevron-left:hover, .fa-chevron-right:hover {
                        cursor: pointer;
                        color: #b50303;                     
                    }
                    
                    .book-btn {
                        margin: 0 auto;
                        text-align: center;
                        border: 1px solid #6c6c6c;
                        padding: 0px 20px;
                        color: #6c6c6c;
                        display: table;
                        margin-top: 10px;
                    }
                    
                    .book_detail {
                        text-align: center;
                        line-height: 20px;
                    }
                    
                    .book_detail p {
                        margin-bottom: 0px;
                        font-size: 0.95em;
                    }
                    
                    .book_detail i {
                        color: #1cc951;
                        font-size: 2rem;
                        margin-bottom: 5px;
                        margin-top: -30px;
                    }
                    
                    .fullbook i {
                        color: #dddddd;
                    }
                    
                    .date-event {
                        margin-bottom: 10px;
                    }
                    
                    .month {
                        font-family: 'DBHeavent-Med';
                        font-size: 2.5em;
                    }
                    
                    .year-right {
                        color: #b50303;
                        //float: right;
                        font-size: 1em;
                    }
                    
                    .bg-info {
                        background-color: transparent !important;
                        color: black;
                    }
                    
                    .bg-dark {
                        background-color: #b50303 !important;
                        border-right: 1px solid white;
                    }
                    
                    .submitbox {
                        margin-top: 0px;
                    }
                    
                    .book_detail:hover, .book_detail {
                        color: #6c6c6c;
                    }
                    
                    @media (max-width:991px) {
                        .day {
                            height: auto !important;
                        }
                    }
                    
                    @media (max-width:767px) {
                        .display-4 {
                            font-size: 1rem;
                        }
                        .day h5 {
                            background-color: #f8f9fa;
                            padding: 3px 5px 5px;
                            margin: -8px -8px 8px -8px;
                        }
                        .date {
                            padding-left: 4px;
                        }
                            .book_detail i {
                            margin-top: 0px;
                        }
                    }
                    
                    @media (min-width: 576px) {
                        .day {
                            height: 200px;
                        }
                    }                    
                `}</style>
            </React.Fragment>
        );
    }
}