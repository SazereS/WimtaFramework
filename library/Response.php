<?php

namespace Library;

class Response
{

    const FORMAT_HTML = 0;
    const FORMAT_JSON = 1;
    const FORMAT_XML  = 2;

    private $_format = self::FORMAT_HTML;
    private $_content;

    /**
     * @var View\Layout
     */
    private $_layout;

    public function __construct()
    {
        $this->_layout = new View\Layout();
    }

    public function setFormat($format)
    {
        if(
            in_array(
                $format,
                array(
                    self::FORMAT_HTML,
                    self::FORMAT_JSON,
                    self::FORMAT_XML,
                )
            )
        ){
            $this->_format = $format;
        }
        return $this;
    }

    public function writeContent()
    {
        echo $this->_content;
        return $this;
    }

    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getLayout()
    {
        return $this->_layout;
    }

    public function renderLayout(View $view)
    {
        if ($this->_format == self::FORMAT_JSON) {
            $this->setContent(
                json_encode(
                    \Helpers\ObjectsToArray::objectsToArray($view->getOut()),
                                                            (Settings::getInstance()->getMode() == 'development')
                            ? JSON_PRETTY_PRINT : 0
                )
            );
            header('Content-Type: application/json');
        } elseif ($this->_format == self::FORMAT_XML) {
            $this->setContent(
                \Helpers\XmlEncode::xmlEncode(
                    \Helpers\ObjectsToArray::objectsToArray($view->getOut()),
                    false
                )
            );
            header('Content-Type: application/xml');
        } else {
            $this->setContent(
                $this->_layout->render(
                    ($layout = Settings::getInstance()->default_layout)
                    ? $layout
                    : 'default',
                    array_merge(
                        $view->getOut(), array('content' => $view->rendered)
                    )
                )->rendered
            );
        }
        return $this;
    }

}