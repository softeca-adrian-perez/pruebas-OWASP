<?php
class ApiEmail extends CakeEmail
{
    public function renderEmail($viewVars, $template) {
        $this->viewVars($viewVars);
        $this->template($template, 'email');
        $this->emailFormat('html');
        return $this->_render($this->_wrap(null));
    }
}
