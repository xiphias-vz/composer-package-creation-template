<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Form;

use Generated\Shared\Transfer\S3UploadTransfer;
use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory getFactory()
 * @method \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig getConfig()
 * @method \Xiphias\Zed\S3FilesGui\Business\S3FilesGuiFacadeInterface getFacade()
 */
class S3BucketsUploadForm extends AbstractType implements S3BucketConstants, S3BucketFormConstants
{
 /**
  * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
  *
  * @return void
  */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data-class' => S3UploadTransfer::class,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $this->addUploadField($builder);
        $this->addUploadButton($builder);
        $this->addBucketField($builder);
        $this->setAction($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addUploadField(FormBuilderInterface $builder): void
    {
        $builder->add(
            static::UPLOADED_FILE,
            FileType::class,
            [
            'label' => false,
            'constraints' => [
                $this->createNotBlankConstraint(),
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addUploadButton(FormBuilderInterface $builder): void
    {
        $builder->add(static::BUTTON_UPLOAD, SubmitType::class,
            [
                'label' => static::BUTTON_UPLOAD_LABEL,
                'attr' => [
                    'class' => static::BUTTON_PRIMARY_SUBMIT_CLASS,
                ],
            ],
        );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addBucketField(FormBuilderInterface $builder): void
    {
        $builder->add(static::HIDDEN_BUCKET_FIELD, HiddenType::class);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function setAction(FormBuilderInterface $builder): void
    {
        $builder->setAction(static::ROUTE_UPLOAD);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(): NotBlank
    {
        return new NotBlank(['message' => static::VALIDATION_NOT_BLANK_MESSAGE]);
    }
}
