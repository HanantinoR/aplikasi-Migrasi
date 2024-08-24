<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TahapController extends Controller
{
    public function index1()
    {
        return view('tahap1.index');
    }

    public function detail1()
    {
        return view('tahap1.detail');
    }

    public function index2()
    {
        return view('tahap2.index');
    }

    public function detail2()
    {
        return view('tahap2.detail');
    }

    public function index3()
    {
        return view('tahap3.index');
    }

    public function detail3()
    {
        return view('tahap3.detail');
    }

    public function index4()
    {
        return view('tahap4.index');
    }

    public function detail4()
    {
        return view('tahap4.detail');
    }
}
