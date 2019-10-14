/*
    Filename: sku_form_image_preview.js
    Author: Malika Liyanage
    Created: 23/07/2019
    Purpose: Used for generating the image previews for the uploaded images in the form 
    in new_sku_form.php
*/

/*Map containing pairs of ids for the file upload input and
its related preview image id
This is not supported in Internet */
var preview_list = new Map([
    ['file-input-front', 'preview-front'],
    ['file-input-top', 'preview-top'],
    ['file-input-back', 'preview-back'],
    ['file-input-bottom', 'preview-bottom'],
    ['file-input-side1', 'preview-side1'],
    ['file-input-side2', 'preview-side2'],
    ['file-input-front-alt', 'preview-front-alt'],
    ['file-input-bottom-alt', 'preview-bottom-alt'],
    ['file-input-top-alt', 'preview-top-alt'],
    ['file-input-side1-alt', 'preview-side1-alt'],
    ['file-input-side2-alt', 'preview-side2-alt'],
    ['file-input-back-alt', 'preview-back-alt'],
    ['file-input-manu-logo', 'preview-manu-logo'],
    ['file-input-brand-logo', 'preview-brand-logo']
]);

var clear_button_list = new Map ([
    ['file-input-front', 'clear-front'],
    ['file-input-top', 'clear-top'],
    ['file-input-back', 'clear-back'],
    ['file-input-bottom', 'clear-bottom'],
    ['file-input-side1', 'clear-side1'],
    ['file-input-side2', 'clear-side2'],
    ['file-input-front-alt', 'clear-front-alt'],
    ['file-input-bottom-alt', 'clear-bottom-alt'],
    ['file-input-top-alt', 'clear-top-alt'],
    ['file-input-side1-alt', 'clear-side1-alt'],
    ['file-input-side2-alt', 'clear-side2-alt'],
    ['file-input-back-alt', 'clear-back-alt'],
    ['file-input-manu-logo', 'clear-manu-logo'],
    ['file-input-brand-logo', 'clear-brand-logo']
]);

var button_upload_list = new Map ([
    ['clear-front', 'file-input-front'],
    ['clear-top', 'file-input-top'],
    ['clear-back', 'file-input-back'],
    ['clear-bottom', 'file-input-bottom'],
    ['clear-side1', 'file-input-side1'],
    ['clear-side2', 'file-input-side2'],
    ['clear-front-alt', 'file-input-front-alt'],
    ['clear-bottom-alt', 'file-input-bottom-alt'],
    ['clear-top-alt', 'file-input-top-alt'],
    ['clear-side1-alt', 'file-input-side1-alt'],
    ['clear-side2-alt', 'file-input-side2-alt'],
    ['clear-back-alt', 'file-input-back-alt'],
    ['clear-manu-logo', 'file-input-manu-logo'],
    ['clear-brand-logo', 'file-input-brand-logo']
])

/*function for adding the image preview called when an image is uploaded */
function readURL(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
            /*Generates a string contaning the id for the preview image */
            var preview_id = '#' + preview_list.get($(input).attr('id'))
            $(preview_id).attr('src',e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
        var clear_button_id = '#' + clear_button_list.get($(input).attr('id'));
        $(clear_button_id).removeClass('hide');
    }
}

/*function for clearing the uploaded image and its image preview */
function clearURL(input){
    var file_id = '#' + button_upload_list.get($(input).attr('id'));
    $(file_id).val('');
    var preview_id = '#' + preview_list.get(button_upload_list.get($(input).attr('id')));
    $(preview_id).attr('src','images/default/system/product/default.jpg');
    var button_id = '#' + $(input).attr('id');
    $(button_id).addClass('hide');
}