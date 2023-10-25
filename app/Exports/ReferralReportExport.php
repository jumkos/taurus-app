<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReferralReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithDefaultStyles, WithCustomStartCell, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    protected $user_id;
    protected $user_name;
    protected $row;

    public function __construct(int $user_id, String $user_name)
    {
        $this->user_id = $user_id;
        $this->user_name = $user_name;
    }

    public function registerEvents(): array {

        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', "Referral Report ".$this->user_name);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:G1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            },
        ];
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function headings(): array
    {
        return ["Referral Code", "Customer Name", "Product Category", "Status", "Approved Limit", "Poin", "Marketing Name"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true], 'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'd6d2d2'],

            ],],

            2    => ['font' => ['bold' => true], 'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'd6d2d2'],

            ],],

            // // Styling a specific cell by coordinate.
            'A1:G'.$this->row => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
    }

    public function collection()
    {
        $report = DB::table('referrals as r')
                        ->join(DB::raw('(
                        SELECT REFERRAL_ID, MAX(`date`) AS MAX_STATUS_DATE
                        FROM referral_statuses
                        GROUP BY REFERRAL_ID
                    ) latest_status'), 'r.id', '=', 'latest_status.REFERRAL_ID')
                        ->join('referral_statuses as rs', function ($join) {
                            $join->on('r.id', '=', 'rs.REFERRAL_ID')
                                ->on('latest_status.MAX_STATUS_DATE', '=', 'rs.date');
                        })
                        ->join('status_parameters as sp', 'rs.STATUS_ID', '=', 'sp.ID')
                        ->join('product_categories as pc','pc.id', '=', 'r.product_category_id')
                        ->join('user_details as ud','ud.id','=','r.refer_id')
                        ->where('r.issuer_id','=', $this->user_id)
                        ->where('rs.STATUS_ID','>',4)
                        ->select('r.id as id', 'r.cust_name','pc.name as product_category','sp.name as status', 'r.approved_nominal as approved_limit', 'ud.name as marketing_name')
                        ->get();
        $this->row = 2;
        foreach ($report as &$rep) {
            $id = $rep->id;
            $rep->id = $this->generateRandomAlphanumericString(5,  $id);
            $tempName = $rep->marketing_name;
            $rep->marketing_name = $rep->approved_limit / 10000000;
            $rep->point = $tempName;
            $this->row = $this->row + 1;
        }
        return $report;
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
