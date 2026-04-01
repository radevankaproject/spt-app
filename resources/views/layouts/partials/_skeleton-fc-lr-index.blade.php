<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 placeholder-glow">
    <div class="placeholder col-3 rounded" style="height: 28px;"></div>
    <div class="placeholder col-2 rounded" style="height: 38px;"></div>
</div>

<div class="card border-0 shadow-sm placeholder-glow">
    <div class="card-header border-bottom pb-3">
        <div class="placeholder col-2 rounded" style="height: 20px;"></div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th><div class="placeholder col-8 rounded"></div></th>
                        <th><div class="placeholder col-8 rounded"></div></th>
                        <th><div class="placeholder col-8 rounded"></div></th>
                        <th><div class="placeholder col-8 rounded"></div></th>
                        <th><div class="placeholder col-8 rounded"></div></th>
                        <th><div class="placeholder col-8 rounded"></div></th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td><div class="placeholder col-7 rounded"></div></td>
                            <td><div class="placeholder col-5 rounded" style="height: 24px;"></div></td>
                            <td>
                                <div class="placeholder col-8 mb-1 rounded d-block"></div>
                                <div class="placeholder col-5 rounded d-block"></div>
                            </td>
                            <td><div class="placeholder col-10 rounded"></div></td>
                            <td><div class="placeholder col-6 rounded" style="height: 24px;"></div></td>
                            <td><div class="placeholder col-8 rounded"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
