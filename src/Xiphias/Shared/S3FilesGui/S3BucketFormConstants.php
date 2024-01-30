<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Shared\S3FilesGui;

interface S3BucketFormConstants
{
    /**
     * @var string
     */
    public const S3_BUCKETS_FORM = 's3_buckets_form';

    /**
     * @var string
     */
    public const S3_BUCKET_FIELD = 's3_bucket_field';

    /**
     * @var string
     */
    public const S3_BUCKET_OPTIONS = 'bucket_options';

    /**
     * @var string
     */
    public const VALIDATION_NOT_BLANK_MESSAGE = 'validation.not_blank';

    /**
     * @var string
     */
    public const SELECTED_FILES_NAMES = 'selected_files_names';

    /**
     * @var string
     */
    public const S3_BUCKETS_FIELD_OPTION_PLACEHOLDER = '- select -';

    /**
     * @var string
     */
    public const DOWNLOAD_SELECTED_BUTTON_CLASSES_DISABLED = 'btn btn-primary btn-w-m disabled';

    /**
     * @var string
     */
    public const DELETE_SELECTED_BUTTON_CLASSES_DISABLED = 'btn btn-primary btn-danger btn-w-m disabled';

    /**
     * @var string
     */
    public const DATA_TOGGLE_MODAL = 'modal';

    /**
     * @var string
     */
    public const DATA_TARGET_MODAL_CLASS = '.confirm-delete-modal';

    /**
     * @var string
     */
    public const LOAD_DATA_BUTTON_CLASSES = 'btn btn-outline btn-w-m safe-submit';

    /**
     * @var string
     */
    public const S3_DOWNLOAD_DELETE_SELECTED_FORM = 's3_download_delete_selected_form';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_FORM_NOT_VALID = 's3.form.not-valid';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_MISSING_BUCKET = 's3.form.missing-bucket';

 /**
  * @var string
  */
    public const SUCCESS_MESSAGE_UPLOAD = 's3.upload.success';

    /**
     * @var string
     */
    public const UPLOADED_FILE = 'uploadedFile';

    /**
     * @var string
     */
    public const HIDDEN_BUCKET_FIELD = 'bucket';

    /**
     * @var string
     */
    public const TWIG_BUCKET_FORM = 'S3BucketForm';

    /**
     * @var string
     */
    public const TWIG_UPLOAD_FORM = 'S3UploadForm';

    /**
     * @var string
     */
    public const TWIG_DOWNLOAD_DELETE_SELECTED_FORM = 'S3DownloadDeleteSelectedForm';

    /**
     * @var string
     */
    public const TWIG_BUCKET_TABLE = 'S3BucketTable';

    /**
     * @var string
     */
    public const BUTTON_SHOW_FILES = 's3-button-show';

    /**
     * @var string
     */
    public const BUTTON_SHOW_FILES_LABEL = 's3.button.show';

    /**
     * @var string
     */
    public const BUTTON_SAFE_SUBMIT_CLASS = 'btn btn-primary safe-submit btn-w-m';

    /**
     * @var string
     */
    public const BUTTON_PRIMARY_SUBMIT_CLASS = 'btn btn-primary btn-w-m';

    /**
     * @var string
     */
    public const BUTTON_UPLOAD = 's3-button-upload';

    /**
     * @var string
     */
    public const BUTTON_UPLOAD_LABEL = 's3.button.upload';

    /**
     * @var string
     */
    public const BUTTON_DOWNLOAD = 's3-button-download';

    /**
     * @var string
     */
    public const BUTTON_DOWNLOAD_LABEL = 's3.button.download';

    /**
     * @var string
     */
    public const BUTTON_DELETE = 's3-button-delete';

    /**
     * @var string
     */
    public const BUTTON_DELETE_LABEL = 's3.button.delete';
}
