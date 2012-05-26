<?php
namespace Avro\CsvBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/*
 * CSV Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CsvFormType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delimiter', 'choice', array(
                'label' => 'Delimiter',
                'choices' => array(
                    ',' => 'comma', 
                    ';' => 'semicolon',
                    '|' => 'pipe',
                    ':' => 'colon'
                )
            ))
            ->add('file', 'file', array(
                'label' => 'File',
                'required' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'avro_csv_csv';
    }
}
