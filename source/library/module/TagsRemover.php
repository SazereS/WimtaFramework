<?php

namespace Library\Module;

class TagsRemover extends \Library\Module
{

    public function __construct()
    {
        parent::__construct();
        \Library\Base::registerHelper(
            'removeTags',
            function($text, $tags = array()){
                return \Library\Base::getModule('tagsremover')->removeTags($text, $tags);
            }
        );
    }

    /**
     * @param string $text
     * @param array $tags White list for tags and attributes
     * @return string
     */
    public function removeTags($text, $tags = array()){
        $matches = array();
        $list = array();
        foreach ($tags as $tag => $attributes) {
            if(is_array($attributes)){
                $list[] = $tag;
            } else {
                $list[] = $attributes;
            }
        }
        $regexp = '/\<\/?([a-zA-Z0-9]+)[^\>]*\>/ims';
        preg_match_all($regexp, $text, $matches, PREG_SET_ORDER);
        foreach($matches as $match){
            if(!in_array($match[1], $list)){
                $text = str_replace($match[0], '', $text);
                continue;
            }
            if(is_array($attrs = $tags[$match[1]])){
                $tag_text = $match[0];
                $temp = array();
                preg_match_all('/(\w+) *= *(("[^"]*")|(\'[^\']*\'))/ims', $match[0], $temp, PREG_SET_ORDER);
                foreach ($temp as $attr) {
                    if(!in_array($attr[1], $attrs)){
                        $tag_text = str_replace($attr[0], '', $tag_text);
                    }
                }
                $text = str_replace(
                    $match[0],
                    $tag_text,
                    $text
                );
            } else {
                $temp = array();
                preg_match('/^\<\/?\w*(.*)\>$/ims', $match[0], $temp);
                $text = str_replace(
                    $match[0],
                    str_replace($temp[1], '', $match[0]),
                    $text
                );
            }
        }
        return $text;
    }

}