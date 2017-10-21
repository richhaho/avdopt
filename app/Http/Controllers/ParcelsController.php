<?php

namespace App\Http\Controllers;

use App\TerminalApiParcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ParcelsController extends Controller
{
    public function index()
    {
        $parcels= TerminalApiParcel::get();
        return view('parcel',compact('parcels'));
    }

    public function getParcels()
    {
        $response = array();
        $response['success']=false;

        $parcels= TerminalApiParcel::get();

        $parcel_arr=array();

        if(count($parcels)>0)
        {

        foreach($parcels  as $parcel )
        {
                $parcel_arr[] = array(
                    'parcel_name'=>$parcel->parcel_name,
                    'url'=>$parcel->url
                );
            }


        }


        $response['success']=true;
        $response['info']=array('parcels'=>$parcels);

        $response['msg']='';

        return Response::json($response, 200); //422
    }
}
