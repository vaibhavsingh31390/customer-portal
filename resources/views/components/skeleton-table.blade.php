@props(['rows' => 5, 'cols' => 4])

<div {{ $attributes->merge(['class' => 'portal-skeleton-table']) }} aria-hidden="true">
    @for ($r = 0; $r < $rows; $r++)
        <div class="portal-skeleton-table__row">
            @for ($c = 0; $c < $cols; $c++)
                <div class="portal-skeleton-table__cell"></div>
            @endfor
        </div>
    @endfor
</div>
