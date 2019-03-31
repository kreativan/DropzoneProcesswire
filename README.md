# Dropzone
Dropzone.js Module for ProcessWire 

This module is built with front-end page edits in mind, for managing files and images on a page.     
It can also post aditional data along with the files, or submit the form after ajax uplaod, so you can use it to edit any page field, and make front-end page editing forms.     
Actually, you can use it for all kind of forms (that needs files handling), like sending emails with attachemnts etc...

What Dropzone module can do:    
* It can uplaod your files to a specified destination.
* It can post aditional data along with the files.
* It can submit your form as normal after uploading files.
* It can load existing files/images into the field.
* It sends post request on file remove, so you can use it to delete files from a server or a page. 
* Yes, it can remove images/file from processwire page field.
* And yeah, it can add images/files toa  processeire page field.
* It has bult in methods for adding and removing fields froma page field 
* If you have a debug mode enabled, it will console.log json response from php so you can review and debug posted data.
* It has basic form and file validations.
* It has number (2+3=?) captcha (optional) and custom honeypot spam protection.
* It can submit all your form fields not just files.
* It's using Sweet Alert plugin to display alerts and notifications.

### Basic Usage

```
<form id="dropzone-form" action="./" method="POST">

    <?php

        // dropzone params
        $params = [
            "url" => "./", // url where u want to post data (required)
            "formID" => "dropzone-form", // form css ID, requierd if u want to submit the form and rest of the form fields (recomended)
            "buttonID" => "submit-dropzone", // submit button css ID, default = submit-dropzone (required)
            "submitForm" => "false", // submit form after dropzone Ajax request, default = false (optional)
            "redirect" => "true", // redirect to the same page after modal confim, only works if submitForm = false
            "acceptedFiles" => "image/*", // allowed files (.jpg,.png,.pdf), default = image/*
            "maxFiles" => 10, // max number of files allowed, default = 5 (optional)
            "maxFilesize" => 0.3, // max file size allowed in MB, default = 0.3 (optional)
            'thumbnailWidth' => 140, // thumbnail width, default = 120 (optional)
            'thumbnailHeight' => 140, // thumbnail height, default = 120 (optional)
            // Array of existing images/files. ["url" => "", "name" => "", "size" => ""]
            "my_files" => $dropzone->getPageFiles($pages->get("/")->images),
        ];

        // send aditional data
        $data = [
            "name" => "Kreativan",
            "page_id" => $page->id,
            "page_name" => $page->name,
        ];

        echo $dropzone->loadDropzone($params, $data);

    ?>

    <div>
        <?php
            // Optional Captcha 
            echo $dropzone->renderCaptcha();
        ?>
    </div>

    <div>
        <!-- NOTE: button name needs to be != submit -->
        <input id="submit-dropzone" class="uk-button uk-button-primary" type="submit" name="dropzoneSubmit" value="Submit" />
    </div>

</form>
```