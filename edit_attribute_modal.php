<div class="modal fade modal-form" id="edit_attribute" tabindex="-1" role="dialog"
    aria-labelledby="edit_attribute_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_attribute_title">New Additional Attribute</h5>
                <button type="button" class="close" id="close_suggest_client_sub_category" data-dismiss="modal" aria-label="Close" onclick="reset_client_sub_category_form()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="new_attribute_form">
                    <div class="form-row">
                        <div class="col col-md-6">
                            <div class="form-group">
                                <input type="text" id="new_attribute" name="new_attribute" placeholder="New Attribute" class="form-control">
                            </div>
                        </div>
                        <div class="col col-md-6">
                            <button class="btn btn-success" id="add_attribute" disabled>Create</button>
                        </div>
                        <div class="panel panel-primary" id="attribute_panel">                       
                        <div class="panel-body">
                            <div class="attribute-list">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" value="Submit">Save changes</button>
                    </form>
            </div>
        </div>
    </div>
</div>