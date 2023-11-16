<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReferralReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportingController extends Controller
{
    /**
     * Show Ranking berdasarkan divisi user yg sedang login.
     */
    public function rankPerDivision()
    {
        $user = auth()->user();
        $user_id = $user->id;
        $ranking = DB::table(DB::raw('
            (SELECT issuer_id, issuer_rating as rating
            FROM referrals
            WHERE issuer_id IN (
                SELECT ud.user_id
                FROM user_details ud
                WHERE ud.division_id = (
                    SELECT d.id
                    FROM user_details ud
                    JOIN divisions d ON ud.division_id = d.id
                    WHERE ud.user_id = ?
                )
            )
            UNION ALL
            SELECT refer_id, refer_rating as rating
            FROM referrals
            WHERE refer_id IN (
                SELECT ud.user_id
                FROM user_details ud
                WHERE ud.division_id = (
                    SELECT d.id
                    FROM user_details ud
                    JOIN divisions d ON ud.division_id = d.id
                    WHERE ud.user_id = ?
                )
            )) AS subquery'))
            ->setBindings([$user_id, $user_id])
            ->join('user_details as ud2', 'subquery.issuer_id', '=', 'ud2.user_id')
            ->groupBy('ud2.name')
            ->selectRaw('ROW_NUMBER() OVER(ORDER BY SUM(rating) desc) as no, ud2.name, SUM(subquery.rating) as total_rating')
            ->orderByDesc('total_rating')
            ->limit(3)
            ->get();

        $response = ['ranking' => $ranking];
        return response($response, 200);
    }

    public function referralLeaderBoard()
    {
        $rankOrder = DB::table('user_details')
            ->join('users', 'user_details.id', '=', 'user_details.user_id')
            ->join('divisions', 'divisions.id', '=', 'user_details.division_id')
            ->select(DB::raw('ROW_NUMBER() OVER (ORDER BY point DESC, user_details.updated_at DESC, users.email_verified_at ASC) AS no, user_details.name,ROUND(user_details.point,2) AS point,divisions.name as division'))
            ->orderBy('point', 'desc')
            ->orderBy('user_details.updated_at', 'desc')
            ->orderBy('users.email_verified_at', 'asc')
            ->limit(3)
            ->get();

        $response = ['ranking' => $rankOrder];
        return response($response, 200);
    }

    public function referralReport()
    {

        $user = auth()->user();
        $hak_akses = $user->hak_akses;
        if ($hak_akses === 3) {
            return Excel::download(new ReferralReportExport(0, "All Marketing"), 'report.xlsx');
        }
        $userName = DB::table('user_details as ud')
            ->where('ud.user_id','=', $user->id)
            ->select('ud.name')
            ->first();
        return Excel::download(new ReferralReportExport($user->id, $userName->name), 'report.xlsx');
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
}
