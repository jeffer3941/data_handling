$("#FileCSV").change(function (evt) {
    let selectedFile = evt.target.files[0];
    let reader = new FileReader();
    reader.onload = function (event) {
        let data = event.target.result;
        let workbook = XLSX.read(data, {
            type: 'binary'
        });
        workbook.SheetNames.forEach(function (sheetName) {
            let XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
            let json_object = JSON.stringify(XL_row_object);
            console.log(JSON.parse(json_object));
            
        })
    };
    reader.onerror = function (event) {
        $('#startProgress').addClass('disabled');
        $('#cancelProgress').addClass('disabled');
        console.error("File could not be read! Code " + event.target.error.code);
    };
    reader.readAsBinaryString(selectedFile);
});
