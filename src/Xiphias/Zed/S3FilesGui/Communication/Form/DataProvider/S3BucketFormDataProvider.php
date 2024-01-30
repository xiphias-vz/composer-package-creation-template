<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Form\DataProvider;

use Aws\S3\S3Client;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface;
use Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface;
use Xiphias\Zed\S3FilesGui\Communication\Table\S3BucketTable;
use Symfony\Component\Form\FormInterface;

class S3BucketFormDataProvider implements S3BucketConstants, S3BucketFormConstants
{
    /**
     * @var string|null
     */
    protected ?string $tableIdentifier;

    /**
     * @param \Aws\S3\S3Client $s3Client
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\Mapper\SessionMapperInterface $sessionMapper
     * @param \Xiphias\Zed\S3FilesGui\Business\Model\TableDataLoader\TableDataLoaderInterface $tableDataLoader
     */
    public function __construct(
        protected S3Client $s3Client,
        protected SessionMapperInterface $sessionMapper,
        protected TableDataLoaderInterface $tableDataLoader
    ) {
        $this->tableIdentifier = static::TABLE_IDENTIFIER_PREFIX . md5(S3BucketTable::class);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            static::S3_BUCKET_OPTIONS => $this->getS3BucketData(),
        ];
    }

    /**
     * @return array
     */
    protected function getS3BucketData(): array
    {
        $buckets = $this->s3Client->listBuckets();

        $bucketList = [];
        foreach ($buckets[static::BUCKETS] as $bucketItem) {
            if (in_array($bucketItem[static::BUCKET_NAME], static::EXCLUDED_BUCKETS)) {
                continue;
            }

            $bucketList[$bucketItem[static::BUCKET_NAME]] = $bucketItem[static::BUCKET_NAME];
        }

        return $bucketList;
    }

    /**
     * @param array|null $bucketFilterForm
     *
     * @return void
     */
    public function setData(?array $bucketFilterForm): void
    {
        $this->sessionMapper->setSessionData($this->tableIdentifier, $bucketFilterForm);
        $this->tableDataLoader->resetLoadedTableData($bucketFilterForm[static::S3_BUCKET_FIELD] ?? '');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->sessionMapper->getSessionData($this->tableIdentifier);
    }

    /**
     * @return array
     */
    public function getFormFieldsData(): array
    {
        $data = [static::S3_BUCKET_FIELD => ''];

        if ($this->sessionMapper->getTableAction() !== '') {
            $formData = $this->sessionMapper->getSessionData($this->tableIdentifier);

            if (($formData[static::S3_BUCKETS_FORM] !== null)) {
                $data[static::S3_BUCKET_FIELD] = $this->sessionMapper->getSessionData($this->tableIdentifier)[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD];
            }
        }

        return $data;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $s3BucketForm
     *
     * @return void
     */
    public function handleFormAndTableData(FormInterface $s3BucketForm): void
    {
        if ($s3BucketForm->isSubmitted() && $s3BucketForm->isValid()) {
            $sessionBucket = $this->sessionMapper->getSessionData($this->tableIdentifier)[static::S3_BUCKETS_FORM][static::S3_BUCKET_FIELD] ?? null;
            if ($sessionBucket === null || $sessionBucket !== $s3BucketForm->getData()[static::S3_BUCKET_FIELD]) {
                $this->setActionAndFormData($s3BucketForm->getData(), static::SHOW_BUTTON);
            }
        }

        $this->tableDataLoader->handleTableAction();
    }

    /**
     * @param array|null $s3BucketFormData
     * @param string $action
     *
     * @return void
     */
    protected function setActionAndFormData(?array $s3BucketFormData, string $action): void
    {
        $this->sessionMapper->setTableAction($action);
        $this->setData($s3BucketFormData);
    }
}
