import {style, download} from './cmd';

export function officelist(param) {
    const Excel = require('exceljs');

    let workbook = new Excel.Workbook()
    let worksheet = workbook.addWorksheet('Office List')
    worksheet.columns = [
        { header: 'Office Name', key: 'Office_Name', width: 55 },
        { header: 'Office Code', key: 'Office_Code', width: 13 },
        { header: 'Address', key: 'Address', width: 60 },
        { header: 'Contact Number', key: 'Contact_Number', width: 18 },
        { header: 'Email Address', key: 'Email_Address', width: 35 }
    ]

    param.data.forEach((e, index) => {
        worksheet.addRow({
            Office_Name: e.name,
            Office_Code: e.office_code,
            Address: e.address,
            Contact_Number: e.contact_number,
            Email_Address: e.contact_email
        })
    })

    style({ worksheet:worksheet, headercount:5, autowidth:true })
    download({ filename:'Office List', workbook:workbook })
}