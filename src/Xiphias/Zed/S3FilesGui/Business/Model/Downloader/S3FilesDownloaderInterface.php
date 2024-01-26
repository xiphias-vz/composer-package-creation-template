<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Downloader;

use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadResponseTransfer;

interface S3FilesDownloaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    public function downloadFiles(S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer): S3FilesDownloadResponseTransfer;
}
