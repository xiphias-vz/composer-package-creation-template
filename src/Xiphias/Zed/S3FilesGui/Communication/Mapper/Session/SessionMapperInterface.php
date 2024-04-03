<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Mapper\Session;

use Generated\Shared\Transfer\LoadMoreDataTransfer;

interface SessionMapperInterface
{
    /**
     * @param string|null $tableIdentifier
     * @param array|null $bucketFilterForm
     *
     * @return void
     */
    public function setSessionData(?string $tableIdentifier, ?array $bucketFilterForm): void;

    /**
     * @param string|null $tableIdentifier
     *
     * @return array
     */
    public function getSessionData(?string $tableIdentifier): array;

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setSelectedFileNames(array $fileNames): void;

    /**
     * @return array|null
     */
    public function getSelectedFileNames(): ?array;

    /**
     * @return void
     */
    public function removeSelectedFileNames(): void;

    /**
     * @param string $searchString
     *
     * @return void
     */
    public function setSearchString(string $searchString): void;

    /**
     * @param string $tableAction
     *
     * @return void
     */
    public function setTableAction(string $tableAction): void;

    /**
     * @return string
     */
    public function getTableAction(): string;

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setDeletedFiles(array $fileNames): void;

    /**
     * @return void
     */
    public function clearDeletedFiles(): void;

    /**
     * @return array
     */
    public function getDeletedFiles(): array;

    /**
     * @return \Generated\Shared\Transfer\LoadMoreDataTransfer|null
     */
    public function getLoadMoreTableData(): ?LoadMoreDataTransfer;

    /**
     * @param \Generated\Shared\Transfer\LoadMoreDataTransfer|null $loadedTableDataTransfer
     *
     * @return void
     */
    public function setLoadedTableData(?LoadMoreDataTransfer $loadedTableDataTransfer): void;

    /**
     * @return void
     */
    public function removeSearchString(): void;

    /**
     * @return string
     */
    public function getSearchString(): string;

    /**
     * @param string $filterString
     *
     * @return void
     */
    public function setFilterString(string $filterString): void;

    /**
     * @return void
     */
    public function removeFilterString(): void;

    /**
     * @return string
     */
    public function getFilterString(): string;

    /**
     * @return array
     */
    public function getUsedFilterStrings(): array;

    /**
     * @param string $filterString
     *
     * @return void
     */
    public function addUsedFilterString(string $filterString): void;

    /**
     * @return void
     */
    public function clearUsedFilterStrings(): void;
}
