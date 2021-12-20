<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {

    }

    public function registerAction()
    {
        $user = new Users();

        // assign values from the form
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email',
            ]
        );

        // store and check for errors
        $success = $user->save();

        // passing the result to the view
        $this->view->success = $success;

        if ($success) {
            $message = "Thanks for registering!";
        } else {
            $message = "Sorry, the following problems were generated:<br>" . implode("<br>", $user->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }
}