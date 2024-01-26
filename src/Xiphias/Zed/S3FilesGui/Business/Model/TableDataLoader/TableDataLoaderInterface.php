<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader;

use Generated\Shared\Transfer\CustomDataTableTransfer;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

interface TableDataLoaderInterface
{
    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @param array $formData
     * @param int $numberOfRecordsPerRequest
     * @param string $action
     *
     * @return \Generated\Shared\Transfer\CustomDataTableTransfer|null
     */
    public function listObjectsFromBucket(
        TableConfiguration $config,
        array $formData,
        int $numberOfRecordsPerRequest,
        string $action
    ): ?CustomDataTableTransfer;

    /**
     * @param string $bucketField
     *
     * @return void
     */
    public function resetLoadedTableData(string $bucketField): void;

    /**
     * @return void
     */
    public function handleTableAction(): void;

    /**
     * @param array $deletedFileNames
     *
     * @return void
     */
    public function removeDeletedFilesFromLoadedTableTransfer(array $deletedFileNames): void;
}
