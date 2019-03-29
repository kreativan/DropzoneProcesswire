/**
 *  Dropzone init
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *  @var dropzoneVars object // dropzone variables
 *  @var dropzoneData object // data we want to POST along with files
 * 
 *
*/

// console.log(dropzoneVars);
// console.log(dropzoneData);

Dropzone.autoDiscover = false;

var myDropzone = new Dropzone("#"+dropzoneVars.id, {
    url: dropzoneVars.url,
    method: "POST",
    paramName: "dropzoneFiles", // default = file
    autoProcessQueue : false,
    acceptedFiles: dropzoneVars.acceptedFiles,
    maxFiles: dropzoneVars.maxFiles, // default = 5
    maxFilesize: dropzoneVars.maxFilesize, // MB default = 0.3
    uploadMultiple: dropzoneVars.uploadMultiple,
    parallelUploads: 100, // use it with uploadMultiple
    createImageThumbnails: dropzoneVars.createImageThumbnails,
    thumbnailWidth: dropzoneVars.thumbnailWidth,
    thumbnailHeight: dropzoneVars.thumbnailHeight,
    // addRemoveLinks: dropzoneVars.addRemoveLinks,
    timeout: 180000,
    // dictRemoveFileConfirmation: dropzoneVars.dictRemoveFileConfirmation, // ask before removing file
    // Language Strings
    dictFileTooBig: dropzoneVars.textMaxSize + " {{maxFilesize}}mb",
    dictInvalidFileType: dropzoneVars.textFileType,
    dictCancelUpload: dropzoneVars.textCancel,
    dictRemoveFile: dropzoneVars.textRemove,
    dictMaxFilesExceeded: "{{maxFiles}}" + dropzoneVars.textMaxFiles,
    dictDefaultMessage: dropzoneVars.textMessage,
});


myDropzone.on("addedfile", function(file) {
    // console.log(file);
    addCustomRemoveButton(file, this);
});


// Add more data to send along with the file as POST data. (optional)
myDropzone.on("sending", function(file, xhr, formData) {

    formData.append('dropzoneAjax', '1');

    // append form fields
    appendFormFields(formData);

    // append custom data
    for (let fieldName in dropzoneData) {
        if (dropzoneData.hasOwnProperty(fieldName)) {
            formData.append(fieldName, dropzoneData[fieldName]);
        }
    }


});


myDropzone.on("error", function(file, response) {
    console.log(response);
});


// on success
myDropzone.on("successmultiple", function(file, response) {

    // get response from successful ajax request
    if (dropzoneVars.debug === true) console.log(response);

    if (dropzoneVars.submitForm === true || !file) {

        dropzoneFormSubmit();

    } else if (response.status && response.message) {
        swal(
            title = response.status, 
            text = response.message, 
            icon = response.status,
        )
        .then((value) => { 
            if (dropzoneVars.redirect === true && response.status != "error") {
                window.location.href = dropzoneVars.current_url;
            } else if (response.status != "error") {
                resetFormFields();
                this.removeAllFiles();
            }
        });
            
    }

});


/**
 *  Trigger on button click
 *  processingQueue and (optionaly) submit the form after sending files
 *  @var dropzoneVars.buttonID
 * 
 */
function submitDropzone() {

    let submitDropzone = (dropzoneVars.buttonID != "") ? document.getElementById(dropzoneVars.buttonID) : "";

    if(submitDropzone) {
        submitDropzone.addEventListener("click", function(e) {
            // Make sure that the form isn't actually being sent.
            e.preventDefault();
            e.stopPropagation();

            if (myDropzone.files != "") {
                // console.log(myDropzone.files);
                myDropzone.processQueue();
            } else {
                dropzoneFormSubmit();
            }

        });
    } else {
        console.error("Submit button ID is missing or wrong");
    }

}
submitDropzone();


/**
 *  Submit form 
 *  based on the form css ID
 *  @var dropzoneVars.formID
 * 
 */
function dropzoneFormSubmit() {

    let form = (dropzoneVars.formID != "") ? document.getElementById(dropzoneVars.formID) : "";

    if(form) {
        var input = document.createElement("input");
        input.setAttribute("type", "text");
        input.setAttribute("name", "dropzoneSubmit");
        input.setAttribute("value", "1");
        input.setAttribute("hidden", "hidden");
        form.appendChild(input);
        form.submit();
    } else {
        console.error("Form ID is missing or wrong");
    }

}

/**
 *  Reset form fields values
 *  use this after ajax form submit
 *  @var dropzoneVars.formID
 *  
 */
function resetFormFields() {

    let id = "#" + dropzoneVars.formID;
    let selector = `${id} input:not(.uk-hidden), ${id} textarea`;
    let formFields = document.querySelectorAll(selector);

    formFields.forEach(e => {

        // do not include submitButton
        if(e.id != dropzoneVars.buttonID) {
            e.setAttribute("value", "");
        }

    });

}


/**
 *  Get all form fields
 *  and append them to form data 
 *  @param formData = new FormData();
 * 
 */
function appendFormFields(formData) {

    formData = (formData) ? formData : new FormData();

    if(formData) {

        let id = "#" + dropzoneVars.formID;
        let selector = `${id} input:not(.uk-hidden), ${id} textarea, ${id} select, ${id} radio, ${id} checkbox`;
        let formFields = document.querySelectorAll(selector);

        formFields.forEach(e => {

            // do not include submitButton
            if(e.id != dropzoneVars.buttonID) {
                //console.log(e.name + " = " + e.value);
                formData.append(e.name, e.value);
            }

        });

    }
    
}


/**
 *  Send file remove Request
 *  @param file         dropzone file // requierd
 *  @param _this        this // this dropzone instance
 *  @var dropzoneData   object // custom data provided by php
 * 
 */
function dropzoneRemoveReq(file, _this) {

    // create form data to send
    var formData = new FormData();
    formData.append('dropzoneRemove', '1');
    formData.append('file_name', file.name);
    formData.append('accepted', file.accepted);
    formData.append('type', file.type);

    // added custom data
    for (let fieldName in dropzoneData) {
        if (dropzoneData.hasOwnProperty(fieldName)) {
            formData.append(fieldName, dropzoneData[fieldName]);
        }
    }

    // use fetch to send post request
    fetch(dropzoneVars.url, {
        method: 'POST',
        body:  formData
    })
    .then(function(response) {
        return response.json();
    })
    .then( function(response) {
        if(dropzoneVars.debug === true) console.log(response);
        if(response.status && response.message && response.status == "error" ) {
            swal(response.status, response.message, response.status);
        } else {
            _this.removeFile(file);
        }
    });

}


/**
 *  Add custom file remove button 
 *  so we can use confirm modal
 *  @param file   dropzone file // requierd
 *  @param _this  this // this dropzone instance
 * 
 */
function addCustomRemoveButton(file, _this) {

    var removeButton = Dropzone.createElement("<button class='dropzone-remove'>Remove</button>");
    //var _this = this;

    // Listen to the click event
    removeButton.addEventListener("click", function(e) {
        // Make sure the button click doesn't submit the form:
        e.preventDefault();
        e.stopPropagation();

        // Remove the file preview.
        //_this.removeFile(file);
        //console.log(file.name);

        swal({
            title: dropzoneVars.textAreYouSure,
            buttons: true,
        })
        .then((ok) => {
            if (ok) {
                dropzoneRemoveReq(file, _this);
            } 
        });
        


    });

    // Add the button to the file preview element.
    file.previewElement.appendChild(removeButton);

}