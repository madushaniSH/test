<div class="modal fade modal-form" id="edit_attribute" tabindex="-1" role="dialog"
    aria-labelledby="edit_attribute_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_attribute_title">New Additional Attribute</h5>
                <button type="button" class="close" id="close_edit_attribute" data-dismiss="modal" aria-label="Close" onclick="reset_attribute_form()">
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
                            <button class="btn btn-success" id="add_attribute" disabled onclick="add_attribute_new();">Create</button>
                            <span id="attribute_error"><span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="panel panel-primary" id="attribute_panel">                       
                        <div class="panel-body">
                            <div class="attribute-list">
                            </div>
                        </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="reset_attribute_form()">Close</button>
                <button type="button" class="btn btn-success" onclick="apply_attribute_list();">Apply</button>
                    </form>
            </div>
        </div>
    </div>
</div>