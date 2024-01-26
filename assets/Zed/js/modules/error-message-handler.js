'use strict';

const hiddenBucketFieldHandler = require('./set-hidden-bucket-field');
const uploadFormName = "s3_buckets_upload_form";
const flashMessagesPath = 'div.flash-messages';
const alertContainerClass = 'alert alert-danger';
const alertIconClass = 'fas fa-ban alert__icon';
const alertTextContainerClass = 'alert__text';
const messageMissingBucket = 'Please choose a bucket to upload to';

function initialize() {
    const uploadForm = new UploadFormHandler();
    uploadForm.addListenerOnS3UploadForm();
}

function UploadFormHandler() {
    const $uploadForm = $('form[name=' + uploadFormName +']');
    const $flashMessages = $(flashMessagesPath);
    let $alertContainer = $flashMessages.children('div.alert-danger').first();

    function addListenerOnS3UploadForm() {
        $uploadForm.on('submit', function(event) {
            if (validateBucketFieldBeforeUpload()){
                return;
            }

            event.preventDefault();
            createFlashMessageElementsIfNoneExist();

            $alertContainer.children('div').first().text(messageMissingBucket);
        })
    }

    function validateBucketFieldBeforeUpload() {
        return !!hiddenBucketFieldHandler.getS3BucketName();
    }

    function createFlashMessageElementsIfNoneExist() {
        if ($alertContainer.length === 0) {
            $alertContainer = createAlertElement('div', alertContainerClass, $flashMessages);
            createAlertElement('i', alertIconClass, $alertContainer);
            createAlertElement('div', alertTextContainerClass, $alertContainer);
        }
    }

    function createAlertElement(elementTag, elementClass, parentElement) {
        let $newElement = $(document.createElement(elementTag));
        $newElement.addClass(elementClass);
        parentElement.append($newElement)

        return $newElement;
    }

    return {
        addListenerOnS3UploadForm: addListenerOnS3UploadForm,
    }
}

module.exports = {
    initialize: initialize,
};
