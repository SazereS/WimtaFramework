<?php

namespace Library\Module\BootstrapForm\Element;

class Checkbox extends \Library\Module\BootstrapForm\Element
{

    protected $_template      = '<input %1$s value="%2$s" type="checkbox" />';
    protected $_value_protect = '"';
    protected $_classes       = '';

    public $custom_holder =
'<div class="form-group%5$s">
    <div class="col-md-offset-%6$s col-md-%7$s">
        <div class="checkbox">
            <label>%1$s %4$s</label>
            %2$s
        </div>
    </div>
</div>';

}
