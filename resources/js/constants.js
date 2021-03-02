export const document_types = [
    'Executive Order',
    'Provincial Ordinance',
    'Letter',
    'Purchase Order',
    'Salary',
    'Budget',
    'Reports',
    'Draft',
    'Others'
];

export const colors = [
    '#F06292',
    '#E53935',
    '#8E24AA',
    '#5E35B1',
    '#3949AB',
    '#1E88E5',
    '#039BE5',
    '#0097A7',
    '#00897B',
    '#43A047',
    '#689F38',
    '#AFB42B',
    '#F9A825',
    '#FFA000',
    '#FB8C00',
    '#F4511E',
    '#6D4C41',
    '#455A64',
    '#424242'
];

export const snackbar_status = {
    success: '#5BB55E',
    info: '#359FF4',
    warning: '#FFA117',
    error: '#F45448'
};

export const snackbar_icon = {
    success: 'mdi-check-bold',
    info: 'mdi-information-outline',
    warning: 'mdi-exclamation-thick',
    error: 'mdi-close-thick'
};

export const priority_level = {
    4 : '#64DD17',
    3 : '#03A9F4',
    2 : '#FFA726',
    1 : '#F44336'
};

export const breakpoint = (col) => {
    if (Array.isArray(col)) {
        return {
            cols: "12",
            xs: col[0] || 12,
            sm: col[1] || 12,
            md: col[2] || 12,
            lg: col[3] || 12,
            xl: col[4] || 12
        }
    } else {
        return {
            cols: "12",
            xs: col || 12,
            sm: col || 12,
            md: col || 12,
            lg: col || 12,
            xl: col || 12
        }
    }
};

export const xstyle = (excel) => {
    var worksheet = excel.worksheet
    var headerColumns = Array.from({length: excel.headercount}, (x,i)=>(i+10).toString(36).toUpperCase())
    worksheet.eachRow({ includeEmpty:false }, (row, rowNumber) => {
        headerColumns.forEach((columnLetter) => {
            if (rowNumber == 1){
                worksheet.getCell(`${columnLetter}${rowNumber}`).style = {
                    fill: {type:'pattern', pattern:'solid', fgColor:{argb:excel.headercolor}},
                    font: {color:{argb:"ffffff"}, bold:true}
                }
            }else{
                worksheet.getCell(`${columnLetter}${rowNumber}`).style = {
                    border: {top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'}}
                }
            }
        })
    }); worksheet.views = [{state:'frozen', xSplit:0, ySplit:1, activeCell:'B2'}];
}