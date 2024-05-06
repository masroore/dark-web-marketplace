<?php

class chart extends Controller
{
    public function __construct()
    {
        parent::__construct(false, true, false, true);
    }

    public function __call($filename, $arguments)
    {
        if (!$this->User->IsVendor) {
            exit;
        }

        $accountModel = $this->loadModel('Account');

        return $accountModel->renderUserQueryGraph($filename);
    }
}
