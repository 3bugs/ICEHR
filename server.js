const express = require('express');
const next = require('next');
const mysql = require('mysql');
const jwt = require('jsonwebtoken');
const Error = require('./model/Error');

const SECRET_KEY = 'icehr_and_2fellows';
const INVALID_TOKEN = -1;

const dev = process.env.NODE_ENV !== 'production';
const app = next({dev});
const handle = app.getRequestHandler();
const bodyParser = require('body-parser');

app
    .prepare()
    .then(() => {
        const server = express();

        server.use(bodyParser.json());       // to support JSON-encoded bodies
        server.use(bodyParser.urlencoded({   // to support URL-encoded bodies
            extended: true
        }));

        /*จัดการ api call*/
        server.post('/api/:action', (req, res) => {
            /*const actualPage = '/post';
            const queryParams = { title: req.params.id };
            app.render(req, res, actualPage, queryParams);*/

            let db = mysql.createConnection({
                host: 'localhost',
                user: 'root',
                password: '',
                database: 'icehr'
            });
            db.connect(function (err) {
                if (err) {
                    res.send({
                        error: new Error(1, 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล', 'error connect db: ' + err.stack),
                    });
                    return;
                }

                console.log('Db connected as id ' + db.threadId);

                switch (req.params.action) {
                    case 'login_member':
                        doLoginMember(req, res, db);
                        break;
                    case 'register_member':
                        doRegisterMember(req, res, db);
                        break;
                    case 'logout_member':
                        break;
                    case 'get_course':
                        doGetCourse(req, res, db);
                        break;
                    default:
                        res.status(404).end();
                        break;
                }

                //db.end();
            });
        });

        /*default สำหรับ post*/
        server.post('*', (req, res) => {
            res.status(404).end();
        });

        /*default สำหรับ get*/
        server.get('*', (req, res) => {
            return handle(req, res)
        });

        server.listen(3000, err => {
            if (err) throw err;
            console.log('> Ready on http://localhost:3000');
        })
    })
    .catch(ex => {
        console.error(ex.stack);
        process.exit(1);
    });

encodeToken = (id) => {
    return jwt.sign({id: id}, SECRET_KEY);
};

decodeToken = (token) => {
    try {
        let decoded = jwt.verify(token, SECRET_KEY);
        return decoded.id;
    } catch (err) {
        return INVALID_TOKEN;
    }
};

/*login สมาชิก*/
doLoginMember = (req, res, db) => {
    let inputEmail = req.body.email;
    let inputPassword = req.body.password;

    db.query(
            `SELECT *
             FROM member
             WHERE email = ?
               AND password = ?`,
        [inputEmail, inputPassword],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลบัญชีผู้ใช้งาน', 'error run query: ' + err.stack),
                });
            } else if (results.length === 0) {
                res.send({
                    error: new Error(1, 'อีเมล หรือรหัสผ่าน ไม่ถูกต้อง', ''),
                    dataList: null
                });
            } else {
                let memberData = {};
                memberData.id = results[0].id;
                memberData.title = results[0].title;
                memberData.firstName = results[0].first_name;
                memberData.lastName = results[0].last_name;
                memberData.age = results[0].age;
                memberData.jobPosition = results[0].job_position;
                memberData.organizationName = results[0].organization_name;
                memberData.organizationType = results[0].organization_type;
                memberData.phone = results[0].phone;
                memberData.email = results[0].email;
                memberData.loginToken = encodeToken(results[0].id);
                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    memberData
                });
            }
        });
    db.end();
};

/*register สมาชิก*/
doRegisterMember = (req, res, db) => {
    let inputTitle = req.body.title;
    let inputFirstName = req.body.firstName;
    let inputLastName = req.body.lastName;
    let inputAge = req.body.age;
    let inputJobPosition = req.body.jobPosition;
    let inputOrganizationName = req.body.organizationName;
    let inputOrganizationType = req.body.organizationType;
    let inputPhone = req.body.phone;
    let inputEmail = req.body.email;
    let inputPassword = req.body.password;
    /*ฟีลด์ข้างล่างนี้จะมีเฉพาะสมัครสมาชิกแบบองค์กร/บริษัท*/
    let inputAddress = req.body.address;
    let inputSubDistrict = req.body.subDistrict;
    let inputDistrict = req.body.district;
    let inputProvince = req.body.province;
    let inputPostalCode = req.body.postalCode;
    let inputOrganizationPhone = req.body.organizationPhone;
    let inputTaxId = req.body.taxId;

    db.query(
            `SELECT *
             FROM member
             WHERE email = ?`,
        [inputEmail],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลบัญชีผู้ใช้งาน', 'error run query: ' + err.stack),
                });
                db.end();
            } else if (results.length > 0) {
                res.send({
                    error: new Error(1, `ไม่สามารถสมัครสมาชิกได้ เนื่องจากมีอีเมล ${inputEmail} ในระบบแล้ว`, '')
                });
                db.end();
            } else {
                db.query(
                        `INSERT INTO member(title, first_name, last_name, age, job_position, organization_name, organization_type, phone, email, password, address, sub_district, district, province,
                                            postal_code, organization_phone, tax_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [inputTitle, inputFirstName, inputLastName, inputAge, inputJobPosition, inputOrganizationName, inputOrganizationType, inputPhone, inputEmail, inputPassword, inputAddress, inputSubDistrict, inputDistrict, inputProvince, inputPostalCode, inputOrganizationPhone, inputTaxId],

                    function (err, results, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูลลงฐานข้อมูล', 'error run query: ' + err.stack),
                            });
                            console.log(err.stack);
                        } else {
                            res.send({
                                error: new Error(0, 'สมัครสมาชิกสำเร็จ', ''),
                            });
                        }
                    });
                db.end();
            }
        });
};

doGetCourse = (req, res, db) => {
    let inputCourseId = req.body.courseId;

    db.query(
        inputCourseId === undefined ? 'SELECT * FROM course' : 'SELECT * FROM course WHERE id = ?',
        inputCourseId === undefined ? null : [inputCourseId],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลหลักสูตรฝึกอบรม', 'error run query: ' + err.stack),
                });
            } else {
                /*
                * ถ้าจะแปลง js date ไปเป็น mysql date format ใช้ .toISOString().slice(0, 19).replace('T', ' ')
                * */

                let dataList = [];
                results.forEach(row => {
                    dataList.push({
                        id: row.id,
                        name: row.name,
                        details: row.details,
                        applicationFee: row.application_fee,
                        place: row.place,
                        beginDate: row.begin_date,
                        endDate: row.end_date,
                        createdAt: row.created_at,
                    });
                });

                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    dataList
                });
            }
        }
    );
    db.end();
};