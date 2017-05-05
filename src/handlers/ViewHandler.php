<?php
namespace handler\view;

use handler\IHander;
use http\HttpContext;

/**
 * Handler for views
 *
 * @author elger
 */
class ViewHandler implements IHander
{

    /**
     * (non-PHPdoc)
     *
     * @see \handler\IHander::handle()
     */
    public function handle($object)
    {
        // render the page
        $responce = HttpContext::get()->getResponse();
        $responce->setStatusCode(200);
        $responce->setContentType('text/html');
        $responce->write(is_string($object) ? $object : $object->render());
        $responce->flush();
        
        return $object;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \handler\IHander::accept()
     */
    public function accept($object)
    {
        return is_string($object) || (is_object($object) && $object instanceof \view\IView);
    }
}