//https://medium.com/@kainikhil/nodejs-how-to-generate-and-properly-serve-pdf-6835737d118e

const pdfMakePrinter = require('pdfmake/src/printer');
const fs = require('fs');
const mysql = require('mysql');
const dbConfig = require('./db-config');
const utils = require('./etc/utils');

function createPdfContent(course, coordinator, trainees) {
    const {
        courseTitle, courseBatchNumber, coursePlace, courseBeginDate,
        courseEndDate, courseDetails, responsibleUser
    } = course;
    const {
        responsibleUserFirstName, responsibleUserLastName,
        responsibleUserPhoneOffice, responsibleUserEmail
    } = responsibleUser;
    const {
        coordinatorTitle, coordinatorFirstName, coordinatorLastName,
        coordinatorPhone, coordinatorEmail
    } = coordinator;

    /* -----trainee-----
        traineeFormNumber, traineeTitle, traineeFirstName, traineeLastName, traineePhone, traineeEmail
    */

    /*const traineeTableBody = [];
    traineeTableBody.push(
        [
            {text: 'ลำดับที่', bold: true, alignment: 'center', margin: [5, 0, 5, 0]},
            {text: 'ชื่อ-นามสกุล', bold: true, alignment: 'center'},
            {text: 'เบอร์โทร', bold: true, alignment: 'center'},
            {text: 'อีเมล', bold: true, alignment: 'center'},
        ]
    );
    for (let i = 0; i < trainees.length; i++) {
        const trainee = trainees[i];
        traineeTableBody.push(
            [
                {text: (i + 1).toString(), alignment: 'center'},
                `${trainee.traineeTitle} ${trainee.traineeFirstName} ${trainee.traineeLastName}`,
                `${trainee.traineePhone}`,
                `${trainee.traineeEmail}`,
            ]
        );
    }*/

    const coordinatorContent = coordinatorFirstName == null ? [{}] : [
        {
            text: 'ผู้ประสานงาน',
            style: 'subHeader'
        },
        {
            text: `${coordinatorTitle} ${coordinatorFirstName} ${coordinatorLastName}`,
            style: 'body',
        },
        {
            ul: [
                `เบอร์โทร: ${coordinatorPhone}`,
                `อีเมล: ${coordinatorEmail}`,
            ],
            style: 'list',
        },
    ];

    let contentList = [];
    for (let i = 0; i < trainees.length; i++) {
        const {
            traineeFormNumber, traineeTitle, traineeFirstName, traineeLastName,
            traineePhone, traineeEmail
        } = trainees[i];

        const pageBreak = i === 0 ? null : 'before';

        contentList = contentList.concat([
            {
                text: `หน้า ${i + 1}/${trainees.length}`,
                alignment: 'right',
                style: 'small',
                pageBreak: pageBreak,
            },
            {
                image: './static/images/mainlogo.png',
                alignment: 'center',
                margin: [0, 0, 0, 20],
            },
            {
                text: 'ใบสมัครเลขที่\n' + traineeFormNumber,
                alignment: 'right',
                style: 'body',
            },
            {
                text: 'โครงการบริการวิชาการ\nสถาบันเสริมศึกษาและทรัพยากรมนุษย์ มหาวิทยาลัยธรรมศาสตร์',
                style: 'header'
            },
            {
                table: {
                    widths: ['auto', '*'],
                    body: [
                        [{text: 'ชื่อหลักสูตร:  ', bold: true}, `${courseTitle} รุ่นที่ ${courseBatchNumber}`],
                        [{text: 'วันที่อบรม:  ', bold: true}, utils.formatCourseDateLong(courseBeginDate, courseEndDate)],
                        [{text: 'สถานที่อบรม:  ', bold: true}, coursePlace],
                    ],
                },
                layout: 'noBorders',
                style: 'bodyNoIndent',
            },
            {
                text: 'ผู้รับผิดชอบโครงการ',
                style: 'subHeader'
            },
            {
                text: `${responsibleUserFirstName} ${responsibleUserLastName}`,
                style: 'body',
            },
            {
                ul: [
                    `เบอร์โทร: ${responsibleUserPhoneOffice}`,
                    `อีเมล: ${responsibleUserEmail}`,
                ],
                style: 'list',
            },

            {
                table: {
                    widths: ['50%', '50%'],
                    body: [
                        [
                            [
                                {
                                    text: 'ผู้สมัครอบรม',
                                    style: 'subHeader'
                                },
                                {
                                    text: `${traineeTitle} ${traineeFirstName} ${traineeLastName}`,
                                    style: 'body',
                                },
                                {
                                    ul: [
                                        `เบอร์โทร: ${traineePhone}`,
                                        `อีเมล: ${traineeEmail}`,
                                    ],
                                    style: 'list',
                                },
                            ],
                            coordinatorContent,
                        ],
                    ],
                },
                layout: 'noBorders',
                style: 'bodyNoIndent',
            },

            /*{
                table: {
                    widths: ['auto', '*', '*', '*'],
                    body: traineeTableBody,
                },
                //layout: 'noBorders',
                style: 'body',
            }*/
        ]);
    }

    return ({
        pageSize: 'A4',
        pageMargins: [60, 60],
        pageOrientation: 'portrait',
        content: contentList,
        styles: {
            body: {
                fontSize: 16,
                margin: [30, 5, 0, 0],
            },
            bodyNoIndent: {
                fontSize: 16,
                margin: [0, 5, 0, 0],
            },
            header: {
                fontSize: 18,
                bold: true,
                alignment: 'center',
                margin: [0, 20],
            },
            subHeader: {
                fontSize: 16,
                bold: true,
                margin: [0, 20, 0, 0],
            },
            list: {
                fontSize: 16,
                margin: [45, 3, 0, 0],
            },
            small: {
                fontSize: 12
            }
        }
    });
}

module.exports = {
    generateTraineeForm_Training: (traineeFormNumberList, download, callback) => { // callback(success, result)
        /*เอาไว้ทดสอบกรณี error, สร้าง pdf ไม่ได้*/
        /*callback(false, null);
        return;*/

        const db = mysql.createConnection({
            host: dbConfig.HOST,
            user: dbConfig.USER,
            password: dbConfig.PASSWORD,
            database: dbConfig.DATABASE,
        });
        db.connect(function (err) {
            if (err) {
                console.log(err.stack);
                callback(false, err.stack);
                return;
            }

            console.log('Db connected as id ' + db.threadId);

            let placeHolder = '';
            for (let i = 0; i < traineeFormNumberList.length; i++) {
                if (i > 0) placeHolder += ',';
                placeHolder += '?';
            }
            db.query(
                `SELECT course_registration_id AS courseRegId,
                            form_number AS traineeFormNumber,       
                            title AS traineeTitle, 
                            first_name AS traineeFirstName, 
                            last_name AS traineeLastName, 
                            phone AS traineePhone, 
                            email AS traineeEmail
                     FROM course_trainee
                     WHERE form_number IN (${placeHolder})
                     ORDER BY id`,
                traineeFormNumberList,
                function (err, results, fields) {
                    if (err) {
                        console.log(err.stack);
                        callback(false, err.stack);
                        db.end();
                    } else {
                        if (results === 0) {
                            callback(false, 'ไม่พบข้อมูลใบสมัคร');
                            db.end();
                        } else {
                            const courseRegId = results[0].courseRegId;
                            const trainees = results;
                            /*for (let i = 0; i < results.length; i++) {
                                const {traineeTitle, traineeFirstName, traineeLastName, traineePhone, traineeEmail} = results[i];
                                trainees.push({
                                    traineeTitle, traineeFirstName, traineeLastName, traineePhone, traineeEmail
                                });
                            }*/

                            db.query(
                                    `SELECT cm.title                  AS courseTitle,
                                            c.batch_number            AS courseBatchNumber,
                                            c.place                   AS coursePlace,
                                            c.begin_date              AS courseBeginDate,
                                            c.end_date                AS courseEndDate,
                                            c.details                 AS courseDetails,
                                            u.first_name              AS responsibleUserFirstName,
                                            u.last_name               AS responsibleUserLastName,
                                            u.phone_office            AS responsibleUserPhoneOffice,
                                            u.email                   AS responsibleUserEmail,
                                            cr.form_number            AS courseRegFormNumber,
                                            cr.coordinator_title      AS coordinatorTitle,
                                            cr.coordinator_first_name AS coordinatorFirstName,
                                            cr.coordinator_last_name  AS coordinatorLastName,
                                            cr.coordinator_phone      AS coordinatorPhone,
                                            cr.coordinator_email      AS coordinatorEmail
                                     FROM course c
                                              INNER JOIN user u ON c.responsible_user_id = u.id
                                              INNER JOIN course_master cm ON c.course_master_id = cm.id
                                              INNER JOIN course_registration cr ON c.id = cr.course_id
                                     WHERE cr.id = ?`,
                                [courseRegId],
                                function (err, results, fields) {
                                    if (err) {
                                        console.log(err.stack);
                                        callback(false, err.stack);
                                    } else {
                                        if (results === 0) {
                                            callback(false, 'ไม่พบข้อมูล');
                                            db.end();
                                        } else {
                                            const {
                                                courseTitle, courseBatchNumber, coursePlace, courseBeginDate, courseEndDate,
                                                courseDetails, responsibleUserFirstName, responsibleUserLastName, responsibleUserPhoneOffice,
                                                responsibleUserEmail, courseRegFormNumber, coordinatorTitle, coordinatorFirstName, coordinatorLastName,
                                                coordinatorPhone, coordinatorEmail
                                            } = results[0];

                                            const course = {
                                                courseTitle, courseBatchNumber, coursePlace, courseBeginDate, courseEndDate,
                                                courseDetails,
                                                responsibleUser: {
                                                    responsibleUserFirstName, responsibleUserLastName, responsibleUserPhoneOffice,
                                                    responsibleUserEmail
                                                }
                                            };

                                            const coordinator = {
                                                coordinatorTitle, coordinatorFirstName, coordinatorLastName,
                                                coordinatorPhone, coordinatorEmail
                                            };

                                            const fontDescriptors = {
                                                Roboto: {
                                                    normal: './static/fonts/DBHeavent.ttf',
                                                    bold: './static/fonts/DBHeavent-Med.ttf',
                                                    italics: './static/fonts/DBHeavent.ttf',
                                                    bolditalics: './static/fonts/DBHeavent-Med.ttf'
                                                }
                                            };
                                            const printer = new pdfMakePrinter(fontDescriptors);
                                            const content = createPdfContent(
                                                course, coordinator, trainees
                                            );
                                            const doc = printer.createPdfKitDocument(content);

                                            if (download) {
                                                /*กรณีต้องการ save ลงไฟล์*/
                                                const pdfFilePath = `${__dirname}/static/public/${courseRegFormNumber}.pdf`;
                                                console.log(`Creating ${pdfFilePath}...`);

                                                const writeStream = fs.createWriteStream(pdfFilePath);

                                                doc.pipe(writeStream.on('error', err => {
                                                    callback(false, err.message);
                                                }));
                                                /*doc.on('end', () => {
                                                    callback(true, 'สร้าง PDF ใบสมัครสำเร็จ');
                                                });*/
                                                doc.end();
                                                writeStream.on('finish', function () {
                                                    callback(true, 'สร้าง PDF ใบสมัครสำเร็จ');
                                                });
                                            } else {
                                                let chunks = [];

                                                doc.on('data', (chunk) => {
                                                    chunks.push(chunk);
                                                });
                                                doc.on('end', () => {
                                                    const result = Buffer.concat(chunks);
                                                    callback(true, result);
                                                    //callback(true, 'data:application/pdf;base64,' + result.toString('base64'));
                                                });
                                                doc.end();
                                            }
                                        }
                                    }
                                }
                            );
                            db.end();
                        }
                    }
                }
            );
        });
    },

    /*generateRegistrationFormTrainingPdf: (formNumber, course, trainees, coordinator, callback) => {
        const {
            courseTitle, courseBatchNumber, coursePlace, courseBeginDate, courseEndDate,
            courseDetails, courseResponsibleUserId
        } = course;

        const {
            coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorBirthDate,
            coordinatorJobPosition, coordinatorOrganizationName, coordinatorOrganizationType,
            coordinatorOrganizationTypeCustom, coordinatorPhone, coordinatorEmail
        } = coordinator;

        for (let i = 0; i < trainees.length; i++) {
            const {
                traineeTitle, traineeFirstName, traineeLastName, traineeBirthDate,
                traineeJobPosition, traineeOrganizationName, traineeOrganizationType,
                traineeOrganizationTypeCustom, traineePhone, traineeEmail
            } = trainees[i];
        }

        try {
            const fontDescriptors = {
                Roboto: {
                    normal: './static/fonts/DBHeavent.ttf',
                    bold: './static/fonts/DBHeavent-Med.ttf',
                    italics: './static/fonts/DBHeavent.ttf',
                    bolditalics: './static/fonts/DBHeavent-Med.ttf'
                }
            };
            const printer = new pdfMakePrinter(fontDescriptors);
            const content = createPdfContent(
                formNumber,
                courseTitle + ' รุ่นที่ ' + courseBatchNumber,
                coursePlace,
                utils.formatCourseDateLong(courseBeginDate, courseEndDate),
                trainees,
            );
            const doc = printer.createPdfKitDocument(content);

            doc.pipe(fs.createWriteStream(`./static/public/${formNumber}.pdf`).on('error', err => {
                callback(false, err.message);
            }));
            doc.on('end', () => {
                callback(true, 'สร้าง PDF ใบสมัครสำเร็จ');
            });
            doc.end();

        } catch (err) {
            throw(err);
        }
    },*/ //generateRegistrationFormTrainingPdf()
};
