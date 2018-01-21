<?php

namespace Avro\CsvBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * CSV Import Form Type.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ImportFormType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'choice', array(
                'label' => 'Delimiter',
                'choices' => array(
                    ',' => 'comma',
                    ';' => 'semicolon',
                    '|' => 'pipe',
                    ':' => 'colon',
                ),
            ))
            ->add('file', 'file', array(
                'label' => 'File',
                'required' => true,
            ))
            ->add('filename', 'hidden', array(
                'required' => false,
            ))
            ->add('fields', 'collection', array(
                'label' => 'Fields',
                'required' => false,
                'type' => 'choice',
                'options' => array(
                    'choices' => $options['field_choices'],
                ),
                'allow_add' => true,
            ));

        $builder->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data || !array_key_exists('file', $data)) {
                return;
            }

            $data['filename'] = $data['file']->getFilename();
            $event->setData($data);
        });
    }

    /**
     * Set default options.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     *
     * @deprecated since version 2.7, to be renamed in 3.0.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'field_choices' => array(),
        ));
    }

    /**
     * Get the forms name.
     *
     * @return string name
     */
    public function getName()
    {
        return 'avro_csv_import';
    }
}
