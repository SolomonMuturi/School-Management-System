<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title"><i class="icon-calendar3 mr-2 text-primary"></i>Class Timetable</h6>
        <div class="header-elements">
            @if($tt_ttr)
                <span class="badge badge-light">{{ $tt_ttr->exam_id ? 'Exam' : 'Class' }} Timetable • {{ $tt_ttr->year }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        @if($tt_days->count() && $tt_slots->count())
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Time</th>
                            @foreach($tt_days as $day)
                                <th>{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tt_slots as $slot)
                            <tr>
                                <td class="font-weight-semibold">{{ $slot->full }}</td>
                                @foreach($tt_days as $day)
                                    @php
                                        $cell = $tt_grid->where('day', $day)->where('time', $slot->full)->first();
                                    @endphp
                                    @if($cell && $cell['subject'])
                                        {{ $cell['subject'] }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">No timetable published for this class yet.</p>
        @endif
    </div>
</div>