<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Mapper;

use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;

interface S3FilesRequestMapperInterface
{
    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer
     */
    public function mapBucketAndFileNamesToS3FilesDownloadRequestTransfer(string $bucketName, array $fileNames): S3FilesDownloadRequestTransfer;

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer
     */
    public function mapBucketAndFileNamesToS3FileDeleteRequestTransfer(string $bucketName, array $fileNames): S3FilesDeleteRequestTransfer;
}
