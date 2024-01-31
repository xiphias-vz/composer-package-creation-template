<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Xiphias\Zed\S3FilesGui\Communication\Form;

use Xiphias\Shared\S3FilesGui\S3BucketConstants;
use Xiphias\Shared\S3FilesGui\S3BucketFormConstants;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory getFactory()
 * @method \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig getConfig()
 * @method \Xiphias\Zed\S3FilesGui\Business\S3FilesGuiFacadeInterface getFacade()
 */
class S3BucketsForm extends AbstractType implements S3BucketFormConstants, S3BucketConstants
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            static::S3_BUCKET_OPTIONS => [],
        ]);
        $resolver->isRequired(static::S3_BUCKET_OPTIONS);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addS3BucketsField($builder, $options[static::S3_BUCKET_OPTIONS]);
        $this->addShowButton($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return void
     */
    private function addS3BucketsField(FormBuilderInterface $builder, array $choices): void
    {
        $builder
            ->add(static::S3_BUCKET_FIELD, ChoiceType::class, [
                'label' => false,
                'placeholder' => static::S3_BUCKETS_FIELD_OPTION_PLACEHOLDER,
                'choices' => $choices,
                'attr' => [
                    'class' => 's3-bucket-select-field',
                ],
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
    protected function addShowButton(FormBuilderInterface $builder): void
    {
        $builder->add(
            static::BUTTON_SHOW_FILES,
            SubmitType::class,
            [
                'label' => static::BUTTON_SHOW_FILES_LABEL,
                'attr' => [
                    'class' => static::BUTTON_SAFE_SUBMIT_CLASS,
                ],
            ],
        );
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(): NotBlank
    {
        return new NotBlank(['message' => static::VALIDATION_NOT_BLANK_MESSAGE]);
    }
}
