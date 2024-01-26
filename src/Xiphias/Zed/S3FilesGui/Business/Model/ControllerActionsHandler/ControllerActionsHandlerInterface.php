<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\ControllerActionsHandler;

use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface ControllerActionsHandlerInterface
{
    /**
     * @return void
     */
    public function removeFilterString(): void;

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setFilterString(?Request $request): void;

    /**
     * @return string
     */
    public function getFilterString(): string;

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setSearchString(?Request $request): void;

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return void
     */
    public function setTableAction(?Request $request): void;

    /**
     * @return void
     */
    public function handleTableAction(): void;

    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleS3BucketFormAndTableData(?Request $request): FormInterface;

    /**
     * @return string|null
     */
    public function getBucketName(): string|null;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function getFileNames(Request $request): array;

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer
     */
    public function getS3FilesDownloadRequest(string $bucketName, array $fileNames): S3FilesDownloadRequestTransfer;

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer
     */
    public function getS3FilesDeleteRequest(string $bucketName, array $fileNames): S3FilesDeleteRequestTransfer;

    /**
     * @param array $fileNames
     *
     * @return void
     */
    public function setDeletedFilesAndTableAction(array $fileNames): void;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleS3DownloadDeleteSelectedForm(Request $request): FormInterface;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function handleUploadAction(Request $request): FormInterface;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function downloadDeleteRedirect(Request $request): string;
}
