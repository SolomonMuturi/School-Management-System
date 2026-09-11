<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ (Route::is('dashboard')) ? 'active' : '' }}">
                        <i class="icon-home4"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(Qs::canAccess('Analytics'))
                {{--Analytics--}}
                <li class="nav-item">
                    <a href="{{ route('analytics.dashboard') }}" class="nav-link {{ Route::is('analytics.dashboard') ? 'active' : '' }}">
                        <i class="icon-stats-bars3"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                @endif

                {{--Academics--}}
                @if(Qs::userIsAcademic())
                    <li class="nav-item nav-item-submenu {{ (Route::is('academic.*') || Route::is('tt.*') || Route::is('ttr.*') || Route::is('ts.*') || Route::is('classes.*') || Route::is('subjects.*') || Route::is('exams.*') || Route::is('grades.*') || Route::is('marks.*')) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Academics</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Academics">

                            {{--Academic Dashboard--}}
                            <li class="nav-item"><a href="{{ route('academic.dashboard') }}" class="nav-link {{ Route::is('academic.dashboard') ? 'active' : '' }}">Academic Dashboard</a></li>

                            {{--Classes--}}
                            <li class="nav-item nav-item-submenu {{ (Route::is('academic.classes') || Route::is('classes.*')) ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('academic.classes') }}" class="nav-link {{ (Route::is('academic.classes') || Route::is('classes.*')) ? 'active' : '' }}">Classes</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('academic.classes') }}" class="nav-link {{ Route::is('academic.classes') ? 'active' : '' }}">Class List</a></li>
                                    @if(Qs::userIsTeamSA())
                                        <li class="nav-item"><a href="{{ route('classes.dashboard') }}" class="nav-link {{ Route::is('classes.dashboard') ? 'active' : '' }}">Class Dashboard</a></li>
                                        <li class="nav-item"><a href="{{ route('classes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['classes.index', 'classes.edit']) ? 'active' : '' }}">Manage Classes</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{--Subjects--}}
                            <li class="nav-item nav-item-submenu {{ Route::is('subjects.*') ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('subjects.index') }}" class="nav-link {{ Route::is('subjects.*') ? 'active' : '' }}">Subjects</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('subjects.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['subjects.index', 'subjects.edit']) ? 'active' : '' }}">All Subjects</a></li>
                                    @if(Qs::userIsTeamSA())
                                        <li class="nav-item"><a href="{{ route('subjects.dashboard') }}" class="nav-link {{ Route::is('subjects.dashboard') ? 'active' : '' }}">Subject Dashboard</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{--Exams--}}
                            <li class="nav-item nav-item-submenu {{ (Route::is('exams.*') || Route::is('grades.*') || Route::is('marks.*')) ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('exams.index') }}" class="nav-link {{ (Route::is('exams.*') || Route::is('grades.*') || Route::is('marks.*')) ? 'active' : '' }}">Exams</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('exams.index') }}" class="nav-link {{ Route::is('exams.index') ? 'active' : '' }}">All Exams</a></li>
                                    @if(Qs::userIsTeamSA())
                                        <li class="nav-item"><a href="{{ route('exams.dashboard') }}" class="nav-link {{ Route::is('exams.dashboard') ? 'active' : '' }}">Exam Dashboard</a></li>
                                        <li class="nav-item"><a href="{{ route('grades.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['grades.index', 'grades.edit']) ? 'active' : '' }}">Grades</a></li>
                                        <li class="nav-item"><a href="{{ route('marks.tabulation') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.tabulation']) ? 'active' : '' }}">Tabulation Sheet</a></li>
                                        <li class="nav-item"><a href="{{ route('marks.batch_fix') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.batch_fix']) ? 'active' : '' }}">Batch Fix</a></li>
                                    @endif
                                    @if(Qs::userIsTeamSAT())
                                        <li class="nav-item"><a href="{{ route('marks.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.index']) ? 'active' : '' }}">Marks</a></li>
                                        <li class="nav-item"><a href="{{ route('marks.bulk') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.bulk', 'marks.show']) ? 'active' : '' }}">Marksheet</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{--Timetables--}}
                            <li class="nav-item"><a href="{{ route('tt.index') }}" class="nav-link {{ (Route::is('tt.*') || Route::is('ttr.*') || Route::is('ts.*')) ? 'active' : '' }}">Timetables</a></li>

                            {{--Academic Years & Terms--}}
                            <li class="nav-item"><a href="{{ route('academic.years') }}" class="nav-link {{ Route::is('academic.years') ? 'active' : '' }}">Academic Years &amp; Terms</a></li>

                            {{--Report Cards--}}
                            <li class="nav-item"><a href="{{ route('academic.report_cards') }}" class="nav-link {{ Route::is('academic.report_cards') ? 'active' : '' }}">Report Cards</a></li>

                            {{--Academic Performance--}}
                            <li class="nav-item"><a href="{{ route('academic.performance') }}" class="nav-link {{ Route::is('academic.performance') ? 'active' : '' }}">Academic Performance</a></li>

                            {{--Academic Reports--}}
                            <li class="nav-item"><a href="{{ route('academic.reports') }}" class="nav-link {{ Route::is('academic.reports') ? 'active' : '' }}">Academic Reports</a></li>

                            {{--Curriculum & Learning--}}
                            <li class="nav-item nav-item-submenu {{ (Route::is('academic.curriculum') || Route::is('academic.lessons') || Route::is('academic.homework')) ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('academic.curriculum') }}" class="nav-link {{ (Route::is('academic.curriculum') || Route::is('academic.lessons') || Route::is('academic.homework')) ? 'active' : '' }}">Curriculum &amp; Learning</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('academic.curriculum') }}" class="nav-link {{ Route::is('academic.curriculum') ? 'active' : '' }}">Curriculum</a></li>
                                    <li class="nav-item"><a href="{{ route('academic.lessons') }}" class="nav-link {{ Route::is('academic.lessons') ? 'active' : '' }}">Lessons</a></li>
                                    <li class="nav-item"><a href="{{ route('academic.homework') }}" class="nav-link {{ Route::is('academic.homework') ? 'active' : '' }}">Assignments &amp; Homework</a></li>
                                </ul>
                            </li>

                            {{--Academic Settings--}}
                            @if(Qs::userIsTeamSA())
                            <li class="nav-item"><a href="{{ route('academic.settings') }}" class="nav-link {{ Route::is('academic.settings') ? 'active' : '' }}">Academic Settings</a></li>
                            @endif

                        </ul>
                    </li>
                    @endif

                {{--People--}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ (Route::is('students.*') || Route::is('academic.assign') || Route::is('st.reset_pass')) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-users"></i> <span> People</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="People">

                            {{--Students--}}
                            <li class="nav-item nav-item-submenu {{ Route::is('students.*') ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('students.index') }}" class="nav-link {{ Route::is('students.*') ? 'active' : '' }}">Students</a>
                                <ul class="nav nav-group-sub">
                                    @if(Qs::userIsTeamSA())
                                        {{--Admit Student--}}
                                        <li class="nav-item"><a href="{{ route('students.create') }}" class="nav-link {{ (Route::is('students.create')) ? 'active' : '' }}">Admit Student</a></li>
                                    @endif

                                    {{--All Students--}}
                                    <li class="nav-item"><a href="{{ route('students.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.index', 'students.show']) ? 'active' : '' }}">All Students</a></li>

                                    {{--Class Lists--}}
                                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit']) ? 'nav-item-expanded' : '' }}">
                                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.list', 'students.edit']) ? 'active' : '' }}">Class Lists</a>
                                        <ul class="nav nav-group-sub">
                                            @foreach(App\Models\MyClass::orderBy('name')->get() as $c)
                                                <li class="nav-item"><a href="{{ route('students.list', $c->id) }}" class="nav-link ">{{ $c->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>

                                    @if(Qs::userIsTeamSA())
                                        {{--Student Promotion--}}
                                        <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage']) ? 'nav-item-expanded' : '' }}"><a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion', 'students.promotion_manage' ]) ? 'active' : '' }}">Student Promotion</a>
                                        <ul class="nav nav-group-sub">
                                            <li class="nav-item"><a href="{{ route('students.promotion') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion']) ? 'active' : '' }}">Promote Students</a></li>
                                            <li class="nav-item"><a href="{{ route('students.promotion_manage') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion_manage']) ? 'active' : '' }}">Manage Promotions</a></li>
                                        </ul>
                                        </li>

                                        {{--Students Graduated--}}
                                        <li class="nav-item"><a href="{{ route('students.graduated') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.graduated' ]) ? 'active' : '' }}">Students Graduated</a></li>
                                    @endif

                                    {{--Student Status--}}
                                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['students.status']) ? 'nav-item-open' : '' }}"><a href="{{ route('students.status') }}" class="nav-link {{ Route::is('students.status') ? 'active' : '' }}">Student Status</a></li>

                                    {{--Student Reports--}}
                                    <li class="nav-item {{ in_array(Route::currentRouteName(), ['students.reports']) ? 'nav-item-open' : '' }}"><a href="{{ route('students.reports') }}" class="nav-link {{ Route::is('students.reports') ? 'active' : '' }}">Student Reports</a></li>

                                    @if(Qs::userIsTeamSA())
                                        {{--Student Settings--}}
                                        <li class="nav-item"><a href="{{ route('students.settings') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.settings' ]) ? 'active' : '' }}">Student Settings</a></li>
                                    @endif
                                </ul>
                            </li>

                            {{--Teachers--}}
                            <li class="nav-item"><a href="{{ route('academic.assign') }}" class="nav-link {{ Route::is('academic.assign') ? 'active' : '' }}">Teacher &amp; Subject Assignment</a></li>

                            {{--Parents--}}
                            <li class="nav-item"><a href="{{ route('students.guardians') }}" class="nav-link {{ Route::is('students.guardians') ? 'active' : '' }}">Parents &amp; Guardians</a></li>

                        </ul>
                    </li>
                @endif

                {{--Operations--}}
                @if(Qs::userIsTeamAccount() && !Qs::userIsTeamSA())
                    <li class="nav-item nav-item-submenu {{ str_starts_with((string)Route::currentRouteName(), 'finance.') ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-cash3"></i> <span> Operations</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Operations">

                            {{--Finance--}}
                            <li class="nav-item nav-item-submenu {{ str_starts_with((string)Route::currentRouteName(), 'finance.') ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('finance.dashboard') }}" class="nav-link {{ str_starts_with((string)Route::currentRouteName(), 'finance.') ? 'active' : '' }}">Finance</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('finance.dashboard') }}" class="nav-link {{ Route::is('finance.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.fee_types') }}" class="nav-link {{ Route::is('finance.fee_types') ? 'active' : '' }}">Fee Types</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.fee_structures') }}" class="nav-link {{ Route::is('finance.fee_structures') ? 'active' : '' }}">Fee Structures</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.billing') }}" class="nav-link {{ Route::is('finance.billing') ? 'active' : '' }}">Student Billing</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.payments') }}" class="nav-link {{ Route::is('finance.payments') ? 'active' : '' }}">Payments</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.receipts') }}" class="nav-link {{ Route::is('finance.receipts') ? 'active' : '' }}">Receipts</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.discounts') }}" class="nav-link {{ Route::is('finance.discounts') ? 'active' : '' }}">Discounts</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.refunds') }}" class="nav-link {{ Route::is('finance.refunds') ? 'active' : '' }}">Refunds</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.expenses') }}" class="nav-link {{ Route::is('finance.expenses') ? 'active' : '' }}">Expenses</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.suppliers') }}" class="nav-link {{ Route::is('finance.suppliers') ? 'active' : '' }}">Suppliers</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.accounts') }}" class="nav-link {{ Route::is('finance.accounts') ? 'active' : '' }}">Cash &amp; Bank</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.reports') }}" class="nav-link {{ Route::is('finance.reports') ? 'active' : '' }}">Reports</a></li>
                                    <li class="nav-item"><a href="{{ route('finance.settings') }}" class="nav-link {{ Route::is('finance.settings') ? 'active' : '' }}">Settings</a></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                @endif

                {{--Services--}}
                @if(Qs::userIsTeamSAT())
                    <li class="nav-item nav-item-submenu {{ Route::is('students.attendance') ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-alarm"></i> <span> Services</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Services">

                            {{--Attendance--}}
                            <li class="nav-item"><a href="{{ route('students.attendance') }}" class="nav-link {{ Route::is('students.attendance') ? 'active' : '' }}">Daily Attendance</a></li>

                        </ul>
                    </li>
                @endif

                @if(Qs::userIsTeamSA())
                    {{--Administration--}}
                    <li class="nav-item nav-item-submenu {{ (Route::is('users.*') || Route::is('settings') || Route::is('academic.settings') || Route::is('pins.*') || str_starts_with((string)Route::currentRouteName(), 'finance.')) ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a href="#" class="nav-link"><i class="icon-shield2"></i> <span> Administration</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Administration">

                            {{--Users--}}
                            <li class="nav-item nav-item-submenu {{ Route::is('users.*') ? 'nav-item-expanded' : '' }}">
                                <a href="{{ route('users.index') }}" class="nav-link {{ Route::is('users.*') ? 'active' : '' }}">Users</a>
                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ route('users.dashboard') }}" class="nav-link {{ Route::is('users.dashboard') ? 'active' : '' }}">Users Dashboard</a></li>
                                    <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link {{ Route::is('users.index') ? 'active' : '' }}">All Users</a></li>
                                    <li class="nav-item"><a href="{{ route('users.roles') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['users.roles', 'users.roles.edit']) ? 'active' : '' }}">Roles</a></li>
                                    <li class="nav-item"><a href="{{ route('users.permissions') }}" class="nav-link {{ Route::is('users.permissions') ? 'active' : '' }}">Permissions</a></li>
                                    <li class="nav-item"><a href="{{ route('users.activity') }}" class="nav-link {{ Route::is('users.activity') ? 'active' : '' }}">Activity Logs</a></li>
                                </ul>
                            </li>

                            @if(Qs::userIsTeamAccount())
                                <li class="nav-item"><a href="{{ route('finance.dashboard') }}" class="nav-link {{ str_starts_with((string)Route::currentRouteName(), 'finance.') ? 'active' : '' }}">Finance</a></li>
                            @endif

                            @if(Qs::canAccess('System Settings'))
                                <li class="nav-item"><a href="{{ route('settings') }}" class="nav-link {{ Route::is('settings') ? 'active' : '' }}">System Settings</a></li>
                            @endif

                            @if(Qs::canAccess('Pins'))
                            <li class="nav-item"><a href="{{ route('pins.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['pins.index', 'pins.create']) ? 'active' : '' }}">PINs</a></li>
                            @endif

                        </ul>
                    </li>
                @endif

                @include('pages.'.Qs::getUserType().'.menu')

                {{--Manage Account--}}
                <li class="nav-item">
                    <a href="{{ route('my_account') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['my_account']) ? 'active' : '' }}"><i class="icon-user"></i> <span>My Account</span></a>
                </li>

                </ul>
            </div>
        </div>
</div>