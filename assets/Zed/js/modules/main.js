'use strict';

const errorMessageHandler = require('./error-message-handler');
const setHiddenBucketField = require('./set-hidden-bucket-field');
const tableLoader = require('./table-loader');

$(document).ready(function() {
    setHiddenBucketField.initialize();
    errorMessageHandler.initialize();
    tableLoader.initialize();
});

