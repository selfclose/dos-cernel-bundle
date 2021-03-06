<?php

namespace DoS\CernelBundle\Form\Type;

use DoS\CernelBundle\Doctrine\Phpcr\ManagerHelper;
use DoS\CernelBundle\Model\MediaInterface;
use DoS\CernelBundle\Model\MediaPathAwareInterface;
use Sylius\Bundle\MediaBundle\Form\DataTransformer\PathToDocumentTransformer;
use Sylius\Bundle\MediaBundle\Form\Type\ImageType as BaseImageType;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperDoctrine;
use Symfony\Cmf\Bundle\MediaBundle\File\UploadFileHelperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends BaseImageType
{
    /**
     * @var UploadFileHelperInterface|UploadFileHelperDoctrine
     */
    protected $uploadFileHelper;

    /**
     * @var string
     */
    protected $mediaRoot = '/cms/medias';

    /**
     * @param UploadFileHelperInterface $uploadFileHelper
     */
    public function setUploadFileHelper(UploadFileHelperInterface $uploadFileHelper = null)
    {
        $this->uploadFileHelper = $uploadFileHelper;
    }

    /**
     * @param string $mediaRoot
     */
    public function setMediaRoot($mediaRoot)
    {
        $this->mediaRoot = $mediaRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['preview'] === true) {
            $options['preview'] = 'image_upload_thumbnail';
        }

        $builder->add(
            $builder
                ->create('media', 'cmf_media_image', array('label' => false, 'imagine_filter' => $options['preview']))
                ->addViewTransformer(new PathToDocumentTransformer($this->documentManager))
        );

        if (!$this->uploadFileHelper) {
            return;
        }

        $builder->get('media')
            // add media path
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                if ($parent = $event->getForm()->getParent()) {
                    if (!$parent = $parent->getParent()) {
                        return;
                    }

                    if (!($mediaAware = $parent->getData()) instanceof MediaPathAwareInterface) {
                        return;
                    }

                    if ($path = $mediaAware->getMediaPath()) {
                        $dirs = ManagerHelper::mkdirs($this->documentManager, $this->mediaRoot . $path);
                        $this->uploadFileHelper->setRootPath(end($dirs)->getId());
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function() {
                // reset to root base
                $this->uploadFileHelper->setRootPath($this->mediaRoot);
            })
        ;

        # FIXME: remove when https://github.com/Sylius/Sylius/pull/2975 was fix.
        # @see parent => Sylius/Bundle/MediaBundle/Form/Type/ImageType
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var MediaInterface $data */
            if (!$data = $event->getData()) {
                return;
            }

            if (null !== ($media = $data->getMedia())) {
                $mediaId = $media->getId() ?: $media->getParentDocument()->getId() .'/'. $media->getNodename();
                if ($mediaId !== $data->getMediaId()) {
                    // This actually helps trigger preUpdate doctrine event since
                    // doctrine is not tracking changes on $media field of Image entity.
                    //
                    // Here we forcefully update $mediaId (which is tracked by doctrine) to trigger
                    // a change if a new media has been uploaded/selected.
                    $data->setMediaId($mediaId);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['preview'] = $options['preview'] ?: false;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'preview' => false, # liip_imagine_filter or true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_image';
    }
}
