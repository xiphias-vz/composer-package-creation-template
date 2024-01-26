<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper;

use Aws\Result;
use Generated\Shared\Transfer\LoadMoreDataTransfer;
use Generated\Shared\Transfer\S3ListObjectsResultTransfer;
use Generated\Shared\Transfer\S3UploadResponseTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;

class S3FilesMapper implements S3FilesMapperInterface, S3BucketFormConstants, S3BucketConstants
{
 /**
  * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadTransfer
  * @param bool $isSuccessful
  * @param string $message
  *
  * @return \Generated\Shared\Transfer\S3UploadResponseTransfer
  */
    public function mapMessageToNewUploadResponseTransfer(
        S3UploadTransfer $uploadTransfer,
        bool $isSuccessful,
        string $message
    ): S3UploadResponseTransfer {
        return (new S3UploadResponseTransfer())
            ->setS3Upload($uploadTransfer)
            ->setIsSuccessful($isSuccessful)
            ->setMessage($message);
    }

    /**
     * @param string $nextContinuationToken
     * @param array $bucketData
     * @param string $bucketName
     *
     * @return \Generated\Shared\Transfer\LoadMoreDataTransfer
     */
    public function mapLoadedMoreDataTransfer(string $nextContinuationToken, array $bucketData, string $bucketName): LoadMoreDataTransfer
    {
        return (new LoadMoreDataTransfer())
            ->setBucketName($bucketName)
            ->setNextContinuationToken($nextContinuationToken)
            ->setLoadedTableData($bucketData);
    }

    /**
     * @param \Aws\Result $listObjectsResult
     *
     * @return \Generated\Shared\Transfer\S3ListObjectsResultTransfer
     */
    public function mapListObjectsResultToListObjectsResultTransfer(Result $listObjectsResult): S3ListObjectsResultTransfer
    {
        return (new S3ListObjectsResultTransfer())->fromArray($listObjectsResult->toArray(), true);
    }
}
