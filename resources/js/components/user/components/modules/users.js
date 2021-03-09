import {style, download} from './cmd';

export function users(param) {
    const Excel = require('exceljs')

    let status = ['Active', 'Inactive']
    let workbook = new Excel.Workbook()

    status.forEach(element => {
        let worksheet = workbook.addWorksheet(element)
        worksheet.columns = [
            { header: 'ID Number', key: 'id_number'},
            { header: 'Username', key: 'username'},
            { header: 'Fullname', key: 'full_name'},
            { header: 'Gender', key: 'gender'},
            { header: 'Birthday', key: 'birthday'},
        ]

        param.data.forEach((user) => {
            if(user.is_active == element){
                worksheet.addRow({
                    id_number: user.id_number,
                    username: user.username,
                    full_name: user.full_name,
                    gender: user.gender,
                    birthday: user.birthday,
                })
            }
        })
        style({ worksheet:worksheet, headercount:5, autowidth:true })
    });

    download({ filename:'User List', workbook:workbook })
}
