# Unified CI3 Musical College Management – Audit & Schema Blueprint

## Current Codebases (audit)
- `../management` (CI3): Super admin/owner portal with Google OAuth, colleges CRUD, departments, courses sharing, questions/tests bank, dashboard reporting. Uses MySQL DB `management` (tables prefixed via constants like `TABLE_COLLEGE`, `TABLE_COURCES`, `TABLE_STUDENT` etc.).
- `../college` (CI3): Faculty + student APIs with roles (principal, HOD, staff) and student portal endpoints. Includes courses/modules/tests, question bank, reports, groups, student auth/session, CORS headers, OneCompiler user provisioning.
- `D:\Drillu\application` (React): Student portal UI consuming `/app/:collegeId/...` style routes; pages for login/register, dashboard, courses/modules, tests/start/submit/results, labs, placements, profile header, error pages. Uses config `SERVICE_URL=/app`, dynamic `:collegeId`, guards, and expects APIs provided by `college` backend.

## Keep vs. Drop vs. Add
- Keep/merge from `management`: owner auth (OAuth), colleges CRUD, course sharing, departments, admin dashboards, questions/tests authoring, CSV tooling.
- Keep/merge from `college`: principal/HOD/staff portals, student auth/session, course/module/test consumption, reports, groups, logo/banner uploads, OneCompiler integration.
- Drop/trim: duplicate default CI `Welcome` scaffolding, unused `Install.ppp`, placeholder pages.
- Add (musical-specific): instrument inventory (catalog, availability), issue/return workflow, maintenance logs; course levels (Beginner/Intermediate/Advanced); class schedule/batches; musical staff roles; dashboards for instrument availability/performance.

## Target Roles & Panels
- Super Admin (owner) – manage colleges, global courses, question bank, share courses.
- Faculty (principal/HOD/staff) – manage students/staff, assign courses/modules/tests, view reports, handle inventory.
- Student – enroll, view courses/modules, take tests, view results, request/hold instruments.

## Schema Blueprint (MySQL 8)
- `users` (id, email, password_hash, role enum: owner|principal|hod|staff|student, profile fields, college_id nullable, status, created_at/updated_at).
- `roles`/`permissions` (if finer RBAC needed) and `role_permissions`.
- `colleges` (from existing) + add `music_specialty` optional.
- `departments` (existing) mapped to instruments/genres.
- `staff` (id, user_id FK, role tag, department_id, joining_date, status).
- `students` (id, user_id FK, registration_number, department_id, batch_id, level enum, expire_date, progress meta).
- `courses` (reuse) + fields `level` enum(Beginner|Intermediate|Advanced), `mode`, `instrument_focus`, `capacity`.
- `course_modules` (reuse) for modules/lessons; `module_schedules` (id, module_id FK, teacher_id FK, start_at, end_at, room, recurrence).
- `batches` (id, course_id, name, schedule_text, start_date, end_date).
- `enrollments` (student_id, course_id, batch_id nullable, status, enrolled_at).
- `tests`/`questions`/`answer_options`/`test_submissions` (reuse from dump).
- `progress_logs` (student_id, course_id, module_id, status, score, notes, recorded_at).
- `instruments` (id, name, category, serial_no, condition, purchase_date, location, availability_status enum: available|issued|maintenance|retired, notes).
- `instrument_transactions` (id, instrument_id FK, issued_to_student_id FK nullable, issued_to_staff_id FK nullable, issued_by_staff_id FK, issue_date, due_date, return_date, condition_on_issue, condition_on_return, remarks, status enum: issued|returned|overdue).
- `instrument_maintenance` (id, instrument_id FK, type, description, status, cost, started_at, completed_at, technician, next_due_date).
- `reports_cache` (id, scope, payload JSON, generated_at) for dashboard snapshots if needed.
- `notifications` (id, user_id, type, payload JSON, read_at, created_at) for overdue instruments/tests.

## Routing/Modules (proposed unification)
- Admin routes keep `OAuth` → dashboard; mount admin controllers under `/admin/*` to avoid clash with faculty/student.
- Faculty/Student keep `(:college)/...` patterns (from `college/routes.php`) to serve student React rewrite; align controllers to new namespace (e.g., `Faculty/`, `Student/`).
- API base `/app/:collegeId/...` compatible with Drillu React paths during rewrite to CI3 views.

## Migration/Imports
- Use `../management/drillu.sql` as base: migrate to MySQL 8, add missing indexes/FKs.
- Script to import existing data (colleges, courses, questions, tests, students, staff) before adding new musical tables.
- Seed data: default owner, sample college, sample courses, starter instruments, demo users per role.

## Immediate Next Steps
- Lift `management` app into `CMMS` as admin module; relocate admin controllers to `/admin` namespace and update routes.
- Bring `college` controllers/models for faculty/student under `/faculty`/`/student` namespaces; preserve API signatures used by React until CI3 views are ready.
- Define migrations for new tables (instruments, batches/schedules, progress logs, notifications).
- Replace React portal with CI3 student views after API parity.

