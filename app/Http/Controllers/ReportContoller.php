<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportContoller extends Controller
{
    private function fetchDistrictReportSummaryRows()
    {
        $sql = "
            SELECT
                ma.district_lgd_code as district_code,
                COALESCE(dist.name, '-') as district,
                COALESCE(mpr.project, '-') as project_name,
                COALESCE(area.area_id, 0) as area_id,
                COALESCE(area.area_name_hi, '-') as area_type,
                COUNT(DISTINCT mp.post_id) as posts_count,
                COUNT(DISTINCT ma.Advertisement_ID) as advertisements_count,
                GROUP_CONCAT(DISTINCT CONCAT(pvm.post_vacancy_map, ':', pvm.no_of_vacancy) SEPARATOR ',') AS total_vacancy,
                COUNT(DISTINCT CASE WHEN upa.status = 'Verified' THEN upa.apply_id END) as approved_count,
                COUNT(DISTINCT CASE WHEN upa.status = 'Rejected' THEN upa.apply_id END) as rejected_count,
                COUNT(DISTINCT CASE WHEN upa.is_final_submit != 0 AND upa.status NOT IN ('Verified','Rejected') THEN upa.apply_id END) as pending_count,
                COUNT(DISTINCT CASE WHEN upa.is_final_submit != 0 THEN upa.apply_id END) as applications_count
            FROM master_advertisement ma
            LEFT JOIN master_post mp
                ON mp.Advertisement_ID = ma.Advertisement_ID
                AND mp.is_disable = 1
            LEFT JOIN master_district dist
                ON ma.district_lgd_code = dist.District_Code_LGD
            LEFT JOIN master_area area
                ON mp.fk_area_id = area.area_id
            LEFT JOIN tbl_user_post_apply upa
                ON upa.fk_post_id = mp.post_id
            LEFT JOIN post_vacancy_map pvm
                ON pvm.fk_post_id = mp.post_id
            LEFT JOIN master_projects mpr
                ON ma.project_code = mpr.project_code
            WHERE ma.is_disable = 1
            GROUP BY ma.district_lgd_code, dist.name, ma.project_code, area.area_id, area.area_name_hi
            ORDER BY dist.name, area.area_id
        ";

        $rows = DB::select($sql);

        foreach ($rows as $row) {
            $cleanData = str_replace(['[', ']'], '', $row->total_vacancy);

            $parts = array_filter(explode(',', $cleanData), fn($v) => $v !== '' && $v !== null);

            $values = array_map(function ($item) {
                $item = trim($item);
                if (str_contains($item, ':')) {
                    $pieces = explode(':', $item, 2);
                    return (int) trim($pieces[1]);
                }
                return (int) $item;
            }, $parts);

            $row->total_vacancy = array_sum($values);
        }

        return $rows;
    }
    function report()
    {

        // $reports = DB::table('master_advertisment')
        // ->leftJoin('master_post', 'master_advertisment.id', '=', 'master_post.advertisment_id')
        // ->get();

        $results = $this->fetchDistrictReportSummaryRows();


        $outputText = "\n";
        $totalAds = 0;
        $totalPosts = 0;
        $zeroPostAlerts = "";

        foreach ($results as $row) {
            $adsCount = (int) ($row->advertisements_count ?? 0);
            $postsCount = (int) ($row->posts_count ?? 0);
            $outputText .= "जिला: {$row->district}, परियोजना: {$row->project_name}, क्षेत्र: {$row->area_type}, विज्ञापन-{$adsCount}, पद-{$postsCount}\n";

            $totalAds += $adsCount;
            $totalPosts += $postsCount;

            // Agar post zero hai toh alert line save karein
            if ($postsCount == 0) {
                $zeroPostAlerts .= "DPO {$row->district}, परियोजना: {$row->project_name} .\n";
            }
        }

        $outputText .= "\nTotal : विज्ञापन-{$totalAds}, पद-{$totalPosts}\n";
        $outputText .= "\n------------\n";
        $outputText .= $zeroPostAlerts;
        $outputText .= "\nfor your kind attention.\n";
        $outputText .= "\n--\n विज्ञापन तो जारी किया गया है किंतु पद नही बनाया गया है। यह वेबसाईट पर त्रुटिपूर्ण Display होगा है तथा candidate apply ही नही कर पाएंगे।

कृपया manual का page13-15 का अध्ययन कर कार्यवाही करें!";

        return view('/admin/report', compact('outputText'));
    }

    /**
     * Export post-wise location/status report as Excel-friendly CSV.
     */
    public function exportPostsExcel()
    {
        $today = now()->toDateString();

        // 1) Fetch core post + advertisement metadata (simple query)
        $posts = DB::table('master_post as mp')
            ->leftJoin('master_advertisement as ma', 'mp.Advertisement_ID', '=', 'ma.Advertisement_ID')
            ->leftJoin('master_district as dist', 'ma.district_lgd_code', '=', 'dist.District_Code_LGD')
            ->leftJoin('master_projects as proj', 'ma.project_code', '=', 'proj.project_code')
            ->leftJoin('master_area as area', 'mp.fk_area_id', '=', 'area.area_id')
            ->leftJoin('master_user as cdpo', function ($join) {
                $join->on('cdpo.admin_district_id', '=', 'ma.district_lgd_code')
                    ->on('cdpo.project_id', '=', 'ma.project_code')
                    ->where('cdpo.Role', '=', 'Super_admin');
            })
            ->select([
                'mp.post_id',
                'mp.title as post_name',
                'mp.gp_nnn_code',
                'mp.village_code',
                'mp.std_nnn_code',
                'mp.ward_no',
                'mp.fk_area_id',
                'ma.Advertisement_ID as advertisement_no',
                'ma.Advertisement_Title as advertisement_title',
                'ma.Advertisement_Date as start_date',
                'ma.Date_For_Age as end_date',
                'ma.district_lgd_code',
                'ma.project_code',
                'dist.name as district',
                DB::raw('COALESCE(proj.project, ma.project_code) as project'),
                'area.area_name_hi as area_type',
                DB::raw('COALESCE(cdpo.Full_Name, "") as cdpo_name'),
            ])
            ->orderBy('dist.name')
            ->orderBy('proj.project')
            ->orderBy('mp.title')
            ->get();

        if ($posts->isEmpty()) {
            return response()->stream(function () {
                fprintf(fopen('php://output', 'w'), "No data");
            }, 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="post_report_empty.txt"'
            ]);
        }

        $postIds = $posts->pluck('post_id')->all();

        // 2) Precompute vacancy sums per post
        $vacancies = DB::table('post_vacancy_map')
            ->select('fk_post_id', DB::raw('SUM(no_of_vacancy) as total'))
            ->whereIn('fk_post_id', $postIds)
            ->groupBy('fk_post_id')
            ->pluck('total', 'fk_post_id');

        // 3) Application counts per post
        $applications = DB::table('tbl_user_post_apply')
            ->select('fk_post_id', DB::raw('COUNT(DISTINCT apply_id) as total'))
            ->whereIn('fk_post_id', $postIds)
            ->groupBy('fk_post_id')
            ->pluck('total', 'fk_post_id');

        // 4) Collect unique location/ward codes for lookups
        $gpCodes = [];
        $villageCodes = [];
        $cityCodes = [];
        $wardIds = [];

        $decodeJson = function ($value) {
            if (empty($value))
                return [];
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_filter($decoded, fn($v) => $v !== null && $v !== ''));
            }
            return [$value];
        };

        foreach ($posts as $p) {
            $gpCodes = array_merge($gpCodes, $decodeJson($p->gp_nnn_code));
            $villageCodes = array_merge($villageCodes, $decodeJson($p->village_code));
            $cityCodes = array_merge($cityCodes, $decodeJson($p->std_nnn_code));
            $wardIds = array_merge($wardIds, array_map('intval', $decodeJson($p->ward_no)));
        }

        $gpCodes = array_values(array_unique(array_filter($gpCodes)));
        $villageCodes = array_values(array_unique(array_filter($villageCodes)));
        $cityCodes = array_values(array_unique(array_filter($cityCodes)));
        $wardIds = array_values(array_unique(array_filter($wardIds)));

        // 5) Fetch lookup names
        $gpMap = DB::table('master_panchayats')
            ->whereIn('panchayat_lgd_code', $gpCodes ?: ['-'])
            ->pluck('panchayat_name_hin', 'panchayat_lgd_code');

        $villageMap = DB::table('master_villages')
            ->whereIn('village_code', $villageCodes ?: ['-'])
            ->pluck('village_name_hin', 'village_code');

        $cityMap = DB::table('master_nnn')
            ->whereIn('std_nnn_code', $cityCodes ?: ['-'])
            ->pluck('nnn_name', 'std_nnn_code');

        $wardMap = DB::table('master_ward')
            ->whereIn('ID', $wardIds ?: ['-'])
            ->select('ID', 'ward_name', 'ward_no')
            ->get()
            ->keyBy('ID');

        // 6) Build rows in PHP
        $data = [];
        foreach ($posts as $p) {
            $gpList = array_map(fn($code) => $gpMap[$code] ?? $code, $decodeJson($p->gp_nnn_code));
            $villageList = array_map(fn($code) => $villageMap[$code] ?? $code, $decodeJson($p->village_code));
            $cityList = array_map(fn($code) => $cityMap[$code] ?? $code, $decodeJson($p->std_nnn_code));
            $wardList = array_map(function ($id) use ($wardMap) {
                if (!isset($wardMap[$id]))
                    return $id;
                $w = $wardMap[$id];
                return $w->ward_name . ' (' . $w->ward_no . ')';
            }, array_map('intval', $decodeJson($p->ward_no)));

            $gpOrCity = !empty($cityList) ? implode(', ', $cityList) : implode(', ', $gpList);
            $villageOrWard = !empty($wardList) ? implode(', ', $wardList) : implode(', ', $villageList);

            $totalVacancy = (int) ($vacancies[$p->post_id] ?? 0);
            $applicationCount = (int) ($applications[$p->post_id] ?? 0);

            $status = 'Under Process';
            if (!empty($p->start_date) && !empty($p->end_date)) {
                if ($today >= $p->start_date && $today <= $p->end_date) {
                    $status = 'Open';
                } elseif ($today > $p->end_date) {
                    $status = 'Closed';
                }
            }

            $data[] = [
                $p->district,
                $p->project,
                $p->cdpo_name,
                $p->area_type,
                $gpOrCity,
                $villageOrWard,
                $p->advertisement_no,
                $p->post_name,
                $totalVacancy,
                $p->start_date,
                $p->end_date,
                $applicationCount,
                $status,
            ];
        }

        $headers = [
            'District',
            'Project (CDPO Area)',
            'CDPO Name',
            'Area Type',
            'Gram Panchayat / City Name',
            'Village / Ward Name',
            'Advertisement No',
            'Post Name',
            'Total Vacancy',
            'Start Date',
            'End Date',
            'Applications Received',
            'Status',
        ];

        $filename = 'post_report_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($headers, $data) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * District-wise summary view (datatable + drill-down).
     */
    public function districtReportView()
    {
        return view('report.district_report');
    }

    /**
     * Summary data: district x area_type with counts.
     */
    public function districtReportData(Request $request)
    {
        $rows = $this->fetchDistrictReportSummaryRows();

        // dd($row);

        return response()->json(['data' => $rows]);
    }

    /**
     * Export summary CSV for district report.
     */


    /**
     * Drill-down detail rows for a district (optionally area_id).
     */
    public function districtReportDetail(Request $request)
    {
        $districtCode = $request->query('district_code');
        $areaId = $request->query('area_id');

        if (empty($districtCode)) {
            return response()->json(['data' => []]);
        }

        $query = DB::table('master_post as mp')
            ->join('master_advertisement as ma', 'mp.Advertisement_ID', '=', 'ma.Advertisement_ID')
            ->leftJoin('master_area as area', 'mp.fk_area_id', '=', 'area.area_id')
            ->leftJoin('tbl_user_post_apply as upa', 'upa.fk_post_id', '=', 'mp.post_id')
            ->leftJoin('post_vacancy_map as pvm', 'pvm.fk_post_id', '=', 'mp.post_id')
            ->leftJoin('master_advertisement as adv', 'mp.Advertisement_ID', '=', 'adv.Advertisement_ID')
            ->where('ma.district_lgd_code', $districtCode)
            ->where('ma.is_disable', 1)
            ->where('mp.is_disable', 1)
            ->select(
                'mp.post_id',
                'mp.title as post_name',
                'ma.Advertisement_ID as advertisement_no',
                DB::raw('COALESCE(adv.Advertisement_Title, "") as advertisement_title'),
                DB::raw('COALESCE(area.area_name_hi, "-") as area_type'),
                'mp.gp_nnn_code',
                'mp.village_code',
                'mp.std_nnn_code',
                'mp.ward_no',
                // --- FIXED LOGIC STARTS HERE ---
                // Vacancy ke liye GROUP_CONCAT use karke PHP mein sum karna safe hai agar data multiply ho raha ho, 
                // ya fir DISTINCT vacancy ID ka logic lagana padega. Detail report mein DISTINCT sum niche hai:
                DB::raw("GROUP_CONCAT(DISTINCT pvm.no_of_vacancy SEPARATOR ',') AS total_vacancy"),
                DB::raw("COUNT(DISTINCT CASE WHEN upa.is_final_submit !=0 THEN upa.apply_id END) as applications_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN upa.status = 'Verified' THEN upa.apply_id END) as approved_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN upa.status = 'Rejected' THEN upa.apply_id END) as rejected_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN upa.is_final_submit != 0 AND (upa.status IS NULL OR upa.status NOT IN ('Verified','Rejected')) THEN upa.apply_id END) as pending_count")
                // --- FIXED LOGIC ENDS HERE ---
            )
            ->groupBy(
                'mp.post_id',
                'mp.title',
                'ma.Advertisement_ID',
                'adv.Advertisement_Title',
                'area.area_name_hi',
                'mp.gp_nnn_code',
                'mp.village_code',
                'mp.std_nnn_code',
                'mp.ward_no'
            );

        if (!is_null($areaId) && $areaId !== '' && $areaId !== 'null' && $areaId !== 'undefined') {
            $query->where('mp.fk_area_id', $areaId);
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            // $row->total_vacancy = explode(',', $row->total_vacancy);

            $cleanData = str_replace(['[', ']'], '', $row->total_vacancy); // Result: "2,2,2"

            // String ko array me convert karein
            $array = explode(',', $cleanData);

            // Sum calculate karein
            $row->total_vacancy = array_sum($array);
        }

        // Decode locations for readability
        $decode = function ($value) {
            if (empty($value))
                return [];
            $d = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($d))
                return $d;
            return [$value];
        };

        $postIds = $rows->pluck('post_id')->all();
        $gpCodes = [];
        $villageCodes = [];
        $cityCodes = [];
        $wardIds = [];

        foreach ($rows as $r) {
            $gpCodes = array_merge($gpCodes, $decode($r->gp_nnn_code));
            $villageCodes = array_merge($villageCodes, $decode($r->village_code));
            $cityCodes = array_merge($cityCodes, $decode($r->std_nnn_code));
            $wardIds = array_merge($wardIds, $decode($r->ward_no));
        }

        $gpMap = DB::table('master_panchayats')->whereIn('panchayat_lgd_code', $gpCodes ?: ['-'])->pluck('panchayat_name_hin', 'panchayat_lgd_code');
        $villageMap = DB::table('master_villages')->whereIn('village_code', $villageCodes ?: ['-'])->pluck('village_name_hin', 'village_code');
        $cityMap = DB::table('master_nnn')->whereIn('std_nnn_code', $cityCodes ?: ['-'])->pluck('nnn_name', 'std_nnn_code');
        $wardMap = DB::table('master_ward')->whereIn('ID', $wardIds ?: ['-'])->select('ID', 'ward_name', 'ward_no')->get()->keyBy('ID');

        $formatted = $rows->map(function ($r) use ($decode, $gpMap, $villageMap, $cityMap, $wardMap) {
            $gpList = array_map(fn($c) => $gpMap[$c] ?? $c, $decode($r->gp_nnn_code));
            $villageList = array_map(fn($c) => $villageMap[$c] ?? $c, $decode($r->village_code));
            $cityList = array_map(fn($c) => $cityMap[$c] ?? $c, $decode($r->std_nnn_code));
            $wardList = array_map(function ($id) use ($wardMap) {
                return isset($wardMap[$id]) ? ($wardMap[$id]->ward_name . ' (' . $wardMap[$id]->ward_no . ')') : $id;
            }, $decode($r->ward_no));

            return [
                'post_name' => $r->post_name,
                'advertisement_no' => $r->advertisement_no,
                'advertisement_title' => $r->advertisement_title,
                'area_type' => $r->area_type,
                'location' => !empty($cityList) ? implode(', ', $cityList) : implode(', ', $gpList),
                'village_ward' => !empty($wardList) ? implode(', ', $wardList) : implode(', ', $villageList),
                'total_vacancy' => (int) $r->total_vacancy,
                'applications_count' => (int) $r->applications_count,
                'approved_count' => (int) $r->approved_count,
                'rejected_count' => (int) $r->rejected_count,
                'pending_count' => (int) $r->pending_count,
            ];
        });

        return response()->json(['data' => $formatted]);
    }

    /**
     * Export summary (CSV) with optional district/area filters (for drill-down export).
     */

    /**
     * Export summary CSV for district report (CSV Version).
     */
    public function districtReportSummaryExport(Request $request)
    {
        $rows = $this->fetchDistrictReportSummaryRows();


        $filename = 'district_report_summary_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, [
                'जिले का नाम ',
                'परियोजना का नाम',
                'क्षेत्र का प्रकार',
                'कुल विज्ञापन',
                'कुल पद',
                'कुल रिक्तियाँ',
                'प्राप्त आवेदन ',
                'पात्र आवेदन',
                'अपात्र आवेदन',
                'लंबित आवेदन'
            ]);

            $totals = [
                'advertisements_count' => 0,
                'posts_count' => 0,
                'total_vacancy' => 0,
                'applications_count' => 0,
                'approved_count' => 0,
                'rejected_count' => 0,
                'pending_count' => 0,
            ];

            // Data rows
            foreach ($rows as $row) {
                $district = is_array($row) ? ($row['district'] ?? '') : ($row->district ?? '');
                $projectName = is_array($row) ? ($row['project_name'] ?? '') : ($row->project_name ?? '');
                $areaType = is_array($row) ? ($row['area_type'] ?? '') : ($row->area_type ?? '');
                $advertisementsCount = is_array($row) ? ($row['advertisements_count'] ?? 0) : ($row->advertisements_count ?? 0);
                $postsCount = is_array($row) ? ($row['posts_count'] ?? 0) : ($row->posts_count ?? 0);
                $totalVacancy = is_array($row) ? ($row['total_vacancy'] ?? 0) : ($row->total_vacancy ?? 0);
                $applicationsCount = is_array($row) ? ($row['applications_count'] ?? 0) : ($row->applications_count ?? 0);
                $approvedCount = is_array($row) ? ($row['approved_count'] ?? 0) : ($row->approved_count ?? 0);
                $rejectedCount = is_array($row) ? ($row['rejected_count'] ?? 0) : ($row->rejected_count ?? 0);
                $pendingCount = is_array($row) ? ($row['pending_count'] ?? 0) : ($row->pending_count ?? 0);

                $totals['advertisements_count'] += (int) $advertisementsCount;
                $totals['posts_count'] += (int) $postsCount;
                $totals['total_vacancy'] += (int) $totalVacancy;
                $totals['applications_count'] += (int) $applicationsCount;
                $totals['approved_count'] += (int) $approvedCount;
                $totals['rejected_count'] += (int) $rejectedCount;
                $totals['pending_count'] += (int) $pendingCount;

                fputcsv($handle, [
                    $district,
                    $projectName,
                    $areaType,
                    $advertisementsCount,
                    $postsCount,
                    $totalVacancy,
                    $applicationsCount,
                    $approvedCount,
                    $rejectedCount,
                    $pendingCount,
                ]);
            }

            // Totals row
            fputcsv($handle, [
                'कुल',
                '',
                '',
                $totals['advertisements_count'],
                $totals['posts_count'],
                $totals['total_vacancy'],
                $totals['applications_count'],
                $totals['approved_count'],
                $totals['rejected_count'],
                $totals['pending_count'],
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Export summary (CSV) with optional district/area filters (for drill-down export).
     */
    public function districtReportExport(Request $request)
    {
        $districtCode = $request->query('district_code');
        $areaId = $request->query('area_id');

        $data = $this->districtReportDetail($request)->getData(true)['data'] ?? [];

        $filename = 'district_report_' . ($districtCode ?? 'all') . '_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, [
                'विज्ञापन शीर्षक',
                'पद का नाम',
                'क्षेत्र का प्रकार',
                'ग्राम पंचायत / शहर',
                'गाँव / वार्ड',
                'कुल रिक्तियाँ',
                'प्राप्त आवेदन ',
                'पात्र आवेदन',
                'अपात्र आवेदन',
                'लंबित आवेदन'
            ]);

            $totals = [
                'total_vacancy' => 0,
                'applications_count' => 0,
                'approved_count' => 0,
                'rejected_count' => 0,
                'pending_count' => 0,
            ];

            // Data rows
            foreach ($data as $row) {
                $totals['total_vacancy'] += (int) ($row['total_vacancy'] ?? 0);
                $totals['applications_count'] += (int) ($row['applications_count'] ?? 0);
                $totals['approved_count'] += (int) ($row['approved_count'] ?? 0);
                $totals['rejected_count'] += (int) ($row['rejected_count'] ?? 0);
                $totals['pending_count'] += (int) ($row['pending_count'] ?? 0);

                fputcsv($handle, [
                    $row['advertisement_title'] ?? '',
                    $row['post_name'] ?? '',
                    $row['area_type'] ?? '',
                    $row['location'] ?? '',
                    $row['village_ward'] ?? '',
                    $row['total_vacancy'] ?? 0,
                    $row['applications_count'] ?? 0,
                    $row['approved_count'] ?? 0,
                    $row['rejected_count'] ?? 0,
                    $row['pending_count'] ?? 0,
                ]);
            }

            // Totals row
            fputcsv($handle, [
                'कुल',
                '',
                '',
                '',
                '',
                $totals['total_vacancy'],
                $totals['applications_count'],
                $totals['approved_count'],
                $totals['rejected_count'],
                $totals['pending_count'],
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
