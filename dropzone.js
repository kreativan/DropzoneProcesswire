/**
 *  Dropzone init
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *  @param {object} dropzoneVars - dropzone variables
 *  @param {object} dropzoneData - data we want to POST along with files
 *  @param {object} dropzoneText - text strings
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
    dictFileTooBig: dropzoneText.max_size + " {{maxFilesize}}mb",
    dictInvalidFileType: dropzoneText.file_type,
    dictCancelUpload: dropzoneText.cancel,
    dictRemoveFile: dropzoneText.remove,
    dictMaxFilesExceeded: "{{maxFiles}} " + dropzoneText.max_files,
    dictDefaultMessage: dropzoneText.message,
    renameFile: function (file) {
      const fileName = file.name;
      let fileExt = "";
      // use this to check if file ext is 3 or 4 chars
      // eg: jpg or jpeg
      let n = fileName.length - 5;
      let getTheDot = fileName[n];
      if(getTheDot == ".") {
        fileExt = fileName.substr(fileName.length - 4);
      } else {
        fileExt = fileName.substr(fileName.length - 3);
      }
      // random date
      let random = + new Date(); 
      let newName = dropzoneVars.renameFilePrefix + "-" + random + "." + fileExt;
      if(dropzoneVars.renameFile) return newName;
      //console.log(newName);
    },
});


myDropzone.on("addedfile", function(file) {
    // console.log(file);
    dropzoneRemoveButton(file, this);
});


myDropzone.on("error", function(file, response) {
    console.log(response);
});


// Add more data to send along with the file as POST data. (optional)
myDropzone.on("sending", function(file, xhr, formData) {

    formData.append('dropzoneAjax', '1');

    // append form fields
    dropzoneAppendFormFelds(formData);

    // append custom data
    for (let fieldName in dropzoneData) {
        if (dropzoneData.hasOwnProperty(fieldName)) {
            formData.append(fieldName, dropzoneData[fieldName]);
        }
    }


});

// Do something while processing is in progress
myDropzone.on("processingmultiple", function(file) {
    document.getElementById(dropzoneVars.formID).classList.add("dropzone-processing");
    document.getElementById(dropzoneVars.buttonID).setAttribute("disabled", "disabled");
});

// Do something while processing is complete
myDropzone.on("completemultiple", function(file) {
    document.getElementById(dropzoneVars.formID).classList.remove("dropzone-processing");
    document.getElementById(dropzoneVars.buttonID).removeAttribute("disabled");
});

// on success
myDropzone.on("successmultiple", function(file, response) {

    // get response from successful ajax request
    if (dropzoneVars.debug === true) console.log(response);

    if (dropzoneVars.submitForm === true) {

        // submit the form as normal
        dropzoneFormSubmit();

    } else if (response.status && response.message) {
        
        Swal.fire({

            title: response.status, 
            text: response.message, 
            type: response.status,

        }).then((result) => {
            
            if (dropzoneVars.redirect === true && response.status != "error") {
                window.location.href = dropzoneVars.redirectUrl;
            } else if (response.status != "error") {
                dropzoneResetFields();
                //this.removeAllFiles();
            }

            this.removeAllFiles();

        });
        
            
    }

});


/**
 *  Add existing images to the dropzone
 *  We got imaegs from dz variable we passed from php
 *  @param {object} dropzoneVars.my_files
 *
 */
for(let i = 0; i < dropzoneVars.my_files.length; i++) {

    let img = dropzoneVars.my_files[i];
    //console.log(img);

    // Create the mock file:
    var mockFile = {name: img.name, size: img.size, url: img.url};
    // Call the default addedfile event handler
    myDropzone.emit("addedfile", mockFile);
    // And optionally show the thumbnail of the file:
    myDropzone.emit("thumbnail", mockFile, img.url);
    // Make sure that there is no progress bar, etc...
    myDropzone.emit("complete", mockFile);
    // If you use the maxFiles option, make sure you adjust it to the
    // correct amount:
    var existingFileCount = 1; // The number of files already uploaded
    myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;


}


/**
 *  Trigger on button click
 *  processingQueue and (optionaly) submit the form after sending files
 *  @param {string} dropzoneVars.buttonID
 *  @function dropzoneFormSubmit() - submits the form
 *  @function dropzoneFormValidate() - validates the form
 *  @function dropzoneCaptcha() - validate captcha
 * 
 */
function submitDropzone() {

    let submitDropzone = (dropzoneVars.buttonID != "") ? document.getElementById(dropzoneVars.buttonID) : "";

    if(submitDropzone) {
        submitDropzone.addEventListener("click", function(e) {
            // Make sure that the form isn't actually being sent.
            e.preventDefault();
            e.stopPropagation();

            validateForm = dropzoneFormValidate();
            captcha = dropzoneCaptcha();
            honeypot = document.querySelector("input[name=dropzoneHoneypot]").value;

            if(validateForm.status === true && !honeypot && captcha === true) {

                if (myDropzone.files != "") {
                    //console.log(myDropzone.files);
                    myDropzone.processQueue();
                } else {
                    dropzoneFormSubmit();
                }

            } else {
                if (dropzoneVars.debug === true) console.log(validateForm);
                Swal.fire({
                    title: dropzoneText.form_invalid, 
                    text: dropzoneText.check_fields+" "+`(${validateForm.errors})`, 
                    type: 'warning'
                });
            }

        });
    } else {
        console.error("Submit button ID is missing or wrong");
    }
	
	
		//
    //  Additional buttons to submit the form, based on buttonSelector
    //

    let submitButtons = (dropzoneVars.buttonSelector) ? document.querySelectorAll(dropzoneVars.buttonSelector) : "";

    if(submitButtons) {
      submitButtons.forEach(elem => {
        elem.addEventListener("click", function(e) {

          e.preventDefault();
          e.stopPropagation();

          validateForm = dropzoneFormValidate();
          captcha = dropzoneCaptcha();
          honeypot = document.querySelector("input[name=dropzoneHoneypot]").value;

          if(validateForm.status === true && !honeypot && captcha === true) {
            if (myDropzone.files != "") {
                //console.log(myDropzone.files);
                myDropzone.processQueue();
            } else {
                dropzoneFormSubmit();
            }
          }

        });
        
      });
    }

}
submitDropzone();


/* ======================================================================
    Functions
====================================================================== */


/**
 *  Submit form 
 *  based on the form css ID
 *  @param {string} dropzoneVars.formID
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
 *  @param {string} dropzoneVars.formID
 *  
 */
function dropzoneResetFields() {

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
 *  @param {object} formData = new FormData();
 * 
 */
function dropzoneAppendFormFelds(formData) {

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
 *  @param {object} file         - dropzone file // requierd
 *  @param {object} _this        - this // this dropzone instance
 *  @param {object} dropzoneData   - object // custom data provided by php
 * 
 */
function dropzoneRemoveReq(file, _this) {

    // create form data to send
    var formData = new FormData();
    formData.append('dropzoneRemove', '1');
    formData.append('file_url', file.url);
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
 *  @param {object} file   dropzone file // requierd
 *  @param {object} _this  this // this dropzone instance
 *  @function dropzoneRemoveReq() // send the file remove request   
 * 
 */
function dropzoneRemoveButton(file, _this) {

    var removeButton = Dropzone.createElement("<button class='dropzone-remove'><i class='fas fa-times'></i></button>");
    //var _this = this;

    // Listen to the click event
    removeButton.addEventListener("click", function(e) {
        // Make sure the button click doesn't submit the form:
        e.preventDefault();
        e.stopPropagation();

        // Remove the file preview.
        //_this.removeFile(file);
        //console.log(file.name);

        Swal.fire({
            title: dropzoneText.are_you_sure,
            type: "question",
            showCancelButton: true,
        }).then((result) => {
            if (result.value) {
				_this.removeFile(file);
                dropzoneRemoveReq(file, _this);
            }
        });
        


    });

    // Add the button to the file preview element.
    file.previewElement.appendChild(removeButton);

}


/**
 *  Validate Form
 *  @var  dropzoneVars.formID 
 *  @return object response object
 *  @example dropzoneFormValidate().status;
 */
function dropzoneFormValidate() {

    let errors = "";

    let formID = "#"+dropzoneVars.formID;
    let selector = `${formID} input:not(.uk-hidden), ${formID} textarea, ${formID} select, ${formID} radio, ${formID} checkbox`;
    let fields = document.querySelectorAll(selector);

    fields.forEach(e => {

        if(e.checkValidity() === false) {
            let name = e.getAttribute("name");
            errors += (errors == "") ? name  : "," + name;
        }

    });

    var validate = (errors != "") ? false : true;
    var response = {
        "status": validate,
        "errors": errors
    }

    // console.log(fields)
    // console.log(response)    

    return response;

}


/**
 *  Validate numb Captcha
 *  @return bool
 * 
 */
function dropzoneCaptcha() {

    let isCaptchaOn =  document.getElementById("numb-captcha-answer");
    
    if(isCaptchaOn) {

        let answer = document.getElementById("numb-captcha-answer").value;
        let question = document.getElementById("numb-captcha-q").value;
        
        if(answer === question) {
            return true;
        } else {
            return false;
        }

    } else {

        return true;
        
    }

}
