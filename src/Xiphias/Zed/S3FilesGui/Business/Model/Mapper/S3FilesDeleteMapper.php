<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Mapper;

use Aws\Result;
use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDeleteResponseTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;

class S3FilesDeleteMapper implements S3FilesDeleteMapperInterface, S3BucketConstants
{
    /**
     * @var string
     */
    protected const DELETED_FILE_NAMES_SEPARATOR = ', ';

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapDeleteRequestTransferToNewResponseTransfer(S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer): S3FilesDeleteResponseTransfer
    {
        return (new S3FilesDeleteResponseTransfer())
            ->setS3FilesDeleteRequest($s3FilesDeleteRequestTransfer)
            ->setIsSuccessful(false)
            ->setMessage(static::MESSAGE_DELETE_FAILED);
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer $responseTransfer
     * @param string $message
     * @param \Aws\Result $result
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapMessageToDeleteResponseTransfer(
        S3FilesDeleteResponseTransfer $responseTransfer,
        string $message,
        Result $result
    ): S3FilesDeleteResponseTransfer {
        $responseTransfer
            ->setIsSuccessful(true)
            ->setMessage(
                sprintf(
                    $message,
                    $this->buildFileNamesString($result),
                    $responseTransfer->getS3FilesDeleteRequest()->getBucketAndFileNames()->getBucketName(),
                ),
            );

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer $responseTransfer
     * @param string $message
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function mapMessageToFailedDeleteResponseTransfer(S3FilesDeleteResponseTransfer $responseTransfer, string $message): S3FilesDeleteResponseTransfer
    {
        $responseTransfer
            ->setIsSuccessful(false)
            ->setMessage($message);

        return $responseTransfer;
    }

    /**
     * @param \Aws\Result $result
     *
     * @return string
     */
    protected function buildFileNamesString(Result $result): string
    {
        $deletedFilesString = '';
        $deletedFiles = $result->get(static::DELETED_KEY);

        foreach ($deletedFiles as $deletedFile) {
            $deletedFilesString .= $deletedFile[static::KEY] . static::DELETED_FILE_NAMES_SEPARATOR;
        }

        return rtrim($deletedFilesString, static::DELETED_FILE_NAMES_SEPARATOR);
    }
}
