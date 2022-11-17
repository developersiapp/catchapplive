<?php
/**
 * Created by PhpStorm.
 * User: tarin
 * Date: 17-09-2018
 * Time: 03:09 PM
 */

namespace App\Helpers;


use Illuminate\Support\Facades\View;

class PaginationHelper
{
    private $totalRecords;
    private $perPage;
    private $offset;

    public function __construct($totalRecords = 0, $perPage = 10, $currentRecord = 0)
    {
        $this->totalRecords = $totalRecords;
        $this->perPage = $perPage;
        $this->offset = $currentRecord;
    }

    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function links()
    {
        $view = View::make('backend.helpers.pagination', [
            'totalRecords' => $this->totalRecords,
            'perPage' => $this->perPage,
            'offset' => $this->offset
        ]);
        return $view->render();
    }
}