export function formatCourseDateShort(beginDate, endDate) {
    const monthNames = [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    ];

    let bDateObj = new Date(beginDate);
    let bDate = bDateObj.getDate();
    let bMonth = monthNames[bDateObj.getMonth()];
    let bYear = (parseInt(bDateObj.getFullYear()) + 543).toString().substr(2);

    let eDateObj = new Date(endDate);
    let eDate = eDateObj.getDate();
    let eMonth = monthNames[eDateObj.getMonth()];
    let eYear = (parseInt(eDateObj.getFullYear()) + 543).toString().substr(2);

    if (bDate === eDate && bMonth === eMonth && bYear === eYear) {
        return `${bDate} ${bMonth} ${bYear}`;
    } else if (bMonth === eMonth && bYear === eYear) {
        return `${bDate}-${eDate} ${bMonth} ${bYear}`;
    } else if (bYear === eYear) {
        return `${bDate} ${bMonth} - ${eDate} ${eMonth} ${bYear}`;
    } else {
        return `${bDate} ${bMonth} ${eYear} - ${eDate} ${eMonth} ${eYear}`;
    }
}

export function formatCourseDateLong(beginDate, endDate) {
    const monthNames = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const dayNames = [
        'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'
    ];

    let bDateObj = new Date(beginDate);
    let bDay = dayNames[bDateObj.getDay()];
    let bDate = bDateObj.getDate();
    let bMonth = monthNames[bDateObj.getMonth()];
    let bYear = (parseInt(bDateObj.getFullYear()) + 543).toString();

    let eDateObj = new Date(endDate);
    let eDay = dayNames[eDateObj.getDay()];
    let eDate = eDateObj.getDate();
    let eMonth = monthNames[eDateObj.getMonth()];
    let eYear = (parseInt(eDateObj.getFullYear()) + 543).toString();

    if (bDate === eDate && bMonth === eMonth && bYear === eYear) {
        return `วัน${bDay}ที่ ${bDate} ${bMonth} ${bYear}`;
    } else {
        return `วัน${bDay}ที่ ${bDate} ${bMonth} ${eYear} - วัน${eDay}ที่ ${eDate} ${eMonth} ${eYear}`;
    }
}

export function numberWithCommas(n) {
    var parts = n.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}
