'use strict';

const s3BucketFieldPath = 'select#s3_buckets_form_s3_bucket_field';
const s3BucketHiddenFieldPath = 'input#s3_buckets_upload_form_bucket';
const s3UploadButtonPath = 'button#s3_buckets_upload_form_upload';
const $s3BucketField = $(s3BucketFieldPath);
function initialize() {
    const hiddenField = new HiddenBucketFieldHandler();
    hiddenField.init();
    hiddenField.addListenerOnS3BucketField();
}

function getS3BucketName() {
    return $s3BucketField.val();
}

function HiddenBucketFieldHandler() {

    const $s3UploadButton = $(s3UploadButtonPath);
    const $s3BucketHiddenField = $(s3BucketHiddenFieldPath);

    function init(){
        $s3BucketHiddenField.val(getS3BucketName());
    }

    function addListenerOnS3BucketField(){
        $s3BucketField.on('change', function() {
            $s3BucketHiddenField.val(getS3BucketName());
            enableUploadButton();
        });
    }

    function enableUploadButton() {
        $s3UploadButton.attr({'disabled': false});
        $s3UploadButton.removeClass('disabled');
    }

    return {
        init: init,
        addListenerOnS3BucketField: addListenerOnS3BucketField,
    }

}
module.exports = {
    initialize: initialize,
    getS3BucketName: getS3BucketName,
}
