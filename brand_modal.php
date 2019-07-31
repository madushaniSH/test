<div class="modal hide fade modal-form" id="suggest_brand" tabindex="-1" role="dialog"
    aria-labelledby="suggest_brand_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suggest_brand_title">Suggest New Brand</h5>
                <button type="button" class="close" id="close_suggest_brand" data-dismiss="modal" aria-label="Close" onclick="reset_brand_form()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="POST" id="new_brand">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="brand_name">*Name:</label>
                                <input type="text" id="brand_name" class="form-control">
                                <span id="brand_name_error" class="error-popup"></span>
                            </div>
                            <div class="form-group">
                                <label for="brand_local_name">Local Name:</label>
                                <input type="text" id="brand_local_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <div>
                                    <label for="brand_manufacturer">*Manufacturer:</label>
                                    <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" href="#suggest_manufacturer">Add New</button>
                                </div>
                                <!-- Using select2 jquery library-->
                                <select name="brand_manufacturer" id="brand_manufacturer" class="form-control manu-list">
                                </select>                                 
                                <span id="brand_manufacturer_error" class="error-popup"></span>
                            </div>
                            <div class="form-group">
                                <label for="brand_source">*Source:</label>
                                <input type="text" id="brand_source" class="form-control">
                                <span id="brand_source_error" class="error-popup"></span>
                            </div>
                            <div class="form-group">
                                <label for="brand_global_code">Global Code:</label>
                                <input type="text" id="brand_global_code" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="upload-section">
                                <p>Upload Image</p>
                                <div class="image-upload">
                                    <label for="file-input-brand-logo">
                                        <img id="preview-brand-logo" class="text-center"
                                            src="images\default\system\product\default.jpg" alt="your image" />
                                    </label>
                                    <input type='file' id="file-input-brand-logo" onchange="readURL(this);" />
                                </div>
                                <a id="clear-brand-logo" class="hide" onclick="clearURL(this);">Clear</a>
                                <span id="brand_image_error" class="error-popup"></span>
                            </div>
                        </div>
                    </div>
                    <label>Recognition Level:</label>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recognition_option" id="brand_option" value="brand" checked>
                            <label class="form-check-label" for="brand_option">
                                Brand
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="recognition_option" id="product_option" value="product">
                            <label class="form-check-label" for="product_option">
                                Product
                            </label>
                        </div>                                
                    </div>
                    <span id="brand_image_size_error" class="error-popup"></span>
                    <div id="brand_results">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset_brand_form()">Close</button>
                <button type="button" class="btn btn-success" value="Submit" onclick="submit_brand_form();">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>