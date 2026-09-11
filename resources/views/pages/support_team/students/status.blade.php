@extends('layouts.master')
@section('page_title', 'Student Status')
@section('content')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Student Status Overview</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($statuses as $st)
                <div class="col-md-3 mb-2">
                    <div class="d-flex justify-content-between border p-2">
                        <span class="font-weight-semibold">{{ ucfirst($st) }}</span>
                        <span class="badge badge-{{ $st == 'active' ? 'success' : ($st == 'suspended' || $st == 'withdrawn' ? 'danger' : ($st == 'graduated' ? 'primary' : 'secondary')) }}">{{ isset($counts[$st]) ? $counts[$st] : 0 }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h6 class="card-title">Update Student Status</h6>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Adm No</th>
                    <th>Class</th>
                    <th>Current Status</th>
                    <th>Set Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s->user->name }}</td>
                        <td>{{ $s->adm_no }}</td>
                        <td>{{ $s->my_class->name }}</td>
                        <td>
                            <span class="badge badge-{{ $s->status == 'active' ? 'success' : ($s->status == 'suspended' || $s->status == 'withdrawn' ? 'danger' : ($s->status == 'graduated' ? 'primary' : 'secondary')) }}">{{ ucfirst($s->status ?: 'active') }}</span>
                        </td>
                        <td>
                            <form method="post" action="{{ route('students.status.update') }}" class="d-flex">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $s->user_id }}">
                                <select name="status" class="select form-control mr-2" style="width:150px">
                                    @foreach($statuses as $st)
                                        <option value="{{ $st }}" {{ $s->status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection