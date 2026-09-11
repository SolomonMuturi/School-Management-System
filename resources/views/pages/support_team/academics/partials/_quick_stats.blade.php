<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-teal-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-users icon-2x opacity-75"></i></div>
                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $students_total }}</h3>
                    <span class="text-uppercase font-size-xs">Total Students</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-blue-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-windows2 icon-2x opacity-75"></i></div>
                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $classes_count }}</h3>
                    <span class="text-uppercase font-size-xs">Classes</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-indigo-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-pin icon-2x opacity-75"></i></div>
                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $subjects_count }}</h3>
                    <span class="text-uppercase font-size-xs">Subjects</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-people icon-2x opacity-75"></i></div>
                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $teachers_count }}</h3>
                    <span class="text-uppercase font-size-xs">Teachers</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-body">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-calendar3 icon-2x text-primary"></i></div>
                <div class="media-body">
                    <h6 class="mb-0">Current Academic Year</h6>
                    <span class="text-muted">{{ $current_year->name }} ({{ $current_year->start_date }} &rarr; {{ $current_year->end_date }})</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-body">
            <div class="media">
                <div class="mr-3 align-self-center"><i class="icon-stack-check icon-2x text-info"></i></div>
                <div class="media-body">
                    <h6 class="mb-0">Current Term</h6>
                    <span class="text-muted">{{ $current_term ? $current_term->name : 'Not set' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info border-0 alert-dismissible">
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    <h6 class="alert-title font-weight-semibold">Academic Performance Summary ({{ $year }})</h6>
    <span>Average Test Score: <strong>{{ $performance['avg_tca'] }}</strong> &nbsp;|&nbsp;
        Average Exam Score: <strong>{{ $performance['avg_exm'] }}</strong> &nbsp;|&nbsp;
        Overall Average: <strong>{{ $performance['avg_total'] }}</strong> &nbsp;|&nbsp;
        Marks Recorded: <strong>{{ $performance['count'] }}</strong></span>
</div>