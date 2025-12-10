<?php
class RedirectController extends AppController
{
    private $allowedRedirects = array(
        '/enquiries',               // ENQUIRIES
        '/edit_enquiry',
        '/reporting',               // REPORTING
        '/booking_status_edit',
        '/bookings',
        '/home',
        '/appointments',            // APPOINTMENT
        '/edit',
        '/edit_event',
        '/tasks',                   // TASK
        '/edit',
        SERVER_NAME
    );

    /**
     *  Redirect emails (problem cookie).
     */
    public function email()
    {
        if (isset($this->request->query['url']) && !empty($this->request->query['url'])) {
            $requestedUrl = $this->request->query['url'];

            if ($this->checkSafeUrl($requestedUrl)) {
                $this->layout = null;
                $this->set(array(
                    'url' => $requestedUrl
                ));
            }
        }

        $this->redirect(array(
            'controller' => 'users',
            'action' => 'login'
        ));
    }

    /**
     * Redirect SMS.
     */
    public function sms()
    {
        if (isset($this->request->query['url']) && !empty($this->request->query['url'])) {
            $requestedUrl = $this->request->query['url'];

            if ($this->checkSafeUrl($requestedUrl)) {
                $this->layout = null;
                $this->set(array(
                    'url' => $requestedUrl
                ));
            }
        }

        $this->redirect(array(
            'controller' => 'users',
            'action' => 'login'
        ));
    }

    /**
     * Check if the requested URL is safe.
     */
    private function checkSafeUrl($url)
    {
        $safe = false;

        foreach ($this->allowedRedirects as $allowed) {
            if (strpos($url, $allowed) === 0) {
                $safe = true;
                break;
            }
        }

        return $safe;
    }
}
