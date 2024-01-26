<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder;

use ArrayObject;
use Generated\Shared\Transfer\S3DownloadResultTransfer;
use Generated\Shared\Transfer\S3FilesDownloadResponseTransfer;
use Generated\Shared\Transfer\S3FilesResultsTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;

class DownloadResponseBuilder implements DownloadResponseBuilderInterface, S3BucketConstants
{
    /**
     * @var string
     */
    protected const DOWNLOAD_FILE_NAMES_SEPARATOR = ', ';

    /**
     * @var string
     */
    protected const PARENTHESIS_LEFT = '(';

    /**
     * @var string
     */
    protected const PARENTHESIS_RIGHT = ')';

    /**
     * @var int
     */
    protected const FIRST_FILE_INDEX = 0;

    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    public function buildDownloadFilesResponse(S3FilesResultsTransfer $s3FilesResultsTransfer): S3FilesDownloadResponseTransfer
    {
        if ($s3FilesResultsTransfer->getErrorResults()->count()) {
            return $this->buildDownloadErrorResponse($s3FilesResultsTransfer->getErrorResults());
        }

        return $this->buildDownloadSuccessfulResponse($s3FilesResultsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    protected function buildDownloadSuccessfulResponse(S3FilesResultsTransfer $s3FilesResultsTransfer): S3FilesDownloadResponseTransfer
    {
        $headers = $this->buildDownloadResponseHeaders($s3FilesResultsTransfer);
        $content = $this->buildDownloadResponseBody($s3FilesResultsTransfer);

        return (new S3FilesDownloadResponseTransfer())
            ->setHeaders($headers)
            ->setContent($content)
            ->setIsSuccessful(true);
    }

    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\S3DownloadResultTransfer> $errorResults
     *
     * @return \Generated\Shared\Transfer\S3FilesDownloadResponseTransfer
     */
    protected function buildDownloadErrorResponse(ArrayObject $errorResults): S3FilesDownloadResponseTransfer
    {
        return (new S3FilesDownloadResponseTransfer())
            ->setContent($this->buildFailedDownloadErrorMessage($errorResults))
            ->setIsSuccessful(false);
    }

    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\S3DownloadResultTransfer> $errorResults
     *
     * @return string
     */
    protected function buildFailedDownloadErrorMessage(ArrayObject $errorResults): string
    {
        $errorMessage = '';

        foreach ($errorResults as $errorResult) {
            $errorMessage .= $errorResult->getFilename() .
                static::PARENTHESIS_LEFT .
                $errorResult->getErrorMessage() .
                static::PARENTHESIS_RIGHT .
                static::DOWNLOAD_FILE_NAMES_SEPARATOR;
        }

        return rtrim($errorMessage, static::DOWNLOAD_FILE_NAMES_SEPARATOR);
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return string
     */
    protected function buildDownloadResponseBody(S3FilesResultsTransfer $s3FilesResultsTransfer): string
    {
        if ($s3FilesResultsTransfer->getZipName()) {
            return file_get_contents($s3FilesResultsTransfer->getZipName());
        }

        $file = $s3FilesResultsTransfer->getSuccessfulResults()->offsetGet(static::FIRST_FILE_INDEX);

        return $file->getFileContent();
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return array
     */
    protected function buildDownloadResponseHeaders(S3FilesResultsTransfer $s3FilesResultsTransfer): array
    {
        if ($s3FilesResultsTransfer->getZipName()) {
            return $this->buildArchiveFileDownloadResponseHeaders($s3FilesResultsTransfer->getZipName());
        }

        return $this->buildSingleFileDownloadResponseHeaders($s3FilesResultsTransfer->getSuccessfulResults()->offsetGet(static::FIRST_FILE_INDEX));
    }

    /**
     * @param string $zipName
     *
     * @return array
     */
    protected function buildArchiveFileDownloadResponseHeaders(string $zipName): array
    {
        return [
            static::CONTENT_TYPE => static::APPLICATION_ZIP,
            static::CONTENT_DISPOSITION => static::ATTACHMENT_WITH_FILENAME . $zipName . static::ZIP_FILE_EXTENSION,
            static::PRAGMA => static::PUBLIC,
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\S3DownloadResultTransfer $result
     *
     * @return array
     */
    protected function buildSingleFileDownloadResponseHeaders(S3DownloadResultTransfer $result): array
    {
        return [
            static::CONTENT_TYPE => $result->getContentType(),
            static::CONTENT_DISPOSITION => static::ATTACHMENT_WITH_FILENAME . $result->getFileName(),
            static::PRAGMA => static::PUBLIC,
        ];
    }
}
