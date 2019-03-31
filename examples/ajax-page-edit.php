<?php
/**
 *  Submit and process form with dropzone and ajax.
 *  If there is files send the ajax request, if not submit form as normal.
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *     
 *  @param  submitForm ==> false // prevet normal form submition
 * 
 *  @method fileToPage()    // add image/file to the page field
 *  @method removeFile()    // removes the file/image from page field      
 *  @method swal()          // display Sweet Alert  
 *  
 *
*/

$dropzone =  $modules->get("Dropzone");


if($input->post->dropzoneAjax) {

    $error  = "";
    $p      = $pages->get("/");

    // Let's add files/images first
    try {
        echo $dropzone->fileToPage($p, "images");
    } catch (Exception $e) {
        $error .= $e->getMessage();
    }


    if($error == "") {

        // edit page fields
        $p->of(false);
        $p->headline = $input->post->headline;
        
        // set response
        $status = "success";
        $message = "Page saved!";
        $error = $error;

    } else {

        // set response
        $status = "error";
        $message = $error;
        $error = $error;

    }
    

    /**
     *	Response 
     *	return json response to the dropzone
     *	@var data array
     */
    $data = [
        "status" => "$status",
        "message" => "$message",
        "error" => $error,
    ];

    header("Content-type: application/json");
    echo json_encode($data);
    exit();


} elseif ($input->post->dropzoneSubmit) {

    $p = $pages->get("/");

    // edit page fields
    $p->of(false);
    $p->headline = $input->post->headline;
    $p->save();

    echo $dropzone->swal("Success", 'Your form has been submited', 'success');

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
                    "submitForm" => "false", // submit form after dropzone Ajax request, default = false (optional)
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