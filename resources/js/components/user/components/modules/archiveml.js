import {download} from './cmd';
import {columnHeader, addRowData, xStyle} from './docuprop';

export function archiveml(param) {
    const Excel = require('exceljs');

    const data = param.data
    let workbook = new Excel.Workbook()
    let worksheet = workbook.addWorksheet('Document Archive Master List')
    columnHeader(worksheet)

    data.forEach(docu => addRowData(worksheet, docu))

    xStyle(worksheet)
    download({ filename:'Document Archive Master List', workbook:workbook })
}
