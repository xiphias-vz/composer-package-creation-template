<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Table;

use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface;
use Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider;
use Xiphias\Zed\S3FilesGui\S3FilesGuiConfig;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class S3BucketTable extends AbstractTable implements S3BucketConstants, S3BucketFormConstants
{
    /**
     * @param \Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider $s3BucketFormDataProvider
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface $tableDataLoader
     * @param \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig $s3FilesGuiConfig
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface $sessionMapper
     */
    public function __construct(
        protected S3BucketFormDataProvider $s3BucketFormDataProvider,
        protected TableDataLoaderInterface $tableDataLoader,
        protected S3FilesGuiConfig $s3FilesGuiConfig,
        protected SessionMapperInterface $sessionMapper
    ) {
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $url = Url::generate(static::S3_TABLE_URL, $this->getRequest()->query->all())->build();
        $config->setUrl($url);

        $config->setHeader(
            [
                static::TABLE_COL_CHECKBOX => '',
                static::TABLE_COL_NAME => static::HEADER_FILE_NAME,
                static::TABLE_COL_ACTIONS => static::TABLE_COL_ACTIONS_HEADER,
            ],
        );

        $config->addRawColumn(static::TABLE_COL_ACTIONS);
        $config->addRawColumn(static::TABLE_COL_CHECKBOX);

        $config->setSortable([
            static::TABLE_COL_NAME,
        ]);
        $config->setDefaultSortField(static::TABLE_COL_CHECKBOX, '');
        $config->setDefaultSortField(static::TABLE_COL_NAME, static::COLUMN_ORDER_DIRECTION_ASCENDING);

        $config->setPaging(true);
        $config->setStateSave(false);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $result = [];
        $formData = $this->sessionMapper->getSessionData($this->tableIdentifier);
        $action = $this->sessionMapper->getTableAction();
        $numberOfRecordsPerRequest = $this->s3FilesGuiConfig->getNumberOfRecordsPerRequest();
        $files = $this->tableDataLoader->listObjectsFromBucket($config, $formData, $numberOfRecordsPerRequest, $action);
        if ($files !== null && $files[static::BUCKET_FILES_DATA] !== null) {
            foreach ($files[static::BUCKET_FILES_DATA] as $file) {
                $rowData = $this->formatRow($file);

                $result[] = $rowData;
            }

            $files->setResult($result);

            return $files->toArray();
        }

        return $result;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getCheckboxHtml(string $file): string
    {
        $info = [
            'name' => static::S3_FILE_NAMES_PARAM,
        ];

        return sprintf(
            "<input id='file_checkbox_%s' name='%s[]' value='%s' class='checkbox' type='checkbox' data-info='%s' form='scope_handle_selected_files'>",
            $file,
            static::S3_FILE_NAMES_PARAM,
            $file,
            json_encode($info, JSON_THROW_ON_ERROR),
        );
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getActionButtons(string $file): string
    {
        $buttons = [];
        $buttons[] = $this->createDownloadButton($file);
        $buttons[] = $this->createDeleteButton($file);

        return implode(' ', $buttons);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function createDownloadButton(string $file): string
    {
        $bucketName = $this->s3BucketFormDataProvider->getData()[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD];

        $downloadFileUrl = Url::generate(
            static::ROUTE_DOWNLOAD,
            [
                static::BUCKET_NAME_QUERY_PARAM => $bucketName,
                static::FILE_NAME_QUERY_PARAM => $file,
            ],
        );

        return $this->generateDownloadButton($downloadFileUrl->build(), static::DOWNLOAD);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function createDeleteButton(string $file): string
    {
        $bucketName = $this->s3BucketFormDataProvider->getData()[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD];

        $deleteFileUrl = Url::generate(
            static::ROUTE_DELETE,
            [
                static::BUCKET_NAME_QUERY_PARAM => $bucketName,
                static::FILE_NAME_QUERY_PARAM => $file,
            ],
        );

        return $this->generateDeleteButton($deleteFileUrl->build(), static::DELETE);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array<string, mixed> $options
     *
     * @return string
     */
    protected function generateDownloadButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => static::CLASS_BTN_DOWNLOAD,
            'icon' => static::ICON_FA_DOWNLOAD,
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function generateDeleteButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => static::CLASS_BTN_DANGER,
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function formatRow(string $fileName): array
    {
        return [
            static::TABLE_COL_CHECKBOX => $this->getCheckboxHtml($fileName),
            static::TABLE_COL_NAME => $fileName,
            static::TABLE_COL_ACTIONS => $this->getActionButtons($fileName),
        ];
    }

    /**
     * @return array
     */
    public function fetchData(): array
    {
        $this->init();

        $data = $this->prepareData($this->config);
        $this->sessionMapper->setTableAction('');

        return $this->generateTableDataArray($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function generateTableDataArray(array $data): array
    {
        if (!empty($data[static::BUCKET_FILES_DATA])) {
            $this->loadData($data[static::CUSTOM_DATATABLE_RESULT]);
            $loadedTableDataTransfer = $this->sessionMapper->getLoadMoreTableData();

            return [
                static::CUSTOM_DATATABLE_RECORDS_DRAW => $this->request->query->getInt(static::CUSTOM_DATATABLE_RECORDS_DRAW, 1),
                static::CUSTOM_DATATABLE_RECORDS_TOTAL => $data[static::CUSTOM_DATATABLE_CONFIGURATION][static::CUSTOM_DATATABLE_RECORDS_TOTAL],
                static::CUSTOM_DATATABLE_RECORDS_FILTERED => $data[static::CUSTOM_DATATABLE_CONFIGURATION][static::CUSTOM_DATATABLE_RECORDS_FILTERED],
                static::BUCKET_FILES_DATA => $this->data,
                static::NEXT_CONTINUATION_TOKEN => $loadedTableDataTransfer->getNextContinuationToken(),
            ];
        } else {
            $this->loadData([]);

            return [
                static::CUSTOM_DATATABLE_RECORDS_DRAW => $this->request->query->getInt(static::CUSTOM_DATATABLE_RECORDS_DRAW, 1),
                static::CUSTOM_DATATABLE_RECORDS_TOTAL => $this->total,
                static::CUSTOM_DATATABLE_RECORDS_FILTERED => $this->filtered,
                static::BUCKET_FILES_DATA => $this->data,
                static::NEXT_CONTINUATION_TOKEN => '',
            ];
        }
    }
}
