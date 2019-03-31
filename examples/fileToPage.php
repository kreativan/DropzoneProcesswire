<?php
/**
 *  imageToPage.php
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *
*/

$dropzone =  $modules->get("Dropzone");

if($input->post->dropzoneAjax) {

    try {

        $p = $pages->get("/");
        echo $dropzone->fileToPage($p, "images");

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
        "post" => $_POST,
    ];
    
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}