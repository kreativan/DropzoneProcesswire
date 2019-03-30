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
     *  @var submitForm                 bool/string // submit form after dropzone Ajax request, default = false (optional)
     *  @var redirect                   bool/string // redirect to the same page after modal confim, only works if submitForm = false
     *  @var url                        string // url where u want to post data (required)
     *  @var id                         string // dropzone field css id, default = dropzone (optional)
     *  @var formID                     string // form css ID, requierd if u want to submit the form and rest of the form fields (recomended)
     *  @var buttonID                   string // submit button css ID, default = submit-dropzone (required)
     * 
     *  @var acceptedFiles              string // allowed files (.jpg,.png,.pdf), default = image/*
     *  @var maxFiles                   integer // max number of files allowed, default = 5 (optional)
     *  @var maxFilesize                float // max file size allowed in MB, default = 0.3 (optional)
     *  @var uploadMultiple             bool // no used currently
     *  @var addRemoveLinks             bool // not used, using custom remove button so we can use custom modal confirm
     *  @var confirmRemove              bool // not used, using custom remove button
     *  @var createImageThumbnails      bool // create thumbnails on image add, default = "true" (optional)
     *  @var thumbnailWidth             integer // thumbnail width, default = 120 (optional)
     *  @var thumbnailHeight            integer // thumbnail height, default = 120 (optional)
     * 
     *  @var images                     array // Array of existing images. ["url" => "", "name" => "", "size" => ""]
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

        // images
        $images = !empty($params["images"]) ? $params["images"] : "";

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
            "images" => $images,
        );


        // custom data user can define
        // will be sent as POST by dropzone along with the files
        $dropzoneData = [];
        // add custom data
        foreach($data as $key => $value) $dropzoneData[$key] = $value;

        // pass variables to the js
        $vars =  "<script>const dropzoneVars = " . json_encode($dropzoneVars) . ";</script>";
        $data = "<script>const dropzoneData = " . json_encode($dropzoneData) . ";</script>";

        // render the field
        return "<div id='$id' class='dropzone'>$vars $data</div>";

    }


    public function uploadPHP() {

        

    }

}