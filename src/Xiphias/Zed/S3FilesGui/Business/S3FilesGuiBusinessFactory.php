<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Business;

use Aws\S3\S3Client;
use Xiphias\Zed\S3FilesGui\Business\Model\Archiver\S3FilesArchiver;
use Xiphias\Zed\S3FilesGui\Business\Model\Archiver\S3FilesArchiverInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Deleter\S3FilesDeleter;
use Xiphias\Zed\S3FilesGui\Business\Model\Deleter\S3FilesDeleterInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Downloader\S3FilesDownloader;
use Xiphias\Zed\S3FilesGui\Business\Model\Downloader\S3FilesDownloaderInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDeleteMapper;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDeleteMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDownloadMapper;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDownloadMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesMapper;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilder;
use Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilderInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Uploader\S3FilesUploader;
use Xiphias\Zed\S3FilesGui\Business\Model\Uploader\S3FilesUploaderInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\Validator\Validator;
use Xiphias\Zed\S3FilesGui\Business\Model\Validator\ValidatorInterface;
use Xiphias\Zed\S3FilesGui\S3FilesGuiDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Translator\Business\TranslatorFacadeInterface;
use ZipArchive;

/**
 * @method \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig getConfig()
 */
class S3FilesGuiBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Downloader\S3FilesDownloaderInterface
     */
    public function createS3FilesDownloader(): S3FilesDownloaderInterface
    {
        return new S3FilesDownloader(
            $this->createFilesArchiver(),
            $this->createDownloadResponseBuilder(),
            $this->getS3Client(),
            $this->createDownloadMapper(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Deleter\S3FilesDeleterInterface
     */
    public function createS3FilesDeleter(): S3FilesDeleterInterface
    {
        return new S3FilesDeleter(
            $this->getS3Client(),
            $this->createS3FilesDeleteMapper(),
            $this->getTranslatorFacade(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Archiver\S3FilesArchiverInterface
     */
    public function createFilesArchiver(): S3FilesArchiverInterface
    {
        return new S3FilesArchiver($this->createZipArchive());
    }

    /**
     * @return \ZipArchive
     */
    protected function createZipArchive(): ZipArchive
    {
        return new ZipArchive();
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\ResponseBuilder\DownloadResponseBuilderInterface
     */
    public function createDownloadResponseBuilder(): DownloadResponseBuilderInterface
    {
        return new DownloadResponseBuilder();
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Validator\ValidatorInterface
     */
    public function createValidator(): ValidatorInterface
    {
        return new Validator(
            $this->getTranslatorFacade(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesMapperInterface
     */
    public function createS3FilesMapper(): S3FilesMapperInterface
    {
        return new S3FilesMapper();
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDeleteMapperInterface
     */
    public function createS3FilesDeleteMapper(): S3FilesDeleteMapperInterface
    {
        return new S3FilesDeleteMapper();
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Uploader\S3FilesUploaderInterface
     */
    public function createS3FilesUploader(): S3FilesUploaderInterface
    {
        return new S3FilesUploader(
            $this->getS3Client(),
            $this->createS3FilesMapper(),
            $this->getTranslatorFacade(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\S3FilesDownloadMapperInterface
     */
    public function createDownloadMapper(): S3FilesDownloadMapperInterface
    {
        return new S3FilesDownloadMapper();
    }

    /**
     * @return \Aws\S3\S3Client
     */
    public function getS3Client(): S3Client
    {
        return $this->getProvidedDependency(S3FilesGuiDependencyProvider::CLIENT_S3);
    }

    /**
     * @return \Spryker\Zed\Translator\Business\TranslatorFacadeInterface
     */
    public function getTranslatorFacade(): TranslatorFacadeInterface
    {
        return $this->getProvidedDependency(S3FilesGuiDependencyProvider::FACADE_TRANSLATOR);
    }
}
