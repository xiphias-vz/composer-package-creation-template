<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Uploader;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Generated\Shared\Transfer\S3UploadResponseTransfer;
use Generated\Shared\Transfer\S3UploadTransfer;
use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Upload\S3FilesUploadMapperInterface;

class S3FilesUploader implements S3FilesUploaderInterface, S3BucketConstants, S3BucketFormConstants
{
    /**
     * @param \Aws\S3\S3Client $s3Client
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Upload\S3FilesUploadMapperInterface $filesMapper
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct(
        protected S3Client                     $s3Client,
        protected S3FilesUploadMapperInterface $filesMapper,
        protected TranslatorFacadeInterface    $translatorFacade
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer $uploadTransfer
     *
     * @return \Generated\Shared\Transfer\S3UploadResponseTransfer
     */
    public function upload(S3UploadTransfer $uploadTransfer): S3UploadResponseTransfer
    {
        try {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile */
            $uploadedFile = $uploadTransfer->getUploadedFile();
            $bucket = $uploadTransfer->getBucket();
            $key = $uploadedFile->getClientOriginalName();
            $isUploadSuccessful = true;

            $this->s3Client->upload(
                $bucket,
                $key,
                $uploadedFile->getContent(),
                static::ACL_PUBLIC_READ,
                [static::S3_CLIENT_UPLOAD_CONTENT_TYPE => $uploadedFile->getMimeType()],
            );

            return $this->filesMapper->mapMessageToNewUploadResponseTransfer(
                $uploadTransfer,
                $isUploadSuccessful,
                sprintf(
                    $this->translatorFacade->trans(static::SUCCESS_MESSAGE_UPLOAD),
                    $key,
                    $bucket,
                ),
            );
        } catch (S3Exception) {
            $isUploadSuccessful = false;

            return $this->filesMapper->mapMessageToNewUploadResponseTransfer(
                $uploadTransfer,
                $isUploadSuccessful,
                $this->translatorFacade->trans(static::ERROR_MESSAGE_GENERAL_UPLOAD),
            );
        }
    }
}
