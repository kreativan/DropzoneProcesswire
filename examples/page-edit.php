<?php
/**
 *  Uplaod and add image with dropzone ajax request,
 *  After ajax response, submit the form as normal, and proceesss rest of the fields.
 *  So, in this example we are processing image upload and form submition separately.
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *  @param  submitForm ==> true
 * 
 *  @method addFile()       // add image/file to the page field, but dont process rest of the form fields
 *  @method removeFile()    // removes the file/image from page field      
 *  @method swal()          // display Sweet Alert   
 * 
 *
*/

$dropzone =  $modules->get("Dropzone");

// Add image first
if($input->post->dropzoneAjax) { 
    $p = $pages->get("/");
    $dropzone->addFile($p, "images");
}

// After ajax response 
// if form is submited edit the page fields
// see $params = ["submitForm" => "true"];
if($input->post->dropzoneSubmit) {

    $p = $pages->get("/");

    // edit page fields
    $p->of(false);
    $p->headline = $input->post->headline;
    $p->save();

    echo $dropzone->swal("Success!", 'Page has been saved', 'success');

}

// Remove Image
if($input->post->dropzoneRemove) {

    $p = $pages->get("/");
    $img_field = "images";
    $img_name = $input->post->file_name;

    $dropzone->removeFile($p, $img_field, $img_name);

}



// include header markup
include($config->paths->templates.'_head.php');
?>

<div class="uk-section uk-section-large">
    <div class="uk-container">


        <form id="dropzone-form" action="./" method="POST">

            <input class="uk-input" type="text" name="title" value="<?= $pages->get("/")->title ?>" required>

            <input class="uk-input uk-margin" type="text" name="headline" value="<?= $pages->get("/")->headline ?>" required>

            <?php

                // dropzone params
                $params = [
                    "url" => "./", // url where u want to post data (required)
                    "formID" => "dropzone-form", // form css ID, requierd if u want to submit the form and rest of the form fields (recomended)
                    "buttonID" => "submit-dropzone", // submit button css ID, default = submit-dropzone (required)
                    "submitForm" => "true", // submit form after dropzone Ajax request, default = false (optional)
                    "redirect" => "true", // redirect to the same page after modal confim, only works if submitForm = false
                    "acceptedFiles" => "image/*", // allowed files (.jpg,.png,.pdf), default = image/*
                    "maxFiles" => 5, // max number of files allowed, default = 5 (optional)
                    "maxFilesize" => 0.3, // max file size allowed in MB, default = 0.3 (optional)
                    'thumbnailWidth' => 140, // thumbnail width, default = 120 (optional)
                    'thumbnailHeight' => 140, // thumbnail height, default = 120 (optional)
                    // Array of existing images/files. ["url" => "", "name" => "", "size" => ""]
                    "my_files" => $dropzone->getPageFiles($pages->get("/")->images),
                ];

                // send aditional data
                $data = [
                    "title" => "MY New Headline",
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

    </div>

</div>

<?php
include($config->paths->templates.'_foot.php');
?>