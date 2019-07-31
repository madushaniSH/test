<div class="modal fade modal-form" id="suggest_client_sub_category" tabindex="-1" role="dialog"
    aria-labelledby="suggest_client_sub_category_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suggest_client_sub_category_title">New Client Subcategory</h5>
                <button type="button" class="close" id="close_suggest_client_sub_category" data-dismiss="modal" aria-label="Close" onclick="reset_client_sub_category_form()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="POST" id="new_client_sub_category">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label for="client_sub_category_name">*Name:</label>
                                <input type="text" id="client_sub_category_name" class="form-control">
                                <span id="client_sub_category_name_error" class="error-popup"></span>
                            </div>
                            <div class="form-group">
                                <label for="client_sub_category_local_name">Local Name:</label>
                                <input type="text" id="client_sub_category_local_name" class="form-control">
                            </div>
                            <div id="sub_results">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset_client_sub_category_form()">Close</button>
                <button type="button" class="btn btn-success" onclick="submit_client_sub_category_form();"
                    value="Submit">Save changes</button>
                    </form>
            </div>
        </div>
    </div>
</div>