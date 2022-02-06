<?php

namespace App\Services\Tasks;

use App\Services\IntranetAPI;

class UpdateTeachers
{

    private IntranetAPI $intranetAPI;

    public function __construct(IntranetAPI $intranetAPI)
    {
        $this->intranetAPI = $intranetAPI;
    }

    public function update()
    {

        dd($this->intranetAPI->teachers());

    }

}