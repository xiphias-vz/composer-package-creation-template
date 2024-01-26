<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business\Model\Deleter;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Generated\Shared\Transfer\S3FilesDeleteRequestTransfer;
use Generated\Shared\Transfer\S3FilesDeleteResponseTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDeleteMapperInterface;
use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;

class S3FilesDeleter implements S3FilesDeleterInterface, S3BucketConstants
{
    /**
     * @param \Aws\S3\S3Client $s3Client
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDeleteMapperInterface $deleteMapper
     * @param \Spryker\Zed\Translator\Business\TranslatorFacadeInterface $translatorFacade
     */
    public function __construct(
        protected S3Client $s3Client,
        protected S3FilesDeleteMapperInterface $deleteMapper,
        protected TranslatorFacadeInterface $translatorFacade
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer
     *
     * @return \Generated\Shared\Transfer\S3FilesDeleteResponseTransfer
     */
    public function deleteFiles(S3FilesDeleteRequestTransfer $s3FilesDeleteRequestTransfer): S3FilesDeleteResponseTransfer
    {
        $responseTransfer = $this->deleteMapper->mapDeleteRequestTransferToNewResponseTransfer($s3FilesDeleteRequestTransfer);

        try {
            $objects = [];
            foreach ($s3FilesDeleteRequestTransfer->getBucketAndFileNames()->getFileNames() as $fileName) {
                $objects[] = [
                    static::KEY => $fileName,
                ];
            }

            if ($objects) {
                $result = $this->s3Client->deleteObjects([
                    static::BUCKET => $s3FilesDeleteRequestTransfer->getBucketAndFileNames()->getBucketName(),
                    static::DELETE => [
                        static::OBJECTS => $objects,
                    ],
                ]);

                $responseTransfer = $this->deleteMapper->mapMessageToDeleteResponseTransfer(
                    $responseTransfer,
                    $this->translatorFacade->trans(static::MESSAGE_DELETE_SUCCESSFUL),
                    $result,
                );
            }
        } catch (AwsException) {
            $responseTransfer = $this->deleteMapper->mapMessageToFailedDeleteResponseTransfer(
                $responseTransfer,
                $this->translatorFacade->trans(static::ERROR_MESSAGE_GENERAL_DELETE),
            );
        }

        return $responseTransfer;
    }
}
