# BANTAY — Admin/Teacher Login (Laravel)

This package contains the files needed to add a role-based login (Admin & Teacher)
to a Laravel project, styled after the "Welcome back" login screen using the
BANTAY green color palette. It ships with one working **Admin account** and a
protected **Admin Dashboard** route.

## 1. Create a fresh Laravel project (skip if you already have one)

```bash
composer create-project laravel/laravel bantay
cd bantay
```

Set up your `.env` database connection (MySQL/SQLite) and run:

```bash
php artisan key:generate
```

## 2. Copy these files into your project

Copy the entire contents of this package into your Laravel project root,
keeping the same folder paths (overwrite/merge where a file already exists):

```
app/Models/User.php
app/Models/Student.php
app/Models/Section.php
app/Models/Announcement.php
app/Models/ActivityLog.php
app/Mail/AnnouncementMail.php
app/Http/Controllers/Auth/AuthenticatedSessionController.php
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Admin/SectionController.php
app/Http/Controllers/Admin/TeacherController.php
app/Http/Controllers/Admin/StudentController.php
app/Http/Controllers/Admin/AnnouncementController.php
app/Http/Controllers/Teacher/DashboardController.php
app/Http/Middleware/EnsureUserHasRole.php
bootstrap/app.php                                  (merge the middleware alias line)
routes/web.php
resources/views/layouts/guest.blade.php
resources/views/layouts/admin.blade.php
resources/views/auth/login.blade.php
resources/views/admin/dashboard.blade.php
resources/views/admin/sections/index.blade.php
resources/views/admin/teachers/register.blade.php
resources/views/admin/teachers/assign.blade.php
resources/views/admin/students/create.blade.php
resources/views/admin/announcements/index.blade.php
resources/views/admin/announcements/create.blade.php
resources/views/emails/announcement.blade.php
resources/views/teacher/dashboard.blade.php
database/migrations/2024_01_01_000001_add_role_to_users_table.php
database/migrations/2024_01_01_000002_add_assignment_columns_to_users_table.php
database/migrations/2024_01_01_000003_create_students_table.php
database/migrations/2024_01_01_000004_create_announcements_table.php
database/migrations/2024_01_01_000005_add_profile_fields_to_users_table.php
database/migrations/2024_01_01_000006_create_sections_table.php
database/migrations/2024_01_01_000007_add_profile_fields_to_students_table.php
database/migrations/2024_01_01_000008_add_birth_date_to_students_table.php
database/migrations/2024_01_01_000009_create_activity_logs_table.php
database/seeders/DatabaseSeeder.php
```

> Note: `resources/views/admin/announcements/email.blade.php` has been
> **removed** — "Post Announcement" and "Email Announcement" are now the same
> single step (see below). If you have an old copy of that file in your
> project, delete it along with its old routes.

## 2b. Link storage (needed for profile picture uploads)

Teacher and student profile pictures are uploaded to `storage/app/public` and
served through the public disk symlink. Run this once per environment:

```bash
php artisan storage:link
```

## 3. Migrate & seed the admin account

```bash
php artisan migrate
php artisan db:seed
```

### Sending real announcement emails over SMTP (required for the "Post Announcement" feature)

Posting an announcement now **automatically emails it** to every registered
teacher (or admin+teacher, depending on the audience you pick) using the
email address they were registered with — there is nothing extra to type in
per announcement. You just need real SMTP credentials in `.env` once:

**Gmail (App Password) example:**

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youraccount@gmail.com
MAIL_PASSWORD=your16digitapppassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="youraccount@gmail.com"
MAIL_FROM_NAME="BANTAY"
```

(Gmail requires an **App Password**, not your normal login password — enable
2-Step Verification on the Google account, then generate one under
Google Account → Security → App passwords.)

**Mailtrap (free sandbox inbox, good for local testing) example:**

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="no-reply@bantay.test"
MAIL_FROM_NAME="BANTAY"
```

Emails are sent synchronously (no queue worker required) — if SMTP isn't
configured yet, the announcement still posts to the board and you'll see a
"could not be sent" notice instead of a failed request.

This creates the default admin account:

| Field    | Value                     |
|----------|---------------------------|
| Email    | admin@bantay.test         |
| Password | Admin@12345                |
| Role     | admin                     |

**Change this password immediately after your first login** (or edit the
seeder before running it).

## 4. Run it

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000/login`.

- Logging in with the admin account redirects to `/admin/dashboard`.
- A teacher account (role = `teacher`) would redirect to `/teacher/dashboard`.
  You can create teacher accounts later from the admin dashboard, or manually:

```bash
php artisan tinker
>>> \App\Models\User::create([
      'name' => 'Jane Teacher',
      'email' => 'teacher@bantay.test',
      'password' => bcrypt('Teacher@12345'),
      'role' => 'teacher',
    ]);
```

## Admin dashboard features

The admin dashboard (`/admin/dashboard`) has a left icon sidebar with:

| Icon | Page | Route |
|---|---|---|
| Dashboard | Overview / stats / real admin activity log | `/admin/dashboard` |
| Sections | Create sections per grade level (7–12) with a student capacity | `/admin/sections` |
| Register Teacher | Create a teacher login (admin-only) | `/admin/teachers/register` |
| Assign Teacher | Set a teacher's grade level, section & subject (Edit → Save per row) | `/admin/teachers/assign` |
| Add Student | Enroll a student, with birthday/age + Edit modal | `/admin/students/create` |
| Announcement | Post an announcement — posts to the board **and** emails it via SMTP in one step | `/admin/announcements` |

All of these are protected by `role:admin` middleware — a teacher account
will get a 403 if they try to visit them directly.

### Sections must be created first

Grade level + section dropdowns on "Assign Teacher" and "Add Student" are
populated from the `sections` table — create sections there first (grade
level 7–12, section name, optional student capacity). A section that hits
its capacity is shown as "Full" and can't be selected when adding a student.

### Assign Teacher — Edit buttons + duplicate-subject warning

Each row on "Assign Teacher" starts **locked (read-only)**. Click **Edit** to
unlock the grade level / section / subject fields for that teacher, then
**Save**. If you assign a teacher to a grade+section+subject that's already
covered by another teacher, the save still goes through (in case you really
do want a co-teacher), but you'll get an orange warning banner naming the
conflicting teacher, and it's recorded in the notification bell / Recent
Activity log so it isn't missed later.

### Add Student — Edit button + birthday/age

The student list next to the "Add Student" form now has an **Edit** button
per row that opens a modal pre-filled with that student's details (including
birthday), so you can update them without leaving the page. Birthday is
optional; age is computed automatically (both live in the form as you type,
and in the "Recently Added" table) — there's no separate "age" field to type
in and get out of sync.

### Notifications = a real admin activity log

Every create/edit/assign/delete an admin does (registering a teacher,
assigning them, adding/editing a student, creating/deleting a section,
posting an announcement) is written to the new `activity_logs` table via
`App\Models\ActivityLog::record(...)`. This powers two things:

- The 🔔 **notification bell** in the top bar (click it) — the latest 8
  actions, newest first, with a red dot if there's a duplicate-subject
  warning to review.
- The **Recent Activity** panel on the dashboard — same data, more rows.

If you build more admin actions later, log them the same way, e.g.:

```php
ActivityLog::record('updated', 'Student', $student->id, auth()->user()->name." edited {$student->fullName()}'s record.");
```

### Teacher dashboard — compact

`/teacher/dashboard` is now a real (compact) dashboard instead of a
placeholder: a slim top bar, four small stat cards (section, subject,
student count, announcement count), an "My Assignment" summary card, and a
recent-announcements panel — all sized for quick scanning rather than a lot
of scrolling.

### Names and profile pictures

Teachers and students are entered as separate First / Middle / Last
name fields (middle name optional) instead of one "Full name" field, and
both forms accept an optional profile picture upload.

## Notes

- Only `admin` and `teacher` roles can sign in — the login controller rejects
  any other/unknown role.
- `EnsureUserHasRole` middleware protects `/admin/*` and `/teacher/*` route
  groups so teachers can't reach the admin dashboard and vice versa.
- The login page has no public "Sign up" — accounts are provisioned by an
  admin, matching how school attendance systems are usually managed.
- The dashboard's "Present/Absent today" numbers are placeholder estimates
  (see the comment in `DashboardController`) until you wire up a real
  attendance/scan table — swap that logic in when you build that feature.
- Colors used come from the BANTAY palette: `--dark:#152B0C`,
  `--dark2:#1B3812`, `--primary:#3F5A2A`, `--secondary:#8BC34A`,
  `--bg:#F3F6F2`.
