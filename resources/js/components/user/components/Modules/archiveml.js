import {style, download} from './cmd';

export default function archiveml(param) {
    const Excel = require('exceljs');

    const type = param.type
    const data = param.data
    const priority_list = param.priority_list

    let workbook = new Excel.Workbook()
    let worksheet = workbook.addWorksheet('Document Archive Master List')
    worksheet.columns = [
        { header: 'Tracking Code', key: 'tracking_code'},
        { header: 'Subject', key: 'subject'},
        { header: 'Sender', key: 'sender'},
        { header: 'Priority Level', key: 'priority_level'},
        { header: 'Document Type', key: 'document_type'},
        { header: 'Status', key: 'status'},
        { header: 'Page Count', key: 'page_count'},
        { header: 'Attachment Page Count', key: 'attachment_page_count'},
        { header: 'Originating Office', key: 'origin_office'},
        { header: 'Destination', key: 'destination'},
        { header: 'Remarks', key: 'remarks'},
    ]

    data.forEach((e, index) => {
        let destination_list = ''

        e.destination.forEach(element => destination_list += element.name + ', ');

        worksheet.addRow({
            tracking_code: e.tracking_code,
            subject: e.subject,
            sender: e.sender?.name,
            priority_level: priority_list[e.priority_level-1],
            document_type: e.document_type['name'],
            status: e.status,
            page_count: e.page_count,
            attachment_page_count: e.attachment_page_count,
            origin_office: e.origin_office['name'],
            destination: destination_list.slice(0, -2),
            remarks: e.remarks,
        })

    })

    let columnWidth = {}
    worksheet.eachRow({ includeEmpty: false }, function (row, rowNumber) {
        const headerColumns = ['A','B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K']
        headerColumns.forEach((v) => {
            let currentColumnLength = worksheet.getCell(`${v}${rowNumber}`)?.value?.toString().trim()?.length

            if(columnWidth[v] == undefined){
                columnWidth[v] = currentColumnLength
            }else{
                columnWidth[v] = (columnWidth[v] <  currentColumnLength) ? currentColumnLength : columnWidth[v]
            }
        })
    })
    Object.values(columnWidth).forEach((width, index) => {
        worksheet.getColumn(index+1).width = width + 5

    });

    style({ worksheet:worksheet, headercount:11 })
    download({ filename:'Document Archive Master List', workbook:workbook })
}