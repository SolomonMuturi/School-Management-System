<div class="row mb-3">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-primary">{{ isset($marks_current) ? $marks_current->count() : 0 }}</h4>
                <span class="text-muted">Assessments</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-success">{{ isset($exam_records) ? $exam_records->count() : 0 }}</h4>
                <span class="text-muted">Exams</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-info">{{ isset($subjects) ? $subjects->count() : 0 }}</h4>
                <span class="text-muted">Learning Areas</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body py-3">
                <h4 class="font-weight-semibold mb-1 text-warning">{{ isset($exam_records) ? $exam_records->where('year', $current_session)->count() : 0 }}</h4>
                <span class="text-muted">Report Cards</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title"><i class="icon-book mr-2 text-primary"></i>Learning Areas / Subjects (Current Performance)</h6>
        <div class="header-elements">
            @if($marks_exam)
                <span class="badge badge-light">Term {{ $marks_exam->term }} • {{ $marks_exam->year }}</span>
                <a href="{{ route('marks.show', [Qs::hash($sr->user_id), $marks_exam->year]) }}" class="btn btn-primary btn-sm ml-1">View Full Report</a>
                <form method="post" action="{{ route('academic.report_cards.pdf') }}" class="d-inline ml-1">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $marks_exam->id }}">
                    <input type="hidden" name="class_id" value="{{ $sr->my_class_id }}">
                    <input type="hidden" name="student_id" value="{{ Qs::hash($sr->id) }}">
                    <button class="btn btn-danger btn-sm"><i class="icon-file-download mr-1"></i>Download Report Card</button>
                </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if(isset($marks_current) && $marks_current->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Learning Area</th>
                        <th>CA (t1+t2)</th>
                        <th>Exam</th>
                        <th>Total</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks_current as $m)
                        <tr>
                            <td>{{ $m->subject ? $m->subject->name : 'Subject #' . $m->subject_id }}</td>
                            <td>{{ $m->tex1 }}</td>
                            <td>{{ $m->exm }}</td>
                            <td class="font-weight-bold">{{ $m->tex3 }}</td>
                            <td>{{ $m->grade ? $m->grade->name : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No assessment marks recorded yet.</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title"><i class="icon-stats-bars2 mr-2 text-success"></i>Examination Results</h6>
        @if($marks_year)
            <div class="header-elements">
                <a href="{{ route('marks.show', [Qs::hash($sr->user_id), $marks_year]) }}" class="btn btn-primary btn-sm">Marksheet</a>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if(isset($exam_records) && $exam_records->count())
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Exam</th>
                        <th>Term</th>
                        <th>Year</th>
                        <th>Class</th>
                        <th>Average</th>
                        <th>Position</th>
                        <th>Teacher Comment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam_records as $er)
                        @php $exam = isset($exams[$er->exam_id]) ? $exams[$er->exam_id] : null; @endphp
                        <tr>
                            <td>{{ $exam ? $exam->name : 'Exam #' . $er->exam_id }}</td>
                            <td>{{ $exam ? $exam->term : 'N/A' }}</td>
                            <td>{{ $er->year }}</td>
                            <td>{{ ($er->my_class_id ? optional(\App\Models\MyClass::find($er->my_class_id))->name : null) ?: 'N/A' }}</td>
                            <td class="font-weight-bold">{{ $er->ave }}</td>
                            <td>{{ $er->pos }}</td>
                            <td>{{ $er->t_comment ?: '-' }}</td>
                            <td>
                                @if($exam)
                                    <a href="{{ route('marks.print', [Qs::hash($sr->user_id), $er->exam_id, $er->year]) }}?download=1" class="btn btn-info btn-sm"><i class="icon-file-download mr-1"></i>Download</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted mb-0">No examination results recorded yet.</p>
        @endif
    </div>
</div>

@if(isset($promotions) && $promotions->count())
    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title"><i class="icon-arrow-up4 mr-2 text-primary"></i>Class History (Promotions)</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>From Class</th>
                        <th>To Class</th>
                        <th>Session</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $p)
                        <tr>
                            <td>{{ $p->fc ? $p->fc->name : 'N/A' }}</td>
                            <td>{{ $p->tc ? $p->tc->name : 'N/A' }}</td>
                            <td>{{ $p->from_session . ' - ' . $p->to_session }}</td>
                            <td>
                                @if($p->grad == 1)
                                    <span class="badge badge-success">Graduated</span>
                                @elseif($p->status == 'done')
                                    <span class="badge badge-primary">Promoted</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif