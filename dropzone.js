/**
 *  Dropzone init
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *  @var dropzoneVars object, dropzone options
 *  @var dropzoneData object data we want to POST along with files
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
    addRemoveLinks: dropzoneVars.addRemoveLinks,
    timeout: 180000,
    dictRemoveFileConfirmation: dropzoneVars.dictRemoveFileConfirmation, // ask before removing file
    // Language Strings
    dictFileTooBig: dropzoneVars.textMaxSize + " {{maxFilesize}}mb",
    dictInvalidFileType: dropzoneVars.textFileType,
    dictCancelUpload: dropzoneVars.textCancel,
    dictRemoveFile: dropzoneVars.textRemove,
    dictMaxFilesExceeded: "{{maxFiles}}" + dropzoneVars.textMaxFiles,
    dictDefaultMessage: dropzoneVars.textMessage,
});


myDropzone.on("addedfile", function(file) {
    console.log(file);
});


myDropzone.on("removedfile", function(file) {

    // console.log(file);
    
    // send ajax request when file removed
    var formData = new FormData();
    formData.append('dropzoneRemove', '1');
    formData.append('file_name', file.name);
    formData.append('accepted', file.accepted);
    formData.append('type', file.type);

    // added custom data to the remove request
    for (let fieldName in dropzoneData) {
        if (dropzoneData.hasOwnProperty(fieldName)) {
            formData.append(fieldName, dropzoneData[fieldName]);
        }
    }

    fetch(dropzoneVars.url, {
        method: 'POST',
        body:  formData
    })
    .then(function(response) {
        return response.json();
    })
    .then( function(response) {
        if(dropzoneVars.debug === true) console.log(response);
    });
    
});


// Add more data to send along with the file as POST data. (optional)
myDropzone.on("sending", function(file, xhr, formData) {
    formData.append('dropzoneSubmit', '1');
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
    if(dropzoneVars.debug === true) console.log(response);
    //console.log(file);

    if(dropzoneVars.submitForm === true) {
        dropzoneFormSubmit();
    }

});



/**
 *  Submit form based on the css ID
 *  @var dropzoneVars.formID
 * 
 */
function dropzoneFormSubmit() {

    let form = (dropzoneVars.formID != "") ? document.getElementById(dropzoneVars.formID) : "";

    if(form) {
        form.submit();
    } else {
        console.error("Form ID is missing or wrong");
    }

}



/**
 *  Trigger on button click
 *  processingQueue and (optionally) submit the forma after success
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
                if(dropzoneVars.submitForm === true) {
                    dropzoneFormSubmit();
                }
            }

        });
    } else {
        console.error("Submit button ID is missing or wrong");
    }

}
submitDropzone();