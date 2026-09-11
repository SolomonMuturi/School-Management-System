@extends('layouts.master')
@section('page_title', 'Student Settings')
@section('content')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">Student ID & Document Settings</h6>
            </div>
            <form method="post" action="{{ route('students.settings.store') }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Student ID Prefix</label>
                        <input type="text" name="student_id_prefix" value="{{ isset($settings['student_id_prefix']) ? $settings['student_id_prefix'] : 'CJ' }}" class="form-control">
                        <span class="form-text text-muted">IDs are generated as PREFIX/CLASS-CODE/YEAR/NUMBER (e.g. CJ/S/2025/1234)</span>
                    </div>
                    <div class="form-group">
                        <label>Document Types (comma separated)</label>
                        <textarea name="doc_types" rows="3" class="form-control">{{ isset($settings['student_doc_types']) ? $settings['student_doc_types'] : '' }}</textarea>
                        <span class="form-text text-muted">e.g. Birth Certificate, Admission Letter, Medical Report</span>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="card-title">Required Fields on Admit/Edit Form</h6>
            </div>
            <form method="post" action="{{ route('students.settings.store') }}" autocomplete="off">
                @csrf
                <div class="card-body">
                    @php $required = isset($settings['student_required_fields']) ? explode(',', $settings['student_required_fields']) : []; @endphp
                    @foreach($all_fields as $key => $label)
                        <div class="form-check mb-2">
                            <label class="form-check-label">
                                <input type="checkbox" name="required_fields[]" value="{{ $key }}" class="form-check-input-styled" {{ in_array($key, $required) ? 'checked' : '' }} data-fouc>
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary">Save Required Fields</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection