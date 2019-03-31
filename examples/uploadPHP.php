<?php
/**
 *  Basic PHP file upload
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *
*/

$dropzone =  $modules->get("Dropzone");

if($input->post->dropzoneAjax) {

    try {

        echo $dropzone->uploadPHP($config->paths->templates."temp/");

        $status = "success";
        $message = "Uplaod Complete!";
        $error = "";

    } catch (Exception $e) {

        $status = "error";
        $message = "Uplaod faild!";
        $error = $e->getMessage();

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
        "files" => $_FILES,
        "post" => $_POST,
    ];
    
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}