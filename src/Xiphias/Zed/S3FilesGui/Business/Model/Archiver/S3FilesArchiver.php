<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Archiver;

use Generated\Shared\Transfer\S3FilesResultsTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use ZipArchive;

class S3FilesArchiver implements S3FilesArchiverInterface, S3BucketConstants
{
    /**
     * @param \ZipArchive $archive
     */
    public function __construct(protected ZipArchive $archive)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesResultsTransfer $s3FilesResultsTransfer
     *
     * @return string
     */
    public function archiveFiles(S3FilesResultsTransfer $s3FilesResultsTransfer): string
    {
        $zipName = $this->generateArchiveName();
        $this->archive->open($zipName, ZipArchive::CREATE);

        foreach ($s3FilesResultsTransfer->getSuccessfulResults() as $file) {
            $this->archive->addFromString($file->getFileName(), $file->getFileContent());
        }

        $this->archive->close();

        return $zipName;
    }

    /**
     * @return string
     */
    protected function generateArchiveName(): string
    {
        return uniqid();
    }
}
