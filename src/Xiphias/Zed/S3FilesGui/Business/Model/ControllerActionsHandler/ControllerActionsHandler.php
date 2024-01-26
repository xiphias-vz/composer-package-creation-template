<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\ControllerActionsHandler;

use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface;
use Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider;
use Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerActionsHandler implements ControllerActionsHandlerInterface, S3BucketConstants, S3BucketFormConstants
{
    /**
     * @param \Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider $s3BucketFormDataProvider
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface $tableDataLoader
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface $sessionMapper
     * @param \Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory $s3FilesGuiCommunicationFactory
     */
    public function __construct(
        protected S3BucketFormDataProvider $s3BucketFormDataProvider,
        protected TableDataLoaderInterface $tableDataLoader,
        protected SessionMapperInterface $sessionMapper,
        protected S3FilesGuiCommunicationFactory $s3FilesGuiCommunicationFactory
    ) {
    }

    /**
     * @return void
     */
    public function removeFilterString(): void
    {
        $this->sessionMapper->removeFilterString();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setFilterString(?Request $request): void
    {
        $filterParam = $request->get(static::FILTER_PARAM) ?? '';

        if ($filterParam) {
            $this->sessionMapper->setFilterString($filterParam);
        }

        if (filter_var($request->get(static::CLEAR_USED_FILTERS_FROM_SESSION), FILTER_VALIDATE_BOOLEAN)) {
            $this->sessionMapper->clearUsedFilterStrings();
        }
    }

    /**
     * @return string
     */
    public function getFilterString(): string
    {
        return $this->sessionMapper->getFilterString();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setSearchString(?Request $request): void
    {
        $searchParam = $request->get(static::SEARCH_PARAM)[static::SEARCH_PARAM_VALUE_KEY] ?? '';

        if ($searchParam) {
            $this->sessionMapper->setSearchString($searchParam);
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setTableAction(?Request $request): void
    {
        $tableAction = $request->request->get(static::ACTION) ?? $request->query->get(static::ACTION) ?? $this->sessionMapper->getTableAction();
        $this->sessionMapper->setTableAction($tableAction);
    }

    /**
     * @return void
     */
    public function handleTableAction(): void
    {
        $this->tableDataLoader->handleTableAction();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleS3BucketFormAndTableData(?Request $request): FormInterface
    {
        $s3BucketForm = $this->s3FilesGuiCommunicationFactory
            ->createS3BucketForm(
                $this->s3BucketFormDataProvider->getFormFieldsData(),
                $this->s3BucketFormDataProvider->getOptions(),
            )
            ->handleRequest($request);

        $this->s3BucketFormDataProvider->handleFormAndTableData($s3BucketForm);

        return $s3BucketForm;
    }

    /**
     * @return string|null
     */
    public function getBucketName(): string|null
    {
        return $this->s3BucketFormDataProvider->getData()[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD] ?? null;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function getFileNames(Request $request): array
    {
        $fileNames = $this->sessionMapper->getSelectedFileNames() ?? [];
        if (!$fileNames) {
            $name = $request->query->get(static::FILE_NAME_QUERY_PARAM);
            if ($name) {
                $fileNames[] = $name;
            }
        }

        return $fileNames;
    }

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer
     */
    public function getS3FilesDownloadRequest(string $bucketName, array $fileNames): S3FilesDownloadRequestTransfer
    {
        $s3FilesDownloadRequestTransfer = $this->s3FilesGuiCommunicationFactory
            ->createS3FilesRequestMapper()
            ->mapBucketAndFileNamesToS3FilesDownloadRequestTransfer($bucketName, $fileNames);

        $this->sessionMapper->removeSelectedFileNames();

        return $s3FilesDownloadRequestTransfer;
    }

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer
     */
    public function getS3FilesDeleteRequest(string $bucketName, array $fileNames): S3FilesDeleteRequestTransfer
    {
        $deleteRequestTransfer = $this->s3FilesGuiCommunicationFactory
            ->createS3FilesRequestMapper()
            ->mapBucketAndFileNamesToS3FileDeleteRequestTransfer($bucketName, $fileNames);

        $this->sessionMapper->removeSelectedFileNames();

        return $deleteRequestTransfer;
    }

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setDeletedFilesAndTableAction(array $fileNames): void
    {
        $this->sessionMapper->setDeletedFiles($fileNames);
        $this->sessionMapper->setTableAction(static::DELETED_FILES);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleS3DownloadDeleteSelectedForm(Request $request): FormInterface
    {
        $this->sessionMapper->setSelectedFileNames($request->get(static::S3_FILE_NAMES_PARAM) ?? []);

        return $this->s3FilesGuiCommunicationFactory->getS3DownloadDeleteSelectedForm()->handleRequest($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleUploadAction(Request $request): FormInterface
    {
        $this->sessionMapper->setTableAction(static::UPLOADED_FILES);

        return $this->s3FilesGuiCommunicationFactory->createS3UploadForm(
            new S3UploadTransfer(),
        )->handleRequest($request);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function downloadDeleteRedirect(Request $request): string
    {
        $downloadDeleteSelectedFormData = $request->get(static::S3_DOWNLOAD_DELETE_SELECTED_FORM);
        if (array_key_exists(static::DOWNLOAD, $downloadDeleteSelectedFormData)) {
            return static::ROUTE_DOWNLOAD;
        }

        if (array_key_exists(static::DELETE, $downloadDeleteSelectedFormData)) {
            return static::ROUTE_DELETE;
        }

        return static::ROUTE_INDEX;
    }
}
