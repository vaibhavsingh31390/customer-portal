{{-- Shared AJAX data table shell. Preserves #listForm, #form_detail, #pagination hooks. --}}
@props(['action', 'headers' => [], 'lengthCol' => 'col-md-3'])

<form action="{{ $action }}" id="listForm" method="post" enctype="multipart/form-data">
    <div class="portal-table-toolbar">
        <div class="portal-table-toolbar__group portal-table-toolbar__group--length">
            <label class="portal-table-toolbar__label" for="dataTable_length_select">Show entries</label>
            <select name="dataTable_length" id="dataTable_length_select" aria-controls="dataTable"
                class="portal-table-toolbar__select portal-select-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="portal-table-toolbar__group portal-table-toolbar__group--search" id="dataTable_filter">
            <label class="portal-table-toolbar__label" for="dataTable_search_input">Search</label>
            <input type="search" id="dataTable_search_input" class="portal-table-toolbar__search portal-search-input"
                placeholder="Search complaints…" aria-controls="dataTable">
        </div>
    </div>

    <div class="loader-wrapper portal-table-loader" aria-live="polite" aria-busy="false">
        <x-loader variant="table" label="Loading results" />
        <x-skeleton-table :rows="6" :cols="count($headers) ?: 4" class="portal-skeleton-table--block" />
    </div>

    <div class="table-responsive portal-table-wrap">
        <table class="table table-hover text-vertical-middle mb-0 portal-table {{ ($numericLastColumn ?? false) ? 'portal-table--counts' : '' }}">
            <thead class="portal-table__head">
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="form_detail"></tbody>
        </table>
    </div>

    <div class="portal-pagination mt-2" id="pagination"></div>
</form>
