<?php
/**
 *  deletePageImage.php
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *  
 *
*/

$dropzone =  $modules->get("Dropzone");

if($input->post->dropzoneRemove) {

    $p = $pages->get("/");
    $img_field = "images2";
    $img_name = $input->post->file_name;

    $isImage =  $dropzone->fileExists($p, $img_field, $img_name);

    if($isImage == true) {

        echo $dropzone->deletePageFile($p, $img_field, $img_name);

        $status = "success";
        $message = "Image has been removed";
        $error = "";

    } else {

        $status = "error";
        $message = "Remove image faild!";
        $error = "Image, field or page dosen't exists";

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