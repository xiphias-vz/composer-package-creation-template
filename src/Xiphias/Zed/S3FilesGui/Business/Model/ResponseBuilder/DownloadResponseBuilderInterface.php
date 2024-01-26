<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder;

use Generated\Shared\Transfer\S3FilesDownloadResponseTransfer;
use Generated\Shared\Transfer\S3FilesResultsTransfer;

interface DownloadResponseBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    public function buildDownloadFilesResponse(S3FilesResultsTransfer $s3FilesResultsTransfer): S3FilesDownloadResponseTransfer;
}
