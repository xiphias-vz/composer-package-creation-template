<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Download;

use Aws\Result;
use Generated\Shared\Transfer\S3DownloadResultTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;

class S3FilesDownloadMapper implements S3FilesDownloadMapperInterface, S3BucketConstants
{
    /**
     * @param \Aws\Result $result
     * @param string $fileName
     *
     * @return \Generated\Shared\Transfer\S3DownloadResultTransfer
     */
    public function mapResultToSuccessfulDownloadResultTransfer(Result $result, string $fileName): S3DownloadResultTransfer
    {
        return (new S3DownloadResultTransfer())
            ->setIsSuccessful(true)
            ->setFileName($fileName)
            ->setFileContent($result->get(static::BODY)->getContents())
            ->setContentType($result->get(static::AWS_RESLT_CONTENT_TYPE_KEY));
    }

    /**
     * @param string $errorMessage
     * @param string $fileName
     *
     * @return \Generated\Shared\Transfer\S3DownloadResultTransfer
     */
    public function mapResultToFailedDownloadResultTransfer(string $errorMessage, string $fileName): S3DownloadResultTransfer
    {
        return (new S3DownloadResultTransfer())
            ->setIsSuccessful(false)
            ->setFileName($fileName)
            ->setErrorMessage($errorMessage);
    }
}
