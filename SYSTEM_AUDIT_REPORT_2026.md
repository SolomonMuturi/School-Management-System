# LAVSMS — COMPLETE SYSTEM LINKING & RELATIONSHIP AUDIT

**Date:** 5 Sep 2026 · **Stack:** Laravel 8.40 / PHP 8.0.30 / MySQL (`school_management`)
**Method:** inventory of routes + controllers + migrations + models + views; targeted SQL integrity queries; runtime smoke harness rendering 100+ controller methods for all roles; FormRequest-validated POST round-trips; `artisan view:cache`, `route:list`, `migrate:status`.

---

## A. Working Relationships (verified OK)

| Module | Controller → Route names → Views | Role gates |
|---|---|---|
| Home/Dashboard | `HomeController@dashboard` → `home` | `auth` |
| Users & Roles | `UserController` (index/dashboard/roles/permissions/show/edit/reset_pass/toggleStatus) → `users.*` | `teamSA` (reset/destroy/roles-edit = `super_admin`) |
| Classes | `MyClassController` (index/dashboard/show/edit/toggleStatus) → `classes.*` | `teamSA`, `super_admin` for destroy |
| Subjects | `SubjectController` (index/dashboard/show/edit/toggleStatus) → `subjects.*` | `teamSA`/`super_admin` |
| Exams | `ExamController` (index/dashboard/show/edit) → `exams.*` | `teamSAT` |
| Grades | `GradeController` → `grades.*` | `teamSAT` |
| Students | `StudentRecordController`, `PromotionController` → `students.*`, `students.promotion*` | `teamSAT` views, `teamSA` writes, `super_admin` destroy |
| Marks | `MarkController` (+ decode fix) → `marks.*` | `teamSAT` |
| PINs / Exam Lock | `PinController` (+ decode fix) → `pins.*` | `teamSA` manage; `pin verify/enter` public-ish (lock-gated) |
| System Settings | `SuperAdmin\SettingController` → `settings` / `settings.update` | `super_admin` |
| My Account (all roles) | `MyAccountController` → `my_account.*` + `logout` | `auth` |
| Academics (years/curriculum/classes/assign/lessons/homework/report cards/performance/reports) | `AcademicController` → `academic.*` | `teamAcademic` (+ `teamSAT` on assignments) |
| Finance | `FinanceController` → `finance.*` | `teamAccount` |
| Timetables | `TimeTableController` → `tt.*` / `ttr.*` / `ts.*` | `teamSA` writes |
| Parent portal | `MyParent\MyController` → `parent.*` (children, fees, testimonials, home) | parent/guardian roles |

- Sidebar (`partials/menu`) matches gates: Administrator group (Users/Finance/System Settings/PINs) for `teamSA`; Finance top-level only for `teamAccount`.
- DB integrity: **0** orphan `student_records.user_id`, **0** duplicate `user_id`; 37 student records ↔ 37 `users` of type `student`; 1 current academic year, 1 current term (seq 1 = First).
- `view:cache` compiled cleanly; `route:list` shows **no duplicate route names**; all migrations ran (`migrate:status` all **Yes**, incl. `..._000600` batch 8).
- Runtime smoke: **all pages rendered OK** for admin (classes, subjects, exams, grades, marks, pins, users, settings, my account, timetables), finance (13 pages incl. statement/billing), academics (11), students (all 37 student profiles + sub-pages), promotion, parent portal, and student marksheet.

## B. Broken Relationships Found & Fixed

1. **Marks year selection 405 / hash-of-hash** — student menu and bulk view called `marks.year_select` (undefined) and `year_selected` re-hashed an already-hashed id. Fixed view routes + removed double-hash.
2. **Mark/PIN pages crashed on raw numeric ids** — `Qs::decodeHash()` on a plain int. Added `decodeStudentId()` guard in base `Controller`; applied in `MarkController` (year_selector/year_selected/show/print_view) and `PinController` (enter_pin/verify).
3. **Curriculum subject route mismatch** — view used `academic.curriculum.subjects.store`; route is `academic.curriculum.subject.store`. Fixed.
4. **Promotion reset/history crash** — views referenced removed `p->fs`/`p->ts` columns; `promotions` actually has `from_class/to_class/from_session/to_session`. Views fixed.
5. **Academic session/term split-brain** — `AcademicRepo` only wrote `academic_years`/`academic_terms` while System Settings held `current_session`. Now single source of truth: settings `current_session` + new `current_term`; `setCurrentYear()`/`setCurrentTerm()` sync both. Verified round-trip.
6. **PIN list view crash on empty student codes** — null-guarded `getSRByUserID()` before rendering hashed id.
7. **`users.reset_pass` silently did nothing** — view sends hashed id but `UserController::reset_pass` never decoded it → updated user id 0/nowhere. Decode added (mirrors `StudentRecordController@reset_pass`).
8. **Users list/dashboard/show crash on seed data** — seeded `users.created_at`/`last_login` are NULL; views called `->format()` unguarded. Guarded all date columns (users.index/dashboard/show, my_account, finance statement/receipt/accounts).
9. **`create_fks.php` blocked fresh installs** — phantom FK blocks for `payments`, `payment_records`, `receipts` (tables never created). Removed.
10. **Wrong model columns** — `StudentRecord` fillable dropped `wd`/`wd_date`; `ExamRecord` dropped `af_id`/`ps_id`.

## C. Missing / Incomplete (not built this pass)

- **Library module is orphaned stubs**: `BookController`/`BookRequestController`, `App\Models\Book`/`BookRequest` (wrong namespace under `app/Models/`), no routes, librarian menu unwired. Migrations (`books`, `book_requests`) exist and are empty. ***Recommended: implement a minimal Library module or remove the stubs.***
- **Foreign keys**: finance/academic/student-module tables (batch ≥ 2) define relationships only at the ORM level; no DB-level FK constraints (original design). Not added to avoid destructive `change()`/dbal work — flagged as recommended future hardening.
- **Results publication** has no explicit "approve/release" step beyond the exam-lock PIN flow (faithful to the original LAVSMS design).

## D. Duplicates

- **None.** No duplicate student records (unique `student_records.user_id` enforced via new migration `..._000600`, engine-verified). Duplicate stub classes (`Book`/`BookRequest`) exist only as dead code (see C).

## E. Sections

- `sections` table **dropped** (`..._000200`); confirmed absent. `dorms` dropped. No view/controller/route residual references sections; dashboard sidebar carries no Sections link; student record & marks carry no `section_id`.

## F. Academic Session & Term — Single Source of Truth

- System Settings (`settings` table): `current_session = 2026-2027`, `current_term = 1` (this is now authoritative).
- `AcademicRepo@setCurrentYear` ⇒ updates `academic_years.is_current` + `settings.current_session`; `setCurrentTerm` ⇒ `academic_terms.is_current` + `settings.current_term`. Round-trip verified (2 → 1).
- `SettingUpdate` validates `current_session` against `^\d{4}-\d{4}$`; settings POST with valid values executed successfully via real FormRequest validation.

## G. Security & Integrity

- Hash ids: reads/writes on marks, pins, students, users consistently decode hashed ids; raw numeric ids accepted safely through `decodeStudentId()`.
- `users.reset_pass`, `students.reset_pass`, `UserController@destroy` refuse changes to the head admin (`Qs::headSA`).
- `users.status`/`classes.status`/`subjects.status`/`classes.assign_teacher`/finance bill/statement consistently use raw ids matching their controllers.
- `User` model casts `last_login` to `datetime`; login-listener still writes it.
- Exam-locked PIN gate verified: with `lock_exam = 1` a student must present a valid unused pin (`times_used < 6`); an invalid/duplicate pin redirects with danger; MIDDLEWARE correctly redirects to dashboard when lock is off.

## H. Finance Relationships

- Billing → per-student statement and receipt; `FinanceController@studentBill`/`statement` keyed on raw `user_id` (matches routes/views). All 13 finance pages render.
- Fee structure → fee type link renders in billing; statement shows discounts+payments. No orphans (tables empty).

## I. Exams / Marks / Pins

- Exam lock + PIN flow fully exercised end-to-end (see G). Marks pages render; `marks.year_selector` for a student with no marks redirects without error; `decodeStudentId` round-trips hash↔raw.

## J. Verdict

### ✅ PASS WITH WARNINGS

The system's routes, controllers, views, roles, and database are internally consistent; the single-source academic session/term change and all discovered and fixed relationships are verified. Warnings (non-blocking, see C): Library module is orphaned stubs, DB-level FKs are absent by design, and results publication is intentionally lock-based rather than approval-based.

---

**Verification evidence:** `smoke_audit.php` (~150 checks) — ALL PASSED; `php artisan view:cache` clean; `php artisan route:list` no dupes; `php artisan migrate:status` all migrated; SQL integrity queries — 0 dups / 0 orphans.