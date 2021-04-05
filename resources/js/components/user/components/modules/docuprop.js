import {style} from './cmd';

export function columnHeader(worksheet) {
    worksheet.columns = [
        { header: 'Tracking Code', key: 'tracking_code'},
        { header: 'Subject', key: 'subject'},
        { header: 'Document Source', key: 'is_external'},
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

export function addRowData(worksheet, docu){
    let destination_list = ''
    docu.destination.forEach(element => destination_list += element.office_code + ', ');
    worksheet.addRow({
        tracking_code: docu.tracking_code,
        subject: docu.subject,
        is_external: (docu.is_external)?'External':'Internal',
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

export function xStyle(worksheet){
    style({ worksheet:worksheet, headercount:11, autowidth:true })
}