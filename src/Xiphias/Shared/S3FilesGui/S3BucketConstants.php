<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Shared\S3FilesGui;

interface S3BucketConstants
{
    /**
     * @var string
     */
    public const S3_BUCKETS_CREDENTIALS = 's3-buckets_credentials';

    /**
     * @var string
     */
    public const S3_REGION = 'eu-central-1';

    /**
     * @var string
     */
    public const S3_VERSION = 'latest';

    /**
     * @var string
     */
    public const ROUTE_INDEX = '/s3-files-gui';

    /**
     * @var string
     */
    public const ROUTE_UPLOAD = '/s3-files-gui/index/upload';

    /**
     * @var string
     */
    public const ROUTE_DOWNLOAD = '/s3-files-gui/index/download';

    /**
     * @var string
     */
    public const ROUTE_DELETE = '/s3-files-gui/index/delete';

    /**
     * @var string
     */
    public const ROUTE_HANDLE_SELECTED_FILES = '/s3-files-gui/index/handle-selected-files';

    /**
     * @var string
     */
    public const KEY_REGION = 'region';

    /**
     * @var string
     */
    public const KEY_VERSION = 'version';

    /**
     * @var string
     */
    public const KEY_CREDENTIALS = 'credentials';

    /**
     * @var string
     */
    public const BODY = 'Body';

    /**
     * @var string
     */
    public const CONTENT_TYPE = 'Content-Type';

    /**
     * @var string
     */
    public const AWS_RESLT_CONTENT_TYPE_KEY = 'ContentType';

    /**
     * @var string
     */
    public const APPLICATION_ZIP = 'application/zip';

    /**
     * @var string
     */
    public const CONTENT_DISPOSITION = 'Content-Disposition';

    /**
     * @var string
     */
    public const ATTACHMENT_WITH_FILENAME = 'attachment; filename=';

    /**
     * @var string
     */
    public const PRAGMA = 'Pragma';

    /**
     * @var string
     */
    public const PUBLIC = 'Public';

    /**
     * @var int
     */
    public const ONE_FILE_COUNT = 1;

    /**
     * @var string
     */
    public const KEY = 'Key';

    /**
     * @var string
     */
    public const BUCKET = 'Bucket';

    /**
     * @var string
     */
    public const DOWNLOAD = 'Download';

    /**
     * @var string
     */
    public const DELETE = 'Delete';

    /**
     * @var string
     */
    public const LOAD_DATA = 'LoadData';

    /**
     * @var string
     */
    public const OBJECTS = 'Objects';

    /**
     * @var string
     */
    public const S3_FILE_NAMES_PARAM = 's3_file';

    /**
     * @var string
     */
    public const ZIP_FILE_EXTENSION = '.zip';

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @var string
     */
    public const FILE_NAME_QUERY_PARAM = 'fileName';

    /**
     * @var string
     */
    public const BUCKET_NAME_QUERY_PARAM = 'bucketName';

    /**
     * @var array
     */
    public const EXCLUDED_BUCKETS = ['oase-staging-ssm-ansible'];

    /**
     * @var string
     */
    public const BUCKETS = 'Buckets';

    /**
     * @var string
     */
    public const BUCKET_NAME = 'Name';

    /**
     * @var string
     */
    public const BUCKET_CONTENTS = 'Contents';

    /**
     * @var string
     */
    public const CLASS_BTN_DOWNLOAD = 'btn-download';

    /**
     * @var string
     */
    public const CLASS_BTN_DANGER = 'btn-danger';

    /**
     * @var string
     */
    public const ICON_FA_DOWNLOAD = 'fa-download';

    /**
     * @var string
     */
    public const TABLE_COL_NAME = 's3-bucket_name';

    /**
     * @var string
     */
    public const TABLE_IDENTIFIER_PREFIX = 'table-';

    /**
     * @var string
     */
    public const TABLE_COL_ACTIONS = 's3-bucket_actions';

    /**
     * @var string
     */
    public const TABLE_COL_ACTIONS_HEADER = 'actions';

    /**
     * @var string
     */
    public const TABLE_COL_CHECKBOX = 's3-bucket_checkbox';

    /**
     * @var string
     */
    public const HEADER_FILE_NAME = 'File name';

    /**
     * @var string
     */
    public const S3_TABLE_URL = '/s3-table';

    /**
     * @var string
     */
    public const SEARCH_PARAM = 'search';

    /**
     * @var string
     */
    public const SEARCH_PARAM_VALUE_KEY = 'value';

    /**
     * @var string
     */
    public const FILTER_PARAM = 'filterParam';

    /**
     * @var string
     */
    public const CLEAR_USED_FILTERS_FROM_SESSION = 'clearUsedFiltersFromSession';

    /**
     * @var string
     */
    public const CHOSEN_BUCKET = 'chosenBucket';

    /**
     * @var string
     */
    public const SEARCH_STRING_SESSION_KEY = 'searchString';

    /**
     * @var string
     */
    public const FILTER_STRING_SESSION_KEY = 'filterString';

    /**
     * @var string
     */
    public const USED_FILTER_STRINGS = 'usedFilterStrings';

    /**
     * @var string
     */
    public const ERROR_STATUS_CODE_KEY = 'ErrorStatusCode';

    /**
     * @var string
     */
    public const AWS_ERROR_MESSAGE_KEY = 'AwsErrorMessage';

    /**
     * @var string
     */
    public const HEADERS = 'Headers';

    /**
     * @var string
     */
    public const DELETED_KEY = 'Deleted';

    /**
     * @var string
     */
    public const STATUS_CODE = 'statusCode';

    /**
     * @var string
     */
    public const MESSAGE_DELETE_SUCCESSFUL = 's3.delete.success';

    /**
     * @var string
     */
    public const MESSAGE_DELETE_FAILED = 'Delete failed';

    /**
     * @var string
     */
    public const MESSAGE_DOWNLOAD_FAILED = 'Download failed because of the following files: %responseContent%';

    /**
     * @var string
     */
    public const MESSAGE_RESPONSE_CONTENT_PARAM = '%responseContent%';

    /**
     * @var string
     */
    public const BUCKET_FILES_DATA = 'data';

    /**
     * @var string
     */
    public const CUSTOM_DATATABLE_RESULT = 'result';

    /**
     * @var string
     */
    public const CUSTOM_DATATABLE_CONFIGURATION = 'configuration';

    /**
     * @var string
     */
    public const CUSTOM_DATATABLE_RECORDS_TOTAL = 'recordsTotal';

    /**
     * @var string
     */
    public const CUSTOM_DATATABLE_RECORDS_FILTERED = 'recordsFiltered';

    /**
     * @var string
     */
    public const CUSTOM_DATATABLE_RECORDS_DRAW = 'draw';

    /**
     * @var string
     */
    public const REG_EXP_FIND_PAGE_START_INDEX = '/&start=(.*?)&/';

    /**
     * @var string
     */
    public const REG_EXP_FIND_ORDER_DIRECTION = '/&order%5B0%5D%5Bdir%5D=(.*?)&/';

    /**
     * @var string
     */
    public const COLUMN_ORDER_DIRECTION_ASCENDING = 'asc';

    /**
     * @var string
     */
    public const COLUMN_ORDER_DIRECTION_DESCENDING = 'desc';

    /**
     * @var string
     */
    public const NEXT_CONTINUATION_TOKEN = 'NextContinuationToken';

    /**
     * @var string
     */
    public const LOADED_TABLE_DATA = 'loadedTableData';

    /**
     * @var string
     */
    public const ACL_PUBLIC_READ = 'public-read';

    /**
     * @var string
     */
    public const S3_CLIENT_UPLOAD_CONTENT_TYPE = 'ContentType';

    /**
     * @var string
     */
    public const TABLE_LOADER_CONFIGURATION_NUMBER_OF_RECORDS_PER_REQUEST = 'TABLE_LOADER_CONFIGURATION:NUMBER_OF_RECORDS_PER_REQUEST';

    /**
     * @var string
     */
    public const MAX_KEYS = 'MaxKeys';

    /**
     * @var string
     */
    public const KEY_COUNT = 'KeyCount';

    /**
     * @var string
     */
    public const PREFIX = 'Prefix';

    /**
     * @var string
     */
    public const TABLE_ACTION = 'TableAction';

    /**
     * @var string
     */
    public const DELETED_FILES = 'deletedFiles';

    /**
     * @var string
     */
    public const UPLOADED_FILES = 'UploadedFiles';

    /**
     * @var string
     */
    public const LOAD_MORE_DATA = 'LoadMoreData';

    /**
     * @var string
     */
    public const FILTER = 'filter';

    /**
     * @var string
     */
    public const CLEAR_FILTER = 'clear-filter';

    /**
     * @var string
     */
    public const PAGING = 'Paging';

    /**
     * @var string
     */
    public const PAGE_LOAD = 'PageLoad';

    /**
     * @var string
     */
    public const SHOW_BUTTON = 'ShowButton';

    /**
     * @var string
     */
    public const LIST_OBJECTS_PARAM_CONTINUTION_TOKEN = 'ContinuationToken';

    /**
     * @var string
     */
    public const IS_TRUNCATED = 'IsTruncated';

    /**
     * @var string
     */
    public const ACTION = 'action';

    /**
     * @var string
     */
    public const SORTING = 'Sorting';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_GENERAL_UPLOAD = 's3.upload.error';

    /**
     * @var string
     */
    public const ERROR_MESSAGE_GENERAL_DELETE = 's3.delete.error';
}
