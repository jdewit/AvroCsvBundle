<?php
namespace Avro\CsvBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/*
 * Preview Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class PreviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        ld($options['fields']); exit;
        $builder
            ->add('delimiter', 'hidden')
            ->add('filePath', 'hidden')
            ->add('fields', 'collection', array(
                'label' => 'Fields',
                'type' => 'choice',
                'options' => array(
                    'choices' => $options['fields']
                ),
                'allow_add' => true,
                'attr' => array(
                    'class' => 'field-choice'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'avro_csv_import_preview';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'fields' => array()
        ));
    }

}
