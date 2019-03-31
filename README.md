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

### Methods
```
<?php
$dropzone = $modules->get("Dropzone);

// init dropzone inside the form    
$dropzone->loadDropzone($params, $data);   

// uplaod files using vanila php    
$dropzone->uploadPHP($dest)    

// upload files using WireUpload class
$dropzone->wireUpload($dest, $allowed_files)

// get page images/files to add to dropzone field    
$dropzone->getPageFiles($page->images)

// add image to page,    
$dropzone->fileToPage($page, $field_name = "images", $allowed_files = [])

// delete image from a page     
$dropzone->deletePageFile($page, "images")   

// check if image exists on a page                 
$dropzone->fileExists($page, $filed_name = "images", $file_name = "example.jpg")

// fileToPage() + json response 
$dropzone->addFile($page, $field_name = "images", $allowed_files = ['jpg','gif', 'png']);

// deletePageFile() + json response    
$dropzone->removeFile($page, $field_name = "images", $allowed_files = ['jpg', 'gif', 'png']);

// Sweet Alert init
$dropzone->swal("Title", "Text", "success/warning/error/info");

// render numb captcha
$dropzone->renderCaptcha()  
```


### Params
Only required parametars are: url, formID and buttonID
```
$params = [
    "url" => "./", // url where u want to post data (required)
    "formID" => "dropzone-form", // form css ID, (requierd)
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
```
### Data
Send aditional data along with the files
```
$data = [
    "name" => "Kreativan",
    "page_id" => $page->id,
    "page_name" => $page->name,
];

echo $modules->get("Dropzone)->loadDropzone($params, $data);
```

## Basic Usage - File Uplaod
Build a form and load dropzone inside
```
<form id="dropzone-form" action="./" method="POST">

    <?php
        // set params
        $params = [
            "url" => $page->url,
            "formID" => "dropzone-form",
            "buttonID" => "submit-dropzone",
        ];
        
        // init dropzone
        echo $modules->get("Dropzone")->loadDropzone($params);
    ?>

    <!-- NOTE: button name needs to be != submit -->
    <div class="uk-margin">
        <input id="submit-dropzone" class="uk-button uk-button-primary" type="submit" name="dropzoneSubmit" value="Submit" />
    </div>

</form>
```

Process the form. Note that this part should be on top of your file, before incldouding anything else.
```
<?php

$dropzone = $modules->get("Dropzone");

if($input->post->dropzoneAjax) {

    // error will be in console.log if debug mode is on
    $error = "";

    try {

        // upload file to /site/templates/temp/ folder
        echo $dropzone->wireUpload($config->paths->templates . "temp/");

        // set response vars
        $status = "success";
        $message = "Uplaod Complete!";

    } catch (Exception $e) {

        // set response vars
        $status = "error";
        $message = "Uplaod faild!";
        $error = $e->getMessage();

    }

    /**
     *	Response 
     *	return json response to the dropzone
     *  (this will trigger Sweet Alert)
     */
    $data = [
        "status" => "$status",  // for sweet alert
        "message" => "$message", // for sweet alert
        "error" => $error,  // so we can review and debug in console.log
        "files" => $_FILES, // so we can review and debug in console.log
        "post" => $_POST, // so we can review and debug in console.log
    ];
    
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}

?>
```

### Add/Remove images on a page
Form
```
<form id="dropzone-form" action="./" method="POST">

    <?php
        // set params
        $params = [
            "url" => $page->url,
            "formID" => "dropzone-form",
            "buttonID" => "submit-dropzone",
            // load existing images to dropzone field
            "my_files" => $dropzone->getPageFiles($pages->get("/")->images),
        ];
        
        // init dropzone
        echo $modules->get("Dropzone")->loadDropzone($params);
    ?>

    <!-- NOTE: button name needs to be != submit -->
    <div class="uk-margin">
        <input id="submit-dropzone" class="uk-button uk-button-primary" type="submit" name="dropzoneSubmit" value="Submit" />
    </div>

</form>
```
Process
```
// Add image to a page
if($input->post->dropzoneAjax) {
    
    $p = $pages->get("/");
    $dropzone->addFile($p, "images");

}

// Remove Image
if($input->post->dropzoneRemove) {

    $p = $pages->get("/");
    $img_field = "images";
    $img_name = $input->post->file_name;

    $dropzone->removeFile($p, $img_field, $img_name);

}
```