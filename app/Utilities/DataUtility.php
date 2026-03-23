<?php

namespace App\Utilities;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataUtility
{
    /**
     * Generate a DataTable response for any query with pagination.
     *
     * @param Builder $query
     * @param array $columns
     * @param int $limit
     * @param int $offset
     * @return \Illuminate\Http\JsonResponse
     */
    public static function generate($query, array $columns = [], $limit = 10, $offset = 0)
    {

        if (!$query) {
            return response()->json(['error' => 'Query cannot be null'], 400);
        }

        return DataTables::of($query->limit($limit)->offset($offset))
            ->addIndexColumn() // Adds serial number
            ->editColumn('created_at', fn($row) => $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : 'N/A')
            /* ->addColumn('actions', fn($row) => '<a href="' . route('edit.route', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                                                 <a href="' . route('delete.route', $row->id) . '" class="btn btn-sm btn-danger">Delete</a>')*/
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Export all data to Excel.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param string $fileName
     * @param array|null $headings
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public static function exportToExcel($query, $fileName = 'export.xlsx', $headings = null, $mainHeading = null)
    {
        $data = $query->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        if (!$headings) {
            $headings = array_map(function ($key) {
                return ucwords(str_replace('_', ' ', $key));
            }, array_keys((array) $data->first()));
        }

        return Excel::download(
            new class ($data, $headings, $mainHeading) implements
            FromCollection,
            WithHeadings,
            WithStyles,
            ShouldAutoSize,
            WithEvents {

            protected $data;
            protected $headings;
            protected $mainHeading;

            public function __construct($data, $headings, $mainHeading)
            {
                $this->data = $data;
                $this->headings = $headings;
                $this->mainHeading = $mainHeading;
            }

            public function collection()
            {
                // Prepend empty row if mainHeading exists
                return collect($this->data);
            }

            public function headings(): array
            {
                // Return headings only; main heading is handled in events
                return $this->headings;
            }

            public function styles(Worksheet $sheet)
            {
                return [
                1 => ['font' => ['bold' => true]], // Heading row bold
                ];
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        if ($this->mainHeading) {
                            $sheet = $event->sheet;
                            $rowCount = $this->data->count();
                            $columnCount = count($this->headings);

                            //  Make sure this is defined before it's used
                            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);

                            // Insert row at top and merge for main heading
                            $sheet->insertNewRowBefore(1, 2);
                            $sheet->mergeCells("A1:{$lastColumn}1");
                            $sheet->setCellValue("A1", $this->mainHeading);
                            $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
                            $sheet->getStyle("A1")->getAlignment()->setHorizontal('center');

                            //  Border apply from heading to last row
                            $borderRange = "A3:{$lastColumn}" . (2 + $rowCount); // A3 to last data row
                            $sheet->getStyle($borderRange)->applyFromArray([
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['argb' => '000000'],
                                    ],
                                ],
                            ]);
                        }
                    }

                ];
            }

            },
            $fileName
        );
    }

  

public static function exportToExcelComplex($query, $fileName = 'export.xlsx', $headingStructure = null)
{
    if ($query instanceof \Illuminate\Support\Collection) {
        $data = $query->values();
    } elseif (is_array($query)) {
        $data = collect($query);
    } elseif (
        $query instanceof \Illuminate\Database\Eloquent\Builder
        || $query instanceof \Illuminate\Database\Query\Builder
        || $query instanceof \Illuminate\Database\Eloquent\Relations\Relation
    ) {
        $data = $query->get();
    } elseif ($query instanceof \Traversable) {
        $data = collect(iterator_to_array($query, false));
    } else {
        return response()->json(['message' => 'Invalid data source provided'], 400);
    }

    if ($data->isEmpty()) {
        return response()->json(['message' => 'No data found'], 404);
    }

    return Excel::download(
        new class ($data, $headingStructure) implements
        FromCollection,
        WithStyles,
        ShouldAutoSize,
        WithEvents {

        protected $data;
        protected $headingStructure;

        public function __construct($data, $headingStructure)
        {
            $this->data = $data;
            $this->headingStructure = $headingStructure;
        }

        public function collection()
        {
            // Map database fields to Excel columns
            return $this->data->map(function ($item, $index) {
                // Convert object to array if needed
                $itemArray = is_object($item) ? get_object_vars($item) : $item;
                
                return [
                    $index + 1, // सरल क्रमांक
                    $itemArray['panchayat_nagar_no'] ?? '',
                    $itemArray['panjiyan_no'] ?? '',
                    $itemArray['nagar_panchayat_name'] ?? '',
                    $itemArray['kendra_name'] ?? '',
                    $itemArray['postname'] ?? '',
                    $itemArray['total_application'] ?? '',
                    $itemArray['Full_Name_hindi'] ?? '',
                    $itemArray['FatherName'] ?? '',
                    $itemArray['niwas'] ?? '',
                    $itemArray['Caste'] ?? '',
                    $itemArray['Age'] ?? '',
                    $itemArray['qualification'] ?? '',
                    $itemArray['total_marks'] ?? '',
                    $itemArray['obtained_marks'] ?? '',
                    $itemArray['percentage'] ?? '',
                    $itemArray['sixty_percent_marks'] ?? '',
                    
                    // विधवा / परित्यक्ता
                    $itemArray['vivdhwa_talakshuda_detail'] ?? '', // विवरण
                    $itemArray['vidhwa_talakshuda_marks'] ?? 0, // अंक
                    
                    // अनु. जाति/जन जाति होने पर
                    $itemArray['caste_answer'] ?? '', // विवरण
                    $itemArray['caste_marks'] ?? 0, // अंक
                    
                    // गरीबी रेखा परिवार से
                    $itemArray['bpl_answer'] ?? '', // विवरण
                    $itemArray['bpl_marks'] ?? 0, // अंक
                    
                    // कार्यकर्ता /सहायिका होने पर
                    $itemArray['karyakarta_sahayika_detail'] ?? '', // विवरण
                    $itemArray['karyakarta_sahayika_marks'] ?? 0, // अंक
                    
                    // अनुभव
                    $itemArray['experience_details'] ?? '', // विवरण
                    $itemArray['experience_marks'] ?? 0, // अंक
                    
                    // Other columns
                    $itemArray['total_counted_marks'] ?? 0,
                    $itemArray['yogyata_kram'] ?? 0,
                    $itemArray['qualified_reason'] ?? '',
                ];
            });
        }

        public function styles(Worksheet $sheet)
        {
            return [
                1 => ['font' => ['bold' => true, 'size' => 14]], // Main heading
                2 => ['font' => ['bold' => true, 'size' => 12]], // Sub heading
                3 => ['font' => ['bold' => true, 'size' => 10]], // Group headings
                4 => ['font' => ['bold' => true, 'size' => 10]], // Column headings
            ];
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $sheet = $event->sheet;
                    $rowCount = $this->data->count();

                    // Calculate total columns
                    $totalColumns = 0;
                    foreach ($this->headingStructure['columnGroups'] as $group) {
                        $totalColumns += count($group['columns']);
                    }

                    $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

                    // Insert 5 rows at the top for headers (main, sub, third, group, column)
                    $sheet->insertNewRowBefore(1, 5);

                    // Main heading (Row 1)
                    $sheet->mergeCells("A1:{$lastColumn}1");
                    $sheet->setCellValue("A1", $this->headingStructure['mainHeading']);
                    $sheet->getStyle("A1")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                    ]);

                    // Sub heading (Row 2)
                    $sheet->mergeCells("A2:{$lastColumn}2");
                    $sheet->setCellValue("A2", $this->headingStructure['subHeading']);
                    $sheet->getStyle("A2")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                    ]);

                    // Third heading (Row 3)
                    $sheet->mergeCells("A3:{$lastColumn}3");
                    $sheet->setCellValue("A3", $this->headingStructure['thirdHeading']);
                    $sheet->getStyle("A3")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                    ]);

                    // Group headings and column headings
                    $currentCol = 1;

                    foreach ($this->headingStructure['columnGroups'] as $group) {
                        $groupStartCol = $currentCol;
                        $groupEndCol = $currentCol + count($group['columns']) - 1;

                        // Group title (Row 4) - merge if title exists and has multiple columns
                        if (!empty($group['title']) && count($group['columns']) > 1) {
                            $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupStartCol);
                            $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupEndCol);
                            $sheet->mergeCells("{$startColLetter}4:{$endColLetter}4");
                            $sheet->setCellValue("{$startColLetter}4", $group['title']);
                            $sheet->getStyle("{$startColLetter}4")->applyFromArray([
                                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]
                            ]);
                        }

                        // Individual column headings (Row 5)
                        foreach ($group['columns'] as $columnTitle) {
                            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol);
                            $sheet->setCellValue("{$colLetter}5", $columnTitle);
                            $sheet->getStyle("{$colLetter}5")->applyFromArray([
                                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]
                            ]);
                            $currentCol++;
                        }
                    }

                    // Apply styles to all data rows (starting from row 6)
                    $dataStartRow = 6;
                    $dataEndRow = $dataStartRow + $rowCount - 1;

                    // Set consistent font and alignment for all data cells
                    $sheet->getStyle("A{$dataStartRow}:{$lastColumn}{$dataEndRow}")->applyFromArray([
                        'font' => ['size' => 10, 'name' => 'Arial', 'bold' => false],
                        'alignment' => ['vertical' => 'center', 'wrapText' => true]
                    ]);

                    // Apply borders to all data including headers (from group headings row)
                    $borderRange = "A4:{$lastColumn}{$dataEndRow}";
                    $sheet->getStyle($borderRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    // Center align numeric columns
                    $numericColumns = ['A', 'B', 'G', 'N', 'O', 'P', 'Q', 'S', 'V', 'X', 'Z', 'AB', 'AC'];
                    foreach ($numericColumns as $col) {
                        $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$dataEndRow}")
                            ->getAlignment()->setHorizontal('center');
                    }

                    // Apply number format for percentage and marks columns
                    $sheet->getStyle("Q{$dataStartRow}:Q{$dataEndRow}")
                        ->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("S{$dataStartRow}:S{$dataEndRow}")
                        ->getNumberFormat()->setFormatCode('0.00');
                    $sheet->getStyle("V{$dataStartRow}:V{$dataEndRow}")
                        ->getNumberFormat()->setFormatCode('0');
                    $sheet->getStyle("X{$dataStartRow}:X{$dataEndRow}")
                        ->getNumberFormat()->setFormatCode('0');
                    $sheet->getStyle("Z{$dataStartRow}:Z{$dataEndRow}")
                        ->getNumberFormat()->setFormatCode('0.00');

                    // Set column widths
                    $columnWidths = [
                        'A' => 10,   // सरल क्रमांक
                        'B' => 15,   // पंचायत क्रमांक
                        'C' => 15,   // पंजीयन क्रमांक
                        'D' => 20,   // ग्राम पंचायत का नाम
                        'E' => 25,   // केंद्र का नाम
                        'F' => 25,   // पद जिस हेतु आवेदन किया गया है
                        'G' => 15,   // कुल प्राप्त आवेदन की संख्या
                        'H' => 25,   // प्रत्येक आवेदिका का नाम
                        'I' => 25,   // पिता / पति का नाम
                        'J' => 40,   // निवास
                        'K' => 15,   // जाति
                        'L' => 20,   // आयु
                        'M' => 20,   // शैक्षणिक योग्यता
                        'N' => 12,   // पूर्णांक
                        'O' => 12,   // प्राप्तांक
                        'P' => 15,   // कुल प्राप्तांक का प्रतिशत
                        'Q' => 15,   // प्राप्तांक प्रतिशत का 60 प्रतिशत
                        'R' => 15,   // विधवा/परित्यक्ता - विवरण
                        'S' => 12,   // विधवा/परित्यक्ता - अंक
                        'T' => 12,   // अनु. जाति - क्रमांक
                        'U' => 12,   // अनु. जाति - अंक
                        'V' => 12,   // गरीबी रेखा - क्रमांक
                        'W' => 12,   // गरीबी रेखा - अंक
                        'X' => 30,   // कार्यकर्ता/सहायिका - विवरण
                        'Y' => 12,   // कार्यकर्ता/सहायिका - अंक
                        'Z' => 30,   // अनुभव - विवरण
                        'AA' => 12,  // अनुभव - अंक
                        'AB' => 12,  // कुल प्राप्तांक
                        'AC' => 12,  // योग्यता क्रम
                        'AD' => 30,  // अपात्र कारण
                    ];

                    // Apply widths to relevant columns
                    foreach ($columnWidths as $col => $width) {
                        $sheet->getColumnDimension($col)->setWidth($width);
                    }

                    // Set row heights
                    $sheet->getRowDimension(1)->setRowHeight(30);
                    $sheet->getRowDimension(2)->setRowHeight(25);
                    $sheet->getRowDimension(3)->setRowHeight(35);
                    $sheet->getRowDimension(4)->setRowHeight(50);

                    // Set auto height for all data rows
                    for ($row = $dataStartRow; $row <= $dataEndRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(-1); // Auto height
                    }

                    // Wrap text for address and description columns
                    $wrapTextColumns = ['J', 'R', 'X', 'Z', 'AD'];
                    foreach ($wrapTextColumns as $col) {
                        $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$dataEndRow}")
                            ->getAlignment()->setWrapText(true);
                    }
                }
            ];
        }
        },
        $fileName
    );
}
    
}
