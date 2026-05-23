# Code Map

A catalogue of every controller, model, library, helper and view folder, grouped by feature area. Inheritance column shows which base class each controller extends (see [`ARCHITECTURE.md`](ARCHITECTURE.md) for what each base class enforces).

Active counts (excluding `bak.*` archived files):
- **77 controllers** in `application/controllers/`
- **71 models**  in `application/models/`
- **30 libraries** in `application/libraries/` (plus 8 SaaS gateway adapters under `Saas_gateways/`)
- **5 helpers** in `application/helpers/`

---

## Base / framework

| Controller | Extends | Purpose |
|---|---|---|
| `Errors` | `CI_Controller` | 404 / generic error pages. Routed via `$route['404_override'] = 'errors'`. |
| `Install` | `CI_Controller` | Web installer wizard. Walks through Envato purchase-key check → DB credentials → schema seed → admin user. Self-disables once `$config['installed'] = TRUE`. |
| `Ajax` | `MY_Controller` | Cross-feature AJAX endpoints (dropdown population, dependent selects, etc.). |
| `Cron_api` | `MY_Controller` | Web-callable cron-style endpoints (fee reminders, SMS, mail). Secured by `cron_secret_key` from `global_settings`. |
| `Popupbox` | `MY_Controller` | Dashboard popup notifications. |
| `System_update` | `Admin_Controller` | In-app updater for new releases (file replacement + DB migration). |
| `Backup` | `Admin_Controller` | mysqldump / restore from the super-admin UI; writes to `uploads/db_backup/`. |
| `Modules` | `Admin_Controller` | List installed addons (rows in `addon` table) and toggle their on/off status. Super-admin only. |
| `Settings` | `Admin_Controller` | Global settings (logo, app name, currency, smtp, etc.). |

## Auth & users

| Controller | Extends | Purpose |
|---|---|---|
| `Authentication` | `Authentication_Controller` | Login / logout / forgot password / role-based redirect. |
| `Profile` | `Admin_Controller` | "My profile" page for any logged-in staff role. |
| `Role` | `Admin_Controller` | Manage staff roles. |
| `Userrole` | `User_Controller` | Student/parent self-service profile + child linking. |
| `User_login_log` | `Admin_Controller` | Login audit trail. |

Helpers: `general_helper.php::is_loggedin / is_superadmin_loggedin / get_loggedin_branch_id / get_permission / access_denied / translate / ...`.

## Tenancy / branch / multi-school

| Controller | Extends | Purpose |
|---|---|---|
| `Branch` | `Admin_Controller` | Super-admin CRUD for branches (= tenants/schools). |
| `Custom_domain` | `Admin_Controller` | Manage the `custom_domain` lookup table — both super-admin and tenant admin views. |
| `School_settings` | `Admin_Controller` | Per-tenant settings (logo, contact, currency, social, payment toggles); writes to the `branch` row. |

Models: `Branch_model`, `Custom_domain_model`, `School_model`, `Application_model` (URL→branch resolver).

## SaaS (signup, packages, billing)

See [`SAAS.md`](SAAS.md) for the full surface area. Key files:

| Controller | Extends | Purpose |
|---|---|---|
| `Saas` | `Admin_Controller` | Super-admin hub: list tenants, approve/reject signups, manage packages, payment gateways, manual payments, audit log, run renewal cron. ~957 lines. |
| `Signup` | `MY_Controller` | Public self-service signup form → inserts into `saas_pending_request`. |
| `Landing` | `CI_Controller` | Public marketing page on apex `smartschool.bd`. Renders one of 5 variants from `landing_setting`. |
| `Landing_admin` | `Admin_Controller` | Super-admin editor for the landing page (copy / colour / variant). |
| `Subscription` | `Admin_Controller` | Tenant-facing: current plan, usage, upgrade CTA. |
| `Saas_billing` | `MY_Controller` | Tenant-facing pay flow: routes `/billing/pay/<inv>`, `/billing/start/<inv>/<provider>`, `/billing/success`, `/billing/fail`, `/billing/cancel`, `/billing/ipn/<provider>`, `/billing/manual`, `/billing/submit_manual`. |
| `Saas_renewals_cli` | `CI_Controller` | CLI entrypoint for the daily auto-renewal cron. Defers to `Saas_renewal_runner`. |
| `Sslcommerz` | (none — payment callback) | Legacy SSLCommerz callback handler kept for backwards-compat. |

Models: `Saas_model`, `Saas_setting_model`, `Saas_payment_gateway_model`, `Saas_manual_payment_submission_model`, `Saas_notification_channel_model`, `Tenant_provisioning_model`, `Landing_model`.

Libraries: `Saas_gateway` (factory), `Saas_renewal_runner`, and 8 provider adapters under `Saas_gateways/`:
`Saas_gateway_base.php`, `Saas_gateway_interface.php`, `Saas_manual_gateway.php`, `Saas_bkash_gateway.php`, `Saas_nagad_gateway.php`, `Saas_rocket_gateway.php`, `Saas_sslcommerz_gateway.php`, `Saas_stripe_gateway.php`, `Saas_paykureghor_gateway.php`.

Helper: `saas_notify_helper.php` — wraps email + (optional) SMS notifications for signup approval / rejection / invoice / renewal-reminder.

## Students, parents, enrolment

| Controller | Extends | Purpose |
|---|---|---|
| `Student` | `Admin_Controller` | Student CRUD, bulk import, photos, custom fields. ~1,215 lines. |
| `Parents` | `Admin_Controller` | Parent CRUD; linked to students via `parent_id` FK. |
| `Alumni` | `Admin_Controller` | Alumni records. |
| `Birthday` | `Admin_Controller` | Birthday dashboard widget data. |
| `Student_promotion` | `Admin_Controller` | Promote students to next class/session. |
| `Classes`, `Sections`, `Subject` | `Admin_Controller` | Academic structure CRUD. |
| `Multiclass` | `Admin_Controller` | Students enrolled in multiple classes (subject-wise). |
| `System_student_field`, `Custom_field` | `Admin_Controller` | Configure which student fields appear and add bespoke ones. |
| `Sessions` | `Admin_Controller` | Academic-year CRUD (DB table is `schoolyear`). |
| `Online_admission` | `Admin_Controller` | Public admission applications. |

Models: `Student_model`, `Parents_model`, `Alumni_model`, `Birthday_model`, `Classes_model`, `School_model`, `Subject_model`, `Multiclass_model`, `Custom_field_model`, `Student_fields_model`, `Online_admission_model`.

## Academics

| Controller | Extends | Purpose |
|---|---|---|
| `Attendance` | `Admin_Controller` | Daily attendance entry + reports. |
| `Attendance_period` | `Admin_Controller` | Period-wise attendance (multi-class). |
| `Timetable` | `Admin_Controller` | Class timetable. |
| `Homework` | `Admin_Controller` | Assign, submit, review homework. |
| `Exam` | `Admin_Controller` | Exam terms + schedule. ~821 lines. |
| `Exam_progress` | `Admin_Controller` | Mark entry per exam. |
| `Onlineexam` | `Admin_Controller` | Online exam authoring + delivery. ~899 lines. |
| `Onlineexam_payment` | `Admin_Controller` | Per-exam payment (separate from fees). ~1,209 lines. |
| `Marksheet_template` | `Admin_Controller` | Configure marksheet PDF layouts. |
| `Certificate` | `Admin_Controller` | Configure & issue student certificates. |
| `Card_manage` | `Admin_Controller` | ID-card / admit-card design and bulk print. |
| `Award` | `Admin_Controller` | Awards / achievements. |
| `Live_class` | `Admin_Controller` | Live-class scheduling — wraps BigBlueButton + Zoom libraries. |

## Finance — fees, payroll, accounting

| Controller | Extends | Purpose |
|---|---|---|
| `Fees` | `Admin_Controller` | Fees structure + assignment. ~1,248 lines. |
| `Feespayment` | `Admin_Controller` | Record fee payments; integrates with the configured payment gateway. ~1,315 lines. |
| `Admissionpayment` | `Frontend_Controller` | Public admission application payment. ~1,253 lines. |
| `Offline_payments` | `Admin_Controller` | Approve manually-submitted offline payments (SaaS only). |
| `Payroll` | `Admin_Controller` | Staff salary configuration. |
| `Advance_salary` | `Admin_Controller` | Advance-salary requests + ledger. |
| `Accounting` | `Admin_Controller` | Chart of accounts, income/expense entries, balance sheet exports. |

## Operations & resources

| Controller | Extends | Purpose |
|---|---|---|
| `Employee` | `Admin_Controller` | Staff CRUD. ~773 lines. |
| `Leave` | `Admin_Controller` | Staff leave requests + approval. |
| `Hostels` | `Admin_Controller` | Hostel buildings, rooms, allocation. |
| `Transport` | `Admin_Controller` | Routes, vehicles, fees. |
| `Library` | `Admin_Controller` | Library books, issue/return. |
| `Inventory` | `Admin_Controller` | Stock items, vendors, purchases, transactions. ~1,284 lines. |
| `Reception`, `Reception_config` | `Admin_Controller` | Visitor / postal / complaint logs at reception. |
| `Attachments` | `Admin_Controller` | Generic file attachment management. |

## Communication

| Controller | Extends | Purpose |
|---|---|---|
| `Communication` | `Admin_Controller` | Internal messaging / notice board. |
| `Sendsmsmail` | `Admin_Controller` | Bulk SMS / email sender (per-tenant SMS provider chosen from `payment_config`-style options). |
| `Event` | `Admin_Controller` | School events + invitations. |

Libraries: SMS providers — `Bulksmsbd`, `Clickatell`, `Custom_sms`, `Msg91`, `Smscountry`, `Textlocal`, `Twilio`. Live-class libraries — `Bigbluebutton_lib`, `Zoom_lib`. Mailer wrapper — `Mailer.php`.

## Public site (Frontend)

| Controller | Extends | Purpose |
|---|---|---|
| `Home` | `Frontend_Controller` | Public school site (per tenant). Handles every public page: about, teachers, gallery, news, events, admission, contact, privacy, terms, video, admit_card, exam_results, certificates, page/<slug>, etc. ~875 lines. |
| `Landing` | `CI_Controller` | Apex marketing site (`smartschool.bd`). |

Views: `application/views/home/` — about, contact, gallery (+ view), news (+ view), events, principal, teachers, sovapoti, faq, admission, admit_card, exam_results, certificates, reportCard, page, privacy, terms, video, payment, plus the `layout/` partials (index/header/footer) of the new Bootstrap-5.3 theme.

Models: `Home_model`, `Frontend_model`, `Gallery_model`, `News_model`, `Event_model`, `Testimonial_model`, `Content_model`.

## Add-ons & extensions

| Controller | Extends | Purpose |
|---|---|---|
| `Addons` | `MY_Controller` | Install / activate / deactivate optional addons from `uploads/addons/<hash>/`. |

The `uploads/addons/` directory currently contains 4 addon packages (UUID-named subdirs) plus `index.html`. The mechanism is generic — any drop-in addon zip is unpacked here and a row inserted in the `addon` table.

## Models (cross-feature)

These don't have a one-to-one controller mapping:

| Model | Purpose |
|---|---|
| `Application_model` | URL-host → branch resolver. Autoloaded. |
| `Crud_model` | Generic CRUD shim used by older controllers. |
| `Module_model` | Backing the `Modules` controller (addon registry). |
| `Email_model` | Email-sending wrapper. |
| `Sms_model` | SMS-sending wrapper, dispatches to the configured provider library. |
| `Dashboard_model` | Powers the admin dashboard widgets. |
| `Install_model` | Used only by the web installer. |

Archived: `application/models/bak.Saas_model.php` (pre-refactor copy still in tree).

## Helpers

| Helper | Purpose |
|---|---|
| `general_helper.php` | The big one. Auth helpers (`is_loggedin`, `is_superadmin_loggedin`, `get_loggedin_role_id`, `get_loggedin_branch_id`, `get_permission`, `access_denied`), translation (`translate()`), DB lookups (`get_global_setting`, `get_type_name_by_id`), and lots of presentational helpers. |
| `general_helper_patch.php` | Newer helpers added on top of `general_helper.php` without touching the legacy file (host normalisation, SaaS-specific utilities). |
| `custom_fields_helper.php` | Renders dynamic custom-field forms (used by `Student`, `Employee`). |
| `saas_notify_helper.php` | Email + SMS notifications for SaaS lifecycle events (approval, rejection, invoice, renewal reminder). |
| `unzip_helper.php` | Wrapper around `ZipArchive` used by `Addons` install + `System_update`. |

## Libraries

Grouped by responsibility:

**Core helpers**
- `App_lib` — utility methods used everywhere (`isExistingAddon`, `get_credential_id`, `studentLastRegID`, photo upload, file helpers). Autoloaded.
- `Bulk` — bulk-action helpers (delete, status flip).
- `Csvimport` — CSV → DB import (students, employees).
- `Slug` — slug generator.
- `Recaptcha` — Google reCAPTCHA helper.

**PDF / QR**
- `Html2pdf` — wrapper around mpdf (`application/third_party/mpdf`).
- `Ciqrcode` + `qrcode/` — QR generation (gated by the `qrcode` addon).

**Mail**
- `Mailer` — SMTP send via the vendored PHPMailer.

**SMS providers**
- `Bulksmsbd`, `Clickatell`, `Custom_sms`, `Msg91`, `Smscountry`, `Textlocal`, `Twilio` — per-provider adapters. Picked via `Sms_model`.

**Live classes**
- `Bigbluebutton_lib` (uses `application/third_party/bigbluebutton`), `Zoom_lib`.

**Payment gateways (fees / admissions / online exams)**
- `Sslcommerz` — SSLCommerz adapter (used by `Sslcommerz` controller too).
- `Stripe_payment` — Stripe (via vendored Stripe SDK).
- `Paypal_payment` — PayPal (via vendored omnipay/paypal).
- `Razorpay_payment` — Razorpay (via vendored razorpay SDK).
- `Midtrans_payment` — Midtrans (via vendored midtrans SDK).
- `Paytm_kit_lib` — Paytm.

**SaaS billing gateways** — separate from the legacy fees gateways above:
- `Saas_gateway.php` (factory) + `Saas_gateways/Saas_gateway_base.php` + `Saas_gateway_interface.php`
- Providers: `Saas_bkash_gateway`, `Saas_nagad_gateway`, `Saas_rocket_gateway`, `Saas_sslcommerz_gateway`, `Saas_stripe_gateway`, `Saas_paykureghor_gateway`, `Saas_manual_gateway`.

**SaaS lifecycle**
- `Saas_renewal_runner` — the daily-renewal worker. Triggered from CLI (`Saas_renewals_cli`) or web button (`Saas::run_renewal_cron_now`).

## View folders

Top-level subfolders of `application/views/` (each typically contains `index.php` and feature-specific partials):

`accounting/`, `addons/`, `advance_salary/`, `alumni/`, `attachments/`, `attendance/`, `attendance_period/`, `authentication/`, `award/`, `birthday/`, `branch/`, `card_manage/`, `certificate/`, `classes/`, `communication/`, `cron_api/`, `custom_domain/`, `custom_field/`, `dashboard/`, `database_backup/`, `employee/`, `errors/`, `event/`, `exam/`, `exam_progress/`, `fees/`, `frontend/`, `home/` (+ `home/layout/`), `homework/`, `hostels/`, `install/`, `inventory/`, `landing/`, `landing_admin/`, `language/`, `layout/`, `leave/`, `library/`, `live_class/`, `marksheet_template/`, `modules/`, `multiclass/`, `offline_payments/`, `online_admission/`, `onlineexam/`, `parents/`, `payroll/`, `profile/`, `reception/`, `reception_config/`, `role/`, `saas/`, `saas_billing/`, `school_settings/`, `sections/`, `sendsmsmail/`, `sessions/`, `settings/`, `signup/`, `student/`, `student_promotion/`, `subject/`, `subscription/`, `system_student_field/`, `system_update/`, `timetable/`, `transport/`, `user_login_log/`, `userrole/`.

Reusable layouts live in `application/views/layout/` (admin chrome) and `application/views/home/layout/` (public theme).

## Archived / legacy files

These files are still tracked at `HEAD` but are not loaded by the live code:

| Path | Notes |
|---|---|
| `application/controllers/bak.Attendance.php` | Pre-refactor copy. CI auto-loads by file name; this is a different name so it is ignored, but it's still in the tree. |
| `application/controllers/bak.Home.php` | Pre-redesign copy of the public home controller. |
| `application/controllers/bak.Landing.php` | Pre-redesign copy of landing. |
| `application/controllers/bak.Subscription.php` | Older subscription page. |
| `application/models/bak.Saas_model.php` | Pre-refactor copy of `Saas_model.php`. |
| `application/views/landing/bak.variant_b.php` | Pre-redesign landing variant. |
| `application/views/landing/bak.zip` | Snapshot of older landing variants. |
| `application/bupviews.zip` | Snapshot of an older `views/` tree. |
| `application/config/safe3.zip` | Snapshot of `config.php` + `purchase_key.php`. |

Recommended cleanup: delete or move out of the repo. See [`SECURITY.md`](SECURITY.md) for the secrets implications.

---

*Source: full scan of `application/controllers/`, `application/models/`, `application/libraries/` (and `Saas_gateways/`), `application/helpers/`, `application/views/`, `application/core/MY_Controller.php`.*
