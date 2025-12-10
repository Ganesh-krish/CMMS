## QA / Migration Checklist

- Run migrations (timestamp `20251210190000`) after setting DB credentials in `application/config/database.php`:
  - CLI: `$ php index.php migrate` or load Migration library and call `latest()`.
  - Verify new tables: `batches`, `module_schedules`, `enrollments`, `progress_logs`, `instruments`, `instrument_transactions`, `instrument_maintenance`, `notifications`.
  - Confirm columns added to `courses` (`level`, `instrument_focus`, `capacity`).
- Smoke tests
  - Owner login (OAuth) → `/Dashboard`.
  - Faculty login `/{$college}/login/faculty` (principal/HOD/staff) and verify access to courses/tests/groups.
  - Student portal: `/student-portal/{college}/login` → dashboard shows active courses.
  - Inventory endpoints:
    - `/{college}/inventory` list.
    - `/{college}/inventory/create` → create instrument.
    - `/{college}/inventory/issue` and `.../return`.
    - `/{college}/inventory/maintenance`.
  - Batches & schedules:
    - `/{college}/batches/{courseId}` list.
    - `/{college}/batches/create` and `.../schedules/add`.
  - Reports KPIs: `/{college}/report/kpis` returns counts (students/staff/courses/batches/instruments).
- Config
  - Set `base_url`, `college_url`, `student_url` in `application/config/config.php`.
  - Configure `ONE_COMPILER_API_KEY` env for student code runner.
  - Update Google OAuth client if needed in `application/config/constants.php`.


