<?php namespace ProcessWire;
/**
 *  Dropzone module
 * 
 *  This module is built with page edits in mind, for editing pages with files and images, on the front-end.
 *  It can remove and add files and images on a page with ajax requests. 
 *  Dropzone module can submit other form data along with files, therefore it can be used to edit all page fields.
 *  It can also be used just to upload files to a specific destination, @see uploadPHP() and wireUpload() methods.
 *  Oh, yes, u can use dropzone for all kind of forms (that need files handling), to send emails with attachemnts etc..
 *  Also handles basic form validation and custom captcha.
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2019 kreativan.net
 *  @link http://www.kreativan.net
 *
 *  loadDropzone()      init dropzone inside the form
 *  uploadPHP()         uplaod files using vanila php
 *  wireUpload()        upload files using WireUpload class
 * 
 *  getPageFiles()      get page images, returns dropzone image array
 *  fileToPage()        add image to page,
 *  deletePageFile()    delete image from a page
 *  fileExists()        heck if image exists on a page
 * 
 *  addFile             fileToPage() + json response 
 *  removeFile          deletePageFile() + json response
 *  
 *  swal()              Sweet Alert init
 *  renderCaptcha()     render numb captcha
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
     *  @param array $params                main array of params
     *  @param array $data                  data you wish to POST along with files
     *  
     *  @param bool|string $submitForm      submit form after dropzone Ajax request, default = false (optional)
     *  @param bool|string $redirect        redirect to the same page after modal confim, only works if submitForm = false
     *  @param string url                   url where u want to post data (required)
     *  @param string id                    dropzone field css id, default = dropzone (optional)
     *  @param string formID                form css ID, requierd if u want to submit the form and rest of the form fields (recomended)
     *  @param string buttonID              submit button css ID, default = submit-dropzone (required)
     * 
     *  @param string acceptedFiles         allowed files (.jpg,.png,.pdf), default = image/*
     *  @param integer maxFiles             max number of files allowed, default = 5 (optional)
     *  @param float maxFilesize            max file size allowed in MB, default = 0.3 (optional)
     *  @param bool uploadMultiple          no used currently
     *  @param bool addRemoveLinks          not used, using custom remove button so we can use custom modal confirm
     *  @param bool confirmRemove           not used, using custom remove button
     *  @param bool createImageThumbnails   create thumbnails on image add, default = "true" (optional)
     *  @param integer thumbnailWidth       thumbnail width, default = 120 (optional)
     *  @param integer thumbnailHeight      thumbnail height, default = 120 (optional)
     * 
     *  @param array $my_files               Array of existing images/files. ["url" => "", "name" => "", "size" => ""]
     * 
     */
    public function loadDropzone($params = [], $data = []) {

        // Load Scripts
        // $this->config->scripts->append($this->config->urls->siteModules . $this->className() . "/assets/sweetalert.min.js");
        $this->config->scripts->append($this->config->urls->siteModules . $this->className() . "/assets/sweetalert2.all.min.js");
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

        $textMessage        = __("Drop files here to upload");
        $textMaxSize        = __("Max allowed file size is");
        $textMaxFiles       = __("files are allowed");
        $textFileType       = __("Invalid File Type");
        $textCancel         = __("Cancel");
        $textRemove         = __("Remove");
        $textAreYouSure     = __("Are you Sure?");
        $textFormInvalid    = __("Form invalid");
        $textCheckFields    = __("Please fill in all required fields");

        // images
        $my_files = !empty($params["my_files"]) ? $params["my_files"] : "";

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
            "my_files" => $my_files,
        );

        // text strings
        $dropzoneText = [
            "message" => $textMessage,
            "max_size" => $textMaxSize, 
            "max_files" => $textMaxFiles,
            "file_type" => $textFileType,
            "cancel" => $textCancel,
            "remove" => $textRemove,
            "are_you_sure" => $textAreYouSure,
            "form_invalid" => $textFormInvalid,
            "check_fields" => $textCheckFields,
        ];


        // custom data user can define
        // will be sent as POST by dropzone along with the files
        $dropzoneData = [];
        // add custom data
        foreach($data as $key => $value) $dropzoneData[$key] = $value;

        // pass variables to the js
        $vars =  "<script>const dropzoneVars = " . json_encode($dropzoneVars) . ";</script>";
        $data = "<script>const dropzoneData = " . json_encode($dropzoneData) . ";</script>";
        $text = "<script>const dropzoneText = " . json_encode($dropzoneText) . ";</script>";

        // render the field
        $dropzone = "
            <input type='text' name='dropzoneHoneypot' style='display:none;' />
            <div id='$id' class='dropzone'>$vars $data $text</div>
        ";
        return $dropzone;

    }

    /**
     *  Upload files using vanilla php
     *  @param string $dest destination folder
     * 
     */
    public function uploadPHP($dest = "") {

        if (!empty($_FILES)) {
            // if dest folder doesen't exists, create it
            if(!file_exists($dest) && !is_dir($dest)) throw new WireException("Destination path doesn't exists");

            foreach($_FILES['dropzoneFiles']['tmp_name'] as $key => $value) {
                $tempFile = $_FILES['dropzoneFiles']['tmp_name'][$key];
                $targetFile =  $dest. $_FILES['dropzoneFiles']['name'][$key];
                move_uploaded_file($tempFile,$targetFile);
            }

        }

    }


    /**
     *  Uplaod files using WireUpload class
     *  @param string $dest            destination folder
     *  @param array $allowed_files    this is required for WireUpload
     * 
     */
    public function wireUpload($dest = "", $allowed_files = ['jpg', 'jpeg', 'gif', 'png']) {

        // if dest folder doesen't exists, create it
        if(!file_exists($dest) && !is_dir($dest)) mkdir($dest);

        // WireUpload
        $upload = new WireUpload("dropzoneFiles");
        // $upload->setMaxFiles(1);
        $upload->setOverwrite(true);
        $upload->setDestinationPath($dest);
        $upload->setValidExtensions($allowed_files); 
        $upload->execute();

    }


    /**
     *  Create images/files array to send to dropzone as existing images.
     *  @param page|object $page_field     page images/files field
	 *	@param string $thumb_size		     thumbnail crop size
     *  @example $this->getPageFiles($page->images); 
     *  @return array ["url" => "", "name" => "", "size" => ""]
     * 
     */
    public function getPageFiles($page_field, $thumb_size = "120") {

        $arr = [];

        if(!empty($page_field) && $page_field->count) {
            foreach($page_field as $f) {
                $arr[] = [
                    "url" => $f->size($thumb_size, $thumb_size)->url, 
                    "name" => $f->basename, 
                    "size" => $f->filesize
                ];
            }
        }

        return $arr;

    }
    

    /**
     *  Add images/files to the page
     *  @param Page|object $p 
     *  @param string $page_field       page images/files field name
     *  @param array $allowed_files     ['jpg', 'jpeg', 'gif', 'png']
     * 
     */
    public function fileToPage($p, $page_field, $allowed_files = ['jpg', 'jpeg', 'gif', 'png']) {

        $dest = $this->config->paths->files . $p->id . "/";
        
        if($p->{$page_field}) {
            // upload image
            $upload = new WireUpload("dropzoneFiles");
            $upload->setOverwrite(false);
            $upload->setDestinationPath($dest);
            $upload->setValidExtensions($allowed_files);
            $upload->execute();
            $p->of(false);
            foreach($upload->execute() as $filename) $p->{$page_field}->add($dest . $filename);
            $p->save();
        } else {
            
            throw new WireException("File/image field name doesn't exists");

        }

    }

    /**
     *  Delete image/file from a page 
     *  @param page|object $p 
     *  @param string $page_field        page images/files field name
     *  @param string $file_name         image/file basename, optional, if not spefified $input->post->file_name will be used
     * 
     *  $_POST will return this: $input->post->@var
     *  @var string $dropzoneRemove  value = 1, so we can check for request @example if ($input->post->dropzoneRemove) {...}
     *  @var url|string $file_url    image/file url
     *  @var string $file_name       image/file basename,
     *  @var bool $accepted          true / false
     *  @var string $type            eg: "image/jpeg"
     * 
     */
    public function deletePageFile($p, $page_field, $file_name) {
        
        $file_name_post = $this->sanitizer->text($this->input->post->file_name);
        $file_name = !empty($file_name) ? $file_name : $file_name_post;

        $fileExists  = $this->fileExists($p, $page_field, $file_name);

        if($fileExists == true) {

            if(!empty($p->{$page_field})) $this_file = $p->{$page_field}->get("name=$file_name");

            if($this_file && $this_file != "") {
                $p->of(false);
                if($p->{$page_field}->count == 1) $p->{$page_field}->removeAll();
                if(!empty($this_file && $this_file != "")) $p->{$page_field}->delete($this_file);
                $p->save();
            }

        } 

    }
    
    /**
     *  Check if image/file exists on a page
     *  @param page|object $p
     *  @param string $page_field      page images/files field name
     *  @param string $file_name      image/file basename, @example example.jpg
     *  @return bool
     * 
     */
    public function fileExists($p, $page_field, $file_name) {
        if(!empty($p) && !empty($p->{$page_field}) && !empty($file_name)) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     *  Sweet Alert
     *  @param string $title
     *  @param string $text
     *  @param string $type  success/warning/error/question/info
     * 
     */
    public function swal($title, $text, $type) {

        $swal = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire(
                      '$title',
                      '$text',
                      '$type'
                    )
                }, false);
            </script>
        ";

        return $swal;

    }

    /**
     *  Sweet Alert Mini
     *  @param string $title 
     *  @param string $type  success/error/warning/info/question
     *  @param string $pos   top', 'top-start', 'top-end', 'center', 'center-start', 'center-end', 'bottom', 'bottom-start', or 'bottom-end'.
     * 
     */
    public function swalMini($title, $type = "success", $pos = "top-end") {

        $swal = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const Toast = Swal.mixin({
                      toast: true,
                      position: '$pos',
                      showConfirmButton: false,
                      timer: 3000
                    });

                    Toast.fire({
                      type: '$type',
                      title: '$title'
                    })

                }, false);
            </script>
        ";

        return $swal;

    }


    /**
     *  Number Catpcha
     *  Use it inside a form
     *  @return html
     */
    public function renderCaptcha() {

        $numb_1 = rand(1, 5);
        $numb_2 = rand(1, 5);
        $answer = $numb_1 + $numb_2;

        $inputs = "
            <input id='numb-captcha-answer' type='text' name='answer' value='{$answer}' required style='display:none;' />
            <input id='numb-captcha-q' class='uk-input' type='text' name='numb_captcha' 
            placeholder='$numb_1 + $numb_2 = ?' required />
        ";

        return $inputs;

    }

    /* =========================================================== 
        AIO methods
    =========================================================== */

    /**
     *  Add images/files to the page and return json response
     *  @param page|object $p
     *  @param string $page_field     page images/files field name
     *  @param array $allowed_files   this is required for WireUpload
     *  
     *  @see $this->fileToPage()
     *  
     */
    public function addFile($p, $page_field, $allowed_files = ['jpg', 'jpeg', 'gif', 'png']) {

        
        if($p->{$page_field}) {

            $this->fileToPage($p, $page_field, $allowed_files);

            $status = "success";
            $message = __("Upload Complete!");
            $error = "";

        } else {

            $status = "error";
            $message = __("Upload Failed!");
            $error = "$page_field doesnt exists";

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



    /**
     *  Send Dropzone Image remove request and return json response
     * 
     *  @param page|object $p 
     *  @param string $page_field   page images/files field name
     *  @param string $file_name    image/file basename, optional, if not spefified $input->post->file_name will be used
     * 
     *  @see $this->deletePageFile()
     *  @see $this->fileExists()
     * 
     */
    public function removeFile($p, $page_field, $file_name) {

        $file_name_post = $this->sanitizer->text($this->input->post->file_name);
        $file_name = !empty($file_name) ? $file_name : $file_name_post;

        $fileExists =  $this->fileExists($p, $page_field, $file_name);
        if($fileExists == true) {

            // delete image
            echo $this->deletePageFile($p, $page_field, $file_name);

            $status = "success";
            $message = __("Image has been removed");
            $error = "";

        } else {

            $status = "error";
            $message = __("Remove image faild!");
            $error = __("Image, field or page dosen't exists");

        }

        /**
         *	Response 
         *	return json response to the dropzone
         *	@var array $data
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


}