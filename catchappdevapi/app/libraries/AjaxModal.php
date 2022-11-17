<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 17/6/19
 * Time: 10:11 AM
 */

namespace App\Libraries;

use Illuminate\Support\Facades\View;
class AjaxModal extends Ajax
{
    public function __construct()
    {
        $this->js_callback('general_form_modal');
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @return AjaxModal
     */
    public function view($view, $data = [])
    {
        $data['modal_id'] = time() . 'A' . rand(1000, 9999);
        $this->param('modal_id', $data['modal_id']);
        $view = View::make($view, $data);
        $contents = $view->render();
        $this->param('html', $contents);
        return $this;
    }

    public function closeOtherModals($close = true)
    {
        $this->param('close_other_modals', $close);
    }
}