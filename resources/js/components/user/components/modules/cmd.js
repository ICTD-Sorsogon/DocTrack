export const style = (excel) => {
    var worksheet = excel.worksheet
    var headerColumns = Array.from({length: excel.headercount}, (x,i)=>(i+10).toString(36).toUpperCase())
    worksheet.eachRow({ includeEmpty:false }, (row, rowNumber) => {
        headerColumns.forEach((columnLetter) => {
            if (rowNumber == 1){
                worksheet.getCell(`${columnLetter}${rowNumber}`).style = {
                    fill: {type:'pattern', pattern:'solid', fgColor:{argb:'0675BB'}},
                    font: {color:{argb:"ffffff"}, bold:true}
                }
            }else{
                worksheet.getCell(`${columnLetter}${rowNumber}`).style = {
                    border: {top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'}}
                }
            }
        })
    }); worksheet.views = [{state:'frozen', xSplit:0, ySplit:1, activeCell:'B2'}];
};

export const download = (excel) => {
    var workbook = excel.workbook
    var filename = excel.filename

    var buff = workbook.xlsx.writeBuffer().then(function (data) {
        var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        const date = new Date();
        link.setAttribute('download', `${filename} _${date.getFullYear()}${date.getMonth()+1}${date.getDate()}.xlsx`);
        document.body.appendChild(link);
        link.click();
    });
}