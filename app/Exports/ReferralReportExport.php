<?php

namespace App\Exports;

use App\Models\User;
use DateTimeImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReferralReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithDefaultStyles, WithCustomStartCell, WithEvents, WithColumnFormatting
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

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', "Referral Report ".$this->user_name." as of ".date('d M Y H:i:s'));

                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:I1'; // All headers
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
        return ["Referral Code", "Offering Date", "Customer Name", "Product Category", "Status", "Last Update","Approved Limit", "Poin", "Marketing Name"];
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
            'A1:I'.$this->row => [
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

    public function columnFormats(): array
    {
        return [
            'B' => "dd-mm-yyyy",
            'F' => "dd-mm-yyyy",
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
                        ->where(function (Builder $query) {
                            $query->where(function (Builder $query) {
                                $query->where('r.issuer_id','=', $this->user_id);
                                    //   ->where('rs.STATUS_ID','>',4);
                                    //   ->orWhere(0, '=', $this->user_id);
                            })
                                  ->orWhereRaw('0 = ?', $this->user_id);
                                //   ->orWhere(0, '=', $this->user_id);
                        })

                        // ->select('r.id as id', 'r.offering_date as offering_date', 'r.cust_name','pc.name as product_category','sp.name as status', 'rs.date as finish_date', 'r.approved_nominal as approved_limit', 'ud.name as marketing_name')
                        ->select(DB::raw('r.id as id, DATE_FORMAT(r.offering_date,"%Y-%m-%d") as offering_date, r.cust_name,pc.name as product_category,sp.name as status, DATE_FORMAT(rs.date,"%Y-%m-%d") as finish_date, r.approved_nominal as approved_limit, ud.name as marketing_name'))
                        ->get();
        $this->row = 2;
        foreach ($report as &$rep) {
            $id = $rep->id;
            $rep->id = $this->generateRandomAlphanumericString(5,  $id);
            $tempName = $rep->marketing_name;
            $rep->marketing_name = number_format($rep->approved_limit / 10000000);
            $rep->point = $tempName;
            $rep->approved_limit = number_format($rep->approved_limit);
            $this->row = $this->row + 1;
            $rep->offering_date = Date::dateTimeToExcel(new DateTimeImmutable($rep->offering_date));
            $rep->finish_date = Date::dateTimeToExcel(new DateTimeImmutable($rep->finish_date));
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
