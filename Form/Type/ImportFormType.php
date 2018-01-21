<?php

namespace Avro\CsvBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add(
                'delimiter',
                ChoiceType::class,
                array(
                    'choices' => array(
                        'comma' => ',',
                        'semicolon' => ';',
                        'pipe' => '|',
                        'colon' => ':',
                    ),
                    'choices_as_values' => true,
                    'label' => 'Delimiter',
                )
            )
            ->add(
                'file',
                FileType::class,
                array(
                    'label' => 'File',
                    'required' => true,
                )
            )
            ->add(
                'filename',
                HiddenType::class,
                array(
                    'required' => false,
                )
            )
            ->add(
                'fields',
                CollectionType::class,
                array(
                    'allow_add' => true,
                    'entry_type' => ChoiceType::class,
                    'entry_options' => array(
                        'choices' => $options['field_choices'],
                    ),
                    'label' => 'Fields',
                    'required' => false,
                )
            );

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data || !array_key_exists('file', $data)) {
                return;
            }

            $data['filename'] = $data['file']->getFilename();
            $event->setData($data);
        });
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
    public function getBlockPrefix()
    {
        return 'avro_csv_import';
    }
}
