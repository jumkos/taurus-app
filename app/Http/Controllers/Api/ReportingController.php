<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
