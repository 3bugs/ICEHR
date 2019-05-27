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

        /* ********** บริการวิชาการ (ฝึกอบรม) ********** */
        server.get('/service-training/:id', (req, res) => {
            const actualPage = '/service-training';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-training-register/:id', (req, res) => {
            const actualPage = '/service-training-register';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-training-register', (req, res) => {
            res.status(404).end();
        });

        /* ********** บริการสังคม ********** */
        server.get('/service-social/:id', (req, res) => {
            const actualPage = '/service-social';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-social-register/:id', (req, res) => {
            const actualPage = '/service-social-register';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-social-register', (req, res) => {
            res.status(404).end();
        });

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
                    case 'register_course':
                        doRegisterCourse(req, res, db);
                        break;
                    case 'get_name_title':
                        doGetNameTitle(req, res, db);
                        break;
                    case 'get_organization_type':
                        doGetOrganizationType(req, res, db);
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

                memberData.address = results[0].address;
                memberData.subDistrict = results[0].sub_district;
                memberData.district = results[0].district;
                memberData.province = results[0].province;
                memberData.postalCode = results[0].postal_code;
                memberData.organizationPhone = results[0].organization_phone;
                memberData.taxId = results[0].tax_id;

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
    let inputOrganizationTypeCustom = req.body.organizationTypeCustom;
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
                let address = inputAddress === undefined ? null : inputAddress.trim();
                let subDistrict = inputSubDistrict === undefined ? null : inputSubDistrict.trim();
                let district = inputDistrict === undefined ? null : inputDistrict.trim();
                let province = inputProvince === undefined ? null : inputProvince.trim();
                let postalCode = inputPostalCode === undefined ? null : inputPostalCode.trim();
                let organizationPhone = inputOrganizationPhone === undefined ? null : inputOrganizationPhone.trim();
                let taxId = inputTaxId === undefined ? null : inputTaxId.trim();

                db.query(
                        `INSERT INTO member(title, first_name, last_name, age, job_position, organization_name, organization_type, organization_type_custom, phone, email, password, 
                                            address, sub_district, district, province, postal_code, organization_phone, tax_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [inputTitle.trim(), inputFirstName.trim(), inputLastName.trim(), inputAge, inputJobPosition.trim(), inputOrganizationName.trim(), inputOrganizationType, inputOrganizationTypeCustom.trim(),
                        inputPhone.trim(), inputEmail.trim(), inputPassword.trim(), address, subDistrict, district, province, postalCode, organizationPhone, taxId],

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
    const inputServiceType = req.body.serviceType;
    const inputCourseId = req.body.courseId;

    /*
    * SELECT c.id, c.batch_number, c.details, c.begin_date, c.end_date, c.place, cm.title "
    . " FROM course c INNER JOIN course_master cm ON c.course_master_id = cm.id "
    . " ORDER BY c.begin_date";
    * */

    const selectClause = 'SELECT c.id, c.batch_number, c.details, c.application_fee, c.begin_date, c.end_date, c.place, cm.title, cm.service_type, u.first_name, u.last_name, u.phone_office, u.email FROM course c INNER JOIN course_master cm INNER JOIN user u ON c.course_master_id = cm.id AND c.responsible_user_id = u.id ';
    const whereClause = inputCourseId === undefined ? ' WHERE cm.service_type = ? ' : ' WHERE cm.service_type = ? AND c.id = ? ';
    const orderClause = ' ORDER BY c.begin_date';
    const sql = selectClause + whereClause + orderClause;

    db.query(
        sql,
        inputCourseId === undefined ? [inputServiceType] : [inputServiceType, inputCourseId],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                /*
                * ถ้าจะแปลง js date ไปเป็น mysql date format ใช้ .toISOString().slice(0, 19).replace('T', ' ')
                * */

                let dataList = [];
                results.forEach(row => {
                    dataList.push({
                        id: row.id,
                        serviceType: row.service_type,
                        name: row.title + ' รุ่นที่ ' + row.batch_number,
                        details: row.details,
                        applicationFee: row.application_fee,
                        place: row.place,
                        beginDate: row.begin_date,
                        endDate: row.end_date,
                        responsibleUser: {
                            firstName: row.first_name,
                            lastName: row.last_name,
                            phoneOffice: row.phone_office,
                            email: row.email,
                        },
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

doRegisterCourse = (req, res, db) => {
    const {loginToken, courseId, trainees, coordinator, receipt} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);
    const {
        coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorAge, coordinatorJobPosition,
        coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom, coordinatorPhone, coordinatorEmail
    } = coordinator;
    const {
        receiptAddress, receiptSubDistrict, receiptDistrict, receiptProvince,
        receiptPostalCode, receiptOrganizationPhone, receiptTaxId
    } = receipt;

    db.query(
            `INSERT INTO course_registration (course_id, member_id, coordinator_title, coordinator_first_name, coordinator_last_name, coordinator_age, coordinator_job_position,
                                              coordinator_organization_name, coordinator_organization_type, coordinator_organization_type_custom, coordinator_phone, coordinator_email,
                                              receipt_address, receipt_sub_district, receipt_district, receipt_province, receipt_postal_code, receipt_organization_phone, receipt_tax_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, coordinatorTitle.trim(), coordinatorFirstName.trim(), coordinatorLastName.trim(), coordinatorAge, coordinatorJobPosition.trim(),
            coordinatorOrganizationName.trim(), coordinatorOrganizationType, coordinatorOrganizationTypeCustom === undefined ? coordinatorOrganizationTypeCustom : coordinatorOrganizationTypeCustom.trim(),
            coordinatorPhone.trim(), coordinatorEmail.trim(), receiptAddress.trim(), receiptSubDistrict.trim(), receiptDistrict.trim(), receiptProvince.trim(), receiptPostalCode.trim(),
            receiptOrganizationPhone.trim(), receiptTaxId.trim()],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                });
                console.log(err.stack);
                db.end();
            } else {
                let insertId = results.insertId;

                /*เลขที่ใบสมัคร รูปแบบ 2019-0001*/
                const formNumber = new Date().getFullYear() + '-' + ('000' + insertId).slice(-4);
                db.query(
                        `UPDATE course_registration
                         SET form_number = ?
                         WHERE id = ?`,
                    [formNumber, insertId],

                    function (err, results, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                            console.log(err.stack);
                            db.end();
                        } else {
                            let placeHolder = '';
                            const data = [];
                            for (let i = 0; i < trainees.length; i++) {
                                placeHolder += '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),';

                                const {
                                    traineeTitle, traineeFirstName, traineeLastName, traineeAge, traineeJobPosition,
                                    traineeOrganizationName, traineeOrganizationType, traineeOrganizationTypeCustom, traineePhone, traineeEmail
                                } = trainees[i];

                                const traineeFormNumber = formNumber + '-' + ('000' + (i + 1)).slice(-4);

                                data.push(insertId);
                                data.push(traineeFormNumber);
                                data.push(traineeTitle.trim());
                                data.push(traineeFirstName.trim());
                                data.push(traineeLastName.trim());
                                data.push(traineeAge);
                                data.push(traineeJobPosition.trim());
                                data.push(traineeOrganizationName.trim());
                                data.push(traineeOrganizationType);
                                data.push(traineeOrganizationTypeCustom === undefined ? traineeOrganizationTypeCustom : traineeOrganizationTypeCustom.trim());
                                data.push(traineePhone.trim());
                                data.push(traineeEmail.trim());
                            }
                            placeHolder = placeHolder.substring(0, placeHolder.length - 1);

                            db.query(
                                    `INSERT INTO course_trainee(course_registration_id, form_number, title, first_name, last_name, age, job_position, organization_name, organization_type, organization_type_custom, phone, email)
                                     VALUES ` + placeHolder,
                                data,
                                function (err, results, fields) {
                                    if (err) {
                                        res.send({
                                            error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (3)', 'error run query: ' + err.stack),
                                        });
                                        console.log(err.stack);
                                    } else {
                                        res.send({
                                            error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                                        });
                                    }
                                });
                            db.end();
                        }
                    }
                );
            }
        }
    );
};

doGetNameTitle = (req, res, db) => {
    db.query(
        'SELECT * FROM name_title',
        [],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลคำนำหน้าชื่อ', 'error run query: ' + err.stack),
                });
            } else {
                let dataList = [];
                results.forEach(row => {
                    const {id, title} = row;
                    dataList.push({id, title});
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

doGetOrganizationType = (req, res, db) => {
    db.query(
        'SELECT * FROM organization_type ORDER BY id',
        [],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลประเภทหน่วยงาน', 'error run query: ' + err.stack),
                });
            } else {
                let dataList = [];
                results.forEach(row => {
                    const {id, name} = row;
                    dataList.push({id, name});
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
