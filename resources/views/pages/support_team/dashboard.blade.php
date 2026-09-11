@extends('layouts.master')
@section('page_title', 'My Dashboard')
@section('content')

    @if(Qs::userIsTeamSA())
       <div class="row">
           <div class="col-sm-6 col-xl-3 mb-3">
               <div class="dashboard-stat stat-students">
                   <h3>{{ $users->where('user_type', 'student')->count() }}</h3>
                   <span class="stat-label">Total Students</span>
                   <i class="icon-users4 stat-icon"></i>
               </div>
           </div>

           <div class="col-sm-6 col-xl-3 mb-3">
               <div class="dashboard-stat stat-teachers">
                   <h3>{{ $users->where('user_type', 'teacher')->count() }}</h3>
                   <span class="stat-label">Total Teachers</span>
                   <i class="icon-users2 stat-icon"></i>
               </div>
           </div>

           <div class="col-sm-6 col-xl-3 mb-3">
               <div class="dashboard-stat stat-admins">
                   <h3>{{ $users->where('user_type', 'admin')->count() }}</h3>
                   <span class="stat-label">Total Administrators</span>
                   <i class="icon-user-tie stat-icon"></i>
               </div>
           </div>

           <div class="col-sm-6 col-xl-3 mb-3">
               <div class="dashboard-stat stat-parents">
                   <h3>{{ $users->where('user_type', 'parent')->count() }}</h3>
                   <span class="stat-label">Total Parents</span>
                   <i class="icon-user stat-icon"></i>
               </div>
           </div>
       </div>
       @endif

    {{--Events Calendar Begins--}}
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title"><i class="icon-calendar5 mr-2 text-primary"></i>School Events Calendar</h5>
         {!! Qs::getPanelOptions() !!}
        </div>

        <div class="card-body">
            <div class="fullcalendar-basic"></div>
        </div>
    </div>
    {{--Events Calendar Ends--}}
    @endsection
