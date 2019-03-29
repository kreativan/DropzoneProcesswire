<?php namespace ProcessWire;
/**
 *  Dropzone module
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 Kreativan
 *
 *
*/

class Dropzone extends WireData implements Module {

    public static function getModuleInfo() {
        return array(
            'title' => 'Dropzone',
            'version' => 100,
            'summary' => 'Dropzone.js image upload implementation',
            'icon' => 'upload',
            'singular' => true,
            'autoload' => false
        );
    }

    /**
     *  Load Dropzone
     *  @param params array
     *  @param data   array // data you wish to POST along with files
     * 
     */
    public function loadDropzone($params = [], $data = []) {

        // Load Scripts
        $this->config->scripts->append($this->config->urls->siteModules . $this->className() . "/assets/sweetalert.min.js");
        $this->config->styles->append($this->config->urls->siteModules . $this->className() . "/assets/dropzone.min.css");
        $this->config->scripts->append($this->config->urls->siteModules . $this->className() . "/assets/dropzone.min.js");
        $this->config->scripts->append($this->config->urls->siteModules . $this->className() . "/dropzone.js");

        $submitForm = !empty($params["submitForm"]) && $params["submitForm"] == "true" ? true : false;
        $redirect   = !empty($params["redirect"]) && $params["redirect"] == "false" ? false : true;

        $url        = !empty($params["url"]) ? $params["url"] : "";
        $id         = !empty($params["id"]) ? $params["id"] : "dropzone";
        $formID     = !empty($params["formID"]) ? $params["formID"] : "submit-dropzone";
        $buttonID   = !empty($params["buttonID"]) ? $params["buttonID"] : "";

        $acceptedFiles  = !empty($params["acceptedFiles"]) ? $params["acceptedFiles"] : "image/*";
        $maxFiles       = !empty($params["maxFiles"]) ? $params["maxFiles"] : 5;
        $maxFilesize    = !empty($params["maxFilesize"]) ? $params["maxFilesize"] : 0.3;

        $uploadMultiple = !empty($params["uploadMultiple"]) && $params["uploadMultiple"] == "false" ? false : true;
        $addRemoveLinks = !empty($params["addRemoveLinks"]) && $params["addRemoveLinks"] == "false" ? false : true;
        $confirmRemove  = !empty($params["confirmRemove"]) ? $params["confirmRemove"] : "";

        $createImageThumbnails  = !empty($params["createImageThumbnails"]) && $params["createImageThumbnails"] == "false" ? false : true;
        $thumbnailWidth         = !empty($params["thumbnailWidth"]) ? $params["thumbnailWidth"] : 120;
        $thumbnailHeight        = !empty($params["thumbnailHeight"]) ? $params["thumbnailHeight"] : 120;

        $textMessage    = !empty($params["textMessage"]) ? $params["textMessage"] : __("Drop files here to upload");
        $textMaxSize    = !empty($params["textMaxSize"]) ? $params["textMaxSize"] : __("Max allowed file size is");
        $textMaxFiles   = !empty($params["textMaxFiles"]) ? $params["textMaxFiles"] : __("files are allowed");
        $textFileType   = !empty($params["textFileType"]) ? $params["textFileType"] : __("Invalid File Type");
        $textCancel     = !empty($params["textCancel"]) ? $params["textCancel"] : __("Cancel");
        $textRemove     = !empty($params["textRemove"]) ? $params["textRemove"] : __("Remove");
        $textAreYouSure = !empty($params["textAreYouSure"]) ? $params["textAreYouSure"] : __("Are you Sure?");
        

        // variables
        $dropzoneVars = array(
            "current_url" => $this->page->url,
            "redirect" => $redirect,
            'debug' => $this->config->debug,
            'submitForm' => $submitForm,
            'formID' => $formID,
            'buttonID' => $buttonID,
            'url' => $url,
            'id' => $id,
            'acceptedFiles' => $acceptedFiles,
            'maxFiles' => $maxFiles,
            'maxFilesize' => $maxFilesize,
            'uploadMultiple' => $uploadMultiple,
            'addRemoveLinks' => $addRemoveLinks,
            'dictRemoveFileConfirmation' => $confirmRemove,
            'createImageThumbnails' => $createImageThumbnails,
            'thumbnailWidth' => $thumbnailWidth,
            'thumbnailHeight' => $thumbnailHeight,
            "textMessage" => $textMessage,
            "textMaxSize" => $textMaxSize, 
            "textMaxFiles" => $textMaxFiles,
            "textFileType" => $textFileType,
            "textCancel" => $textCancel,
            "textRemove" => $textRemove,
            "textAreYouSure" => $textAreYouSure,
        );


        // custom data user can define
        // will be sent as POST by dropzone along with the files
        $dropzoneData = [];
        // add custom data
        foreach($data as $key => $value) $dropzoneData[$key] = $value;

        // pass variables to the js
        echo "<script>const dropzoneVars = " . json_encode($dropzoneVars) . ";</script>";
        echo "<script>const dropzoneData = " . json_encode($dropzoneData) . ";</script>";

        // render the field
        return "<div id='$id' class='dropzone'></div>";

    }


}