<?php

namespace App\Support;

use App\Models\CustomerComplaint;
use Illuminate\Database\Eloquent\Builder;

class ComplaintAnalytics
{
    /**
     * @return array<string, int>
     */
    public static function monthlyStatusCounts(?string $clientCode = null): array
    {
        $query = CustomerComplaint::query()
            ->whereRaw(SqlHelper::currentMonthFilter('complaint_date'));

        if ($clientCode !== null && $clientCode !== '') {
            $query->where('client_code', $clientCode);
        }

        $row = $query
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'PN' THEN 1 ELSE 0 END) as count_pn,
                SUM(CASE WHEN status = 'HL' THEN 1 ELSE 0 END) as count_hl,
                SUM(CASE WHEN status = 'SV' THEN 1 ELSE 0 END) as count_sv,
                SUM(CASE WHEN status = 'CM' THEN 1 ELSE 0 END) as count_cm,
                SUM(CASE WHEN status = 'CL' THEN 1 ELSE 0 END) as count_cl,
                SUM(CASE WHEN status NOT IN ('CM', 'CL') THEN 1 ELSE 0 END) as count_open,
                SUM(CASE WHEN status IN ('CM', 'CL') THEN 1 ELSE 0 END) as count_closed
            ")
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'PN' => (int) ($row->count_pn ?? 0),
            'HL' => (int) ($row->count_hl ?? 0),
            'SV' => (int) ($row->count_sv ?? 0),
            'CM' => (int) ($row->count_cm ?? 0),
            'CL' => (int) ($row->count_cl ?? 0),
            'open' => (int) ($row->count_open ?? 0),
            'closed' => (int) ($row->count_closed ?? 0),
            'pending' => (int) ($row->count_pn ?? 0),
        ];
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>, colors: array<int, string>}
     */
    public static function chartPayload(array $counts): array
    {
        $codes = ['PN', 'HL', 'SV', 'CM', 'CL'];

        return [
            'labels' => array_map(fn (string $code) => ComplaintStatus::label($code), $codes),
            'data' => array_map(fn (string $code) => $counts[$code] ?? 0, $codes),
            'colors' => array_map(fn (string $code) => ComplaintStatus::chartColor($code), $codes),
        ];
    }

    /**
     * @return array{total: int, open: int, closed: int, pending: int, PN: int, HL: int, SV: int, CM: int, CL: int}
     */
    public static function toAnalyticsObject(array $counts): object
    {
        return (object) array_merge($counts, [
            'total_count' => $counts['total'],
            'count_pend' => $counts['pending'],
            'count_open' => $counts['open'],
            'count_closed' => $counts['closed'],
        ]);
    }
}
