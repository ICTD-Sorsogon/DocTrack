import {style, download} from './cmd';

export default function archiveae(param) {
    const Excel = require('exceljs');
    const data = param.data
    const selected = param.selected

    console.log(data)
    // selected.type
    // selected.data

    let workbook = new Excel.Workbook()

    function columnHeader(worksheet) {
        worksheet.columns = [
            { header: 'Tracking Code', key: 'tracking_code'},
            { header: 'Subject', key: 'subject'},
            { header: 'Sender', key: 'sender'},
            { header: 'Document Type', key: 'document_type'},
            { header: 'Status', key: 'status'},
            { header: 'Page Count', key: 'page_count'},
            { header: 'Attachment Page Count', key: 'attachment_page_count'},
            { header: 'Originating Office', key: 'origin_office'},
            { header: 'Destination', key: 'destination'},
            { header: 'Remarks', key: 'remarks'},
        ]
    }

    function xStyle(worksheet) {
        let cWidth = {}
        worksheet.eachRow({ includeEmpty: false }, function (row, rowNumber) {
            const headerColumns = ['A','B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']
            headerColumns.forEach((cLetter) => {
                let cLength = worksheet.getCell(`${cLetter}${rowNumber}`).value?.toString().trim()?.length ?? 0
                if(cWidth[cLetter] == undefined){
                    cWidth[cLetter] = cLength
                }else{
                    cWidth[cLetter] = (cWidth[cLetter] <  cLength) ? cLength : cWidth[cLetter]
                }
            })
        })
        Object.values(cWidth).forEach((width, index) => {
            worksheet.getColumn(index+1).width = width + 5
        });
        style({ worksheet:worksheet, headercount:10 })
    }

    if (param.type == 'group') {
        selected.data.forEach((element) => {
            let source = ['Internal', 'External']
            let worksheet = selected.type == 'byOffice' ? workbook.addWorksheet(element.office_code) : workbook.addWorksheet(element)
            columnHeader(worksheet)
            if(selected.type == 'byOffice'){
                data.forEach((e, index) => {
                    let destination = e.destination.map(des => des.name)
                        if(e.origin_office.name == element.name || destination.includes(element.name)){
                            by(worksheet, e)
                        }
                })
            } else if(selected.type == 'byType'){
                data.forEach((e, index) => {
                    if(e.document_type.name == element){
                        by(worksheet, e)
                    }
                })
            } else if(selected.type == 'bySource'){
                data.forEach((e, index) => {
                    if(source[e.is_external] == element){
                        by(worksheet, e)
                    }
                })
            }
            xStyle(worksheet)
        });
    } else {
        //don't disturb me
    }
    download({ filename:'Logs', workbook:workbook })

    function by(worksheet, e){
        let destination_list = ''
        e.destination.forEach(element => destination_list += element.name + ', ');
        worksheet.addRow({
            tracking_code: e.tracking_code,
            subject: e.subject,
            sender: e.sender?.name,
            document_type: e.document_type['name'],
            status: e.status,
            page_count: e.page_count,
            attachment_page_count: e.attachment_page_count,
            origin_office: e.origin_office['name'],
            destination: destination_list.slice(0, -2),
            remarks: e.remarks,
        })

    }
}


