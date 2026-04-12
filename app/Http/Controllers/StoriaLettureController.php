<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StoriaLetture;

class StoriaLettureController extends Controller
{
    public function AggiungiLettura(Request $request, $id_storia)
    {
        $storiaLettura = new StoriaLetture();
        $storiaLettura->storia_id = $id_storia;
        if ($request->get('data_lettura') != 0)
            $storiaLettura->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $storiaLettura->data_lettura = null;
        $storiaLettura->save();
        return redirect(route('storia.details', $id_storia));
    }

    public function RimuoviLettura($id_storia, $data)
    {
        $storiaLettura = StoriaLetture::where('data_lettura', '=', $data)->where('storia_id', '=', $id_storia);
        $storiaLettura->delete();
        return redirect(route('storia.details', $id_storia));
    }
}
