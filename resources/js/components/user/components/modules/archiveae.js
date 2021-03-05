import {download} from './cmd';
import {columnHeader, addRowData, xStyle} from './docuprop';

export function archiveae(param) {
    const Excel = require('exceljs');
    const data = param.data
    const selected = param.selected

    let workbook = new Excel.Workbook()

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
                    var exist = fsel.every(oi=>docu.destination.map(des=>des.id).includes(oi)) || fsel.includes(docu.origin_office.id)
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


