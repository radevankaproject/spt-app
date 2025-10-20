{{-- resources/views/layouts/partials/_skeleton-table.blade.php --}}
{{-- Variabel yang dibutuhkan: $rows (jumlah baris), $cols (jumlah kolom) --}}
<table class="table table-hover">
    <thead>
        <tr>
            @for ($i = 0; $i < ($cols ?? 3); $i++)
                <th>
                    <div class="skeleton skeleton-text skeleton-text-sm"></div>
                </th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @for ($i = 0; $i < ($rows ?? 5); $i++)
            <tr>
                @for ($j = 0; $j < ($cols ?? 3); $j++)
                    <td>
                        <div class="skeleton skeleton-text" style="width: {{ rand(70, 95) }}%"></div>
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>
