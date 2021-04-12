<?php

namespace App\Http\Controllers;

use App\Models\BankList;

class BankListController extends BaseController
{
    public function index()
    {
        $banks = BankList::all();
        return $this->successfulResponse(200, $banks);
    }
}
