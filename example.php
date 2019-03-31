<?php
/**
 *  example.php
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *
*/

$dropzone =  $modules->get("Dropzone");


// Add image
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

// Form submited as normal
if($input->post->dropzoneSubmit) { 

    d($_POST);
    print_r($_POST);

    $dropzone->swal("Success", "You done it! Bravo!", "success")
    
}

?>


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

    <div class="uk-margin">
        <?php
            // Optional Captcha 
            echo $dropzone->renderCaptcha();
        ?>
    </div>

    <!-- NOTE: button name needs to be != submit -->
    <div class="uk-margin">
        <input id="submit-dropzone" class="uk-button uk-button-primary" type="submit" name="dropzoneSubmit" value="Submit" />
    </div>

</form>