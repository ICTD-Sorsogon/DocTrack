import {style, download} from './cmd';

export default function archiveae(param) {
    const Excel = require('exceljs');
    const data = param.data
    const selected = param.selected

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
        const headerColumns = ['A','B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']
        let cWidth = {}
        worksheet.eachRow({ includeEmpty: false }, function (row, rowNumber) {
            headerColumns.forEach((cLetter) => {
                let cLength = worksheet.getCell(`${cLetter}${rowNumber}`).value?.toString().trim()?.length ?? 0
                if(cWidth[cLetter] == undefined){
                    cWidth[cLetter] = cLength
                }else{
                    cWidth[cLetter] = (cWidth[cLetter] <  cLength) ? cLength : cWidth[cLetter]
                }
            })
        })
        Object.values(cWidth).forEach((width, index) => { worksheet.getColumn(index+1).width = width + 5 });
        style({ worksheet:worksheet, headercount:headerColumns.length })
    }

    function addRowData(worksheet, docu){
        let destination_list = ''
        docu.destination.forEach(element => destination_list += element.office_code + ', ');
        worksheet.addRow({
            tracking_code: docu.tracking_code,
            subject: docu.subject,
            sender: docu.sender?.name,
            document_type: docu.document_type['name'],
            status: docu.status,
            page_count: docu.page_count,
            attachment_page_count: docu.attachment_page_count,
            origin_office: docu.origin_office.office_code,
            destination: destination_list.slice(0, -2),
            remarks: docu.remarks,
        })
    }

    if (param.type == 'group') {
        selected.data.forEach((element) => {
            let source = ['Internal', 'External']
            let worksheet = selected.type == 'byOffice' ? workbook.addWorksheet(element.office_code) : workbook.addWorksheet(element)
            columnHeader(worksheet)
            if(selected.type == 'byOffice'){
                data.forEach((docu) => {
                    let destination = docu.destination.map(des => des.name)
                        if(docu.origin_office.name == element.name || destination.includes(element.name)){
                            addRowData(worksheet, docu)
                        }
                })
            } else if(selected.type == 'byType'){
                data.forEach((docu) => {
                    if(docu.document_type.name == element){
                        addRowData(worksheet, docu)
                    }
                })
            } else if(selected.type == 'bySource'){
                data.forEach((docu) => {
                    if(source[docu.is_external] == element){
                        addRowData(worksheet, docu)
                    }
                })
            }
            xStyle(worksheet)
        });
    } else {
        let worksheet = workbook.addWorksheet('Selected Custom Report')
        columnHeader(worksheet)
        data.forEach((docu, index) => {
            var satisfied = []
            Object.entries(param.filter).forEach((f)=>{
                if (f[0] == 'byOffice') {
                    var fsel = f[1].map(o=>o.id)
                    var exist = fsel.every(oi=>docu.destination.map(des=>des.id).includes(oi)) || f[1].map(o=>o.id).includes(docu.origin_office.id)
                    satisfied.push(exist)
                }
                if (f[0] == 'byType') {
                    satisfied.push(f[1].includes(docu.document_type.name))
                }
                if (f[0] == 'bySource') {
                    satisfied.push(f[1].includes((docu.is_external)?'External':'Internal'))
                }
            })
            if (satisfied.every(i=>i==true)) {
                addRowData(worksheet, docu)
            }
        })
        xStyle(worksheet)
    }
    download({ filename:'Document Custom Report', workbook:workbook })
}


