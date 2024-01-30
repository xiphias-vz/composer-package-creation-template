<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper;

use Generated\Shared\Transfer\LoadMoreDataTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Client\Session\SessionClientInterface;

class SessionMapper implements SessionMapperInterface, S3BucketFormConstants, S3BucketConstants
{
    /**
     * @var string
     */
    protected const COLON_SEPARATOR = ':';

    /**
     * @param \Spryker\Client\Session\SessionClientInterface $sessionClient
     */
    public function __construct(
        protected SessionClientInterface $sessionClient
    ) {
    }

    /**
     * @param string|null $tableIdentifier
     * @param array|null $bucketFilterForm
     *
     * @return void
     */
    public function setSessionData(?string $tableIdentifier, ?array $bucketFilterForm): void
    {
        $this->sessionClient->set(
            $tableIdentifier . static::COLON_SEPARATOR . (static::S3_BUCKETS_FORM),
            $bucketFilterForm,
        );
    }

    /**
     * @param string|null $tableIdentifier
     *
     * @return array
     */
    public function getSessionData(?string $tableIdentifier): array
    {
        $idS3Bucket = $this->sessionClient->get($tableIdentifier . static::COLON_SEPARATOR . static::S3_BUCKETS_FORM, null);

        return [
            static::S3_BUCKETS_FORM => $idS3Bucket,
        ];
    }

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setSelectedFileNames(array $fileNames): void
    {
        $this->sessionClient->set(static::SELECTED_FILES_NAMES, $fileNames);
    }

    /**
     * @return array|null
     */
    public function getSelectedFileNames(): ?array
    {
        return $this->sessionClient->get(static::SELECTED_FILES_NAMES);
    }

    /**
     * @return void
     */
    public function removeSelectedFileNames(): void
    {
        $this->sessionClient->remove(static::SELECTED_FILES_NAMES);
    }

    /**
     * @param string $searchString
     *
     * @return void
     */
    public function setSearchString(string $searchString): void
    {
        $this->sessionClient->set(static::SEARCH_STRING_SESSION_KEY, $searchString);
    }

    /**
     * @param string $tableAction
     *
     * @return void
     */
    public function setTableAction(string $tableAction): void
    {
        $this->sessionClient->set(static::TABLE_ACTION, $tableAction);
    }

    /**
     * @return string
     */
    public function getTableAction(): string
    {
        return $this->sessionClient->get(static::TABLE_ACTION) ?? '';
    }

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setDeletedFiles(array $fileNames): void
    {
        $this->sessionClient->set(static::DELETED_FILES, $fileNames);
    }

    /**
     * @return void
     */
    public function clearDeletedFiles(): void
    {
        $this->sessionClient->remove(static::DELETED_FILES);
    }

    /**
     * @return array
     */
    public function getDeletedFiles(): array
    {
        return $this->sessionClient->get(static::DELETED_FILES) ?? [];
    }

    /**
     * @return \Generated\Shared\Transfer\LoadMoreDataTransfer|null
     */
    public function getLoadMoreTableData(): ?LoadMoreDataTransfer
    {
        return $this->sessionClient->get(static::LOADED_TABLE_DATA) ?? null;
    }

    /**
     * @param \Generated\Shared\Transfer\LoadMoreDataTransfer|null $loadedTableDataTransfer
     *
     * @return void
     */
    public function setLoadedTableData(?LoadMoreDataTransfer $loadedTableDataTransfer): void
    {
        $this->sessionClient->set(static::LOADED_TABLE_DATA, $loadedTableDataTransfer);
    }

    /**
     * @return void
     */
    public function removeSearchString(): void
    {
        $this->sessionClient->remove(static::SEARCH_STRING_SESSION_KEY);
    }

    /**
     * @return string
     */
    public function getSearchString(): string
    {
        return $this->sessionClient->get(static::SEARCH_STRING_SESSION_KEY) ?? '';
    }

    /**
     * @param string $filterString
     *
     * @return void
     */
    public function setFilterString(string $filterString): void
    {
        $this->sessionClient->set(static::FILTER_STRING_SESSION_KEY, $filterString);
    }

    /**
     * @return void
     */
    public function removeFilterString(): void
    {
        $this->sessionClient->remove(static::FILTER_STRING_SESSION_KEY);
    }

    /**
     * @return string
     */
    public function getFilterString(): string
    {
        return $this->sessionClient->get(static::FILTER_STRING_SESSION_KEY) ?? '';
    }

    /**
     * @return array
     */
    public function getUsedFilterStrings(): array
    {
        return $this->sessionClient->get(static::USED_FILTER_STRINGS) ?? [];
    }

    /**
     * @param string $filterString
     *
     * @return void
     */
    public function addUsedFilterString(string $filterString): void
    {
        if ($this->getUsedFilterStrings()) {
            $alreadyUsedStrings = $this->getUsedFilterStrings();
            $alreadyUsedStrings[] = $filterString;
            $this->sessionClient->set(static::USED_FILTER_STRINGS, $alreadyUsedStrings);

            return;
        }

        $this->sessionClient->set(static::USED_FILTER_STRINGS, [$filterString]);
    }

    /**
     * @return void
     */
    public function clearUsedFilterStrings(): void
    {
        $this->sessionClient->remove(static::USED_FILTER_STRINGS);
    }
}
