<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Referral;
use App\Models\ReferralStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{

    public function createReferral (Request $request) {
        $validator = Validator::make($request->all(), [
            'issuer_id' => 'required|integer',
            'refer_id' => 'required|integer',
            'cust_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'offering_date' => 'required|date',
            'product_type_id' => 'required|integer',
            'product_category_id' => 'required|integer',
            'product_id' => 'required|integer',
            'nominal' => 'required|decimal:2',
            'info' => 'required|string',
            'files' => 'required',
            'files.*' => 'required|max:2048',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        //save referral
        $newReferral = array(
            "issuer_id" => $request['issuer_id'],
            "refer_id" => $request['refer_id'],
            "cust_name" => $request['cust_name'],
            "phone" => $request['phone'],
            "address" => $request['address'],
            "offering_date" => $request['offering_date'],
            "product_type_id" => $request['product_type_id'],
            "product_category_id" => $request['product_category_id'],
            "product_id" => $request['product_id'],
            "nominal" => $request['nominal'],
            "info" => $request['info'],
        );
        $referral = Referral::create($newReferral);

        //save document
        $response = ['message' => 'New Refferal has ben sent'];
        if ($request->hasFile('files')) {
            foreach($request->file('files') as $file)
            {
                $response = ['anu2' => 'anu2'];
                //get filename with extension
                $filenamewithextension = $file->getClientOriginalName();

                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

                //get file extension
                $extension = $file->getClientOriginalExtension();

                //filename to store
                $filenametostore = $filename.'_'.uniqid().'.'.$extension;

                //Upload File to external server
                Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));

                //Store $filenametostore in the database
                $newDocument = array(
                    "referral_id" => $referral -> id,
                    "name" => $filenametostore,
                );
                Document::create($newDocument);
            }
        }

        //save referral status
        $newReferralStatus = array(
            "referral_id" => $referral -> id,
            "date" => date('Y-m-d H:i:s'),
            "status_id" => 1,
            "detail" => 'Approval request sent',
        );
        ReferralStatus::create($newReferralStatus);

        // $response = ['message' => 'New Refferal has ben sent'];
        return response($response, 200);
    }
}
