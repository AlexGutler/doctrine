<?php

namespace AG\ErrorList;


class ErrorList
{
    private $items = array();
    private $list;

    function __construct($items = array())
    {
        $this->items = $items;
    }

    function render()
    {
        $this->list .= '<div class="col-sm-8">';
        $this->list .= '<p class="alert-danger"><strong>Erros encontados:</strong></p>';
        $this->list .= '<ul style="list-style-type:disc">';

        foreach ($this->items as $item)
        {
            $this->list .= '<li>'.$item.'</li>';
        }

        $this->list .= '</ul>';

        $this->list .= '</div>';
        return $this->list;
    }
}