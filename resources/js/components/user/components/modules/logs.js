import {style, download} from './cmd';

export default function logs(param) {
    const Excel = require('exceljs');

    let workbook = new Excel.Workbook()
    let worksheet = workbook.addWorksheet('Logs')
    worksheet.columns = [
        { header: 'User ID', key: 'user_id', width: 55 },
        { header: 'Action', key: 'action', width: 13 },
        { header: 'Remarks', key: 'remarks', width: 60 },
        { header: 'Original Values', key: 'original_values', width: 18 },
        { header: 'New Values', key: 'new_values', width: 35 }
    ]
    param.data.forEach((e, index) => {
        worksheet.addRow({
            user_id: e.user_id,
            action: e.action,
            remarks: e.remarks,
            original_values: e.original_values,
            new_values: e.new_values
        })
    })

    style({ worksheet:worksheet, headercount:5 })
    download({ filename:'Logs', workbook:workbook })
}