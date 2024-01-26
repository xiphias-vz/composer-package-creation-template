<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Downloader;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Generated\Shared\Transfer\S3DownloadResultTransfer;
use Generated\Shared\Transfer\S3FilesDownloadRequestTransfer;
use Generated\Shared\Transfer\S3FilesDownloadResponseTransfer;
use Generated\Shared\Transfer\S3FilesResultsTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Archiver\S3FilesArchiverInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDownloadMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilderInterface;

class S3FilesDownloader implements S3FilesDownloaderInterface, S3BucketConstants
{
    /**
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Archiver\S3FilesArchiverInterface $archiver
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilderInterface $responseBuilder
     * @param \Aws\S3\S3Client $s3Client
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDownloadMapperInterface $downloadMapper
     */
    public function __construct(
        protected S3FilesArchiverInterface $archiver,
        protected DownloadResponseBuilderInterface $responseBuilder,
        protected S3Client $s3Client,
        protected S3FilesDownloadMapperInterface $downloadMapper
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    public function downloadFiles(S3FilesDownloadRequestTransfer $s3FilesDownloadRequestTransfer): S3FilesDownloadResponseTransfer
    {
        $s3FilesResultsTransfer = $this->getFilesFromS3Bucket(
            $s3FilesDownloadRequestTransfer->getBucketAndFileNames()->getBucketName(),
            $s3FilesDownloadRequestTransfer->getBucketAndFileNames()->getFileNames(),
        );

        if ($s3FilesResultsTransfer->getErrorResults()->count()) {
            return $this->responseBuilder->buildDownloadFilesResponse($s3FilesResultsTransfer);
        }

        if ($this->isZipFileNecessary($s3FilesResultsTransfer->getSuccessfulResults()->count())) {
            $s3FilesResultsTransfer->setZipName($this->archiver->archiveFiles($s3FilesResultsTransfer));
        }

        $downloadResponseTransfer = $this->responseBuilder->buildDownloadFilesResponse($s3FilesResultsTransfer);

        if ($s3FilesResultsTransfer->getZipName()) {
            unlink($s3FilesResultsTransfer->getZipName());
        }

        return $downloadResponseTransfer;
    }

    /**
     * @param string $bucketName
     * @param array $fileNames
     *
     * @return \Generated\Shared\Transfer\S3FilesResultsTransfer
     */
    protected function getFilesFromS3Bucket(string $bucketName, array $fileNames): S3FilesResultsTransfer
    {
        $s3FilesResults = new S3FilesResultsTransfer();

        foreach ($fileNames as $fileName) {
            $result = $this->getS3DownloadResult($bucketName, $fileName);

            if ($result->getIsSuccessful()) {
                $s3FilesResults->getSuccessfulResults()->append($result);

                continue;
            }

            $s3FilesResults->getErrorResults()->append($result);
        }

        return $s3FilesResults;
    }

    /**
     * @param string $bucketName
     * @param string $fileName
     *
     * @return \Generated\Shared\Transfer\S3DownloadResultTransfer
     */
    protected function getS3DownloadResult(string $bucketName, string $fileName): S3DownloadResultTransfer
    {
        try {
            $result = $this->s3Client->getObject([
                static::BUCKET => $bucketName,
                static::KEY => $fileName,
            ]);

            $resultTransfer = $this->downloadMapper->mapResultToSuccessfulDownloadResultTransfer($result, $fileName);
        } catch (AwsException $exception) {
            $resultTransfer = $this->downloadMapper->mapResultToFailedDownloadResultTransfer($exception->getAwsErrorMessage(), $fileName);
        }

        return $resultTransfer;
    }

    /**
     * @param int $numberOfFilesForDownload
     *
     * @return bool
     */
    protected function isZipFileNecessary(int $numberOfFilesForDownload): bool
    {
        return $numberOfFilesForDownload > static::ONE_FILE_COUNT;
    }
}
