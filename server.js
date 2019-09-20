/*import {
    SERVICE_TRAINING, SERVICE_DRIVING_LICENSE, SERVICE_SOCIAL,
    SERVICE_PREFIX_TRAINING, SERVICE_PREFIX_DRIVING_LICENSE, SERVICE_PREFIX_SOCIAL
} from "./etc/constants";*/

//https://medium.com/@mhagemann/create-a-mysql-database-middleware-with-node-js-8-and-async-await-6984a09d49f4

const constants = require('./etc/constants');
const utils = require('./etc/utils');

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
const http = require('http');
const https = require('https');

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

        /* ********** งานวิจัยและวิชาการ ********** */
        server.get('/academic-paper/:id', (req, res) => {
            const actualPage = '/academic-paper';
            const queryParams = {id: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        /* ********** เอกสารดาวน์โหลด ********** */
        server.get('/document-download/:type', (req, res) => {
            const actualPage = '/document-download';

            /*const documentTypeList = {
                training: 'training_documents',
                report: 'performance_report',
                km: 'km',
                qa: 'qa_quality_assurance',
                booklet: 'pamphlet/infographic',
            };
            const queryParams = {documentType: documentTypeList[req.params.type]};*/

            const queryParams = {documentType: req.params.type};
            app.render(req, res, actualPage, queryParams)
        });

        /* ********** ข่าว/กิจกรรม/หลักสูตร In-House ที่ผ่านมา ********** */
        server.get('/news/:id', (req, res) => {
            const actualPage = '/news';
            const queryParams = {id: req.params.id};
            app.render(req, res, actualPage, queryParams)
        });

        /*จัดการ POST api call*/
        server.post('/api/:action', (req, res) => {
            /*const actualPage = '/post';
            const queryParams = { title: req.params.id };
            app.render(req, res, actualPage, queryParams);*/

            let db = mysql.createConnection({
                host: dbConfig.HOST,
                port: dbConfig.PORT,
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
                    case 'search_course':
                        doSearchCourse(req, res, db);
                        break;
                    case 'register_course':
                        registerCourse(req, res, db, constants.SERVICE_TRAINING);
                        break;
                    case 'register_course_social':
                        registerCourse(req, res, db, constants.SERVICE_SOCIAL);
                        //doRegisterCourseSocial(req, res, db);
                        break;
                    case 'register_course_driving_license':
                        registerCourseDrivingLicense(req, res, db);
                        //registerCourse(req, res, db, constants.SERVICE_DRIVING_LICENSE);
                        //doRegisterCourseDrivingLicense(req, res, db);
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
                    case 'get_academic_paper':
                        doGetAcademicPaper(req, res, db);
                        break;
                    case 'search_academic_paper':
                        doSearchAcademicPaper(req, res, db);
                        break;
                    case 'add_academic_paper_download':
                        doAddAcademicPaperDownload(req, res, db);
                        break;
                    case 'get_training_course_category':
                        doGetTrainingCourseCategory(req, res, db);
                        break;
                    case 'get_driving_license_course_type':
                        doGetDrivingLicenseCourseType(req, res, db);
                        break;
                    case 'get_document_download':
                        doGetDocumentDownload(req, res, db);
                        break;
                    case 'update_document_download_count':
                        doUpdateDocumentDownloadCount(req, res, db);
                        break;
                    case 'get_news_latest':
                        doGetNewsLatest(req, res, db);
                        break;
                    case 'get_in_house_latest':
                        doGetInHouseLatest(req, res, db);
                        break;
                    case 'get_news_by_id':
                        doGetNewsById(req, res, db);
                        break;
                    case 'get_activity':
                        doGetActivity(req, res, db);
                        break;
                    case 'get_faq':
                        doGetFaq(req, res, db);
                        break;
                    case 'get_course_num_trainee_available':
                        doGetCourseNumTraineeAvailable(req, res, db);
                        break;
                    case 'get_intro':
                        doGetIntro(req, res, db);
                        break;
                    case 'get_service':
                        doGetService(req, res, db);
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
    let inputMemberType = req.body.memberType;
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
                let jobPosition = inputJobPosition == null ? null : inputJobPosition.trim();
                let organizationName = inputOrganizationName == null ? null : inputOrganizationName.trim();
                let organizationTypeCustom = inputOrganizationTypeCustom == null ? null : inputOrganizationTypeCustom.trim();
                let address = inputAddress == null ? null : inputAddress.trim();
                let subDistrict = inputSubDistrict == null ? null : inputSubDistrict.trim();
                let district = inputDistrict == null ? null : inputDistrict.trim();
                let province = inputProvince == null ? null : inputProvince.trim();
                let postalCode = inputPostalCode == null ? null : inputPostalCode.trim();
                let organizationPhone = inputOrganizationPhone == null ? null : inputOrganizationPhone.trim();
                let taxId = inputTaxId == null ? null : inputTaxId.trim();

                db.query(
                        `INSERT INTO member(member_type, title, first_name, last_name, birth_date, job_position, organization_name, organization_type, organization_type_custom, phone, email, password,
                                            address, sub_district, district, province, postal_code, organization_phone, tax_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [inputMemberType, inputTitle.trim(), inputFirstName.trim(), inputLastName.trim(), inputBirthDate, jobPosition, organizationName, inputOrganizationType, organizationTypeCustom,
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
    const inputOffset = req.body.offset;
    const inputLimit = req.body.limit;

    /*
    * SELECT c.id, c.batch_number, c.details, c.begin_date, c.end_date, c.place, cm.title "
    . " FROM course c INNER JOIN course_master cm ON c.course_master_id = cm.id "
    . " ORDER BY c.begin_date";
    * */

    const selectClause = `SELECT c.id,
                                 c.batch_number,
                                 c.details,
                                 c.application_fee,
                                 c.begin_date,
                                 c.end_date,
                                 c.place,
                                 c.trainee_limit,
                                 cm.title,
                                 cm.service_type,
                                 u.first_name,
                                 u.last_name,
                                 u.phone_office,
                                 u.email
                          FROM course c
                                   INNER JOIN course_master cm
                                              ON c.course_master_id = cm.id
                                   INNER JOIN user u
                                              ON c.responsible_user_id = u.id`;
    let whereClause = " WHERE c.status = 'normal' AND cm.service_type = ? ";

    if (inputCourseId == null) {
        if ((inputMonth == null || inputYear == null)) {
            whereClause += ' AND c.begin_date >= ? ';
        }
    } else {
        whereClause += ' AND c.id = ? ';
    }

    const monthString = ('0' + inputMonth).slice(-2);
    const yearString = String(inputYear);
    const monthYearString = `${yearString}-${monthString}-%`;

    if (inputMonth != null && inputYear != null) {
        whereClause += ' AND c.begin_date LIKE ? ';
    }

    const orderClause = ' ORDER BY c.begin_date ';
    const limitClause = (inputOffset == null || inputLimit == null) ? '' : ` LIMIT ${inputOffset}, ${inputLimit} `;
    const sql = selectClause + whereClause + orderClause + limitClause;

    const today = utils.getDateFormatFromDateObject(new Date());

    db.query(
        sql,
        inputCourseId == null
            ? ((inputMonth == null || inputYear == null) ? [inputServiceType, today] : [inputServiceType, monthYearString])
            : [inputServiceType, inputCourseId],

        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                /*
                * ถ้าจะแปลง js date ไปเป็น mysql date format ใช้ .toISOString().slice(0, 19).replace('T', ' ')
                * */

                const dataList = [];

                /////////////////////////////////////////////////////////////////////////////////////////////////////
                if (inputCourseId == null) { // course list
                    results.forEach(row => {
                        checkIfCourseFull(db, row.id, row.service_type, (isCourseFull, regCount) => {
                            pushCourseIntoDataList(dataList, row, isCourseFull, regCount);
                        });
                    });
                } else { // course details
                    results.forEach(row => {
                        pushCourseIntoDataList(dataList, row, null, null);
                    });
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////////

                /*if (dataList.length === 0) {
                    res.send({
                        error: new Error(1, 'dataList.length === 0 !!!!!', null),
                    });
                    db.end();
                    return;
                }*/

                /*ถ้าเป็นหน้า course details จะ query ตารางราคา, assets ด้วย*/
                if (dataList.length > 0 && inputCourseId != null) {
                    db.query(
                            `SELECT title, file_name, type, created_at
                             FROM course_asset
                             WHERE course_id = ?`,
                        [inputCourseId],
                        function (err, results, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                                db.end();
                            } else {
                                const assetList = [];
                                results.forEach(row => {
                                    const fileName = row.file_name;
                                    let prefixPosition = fileName.indexOf('-');
                                    let extensionPosition = fileName.lastIndexOf('.');
                                    let title = fileName.substring(prefixPosition + 1, extensionPosition);

                                    assetList.push({
                                        title: title, //row.title
                                        fileName: fileName,
                                        type: row.type,
                                        createdAt: row.created_at,
                                    });
                                });

                                dataList[0].assets = assetList;

                                db.query(
                                        `SELECT title, amount, created_at
                                         FROM course_fee
                                         WHERE course_id = ?`,
                                    [inputCourseId],
                                    function (err, results, fields) {
                                        if (err) {
                                            res.send({
                                                error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (3)', 'error run query: ' + err.stack),
                                            });
                                            db.end();

                                        } else {
                                            const feeList = [];
                                            results.forEach(row => {
                                                feeList.push({
                                                    title: row.title,
                                                    amount: row.amount,
                                                    createdAt: row.created_at,
                                                });
                                            });

                                            dataList[0].fees = feeList;

                                            checkIfCourseFull(db, dataList[0].id, dataList[0].serviceType, (isCourseFull, regCount) => {
                                                dataList[0].isCourseFull = isCourseFull;
                                                dataList[0].regCount = regCount;

                                                res.send({
                                                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                                    //sql,
                                                    monthYearString,
                                                    dataList
                                                });
                                                db.end();
                                            });
                                        }
                                    }
                                );
                                //db.end();
                            }
                        }
                    );
                    //db.end();
                } else if (inputCourseId == null) {
                    db.query(
                            `SELECT COUNT(*) AS totalCount
                             FROM course c
                                      INNER JOIN course_master cm
                                                 ON c.course_master_id = cm.id
                             WHERE cm.service_type = ? ` + ((inputMonth == null || inputYear == null) ? ' AND c.begin_date >= ? ' : ''),
                        (inputMonth == null || inputYear == null) ? [inputServiceType, today] : [inputServiceType],
                        function (err, totalCountResults, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (4)', 'error run query: ' + err.stack),
                                });
                            } else {
                                res.send({
                                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                    dataList,
                                    totalCount: totalCountResults[0].totalCount,
                                });
                            }
                        }
                    );
                    db.end();
                }
            }
        }
    );
};

pushCourseIntoDataList = (dataList, row, isCourseFull, regCount) => {
    dataList.push({
        id: row.id,
        serviceType: row.service_type,
        name: row.title + (row.service_type !== constants.SERVICE_DRIVING_LICENSE ? ' รุ่นที่ ' + row.batch_number : ''),
        details: row.details,
        applicationFee: row.application_fee,
        place: row.place,
        beginDate: row.begin_date,
        endDate: row.end_date,
        traineeLimit: row.trainee_limit,
        responsibleUser: {
            firstName: row.first_name,
            lastName: row.last_name,
            phoneOffice: row.phone_office,
            email: row.email,
        },
        createdAt: row.created_at,
        isCourseFull: isCourseFull,
        regCount: regCount,
    });
};

checkIfCourseFull = (db, courseId, serviceType, callback) => {
    let sql = null;

    switch (serviceType) {
        case constants.SERVICE_TRAINING:
            sql = `SELECT c.trainee_limit
                   FROM course c
                            INNER JOIN course_registration cr
                                       ON c.id = cr.course_id
                            INNER JOIN course_trainee ct
                                       ON cr.id = ct.course_registration_id
                   WHERE c.id = ?`;
            break;
        case constants.SERVICE_SOCIAL:
            sql = `SELECT c.trainee_limit
                   FROM course c
                            INNER JOIN course_registration_social cr
                                       ON c.id = cr.course_id
                   WHERE c.id = ?`;
            break;
        case constants.SERVICE_DRIVING_LICENSE:
            sql = `SELECT c.trainee_limit
                   FROM course c
                            INNER JOIN course_registration_driving_license cr
                                       ON c.id = cr.course_id
                   WHERE c.id = ?`;
            break;
    }

    db.query(
        sql,
        [courseId],
        function (err, results, fields) {
            if (err) {
                callback(false, null); // ถ้าเกิด error ให้ return ว่า course ยังไม่เต็มไปก่อน
            } else {
                if (results.length > 0) {
                    const traineeLimit = results[0].trainee_limit;
                    const regCount = results.length;
                    if (regCount >= traineeLimit) {
                        callback(true, regCount); // เต็มแล้ว
                    } else {
                        callback(false, regCount); // ยังไม่เต็ม
                    }
                } else {
                    callback(false, 0); // ถ้า query แล้วไม่ได้ result เลย แสดงว่ายังไม่มีคนสมัครคอร์สนั้น
                }
            }
        }
    );
};

doSearchCourse = (req, res, db) => {
    const {serviceType} = req.body;
    let {searchTitle, searchCategory, searchPlaceType, searchMonth, searchYear} = req.body.searchFields;
    if (searchTitle != null) {
        if (searchTitle.trim() === '') {
            searchTitle = null;
        }
    }

    const whereServiceType = 'cm.service_type = ?'
    let whereTitle = 'TRUE';
    let whereCategory = 'TRUE';
    let wherePlaceType = 'TRUE';
    let whereMonth = 'TRUE';
    let whereYear = 'TRUE';

    const searchValueArray = [];
    searchValueArray.push(serviceType);

    if (searchTitle) {
        whereTitle = 'cm.title LIKE ?';
        searchValueArray.push(`%${searchTitle}%`);
    }
    if (searchCategory && searchCategory !== '9999') {
        whereCategory = 'cm.category = ?';
        searchValueArray.push(`${searchCategory}`);
    }
    if (searchPlaceType && searchPlaceType !== '9999') {
        wherePlaceType = 'c.place_type = ?';
        searchValueArray.push(`${searchPlaceType}`);
    }
    if (searchMonth && searchMonth !== '9999') {
        whereMonth = 'MONTH(c.begin_date) = ?';
        searchValueArray.push(`${searchMonth}`);
    }
    if (searchYear && searchYear !== '9999') {
        whereYear = 'YEAR(c.begin_date) = ?';
        searchValueArray.push(`${searchYear}`);
    }

    db.query(
        `SELECT c.id,
                    c.batch_number,
                    c.details,
                    c.application_fee,
                    c.begin_date,
                    c.end_date,
                    c.place,
                    c.place_type,
                    cm.title,
                    cm.category,
                    cm.service_type
             FROM course c
                      INNER JOIN course_master cm
                                 ON c.course_master_id = cm.id
            WHERE c.status = 'normal' AND ${whereServiceType} AND ${whereTitle} AND ${whereCategory} AND ${wherePlaceType} AND ${whereMonth} AND ${whereYear}`,
        searchValueArray,
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                const dataList = [];
                let count = 0;
                results.forEach((row, index) => {
                    checkIfCourseFull(db, row.id, row.service_type, (isCourseFull, regCount) => {
                        dataList.push({
                            id: row.id,
                            name: `${row.title} รุ่นที่ ${row.batch_number}`,
                            details: row.details,
                            applicationFee: row.application_fee,
                            place: row.place,
                            beginDate: row.begin_date,
                            endDate: row.end_date,
                            createdAt: row.created_at,
                            isCourseFull: isCourseFull,
                            regCount: regCount,
                        });

                        if (++count === results.length) {
                            res.send({
                                error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                dataList,
                            });
                            db.end();
                        }
                    });
                });
            }
        }
    );
    //db.end();
};

doGetCourseNumTraineeAvailable = (req, res, db) => {
    const {courseId} = req.body;

    getCourseNumTraineeAvailable(
        db, courseId, (success, data) => {
            if (success) {
                res.send({
                    error: new Error(0, `หลักสูตรนี้เหลือที่ว่าง ${data} ที่`, ''),
                });
            } else {
                res.send({
                    error: new Error(1, data, ''),
                });
            }
        }
    );
};

getCourseNumTraineeAvailable = (db, courseId, callback) => {
    let registrationTable = null;

    //

    db.query(
            `SELECT cm.service_type
             FROM course c
                      INNER JOIN course_master cm
                                 ON c.course_master_id = cm.id
             WHERE c.id = ?`,
        [courseId],
        function (err, results, fields) {
            if (err) {
                callback(false, 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล (1)');
            } else {
                const serviceType = results[0]['service_type'];
                switch (serviceType) {
                    case constants.SERVICE_TRAINING:
                        registrationTable = 'course_registration';
                        break;
                    case constants.SERVICE_SOCIAL:
                        registrationTable = 'course_registration_social';
                        break;
                    case constants.SERVICE_DRIVING_LICENSE:
                        registrationTable = 'course_registration_driving_license';
                        break;
                }

                db.query(
                        `SELECT trainee_limit
                         FROM course
                         WHERE id = ?`,
                    [courseId],
                    function (err, results, fields) {
                        if (err) {
                            callback(false, 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล (2)');
                        } else {
                            if (results.length > 0) {
                                const traineeLimit = results[0]['trainee_limit'];

                                if (serviceType === constants.SERVICE_SOCIAL || serviceType === constants.SERVICE_DRIVING_LICENSE) {
                                    db.query(
                                        `SELECT COUNT(id) AS trainee_count 
                             FROM ${registrationTable}
                             WHERE course_id = ? AND register_status <> ?`,
                                        [courseId, 'cancel'],
                                        function (err, results, fields) {
                                            if (err) {
                                                callback(false, 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล (3)');
                                            } else {
                                                const traineeCount = results[0]['trainee_count'];
                                                callback(true, traineeLimit - traineeCount);
                                            }
                                        }
                                    );
                                } else if (serviceType === constants.SERVICE_TRAINING) {
                                    db.query(
                                            `SELECT COUNT(*) AS trainee_count
                                             FROM course_registration cr
                                                      INNER JOIN course_trainee ct
                                                                 ON cr.id = ct.course_registration_id
                                             WHERE cr.course_id = ?
                                               AND ct.register_status <> ?`,
                                        [courseId, 'cancel'],
                                        function (err, results, fields) {
                                            if (err) {
                                                callback(false, 'เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล (4):');
                                            } else {
                                                const traineeCount = results[0]['trainee_count'];
                                                callback(true, traineeLimit - traineeCount);
                                            }
                                        }
                                    );
                                }
                            } else {
                                callback(false, 'ไม่พบข้อมูลหลักสูตร');
                            }
                        }
                    }
                );
            }
        }
    );
};

/*const upload = multer().single();

getCourseDrivingLicenseCourseId = (req, res, db) => {
    upload(req, res, err => {
        if (err instanceof multer.MulterError) {
            res.send({
                error: new Error(1, 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (1)', ''),
            });
            return;
        } else if (err) {
            res.send({
                error: new Error(1, 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์ (2)', ''),
            });
            return;
        }
        const {courseId} = req.body;
        const msg = `Course ID: ${courseId}`;
        console.log(msg);
        res.send({
            error: new Error(0, msg, ''),
        });
    });
};*/

registerCourse = (req, res, db, serviceType) => {
    let {courseId, trainees} = req.body;

    console.log(`Course ID: ${courseId}`);

    getCourseNumTraineeAvailable(
        db, courseId, (success, data) => {
            if (success) {
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*res.send({
                    error: new Error(0, `หลักสูตรนี้เหลือที่ว่าง ${data} ที่`, ''),
                });
                db.end();
                return;*/
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                const available = data; //data ก็คือจำนวนที่ว่างที่เหลือของคอร์ส (กรณี success)
                if (available > 0) { //คอร์สยังไม่เต็ม
                    //สำหรับวิชาการ ต้องเช็คต่อว่า จำนวนที่สมัครเข้ามา รับได้หรือไม่ (วิชาการ สามารถกรอกใบสมัครได้ครั้งละมากกว่า 1 คน)
                    if (serviceType === constants.SERVICE_TRAINING && trainees.length > available) {
                        res.send({
                            error: new Error(1, 'ขออภัย ไม่สามารถรับสมัครได้ เนื่องจากจำนวนผู้สมัครที่ท่านกรอกใบสมัครเข้ามา มีมากกว่าจำนวนที่รับได้ในตอนนี้', ''),
                        });
                        db.end();
                    } else {
                        //สมัครได้
                        switch (serviceType) {
                            case constants.SERVICE_TRAINING:
                                doRegisterCourse(req, res, db);
                                break;
                            case constants.SERVICE_SOCIAL:
                                doRegisterCourseSocial(req, res, db);
                                break;
                            case constants.SERVICE_DRIVING_LICENSE:
                                //doRegisterCourseDrivingLicense(req, res, db);
                                break;
                        }
                    }
                } else { //คอร์สเต็มแล้ว
                    res.send({
                        error: new Error(1, 'ขออภัย หลักสูตรนี้มีผู้สมัครเต็มจำนวนแล้ว', ''),
                    });
                    db.end();
                }
            } else {
                res.send({
                    error: new Error(1, data, ''),
                });
                db.end();
            }
        }
    );
};

doRegisterCourse = (req, res, db) => {
    const {loginToken, courseId, trainees, coordinator, receipt} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);

    let {
        coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorBirthDate, coordinatorJobPosition,
        coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom, coordinatorPhone, coordinatorEmail
    } = coordinator;
    const {
        receiptName, receiptAddress, receiptSubDistrict, receiptDistrict, receiptProvince,
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
                                              receipt_name, receipt_address, receipt_sub_district, receipt_district, receipt_province, receipt_postal_code, receipt_organization_phone, receipt_tax_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorBirthDate, coordinatorJobPosition,
            coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom,
            coordinatorPhone, coordinatorEmail, receiptName.trim(), receiptAddress.trim(), receiptSubDistrict.trim(), receiptDistrict.trim(), receiptProvince.trim(),
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
                                        // constants.HOST_BACKEND
                                        // /pages/print_ac_registration_form.php?ac_course_reg_id=92&payment=1&user=1&email=1

                                        const sendMailUrl = `${constants.HOST_BACKEND}/pages/print_ac_registration_form.php?ac_course_reg_id=${insertCourseRegId}&payment=1&user=1&email=1`;
                                        if (constants.HOST_BACKEND.substring(0, 5) === 'https') {
                                            https.get(sendMailUrl);
                                        } else {
                                            http.get(sendMailUrl);
                                        }

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
    const {loginToken, courseId, traineeData, receipt} = req.body;
    const memberId = loginToken === null ? 0 : decodeToken(loginToken);

    const {
        traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate, traineeOccupation, traineeWorkPlace, traineeAddress, traineeSubDistrict, traineeDistrict,
        traineeProvince, traineePostalCode, traineePhone, traineeEmail, traineeContactPersonName, traineeContactPersonPhone, traineeDisease,
        traineeNewsSourceWeb, traineeNewsSourceEmail, traineeNewsSourceBrochure, traineeNewsSourceOnline, traineeNewsSourceMouth
    } = traineeData;

    const {
        registerReceiptName, registerReceiptAddress, registerReceiptSubDistrict, registerReceiptDistrict, registerReceiptProvince, registerReceiptPostalCode
    } = receipt;

    /*ใช้แต่ละ bit เก็บค่า news source แต่ละค่า*/
    const newsSource = (traineeNewsSourceWeb ? 1 : 0) + (traineeNewsSourceEmail ? 2 : 0) + (traineeNewsSourceBrochure ? 4 : 0)
        + (traineeNewsSourceOnline ? 8 : 0) + (traineeNewsSourceMouth ? 16 : 0);

    /*แปลงกลับเป็น binary string ใช้ number.toString(2)*/

    db.query(
            `INSERT INTO course_registration_social (course_id, member_id, title, first_name, last_name, birth_date, occupation, work_place, address, sub_district, district,
                                                     province, postal_code, phone, email, contact_name, contact_phone, disease, news_source,
                                                     receipt_name, receipt_address, receipt_sub_district, receipt_district, receipt_province, receipt_postal_code)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate, traineeOccupation, traineeWorkPlace, traineeAddress, traineeSubDistrict, traineeDistrict,
            traineeProvince, traineePostalCode, traineePhone, traineeEmail, traineeContactPersonName, traineeContactPersonPhone, traineeDisease, newsSource,
            registerReceiptName, registerReceiptAddress, registerReceiptSubDistrict, registerReceiptDistrict, registerReceiptProvince, registerReceiptPostalCode],

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
                            const sendMailUrl = `${constants.HOST_BACKEND}/pages/print_ac_registration_form.php?service_type=${constants.SERVICE_SOCIAL}&trainee_id=${insertId}&payment=1&user=1&email=1`;
                            if (constants.HOST_BACKEND.substring(0, 5) === 'https') {
                                https.get(sendMailUrl);
                            } else {
                                http.get(sendMailUrl);
                            }

                            res.send({
                                error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                                courseRegId: insertId,
                            });
                            console.log('ลงทะเบียนสำเร็จ');
                        }
                    }
                );
                db.end();
            }
        }
    );
};

const pidImageStorage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'backend/uploads/slip_images')
    },
    filename: function (req, file, cb) {
        const fileName = Date.now() + '-' + file.originalname;
        cb(null, fileName);
    }
});

const uploadPidImage = multer({storage: pidImageStorage}).array('traineeImageFilePid', 5);

registerCourseDrivingLicense = (req, res, db) => {
    uploadPidImage(req, res, function (err) {
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

        const {courseId} = req.body;

        getCourseNumTraineeAvailable(
            db, courseId, (success, data) => {
                if (success) {
                    if (data > 0) { //data ก็คือจำนวนที่ว่างที่เหลือของคอร์ส (กรณี success)
                        doRegisterCourseDrivingLicense(req, res, db);
                    } else {
                        res.send({
                            error: new Error(1, 'ขออภัย หลักสูตรนี้มีผู้สมัครเต็มจำนวนแล้ว', ''),
                        });
                        db.end();
                    }
                } else {
                    res.send({
                        error: new Error(1, data, ''),
                    });
                    db.end();
                }
            }
        );
    });
};

doRegisterCourseDrivingLicense = (req, res, db) => {
    const {
        loginToken, courseId,
        traineeTitle, traineeFirstName, traineeLastName, traineePid, traineeAddress, traineeMoo, traineeSoi, traineeRoad,
        traineeSubDistrict, traineeDistrict, traineeProvince, traineePostalCode,
        traineePhone, traineeSelectedCourseType, traineeSelectedLicenseTypeCar, traineeSelectedLicenseTypeBicycle, traineeSelectedLicenseTypeTricycle
    } = req.body;

    console.log('Login Token: ' + loginToken);
    console.log('1: ' + loginToken == null);
    console.log('2: ' + loginToken === null);
    console.log(typeof loginToken);

    // ถ้า user ไม่ได้ login, ค่า loginToken จะส่งมาเป็นสตริงว่าง
    const memberId = loginToken === '' ? 0 : decodeToken(loginToken);

    /*ใช้แต่ละ bit เก็บค่า license type แต่ละค่า (user สามารถเลือกได้มากกว่า 1 license)*/
    const licenseType = (traineeSelectedLicenseTypeCar === '1' ? 1 : 0) + (traineeSelectedLicenseTypeBicycle === '1' ? 2 : 0) + (traineeSelectedLicenseTypeTricycle === '1' ? 4 : 0);

    /*แปลงกลับเป็น binary string ใช้ number.toString(2)*/

    //const {filename} = req.file;
    const filename = [];
    let i;
    for (i = 0; i < req.files.length; i++) {
        filename.push(req.files[i].filename);
    }
    for (; i < 5; i++) {
        filename.push(null);
    }

    db.query(
            `INSERT INTO course_registration_driving_license
             (course_id, member_id, title, first_name, last_name, pid, address, moo, soi, road, sub_district, district,
              province, postal_code, phone, pid_file_name, pid_file_name_2, pid_file_name_3, pid_file_name_4, pid_file_name_5,
              course_type, license_type)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
        [courseId, memberId, traineeTitle, traineeFirstName, traineeLastName, traineePid, traineeAddress, traineeMoo, traineeSoi, traineeRoad,
            traineeSubDistrict, traineeDistrict, traineeProvince, traineePostalCode, traineePhone, filename[0], filename[1], filename[2], filename[3], filename[4],
            traineeSelectedCourseType, licenseType],

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
                            const sendMailUrl = `${constants.HOST_BACKEND}/pages/print_dl_registration_form.php?trainee_id=${insertId}&payment=1&user=1&email=1`;
                            if (constants.HOST_BACKEND.substring(0, 5) === 'https') {
                                https.get(sendMailUrl);
                            } else {
                                http.get(sendMailUrl);
                            }

                            res.send({
                                error: new Error(0, 'ลงทะเบียนสำเร็จ', ''),
                                courseRegId: insertId,
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
                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ สถาบันจะติดต่อท่านตามข้อมูลที่ได้รับ', ''),
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
                                    db.query( // ถ้าหาในบริการวิชาการและใบขับขี่ไม่เจอ ก็ไปหาในสังคม
                                            `SELECT cm.title           AS courseTitle,
                                                    c.batch_number     AS courseBatchNumber,
                                                    c.place            AS coursePlace,
                                                    c.begin_date       AS courseBeginDate,
                                                    c.end_date         AS courseEndDate,
                                                    cr.form_number     AS formNumber,
                                                    cr.id              AS traineeId,
                                                    cr.title           AS traineeTitle,
                                                    cr.first_name      AS traineeFirstName,
                                                    cr.last_name       AS traineeLastName,
                                                    cr.register_status AS registerStatus
                                             FROM course_registration_social cr
                                                      INNER JOIN course c
                                                                 ON cr.course_id = c.id
                                                      INNER JOIN course_master cm
                                                                 ON c.course_master_id = cm.id
                                             WHERE cr.form_number = ?`,
                                        [formNumber],
                                        function (err, results, fields) {
                                            if (err) {
                                                res.send({
                                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (3)', 'error run query: ' + err.stack),
                                                });
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
                    //db.end();
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

const uploadSlipImage = multer({storage: slipImageStorage}).array('file', 5);

doAddTransferNotification = (req, res, db) => {
    uploadSlipImage(req, res, function (err) {
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

        let serviceType = null;
        let sql = null;

        if (serviceTypePrefix === constants.SERVICE_PREFIX_TRAINING) {
            serviceType = constants.SERVICE_TRAINING;
            sql = `UPDATE course_trainee
                   SET register_status = 'wait-approve'
                   WHERE id = ?
                     AND register_status <> 'complete'`;
        } else if (serviceTypePrefix === constants.SERVICE_PREFIX_SOCIAL) {
            serviceType = constants.SERVICE_SOCIAL;
            sql = `UPDATE course_registration_social
                   SET register_status = 'wait-approve'
                   WHERE id = ?
                     AND register_status <> 'complete'`;
        } else if (serviceTypePrefix === constants.SERVICE_PREFIX_DRIVING_LICENSE) {
            serviceType = constants.SERVICE_DRIVING_LICENSE;
            sql = `UPDATE course_registration_driving_license
                   SET register_status = 'wait-approve'
                   WHERE id = ?
                     AND register_status <> 'complete'`;
        }

        /*let serviceType = serviceTypePrefix === constants.SERVICE_PREFIX_TRAINING // AC
            ? constants.SERVICE_TRAINING
            : (serviceTypePrefix === constants.SERVICE_PREFIX_DRIVING_LICENSE // DL
                ? constants.SERVICE_DRIVING_LICENSE
                : null);*/

        //const {filename} = req.file;

        let valuePlaceHolders = '';
        const valueList = [];
        for (let i = 0; i < req.files.length; i++) {
            if (i !== 0) {
                valuePlaceHolders += ',';
            }
            valuePlaceHolders += '(?, ?, ?, ?, ?, ?)';

            valueList.push(memberId);
            valueList.push(traineeId);
            valueList.push(serviceType);
            valueList.push(amount);
            valueList.push(transferDate);
            valueList.push(req.files[i].filename);
        }

        db.query(
            `INSERT INTO payment_notification
                     (member_id, trainee_id, service_type, amount, transfer_date, slip_file_name)
                 VALUES ${valuePlaceHolders}`,
            valueList,
            function (err, results, fields) {
                if (err) {
                    res.send({
                        error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (1)', 'error run query: ' + err.stack),
                    });
                    db.end();
                } else {
                    db.query(
                        sql,
                        [traineeId],
                        function (err, results, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                            } else {
                                res.send({
                                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ', ''),
                                    //debug: `ชื่อไฟล์ ${filename}, ยอดโอน ${amount} บาท, โอนเมื่อ ${transferDate}`,
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

doGetAcademicPaper = (req, res, db) => {
    const {id, offset, limit} = req.body;
    const whereClause = id == null ? 'TRUE' : 'id = ?';
    const limitClause = (offset == null || limit == null) ? '' : `LIMIT ${offset}, ${limit}`;

    db.query(
        `SELECT id, title, file_name AS fileName, first_name AS firstName, last_name AS lastName, 
                year_published AS yearPublished, abstract, fund_source AS fundSource, created_at AS createdAt
             FROM academic_paper
             WHERE ${whereClause}
             ORDER BY id DESC
             ${limitClause}`,
        id == null ? [] : [id],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                if (id == null) {
                    db.query(
                            `SELECT COUNT(*) AS totalCount
                             FROM academic_paper`,
                        [],
                        function (err, totalCountResults, fields) {
                            if (err) {
                                res.send({
                                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                                });
                            } else {
                                res.send({
                                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                    dataList: results,
                                    totalCount: totalCountResults[0].totalCount,
                                });
                            }
                        }
                    );
                    db.end();
                } else {
                    res.send({
                        error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                        dataList: results
                    });
                    db.end();
                }
            }
        }
    );
    //db.end();
};

doSearchAcademicPaper = (req, res, db) => {
    const {searchTitle, searchYearPublished, searchName} = req.body.searchFields;

    const whereTitle = searchTitle ? 'title LIKE ?' : 'TRUE';
    const whereYearPublished = searchYearPublished ? 'year_published LIKE ?' : 'TRUE';
    const whereName = searchName ? '(first_name LIKE ? || last_name LIKE ?)' : 'TRUE';

    const searchValueArray = [];
    if (searchTitle) {
        searchValueArray.push(`%${searchTitle}%`);
    }
    if (searchYearPublished) {
        searchValueArray.push(`%${searchYearPublished}%`);
    }
    if (searchName) {
        searchValueArray.push(`%${searchName}%`);
        searchValueArray.push(`%${searchName}%`);
    }

    db.query(
        `SELECT id, title, file_name AS fileName, first_name AS firstName, last_name AS lastName, 
                year_published AS yearPublished, abstract, fund_source AS fundSource, created_at AS createdAt
             FROM academic_paper
             WHERE ${whereTitle} AND ${whereYearPublished} AND ${whereName}
             ORDER BY id DESC`,
        searchValueArray,
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    dataList: results
                });
            }
        }
    );
    db.end();
};

doAddAcademicPaperDownload = (req, res, db) => {
    const {id, fields} = req.body;

    db.query(
            `INSERT INTO academic_paper_download (academic_paper_id, first_name, last_name, organization_name, job_position, occupation, email, use_purpose)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
        [id, fields.firstName, fields.lastName, fields.organizationName, fields.jobPosition, fields.occupation, fields.email, fields.use],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ', ''),
                });
            }
        }
    );
    db.end();
};

doGetTrainingCourseCategory = (req, res, db) => {
    db.query(
        'SELECT * FROM training_course_category',
        [],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลหมวดหมู่หลักสูตร', 'error run query: ' + err.stack),
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

doGetDrivingLicenseCourseType = (req, res, db) => {
    db.query(
        'SELECT id, title, application_fee FROM driving_license_course_type',
        [],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูลประเภทหลักสูตรการอบรมใบขับขี่', 'error run query: ' + err.stack),
                });
            } else {
                let dataList = [];
                results.forEach(row => {
                    const {id, title} = row;
                    const applicationFee = row.application_fee;
                    dataList.push({id, title, applicationFee});
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

doGetDocumentDownload = (req, res, db) => {
    const {documentType, offset, limit} = req.body;
    const limitClause = (offset == null || limit == null) ? '' : `LIMIT ${offset}, ${limit}`;

    db.query(
        `SELECT id,
                    title,
                    short_description,
                    file_path,
                    file_name,
                    image_file_name,
                    download,
                    hit
             FROM document_download
             WHERE document_type = ? AND status = ?
            ORDER BY created_at DESC
        ${limitClause}`,
        [documentType, 'publish'],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                db.query(
                        `SELECT COUNT(*) AS totalCount
                         FROM document_download
                         WHERE document_type = ?
                           AND status = ?`,
                    [documentType, 'publish'],
                    function (err, totalCountResults, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                        } else {
                            res.send({
                                error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                dataList: results,
                                totalCount: totalCountResults[0].totalCount,
                            });
                        }
                    }
                );
                db.end();
            }
        }
    );
    //db.end();
};

doGetFaq = (req, res, db) => {
    const {offset, limit} = req.body;
    const limitClause = (offset == null || limit == null) ? '' : `LIMIT ${offset}, ${limit}`;

    db.query(
        `SELECT id,
                    title,
                    details
             FROM faq
             WHERE status = ?
             ORDER BY created_at DESC
        ${limitClause}`,
        ['publish'],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                db.query(
                        `SELECT COUNT(*) AS totalCount
                         FROM faq
                         WHERE status = ?`,
                    ['publish'],
                    function (err, totalCountResults, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                        } else {
                            res.send({
                                error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                dataList: results,
                                totalCount: totalCountResults[0].totalCount,
                            });
                        }
                    }
                );
                db.end();
            }
        }
    );
    //db.end();
};

doUpdateDocumentDownloadCount = (req, res, db) => {
    const {id} = req.body;

    db.query(
            `UPDATE document_download
             SET download = download + 1
             WHERE id = ?`,
        [id],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'บันทึกข้อมูลสำเร็จ', ''),
                });
            }
        }
    );
    db.end();
};

doGetNewsLatest = (req, res, db) => {
    db.query(
            `SELECT id, title, short_description, details, image_file_name, news_date, news_type
             FROM news
             WHERE news_type = ?
               AND status = ?
             ORDER BY created_at DESC
             LIMIT 0, 4`,
        ['training', 'publish'],
        function (err, resultsTrainingNews, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
                db.end();
            } else {
                db.query(
                        `SELECT id, title, short_description, details, image_file_name, news_date, news_type
                         FROM news
                         WHERE news_type = ?
                           AND status = ?
                         ORDER BY created_at DESC
                         LIMIT 0, 4`,
                    ['public-relations', 'publish'],
                    function (err, resultsPublicRelationsNews, fields) {
                        if (err) {
                            res.send({
                                error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (2)', 'error run query: ' + err.stack),
                            });
                        } else {
                            res.send({
                                error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                                trainingNewsList: resultsTrainingNews,
                                publicRelationsNewsList: resultsPublicRelationsNews,
                            });
                        }
                    }
                );
                db.end();
            }
        }
    );
};

doGetInHouseLatest = (req, res, db) => {
    db.query(
            `SELECT id, title, short_description, details, image_file_name, news_date, news_type
             FROM news
             WHERE news_type = ?
             ORDER BY created_at DESC
             LIMIT 0, 3`,
        ['in-house'],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    dataList: results,
                });
            }
        }
    );
    db.end();
};

doGetActivity = (req, res, db) => {
    const {id, offset, limit} = req.body;
    const whereClause = id == null ? 'TRUE' : 'id = ?';
    const limitClause = (offset == null || limit == null) ? '' : `LIMIT ${offset}, ${limit}`;

    db.query(
        `SELECT n.id, n.title, n.short_description, n.details, n.image_file_name, n.news_date, n.news_type,
                    na.file_name
             FROM (SELECT * FROM news WHERE news_type = 'activity' AND ${whereClause} AND status = 'publish' ${limitClause}) n
                 LEFT JOIN news_asset na 
                     ON n.id = na.news_id
             ORDER BY n.created_at DESC`,
        id == null ? [] : [id],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล (1)', 'error run query: ' + err.stack),
                });
            } else {
                let previousId = 0;
                const activityList = [];
                let activity = null;
                for (let i = 0; i < results.length; i++) {
                    const row = results[i];
                    if (row['id'] !== previousId) {
                        if (previousId !== 0) {
                            activityList.push(activity);
                        }
                        activity = {
                            id: row['id'],
                            title: row['title'],
                            short_description: row['short_description'],
                            details: row['details'],
                            image_file_name: row['image_file_name'],
                            news_date: row['news_date'],
                            news_type: row['news_type'],
                        };
                        activity['image_list'] = (row['file_name'] == null ? [] : [row['file_name']]);
                    } else {
                        activity['image_list'].push(row['file_name']);
                    }
                    previousId = row['id'];
                }
                activityList.push(activity);

                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    activityList: activityList,
                    count: results.length,
                });
            }
        }
    );
    db.end();
};

doGetNewsById = (req, res, db) => {
    const {id} = req.body;

    db.query(
            `SELECT n.id,
                    n.title,
                    n.short_description,
                    n.details,
                    n.image_file_name,
                    n.news_date,
                    n.news_type,
                    na.file_name
             FROM news n
                      LEFT JOIN news_asset na
                                ON n.id = na.news_id
             WHERE n.id = ?`,
        [id],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                if (results.length > 0) {
                    let news = {
                        id: results[0].id,
                        title: results[0].title,
                        details: results[0].details,
                        newsDate: results[0].news_date,
                        newsType: results[0].news_type,
                    };

                    let imageList = [];
                    results.forEach(row => {
                        if (row.file_name != null) {
                            imageList.push(row.file_name);
                        }
                    });
                    news['imageList'] = imageList;

                    res.send({
                        error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                        news: news,
                    });
                } else {
                    res.send({
                        error: new Error(1, 'ไม่พบข้อมูล', ''),
                    });
                }
            }
        }
    );
    db.end();
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

doGetIntro = (req, res, db) => {
    const {type} = req.body;

    db.query(
            `SELECT id, title, details, image_file_name
             FROM intro
             WHERE type = ?
               AND status = ?
             ORDER BY sort_index`,
        [type, 'publish'],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    dataList: results,
                });
            }
        }
    );
    db.end();
};

doGetService = (req, res, db) => {
    db.query(
            `SELECT id, title, details, slug, url, icon_file_name
             FROM service 
             WHERE status = 1`,
        [],
        function (err, results, fields) {
            if (err) {
                res.send({
                    error: new Error(1, 'เกิดข้อผิดพลาดในการอ่านข้อมูล', 'error run query: ' + err.stack),
                });
            } else {
                res.send({
                    error: new Error(0, 'อ่านข้อมูลสำเร็จ', ''),
                    serviceList: results,
                });
            }
        }
    );
    db.end();
};