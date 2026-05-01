const readUploadedFileAsText = (inputFile) => {
    const temporaryFileReader = new FileReader();

    return new Promise((resolve, reject) => {
        temporaryFileReader.onerror = () => {
            temporaryFileReader.abort();
            reject(new DOMException("Problem parsing input file."));
        };

        temporaryFileReader.onload = () => {
            resolve(temporaryFileReader.result);
        };
        temporaryFileReader.readAsDataURL(inputFile);
    });
};


class Imx {
    constructor() {
    }

    /**
     *  Serializa un formulario a un objeto complejo 
     * @param {*} formulario 
     * @returns 
     */
    static async getBase64(file) {
        try {
            const fileContents = readUploadedFileAsText(file).then(function (result) {
                return result;
            })
            return fileContents;

        } catch (e) {
            console.warn(e.message);
            return e.message;

        }
    }

    static async serialize(formulario) {
        var dataObj = {},
            inputType,
            label;
        await Promise.all([...$('#' + formulario + ' input, #' + formulario + ' select , #' + formulario + ' textarea')].map(async input => {
            input = $(input);
            var fieldName = input.attr('name');
            if (fieldName != undefined) {
                fieldName = fieldName.replace(/[\[\]']+/g, '');
                var valor = input.val();
                var data = [];

                if ($('#' + fieldName).hasClass("select2-hidden-accessible")) {
                    inputType = 'select';
                    label = input.text().trim();

                }
                else if ($("#" + fieldName).is('select')) {
                    inputType = 'select';
                    label = $("#" + input.name + " :selected").text().trim();

                } else if (input.attr('type') == "file") {
                    inputType = 'file';
                    label = "";
                    var i;
                    for (i = 0; i < document.getElementById(input.attr('name')).files.length; i++) {
                        var fileb64 = await Imx.getBase64(document.getElementById(input.attr('name')).files[i]);

                        data[i] = {
                            name: document.getElementById(input.attr('name')).files[i].name,
                            size: document.getElementById(input.attr('name')).files[i].size,
                            type: document.getElementById(input.attr('name')).files[i].type,
                            data: fileb64
                        }

                    }

                }
                else {
                    inputType = 'text';
                    label = input.value;

                }
                dataObj[fieldName] = { "label": label, "type": inputType, "value": valor, "data": data };
            }
        }))


        return dataObj;

    }
    // ahora vemos si tenemos tipo file


    /**
     *  valida si un string es tipo Json
     * @param {*} str 
     * @returns 
     */
    static isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    /** 
     *  Funcion para serializar todos los formularios y enviarlos a una api 
     */

    static async validaForm(path, validarResponse = true, skip = [], extra = "", extra1 = "", extra2 = "", confirma = true, isfile = false) {
        var payload = [];
        let formsCollection = document.forms;
        let r;
        let isvalid = true;
        for (r = 0; r < formsCollection.length; r++) {
            if (formsCollection[r].id.substr(0, 2) == "x_") {
                continue;
            }
            let formValidationObject = $('#' + formsCollection[r].id).parsley();
            if ((skip.indexOf(formsCollection[r].id) > -1)) {
                formValidationObject.destroy();
            }
            else {
                // validamos form 1
                formValidationObject.validate();
                if (!formValidationObject.isValid()) {
                    isvalid = false;
                }
            }
            var formdata = await Imx.serialize(formsCollection[r].id);
            var thisform = {
                'form': formsCollection[r].id,
                'data': formdata
            };
            payload.push(thisform);
        }
        if (extra) {
            payload.push(extra);
        }
        if (extra1) {
            payload.push(extra1);
        }
        if (extra2) {
            payload.push(extra2);
        }
        if (!isvalid) {
            alert('Please check all required input fields are filled ');
            return false;
        }
        if (confirma && !confirm('Do You want to save the changes?')) {

            return false;
        }
        // BLOQUEAR 
        $('#loader').fadeIn();

        Imx.post(payload, path, validarResponse);
    }
    static async post(payload, path, validarResponse) {
        await axiosPost(payload, path, function (result) {
            if (validarResponse) {
                $('#loader').fadeOut();

                let estatus = result.status ?? "";
                if (estatus == "ok") {
                    callbackForm(result);
                    return true;
                }
                if (estatus == "error") {
                    alert(result.text);
                    return false;
                }
                else {

                    alert("Server send an invalid response");
                    // DESBLOQUEAR
                    return false;
                }
            }
            else {
                //DESBLOQUEAR
                $('#loader').fadeOut();
                callbackForm(result);

            }

        }).catch(function (err, result) {
            $('#loader').fadeOut();
            if (err.response === undefined) {
                alert('Error JS ' + err);
                return false;
            }
            if (err.response.status == 500) {
                alert('Error ' + error.response.status, 'Error de API');
                return false;

            } else {
                return false;
            }
        });
    }

}