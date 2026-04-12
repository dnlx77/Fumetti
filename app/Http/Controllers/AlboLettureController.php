<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlboLetture;

class AlboLettureController extends Controller
{
    public function AggiungiLettura(Request $request, $id_albo)
    {
        $alboLettura = new AlboLetture();
        $alboLettura->albo_id = $id_albo;
        if ($request->get('data_lettura') != 0)
            $alboLettura->data_lettura = \DateTime::createFromFormat('d-m-Y', $request->get('data_lettura'));
        else
            $alboLettura->data_lettura = null;
        $alboLettura->save();
        return redirect(route('albo.details', $id_albo));
    }

    public function RimuoviLettura($id_albo, $data)
    {
        $alboLettura = AlboLetture::where('data_lettura', '=', $data)->where('albo_id', '=', $id_albo);
        $alboLettura->delete();
        return redirect(route('albo.details', $id_albo));
    }
}
