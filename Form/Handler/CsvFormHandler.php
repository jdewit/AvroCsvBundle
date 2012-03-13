<?php
namespace Avro\CsvBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Avro\CsvBundle\Util\Reader;

/*
 * CSV Form Handler 
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CsvFormHandler
{
    protected $form;
    protected $request;
    protected $reader;

    public function __construct(Form $form, Request $request, Reader $reader)
    {
        $this->form = $form;
        $this->request = $request;  
        $this->reader = $reader;
    }

    public function process()
    {
        if ('POST' === $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $result = $this->reader->parse($this->form['file']->getData(), $this->form['delimiter']->getData());

                return $result;
            } 
        }

        return false;
    }
}
