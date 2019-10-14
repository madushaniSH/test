<div class="modal hide fade modal-form" id="suggest_manufacturer" tabindex="-1" role="dialog"
    aria-labelledby="suggest_manufacturer_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suggest_manufacturer_title">Suggest New Manufacturer</h5>
                <button type="button" class="close" id="close_suggest_manufacturer" data-dismiss="modal" aria-label="Close" onclick="reset_manufacturer_form();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col col-md-6">
                    <form action="POST" id="new_manufacturer" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="manufacturer_name">*Name:</label>
                            <input type="text" id="manufacturer_name" class="form-control">
                            <span id="manufacturer_name_error" class="error-popup"></span>
                        </div>
                        <div class="form-group">
                            <label for="manufacturer_local_name">Local Name:</label>
                            <input type="text" id="manufacturer_local_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="manufacturer_source">Source:</label>
                            <input type="text" id="manufacturer_source" class="form-control">
                            <span id="manufacturer_source_error" class="error-popup"></span>
                        </div>
                        <span id="manufacturer_image_size_error" class="error-popup"></span>
                        <div id="manu_results">
                        </div>
                    </div>
                    <div class="col col-md-2">
                        <div class="upload-section">
                            <p>Logo</p>
                            <div class="image-upload">
                                <label for="file-input-manu-logo">
                                    <img id="preview-manu-logo" class="text-center"
                                        src="images\default\system\product\default.jpg" alt="your image" />
                                </label>
                                <input type='file' id="file-input-manu-logo" onchange="readURL(this);" />
                            </div>
                            <a id="clear-manu-logo" class="hide" onclick="clearURL(this);">Clear</a>
                            <span id="manufacturer_image_error" class="error-popup"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset_manufacturer_form();">Close</button>
                <button type="button" class="btn btn-success" onclick="submit_manufacturer_form();"
                    value="Submit">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>