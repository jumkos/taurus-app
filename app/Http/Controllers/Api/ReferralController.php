<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ReferralCreated;
use App\Models\Document;
use App\Models\NewRefMailData;
use App\Models\Referral;
use App\Models\ReferralStatus;
use App\Models\StatusParameter;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;

class ReferralController extends Controller
{

    public function createReferral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'issuer_id' => 'required|integer',
            // 'refer_id' => 'required|integer',
            'cust_name' => 'required|string|max:250',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:1000',
            'offering_date' => 'required|date',
            'product_type_id' => 'required|integer',
            'product_category_id' => 'required|integer',
            'product_id' => 'required|integer',
            'product_detail' => 'required|string|max:250',
            'nominal' => 'required|numeric',
            'info' => 'required|string|max:1000',
            'files' => 'required',
            'files.*' => 'required|max:2048',
            'relation' => 'required|string|max:1000',
            'referantor' => 'required|string|max:1000',
            'contact_person' => 'required|string|max:1000',
            'refer_to_division' => 'required|integer',
            'refer_to_region' => 'required|integer',
            'refer_to_city' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        //save referral
        $newReferral = array(
            "issuer_id" => $request['issuer_id'],
            // "refer_id" => $request['refer_id'],
            "cust_name" => $request['cust_name'],
            "phone" => $request['phone'],
            "address" => $request['address'],
            "offering_date" => $request['offering_date'],
            "product_type_id" => $request['product_type_id'],
            "product_category_id" => $request['product_category_id'],
            "product_id" => $request['product_id'],
            "nominal" => $request['nominal'],
            "info" => $request['info'],
            "product_detail" => $request['product_detail'],
            "relation" => $request['relation'],
            "referantor" => $request['referantor'],
            "contact_person" => $request['contact_person'],
            "refer_to_division" => $request['refer_to_division'],
            "refer_to_region" => $request['refer_to_region'],
            "refer_to_city" => $request['refer_to_city'],
        );
        $referral = Referral::create($newReferral);

        //save document
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                //get filename with extension
                $filenamewithextension = $file->getClientOriginalName();

                //get filename without extension
                $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

                //get file extension
                $extension = $file->getClientOriginalExtension();

                //filename to store
                $filenametostore = $filename . '_' . uniqid() . '.' . $extension;

                //Upload File to external server
                Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));

                //Store $filenametostore in the database
                $newDocument = array(
                    "referral_id" => $referral->id,
                    "name" => $filenametostore,
                );
                Document::create($newDocument);
            }
        }

        //save referral status
        $newReferralStatus = array(
            "referral_id" => $referral->id,
            "date" => date('Y-m-d H:i:s'),
            "status_id" => 1,
            "detail" => 'Published',
        );
        ReferralStatus::create($newReferralStatus);

        //TODO: notif email ke refer
        // $userIssuer = DB::table('users as u')
        //     ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
        //     ->select('u.email', 'ud.name')
        //     ->where('u.id', $request['issuer_id'])
        //     ->first();
        // $userRefer = DB::table('users as u')
        //     ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
        //     ->select('u.email', 'ud.name')
        //     ->where('u.id', $request['refer_id'])
        //     ->first();
        // $mailData = (object) (array(
        //     "addressFrom" => $userIssuer->email,
        //     "nameFrom" => $userIssuer->name,
        //     "nameTo" => $userRefer->name,
        //     "customerName" => $request['cust_name'],
        //     "customerPhone" => $request['phone'],
        // ));
        // Mail::to($userRefer->email)->send(new ReferralCreated($mailData));
        $response = ['message' => 'New Refferal has ben published'];
        return response($response, 200);
    }

    public function updateReferralStatus(Request $request)
    {

        $finalSts = [5, 6, 7];
        $validator = Validator::make($request->all(), [
            'referral_id' => 'required|integer',
            'status' => 'required|integer',
            'detail' => 'required|string|max:255',

        ]);
        $validator->sometimes('rating', 'required|integer|min:1|max:5', function (Fluent $input) {
            $finalSts = [5, 6, 7];
            return in_array($input->status, $finalSts);
        });
        $validator->sometimes('comment', 'required|string|max:255', function (Fluent $input) {
            $finalSts = [5, 6, 7];
            return in_array($input->status, $finalSts);
        });

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $response = ['message' => 'Rating has ben updated'];
        $user = auth()->user();
        $referrals = Referral::where('id', $request['referral_id'])->first();

        if($user->id!=$referrals->issuer_id){
            //save referral status
            $newReferralStatus = array(
                "referral_id" => $request['referral_id'],
                "date" => date('Y-m-d H:i:s'),
                "status_id" => $request['status'],
                "detail" => $request['detail'],
            );
            ReferralStatus::create($newReferralStatus);
            $response = ['message' => 'Refferal status has ben updated'];
        }

        //saving rating and comment
        //TODO: save rating & rating_by(jumlah yg merating) ke user_detail
        if(in_array($request['status'], $finalSts)){
            switch ($user->id) {
                case $referrals->issuer_id:
                    $referrals->refer_rating = $request['rating'];
                    $referrals->refer_comment = $request['comment'];
                    $referrals->save();
                    break;

                default:
                    $referrals->issuer_rating = $request['rating'];
                    $referrals->issuer_comment = $request['comment'];
                    $referrals->save();
                    if($request['status']==7){
                        DB::table('user_details')
                                ->where('user_id', $user->id)
                                ->increment('point', $referrals->nominal/10000000);
                    }
                    break;
            }
        }
        //save rating & rating_by ke user_detail
        DB::table('user_details')
                        ->where('user_id', $user->id)
                        ->increment('rating', $request['rating']);
        DB::table('user_details')
                        ->where('user_id', $user->id)
                        ->increment('rating_by', 1);


        return response($response, 200);
    }

    public function getListProductType()
    {
        $productTypes = DB::table('product_types')
            ->select('id', 'name')
            ->get();
        $response = ['product_types' => $productTypes];
        return response($response, 200);
    }

    public function getListProductCategory($productTypeId)
    {
        $productCategories = DB::table('product_categories')
            ->select('id', 'name')
            ->where('product_types_id', $productTypeId)
            ->get();
        if ($productCategories->isEmpty()) {
            return response('Not Found', 404);
        }
        $response = ['product_categories' => $productCategories];
        return response($response, 200);
    }

    public function getListStatusReferral($currentStatusId)
    {
        $statusReferral = DB::table('status_parameters')
            ->select('id', 'name')
            ->where('parrent_id', $currentStatusId)
            ->get();

        if ($statusReferral->isEmpty()) {
            return response('Not Found', 404);
        }
        $response = ['status' => $statusReferral];
        return response($response, 200);
    }

    public function getListDivision()
    {
        $divisions = DB::table('divisions')
            ->select('id', 'name')
            ->get();
        $response = ['divisions' => $divisions];
        return response($response, 200);
    }

    public function getListRegion()
    {
        $regions = DB::table('regions')
            ->select('id', 'name')
            ->get();
        $response = ['regions' => $regions];
        return response($response, 200);
    }

    public function getListBranch($regionId)
    {
        $branches = DB::table('branches')
            ->select('id', 'name')
            ->where('regions_id', $regionId)
            ->get();
        $response = ['branches' => $branches];
        return response($response, 200);
    }

    public function getListCity($regionId)
    {
        $branches = DB::table('cities')
            ->select('id', 'name')
            ->where('regions_id', $regionId)
            ->get();
        $response = ['cities' => $branches];
        return response($response, 200);
    }

    public function getListToReferName($divisionId, $regionId, $branchLocationId)
    {
        $referNames = DB::table('user_details')
            ->select('user_id', 'name')
            ->where('division_id', $divisionId)
            ->where('region_id', $regionId)
            ->where('branch_location_id', $branchLocationId)
            ->get();
        $response = ['referNames' => $referNames];
        return response($response, 200);
    }

    public function getMyListReferral()
    {
        $user = auth()->user();

        $myListReferal = DB::table('referrals as r')
            ->join(DB::raw('(
            SELECT REFERRAL_ID, MAX(`date`) AS MAX_STATUS_DATE
            FROM referral_statuses
            GROUP BY REFERRAL_ID
        ) latest_status'), 'r.id', '=', 'latest_status.REFERRAL_ID')
            ->join('referral_statuses as rs', function ($join) {
                $join->on('r.id', '=', 'rs.REFERRAL_ID')
                    ->on('latest_status.MAX_STATUS_DATE', '=', 'rs.date');
            })
            ->join('status_parameters as s', 'rs.STATUS_ID', '=', 's.ID')
            ->join('user_details as ud', 'r.refer_id', '=', 'ud.user_id')
            ->select('r.id', 'r.cust_name', 'r.created_at', 'ud.name as assigned_to', 's.name as status')
            ->where('r.issuer_id', $user->id)
            ->get();
        foreach ($myListReferal as &$ref) {
            $id = $ref->id;
            $ref->uniq_no = $this->generateRandomAlphanumericString(5,  $id);
        }
        $response = ['myListReferal' => $myListReferal];
        return response($response, 200);
    }

    function generateRandomAlphanumericString($length, $seed) {
        mt_srand($seed); // Seed the random number generator

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }

    public function getTrackingDetail($referralId)
    {
        $tracking = DB::table('referrals as r')
            ->join('user_details as ud', 'r.refer_id', '=', 'ud.user_id')
            ->join('divisions as d', 'd.id', '=', 'ud.division_id')
            ->select('r.cust_name', 'r.phone', 'r.address', 'ud.name as refer_name', 'd.name as refer_division')
            ->where('r.id', $referralId)
            ->first();

        $dt = DB::table('referral_statuses as rs')
                    ->join('status_parameters as s', 'rs.status_id', '=', 's.id')
                    ->join('referrals as r', 'rs.referral_id', '=', 'r.id')
                    ->select('rs.date', 'rs.detail as remark', 's.name as status')
                    ->where('r.id', $referralId)
                    ->orderBy('rs.date', 'asc')
                    ->get();

        $tracking -> detail = $dt;
        $response = ['tracking' => $tracking];
        return response($response, 200);
    }

    public function getMyRequestReferral()
    {
        $user = auth()->user();

        $myRequestListReferal = DB::table('referrals as r')
            ->join(DB::raw('(
            SELECT REFERRAL_ID, MAX(`date`) AS MAX_STATUS_DATE
            FROM referral_statuses
            GROUP BY REFERRAL_ID
        ) latest_status'), 'r.id', '=', 'latest_status.REFERRAL_ID')
            ->join('referral_statuses as rs', function ($join) {
                $join->on('r.id', '=', 'rs.REFERRAL_ID')
                    ->on('latest_status.MAX_STATUS_DATE', '=', 'rs.date');
            })
            ->join('status_parameters as s', 'rs.STATUS_ID', '=', 's.ID')
            ->join('user_details as ud', 'r.refer_id', '=', 'ud.user_id')
            ->select('r.id', 'r.cust_name', 'r.created_at', 'ud.name as assigned_to', 's.name as status')
            ->where('r.refer_id', $user->id)
            ->get();
        $response = ['myRequestListReferal' => $myRequestListReferal];
        return response($response, 200);
    }

    public function getReferalDetail($referralId)
    {
        $referalDetail = DB::table('referrals as r')
            ->join(DB::raw('(
            SELECT REFERRAL_ID, MAX(`date`) AS MAX_STATUS_DATE
            FROM referral_statuses
            GROUP BY REFERRAL_ID
        ) latest_status'), 'r.id', '=', 'latest_status.REFERRAL_ID')
            ->join('referral_statuses as rs', function ($join) {
                $join->on('r.id', '=', 'rs.REFERRAL_ID')
                    ->on('latest_status.MAX_STATUS_DATE', '=', 'rs.date');
            })
            ->join('status_parameters as s', 'rs.STATUS_ID', '=', 's.ID')
            ->join('user_details as ud', 'r.refer_id', '=', 'ud.user_id')
            ->join('user_details as ud2', 'r.issuer_id', '=', 'ud2.user_id')
            ->join('users as u', 'r.issuer_id', '=', 'u.id')
            ->join('product_types as pt', 'r.product_type_id', '=', 'pt.id')
            ->join('product_categories as pc', 'r.product_category_id', '=', 'pc.id')
            ->select('r.id', 'ud2.name as issuer_name', 'u.nip as issuer_nip', 'r.cust_name', 'r.phone', 'r.address', 'r.offering_date', 'pt.name as product_type', 'pc.name as product_category', 'r.product_detail as product','r.nominal', 'r.info')
            ->where('r.id', $referralId)
            ->first();
        $response = ['referalDetail' => $referalDetail];
        return response($response, 200);
    }

    public function getReferalDocuments($referralId)
    {
        $referalDocument = DB::table('documents')
            ->select('id', 'name as show_name', 'name as hiden_name')
            ->where('referral_id', $referralId)
            ->get();

        foreach ($referalDocument as &$doc) {
            $lastUd = strripos($doc->show_name, '_');
            $lastDt = strripos($doc->show_name, '.');
            $uniq = substr($doc->show_name, $lastUd, $lastDt-$lastUd);
            $doc->show_name = str_replace($uniq,'',$doc->show_name);
        }
        $response = ['referalDocument' => $referalDocument];
        return response($response, 200);
    }

    public function downloadDocuments($docName)
    {

        $lastUd = strripos($docName, '_');
        $lastDt = strripos($docName, '.');
        $uniq = substr($docName, $lastUd, $lastDt-$lastUd);
        $newDocname = str_replace($uniq,'',$docName);
        $filecontent = Storage::disk('ftp')->get($docName); // read file content
           // download file.
           return response($filecontent, '200', array(
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.$newDocname.'"'
            ));
    }

    public function getNewRequestCount()
    {

        $user = auth()->user();

        $newRequest = DB::table('referrals as r')
            ->join(DB::raw('(
                SELECT REFERRAL_ID, MAX(`date`) AS MAX_STATUS_DATE
                FROM referral_statuses
                GROUP BY REFERRAL_ID
            ) latest_status'), 'r.id', '=', 'latest_status.REFERRAL_ID')
            ->join('referral_statuses as rs', function ($join) {
                $join->on('r.id', '=', 'rs.REFERRAL_ID')
                    ->on('latest_status.MAX_STATUS_DATE', '=', 'rs.date');
            })
            ->select('*')
            ->where('r.refer_id', $user->id)
            ->where('rs.status_id', 1)
            ->count();
        $response = ['new_request' => $newRequest];
        return response($response, 200);
    }

    public function getForm($formtipe)
    {
        $form = DB::table('form_referrals')
            ->select('label', 'value', 'tipe', 'mandatory', 'min_lenght', 'max_lenght')
            ->where('form', $formtipe)
            ->get();
        $response = ['form' => $form];
        return response($response, 200);
    }

    public function getListOpenReferral()
    {
        $user = auth()->user();

        // $rating = DB::table('user_details')
        //                 ->join('users', 'users.id', '=', 'user_details.user_id')
        //                 ->select('user_details.rating','user_details.rating_by')
        //                 ->where('user_id', $user->id)
        //                 ->first();

        // $avgRating = $rating->rating / $rating->rating_by;

        $userDetail = DB::table('user_details')
                        ->where('user_id', $user->id)
                        ->select('division_id', 'region_id', 'city_id', 'rating_by')
                        ->first();

        $openListReferal = DB::table('referrals as r')
            ->join('regions as rg', 'r.refer_to_region', '=', 'rg.id')
            ->join('user_details as ud', 'r.issuer_id', '=', 'ud.user_id')
            ->select('r.id', 'r.cust_name', 'r.nominal', 'rg.name as province', 'ud.rating_by')
            ->select(DB::raw('r.id, r.cust_name, r.nominal, rg.name as province, ud.rating_by, (ud.rating/ud.rating_by) as issuer_rating'))
            ->whereNot('r.issuer_id', $user->id)
            ->whereNull('r.refer_id')
            ->where('r.refer_to_division', $userDetail->division_id)
            ->where('r.refer_to_region', $userDetail->region_id)
            ->where('r.refer_to_city', $userDetail->city_id)
            ->get();
        $response = ['openListReferal' => $openListReferal];
        return response($response, 200);
    }

    public function takeReferral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $response = ['message' => 'Referral successfully taken'];
        $user = auth()->user();
        $updatedReferrals = DB::table('referrals')
                    ->where('id', $request['referral_id'])
                    ->update(['refer_id' =>  $user->id]);

        if ($updatedReferrals==0) {
            $response = ['message' => 'Failed to take the Referral'];
        }

        return response($response, 200);
    }
}
