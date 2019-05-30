/*memberData.id = results[0].id;
memberData.title = results[0].title;
memberData.firstName = results[0].first_name;
memberData.lastName = results[0].last_name;
memberData.age = results[0].age;
memberData.jobPosition = results[0].job_position;
memberData.organizationName = results[0].organization_name;
memberData.organizationType = results[0].organization_type;
memberData.phone = results[0].phone;
memberData.email = results[0].email;

memberData.address = results[0].address;
memberData.subDistrict = results[0].sub_district;
memberData.district = results[0].district;
memberData.province = results[0].province;
memberData.postalCode = results[0].postal_code;
memberData.organizationPhone = results[0].organization_phone;
memberData.taxId = results[0].tax_id;*/

const KEY_USER = 'user';

export function getLoginUser() {
    let userJsonString = localStorage.getItem(KEY_USER);

    if (userJsonString != null) {
        return JSON.parse(userJsonString);
    } else {
        return null;
    }
}

export function setLoginUser(user) {
    if (user == null) {
        localStorage.removeItem(KEY_USER);
    } else {
        localStorage.setItem(KEY_USER, JSON.stringify(user));
    }
}

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

export function isString(value) {
    return typeof value === 'string' || value instanceof String;
}

export function isValidEmail(value) {
    let emailRegex = /[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}/igm;
    return emailRegex.test(value);
}

export function isValidPid(pid) {
    if (pid.length !== 13) return false;

    let sum = 0;
    for (let i = 0; i < 12; i++) {
        sum += parseFloat(pid.charAt(i)) * (13 - i);
    }

    return (11 - sum % 11) % 10 === parseInt(pid.charAt(12));
}