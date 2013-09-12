<?php

namespace Library;

class StackTrace
{

    private $_message;
    private $_stack;
    private $_limit;
    private $_formatted;
    private $_decorators = array(
        'base'   => '
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
<div class="container">
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><b>%s</b></h3>
</div>
<div class="panel-body">
<div class="panel-group" id="accordion-trace">
%s
</div>
</div>
</div>
</div>
<script src="//code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        ',
        'each'   => '
<div class="panel panel-default">
%s
</div>
        ',
        'header' => '
<div class="panel-heading">
    <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-trace" href="#collapse%s">
            %s
        </a>
    </h4>
</div>
        ',
        'body'   => '
<div id="collapse%s" class="panel-collapse collapse %s">
    <div class="panel-body">
%s
    </div>
</div>
        '
    );

    public function __construct($message)
    {
        $this->_message = $message;
        $this->_limit   = (Settings::getInstance()->debug_stacktrace_limit)
            ? Settings::getInstance()->debug_stack_limit
            : 10;
        $this->_stack   = debug_backtrace(
            DEBUG_BACKTRACE_PROVIDE_OBJECT,
            $this->_limit
        );
    }

    public function build()
    {
        if (Settings::getInstance()->debug_stacktrace_style == 'simple') {
            $this->_formatted = '<h3>'
                . $this->_message
                . '</h3><pre>'
                . print_r($this->_stack, true)
                . '</pre>';
        } else {
            foreach ($this->_stack as $id => $trace) {
                $stack[] = sprintf(
                    $this->_decorators['each'],
                    sprintf(
                        $this->_decorators['header'],
                        $id,
                        '<b>#' . $id . '</b> ' . $trace['file'] . ' (' . $trace['line'] . ')'
                    )
                    . sprintf(
                        $this->_decorators['body'],
                        $id,
                        ($id == 2) ? 'in' : '',
                        $trace['class']
                        . $trace['type']
                        . $trace['function']
                        . '(<br>'
                        . implode(',<br>', $trace['args'])
                        . '<br>)'
                    )
                );
            }
            $this->_formatted = sprintf(
                $this->_decorators['base'],
                $this->_message,
                implode(PHP_EOL, $stack)
            );
        }
        return $this;
    }

    public function show()
    {
        if (Settings::getInstance()->debug_stacktrace == 'off') {
            return;
        }
        echo $this->_formatted;
        die();
    }

}