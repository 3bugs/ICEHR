/*import {
    SERVICE_TRAINING, SERVICE_DRIVING_LICENSE, SERVICE_SOCIAL,
    SERVICE_PREFIX_TRAINING, SERVICE_PREFIX_DRIVING_LICENSE, SERVICE_PREFIX_SOCIAL
} from "./etc/constants";*/

const constants = require('./etc/constants');

const express = require('express');
const next = require('next');
const mysql = require('mysql');
const dbConfig = require('./db-config');
const jwt = require('jsonwebtoken');
const Error = require('./model/Error');
const fs = require('fs');
const pdf = require("./pdf");

const SECRET_KEY = 'icehr_and_2fellows';
const INVALID_TOKEN = -1;

const dev = process.env.NODE_ENV !== 'production';
const app = next({dev});
const handle = app.getRequestHandler();
const bodyParser = require('body-parser');
const multer = require('multer');

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

        /* ********** บริการอบรมสอบใบขับขี่ ********** */
        server.get('/service-driving-license/:id', (req, res) => {
            const actualPage = '/service-driving-license';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-driving-license-register/:id', (req, res) => {
            const actualPage = '/service-driving-license-register';
            const queryParams = {courseId: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        server.get('/service-driving-license-register', (req, res) => {
            res.status(404).end();
        });

        /*จัดการ POST api call*/
        server.post('/api/:action', (req, res) => {
            /*const actualPage = '/post';
            const queryParams = { title: req.params.id };
            app.render(req, res, actualPage, queryParams);*/

            let db = mysql.createConnection({
                host: dbConfig.HOST,
                user: dbConfig.USER,
                password: dbConfig.PASSWORD,
                database: dbConfig.DATABASE,
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
                    case 'register_course_social':
                        doRegisterCourseSocial(req, res, db);
                        break;
                    case 'register_course_driving_license':
                        doRegisterCourseDrivingLicense(req, res, db);
                        break;
                    case 'register_in_house':
                        doRegisterInHouse(req, res, db);
                        break;
                    case 'get_name_title':
                        doGetNameTitle(req, res, db);
                        break;
                    case 'get_organization_type':
                        doGetOrganizationType(req, res, db);
                        break;
                    case 'get_registration_list_by_member':
                        doGetRegistrationListByMember(req, res, db);
                        break;
                    case 'get_trainee_by_form_number':
                        doGetTraineeByFormNumber(req, res, db);
                        break;
                    case 'add_transfer_notification':
                        doAddTransferNotification(req, res, db);
                        break;
                    default:
                        //res.status(404).end();
                        res.send({
                            error: new Error(1, 'Invalid API endpoint', ''),
                            dataList: null
                        });
                        break;
                }
                //db.end();
            });
        });

        /*จัดการ GET api call*/
        server.get('/api/:action', (req, res) => {
            /*const actualPage = '/post';
            const queryParams = { title: req.params.id };
            app.render(req, res, actualPage, queryParams);*/

            let db = mysql.createConnection({
                host: dbConfig.HOST,
                user: dbConfig.USER,
                password: dbConfig.PASSWORD,
                database: dbConfig.DATABASE,
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
                    case 'get_trainee_form_pdf':
                        doGetTraineeFormPdf(req, res, db);
                        break;
                    default:
                        res.status(404).end();
                        /*res.send({
                            error: new Error(1, 'Invalid API endpoint', ''),
                            dataList: null
                        });*/
                        break;
                }
                //db.end();
            });
        });

        const docDefinition = {
            content: ['This will show up in the file created สวัสดี']
        };

        /////////////////////////////////////////////////////////////////////////////////////
        /*ทดสอบการสร้าง PDF*/
        server.get('/test-pdf', (req, res) => {
            pdf.generateRegistrationFormTrainingPdf(
                'AC-2019-0099',
                {
                    courseTitle: 'การเขียนอีเมลภาษาอังกฤษสู่ความสำเร็จยุค THAILAND 4.0',
                    courseBatchNumber: 15,
                    coursePlace: 'ม.ธรรมศาสตร์ ท่าพระจันทร์ กรุงเทพฯ',
                    courseBeginDate: '2019-05-01',
                    courseEndDate: '2019-05-03',
                    courseDetails: '',
                    courseResponsibleUserId: 1
                },
                [
                    {
                        traineeTitle: 'นาย',
                        traineeFirstName: 'พร้อมเลิศ',
                        traineeLastName: 'หล่อวิจิตร',
                        traineePhone: '085-058-1776',
                        traineeEmail: 'promlert@gmail.com',
                    },
                    {
                        traineeTitle: 'นาง',
                        traineeFirstName: 'จิตริณีย์',
                        traineeLastName: 'หล่อวิจิตร',
                        traineePhone: '086-999-1827',
                        traineeEmail: 'chitrinee_l@gmail.com',
                    },
                    {
                        traineeTitle: 'นาย',
                        traineeFirstName: 'บัลลพ',
                        traineeLastName: 'หล่อวิจิตร',
                        traineePhone: '099-999-9999',
                        traineeEmail: 'banlop2553@gmail.com',
                    },
                ],
                {},
                (success, message) => {
                    res.send(message);
                });
        });
        /////////////////////////////////////////////////////////////////////////////////////

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
        });

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
                memberData.birthDate = results[0].birth_date;
                memberData.jobPosition = results[0].job_position;
                memberData.organizationName = results[0].organization_name;
                memberData.organizationType = results[0].organization_type;
                memberData.organizationTypeCustom = results[0].organization_type_custom;
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
    let inputBirthDate = req.body.birthDate;
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
                let organizationTypeCustom = inputOrganizationTypeCustom == null ? null : inputOrganizationTypeCustom.trim();
                let address = inputAddress == null ? null : inputAddress.trim();
                let subDistrict = inputSubDistrict == null ? null : inputSubDistrict.trim();
                let district = inputDistrict == null ? null : inputDistrict.trim();
                let province = inputProvince == null ? null : inputProvince.trim();
                let postalCode = inputPostalCode == null ? null : inputPostalCode.trim();
                let organizationPhone = inputOrganizationPhone == null ? null : inputOrganizationPhone.trim();
                let taxId = inputTaxId == null ? null : inputTaxId.trim();

                db.query(
                        `INSERT INTO member(title, first_name, last_name, birth_date, job_position, organization_name, organization_type, organization_type_custom, phone, email, password,
                                            address, sub_district, district, province, postal_code, organization_phone, tax_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [inputTitle.trim(), inputFirstName.trim(), inputLastName.trim(), inputBirthDate, inputJobPosition.trim(), inputOrganizationName.trim(), inputOrganizationType, organizationTypeCustom,
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
    const inputMonth = req.body.month;
    const inputYear = req.body.year;

    /*
    * SELECT c.id, c.batch_number, c.details, c.begin_date, c.end_date, c.place, cm.title "
    . " FROM course c INNER JOIN course_master cm ON c.course_master_id = cm.id "
    . " ORDER BY c.begin_date";
    * */

    const selectClause = 'SELECT c.id, c.batch_number, c.details, c.application_fee, c.begin_date, c.end_date, c.place, cm.title, cm.service_type, u.first_name, u.last_name, u.phone_office, u.email FROM course c INNER JOIN course_master cm INNER JOIN user u ON c.course_master_id = cm.id AND c.responsible_user_id = u.id ';
    let whereClause = ' WHERE cm.service_type = ? ';

    if (inputCourseId !== undefined) {
        whereClause += ' AND c.id = ? ';
    }

    const monthString = ('0' + inputMonth).slice(-2);
    const yearString = String(inputYear);
    const monthYearString = `${yearString}-${monthString}-%`;

    if (inputMonth !== undefined && inputYear !== undefined) {
        whereClause += ' AND c.begin_date LIKE ? ';
    }

    const orderClause = ' ORDER BY c.begin_date';
    const sql = selectClause + whereClause + orderClause;

    db.query(
        sql,
        inputCourseId === undefined ? ((inputMonth === undefined || inputYear === undefined) ? [inputServiceType] : [inputServiceType, monthYearString]) : [inputServiceType, inputCourseId],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                /*
                * ถ้าจะแปลง js date ไปเป็น mysql date format ใช้ .toISOString().slice(0, 19).replace('T', ' ')
                * */

                const dataList = [];
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
                    sql,
                    monthYearString,
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
    let {
        coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorBirthDate, coordinatorJobPosition,
        coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom, coordinatorPhone, coordinatorEmail
    } = coordinator;
    const {
        receiptAddress, receiptSubDistrict, receiptDistrict, receiptProvince,
        receiptPostalCode, receiptOrganizationPhone, receiptTaxId
    } = receipt;

    coordinatorTitle = coordinatorTitle == null ? null : coordinatorTitle.trim();
    coordinatorFirstName = coordinatorFirstName == null ? null : coordinatorFirstName.trim();
    coordinatorLastName = coordinatorLastName == null ? null : coordinatorLastName.trim();
    coordinatorJobPosition = coordinatorJobPosition == null ? null : coordinatorJobPosition.trim();
    coordinatorOrganizationName = coordinatorOrganizationName == null ? null : coordinatorOrganizationName.trim();
    coordinatorOrganizationTypeCustom = coordinatorOrganizationTypeCustom == null ? null : coordinatorOrganizationTypeCustom.trim();
    coordinatorPhone = coordinatorPhone == null ? null : coordinatorPhone.trim();
    coordinatorEmail = coordinatorEmail == null ? null : coordinatorEmail.trim();

    db.query(
            `INSERT INTO course_registration (course_id, member_id, coordinator_title, coordinator_first_name, coordinator_last_name, coordinator_birth_date, coordinator_job_position,
                                              coordinator_organization_name, coordinator_organization_type, coordinator_organization_type_custom, coordinator_phone, coordinator_email,
                                              receipt_address, receipt_sub_district, receipt_district, receipt_province, receipt_postal_code, receipt_organization_phone, receipt_tax_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorBirthDate, coordinatorJobPosition,
            coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom,
            coordinatorPhone, coordinatorEmail, receiptAddress.trim(), receiptSubDistrict.trim(), receiptDistrict.trim(), receiptProvince.trim(),
            receiptPostalCode.trim(), receiptOrganizationPhone.trim(), receiptTaxId.trim()],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                });
                console.log(err.stack);
                db.end();
            } else {
                let insertCourseRegId = results.insertId;

                /*เลขที่ใบสมัคร รูปแบบ AC-2019-0001*/
                const formNumber = constants.SERVICE_PREFIX_TRAINING + '-' + new Date().getFullYear() + '-' + ('000' + insertCourseRegId).slice(-4);
                db.query(
                        `UPDATE course_registration
                         SET form_number = ?
                         WHERE id = ?`,
                        [formNumber, insertCourseRegId],

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
                                    traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate, traineeJobPosition,
                                    traineeOrganizationName, traineeOrganizationType, traineeOrganizationTypeCustom, traineePhone, traineeEmail
                                } = trainees[i];

                                const traineeFormNumber = formNumber + '-' + ('000' + (i + 1)).slice(-4);
                                trainees[i].traineeFormNumber = traineeFormNumber;

                                //console.log(traineeFirstName);

                                data.push(insertCourseRegId);
                                data.push(traineeFormNumber);
                                data.push(traineeTitle.trim());
                                data.push(traineeFirstName.trim());
                                data.push(traineeLastName.trim());
                                data.push(traineeBirthDate);
                                data.push(traineeJobPosition.trim());
                                data.push(traineeOrganizationName.trim());
                                data.push(traineeOrganizationType);
                                data.push(traineeOrganizationTypeCustom == null ? null : traineeOrganizationTypeCustom.trim());
                                data.push(traineePhone.trim());
                                data.push(traineeEmail.trim());
                            }
                            placeHolder = placeHolder.substring(0, placeHolder.length - 1);

                            db.query(
                                    `INSERT INTO course_trainee(course_registration_id, form_number, title, first_name, last_name, birth_date, job_position, organization_name, organization_type,
                                                                organization_type_custom, phone, email)
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
                                            courseRegId: insertCourseRegId,
                                        });

                                        // สร้างอาร์เรย์ของหมายเลขใบสมัครแต่ละคน
                                        /*const traineeFormNumberList = [];
                                        for (let i = 0; i < trainees.length; i++) {
                                            const trainee = trainees[i];
                                            traineeFormNumberList.push(trainee.traineeFormNumber);
                                        }

                                        pdf.generateTraineeForm_Training(traineeFormNumberList, (success, message) => {
                                            if (success) {
                                                res.send({
                                                    error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                                                });
                                            } else {
                                                res.send({
                                                    error: new Error(1, message, ''),
                                                });
                                            }
                                        });*/
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

doRegisterCourseSocial = (req, res, db) => {
    const {loginToken, courseId, traineeData} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);

    const {
        traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate, traineeOccupation, traineeWorkPlace, traineeAddress, traineeSubDistrict, traineeDistrict,
        traineeProvince, traineePostalCode, traineePhone, traineeEmail, traineeContactPersonName, traineeContactPersonPhone, traineeDisease,
        traineeNewsSourceWeb, traineeNewsSourceEmail, traineeNewsSourceBrochure, traineeNewsSourceOnline, traineeNewsSourceMouth
    } = traineeData;

    /*ใช้แต่ละ bit เก็บค่า news source แต่ละค่า*/
    const newsSource = (traineeNewsSourceWeb ? 1 : 0) + (traineeNewsSourceEmail ? 2 : 0) + (traineeNewsSourceBrochure ? 4 : 0)
        + (traineeNewsSourceOnline ? 8 : 0) + (traineeNewsSourceMouth ? 16 : 0);

    /*แปลงกลับเป็น binary string ใช้ number.toString(2)*/

    db.query(
            `INSERT INTO course_registration_social (course_id, member_id, title, first_name, last_name, birth_date, occupation, work_place, address, sub_district, district, province, postal_code,
                                                     phone, email, contact_name, contact_phone, disease, news_source)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate, traineeOccupation, traineeWorkPlace, traineeAddress, traineeSubDistrict, traineeDistrict,
            traineeProvince, traineePostalCode, traineePhone, traineeEmail, traineeContactPersonName, traineeContactPersonPhone, traineeDisease, newsSource],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                });
                console.log(err.stack);
                db.end();
            } else {
                let insertId = results.insertId;

                /*เลขที่ใบสมัคร รูปแบบ SO-2019-0001*/
                const formNumber = constants.SERVICE_PREFIX_SOCIAL + '-' + new Date().getFullYear() + '-' + ('000' + insertId).slice(-4);
                db.query(
                        `UPDATE course_registration_social
                         SET form_number = ?
                         WHERE id = ?`,
                    [formNumber, insertId],

                    function (err, results, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                            console.log(err.stack);
                        } else {
                            res.send({
                                error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                            });
                        }
                    }
                );
                db.end();
            }
        }
    );
};

doRegisterCourseDrivingLicense = (req, res, db) => {
    const {loginToken, courseId, traineeData} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);

    const {
        traineeTitle, traineeFirstName, traineeLastName, traineePid, traineeAddress, traineeSubDistrict, traineeDistrict, traineeProvince, traineePostalCode,
        traineePhone, traineeSelectedCourseType, traineeSelectedLicenseTypeCar, traineeSelectedLicenseTypeBicycle, traineeSelectedLicenseTypeTricycle
    } = traineeData;

    /*ใช้แต่ละ bit เก็บค่า license type แต่ละค่า (user สามารถเลือกได้มากกว่า 1 license)*/
    const licenseType = (traineeSelectedLicenseTypeCar ? 1 : 0) + (traineeSelectedLicenseTypeBicycle ? 2 : 0) + (traineeSelectedLicenseTypeTricycle ? 4 : 0);

    /*แปลงกลับเป็น binary string ใช้ number.toString(2)*/

    db.query(
            `INSERT INTO course_registration_driving_license
             (course_id, member_id, title, first_name, last_name, pid, address, sub_district, district,
              province, postal_code, phone, course_type, license_type)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, traineeTitle, traineeFirstName, traineeLastName, traineePid, traineeAddress, traineeSubDistrict, traineeDistrict,
            traineeProvince, traineePostalCode, traineePhone, traineeSelectedCourseType, licenseType],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                });
                console.log(err.stack);
                db.end();
            } else {
                let insertId = results.insertId;

                /*เลขที่ใบสมัคร รูปแบบ DL-2019-0001*/
                const formNumber = constants.SERVICE_PREFIX_DRIVING_LICENSE + '-' + new Date().getFullYear() + '-' + ('000' + insertId).slice(-4);
                db.query(
                        `UPDATE course_registration_driving_license
                         SET form_number = ?
                         WHERE id = ?`,
                    [formNumber, insertId],

                    function (err, results, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                            console.log(err.stack);
                        } else {
                            res.send({
                                error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                            });
                        }
                    }
                );
                db.end();
            }
        }
    );
};

doRegisterInHouse = (req, res, db) => {
    const {loginToken, formData} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);

    const {
        fieldTitle, fieldFirstName, fieldLastName, fieldOrganizationName, fieldPhone, fieldEmail,
        fieldCourse, fieldNumDay, fieldNumTrainee, fieldPlace, fieldRemark,
    } = formData;

    db.query(
        `INSERT INTO in_house (member_id, title, first_name, last_name, organization_name, phone, email, course, num_day, num_trainee, place, remark)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [memberId, fieldTitle, fieldFirstName, fieldLastName, fieldOrganizationName, fieldPhone, fieldEmail, fieldCourse, fieldNumDay, fieldNumTrainee, fieldPlace, fieldRemark],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error run query: ' + err.stack),
                });
                console.log(err.stack);
                db.end();
            } else {
                res.send({
                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ', ''),
                });
                db.end();
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

doGetRegistrationListByMember = (req, res, db) => {
    const {memberId} = req.body;

    db.query(
            `SELECT cm.title       AS courseTitle,
                    c.batch_number AS courseBatchNumber,
                    cr.id          AS registrationId,
                    cr.course_id   AS courseId,
                    cr.form_number AS registrationFormNumber,
                    cr.created_at  AS registrationDateTime,
                    ct.id          AS traineeId,
                    ct.form_number AS traineeFormNumber,
                    ct.first_name  AS traineeFirstName,
                    ct.last_name   AS traineeLastName
             FROM course_registration cr
                      INNER JOIN course_trainee ct
                                 ON ct.course_registration_id = cr.id
                      INNER JOIN course c
                                 ON cr.course_id = c.id
                      INNER JOIN course_master cm
                                 ON c.course_master_id = cm.id
             WHERE cr.member_id = ?
             ORDER BY registrationId DESC, traineeId`,
        [memberId],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
            } else {
                const dataList = [];
                let reg;
                let previousRegistrationId = 0;
                results.forEach(row => {
                    const {
                        courseId, courseTitle, courseBatchNumber,
                        registrationId, registrationFormNumber, registrationDateTime,
                        traineeId, traineeFormNumber, traineeFirstName, traineeLastName
                    } = row;

                    if (registrationId !== previousRegistrationId) {
                        previousRegistrationId = registrationId;

                        reg = {
                            courseId, courseTitle, courseBatchNumber,
                            registrationId, registrationFormNumber, registrationDateTime,
                            traineeList: [{
                                traineeId, traineeFormNumber, traineeFirstName, traineeLastName
                            }],
                        };
                        dataList.push(reg);
                    } else {
                        reg.traineeList.push({
                            traineeId, traineeFormNumber, traineeFirstName, traineeLastName
                        });
                    }
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

doGetTraineeByFormNumber = (req, res, db) => {
    const {formNumber} = req.body;

    db.query( // หาในบริการวิชาการก่อน
            `SELECT cm.title           AS courseTitle,
                    c.batch_number     AS courseBatchNumber,
                    c.place            AS coursePlace,
                    c.begin_date       AS courseBeginDate,
                    c.end_date         AS courseEndDate,
                    ct.form_number     AS formNumber,
                    ct.id              AS traineeId,
                    ct.title           AS traineeTitle,
                    ct.first_name      AS traineeFirstName,
                    ct.last_name       AS traineeLastName,
                    ct.register_status AS registerStatus
             FROM course_registration cr
                      INNER JOIN course_trainee ct
                                 ON ct.course_registration_id = cr.id
                      INNER JOIN course c
                                 ON cr.course_id = c.id
                      INNER JOIN course_master cm
                                 ON c.course_master_id = cm.id
             WHERE ct.form_number = ?`,
        [formNumber],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                if (results.length > 0) {
                    const {
                        courseTitle, courseBatchNumber, coursePlace, courseBeginDate, courseEndDate,
                        formNumber, traineeId, traineeTitle, traineeFirstName, traineeLastName, registerStatus
                    } = results[0];

                    res.send({
                        error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                        data: {
                            course: {
                                title: courseTitle,
                                batchNumber: courseBatchNumber,
                                name: `${courseTitle} รุ่นที่ ${courseBatchNumber}`,
                                beginDate: courseBeginDate,
                                endDate: courseEndDate,
                                place: coursePlace,
                            },
                            trainee: {
                                id: traineeId,
                                title: traineeTitle,
                                firstName: traineeFirstName,
                                lastName: traineeLastName,
                            },
                            formNumber,
                            registerStatus,
                        }
                    });
                    db.end();
                } else {
                    db.query( // ถ้าหาในบริการวิชาการไม่เจอ ก็ไปหาในใบขับขี่ต่อ
                            `SELECT cm.title             AS courseTitle,
                                    c.batch_number       AS courseBatchNumber,
                                    c.place              AS coursePlace,
                                    c.begin_date         AS courseBeginDate,
                                    c.end_date           AS courseEndDate,
                                    ct.title             AS courseType,
                                    crdl.form_number     AS formNumber,
                                    crdl.id              AS traineeId,
                                    crdl.title           AS traineeTitle,
                                    crdl.first_name      AS traineeFirstName,
                                    crdl.last_name       AS traineeLastName,
                                    crdl.register_status AS registerStatus
                             FROM course_registration_driving_license crdl
                                      INNER JOIN course c
                                                 ON crdl.course_id = c.id
                                      INNER JOIN course_master cm
                                                 ON c.course_master_id = cm.id
                                      INNER JOIN driving_license_course_type ct
                                                 ON crdl.course_type = ct.id
                             WHERE crdl.form_number = ?`,
                        [formNumber],
                        function (err, results, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                            } else {
                                if (results.length > 0) {
                                    const {
                                        courseTitle, courseBatchNumber, coursePlace, courseBeginDate, courseEndDate,
                                        formNumber, traineeId, traineeTitle, traineeFirstName, traineeLastName, courseType, registerStatus
                                    } = results[0];

                                    res.send({
                                        error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                        data: {
                                            course: {
                                                title: courseTitle,
                                                batchNumber: courseBatchNumber,
                                                name: `${courseTitle}`,
                                                beginDate: courseBeginDate,
                                                endDate: courseEndDate,
                                                place: coursePlace,
                                                type: courseType,
                                            },
                                            trainee: {
                                                id: traineeId,
                                                title: traineeTitle,
                                                firstName: traineeFirstName,
                                                lastName: traineeLastName,
                                            },
                                            formNumber,
                                            registerStatus,
                                        }
                                    });
                                } else {
                                    res.send({
                                        error: new Error(1, `ไม่พบข้อมูลใบสมัครเลขที่ ${formNumber}`, ''),
                                    });
                                }
                            }
                        }
                    );
                    db.end();
                }
            }
        }
    );
};

const slipImageStorage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'backend/uploads/slip_images')
    },
    filename: function (req, file, cb) {
        const fileName = Date.now() + '-' + file.originalname;
        cb(null, fileName);
    }
});

const upload = multer({storage: slipImageStorage}).single('file');

doAddTransferNotification = (req, res, db) => {
    upload(req, res, function (err) {
        if (err instanceof multer.MulterError) {
            res.send({
                error: new Error(1, 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์', ''),
            });
            return;
        } else if (err) {
            res.send({
                error: new Error(1, 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์', ''),
            });
            return;
        }

        const {formNumber, memberId, traineeId, amount, transferDate} = req.body;
        const serviceTypePrefix = formNumber.substring(0, 2).toUpperCase();
        let serviceType = serviceTypePrefix === constants.SERVICE_PREFIX_TRAINING // AC
            ? constants.SERVICE_TRAINING
            : (serviceTypePrefix === constants.SERVICE_PREFIX_DRIVING_LICENSE // DL
                ? constants.SERVICE_DRIVING_LICENSE
                : null);
        const {filename} = req.file;
        db.query(
                `INSERT INTO payment_notification
                     (member_id, trainee_id, service_type, amount, transfer_date, slip_file_name)
                 VALUES (?, ?, ?, ?, ?, ?)`,
            [memberId, traineeId, serviceType, amount, transferDate, filename],
            function (err, results, fields) {
                if (err) {
                    res.send({
                        error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                    });
                    db.end();
                } else {
                    db.query(
                        serviceType === constants.SERVICE_TRAINING ?
                                `UPDATE course_trainee
                                 SET register_status = 'wait-approve'
                                 WHERE id = ?
                                   AND register_status <> 'complete'` :
                            (serviceType === constants.SERVICE_DRIVING_LICENSE ?
                                    `UPDATE course_registration_driving_license
                                     SET register_status = 'wait-approve'
                                     WHERE id = ?
                                       AND register_status <> 'complete'` : ''),
                        [traineeId],
                        function (err, results, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                            } else {
                                res.send({
                                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ', ''),
                                    debug: `ชื่อไฟล์ ${filename}, ยอดโอน ${amount} บาท, โอนเมื่อ ${transferDate}`,
                                });
                            }
                        }
                    );
                    db.end();
                }
            }
        );
    })
};

/*doGetRegistrationListByMember = (req, res, db) => {
    const {memberId} = req.body;

    db.query(
            `SELECT cm.title       AS courseTitle,
                    c.batch_number AS courseBatchNumber,
                    cr.id          AS registrationId,
                    cr.form_number AS registrationFormNumber,
                    cr.created_at  AS registrationDateTime
             FROM course c
                      INNER JOIN course_registration cr
                                 ON cr.course_id = c.id
                      INNER JOIN course_master cm
                                 ON c.course_master_id = cm.id
             WHERE cr.member_id = ?`,
        [memberId],
        function (err, results, fields) {
            let courseName = '';
            if (err) {
                db.end();
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
            } else {
                const dataList = [];
                results.forEach(row => {
                    const {
                        courseTitle, courseBatchNumber,
                        registrationId, registrationFormNumber, registrationDateTime,
                    } = row;

                    const reg = {
                        courseTitle, courseBatchNumber,
                        registrationId, registrationFormNumber, registrationDateTime,
                        traineeList: [],
                    };
                    dataList.push(reg);

                    db.query(
                            `SELECT ct.id          AS traineeId,
                                    ct.form_number AS traineeFormNumber,
                                    ct.first_name  AS traineeFirstName,
                                    ct.last_name   AS traineeLastName
                             FROM course_registration cr
                                      INNER JOIN course_trainee ct
                                                 ON ct.course_registration_id = cr.id
                             WHERE cr.id = ?`,
                        [registrationId],
                        function (err, results, fields) {
                            let courseName = '';
                            if (err) {
                                db.end();
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                            } else {
                                results.forEach(row => {
                                   reg.traineeList.push(row);
                                });
                            }
                        }
                    );
                });
                db.end();

                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    dataList
                });
            }
        }
    );
};*/

doGetTraineeFormPdf = (req, res, db) => {
    //const {courseRegId} = req.body;

    /*traineeId ไว้สำหรับสร้าง PDF รายบุคคลที่หลังบ้าน*/
    const {courseRegId, traineeId, download} = req.query;

    db.query(
            `SELECT cr.form_number AS regFormNumber, ct.form_number
             FROM course_registration cr
                      INNER JOIN course_trainee ct
                                 ON ct.course_registration_id = cr.id
             WHERE cr.id = ?`,
        [courseRegId],

        function (err, results, fields) {
            if (err) {
                res.status(500).end();
                console.log(err.stack);
            } else {
                const {regFormNumber} = results[0];
                const traineeFormNumberList = [];
                results.forEach(row => {
                    traineeFormNumberList.push(row.form_number);
                });

                const pdfFilePath = `${__dirname}/static/public/${regFormNumber}.pdf`;

                if (download && fs.existsSync(pdfFilePath)) {
                    //มี pdf แล้ว
                    console.log(`${pdfFilePath} exists.`);
                    res.download(pdfFilePath, `${regFormNumber}.pdf`, err => {
                        console.log('Error download.');
                    });
                } else {
                    pdf.generateTraineeForm_Training(traineeFormNumberList, download, (success, result) => {
                        if (success) {
                            if (download) {
                                res.download(pdfFilePath, `${regFormNumber}.pdf`, err => {
                                    console.log('Error download.');
                                });
                            } else {
                                res.setHeader('Content-Type', 'application/pdf');
                                res.send(result);
                            }
                        } else {
                            res.status(500).end();
                        }
                    });
                }
            }
        }
    );
    db.end();
};
