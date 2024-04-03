<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication;

use Aws\S3\S3Client;
use Generated\Shared\Transfer\S3UploadTransfer;
use Spryker\Client\Session\SessionClientInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Xiphias\Zed\S3FilesGui\Communication\ControllerActionsHandler\ControllerActionsHandler;
use Xiphias\Zed\S3FilesGui\Communication\ControllerActionsHandler\ControllerActionsHandlerInterface;
use Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider;
use Xiphias\Zed\S3FilesGui\Communication\Form\S3BucketsForm;
use Xiphias\Zed\S3FilesGui\Communication\Form\S3BucketsUploadForm;
use Xiphias\Zed\S3FilesGui\Communication\Form\S3DownloadDeleteSelectedForm;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\Request\S3FilesRequestMapper;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\Request\S3FilesRequestMapperInterface;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\S3FilesMapper;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\S3FilesMapperInterface;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\Session\SessionMapper;
use Xiphias\Zed\S3FilesGui\Communication\Mapper\Session\SessionMapperInterface;
use Xiphias\Zed\S3FilesGui\Communication\Table\S3BucketTable;
use Xiphias\Zed\S3FilesGui\Communication\Table\TableDataLoader\TableDataLoader;
use Xiphias\Zed\S3FilesGui\Communication\Table\TableDataLoader\TableDataLoaderInterface;
use Xiphias\Zed\S3FilesGui\S3FilesGuiDependencyProvider;

/**
 * @method \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig getConfig()
 * @method \Xiphias\Zed\S3FilesGui\Business\S3FilesGuiFacadeInterface getFacade()
 */
class S3FilesGuiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Xiphias\Zed\S3FilesGui\Communication\Table\S3BucketTable
     */
    public function createS3BucketTable(): S3BucketTable
    {
        return new S3BucketTable(
            $this->createS3BucketDataProvider(),
            $this->createTableDataLoader(),
            $this->getConfig(),
            $this->createSessionMapper(),
        );
    }

    /**
     * @param array $formData
     * @param array $formOptions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createS3BucketForm(array $formData = [], array $formOptions = []): FormInterface
    {
        return $this->getFormFactory()->create(S3BucketsForm::class, $formData, $formOptions);
    }

    /**
     * @param \Generated\Shared\Transfer\S3UploadTransfer|null $data
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createS3UploadForm(?S3UploadTransfer $data = null, array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(S3BucketsUploadForm::class, $data, $options);
    }

    /**
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponse(string $content, int $statusCode, array $headers): Response
    {
        return new Response(
            $content,
            $statusCode,
            $headers,
        );
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getS3DownloadDeleteSelectedForm(): FormInterface
    {
        return $this->getFormFactory()->create(S3DownloadDeleteSelectedForm::class);
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider\S3BucketFormDataProvider
     */
    public function createS3BucketDataProvider(): S3BucketFormDataProvider
    {
        return new S3BucketFormDataProvider(
            $this->getS3Client(),
            $this->createSessionMapper(),
            $this->createTableDataLoader(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Communication\Mapper\Request\S3FilesRequestMapperInterface
     */
    public function createS3FilesRequestMapper(): S3FilesRequestMapperInterface
    {
        return new S3FilesRequestMapper();
    }

    /**
     * @return TableDataLoaderInterface
     */
    public function createTableDataLoader(): TableDataLoaderInterface
    {
        return new TableDataLoader(
            $this->getS3Client(),
            $this->createSessionMapper(),
            $this->createS3FilesMapper(),
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\Upload\S3FilesUploadMapperInterface
     */
    public function createS3FilesMapper(): S3FilesMapperInterface
    {
        return new S3FilesMapper();
    }

    /**
     * @return ControllerActionsHandlerInterface
     */
    public function createControllerActionsHandler(): ControllerActionsHandlerInterface
    {
        return new ControllerActionsHandler(
            $this->createS3BucketDataProvider(),
            $this->createTableDataLoader(),
            $this->createSessionMapper(),
            $this,
        );
    }

    /**
     * @return \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface
     */
    public function createSessionMapper(): SessionMapperInterface
    {
        return new SessionMapper(
            $this->getSessionClient(),
        );
    }

    /**
     * @return \Spryker\Client\Session\SessionClientInterface
     */
    public function getSessionClient(): SessionClientInterface
    {
        return $this->getProvidedDependency(S3FilesGuiDependencyProvider::CLIENT_SESSION);
    }

    /**
     * @return \Aws\S3\S3Client
     */
    public function getS3Client(): S3Client
    {
        return $this->getProvidedDependency(S3FilesGuiDependencyProvider::CLIENT_S3);
    }
}
