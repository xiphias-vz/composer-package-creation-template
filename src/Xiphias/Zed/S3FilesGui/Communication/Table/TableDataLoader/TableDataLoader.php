<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Xiphias\Zed\S3FilesGui\Communication\Table\TableDataLoader;

use Aws\S3\S3Client;
use Generated\Shared\Transfer\CustomDataTableTransfer;
use Generated\Shared\Transfer\LoadMoreDataTransfer;
use Generated\Shared\Transfer\S3ListObjectsResultTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\S3FilesMapperInterface;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\Session\SessionMapperInterface;

class TableDataLoader implements TableDataLoaderInterface, S3BucketConstants, S3BucketFormConstants
{
    /**
     * @param S3Client $s3Client
     * @param SessionMapperInterface $sessionMapper
     * @param S3FilesMapperInterface $fileMapper
     */
    public function __construct(
        protected S3Client $s3Client,
        protected SessionMapperInterface $sessionMapper,
        protected S3FilesMapperInterface $fileMapper
    ) {
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @param array $formData
     * @param int $numberOfRecordsPerRequest
     * @param string $action
     *
     * @return \Generated\Shared\Transfer\CustomDataTableTransfer|null
     */
    public function listObjectsFromBucket(TableConfiguration $config, array $formData, int $numberOfRecordsPerRequest, string $action): ?CustomDataTableTransfer
    {
        $dataTable = new CustomDataTableTransfer();

        $firstRecordIndex = $this->findAttributeInUrl($config->getUrl(), static::REG_EXP_FIND_PAGE_START_INDEX, 0);
        $columnOrder = $this->findAttributeInUrl($config->getUrl(), static::REG_EXP_FIND_ORDER_DIRECTION, $config->getDefaultSortField()[static::TABLE_COL_NAME]);

        $pageNumber = $this->calculatePageNumber($firstRecordIndex, $config->getPageLength());
        $lastRecordIndex = $config->getPageLength() * $pageNumber;

        $data = [];
        $paginatedData = [];
        $bucketData = [];

        if (($formData[static::S3_BUCKETS_FORM] !== null)) {
            $bucketName = $formData[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD];

            $bucketData[static::BUCKET_CONTENTS] = $this->getFilesFromBucket($bucketName, $numberOfRecordsPerRequest, $action);

            if ($bucketData[static::BUCKET_CONTENTS] !== null) {
                $data = $this->orderColumnByDirection($bucketData[static::BUCKET_CONTENTS], $columnOrder, $action);
                $this->saveLoadedTableDataToSession($data);

                $data = $this->defaultTableSearch($data);

                for ($i = $firstRecordIndex; $i < $lastRecordIndex; $i++) {
                    if (array_key_exists($i, $data)) {
                        $paginatedData[] = $data[$i];
                    }
                }
            }

            $dataTable->setConfiguration(
                [
                    static::CUSTOM_DATATABLE_RECORDS_TOTAL => count($data),
                    static::CUSTOM_DATATABLE_RECORDS_FILTERED => count($data),
                ],
            );
            $dataTable->setData($paginatedData);

            return $dataTable;
        }

        return null;
    }

    /**
     * @param string $bucketField
     *
     * @return void
     */
    public function resetLoadedTableData(string $bucketField): void
    {
        if ($this->sessionMapper->getTableAction() === static::SHOW_BUTTON) {
            $this->sessionMapper->setLoadedTableData(null);
        }
    }

    /**
     * @return void
     */
    public function handleTableAction(): void
    {
        switch ($this->sessionMapper->getTableAction()) {
            case static::DELETED_FILES:
                if ($this->sessionMapper->getDeletedFiles()) {
                    $this->removeDeletedFilesFromLoadedTableTransfer($this->sessionMapper->getDeletedFiles());
                }

                break;
            case static::UPLOADED_FILES:
            case static::LOAD_MORE_DATA:
            case static::PAGING:
            case static::PAGE_LOAD:
            case static::SORTING:
                break;
            case static::SHOW_BUTTON:
            case static::CLEAR_FILTER:
                $this->sessionMapper->removeFilterString();

                break;
            case static::FILTER:
            default:
                $this->sessionMapper->setLoadedTableData(null);
        }
    }

    /**
     * @param array $deletedFileNames
     *
     * @return void
     */
    public function removeDeletedFilesFromLoadedTableTransfer(array $deletedFileNames): void
    {
        $loadedTableData = $this->sessionMapper->getLoadMoreTableData();

        $modifiedTableData = $this->findAndDeleteItems($loadedTableData->getLoadedTableData(), $deletedFileNames);
        $loadedTableData->setLoadedTableData($modifiedTableData);

        $this->sessionMapper->clearDeletedFiles();

        $this->sessionMapper->setLoadedTableData($loadedTableData);
    }

    /**
     * @param array $haystack
     * @param array $needles
     *
     * @return array
     */
    protected function findAndDeleteItems(array $haystack, array $needles): array
    {
        $keys = array_fill_keys($needles, true);
        foreach ($haystack as $key => $value) {
            if (isset($keys[$value[static::KEY]])) {
                unset($haystack[$key]);
            }
        }

        return array_values($haystack);
    }

    /**
     * @param array $data
     * @param string $order
     * @param string $action
     *
     * @return array
     */
    protected function orderColumnByDirection(array $data, string $order, string $action): array
    {
        if ($action === static::SORTING) {
            usort($data, function ($a, $b) use ($order) {
                if ($order === static::COLUMN_ORDER_DIRECTION_DESCENDING) {
                    return $b <=> $a;
                }

                return $a <=> $b;
            });
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function saveLoadedTableDataToSession(array $data): void
    {
        $loadedTableData = $this->sessionMapper->getLoadMoreTableData();
        $loadedTableData->setLoadedTableData($data);

        $this->sessionMapper->setLoadedTableData($loadedTableData);
    }

    /**
     * @param string $url
     * @param string $pattern
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    protected function findAttributeInUrl(string $url, string $pattern, mixed $defaultValue): mixed
    {
        preg_match($pattern, $url, $results);

        return $results[1] ?? $defaultValue;
    }

    /**
     * @param int $startIndex
     * @param int $pageLength
     *
     * @return int
     */
    protected function calculatePageNumber(int $startIndex, int $pageLength): int
    {
        return ($startIndex / $pageLength) + 1;
    }

    /**
     * @param string $bucketName
     * @param int $numberOfRecordsPerRequest
     * @param string $action
     *
     * @return array
     */
    protected function getFilesFromBucket(string $bucketName, int $numberOfRecordsPerRequest, string $action): array
    {
        $loadMoreTableDataTransfer = $this->sessionMapper->getLoadMoreTableData();
        if ($action === static::FILTER || $action === static::CLEAR_FILTER) {
            $loadMoreTableDataTransfer = null;
        }

        if ($loadMoreTableDataTransfer === null || $this->checkIfFilesUploaded($action) || $this->checkIfBucketChanged($loadMoreTableDataTransfer, $bucketName)) {
            return $this->getLoadedTableDataCombinedWithDataFromS3($bucketName, $numberOfRecordsPerRequest, null);
        }

        $loadedTableData = $loadMoreTableDataTransfer->getLoadedTableData();

        if ($action === static::SORTING) {
            return $loadedTableData;
        }

        if ($action !== static::PAGING) {
            $nextContinuationToken = $loadMoreTableDataTransfer->getNextContinuationToken();

            if ($this->returnOnlyLoadedData($loadedTableData, $nextContinuationToken, $action)) {
                return $loadedTableData;
            }

            $loadedTableData = $this->getLoadedTableDataCombinedWithDataFromS3($bucketName, $numberOfRecordsPerRequest, $loadMoreTableDataTransfer);
        }

        return $loadedTableData;
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    protected function checkIfFilesUploaded(string $action): bool
    {
        return $action === static::UPLOADED_FILES;
    }

    /**
     * @param \Generated\Shared\Transfer\LoadMoreDataTransfer|null $loadMoreDataTransfer
     * @param string $bucketName
     *
     * @return bool
     */
    protected function checkIfBucketChanged(?LoadMoreDataTransfer $loadMoreDataTransfer, string $bucketName): bool
    {
        return $loadMoreDataTransfer->getBucketName() !== $bucketName;
    }

    /**
     * @param array $loadedTableData
     * @param string $nextContinuationToken
     * @param string $action
     *
     * @return bool
     */
    protected function returnOnlyLoadedData(array $loadedTableData, string $nextContinuationToken, string $action): bool
    {
        return ($loadedTableData && $nextContinuationToken === '') || ($action === static::SHOW_BUTTON && $loadedTableData);
    }

    /**
     * @param string $bucketName
     * @param int $numberOfRecordsPerRequest
     * @param \Generated\Shared\Transfer\LoadMoreDataTransfer|null $loadMoreTableDataTransfer
     *
     * @return array
     */
    protected function getLoadedTableDataCombinedWithDataFromS3(
        string $bucketName,
        int $numberOfRecordsPerRequest,
        ?LoadMoreDataTransfer $loadMoreTableDataTransfer
    ): array {
        $loadMoreTableDataTransfer = $this->loadMoreData($bucketName, $numberOfRecordsPerRequest, $loadMoreTableDataTransfer);

        return $loadMoreTableDataTransfer->getLoadedTableData();
    }

    /**
     * @param string $bucketName
     * @param int $numberOfRecordsPerRequest
     * @param \Generated\Shared\Transfer\LoadMoreDataTransfer|null $loadMoreTableDataTransfer
     *
     * @return \Generated\Shared\Transfer\LoadMoreDataTransfer|null
     */
    protected function loadMoreData(string $bucketName, int $numberOfRecordsPerRequest, ?LoadMoreDataTransfer $loadMoreTableDataTransfer): ?LoadMoreDataTransfer
    {
        $bucketData = [];
        $nextContinuationToken = '';
        if ($loadMoreTableDataTransfer !== null) {
            $nextContinuationToken = $loadMoreTableDataTransfer->getNextContinuationToken();
            $bucketData = $loadMoreTableDataTransfer->getLoadedTableData();
        }

        $listObjectsResultTransfer = $this->getNewRecordsFromS3($bucketName, $numberOfRecordsPerRequest, $nextContinuationToken);

        $bucketData = $this->addS3ResultToBucketData($bucketData, $listObjectsResultTransfer);

        $nextContinuationTokenFromResult = '';
        if ($listObjectsResultTransfer->getIsTruncated()) {
            $nextContinuationTokenFromResult = $listObjectsResultTransfer->getNextContinuationToken();
        }

        $loadedTableDataTransfer = $this->fileMapper->mapLoadedMoreDataTransfer($nextContinuationTokenFromResult, $bucketData, $bucketName);

        $this->sessionMapper->setLoadedTableData($loadedTableDataTransfer);

        return $loadedTableDataTransfer;
    }

    /**
     * @param array $bucketData
     * @param \Generated\Shared\Transfer\S3ListObjectsResultTransfer $listObjectsResultTransfer
     *
     * @return array
     */
    protected function addS3ResultToBucketData(array $bucketData, S3ListObjectsResultTransfer $listObjectsResultTransfer): array
    {
        if ($listObjectsResultTransfer->getContents()) {
            foreach ($listObjectsResultTransfer->getContents() as $object) {
                $bucketData[] = $object;
            }
        }

        return $bucketData;
    }

    /**
     * @param string $bucketName
     * @param int $maxKeys
     * @param string $nextContinuationToken
     *
     * @return \Generated\Shared\Transfer\S3ListObjectsResultTransfer
     */
    protected function getNewRecordsFromS3(string $bucketName, int $maxKeys, string $nextContinuationToken): S3ListObjectsResultTransfer
    {
        $params = $this->prepareParams($bucketName, $maxKeys, $nextContinuationToken);

        $filterString = $this->sessionMapper->getFilterString();

        if ($filterString) {
            return $this->getRecordsWithFilterStringCombinations($filterString, $params);
        }

        $result = $this->s3Client->listObjectsV2($params);

        return $this->fileMapper->mapListObjectsResultToListObjectsResultTransfer($result);
    }

    /**
     * @param string $bucketName
     * @param int $maxKeys
     * @param string $nextContinuationToken
     *
     * @return array
     */
    protected function prepareParams(string $bucketName, int $maxKeys, string $nextContinuationToken): array
    {
        $params = [
            static::BUCKET => $bucketName,
            static::MAX_KEYS => $maxKeys,
        ];

        if ($nextContinuationToken) {
            $params[static::LIST_OBJECTS_PARAM_CONTINUTION_TOKEN] = $nextContinuationToken;
        }

        return $params;
    }

    /**
     * @param string $filterString
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\S3ListObjectsResultTransfer
     */
    protected function getRecordsWithFilterStringCombinations(string $filterString, array $params): S3ListObjectsResultTransfer
    {
        $listObjectsResultTransfer = new S3ListObjectsResultTransfer();
        $listObjectsPreviousResultTransfer = new S3ListObjectsResultTransfer();
        $maxNumberOfObjectsInResult = $params[static::MAX_KEYS];

        $filterStringCombinations = array_unique([$filterString, strtoupper($filterString), strtolower($filterString), ucfirst(strtolower($filterString))]);

        foreach ($filterStringCombinations as $filterStringCombination) {
            if ($this->isFilterStringCombinationAlreadyUsed($filterStringCombination)) {
                continue;
            }

            $listObjectsResultTransfer = $this->getRecordsForCurrentPrefixCombination($filterStringCombination, $params);
            $listObjectsResultTransfer = $this->mergeWithPrevious($listObjectsPreviousResultTransfer, $listObjectsResultTransfer);

            if (!$listObjectsResultTransfer->getIsTruncated()) {
                $this->sessionMapper->addUsedFilterString($filterStringCombination);
            }

            if ($listObjectsResultTransfer->getKeyCount() === $maxNumberOfObjectsInResult) {
                return $listObjectsResultTransfer;
            }

            $listObjectsPreviousResultTransfer = $listObjectsResultTransfer;
            $params[static::MAX_KEYS] = $listObjectsResultTransfer->getMaxKeys() - $listObjectsResultTransfer->getKeyCount();
        }
        $this->sessionMapper->clearUsedFilterStrings();

        return $listObjectsResultTransfer;
    }

    /**
     * @param string $prefix
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\S3ListObjectsResultTransfer
     */
    protected function getRecordsForCurrentPrefixCombination(string $prefix, array $params): S3ListObjectsResultTransfer
    {
        $params[static::PREFIX] = $prefix;
        $result = $this->s3Client->listObjectsV2($params);

        return $this->fileMapper->mapListObjectsResultToListObjectsResultTransfer($result);
    }

    /**
     * @param string $filterStringCombination
     *
     * @return bool
     */
    protected function isFilterStringCombinationAlreadyUsed(string $filterStringCombination): bool
    {
        return in_array($filterStringCombination, $this->sessionMapper->getUsedFilterStrings());
    }

    /**
     * @param \Generated\Shared\Transfer\S3ListObjectsResultTransfer $listObjectsPreviousResultTransfer
     * @param \Generated\Shared\Transfer\S3ListObjectsResultTransfer $listObjectsResultTransfer
     *
     * @return \Generated\Shared\Transfer\S3ListObjectsResultTransfer
     */
    protected function mergeWithPrevious(
        S3ListObjectsResultTransfer $listObjectsPreviousResultTransfer,
        S3ListObjectsResultTransfer $listObjectsResultTransfer
    ): S3ListObjectsResultTransfer {
        $listObjectsResultTransfer->setContents(array_merge($listObjectsPreviousResultTransfer->getContents(), $listObjectsResultTransfer->getContents()));
        $listObjectsResultTransfer->setKeyCount(($listObjectsPreviousResultTransfer->getKeyCount() ?? 0) + ($listObjectsResultTransfer->getKeyCount() ?? 0));

        return $listObjectsResultTransfer;
    }

    /**
     * @param array $bucketContents
     *
     * @return array
     */
    protected function defaultTableSearch(array $bucketContents): array
    {
        $data = $this->extractFileNames($bucketContents);
        $this->sessionMapper->removeSearchString();

        return $data;
    }

    /**
     * @param array $bucketContents
     *
     * @return array
     */
    protected function extractFileNames(array $bucketContents): array
    {
        $data = [];
        $searchString = $this->sessionMapper->getSearchString();

        if ($searchString) {
            return $this->extractFileNamesContainingSearchString($bucketContents, $searchString);
        }

        foreach ($bucketContents as $content) {
            $data[] = $content;
        }

        return $data;
    }

    /**
     * @param array $bucketContents
     * @param string $searchString
     *
     * @return array
     */
    protected function extractFileNamesContainingSearchString(array $bucketContents, string $searchString): array
    {
        $data = [];
        foreach ($bucketContents as $content) {
            $fileName = is_array($content) ? $content[static::KEY] : $content;
            if ($this->isSearchStringInFileName($fileName, $searchString)) {
                $data[] = $fileName;
            }
        }

        return $data;
    }

    /**
     * @param string $fileName
     * @param string $searchString
     *
     * @return bool
     */
    protected function isSearchStringInFileName(string $fileName, string $searchString): bool
    {
        return str_contains(strtolower($fileName), strtolower(trim($searchString)));
    }
}
