@extends('layouts.master')
@section('page_title', 'Academic Years & Terms')
@section('content')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Academic Years & Terms</h6>
            {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-highlight">
                <li class="nav-item"><a href="#years" class="nav-link active" data-toggle="tab">Academic Years</a></li>
                <li class="nav-item"><a href="#terms" class="nav-link" data-toggle="tab">Terms / Semesters</a></li>
                @if(Qs::userIsTeamSA())
                    <li class="nav-item"><a href="#year-add" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Year</a></li>
                    <li class="nav-item"><a href="#term-add" class="nav-link" data-toggle="tab"><i class="icon-plus2"></i> Add Term</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="years">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="post" action="{{ route('academic.year_term.store') }}" class="form-inline">
                                @csrf
                                <label class="mr-2">Year:</label>
                                <select name="academic_year_id" id="yid" class="form-control select mr-3" data-placeholder="Select Year">
                                    @foreach($years as $y)
                                        <option {{ $y->id == $current_year->id ? 'selected' : '' }} value="{{ $y->id }}">{{ $y->name }}</option>
                                    @endforeach
                                </select>
                                <label class="mr-2">Term:</label>
                                <select name="academic_term_id" id="tid" class="form-control select mr-3" data-placeholder="Select Term">
                                    @foreach($terms as $t)
                                        <option {{ $t->is_current ? 'selected' : '' }} value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <input type="date" name="start_date" class="form-control mr-2" placeholder="Start">
                                <input type="date" name="end_date" class="form-control mr-2" placeholder="End">
                                <button class="btn btn-primary" type="submit">Link Term to Year</button>
                            </form>
                        </div>
                    </div>

                    <table class="table datatable-responsive">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Terms</th>
                            <th>Make Current</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($years as $y)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $y->name }}
                                    @if($y->id == $current_year->id)
                                        <span class="badge badge-success">Current</span>
                                    @endif
                                </td>
                                <td>{{ $y->start_date }}</td>
                                <td>{{ $y->end_date }}</td>
                                <td>{{ $y->status }}</td>
                                <td>
                                    @foreach($y->terms as $t)
                                        <span class="badge badge-info">{{ $t->name }} {{ $t->pivot->start_date ? '('.date('M d', strtotime($t->pivot->start_date)).')' : '' }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <form method="post" action="{{ route('academic.years.current') }}">
                                        @csrf
                                        <input type="hidden" name="year_id" value="{{ $y->id }}">
                                        <button type="submit" class="btn btn-sm {{ $y->id == $current_year->id ? 'btn-success disabled' : 'btn-outline-primary' }}" {{ $y->id == $current_year->id ? 'disabled' : '' }}>Set Current</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="terms">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <table class="table datatable-responsive">
                                <thead>
                                <tr><th>S/N</th><th>Name</th><th>Abbr</th><th>Sequence</th><th>Status</th><th>Set Current</th></tr>
                                </thead>
                                <tbody>
                                @foreach($terms as $t)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $t->name }}</td>
                                        <td>{{ $t->abbr }}</td>
                                        <td>{{ $t->sequence }}</td>
                                        <td>
                                            @if($t->is_current)
                                                <span class="badge badge-success">Current Term</span>
                                            @else
                                                <span class="badge badge-light">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form method="post" action="{{ route('academic.terms.current') }}">
                                                @csrf
                                                <input type="hidden" name="term_id" value="{{ $t->id }}">
                                                <button type="submit" class="btn btn-sm {{ $t->is_current ? 'btn-success disabled' : 'btn-outline-primary' }}" {{ $t->is_current ? 'disabled' : '' }}>Set Current</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if(Qs::userIsTeamSA())
                    <div class="tab-pane fade" id="year-add">
                        <form method="post" action="{{ route('academic.years.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Academic Year Name (e.g. 2027-2028):</label>
                                        <input type="text" name="name" class="form-control" placeholder="2027-2028" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Start Date:</label>
                                        <input type="date" name="start_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>End Date:</label>
                                        <input type="date" name="end_date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary">Save Year</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="term-add">
                        <form method="post" action="{{ route('academic.terms.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Term Name:</label>
                                        <input type="text" name="name" class="form-control" placeholder="Fourth Term" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Abbreviation:</label>
                                        <input type="text" name="abbr" class="form-control" placeholder="4th">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Sequence:</label>
                                        <input type="number" name="sequence" class="form-control" placeholder="4">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary">Save Term</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection