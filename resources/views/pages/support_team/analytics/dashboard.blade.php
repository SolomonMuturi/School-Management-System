@extends('layouts.master')
@section('page_title', 'Analytics Dashboard')
@section('content')

    <style>
        .dashboard-stat h3 { font-size: 1.25rem; margin-bottom: 0.1rem; }
    </style>

    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-finance"><i class="icon-cash3 mr-1"></i><span class="d-none d-md-inline">Financial Analytics</span></a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-academic"><i class="icon-graduation2 mr-1"></i><span class="d-none d-md-inline">Academic Analytics</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        {{-- ============ FINANCIAL ============ --}}
        <div class="tab-pane fade show active" id="tab-finance">
            <div class="row">
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-admins">
                        <h3>{{ number_format($kp_financial['collected'], 2) }}</h3>
                        <span class="stat-label">Fees Collected ({{ $year }})</span>
                        <i class="icon-cash3 stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-students">
                        <h3>{{ number_format($kp_financial['outstanding'], 2) }}</h3>
                        <span class="stat-label">Outstanding Fees</span>
                        <i class="icon-user-block stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-teachers">
                        <h3>{{ number_format($kp_financial['expenses'], 2) }}</h3>
                        <span class="stat-label">Expenses</span>
                        <i class="icon-cart5 stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-parents">
                        <h3>{{ number_format($kp_financial['net'], 2) }}</h3>
                        <span class="stat-label">Net Income</span>
                        <i class="icon-stats-growth stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-cash3 mr-2 text-primary"></i>Cash Flow (Income vs Expenses, last 6 months)</h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <div id="cashflow-chart" style="height: 320px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-pie-chart4 mr-2 text-primary"></i>Payment Methods</h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <div id="methods-chart" style="height: 320px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-user-block mr-2 text-danger"></i>Outstanding Balance by Class</h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <div id="outstanding-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ ACADEMIC ============ --}}
        <div class="tab-pane fade" id="tab-academic">
            <div class="row">
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-students">
                        <h3>{{ $kp_academic['students'] }}</h3>
                        <span class="stat-label">Active Students</span>
                        <i class="icon-users stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-teachers">
                        <h3>{{ $kp_academic['teachers'] }}</h3>
                        <span class="stat-label">Teachers</span>
                        <i class="icon-user-tie stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-admins">
                        <h3>{{ $kp_academic['subjects'] }}</h3>
                        <span class="stat-label">Subjects</span>
                        <i class="icon-books stat-icon"></i>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 mb-3">
                    <div class="dashboard-stat stat-parents">
                        <h3>{{ $kp_academic['classes'] }}</h3>
                        <span class="stat-label">Classes</span>
                        <i class="icon-magazine stat-icon"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-users mr-2 text-primary"></i>Students by Class</h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <div id="byclass-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-graduation2 mr-2 text-primary"></i>Performance by Class
                                @if($latest_exam)
                                    <span class="text-muted font-size-xs">({{ $latest_exam->name }})</span>
                                @endif
                            </h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            @if($latest_exam)
                                <div id="perf-chart" style="height: 300px;"></div>
                            @else
                                <p class="text-muted mb-0">No exams recorded for {{ $year }} yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="card">
                        <div class="card-header header-elements-inline">
                            <h5 class="card-title"><i class="icon-stats-growth mr-2 text-success"></i>Mark Distribution ({{ $year }})</h5>
                            {!! Qs::getPanelOptions() !!}
                        </div>
                        <div class="card-body">
                            <div id="dist-chart" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('global_assets/js/plugins/visualization/echarts/echarts.min.js') }}"></script>
    <script>
        $(function () {
            var colors = ['#2196F3', '#26A69A', '#EF5350', '#FF7043', '#AB47BC', '#5C6BC0', '#26A69A', '#FFA726'];
            var allCharts = [];

            // Cash flow
            var cash = @json($monthly_cashflow);
            var months = $.map(cash, function (m) { return m.label; });
            var income = $.map(cash, function (m) { return m.income; });
            var expenses = $.map(cash, function (m) { return m.expenses; });

            var cashflowEl = document.getElementById('cashflow-chart');
            if (cashflowEl) {
                var cf = echarts.init(cashflowEl);
                allCharts.push(cf);
                cf.setOption({
                    tooltip: {trigger: 'axis'},
                    legend: {data: ['Income', 'Expenses'], bottom: 0},
                    grid: {left: 60, right: 30, top: 20, bottom: 50},
                    xAxis: {type: 'category', data: months},
                    yAxis: {type: 'value'},
                    series: [
                        {name: 'Income', type: 'line', smooth: true, areaStyle: {}, itemStyle: {color: '#26A69A'}, data: income},
                        {name: 'Expenses', type: 'line', smooth: true, areaStyle: {}, itemStyle: {color: '#EF5350'}, data: expenses}
                    ]
                });
            }

            // Payment methods donut
            var methods = @json($payment_methods);
            var pmEl = document.getElementById('methods-chart');
            if (pmEl) {
                var pm = echarts.init(pmEl);
                allCharts.push(pm);
                pm.setOption({
                    tooltip: {trigger: 'item', formatter: '{b}: {c} ({d}%)'},
                    legend: {bottom: 0, type: 'scroll', textStyle: {fontSize: 11}},
                    series: [{
                        type: 'pie',
                        radius: ['45%', '72%'],
                        center: ['50%', '45%'],
                        itemStyle: {borderRadius: 6, borderColor: '#fff', borderWidth: 2},
                        data: $.map(methods, function (m, i) {
                            return {name: m.payment_method.replace(/_/g, ' '), value: m.total, itemStyle: {color: colors[i % colors.length]}};
                        })
                    }]
                });
            }

            // Outstanding by class
            var oustanding = @json($outstanding_by_class);
            var ocEl = document.getElementById('outstanding-chart');
            if (ocEl) {
                var oc = echarts.init(ocEl);
                allCharts.push(oc);
                oc.setOption({
                    tooltip: {trigger: 'axis'},
                    grid: {left: 90, right: 30, top: 20, bottom: 40},
                    xAxis: {type: 'value'},
                    yAxis: {type: 'category', data: $.map(oustanding, function (o) { return o.class; })},
                    series: [{
                        name: 'Balance',
                        type: 'bar',
                        barWidth: 18,
                        data: $.map(oustanding, function (o) { return {value: o.balance, itemStyle: {color: '#EF5350'}}; })
                    }]
                });
            }

            // Students by class
            var byclass = @json($students_by_class);
            var bcEl = document.getElementById('byclass-chart');
            if (bcEl) {
                var bc = echarts.init(bcEl);
                allCharts.push(bc);
                bc.setOption({
                    tooltip: {trigger: 'axis'},
                    grid: {left: 60, right: 30, top: 20, bottom: 50},
                    xAxis: {type: 'category', data: $.map(byclass, function (c) { return c.name; })},
                    yAxis: {type: 'value'},
                    series: [{
                        name: 'Students',
                        type: 'bar',
                        barWidth: 22,
                        data: $.map(byclass, function (c, i) { return {value: c.student_record_count, itemStyle: {color: colors[i % colors.length]}}; })
                    }]
                });
            }

            // Exam performance by class
            var perf = @json($exam_performance);
            var pfEl = document.getElementById('perf-chart');
            if (pfEl) {
                var pf = echarts.init(pfEl);
                allCharts.push(pf);
                pf.setOption({
                    tooltip: {trigger: 'axis'},
                    grid: {left: 60, right: 30, top: 20, bottom: 50},
                    xAxis: {type: 'category', data: $.map(perf, function (p) { return p.class; })},
                    yAxis: {type: 'value', max: 100},
                    series: [{
                        name: 'Average',
                        type: 'bar',
                        barWidth: 22,
                        data: $.map(perf, function (p) { return {value: p.average, itemStyle: {color: '#26A69A'}}; })
                    }]
                });
            }

            // Mark distribution
            var dist = @json($marks_distribution);
            var dtEl = document.getElementById('dist-chart');
            if (dtEl) {
                var order = ['0-39', '40-49', '50-59', '60-69', '70-79', '80-100'];
                var dataMap = {};
                $.each(dist, function (i, d) { dataMap[d.band] = d.count; });
                var labels = $.grep(order, function (b) { return dataMap[b] !== undefined; });
                var values = $.map(labels, function (b) { return dataMap[b]; });

                var dt = echarts.init(dtEl);
                allCharts.push(dt);
                dt.setOption({
                    tooltip: {trigger: 'axis'},
                    grid: {left: 60, right: 30, top: 20, bottom: 50},
                    xAxis: {type: 'category', data: labels},
                    yAxis: {type: 'value'},
                    series: [{
                        name: 'Marks Recorded',
                        type: 'bar',
                        barWidth: 26,
                        itemStyle: {color: '#2196F3'},
                        data: values
                    }]
                });
            }

            window.addEventListener('resize', function () {
                allCharts.forEach(function (ch) { ch.resize(); });
            });
        });
    </script>
@endsection