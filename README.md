# Dropzone
Dropzone.js Module for ProcessWire 

This module is built with front-end page edits in mind, for managing files and images on a page.     
It can also post aditional data along with the files, or submit the form after ajax uplaod, so you can use it to edit any page field, and make front-end page editing forms.     
Actually, you can use it for all kind of forms (that needs files handling), like sending emails with attachemnts etc...

What Dropzone module can do:    
* It can uplaod your files to a specified destination.
* It can post aditional data along with the images/files.
* It can submit your form as normal after uploading files.
* It can load existing images/files into the field.
* It sends post request on file remove, so you can use it to delete files from a server or a page. 
* Yes, it can remove images/file from processwire page.
* And yeah, it can add images/files to a processeire page.
* It has bult in methods for adding and removing files/images from a page.
* If you have a debug mode enabled, it will console.log json response from php so you can review and debug posted data.
* It has basic form and file validations.
* It has number (2+3=?) captcha (optional) and custom honeypot spam protection.
* It can submit all your form fields not just files.
* It's using Sweet Alert plugin to display alerts and notifications.


### Methods

```php
$dropzone = $modules->get("Dropzone");

// Init dropzone inside the form    
$dropzone->loadDropzone($params, $data);   

// Uplaod files using vanila php    
// File validation done by dropzone.js
$dropzone->uploadPHP($dest);    

// Upload files using WireUpload class
$dropzone->wireUpload($dest, $allowed_files = ['jpg', 'jpeg', 'gif', 'png']);

// Get page images/files to add to dropzone field 
// ["my_files" =>  $dropzone->getPageFiles($page->images)]  
$dropzone->getPageFiles($page->images);

// Add image/file to the page     
$dropzone->fileToPage($page, $field_name = "images", $allowed_files = ['jpg', 'jpeg', 'gif', 'png']);

// Delete image/file from a page     
$dropzone->deletePageFile($page, $field_name = "images"); 

// Add image/file to the page with a json response. fileToPage() + json response 
$dropzone->addFile($page, $field_name = "images", $allowed_files = ['jpg', 'jpeg', 'gif', 'png']);

// Delete image/file from a page with a jesnon respone. deletePageFile() + json response    
$dropzone->removeFile($page, $field_name = "images", $allowed_files = ['jpg', 'jpeg', 'gif', 'png']);

// Check if image exists on a page                 
$dropzone->fileExists($page, $filed_name = "images", $file_name = "example.jpg");

// Render numb captcha
// use it inside a form, no additional actions required
$dropzone->renderCaptcha();

// Sweet Alert init
$dropzone->swal("Title", "Text", "success/warning/error/question/info");

// Sweet Alert Mini init
$dropzone->swal("Title", "success/warning/error/question/info", "top-end");

```


### Params
Only required parametars are: url, formID and buttonID

```php
$params = [
    "url" => "./", // url where u want to post data (required)
    "formID" => "dropzone-form", // form css ID, (requierd)
    "buttonID" => "submit-dropzone", // submit button css ID, default = submit-dropzone (required)
    "submitForm" => "false", // submit form after dropzone Ajax request, default = false (optional)
    "redirect" => "true", // redirect to the same page after modal confim, only works if submitForm = false
	"redirectUrl" => "./",
    "acceptedFiles" => "image/*", // allowed files (.jpg,.png,.pdf), default = image/*
    "maxFiles" => 10, // max number of files allowed, default = 5 (optional)
    "maxFilesize" => 0.3, // max file size allowed in MB, default = 0.3 (optional)
    'thumbnailWidth' => 140, // thumbnail width, default = 120 (optional)
    'thumbnailHeight' => 140, // thumbnail height, default = 120 (optional)
    // Array of existing images/files. ["url" => "", "name" => "", "size" => ""]
    "my_files" => $dropzone->getPageFiles($page->images, "140"),
    "renameFile" => "true",
    "renameFilePrefix" => "my-image-prefix,
];

$data = [
    "name" => "Kreativan",
    "page_id" => $page->id,
    "page_name" => $page->name,
];

echo $modules->get("Dropzone")->loadDropzone($params, $data);
```

## How to use?
```php
<?php

   // Dropzone request (Ajax)
   if($input->post->dropzoneAjax) {

       // do something
       // uplaod images, process form...

        exit();

   } 

    // Dropzone remove request (Ajax)
    if($input->post->dropzoneRemove) {

       // do something
       // delete image from page or server...

        exit();

    } 

    // Form Submit with dropzone ($_POST)
    if($input->post->dropzoneSubmit) {

        // do something
        // this is "normal" form submit
        // do something with $_POST variables

    } 


?>

<form id="dropzone-form" action="./" method="POST">

    <input type="text" name="name" placeholder="Name" />

    <input type="email" name="email" placeholder="Email" />

    <?php
        // set params
        $params = [
            "url" => $page->url,
            "formID" => "dropzone-form",
            "buttonID" => "submit-dropzone",
        ];

        // more data
        $data = [
            "name" => "kreativan",
            "email" => "kreativan@outlook.com",
        ];
        
        // init dropzone
        echo $modules->get("Dropzone")->loadDropzone($params, $data);
    ?>

    <?php
        // Optional Captcha 
        echo $dropzone->renderCaptcha();
    ?>

    <!-- NOTE: button name needs to be != submit -->
    <input id="submit-dropzone" type="submit" name="dropzoneSubmit" value="Submit" />

</form>
```

## Test 
**TIP**: *Enable debug mode in /site/config.php and all responses sent back to dropzone will be logged in console*.  

Set the response and echo json to review your data in console.log

```php
<?php
if($input->post->dropzoneAjax) {

    // store errors so we can log them
    $error = "";

    /**
     *	Response 
     *	return json response to the dropzone
     *	@var data array
     */
    $data = [
        "status" => "success", // for sweet alert
        "message" => "Uplaod complete!", // for sweet alert
        "error" => $error, // log error
        "files" => $_FILES, // log $_FILES
        "post" => $_POST, // log $_POST
    ];

    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}
```


## Basic Usage - File Uplaod
Build a form and load dropzone inside

```php
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
    <input id="submit-dropzone" type="submit" name="dropzoneSubmit" value="Submit" />

</form>
```

Process the form. Note that this part should be on top of your file, before incldouding anything else.

```php
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

```php
<form id="dropzone-form" action="./" method="POST">

    <?php
        // set params
        $params = [
            "url" => $page->url,
            "formID" => "dropzone-form",
            "buttonID" => "submit-dropzone",
            // load existing images to dropzone field
            "my_files" => $dropzone->getPageFiles($page->images),
        ];
        
        // Send aditional data
        // In this case $page->id, so we know what page to edit
        $data = [
            "page_id" => $page->id,
        ];

        // init dropzone
        echo $modules->get("Dropzone")->loadDropzone($params, $data);
    ?>

    <!-- NOTE: button name needs to be != submit -->
    <input id="submit-dropzone" type="submit" name="dropzoneSubmit" value="Submit" />

</form>
```

Process

```php
<?php

// Add image to a page
if($input->post->dropzoneAjax) {
    
    $id = $sanitizer->int($input->post->page_id);
    $p = $pages->get("id=$id");

    $dropzone->addFile($p, "images");

}

// Remove Image
if($input->post->dropzoneRemove) {

    $id = $sanitizer->int($input->post->page_id);
    $p = $pages->get("id=$id");

    $img_field = "images";
    $img_name = $input->post->file_name;

    $dropzone->removeFile($p, $img_field, $img_name);

}
```

## Edit Page 
Add / remove images and edit other fields on a page.

```php
<form id="dropzone-form" action="./" method="POST">

    <input type="text" name="title" value="<?= $page->title ?>">

    <input type="text" name="headline" value="<?= $pages->headline ?>" >

    <input type="text" name="text" value="<?= $pages->text ?>" >

    <?php
        // set params
        $params = [
            "url" => $page->url,
            "formID" => "dropzone-form",
            "buttonID" => "submit-dropzone",
            // Let's enable form submit so we can process form after files upload separately
            // You can also keep it false, and do eevrything in one ajax request
            "submitForm" => "true",
            // load existing images to dropzone field
            "my_files" => $dropzone->getPageFiles($page->images),
        ];

        // Send aditional data
        // In this case $page->id and few more page fields
        $data = [
            "page_id" => $page->id,
            "title" => $page->title,
            "headline" => $page->headline,
            "text" => $page->text,
        ];
        
        // init dropzone
        echo $modules->get("Dropzone")->loadDropzone($params, $data);
    ?>

    <!-- NOTE: button name needs to be != submit -->
    <input id="submit-dropzone" type="submit" name="dropzoneSubmit" value="Submit" />

</form>

```

Process page edit form:

```php
<?php

$dropzone =  $modules->get("Dropzone");

// Add images first
if($input->post->dropzoneAjax) { 

    $id = $sanitizer->int($input->post->page_id);
    $p = $pages->get("id=$id");

    $dropzone->addFile($p, "images");

}

// After ajax response 
// if form is submited edit page fields
// see $params = ["submitForm" => "true"];

if($input->post->dropzoneSubmit) {

    // get the page
    $id = $sanitizer->int($input->post->page_id);
    $p = $pages->get("id=$id");

    $title      = $sanitizer->text($input->post->title);
    $headline   = $sanitizer->text($input->post->headline);
    $text       = $sanitizer->textarea($input->post->text);

    // edit page fields
    $p->of(false);
    $p->title = $title;
    $p->headline = $headline;
    $p->text = $text;
    $p->save();

    echo $dropzone->swal("Success", "$p->title has been saved", 'success');

}

// Manage Image Removes
if($input->post->dropzoneRemove) {

    $id = $sanitizer->int($input->post->page_id);
    $p = $pages->get("id=$id");

    $img_field = "images";
    $img_name = $input->post->file_name;

    $dropzone->removeFile($p, $img_field, $img_name);

}

```

#### This are some quick examples, for more look and the examples folder.