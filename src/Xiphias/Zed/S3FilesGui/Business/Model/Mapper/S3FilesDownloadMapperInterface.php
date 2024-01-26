<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper;

use Aws\Result;
use Generated\Shared\Transfer\S3DownloadResultTransfer;

interface S3FilesDownloadMapperInterface
{
    /**
     * @param \Aws\Result $result
     * @param string $fileName
     *
     * @return \Generated\Shared\Transfer\S3DownloadResultTransfer
     */
    public function mapResultToSuccessfulDownloadResultTransfer(Result $result, string $fileName): S3DownloadResultTransfer;

    /**
     * @param string $errorMessage
     * @param string $fileName
     *
     * @return \Generated\Shared\Transfer\S3DownloadResultTransfer
     */
    public function mapResultToFailedDownloadResultTransfer(string $errorMessage, string $fileName): S3DownloadResultTransfer;
}
