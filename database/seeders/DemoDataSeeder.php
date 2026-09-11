<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\ClassType;
use App\Models\Exam;
use App\Models\MyClass;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    protected const SESSION = '2026';
    protected const TERM = 'Third Term';

    protected $classPId;
    protected $acYearId;
    protected $acTermId;

    protected $teacherIds = [];
    protected $parentIds = [];
    protected $studentIds = [];
    protected $studentAdmToUserId = [];
    protected $classIds = [];
    protected $classStudents = [];
    protected $subjectIdsPerClass = [];
    protected $feeTypeIds = [];
    protected $grades = [];

    public function run()
    {
        $this->classPId = ClassType::where('code', 'P')->first()->id;
        $this->grades = DB::table('grades')->select('id', 'mark_from', 'mark_to')->get();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->wipe();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->setupSettingsAndCalendar();
        $this->seedTeachersAndClasses();
        $this->seedSubjects();
        $this->seedParents();
        $this->seedStudents();
        $this->seedStudentModules();
        $this->seedCurriculum();
        $this->seedLessons();
        $this->seedAssignments();
        $this->seedExams();
        $this->seedMarksAndRecords();
        $this->seedFinance();
        $this->seedLibrary();
        $this->seedTimetables();
        $this->seedPins();
        $this->summary();
    }

    /***************************** WIPE *****************************/

    protected function wipe()
    {
        foreach ([
            'marks', 'exam_records', 'promotions', 'time_tables', 'time_slots', 'time_table_records',
            'book_requests', 'books', 'pins', 'assignment_submissions', 'assignments', 'lessons',
            'teacher_assignments', 'curriculum_topics', 'curriculum_subject', 'curriculums',
            'student_events', 'student_activities', 'student_transport', 'student_health',
            'student_discipline', 'student_documents', 'student_attendance', 'student_guardians',
            'refunds', 'discounts', 'finance_receipts', 'finance_payments', 'finance_transactions',
            'student_fees', 'fee_structures', 'fee_types', 'expenses', 'expense_categories', 'suppliers',
            'staff_records', 'student_records', 'subjects', 'my_classes', 'exams',
        ] as $table) {
            DB::table($table)->delete();
        }

        DB::table('users')->whereNotIn('user_type', ['super_admin', 'admin'])->delete();
    }

    /***************************** SETTINGS & CALENDAR *****************************/

    protected function setupSettingsAndCalendar()
    {
        $updates = [
            'current_session' => self::SESSION,
            'current_term' => '3',
            'system_title' => 'BFJPS',
            'system_name' => 'Bright Future Junior Primary School',
            'term_begins' => '8/24/2026',
            'term_ends' => '11/27/2026',
            'phone' => '+254700000001',
            'address' => 'Machakos County, Kenya',
            'system_email' => 'info@brightfuturejps.ac.ke',
            'alt_email' => 'payments@brightfuturejps.ac.ke',
            'email_host' => '',
            'email_pass' => '',
            'lock_exam' => '0',
            'logo' => '',
            'student_id_prefix' => 'BFJPS',
            'next_term_fees_p' => '22500',
        ];
        foreach ($updates as $type => $description) {
            DB::table('settings')->updateOrInsert(['type' => $type], [
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('academic_years')->update(['is_current' => 0]);
        $year = AcademicYear::updateOrCreate(['name' => self::SESSION], [
            'start_date' => '2026-01-12',
            'end_date' => '2026-12-04',
            'is_current' => 1,
            'status' => 'active',
        ]);
        $this->acYearId = $year->id;

        DB::table('academic_terms')->update(['is_current' => 0]);
        DB::table('academic_terms')->where('sequence', 3)->update(['is_current' => 1]);

        $termWindows = [
            1 => ['2026-01-12', '2026-04-24'],
            2 => ['2026-05-04', '2026-08-14'],
            3 => ['2026-08-24', '2026-11-27'],
        ];
        foreach ($termWindows as $seq => $win) {
            $term = AcademicTerm::where('sequence', $seq)->first();
            if ($seq === 3) { $this->acTermId = $term->id; }
            DB::table('academic_year_term')->updateOrInsert(
                ['academic_year_id' => $year->id, 'academic_term_id' => $term->id],
                ['start_date' => $win[0], 'end_date' => $win[1], 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /***************************** TEACHERS & CLASSES *****************************/

    protected function seedTeachersAndClasses()
    {
        $teachers = [
            ['TCH001', 'Mary Wanjiru', 'Female', '1988-03-15'],
            ['TCH002', 'John Kipchoge', 'Male', '1986-07-22'],
            ['TCH003', 'Achieng Ouma', 'Female', '1990-11-02'],
            ['TCH004', 'Winny Akinyi', 'Female', '1987-02-19'],
            ['TCH005', 'Brian Mutua', 'Male', '1989-09-14'],
            ['TCH006', 'Faith Chebet', 'Female', '1991-05-30'],
            ['TCH007', 'Charles Onyango', 'Male', '1984-12-08'],
            ['TCH008', 'Mercy Njeri', 'Female', '1992-01-25'],
            ['TCH009', 'Dennis Kariuki', 'Male', '1986-06-11'],
        ];
        $now = now();
        $userRows = [];
        $staffRows = [];
        foreach ($teachers as $i => $t) {
            $userId = null;
            $userRows[] = [
                'name' => $t[1],
                'username' => strtolower($t[0]),
                'email' => strtolower($t[0]) . '@bfjps.ac.ke',
                'phone' => '0710 0100' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT),
                'dob' => $t[3],
                'gender' => $t[2],
                'address' => 'Machakos County, Kenya',
                'user_type' => 'teacher',
                'code' => $t[0],
                'password' => Hash::make(Str::random(24)),
                'status' => 'active',
                'photo' => \App\Helpers\Qs::getDefaultUserImage(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $staffRows[] = ['code' => $t[0], 'emp_date' => '2022-01-10'];
        }
        foreach ($userRows as $i => $row) {
            $id = DB::table('users')->insertGetId($row);
            $this->teacherIds[$staffRows[$i]['code']] = $id;
            DB::table('staff_records')->insert([
                'user_id' => $id,
                'code' => $staffRows[$i]['code'],
                'emp_date' => $staffRows[$i]['emp_date'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $classes = [
            ['Grade 1', 'G1', 'TCH001'],
            ['Grade 2', 'G2', 'TCH004'],
            ['Grade 3', 'G3', 'TCH007'],
        ];
        foreach ($classes as $c) {
            $id = DB::table('my_classes')->insertGetId([
                'name' => $c[0],
                'code' => $c[1],
                'class_type_id' => $this->classPId,
                'teacher_id' => $this->teacherIds[$c[2]],
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->classIds[$c[1]] = $id;
        }
    }

    /***************************** SUBJECTS *****************************/

    protected function seedSubjects()
    {
        $areas = [
            ['English', 'ENG'],
            ['Kiswahili', 'KIS'],
            ['Mathematics', 'MAT'],
            ['Environmental Activities', 'ENV'],
            ['Creative Activities', 'CRA'],
            ['Religious Education', 'CRE'],
            ['Physical and Movement Education', 'PME'],
        ];
        $assigned = [
            'G1' => ['TCH001', 'TCH002', 'TCH003', 'TCH001', 'TCH002', 'TCH003', 'TCH003'],
            'G2' => ['TCH004', 'TCH005', 'TCH006', 'TCH004', 'TCH005', 'TCH006', 'TCH006'],
            'G3' => ['TCH007', 'TCH008', 'TCH009', 'TCH007', 'TCH008', 'TCH009', 'TCH009'],
        ];
        $now = now();
        foreach (['G1', 'G2', 'G3'] as $cls) {
            foreach ($areas as $i => $a) {
                $teacherCode = $assigned[$cls][$i];
                $subjectId = DB::table('subjects')->insertGetId([
                    'name' => $a[0],
                    'code' => $a[1],
                    'slug' => strtolower($a[1]),
                    'my_class_id' => $this->classIds[$cls],
                    'teacher_id' => $this->teacherIds[$teacherCode],
                    'description' => 'CBC ' . $a[0] . ' learning area for ' . $cls,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $this->subjectIdsPerClass[$cls][$a[0]] = $subjectId;

                DB::table('teacher_assignments')->insert([
                    'teacher_id' => $this->teacherIds[$teacherCode],
                    'subject_id' => $subjectId,
                    'my_class_id' => $this->classIds[$cls],
                    'academic_year_id' => $this->acYearId,
                    'academic_term_id' => $this->acTermId,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /***************************** PARENTS *****************************/

    protected function seedParents()
    {
        $parents = [
            ['PAR001', 'Jane Otieno', 'Female'], ['PAR002', 'Peter Kamau', 'Male'],
            ['PAR003', 'Grace Wanjiku', 'Female'], ['PAR004', 'Daniel Kiprop', 'Male'],
            ['PAR005', 'Margaret Achieng', 'Female'], ['PAR006', 'Samuel Mwangi', 'Male'],
            ['PAR007', 'Lucy Njeri', 'Female'], ['PAR008', 'Joseph Ochieng', 'Male'],
            ['PAR009', 'Agnes Chepkorir', 'Female'], ['PAR010', 'James Kinyua', 'Male'],
            ['PAR011', 'Mercy Akinyi', 'Female'], ['PAR012', 'Stephen Kipchirchir', 'Male'],
            ['PAR013', 'Peris Wangari', 'Female'], ['PAR014', 'Collins Otieno', 'Male'],
            ['PAR015', 'Rose Chebet', 'Female'], ['PAR016', 'Elijah Mutuku', 'Male'],
            ['PAR017', 'Faith Wairimu', 'Female'], ['PAR018', 'Kenneth Odhiambo', 'Male'],
            ['PAR019', 'Esther Wafula', 'Female'], ['PAR020', 'Vincent Kiplagat', 'Male'],
            ['PAR021', 'Caroline Nyambura', 'Female'], ['PAR022', 'George Onyango', 'Male'],
            ['PAR023', 'Ann Wambui', 'Female'], ['PAR024', 'Boniface Kiptoo', 'Male'],
            ['PAR025', 'Elizabeth Muthoni', 'Female'], ['PAR026', 'Simon Njoroge', 'Male'],
            ['PAR027', 'Juliana Adhiambo', 'Female'], ['PAR028', 'David Kamande', 'Male'],
            ['PAR029', 'Naomi Jepchirchir', 'Female'], ['PAR030', 'Amos Kipkorir', 'Male'],
        ];
        $now = now();
        foreach ($parents as $i => $p) {
            $id = DB::table('users')->insertGetId([
                'name' => $p[1],
                'username' => strtolower($p[0]),
                'email' => strtolower($p[0]) . '@bfjps.ac.ke',
                'phone' => '0700 000 ' . (100 + $i + 1),
                'gender' => $p[2],
                'address' => 'Machakos County, Kenya',
                'user_type' => 'parent',
                'code' => $p[0],
                'password' => Hash::make(Str::random(24)),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->parentIds[$p[0]] = $id;
        }
    }

    /***************************** STUDENTS *****************************/

    protected function seedStudents()
    {
        $students = [
            // code, name, gender, dob, grade, parent, previous school
            ['STU001', 'Kevin Otieno', 'Male', '2020-03-12', 'G1', 'PAR001', null],
            ['STU002', 'Diana Nyambura', 'Female', '2020-07-25', 'G1', 'PAR002', 'St Mary ECD Centre'],
            ['STU003', 'Brian Kiprotich', 'Male', '2020-01-18', 'G1', 'PAR003', null],
            ['STU004', 'Faith Achieng', 'Female', '2020-09-04', 'G1', 'PAR004', null],
            ['STU005', 'Abdul Hassan', 'Male', '2020-05-30', 'G1', 'PAR005', 'Green Valley ECD'],
            ['STU006', 'Winnie Chepkorir', 'Female', '2020-11-11', 'G1', 'PAR006', null],
            ['STU007', 'Marcus Ngugi', 'Male', '2020-02-20', 'G1', 'PAR007', null],
            ['STU008', 'Sharon Adhiambo', 'Female', '2020-08-15', 'G1', 'PAR008', null],
            ['STU009', 'Victor Kiptanui', 'Male', '2020-06-08', 'G1', 'PAR009', 'Machakos ECD Academy'],
            ['STU010', 'Miriam Wanjiru', 'Female', '2020-04-22', 'G1', 'PAR010', null],
            ['STU011', 'Amos Cheruiyot', 'Male', '2019-03-02', 'G2', 'PAR011', null],
            ['STU012', 'Cynthia Moraa', 'Female', '2019-07-19', 'G2', 'PAR012', 'St Mary ECD Centre'],
            ['STU013', 'Felix Kariuki', 'Male', '2019-01-27', 'G2', 'PAR013', null],
            ['STU014', 'Gladys Jepkorir', 'Female', '2019-10-09', 'G2', 'PAR014', null],
            ['STU015', 'Haron Odhiambo', 'Male', '2019-05-14', 'G2', 'PAR015', 'Green Valley ECD'],
            ['STU016', 'Irene Njoki', 'Female', '2019-12-03', 'G2', 'PAR016', null],
            ['STU017', 'Joshua Wafula', 'Male', '2019-02-11', 'G2', 'PAR017', null],
            ['STU018', 'Karen Muthoni', 'Female', '2019-08-29', 'G2', 'PAR018', null],
            ['STU019', 'Leonard Kipchoge', 'Male', '2019-04-06', 'G2', 'PAR019', 'Machakos ECD Academy'],
            ['STU020', 'Naomi Atieno', 'Female', '2019-09-21', 'G2', 'PAR020', null],
            ['STU021', 'Meshack Kimathi', 'Male', '2018-03-17', 'G3', 'PAR021', null],
            ['STU022', 'Nelly Mwende', 'Female', '2018-07-08', 'G3', 'PAR022', 'Green Valley Prep'],
            ['STU023', 'Oscar Rono', 'Male', '2018-01-30', 'G3', 'PAR023', null],
            ['STU024', 'Patricia Jepchumba', 'Female', '2018-10-25', 'G3', 'PAR024', null],
            ['STU025', 'Quincy Baraka', 'Male', '2018-05-19', 'G3', 'PAR025', 'Machakos Academy'],
            ['STU026', 'Ruth Chepngetich', 'Female', '2018-11-14', 'G3', 'PAR026', null],
            ['STU027', 'Samuel Mutiso', 'Male', '2018-02-06', 'G3', 'PAR027', null],
            ['STU028', 'Tabitha Wacera', 'Female', '2018-08-11', 'G3', 'PAR028', null],
            ['STU029', 'Uriel Kipngetich', 'Male', '2018-04-27', 'G3', 'PAR029', 'St Mary ECD Centre'],
            ['STU030', 'Vivian Auma', 'Female', '2018-09-09', 'G3', 'PAR030', null],
        ];
        $houses = ['Riverside', 'Highlands', 'Savanna'];
        $admittedByGrade = [
            'G1' => ['2026', '2026-01-05'],
            'G2' => ['2025', '2025-01-06'],
            'G3' => ['2024', '2024-01-08'],
        ];
        $now = now();
        foreach ($students as $i => $s) {
            $idx = (int)substr($s[0], 3) - 1;
            $grade = $s[4];
            $admNo = 'ADM' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            $userId = DB::table('users')->insertGetId([
                'name' => $s[1],
                'username' => strtolower($s[0]),
                'email' => strtolower($s[0]) . '@bfjps.ac.ke',
                'phone' => '0711 000 ' . (100 + $idx + 1),
                'dob' => $s[3],
                'gender' => $s[2],
                'address' => 'Machakos County, Kenya',
                'user_type' => 'student',
                'code' => $s[0],
                'password' => Hash::make(Str::random(24)),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->studentIds[$s[0]] = $userId;
            $this->studentAdmToUserId[$admNo] = $userId;
            $this->classStudents[$grade][] = $userId;

            DB::table('student_records')->insert([
                'user_id' => $userId,
                'my_class_id' => $this->classIds[$grade],
                'my_parent_id' => $this->parentIds[$s[5]],
                'adm_no' => $admNo,
                'session' => self::SESSION,
                'house' => $houses[$idx % 3],
                'age' => (int)Carbon::parse($s[3])->diffInYears(Carbon::parse('2026-11-27')),
                'year_admitted' => $admittedByGrade[$grade][0],
                'grad' => 0,
                'status' => 'active',
                'admission_date' => $admittedByGrade[$grade][1],
                'previous_school' => $s[6],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /***************************** STUDENT SUB-MODULES *****************************/

    protected function seedStudentModules()
    {
        $now = now();
        $adminId = 1;

        // Guardians
        foreach ($this->studentAdmToUserId as $admNo => $uid) {
            $idx = (int)substr($admNo, 3) - 1;
            $parCode = 'PAR' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            DB::table('student_guardians')->insert([
                'student_id' => $uid,
                'user_id' => $this->parentIds[$parCode],
                'relationship' => 'Parent',
                'is_primary' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Health
        foreach ($this->studentAdmToUserId as $admNo => $uid) {
            $idx = (int)substr($admNo, 3) - 1;
            $parCode = 'PAR' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT);
            $par = DB::table('users')->where('id', $this->parentIds[$parCode])->first();
            DB::table('student_health')->insert([
                'student_id' => $uid,
                'allergies' => $idx % 9 === 0 ? 'Peanuts' : null,
                'medical_conditions' => $idx === 13 ? 'Mild asthma' : null,
                'medications' => $idx === 13 ? 'Ventolin inhaler as needed' : null,
                'emergency_contact_name' => $par->name,
                'emergency_contact_phone' => $par->phone,
                'emergency_contact_relationship' => 'Parent/Guardian',
                'health_notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Attendance over the 15 school days listed
        $dates = [
            '2026-09-01', '2026-09-02', '2026-09-03', '2026-09-04', '2026-09-08',
            '2026-09-09', '2026-09-10', '2026-09-11', '2026-09-15', '2026-09-16',
            '2026-09-17', '2026-09-18', '2026-09-21', '2026-09-22', '2026-09-23',
        ];
        $overrides = [
            2 => [5 => ['absent', 'Sickness - note from clinic'], 17 => ['late', 'Traffic along Mwala road']],
            4 => [9 => ['absent', 'Family funeral']],
            7 => [12 => ['absent', 'Medical appointment']],
            9 => [28 => ['late', 'School bus breakdown']],
            11 => [24 => ['late', 'Late drop-off']],
            12 => [2 => ['absent', 'Migraine']],
            14 => [19 => ['late', 'Heavy rains']],
        ];
        $dateClasses = ['G1' => [], 'G2' => [], 'G3' => []];
        $rows = [];
        foreach ($this->classStudents as $grade => $ids) {
            foreach ($ids as $uid) {
                $dateClasses[$grade][$uid] = $this->classIds[$grade];
            }
        }
        foreach ($dates as $dIdx => $date) {
            foreach ($this->studentAdmToUserId as $admNo => $uid) {
                $grade = $this->gradeOfStudent($uid);
                $status = 'present';
                $note = null;
                if (isset($overrides[$dIdx][intval(substr($admNo, 3))])) {
                    list($status, $note) = $overrides[$dIdx][intval(substr($admNo, 3))];
                }
                $rows[] = [
                    'student_id' => $uid,
                    'class_id' => $dateClasses[$grade][$uid],
                    'date' => $date,
                    'status' => $status,
                    'note' => $note,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) { DB::table('student_attendance')->insert($chunk); }

        // Transport (approx 10 students)
        $transport = [
            ['STU003', 'ROU001', 'Matuu Junction', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
            ['STU005', 'ROU001', 'Katangi Market', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
            ['STU007', 'ROU001', 'Mwala Road', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
            ['STU012', 'ROU002', 'Kangundo Stage', 'KDA DEMO 02', 'Patrick Wambua', '0733 000 302'],
            ['STU015', 'ROU002', 'Kisukioni', 'KDA DEMO 02', 'Patrick Wambua', '0733 000 302'],
            ['STU018', 'ROU002', 'Matuu Boarding', 'KDA DEMO 02', 'Patrick Wambua', '0733 000 302'],
            ['STU019', 'ROU002', 'Ndalani', 'KDA DEMO 02', 'Patrick Wambua', '0733 000 302'],
            ['STU024', 'ROU003', 'Athi River', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
            ['STU027', 'ROU003', 'Sultan Hamud', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
            ['STU029', 'ROU003', 'Mavoko', 'KDA DEMO 01', 'Joseph Maina', '0722 000 301'],
        ];
        foreach ($transport as $t) {
            DB::table('student_transport')->insert([
                'student_id' => $this->studentIds[$t[0]],
                'route_name' => $t[1],
                'pickup_point' => $t[2],
                'vehicle_no' => $t[3],
                'driver_name' => $t[4],
                'driver_phone' => $t[5],
                'status' => 'active',
                'notes' => 'Morning and afternoon trips',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Discipline
        $discipline = [
            ['STU005', '2026-09-10', 'Chronic lateness', 'Lates recorded thrice in September', 'Commendation to punctuality roster', 'Minor'],
            ['STU017', '2026-09-15', 'Failure to complete homework', 'Mathematics homework not submitted twice', 'Parent notified and extra practice given', 'Warning'],
            ['STU028', '2026-09-21', 'Uniform violation', 'Wore non-regulation sweater to class', 'Verbal warning and uniform check', 'Minor'],
        ];
        foreach ($discipline as $d) {
            DB::table('student_discipline')->insert([
                'student_id' => $this->studentIds[$d[0]],
                'date' => $d[1],
                'incident_type' => $d[2],
                'description' => $d[3],
                'action_taken' => $d[4],
                'warning_level' => $d[5],
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Activities
        $activities = [
            ['STU001', 'Sport', 'Football Club', 'Captain', '2026', 'Best Goalkeeper - Inter-houses 2026'],
            ['STU004', 'Club', 'Music Club', 'Member', '2026', null],
            ['STU008', 'Club', 'Art & Craft Club', 'Member', '2026', 'Won 2nd Prize - County Art Fair'],
            ['STU012', 'Sport', 'Netball Team', 'Vice Captain', '2026', null],
            ['STU016', 'Club', 'Drama Club', 'Member', '2026', 'Lead role in term play'],
            ['STU021', 'Sport', 'Athletics', 'Runner', '2026', '1st Place - 100m heats'],
            ['STU025', 'Club', 'Environmental Club', 'Secretary', '2026', null],
            ['STU030', 'Sport', 'Games Day Volunteer', 'Helper', '2026', null],
        ];
        foreach ($activities as $a) {
            DB::table('student_activities')->insert([
                'student_id' => $this->studentIds[$a[0]],
                'activity_type' => $a[1],
                'activity_name' => $a[2],
                'role' => $a[3],
                'session' => $a[4],
                'achievements' => $a[5],
                'notes' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Documents (metadata only)
        $docs = [
            ['STU001', 'Birth Certificate', 'Birth Certificate', 'uploads/demo/birth_cert_stu001.pdf'],
            ['STU002', 'Admission Letter', 'Admission Letter', 'uploads/demo/admission_stu002.pdf'],
            ['STU011', 'Medical Report', 'Medical Report', 'uploads/demo/medical_stu011.pdf'],
            ['STU014', 'Transfer Certificate', 'Transfer Certificate', 'uploads/demo/transfer_stu014.pdf'],
            ['STU021', 'Birth Certificate', 'Birth Certificate', 'uploads/demo/birth_cert_stu021.pdf'],
            ['STU025', 'Report Card - Term 1 2026', 'Report Card', 'uploads/demo/report_stu025.pdf'],
            ['STU030', 'Birth Certificate', 'Birth Certificate', 'uploads/demo/birth_cert_stu030.pdf'],
        ];
        foreach ($docs as $d) {
            DB::table('student_documents')->insert([
                'student_id' => $this->studentIds[$d[0]],
                'title' => $d[1],
                'doc_type' => $d[2],
                'file_path' => $d[3],
                'notes' => 'Demo file - metadata only',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // History events
        $events = [
            ['STU001', 'admission', 'Student admitted to Grade 1 (2026)'],
            ['STU001', 'guardian', 'Guardian linked: Jane Otieno (Parent)'],
            ['STU002', 'document', 'Document uploaded: Admission Letter'],
            ['STU011', 'health', 'Health information updated'],
            ['STU014', 'document', 'Document uploaded: Transfer Certificate'],
            ['STU017', 'discipline', 'Discipline record added: Failure to complete homework'],
            ['STU021', 'activity', 'Activity added: Athletics'],
            ['STU025', 'activity', 'Activity added: Environmental Club'],
        ];
        foreach ($events as $e) {
            DB::table('student_events')->insert([
                'student_id' => $this->studentIds[$e[0]],
                'event_type' => $e[1],
                'description' => $e[2],
                'meta' => null,
                'recorded_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    protected function gradeOfStudent($uid)
    {
        foreach ($this->classStudents as $grade => $ids) {
            if (in_array($uid, $ids, true)) { return $grade; }
        }
        return 'G1';
    }

    /***************************** CURRICULUM *****************************/

    protected function seedCurriculum()
    {
        $now = now();
        $topics = [
            'English' => ['Greetings and Daily Routines', 'Listening and Speaking', 'Reading Fluency', 'Writing Simple Sentences', 'Phonics and Spelling'],
            'Kiswahili' => ['Kusikiliza na Kuzungumza', 'Kusoma', 'Kuandika', 'Msamiati', 'Hadithi na Mashairi'],
            'Mathematics' => ['Counting and Number Recognition', 'Addition and Subtraction', 'Shapes and Measurement', 'Patterns', 'Money and Time'],
            'Environmental Activities' => ['Our School Environment', 'Weather and Seasons', 'Plants and Animals', 'Personal Hygiene', 'Safety at Home and School'],
            'Creative Activities' => ['Drawing and Painting', 'Clay Modelling', 'Music and Movement', 'Craft Making', 'Role Play'],
            'Religious Education' => ['Creation and Thankfulness', 'Good Values at Home', 'Kindness and Sharing', 'Stories from the Bible', 'Prayers and Songs'],
            'Physical and Movement Education' => ['Locomotor Skills', 'Ball Handling Games', 'Balance and Coordination', 'Relay Races', 'Warm-Up and Cool-Down'],
        ];

        foreach (['G1', 'G2', 'G3'] as $cls) {
            $curriculumId = DB::table('curriculums')->insertGetId([
                'name' => $cls . ' - Term 3 2026 Curriculum',
                'program' => 'CBC Junior Primary',
                'academic_year_id' => $this->acYearId,
                'academic_term_id' => $this->acTermId,
                'status' => 'active',
                'description' => 'Competency Based Curriculum learning areas for Term 3 (' . self::SESSION . ') - ' . $cls,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($this->subjectIdsPerClass[$cls] as $subName => $subjectId) {
                DB::table('curriculum_subject')->insert([
                    'curriculum_id' => $curriculumId,
                    'subject_id' => $subjectId,
                    'my_class_id' => $this->classIds[$cls],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($topics[$subName] as $t) {
                    DB::table('curriculum_topics')->insert([
                        'curriculum_id' => $curriculumId,
                        'subject_id' => $subjectId,
                        'my_class_id' => $this->classIds[$cls],
                        'topic' => $t,
                        'learning_objectives' => 'Learner can apply ' . $subName . ' skills for ' . $t . '.',
                        'term' => '3',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /***************************** LESSONS *****************************/

    protected function seedLessons()
    {
        $now = now();
        $plan = [
            'G1' => [
                ['English', '2026-09-02', 'Greetings and Daily Routines'],
                ['Mathematics', '2026-09-03', 'Counting and Number Recognition'],
                ['Kiswahili', '2026-09-04', 'Kusikiliza na Kuzungumza'],
                ['Environmental Activities', '2026-09-08', 'Our School Environment'],
                ['Creative Activities', '2026-09-09', 'Drawing and Painting'],
            ],
            'G2' => [
                ['English', '2026-09-02', 'Reading Fluency'],
                ['Mathematics', '2026-09-03', 'Addition and Subtraction'],
                ['Kiswahili', '2026-09-04', 'Kusoma'],
                ['Religious Education', '2026-09-08', 'Good Values at Home'],
                ['Physical and Movement Education', '2026-09-09', 'Ball Handling Games'],
            ],
            'G3' => [
                ['English', '2026-09-02', 'Writing Simple Sentences'],
                ['Mathematics', '2026-09-03', 'Shapes and Measurement'],
                ['Kiswahili', '2026-09-04', 'Kuandika'],
                ['Environmental Activities', '2026-09-08', 'Weather and Seasons'],
                ['Creative Activities', '2026-09-09', 'Clay Modelling'],
            ],
        ];
        foreach ($plan as $cls => $lessons) {
            $classId = $this->classIds[$cls];
            foreach ($lessons as $l) {
                $subjectId = $this->subjectIdsPerClass[$cls][$l[0]];
                $subject = DB::table('subjects')->where('id', $subjectId)->first();
                DB::table('lessons')->insert([
                    'teacher_id' => $subject->teacher_id,
                    'subject_id' => $subjectId,
                    'my_class_id' => $classId,
                    'lesson_date' => $l[1],
                    'topic' => $l[2],
                    'plan' => 'Starter, main activity and plenary for ' . $l[2] . '.',
                    'learning_objectives' => 'By the end of the lesson the learner should be able to demonstrate ' . $l[2] . '.',
                    'teaching_notes' => 'Use group work and learner demonstrations.',
                    'academic_year_id' => $this->acYearId,
                    'academic_term_id' => $this->acTermId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /***************************** ASSIGNMENTS *****************************/

    protected function seedAssignments()
    {
        $now = now();
        $plan = [
            'G1' => [
                ['English', 'My Family Story', 'Write at least three simple sentences about your family.', '2026-09-12', 100],
                ['Mathematics', 'Counting Practice', 'Count objects at home and record numbers from 1 to 50.', '2026-09-18', 50],
                ['Kiswahili', 'Kuandika Maneno', 'Andika maneno kumi unayojifunza darasani.', '2026-09-25', 50],
            ],
            'G2' => [
                ['English', 'My Best Friend', 'Describe your best friend in five sentences.', '2026-09-14', 100],
                ['Mathematics', 'Adding and Taking Away', 'Solve 10 addition and 10 subtraction problems.', '2026-09-21', 100],
                ['Environmental Activities', 'My Community Helper', 'Draw and name three helpers in your community.', '2026-09-28', 50],
            ],
            'G3' => [
                ['English', 'A Day at the Market', 'Write a short paragraph about a trip to the market.', '2026-09-16', 100],
                ['Mathematics', 'Shapes Around Us', 'Identify and draw shapes you see at home.', '2026-09-23', 50],
                ['Creative Activities', 'My Favourite Song', 'Perform and describe your favourite song.', '2026-09-30', 50],
            ],
        ];
        foreach ($plan as $cls => $assignments) {
            $classId = $this->classIds[$cls];
            foreach ($assignments as $a) {
                $subjectId = $this->subjectIdsPerClass[$cls][$a[0]];
                $subject = DB::table('subjects')->where('id', $subjectId)->first();
                $assignmentId = DB::table('assignments')->insertGetId([
                    'teacher_id' => $subject->teacher_id,
                    'subject_id' => $subjectId,
                    'my_class_id' => $classId,
                    'title' => $a[1],
                    'instructions' => $a[2],
                    'due_date' => $a[3],
                    'max_marks' => $a[4],
                    'academic_year_id' => $this->acYearId,
                    'academic_term_id' => $this->acTermId,
                    'status' => 'open',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                if (in_array($cls . $a[1], ['G1My Family Story', 'G2My Best Friend', 'G3A Day at the Market'])) {
                    $students = array_slice($this->classStudents[$cls], 0, 5);
                    foreach ($students as $stuIdx => $uid) {
                        DB::table('assignment_submissions')->insert([
                            'assignment_id' => $assignmentId,
                            'student_id' => $uid,
                            'submission_text' => 'Work submitted by learner in class.',
                            'status' => 1,
                            'submitted_at' => Carbon::parse('2026-09-10')->addDays($stuIdx),
                            'marks' => $stuIdx < 3 ? mt_rand(70, 95) : null,
                            'feedback' => $stuIdx < 3 ? 'Well done, keep it up!' : null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    /***************************** EXAMS *****************************/

    protected function seedExams()
    {
        $now = now();
        $exams = [
            ['Term 3 Examination', 'End of Term Examination', '2026-11-16', '2026-11-20'],
        ];
        foreach ($exams as $e) {
            DB::table('exams')->insert([
                'name' => $e[0],
                'type' => $e[1],
                'start_date' => $e[2],
                'end_date' => $e[3],
                'term' => 3,
                'year' => self::SESSION,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /***************************** MARKS & EXAM RECORDS *****************************/

    protected function seedMarksAndRecords()
    {
        $now = now();
        $exams = Exam::where('year', self::SESSION)->orderBy('id')->get();
        $admNoByUser = array_flip($this->studentAdmToUserId);

        foreach ($exams as $exam) {
            foreach (['G1', 'G2', 'G3'] as $cls) {
                $classId = $this->classIds[$cls];
                $students = $this->classStudents[$cls];
                $subjectTotals = []; // subjectId => [uid => total]
                $markRows = [];

                foreach ($this->subjectIdsPerClass[$cls] as $subName => $subjectId) {
                    $perStudent = [];
                    foreach ($students as $uid) {
                        $t1 = mt_rand(9, 15);
                        $t2 = mt_rand(9, 15);
                        $tca = $t1 + $t2;
                        $exm = mt_rand(33, 65);
                        $total = $tca + $exm;
                        $perStudent[$uid] = ['t1' => $t1, 't2' => $t2, 'tca' => $tca, 'exm' => $exm, 'total' => $total];
                        $subjectTotals[$subjectId][$uid] = $total;
                    }
                    arsort($perStudent);
                    $rank = 1;
                    $subPositions = [];
                    foreach ($perStudent as $uid => $dummy) {
                        $subPositions[$uid] = $rank;
                        $rank++;
                    }

                    foreach ($students as $uid) {
                        $ps = $perStudent[$uid];
                        $markRows[] = [
                            'student_id' => $uid,
                            'subject_id' => $subjectId,
                            'my_class_id' => $classId,
                            'exam_id' => $exam->id,
                            't1' => $ps['t1'],
                            't2' => $ps['t2'],
                            'tca' => $ps['tca'],
                            'exm' => $ps['exm'],
                            'tex3' => $ps['total'],
                            'sub_pos' => $subPositions[$uid],
                            'grade_id' => $this->gradeFor($ps['total']),
                            'year' => self::SESSION,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                foreach (array_chunk($markRows, 200) as $chunk) { DB::table('marks')->insert($chunk); }

                // Exam records
                $studentTotals = [];
                foreach ($students as $uid) {
                    $sum = 0;
                    foreach ($subjectTotals as $st) { $sum += $st[$uid]; }
                    $studentTotals[$uid] = $sum;
                }
                arsort($studentTotals);
                $aveSum = 0;
                foreach ($studentTotals as $uid => $total) {
                    $aveSum += round($total / 7, 1);
                }
                $classAve = round($aveSum / count($studentTotals), 1);
                $pos = 1;
                $comments = [
                    1 => 'Outstanding performance across all learning areas. Keep shining!',
                    2 => 'Excellent effort this term. Continue the good work.',
                    3 => 'Very good improvement. Aim for the top next term.',
                    4 => 'Good performance. Pay attention to the weaker areas.',
                    5 => 'Fair result; consistent revision will push you higher.',
                    6 => 'Keep practising daily. You can do better with focus.',
                    7 => 'Needs more effort in some subjects. Extra practice advised.',
                    8 => 'Work closely with your teacher on your weak subjects.',
                    9 => 'A lot of improvement is needed. Attend additional sessions.',
                    10 => 'Please seek help early next term. Persistent effort required.',
                ];
                foreach ($studentTotals as $uid => $total) {
                    $ave = round($total / 7, 1);
                    $key = min(10, max(1, $pos));
                    DB::table('exam_records')->insert([
                        'exam_id' => $exam->id,
                        'student_id' => $uid,
                        'my_class_id' => $classId,
                        'total' => $total,
                        'ave' => $ave,
                        'class_ave' => $classAve,
                        'pos' => $pos,
                        'p_comment' => $comments[$key],
                        't_comment' => 'Consistent classwork and homework effort observed.',
                        'year' => self::SESSION,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $pos++;
                }
            }
        }
    }

    protected function gradeFor($total)
    {
        foreach ($this->grades as $g) {
            if ($total >= $g->mark_from && $total <= $g->mark_to) { return $g->id; }
        }
        return null;
    }

    /***************************** FINANCE *****************************/

    protected function seedFinance()
    {
        $now = now();

        // Fee types
        $feeTypes = [
            ['Tuition', 15000],
            ['Activity Fee', 2000],
            ['Learning Materials', 1500],
            ['Meals', 3000],
            ['Assessment/Exam Fee', 1000],
            ['Transport', 4000],
        ];
        foreach ($feeTypes as $ft) {
            $this->feeTypeIds[$ft[0]] = DB::table('fee_types')->insertGetId([
                'name' => $ft[0],
                'description' => 'Term 3 2026 fee line',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Fee structures (same amounts for the three classes)
        $structures = []; // cls => feeType => id
        foreach (['G1', 'G2', 'G3'] as $cls) {
            foreach ($feeTypes as $ft) {
                $structures[$cls][$ft[0]] = DB::table('fee_structures')->insertGetId([
                    'fee_type_id' => $this->feeTypeIds[$ft[0]],
                    'my_class_id' => $this->classIds[$cls],
                    'session' => self::SESSION,
                    'term' => self::TERM,
                    'amount' => $ft[1],
                    'is_active' => true,
                    'description' => 'Term 3 (' . self::SESSION . ') - ' . $cls . ' ' . $ft[0],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Student fees: one line per student per structure
        $studentFees = []; // cls => [uid][feeType] = studentFeeId
        foreach (['G1', 'G2', 'G3'] as $cls) {
            foreach ($this->classStudents[$cls] as $uid) {
                foreach ($feeTypes as $ft) {
                    $sf = DB::table('student_fees')->insertGetId([
                        'student_id' => $uid,
                        'fee_structure_id' => $structures[$cls][$ft[0]],
                        'amount_due' => $ft[1],
                        'discount' => 0,
                        'amount_paid' => 0,
                        'balance' => $ft[1],
                        'due_date' => '2026-08-31',
                        'status' => 'unpaid',
                        'session' => self::SESSION,
                        'term' => self::TERM,
                        'year' => self::SESSION,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $studentFees[$cls][$uid][$ft[0]] = $sf;
                }
            }
        }

        // Discounts
        $discounts = [
            [11, 'Tuition', 'scholarship', 4000, 'OVC scholarship - Masomo ya Pamoja'],
            [12, 'Tuition', 'scholarship', 4000, 'OVC scholarship - Masomo ya Pamoja'],
            [13, 'Meals', 'discount', 2000, 'Staff child discount'],
            [14, 'Learning Materials', 'waiver', 1500, 'Financial hardship waiver'],
            [23, 'Tuition', 'scholarship', 4000, 'County Bursary Allocation'],
            [25, 'Tuition', 'discount', 2000, 'Sibling discount (2 children enrolled)'],
        ];
        $this->applyDiscounts($discounts, $studentFees);

        // Payments plan (student no => [feeType, amount, date, method, reference])
        $paymentsPlan = [
            1 => [['Tuition', 15000, '2026-09-02', 'mobile_money', 'DEMO-MPESA-0001']],
            2 => [['Tuition', 15000, '2026-09-02', 'mobile_money', 'DEMO-MPESA-0002']],
            3 => [['Tuition', 15000, '2026-09-03', 'cash', 'DEMO-CASH-0003']],
            4 => [['Tuition', 15000, '2026-09-03', 'mobile_money', 'DEMO-MPESA-0004']],
            5 => [['Tuition', 15000, '2026-09-04', 'bank', 'DEMO-BANK-0005']],
            6 => [['Tuition', 15000, '2026-09-04', 'mobile_money', 'DEMO-MPESA-0006']],
            7 => [['Tuition', 10000, '2026-09-07', 'mobile_money', 'DEMO-MPESA-0007'], ['Activity Fee', 2000, '2026-09-07', 'mobile_money', 'DEMO-MPESA-0008']],
            8 => [['Tuition', 10000, '2026-09-08', 'cash', 'DEMO-CASH-0009'], ['Activity Fee', 2000, '2026-09-08', 'cash', 'DEMO-CASH-0010']],
            9 => [['Tuition', 15000, '2026-09-09', 'card', 'DEMO-CARD-0011'], ['Meals', 3000, '2026-09-09', 'card', 'DEMO-CARD-0012']],
            10 => [['Tuition', 15000, '2026-09-10', 'bank', 'DEMO-BANK-0013'], ['Meals', 3000, '2026-09-10', 'bank', 'DEMO-BANK-0014']],
            11 => [['Tuition', 8000, '2026-09-11', 'mobile_money', 'DEMO-MPESA-0015']],
            12 => [['Tuition', 8000, '2026-09-11', 'mobile_money', 'DEMO-MPESA-0016']],
            13 => [['Tuition', 8000, '2026-09-14', 'cash', 'DEMO-CASH-0017']],
            14 => [['Tuition', 8000, '2026-09-14', 'mobile_money', 'DEMO-MPESA-0018']],
            15 => [['Tuition', 15000, '2026-09-15', 'bank', 'DEMO-BANK-0019'], ['Activity Fee', 2000, '2026-09-15', 'bank', 'DEMO-BANK-0020'], ['Learning Materials', 1500, '2026-09-15', 'bank', 'DEMO-BANK-0021'], ['Meals', 3000, '2026-09-15', 'mobile_money', 'DEMO-MPESA-0022'], ['Assessment/Exam Fee', 1000, '2026-09-15', 'mobile_money', 'DEMO-MPESA-0023'], ['Transport', 4000, '2026-09-15', 'mobile_money', 'DEMO-MPESA-0024']],
            16 => [['Tuition', 15000, '2026-09-16', 'bank', 'DEMO-BANK-0025'], ['Activity Fee', 2000, '2026-09-16', 'bank', 'DEMO-BANK-0026'], ['Learning Materials', 1500, '2026-09-16', 'bank', 'DEMO-BANK-0027'], ['Meals', 3000, '2026-09-16', 'mobile_money', 'DEMO-MPESA-0028'], ['Assessment/Exam Fee', 1000, '2026-09-16', 'mobile_money', 'DEMO-MPESA-0029'], ['Transport', 4000, '2026-09-16', 'mobile_money', 'DEMO-MPESA-0030']],
            17 => [['Tuition', 15000, '2026-09-17', 'bank', 'DEMO-BANK-0031'], ['Transport', 4000, '2026-09-17', 'bank', 'DEMO-BANK-0032']],
            18 => [['Tuition', 15000, '2026-09-18', 'cash', 'DEMO-CASH-0033'], ['Meals', 3000, '2026-09-18', 'cash', 'DEMO-CASH-0034']],
            19 => [['Meals', 3000, '2026-09-21', 'mobile_money', 'DEMO-MPESA-0035']],
            20 => [['Meals', 3000, '2026-09-21', 'mobile_money', 'DEMO-MPESA-0036']],
            21 => [['Activity Fee', 2000, '2026-09-22', 'cash', 'DEMO-CASH-0037'], ['Assessment/Exam Fee', 1000, '2026-09-22', 'cash', 'DEMO-CASH-0038']],
            22 => [['Activity Fee', 2000, '2026-09-23', 'mobile_money', 'DEMO-MPESA-0039'], ['Assessment/Exam Fee', 1000, '2026-09-23', 'mobile_money', 'DEMO-MPESA-0040']],
        ];

        $accountByMethod = ['cash' => 'Main Cash', 'bank' => 'Main Bank Account', 'mobile_money' => 'Mobile Money', 'card' => 'Main Bank Account'];

        // Ensure the default finance accounts exist (idempotent)
        $existingAccounts = DB::table('finance_accounts')->pluck('id', 'name')->all();
        foreach ([
            ['Main Cash', 'cash'],
            ['Main Bank Account', 'bank'],
            ['Mobile Money', 'mobile_money'],
        ] as [$accName, $accType]) {
            if (!isset($existingAccounts[$accName])) {
                DB::table('finance_accounts')->insert([
                    'name' => $accName,
                    'type' => $accType,
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $accounts = DB::table('finance_accounts')->get()->keyBy('name');

        $paymentRows = [];
        $receiptRows = [];
        $txRows = [];
        $balances = [];
        foreach ($accounts as $name => $acc) { $balances[$acc->name] = 0; }

        $paymentNo = 0;
        $receiptNo = 0;
        $byAdm = $this->studentAdmToUserId;

        foreach ($paymentsPlan as $stuNo => $payments) {
            $uid = $this->studentIds['STU' . str_pad((string)$stuNo, 3, '0', STR_PAD_LEFT)];
            $cls = $this->gradeOfStudent($uid);

            foreach ($payments as $p) {
                $accountName = $accountByMethod[$p[3]];
                $accountId = $accounts[$accountName]->id;
                $sfId = $studentFees[$cls][$uid][$p[0]];

                $paymentRows[] = [
                    'student_id' => $uid,
                    'student_fee_id' => $sfId,
                    'amount' => $p[1],
                    'payment_date' => $p[2],
                    'payment_method' => $p[3],
                    'reference_no' => $p[4],
                    'received_by' => 'MySchool Admin',
                    'notes' => 'Term 3 (' . self::SESSION . ') fee payment',
                    'status' => 'completed',
                    'finance_account_id' => $accountId,
                    'session' => self::SESSION,
                    'term' => self::TERM,
                    'year' => self::SESSION,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $paymentNo++;

                // Keep student fee in sync
                $sfRow = DB::table('student_fees')->where('id', $sfId)->first();
                $newPaid = $sfRow->amount_paid + $p[1];
                $newBalance = max(0, $sfRow->amount_due - $sfRow->discount - $newPaid);
                DB::table('student_fees')->where('id', $sfId)->update([
                    'amount_paid' => $newPaid,
                    'balance' => $newBalance,
                    'status' => $newBalance <= 0 ? 'paid' : 'partial',
                ]);

                // Receipt for most payments
                if ($receiptNo < 25) {
                    $receiptNo++;
                    $receiptRows[] = [
                        'receipt_no' => 'RCT-2026-' . str_pad((string)$receiptNo, 4, '0', STR_PAD_LEFT),
                        'finance_payment_id' => 0,
                        'student_id' => $uid,
                        'amount' => $p[1],
                        'payment_method' => $p[3],
                        'reference_no' => $p[4],
                        'balance_after' => $newBalance,
                        'session' => self::SESSION,
                        'term' => self::TERM,
                        'year' => self::SESSION,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $txRows[] = [
                    'finance_account_id' => $accountId,
                    'type' => 'income',
                    'amount' => $p[1],
                    'description' => 'Payment ' . $p[4],
                    'transaction_date' => $p[2],
                    'reference_no' => $p[4],
                    'related_to' => 'StudentFee#' . $sfId,
                    'session' => self::SESSION,
                    'year' => self::SESSION,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $balances[$accountName] += $p[1];
            }
        }

        // Insert payment rows, then fix receipt finance_payment_id via matching reference + date
        foreach (array_chunk($paymentRows, 100) as $chunk) {
            DB::table('finance_payments')->insert($chunk);
        }
        $savedPayments = DB::table('finance_payments')->where('year', self::SESSION)->orderBy('id')->get();
        $paymentRefs = [];
        foreach ($savedPayments as $sp) {
            $paymentRefs[$sp->reference_no] = $sp->id;
        }
        foreach ($receiptRows as $i => $row) {
            $receiptRows[$i]['finance_payment_id'] = $paymentRefs[$row['reference_no']];
        }
        foreach (array_chunk($receiptRows, 100) as $chunk) {
            DB::table('finance_receipts')->insert($chunk);
        }
        foreach (array_chunk($txRows, 100) as $chunk) {
            DB::table('finance_transactions')->insert($chunk);
        }

        // Refunds
        $refunds = [
            [19, 'Meals', 'DEMO-MPESA-0035', 3000, '2026-10-05', 'Overpayment rolled over to next term'],
            [21, 'Activity Fee', 'DEMO-CASH-0037', 2000, '2026-10-06', 'Duplicate payment refunded'],
        ];
        foreach ($refunds as $r) {
            $uid = $this->studentIds['STU' . str_pad((string)$r[0], 3, '0', STR_PAD_LEFT)];
            $cls = $this->gradeOfStudent($uid);
            $sfId = $studentFees[$cls][$uid][$r[1]];
            $paymentId = $paymentRefs[$r[2]];
            $payment = DB::table('finance_payments')->where('id', $paymentId)->first();
            $accountName = $accountByMethod[$payment->payment_method];

            DB::table('refunds')->insert([
                'finance_payment_id' => $paymentId,
                'student_id' => $uid,
                'amount' => $r[3],
                'reason' => $r[5],
                'processed_by' => 'MySchool Admin',
                'status' => 'completed',
                'refund_date' => $r[4],
                'session' => self::SESSION,
                'term' => self::TERM,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sfRow = DB::table('student_fees')->where('id', $sfId)->first();
            $newPaid = max(0, $sfRow->amount_paid - $r[3]);
            $newBalance = max(0, $sfRow->amount_due - $sfRow->discount - $newPaid);
            $newStatus = $newBalance <= 0 ? 'paid' : ($newPaid > 0 ? 'partial' : 'unpaid');
            DB::table('student_fees')->where('id', $sfId)->update([
                'amount_paid' => $newPaid,
                'balance' => $newBalance,
                'status' => $newStatus,
            ]);

            DB::table('finance_transactions')->insert([
                'finance_account_id' => $payment->finance_account_id,
                'type' => 'refund',
                'amount' => $r[3],
                'description' => 'Refund ' . ($payment->reference_no ?: ''),
                'transaction_date' => $r[4],
                'reference_no' => $payment->reference_no,
                'related_to' => 'FinancePayment#' . $paymentId,
                'session' => self::SESSION,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $balances[$accountName] -= $r[3];
        }

        // Flag overdue lines for a few students with no payments
        foreach ([24, 26, 28] as $stuNo) {
            $uid = $this->studentIds['STU' . str_pad((string)$stuNo, 3, '0', STR_PAD_LEFT)];
            $cls = $this->gradeOfStudent($uid);
            foreach (array_keys($studentFees[$cls][$uid]) as $ft) {
                DB::table('student_fees')
                    ->where('id', $studentFees[$cls][$uid][$ft])
                    ->where('status', 'unpaid')
                    ->update(['status' => 'overdue']);
            }
        }

        // Expenses
        $expenseCategories = [
            ['Salaries'], ['Food'], ['Electricity'], ['Water'], ['Transport'], ['Maintenance'], ['Supplies'], ['Other'],
        ];
        $catIds = [];
        foreach ($expenseCategories as $c) {
            $catIds[$c[0]] = DB::table('expense_categories')->insertGetId([
                'name' => $c[0], 'description' => $c[0] . ' category',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $suppliers = [
            ['Twiga Fresh Foods Ltd', 'Mary Kilonzo', '0712 500 401', 'supplies@twigafresh.co.ke', 'Nairobi'],
            ['Machakos Office Supplies', 'Peter Ndolo', '0723 500 402', 'orders@machakosoffice.co.ke', 'Machakos Town'],
            ['Solar Grid Energy PLC', 'Grace Auma', '0734 500 403', 'billing@solargrid.co.ke', 'Nairobi'],
            ['Mwema Transporters Ltd', 'James Mwema', '0745 500 404', 'logistics@mwematransport.co.ke', 'Machakos'],
        ];
        foreach ($suppliers as $s) {
            DB::table('suppliers')->insert([
                'name' => $s[0], 'contact_person' => $s[1], 'phone' => $s[2],
                'email' => $s[3], 'address' => $s[4], 'total_owed' => 0, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $supplierRows = DB::table('suppliers')->get();

        $expenses = [
            ['Salaries', 'Staff payroll - Term 3 teachers stipend', 85000, '2026-09-30', 'cash', 'DEMO-EXP-0001', 'Staff Payroll', null],
            ['Food', 'Lunch groceries - week 1', 18000, '2026-09-12', 'mobile_money', 'DEMO-EXP-0002', null, 0],
            ['Electricity', 'Power bill - August 2026', 6500, '2026-09-10', 'bank', 'DEMO-EXP-0003', null, 2],
            ['Water', 'County water bill - September', 3200, '2026-09-18', 'cash', 'DEMO-EXP-0004', null, null],
            ['Maintenance', 'Classroom repainting', 15000, '2026-09-15', 'cash', 'DEMO-EXP-0005', 'Athi River Builders', null],
            ['Supplies', 'Stationery and exercise books', 24500, '2026-09-20', 'bank', 'DEMO-EXP-0006', null, 1],
            ['Transport', 'School bus fuel', 9000, '2026-09-25', 'mobile_money', 'DEMO-EXP-0007', null, 3],
            ['Supplies', 'Chalk and marker pens', 3800, '2026-09-05', 'cash', 'DEMO-EXP-0008', null, null],
            ['Food', 'Milk programme - September', 12000, '2026-09-16', 'mobile_money', 'DEMO-EXP-0009', null, 0],
            ['Electricity', 'Power bill - September 2026', 6800, '2026-10-10', 'bank', 'DEMO-EXP-0010', null, 2],
            ['Maintenance', 'Borehole pump repair', 11000, '2026-09-22', 'cash', 'DEMO-EXP-0011', 'Athi River Builders', null],
            ['Transport', 'Bus servicing', 14500, '2026-10-02', 'bank', 'DEMO-EXP-0012', null, 3],
            ['Salaries', 'Support staff wages', 28000, '2026-10-31', 'cash', 'DEMO-EXP-0013', 'Staff Payroll', null],
            ['Supplies', 'Exam papers printing', 9500, '2026-11-12', 'cash', 'DEMO-EXP-0014', null, 1],
            ['Water', 'Water tank refill', 4100, '2026-11-05', 'cash', 'DEMO-EXP-0015', null, null],
        ];
        $supplierByName = [];
        foreach ($supplierRows as $s) { $supplierByName[$s->name] = $s->id; }
        foreach ($expenses as $i => $e) {
            $accountName = $accountByMethod[$e[4]];
            $expense = DB::table('expenses')->insertGetId([
                'expense_category_id' => $catIds[$e[0]],
                'supplier_id' => isset($e[6], $supplierByName[$e[6]]) ? $supplierByName[$e[6]] : null,
                'finance_account_id' => $accounts[$accountName]->id,
                'description' => $e[1],
                'amount' => $e[2],
                'expense_date' => $e[3],
                'payee' => $e[6],
                'payment_method' => $e[4],
                'reference_no' => $e[5],
                'receipt_document' => null,
                'status' => 'completed',
                'recorded_by' => 'MySchool Admin',
                'session' => self::SESSION,
                'term' => self::TERM,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            DB::table('finance_transactions')->insert([
                'finance_account_id' => $accounts[$accountName]->id,
                'type' => 'expense',
                'amount' => $e[2],
                'description' => $e[1],
                'transaction_date' => $e[3],
                'reference_no' => $e[5],
                'related_to' => 'Expense#' . $expense,
                'session' => self::SESSION,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $balances[$accountName] -= $e[2];
        }

        // Persist account balances
        foreach ($balances as $name => $bal) {
            DB::table('finance_accounts')->where('name', $name)->update(['current_balance' => $bal]);
        }
    }

    protected function applyDiscounts($discounts, &$studentFees)
    {
        $now = now();
        foreach ($discounts as $d) {
            $stuNo = $d[0];
            $ft = $d[1];
            $uid = $this->studentIds['STU' . str_pad((string)$stuNo, 3, '0', STR_PAD_LEFT)];
            $cls = $this->gradeOfStudent($uid);
            $sfId = $studentFees[$cls][$uid][$ft];

            DB::table('discounts')->insert([
                'student_id' => $uid,
                'student_fee_id' => $sfId,
                'type' => $d[2],
                'amount' => $d[3],
                'reason' => $d[4],
                'approved_by' => 'MySchool Admin',
                'status' => 'active',
                'session' => self::SESSION,
                'term' => self::TERM,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sfRow = DB::table('student_fees')->where('id', $sfId)->first();
            $newDiscount = $sfRow->discount + $d[3];
            $balance = max(0, $sfRow->amount_due - $newDiscount - $sfRow->amount_paid);
            $newStatus = $sfRow->amount_paid > 0 ? ($balance <= 0 ? 'paid' : 'partial') : ($balance <= 0 ? 'paid' : 'unpaid');
            DB::table('student_fees')->where('id', $sfId)->update([
                'discount' => $newDiscount,
                'balance' => $balance,
                'status' => $newStatus,
            ]);
        }
    }

    /***************************** LIBRARY *****************************/

    protected function seedLibrary()
    {
        $now = now();
        $books = [
            ['Bravo English Activity Book 1', 'English', 'Jane Njoroge', 'Textbook', 'Library - Shelf A', 15, 'G1'],
            ['Bravo English Activity Book 2', 'English', 'Jane Njoroge', 'Textbook', 'Library - Shelf A', 15, 'G2'],
            ['Bravo English Activity Book 3', 'English', 'Jane Njoroge', 'Textbook', 'Library - Shelf A', 15, 'G3'],
            ['Spotlight Kiswahili 1', 'Kiswahili', 'Hassan Ali', 'Textbook', 'Library - Shelf B', 12, 'G1'],
            ['Spotlight Kiswahili 2', 'Kiswahili', 'Hassan Ali', 'Textbook', 'Library - Shelf B', 12, 'G2'],
            ['Spotlight Kiswahili 3', 'Kiswahili', 'Hassan Ali', 'Textbook', 'Library - Shelf B', 12, 'G3'],
            ['New Progressive Primary Mathematics 1', 'Mathematics', 'Karoli Mwangi', 'Textbook', 'Library - Shelf C', 18, 'G1'],
            ['New Progressive Primary Mathematics 2', 'Mathematics', 'Karoli Mwangi', 'Textbook', 'Library - Shelf C', 18, 'G2'],
            ['New Progressive Primary Mathematics 3', 'Mathematics', 'Karoli Mwangi', 'Textbook', 'Library - Shelf C', 18, 'G3'],
            ['Environmental Activities Pupil Book 1', 'Environmental', 'Eunice Keter', 'Textbook', 'Library - Shelf D', 10, 'G1'],
            ['Environmental Activities Pupil Book 2', 'Environmental', 'Eunice Keter', 'Textbook', 'Library - Shelf D', 10, 'G2'],
            ['Environmental Activities Pupil Book 3', 'Environmental', 'Eunice Keter', 'Textbook', 'Library - Shelf D', 10, 'G3'],
            ['Creative Arts & Crafts 1', 'Creative', 'Winnie Ochieng', 'Activity Book', 'Library - Shelf E', 14, 'G1'],
            ['Creative Arts & Crafts 2', 'Creative', 'Winnie Ochieng', 'Activity Book', 'Library - Shelf E', 14, 'G2'],
            ['Creative Arts & Crafts 3', 'Creative', 'Winnie Ochieng', 'Activity Book', 'Library - Shelf E', 14, 'G3'],
            ['Christian Religious Education 1', 'Religious', 'Sister Anna Mwikali', 'Textbook', 'Library - Shelf F', 8, 'G1'],
            ['Christian Religious Education 3', 'Religious', 'Sister Anna Mwikali', 'Textbook', 'Library - Shelf F', 8, 'G3'],
            ['PE & Movement Skills 1', 'PE', 'Samson Kiplangat', 'Textbook', 'Library - Shelf G', 9, 'G1'],
            ['PE & Movement Skills 2', 'PE', 'Samson Kiplangat', 'Textbook', 'Library - Shelf G', 9, 'G2'],
            ['PE & Movement Skills 3', 'PE', 'Samson Kiplangat', 'Textbook', 'Library - Shelf G', 9, 'G3'],
            ['The Clever Crocodile and Other Stories', null, 'Wangari Karanja', 'Storybook', 'Library - Story Corner', 20, null],
            ['My First English Picture Dictionary', null, 'Lucy Njeri', 'Reference', 'Library - Reference', 6, null],
            ['Tales from the Kenyan Savanna', null, 'Omega Publishers', 'Storybook', 'Library - Story Corner', 18, null],
            ['Journey through the Rift Valley', null, 'Omega Publishers', 'Storybook', 'Library - Story Corner', 12, null],
            ['Junior Science Explorer', null, 'Francis Gakuru', 'Reference', 'Library - Reference', 5, null],
            ['ABC of Healthy Living', null, 'Ministry of Health', 'Reference', 'Library - Reference', 7, null],
            ['Count with Me: Numbers Wall Book', null, 'Kariuki & Sons', 'Activity Book', 'Library - Shelf C', 10, null],
            ['African Safari Colouring Book', null, 'ArtEdge Publishers', 'Storybook', 'Library - Story Corner', 16, null],
            ['Our Heroes and Heroines', null, 'Pending Shadows', 'Storybook', 'Library - Story Corner', 11, null],
            ['World of Insects for Young Learners', null, 'Natural Kenya', 'Reference', 'Library - Reference', 6, null],
        ];
        $bookIds = [];
        foreach ($books as $b) {
            $total = $b[5];
            $issued = mt_rand(2, min(8, $total));
            $bookIds[] = DB::table('books')->insertGetId([
                'name' => $b[0],
                'my_class_id' => $b[6] ? $this->classIds[$b[6]] : null,
                'description' => $b[1] ? $b[1] . ' learning area title' : 'General reading title',
                'author' => $b[2],
                'book_type' => $b[3],
                'url' => null,
                'location' => $b[4],
                'total_copies' => $total,
                'issued_copies' => $issued,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Book requests / borrowings
        $borrows = [
            [22, 1, '2026-09-02', '2026-09-16', 1, 'returned'],
            [23, 1, '2026-09-02', '2026-09-16', 1, 'returned'],
            [24, 4, '2026-09-03', '2026-09-17', 1, 'returned'],
            [22, 6, '2026-09-03', '2026-09-24', 0, 'issued'],
            [25, 8, '2026-09-04', '2026-09-18', 1, 'returned'],
            [26, 9, '2026-09-04', '2026-09-18', 0, 'issued'],
            [27, 10, '2026-09-08', '2026-09-22', 1, 'returned'],
            [28, 13, '2026-09-08', '2026-09-22', 0, 'issued'],
            [29, 16, '2026-09-09', '2026-09-23', 1, 'returned'],
            [30, 18, '2026-09-09', '2026-09-23', 0, 'issued'],
            [21, 20, '2026-09-10', '2026-09-24', 1, 'returned'],
            [2, 20, '2026-09-10', '2026-09-24', 1, 'returned'],
            [3, 21, '2026-09-11', '2026-09-25', 1, 'returned'],
            [4, 22, '2026-09-11', '2026-09-25', 0, 'issued'],
            [5, 23, '2026-09-14', '2026-09-28', 1, 'returned'],
            [6, 24, '2026-09-14', '2026-09-28', 0, 'issued'],
            [7, 25, '2026-09-15', '2026-09-29', 1, 'returned'],
            [8, 26, '2026-09-15', '2026-09-29', 0, 'issued'],
            [9, 27, '2026-09-16', '2026-09-30', 1, 'returned'],
            [10, 28, '2026-09-16', '2026-09-30', 0, 'issued'],
        ];
        foreach ($borrows as $br) {
            $uid = $this->studentIds['STU' . str_pad((string)$br[0], 3, '0', STR_PAD_LEFT)];
            DB::table('book_requests')->insert([
                'book_id' => $bookIds[$br[1] - 1],
                'user_id' => $uid,
                'start_date' => $br[2],
                'end_date' => $br[3],
                'returned' => $br[4],
                'status' => $br[5],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /***************************** TIMETABLES *****************************/

    protected function seedTimetables()
    {
        $now = now();
        $slotData = [
            [8, '00', 'AM', 8, '40', 'AM'],
            [8, '40', 'AM', 9, '20', 'AM'],
            [9, '30', 'AM', 10, '10', 'AM'],
            [10, '30', 'AM', 11, '10', 'AM'],
            [11, '20', 'AM', 12, '00', 'PM'],
            [12, '00', 'PM', 12, '40', 'PM'],
        ];

        foreach (['G1', 'G2', 'G3'] as $cls) {
            $classId = $this->classIds[$cls];
            $ttrId = DB::table('time_table_records')->insertGetId([
                'name' => $cls . ' Timetable - Term 3 ' . self::SESSION,
                'my_class_id' => $classId,
                'year' => self::SESSION,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $slotIds = [];
            foreach ($slotData as $s) {
                $timeFrom = $s[0] . ':' . $s[1] . ' ' . $s[2];
                $timeTo = $s[3] . ':' . $s[4] . ' ' . $s[5];
                $slotIds[] = DB::table('time_slots')->insertGetId([
                    'ttr_id' => $ttrId,
                    'hour_from' => $s[0],
                    'min_from' => $s[1],
                    'meridian_from' => $s[2],
                    'hour_to' => $s[3],
                    'min_to' => $s[4],
                    'meridian_to' => $s[5],
                    'time_from' => $timeFrom,
                    'time_to' => $timeTo,
                    'timestamp_from' => strtotime($timeFrom),
                    'timestamp_to' => strtotime($timeTo),
                    'full' => $timeFrom . ' - ' . $timeTo,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $subjects = array_values($this->subjectIdsPerClass[$cls]);
            $days = [
                ['Monday', 1], ['Tuesday', 2], ['Wednesday', 3], ['Thursday', 4], ['Friday', 5],
            ];
            foreach ($days as $d) {
                foreach ($slotIds as $pIdx => $slotId) {
                    $subjectId = $subjects[($d[1] + $pIdx) % count($subjects)];
                    $timeFrom = $slotData[$pIdx][0] . ':' . $slotData[$pIdx][1] . ' ' . $slotData[$pIdx][2];
                    DB::table('time_tables')->insert([
                        'ttr_id' => $ttrId,
                        'ts_id' => $slotId,
                        'subject_id' => $subjectId,
                        'timestamp_from' => strtotime($d[0] . ' ' . $timeFrom),
                        'timestamp_to' => strtotime($d[0] . ' ' . $timeFrom . '+40 minutes'),
                        'day' => $d[0],
                        'day_num' => $d[1],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /***************************** PINS *****************************/

    protected function seedPins()
    {
        $now = now();
        $rows = [];
        foreach (array_values($this->studentIds) as $i => $uid) {
            $rows[] = [
                'code' => 'BFJPS-2026-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT),
                'used' => $i < 6 ? '1' : '0',
                'times_used' => $i < 6 ? '1' : '0',
                'user_id' => null,
                'student_id' => $uid,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('pins')->insert($rows);
    }

    /***************************** SUMMARY *****************************/

    protected function summary()
    {
        $tables = [
            'users', 'student_records', 'staff_records', 'my_classes', 'subjects', 'classes' => 'my_classes',
            'teacher_assignments', 'exams', 'marks', 'exam_records', 'curriculums', 'curriculum_topics',
            'lessons', 'assignments', 'assignment_submissions', 'student_guardians', 'student_attendance',
            'student_health', 'student_transport', 'student_discipline', 'student_activities',
            'student_events', 'student_documents', 'fee_types', 'fee_structures', 'student_fees',
            'finance_payments', 'finance_receipts', 'discounts', 'refunds', 'expense_categories',
            'suppliers', 'expenses', 'finance_transactions', 'books', 'book_requests',
            'time_table_records', 'time_slots', 'time_tables', 'pins',
        ];
        $this->warn('');
        $this->warn('========== BFJPS DEMO DATA SUMMARY ==========');
        foreach ($tables as $table) {
            $real = is_int($table) ? $table : $table;
            $this->line('  ' . $real . ': ' . DB::table($real)->count());
        }
        $this->warn('Settings: current_session=' . \App\Models\Setting::where('type', 'current_session')->value('description')
            . ' | current_term=' . \App\Models\Setting::where('type', 'current_term')->value('description'));
        $this->warn('=============================================');
    }

    protected function warn($msg) { echo $msg . PHP_EOL; }
    protected function line($msg) { echo $msg . PHP_EOL; }
}