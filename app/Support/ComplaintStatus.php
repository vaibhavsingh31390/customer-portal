<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class ComplaintStatus
{
    public const CLOSED = ['CM', 'CL'];

    public const CODES = ['PN', 'HL', 'SV', 'CM', 'CL'];

    public static function isClosed(?string $status): bool
    {
        return in_array($status, self::CLOSED, true);
    }

    public static function isValidFilter(?string $filter): bool
    {
        if ($filter === null || $filter === '') {
            return true;
        }

        return in_array($filter, array_merge(self::CODES, ['open', 'closed']), true);
    }

    /**
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            '' => 'All statuses',
            'open' => 'All open',
            'closed' => 'All closed',
            'PN' => self::label('PN'),
            'HL' => self::label('HL'),
            'SV' => self::label('SV'),
            'CM' => self::label('CM'),
            'CL' => self::label('CL'),
        ];
    }

    public static function applyFilter(Builder $query, ?string $filter, string $column = 'status'): Builder
    {
        if ($filter === null || $filter === '') {
            return $query;
        }

        if ($filter === 'open') {
            return $query->whereNotIn($column, self::CLOSED);
        }

        if ($filter === 'closed') {
            return $query->whereIn($column, self::CLOSED);
        }

        if (in_array($filter, self::CODES, true)) {
            return $query->where($column, $filter);
        }

        return $query;
    }

    public static function chartColor(string $code): string
    {
        return match ($code) {
            'PN' => '#d97706',
            'HL' => '#64748b',
            'SV' => '#2563eb',
            'CM' => '#16a34a',
            'CL' => '#dc2626',
            default => '#94a3b8',
        };
    }

    public static function label(?string $status): string
    {
        return match ($status) {
            'CL' => 'Cancelled',
            'CM' => 'Complete',
            'HL' => 'On Hold',
            'PN' => 'Pending',
            'SV' => 'Awaiting Verification',
            default => $status ?: 'Unknown',
        };
    }

    public static function lifecycleLabel(?string $status): string
    {
        return self::isClosed($status) ? 'Closed' : 'Open';
    }

    public static function badgeVariant(?string $status): string
    {
        return match ($status) {
            'CM' => 'success',
            'CL' => 'error',
            'HL' => 'default',
            'SV' => 'info',
            'PN' => 'pending',
            default => 'default',
        };
    }

    public static function tableBadgeHtml(?string $status): string
    {
        $status = (string) ($status ?? '');
        $label = htmlspecialchars(self::label($status), ENT_QUOTES, 'UTF-8');
        $variant = self::badgeVariant($status);
        $lifecycleClass = self::isClosed($status) ? 'closed' : 'open';
        $title = htmlspecialchars(
            self::lifecycleLabel($status).' ticket · '.self::label($status),
            ENT_QUOTES,
            'UTF-8'
        );

        return '<span class="portal-badge portal-badge--'.$variant.' portal-badge--ticket portal-badge--ticket-'.$lifecycleClass.'" title="'.$title.'">'.$label.'</span>';
    }

    public static function rowClass(?string $status): string
    {
        return self::isClosed($status)
            ? 'portal-table__row portal-table__row--closed'
            : 'portal-table__row portal-table__row--open';
    }
}
