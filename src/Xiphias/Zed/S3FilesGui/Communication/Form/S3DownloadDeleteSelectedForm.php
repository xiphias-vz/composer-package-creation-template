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
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \Xiphias\Zed\S3FilesGui\Communication\S3FilesGuiCommunicationFactory getFactory()
 * @method \Xiphias\Zed\S3FilesGui\S3FilesGuiConfig getConfig()
 * @method \Xiphias\Zed\S3FilesGui\Business\S3FilesGuiFacadeInterface getFacade()
 */
class S3DownloadDeleteSelectedForm extends AbstractType implements S3BucketFormConstants, S3BucketConstants
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addDownloadSelectedButton($builder);
        $this->addDeleteSelectedButton($builder);
        $this->setAction($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    public function addDownloadSelectedButton(FormBuilderInterface $builder): void
    {
        $builder->add(
            static::BUTTON_DOWNLOAD,
            SubmitType::class,
            [
                'label' => static::BUTTON_DOWNLOAD_LABEL,
                'attr' => [
                   'class' => static::DOWNLOAD_SELECTED_BUTTON_CLASSES_DISABLED,
                   'disabled' => true,
                ],
            ],
        );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    public function addDeleteSelectedButton(FormBuilderInterface $builder): void
    {
        $builder->add(
            static::BUTTON_DELETE,
            ButtonType::class,
            [
                'label' => static::BUTTON_DELETE_LABEL,
                'attr' => [
                    'class' => static::DELETE_SELECTED_BUTTON_CLASSES_DISABLED,
                    'data-toggle' => static::DATA_TOGGLE_MODAL,
                    'data-target' => static::DATA_TARGET_MODAL_CLASS,
                    'disabled' => true,
                ],
            ],
        );
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    public function setAction(FormBuilderInterface $builder): void
    {
        $builder->setAction(
            static::ROUTE_HANDLE_SELECTED_FILES,
        );
    }
}
