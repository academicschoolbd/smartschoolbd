# SmartSchool.bd — Single-Source-of-Truth Project Draft (Phase 0 → Final)

**Codebase:** Ramom School Management System v6.8 (CodeIgniter 3.1.13, PHP 8.2/8.3, MariaDB 11.4 on LiteSpeed/Spaceship)
**Goal:** Transform Ramom (single-school) into a multi-tenant **SaaS** where any number of schools each get their own subdomain or custom domain, with self-service signup, subscriptions, plan limits, isolated data, and a CMS-driven public school site — all sharing one codebase + one DB.
**Last updated:** 2026-05-20 (post `zgruhjabaz_smartschoolbd+3.sql`, post `smartschool_changed_files+7.zip`)
**Owner:** al.exbru69789@gmail.com

> Legend — **DONE** ☑ , **PARTIAL** ◐ , **PENDING** ☐ , **BLOCKED** ⛔ , **OBSOLETE** ⊘

---

## 0. TL;DR — what changed since the previous draft (2026-05-16)

The previous draft anticipated a long P1/P5/P8 build-out. The new SQL dump (`zgruhjabaz_smartschoolbd+3.sql`, generated 2026-05-19 22:46) shows that **a large chunk of P1, P5, P8 and P9 schema is already live in production**, and the strategic direction has pivoted from "paid plans from day one" to **"free for every Bangladeshi school for now, paid plans introduced once volume justifies it"** (codified in the `landing_setting` row's `pricing_mode='free'` and the marketing copy).

Concrete new evidence in the DB:

| Area | Change | Where in dump |
|---|---|---|
| Branch schema | `branch` now has `slug`, `subdomain`, `plan`, `owner_user_id`, `settings_json` with `UNIQUE(slug)` + `UNIQUE(subdomain)` | line 286, 9482 |
| Tenants live | branch.id = 1 (apex/Fakel), 4 (NGPS), **5 (devine2e — Devin E2E test)**, **6 (devine2eb — first real signup)** | line 338-341 |
| Login isolation | `login_credential.branch_id` added; **composite `UNIQUE(branch_id, username)` shipped** | line 4057, 10047 |
| Self-service signup | New `saas_pending_request` table with 2 records (one e2e test, one real user from a real Bangladeshi IP); `Saas::approve()` controller method exists, auto-creates branch + custom_domain row + audit_log entry | line 6749, 6769, 575, 179 |
| Subscriptions | `saas_subscriptions` table is the *active* per-tenant subscription store (4 rows: 1, 4, 5, 6); the older `subscription` table (created by 007) is left in place but largely unused | line 6779, 8735 |
| Packages | `saas_package` seeded with 3 tiers: **Free Trial (50 stu, 14-day)**, **Starter (500 stu, 999 BDT/mo)**, **Pro (unlimited, 2999 BDT/mo)** with full features+limits JSON | line 6691, 6717 |
| Billing skeleton | `invoice` + `payment` + `saas_payment` tables created (no rows yet) | line 2339, 5301, 6728 |
| Jobs queue | `jobs` table created with `queue/kind/payload/status/attempts/max_attempts/run_at/started_at/finished_at` schema (status enum incl. 'dead') | line 2363, 9989 |
| Audit log | `audit_log` table live, 2 rows recording the two `tenant.approve` events with actor, IP, UA, JSON meta | line 179, 199 |
| Custom domain | `custom_domain` rows for ngps, test, devine2e, devine2eb; `Saas::approve()` auto-inserts the subdomain row | line 590 |
| Landing/marketing | New `landing_setting` singleton (id=1) drives an A/B-able apex landing page; current copy is bilingual ("Run your school in 5 minutes — on us." / "আপনার স্কুলের জন্য সম্পূর্ণ ফ্রি স্কুল ম্যানেজমেন্ট"); `pricing_mode='free'` | line 2386, 2411 |
| Public site theme | `home/layout/index.php`, `home/layout/header.php`, `home/layout/footer.php`, `home/index.php` are fully rewritten — a modern Bootstrap-5.3 + Swiper-10 + AOS + GLightbox layout driven entirely by `front_cms_setting` color tokens (primary/hover/menu/footer/copyright) | (changed_files zip) |
| Migrations | `migrations.version = 670` (single row) | line 4959 |

So the project is materially ahead of where the previous draft put it. This v2 draft rewrites every phase's status table based on the dump.

---

## 1. TL;DR phase status (refreshed)

| # | Phase | One-liner | Was (2026-05-16) | Now (2026-05-20) |
|---|---|---|---|---|
| **P0** | Infrastructure | DNS, wildcard SSL, cron, mail | ☑ | ☑ |
| **P0.5** | Phase-0 leftovers | AutoSSL re-run, DMARC tighten, CF cleanup | ◐ | ◐ |
| **P0.6** | Git repo bootstrap | Need version control for migrations | ☐ | ☐ |
| **P1** | Multi-tenant foundation | DB-level isolation via `branch_id` | ◐ | **☑** (T8 + T10 shipped, T9/T11 partial) |
| **P2** | First tenant onboarded | NGPS live on `ngps.smartschool.bd` | ☑ | ☑ |
| **P3** | Per-tenant content + theme | Slider, news, gallery, i18n menu, **new public theme** | ☑ | ☑ + new theme rolled out |
| **P4** | Admin/auth isolation | Branch admin users, strict subdomain pin | ◐ | ◐ (P4.3 patch still drafted, not deployed) |
| **P5** | Tenant lifecycle | Signup, trial, billing, renewal, suspension, churn | ☐ | **◐** (signup + approve shipped; billing pending) |
| **P6** | Operations | Backups, monitoring, alerting, perf | ☐ | ☐ |
| **P7** | Custom domains | `app.theirschool.com` for paying tenants | ☐ | ☐ (schema ready) |
| **P8** | SaaS addon + feature gating | Plans, packages, usage limits | ◐ | **◐** (packages seeded; gating not enforced in code yet) |
| **P9** | Marketing site + public surface | Apex landing page, pricing, signup CTA | ☐ | **◐** (`landing_setting` schema + bilingual copy live) |
| **P10** | Scale-out + finalization | VPS, CDN, Redis, queue, docs, handover | ☐ | ☐ |

---

## 2. Architecture decisions (locked in)

| # | Decision | Choice | Why |
|---|---|---|---|
| 2.1 | Tenancy model | Shared DB, every tenant-scoped table has `branch_id` | Ramom already has this on most tables; inventing a parallel `tenant_id` was rejected as a 4-6 week refactor for no gain. |
| 2.2 | Tenant identifier | `branch.id` (int) + `branch.subdomain` (string) + `branch.slug` (string), both UNIQUE | Both columns are now in the schema (line 287-288), both backed by unique indexes (line 9484-9485). `subdomain` drives the host; `slug` is reserved for human-readable URL aliases. |
| 2.3 | Subdomain routing | DB table `custom_domain(url, school_id, …)` resolved by `Home_model::getDefaultBranch()` / `Home_model::getCurrentDomain()` | Header.php now branches on `app_lib->isExistingAddon('saas') && db->table_exists('custom_domain') && home_model->getCurrentDomain()` to decide whether to bypass the `url_alias`-prefixed login URL. |
| 2.4 | Subdomain → folder | Spaceship symlink: `ln -s /home/zgruhjabaz/smartschool.bd /home/zgruhjabaz/<sub>.smartschool.bd` | Confirmed for ngps, test, devine2e, devine2eb. |
| 2.5 | Apex behaviour | Apex `smartschool.bd` is **not** seeded in `custom_domain` | Keeps session-based branch picker + URL-alias path working for super-admin. The new `landing_setting`-driven landing page will eventually replace the apex Fakel CMS (still pending). |
| 2.6 | Auth model | One `login_credential` row per user, scoped by `branch_id` | **Now shipped** (line 4057 + composite UNIQUE on line 10047). Same email/username allowed across tenants. |
| 2.7 | Codebase access | Single repo, monorepo style | Still no Git repo committed — see P0.6 below. The "changed files zip" approach means we have no diff history. |
| 2.8 | Deployment | Spaceship FTP/Git deploy (current host); migrate to VPS at P10 | Matches current infra. |
| 2.9 | Payment provider | SSLCommerz primary (BD); Stripe + bKash + Nagad + Rocket also enumerated; Manual fallback | `saas_payment.provider` enum is now `('manual','sslcommerz','stripe','bkash','nagad','rocket')`. None wired yet. |
| 2.10 | Pricing strategy | **PIVOT**: "Free for every Bangladeshi school for now; paid plans for heavier features (custom domain, REST API, large storage) introduced once costs justify it" | The active `landing_setting` row says `pricing_mode='free'` and the public copy commits "anything you create today stays on the free plan; you will never be forced to pay to keep your existing data running." `saas_package` still has free/starter/pro tiers seeded, but Starter+Pro are not actively offered to public signups. |
| 2.11 | Trial model | 14-day Free trial assigned automatically on signup; default behavior is auto-trial-to-active (no auto-suspend in the live data — branch 5 still 'trial', branches 1+4+6 are 'active') | `saas_subscriptions.status` enum is `trial/active/past_due/suspended/cancelled`. |
| 2.12 | Tenant onboarding | Self-service form → `saas_pending_request` (`status='pending'`) → super-admin clicks Approve → `Saas::approve()` creates the branch row + custom_domain row + writes audit_log entry. Spaceship subdomain creation still **manual** by super-admin. | The `audit_log.meta` JSON confirms: `{"subdomain":"devine2eb", "owner_email":"buje@mailinator.com", "package_id":1, "request_id":2}`. |
| 2.13 | Branch admin auth | `branch.owner_user_id` column allows pinning the school's owner staff record to the branch | Column is in place but every existing branch still has `owner_user_id=NULL`. The `Saas::approve()` flow seems to create the owner staff but does not yet write back this FK. |
| 2.14 | Per-tenant settings | `branch.settings_json` longtext column for ad-hoc per-tenant config without needing schema migrations | Column in place, currently NULL for all rows. |
| 2.15 | PR strategy | One PR per sub-task | Still aspirational — no Git repo yet. |

---

## P0 — Infrastructure (☑ DONE)

Unchanged from prior draft. Same status, same leftovers in P0.5.

### P0.5 — Phase 0 leftovers (non-blocking)
Unchanged.

### P0.6 — Git repository bootstrap (☐ STILL PENDING)
Same as before. Without a repo we are accumulating "changed_files+N.zip" snapshots, which makes diff review and rollback awkward. Recommend bootstrapping a private GitHub/GitLab repo before P5.2 (billing) starts so all controller changes go through PRs.

---

## P1 — Multi-tenant foundation (now mostly ☑)

The biggest delta vs prior draft. Concrete evidence below.

| ID | Task | Status now | Detail |
|---|---|---|---|
| P1-T0 | Local dev setup | ◐ | No local MySQL on Devin VM — verification still happens against live DB dumps. |
| P1-T1 | DB schema audit (`docs/phase1/TABLES.md`) | ◐ | Still informal; we have 190 tables in the dump but no checked-in classification doc. |
| P1-T2 | Symlink subdomain folder → app folder | ☑ | ngps, test, devine2e, devine2eb. |
| P1-T3 | Create `tenants` table | ⊘ | Replaced by extended `branch` table. |
| P1-T4 | Add `tenant_id` to all tables | ⊘ | `branch_id` already present on tenant-scoped tables. |
| P1-T5 | Backfill | ⊘ | All legacy rows on `branch_id=1`. |
| P1-T6 | Host resolve hook | ☑ | `Home_model::getDefaultBranch()` + `Home_model::getCurrentDomain()` + `Authentication_model::urlaliasToBranch()`. |
| P1-T7 | Scope all queries by `branch_id` | ☑ | Pattern in place across controllers. |
| P1-T8 | UNIQUE → UNIQUE(branch_id, field) on `login_credential` | **☑** | `uk_login_credential_branch_username (branch_id, username)` shipped (line 10049). Other tables (`staff.email`, `student.email`, `class.name`, etc.) still need the same treatment — see "remaining T8 work" below. |
| P1-T9 | Per-tenant file uploads | ◐ | Still using `/uploads/<module>/<hash>.jpg` — no per-branch dir prefix yet. |
| P1-T10 | Login isolation (same email across tenants) | **☑** | `login_credential.branch_id` (line 4057) + composite UNIQUE shipped. **However** existing rows still have `branch_id=NULL` for both super-admin and the legacy parent accounts (line 4074 onward) — backfill of student/parent rows to their owning branch is pending. |
| P1-T11 | Jobs table + worker | **◐** | `jobs` table is created with the full schema including `queue/kind/payload/attempts/max_attempts/started_at/finished_at/status:dead` — but no worker script `application/cli/run_jobs.php` is visible, and the table is empty. |
| P1-T12 | Isolation regression test | ☐ | No automated test yet. |

### Remaining T8 work (UNIQUE composites)
Still need to audit and add `UNIQUE(branch_id, …)` on:

| Table | Field | Risk if not done |
|---|---|---|
| `staff` | `email` | Two tenants can't reuse the same staff email. |
| `student` | `email`, `roll` | Same. |
| `class` | `name` | Two tenants can't both have a class named "Six". |
| `section`, `subject` | `name` | Same. |
| `fees_type` | `name` | Same. |
| `staff_department`, `staff_designation` | `name` | Same. |
| `parent` | `mobile`, `email` | Same. |

Migration template:
```sql
ALTER TABLE staff
  DROP INDEX email,                              -- if exists; check first
  ADD UNIQUE KEY uk_staff_branch_email (branch_id, email);
```

### Remaining T10 work (login_credential backfill)
The UNIQUE index is in place but live rows still have `branch_id IS NULL`. Migration:
```sql
UPDATE login_credential lc
  JOIN staff s ON s.id = lc.user_id
   AND lc.role NOT IN (6,7)
   AND lc.branch_id IS NULL
  SET lc.branch_id = s.branch_id;
UPDATE login_credential lc
  JOIN student st ON st.id = lc.user_id
   AND lc.role IN (6,7)
   AND lc.branch_id IS NULL
  SET lc.branch_id = st.branch_id;
-- Super-admin (lc.id=1, role=1) intentionally left NULL.
```

### Remaining T11 work (worker)
Need to ship the actual `run_jobs.php` CLI script and add the cron entry:
```
*/1 * * * * /usr/bin/php /home/zgruhjabaz/smartschool.bd/index.php cli/run_jobs >> /home/zgruhjabaz/logs/jobs.log 2>&1
```

---

## P2 — First tenant onboarded (NGPS) — ☑ DONE

Unchanged. NGPS = branch.id=4, subdomain=`ngps`, status=`active`, on Free plan. Renders end-to-end at `https://ngps.smartschool.bd/`.

---

## P3 — Per-tenant content + i18n + new public theme — ☑ DONE for NGPS

Two layers here. The content layer (slider, news, gallery, FAQ, etc.) was already done in the prior draft. The **template layer** is new in this drop:

### P3.X New default home template (smartschoolbd.app design language)
All four files in the changed-files zip are part of one redesign. Together they replace the legacy Ramom home views with a modern card-based layout that matches the `smartschoolbd.app` reference design and is fully driven by `front_cms_setting` tokens.

**Files in the drop:**
| File | Purpose |
|---|---|
| `application/views/home/layout/index.php` | Master layout. Defines theming helpers `thm_shade()`, `thm_contrast_text()`, `thm_rgba()`. Pulls colors out of `$cms_setting['primary_color']`, `hover_color`, `menu_color`, `footer_background_color`, `copyright_bg_color`, `text_color`, `text_secondary_color`, `border_radius` and exposes them as CSS variables. Loads jQuery 3.7, Bootstrap 5.3.2, Swiper 10, AOS 2.3.4, GLightbox via CDN. Includes header + `$main_contents` + footer. |
| `application/views/home/layout/header.php` | Two-row site head: topbar (EIIN badge + address on the left; phone/email/Facebook/Login on the right) + brand banner (logo + Bengali name + English name + contact). Then a sticky `.t2-navbar` that pulls `home_model->menuList()` and renders dropdowns + a SaaS-aware login dropdown (Teacher/Staff/Student/Parent) when not logged in, or a Dashboard dropdown when logged in. The login URL respects `cms_setting['url_alias']` for legacy branches and falls back to plain `/authentication` when the host is mapped via `custom_domain`. |
| `application/views/home/layout/footer.php` | Four-column footer (Brand+about+socials / Quick Links / Important Pages / Contact) + copyright bar with `cms_setting['copyright_text']` override, founded-year box, social icons (FB, YT, Twitter, IG, LinkedIn). Loads legacy sweetalert.min.js for flash messages. |
| `application/views/home/index.php` | Home page body. Renders: Hero Slider (Swiper, fade effect, autoplay 5s) → marquee bar (`cms_setting['emergency_notice']`) → stats strip (live counts from `students` / `staff` / `classes` / `sessions` filtered by `branch_id`) → quick-menu grid (Notice / Results / Gallery / Admission) → welcome card → speeches grid (Sovapoti + Principal) → 4 corner service boxes (Students+Parents / Teachers+Staff / Downloads / Academic) → sidebar (Notice marquee / Hotline / Important external links / Facebook page embed) → teacher grid → video gallery (YouTube IDs from `front_cms_faq_list.description`) → latest news cards → photo gallery. |

**Notable about the template:**
- All colors are derived at render-time from a handful of stored tokens. Changing `primary_color` ripples through the entire layout because every shade is computed by `thm_shade()`.
- `thm_contrast_text()` picks black or white text per surface based on luma, so primary/accent colors can be any hex and the text stays readable.
- The header's login URL logic correctly handles three cases: (a) no `url_alias` → `/authentication`; (b) `url_alias` set but no custom_domain match → `{alias}/authentication`; (c) `url_alias` set AND host is mapped in `custom_domain` → revert to plain `/authentication`. This is the right behaviour for both legacy and SaaS hosts.
- AOS animations are wrapped with a no-JS fallback (`html:not(.aos-active) [data-aos]`) so content always renders.
- Mobile responsive: viewport meta + Bootstrap grid + `.t2-nav-toggle` hamburger.

**Bugs / improvements spotted (worth a follow-up):**
1. `home/index.php` line 56-66 queries `students`, `staff`, `classes`, `sessions` (plural) for the stats strip. Actual table names are `student`, `staff`, `class`, `schoolyear`. Only `staff` will match; the rest fail `table_exists()` and silently render nothing.
2. `home/index.php` line 394 reuses `front_cms_faq_list` for the **video** gallery, treating `description` as a YouTube ID. That's clever reuse but should be either renamed (`front_cms_videos`) or documented in the admin UI.
3. `home/index.php` line 466-484 reads gallery from `front_cms_gallery` but the canonical table for gallery photos is `front_cms_gallery_content` (gallery_category → gallery_content). Verify which one the upload UI writes to.
4. The `marquee` HTML element (deprecated) is used twice; consider replacing with a CSS-animated div.
5. External CDN-only assets (jsdelivr, cdnjs, fonts.googleapis.com) — fine on Spaceship but will need to be self-hosted (or CF cached) once we are on a VPS in a regulated environment.

Bengali menu translation (the 13 system menu ids) — still ☑ from the prior draft, unaffected by the new template.

---

## P4 — Admin/auth isolation (◐ PARTIAL)

### P4.1 Source-code proof — ☑ DONE (unchanged)

### P4.2 Branch admin user for NGPS — ◐ now blocked on a different issue
The previous block was the JS datepicker silently dropping `joining_date`. Status is unchanged because we have not retested since the Phase 3 template rolled out. However the SQL dump shows `branch.owner_user_id` is NULL for every branch including NGPS — which means even if `Saas::approve()` created an owner staff record for branches 5 and 6, it did not back-fill the FK. Two follow-ups:
- Audit `Saas::approve()` to confirm whether it actually creates the owner staff. If yes, write the staff.id back into `branch.owner_user_id`.
- Re-test the manual `/employee/add` route for NGPS with the new theme; the datepicker JS may have changed.

### P4.3 Strict subdomain → branch enforcement — ☐ STILL DRAFTED, NOT APPLIED
Same patch as in the previous draft (`enforce_subdomain_branch_isolation()` in `MY_Controller::__construct`). No evidence in dump that it was deployed (no new config row, no schema impact). This remains the single most important hardening step before P5 billing goes live, because it prevents a tenant admin from poking another tenant via the branch switcher.

### P4.4 Acceptance gates for closing P4 — unchanged

---

## P5 — Tenant lifecycle (◐ NOW PARTIAL, was ☐)

The biggest functional shift. Self-service signup → admin approval is **working in production** — proof: `saas_pending_request` row #2 was submitted from a real Bangladeshi IP `27.147.242.210` with a real browser User-Agent on 2026-05-17 and was approved 1 minute later.

### P5.1 Signup form — **☑ SHIPPED**
Live evidence:
```
saas_pending_request (id=2):
  school_name      : Rajah Ford
  school_name_bn   : Julie Marsh
  subdomain        : devine2eb
  owner_name       : Kamal Morrison
  owner_email      : buje@mailinator.com   (mailinator → user testing the form)
  owner_phone      : +1 (983) 746-2046
  package_id       : 1                      (Free Trial)
  status           : approved
  branch_id        : 6                      (back-filled on approve)
  created_at       : 2026-05-17 01:38:35
  processed_at     : 2026-05-17 01:50:36
```
And the resulting branch:
```
branch (id=6):
  slug      : devine2eb
  subdomain : devine2eb
  name      : Rajah Ford
  school_name : Julie Marsh
  email     : buje@mailinator.com
  status    : 1 (active)
  plan      : starter
```
And the resulting custom_domain row + audit_log entry are both present.

**Remaining work in P5.1:**
- The form is approval-gated rather than auto-provisioning. That's the right call for the first 50 tenants. Document this in `docs/ADMIN_GUIDE.md` when it exists.
- Branch row sets `plan='starter'` regardless of which package the requester picked (live data shows `package_id=1` (Free) → `branch.plan='starter'`). Either drop `branch.plan` and read from `saas_subscriptions.package_id` (single source of truth), or set it correctly from `saas_pending_request.package_id` at approve time.
- Subdomain creation on Spaceship still appears to be manual. Wrap that in a "subdomain not yet provisioned" warning on the approve screen.

### P5.2 Billing — ☐ NOT STARTED
Schema is in place:
- `invoice` (id, branch_id, subscription_id, invoice_no UNIQUE, period_start, period_end, amount, currency, status enum, due_date, paid_at, pdf_path)
- `payment` (id, invoice_id, branch_id, amount, currency, provider enum incl. bkash/nagad, status enum, paid_at, raw_response, …)
- `saas_payment` (same shape, plus 'rocket' in the provider enum)
- All three are empty. No `Billing.php` controller in the changed-files drop.

**Plan**: bring up `application/controllers/Billing.php` with `pay()`, `success_callback()`, `fail_callback()`, `ipn()` for SSLCommerz. Same shape for Stripe (intl). Manual provider = super-admin "Mark paid" button. Same renewal cron as in the v1 draft.

Important: with the **"free for everyone now"** stance (decision 2.10), billing is *not* on the critical path. We need it before we promote anyone off Free, but Free-forever for the first cohort means we can defer P5.2 until we have ~20+ tenants OR until we add the first Pro-only feature (custom domains).

### P5.3 / P5.4 — Suspension, dunning, churn — ☐ schema ready, code not written
`saas_subscriptions.status` enum supports it.

### P5.5 Super-admin tenant management UI — ◐
`Saas::approve()` exists. Beyond that the full superadmin module (tenants list, suspend/reactivate/impersonate, plans CRUD, invoices, audit viewer, reports) is still TBD.

### P5.6 Notifications — ☐
Schema is ready (`email_templates`, `email_templates_details`, `sms_template`, `sms_template_details` all in dump). No new templates seeded for SaaS events yet.

### P5.7 Audit log — ☑ for `tenant.approve`; expansion pending
Live evidence (line 197-200):
```
(1, 5, 1, 1, 'tenant.approve', 'branch', 5, '{"subdomain":"devine2e",  …, "package_id":1, "request_id":1}', '54.201.200.193', 'curl/7.81.0',  '2026-05-17 00:35:50'),
(2, 6, 1, 1, 'tenant.approve', 'branch', 6, '{"subdomain":"devine2eb", …, "package_id":1, "request_id":2}', '27.147.242.210', 'Mozilla/5.0 (Win) Chrome/148', '2026-05-17 01:50:36');
```
Need to extend coverage to: `tenant.suspend`, `tenant.reactivate`, `tenant.delete`, `staff.create`, `staff.delete`, `login`, `login_failed`, `plan.change`, `payment.received`, `invoice.generated`. Helper `audit_log($action, $target_type, $target_id, $meta)` should be added to `application/helpers/` (or a library).

### P5.8 Acceptance gates for closing P5
Same as v1 draft; only T5.1 (signup) flips to ☑.

---

## P6 — Operations (☐ unchanged from v1 draft)

Backups, monitoring, perf, security — all still pending. No changes vs v1 draft. Prioritize after P5.2 (billing) lands.

---

## P7 — Custom domains (☐ schema-ready, flow not built)

`custom_domain.domain_type` enum has `'subdomain' | 'custom'` (line 580). Current 4 rows are all `subdomain`. Same plan as v1 draft.

Gating note: with the "free-for-now" stance, custom domains can be marketed as the first paid feature whenever P5.2 billing lights up.

---

## P8 — SaaS addon + plans + feature gating (◐, packages now seeded)

### P8.1 Packages — ☑ seeded
`saas_package` has 3 rows:

| code | name | BDT | USD | period | student_limit | staff_limit | features | limits | trial_days |
|---|---|---:|---:|---|---:|---:|---|---|---:|
| `free` | Free Trial | 0 | 0 | monthly | 50 | 5 | dashboard, student, staff, class, section, subject, attendance, exam, frontend, notice | storage 100MB, no custom_domain, no api, no sms, no accounting | 14 |
| `starter` | Starter | 999 | 12 | monthly | 500 | 50 | + library, fees, sms, parent, student_portal | storage 2GB, sms ON | 0 |
| `pro` | Pro | 2999 | 35 | monthly | unlimited | unlimited | + accounting, transport, hostel, payroll, custom_domain, api, reports | storage 10GB, custom_domain ON, api ON, accounting ON | 0 |

### P8.2 Enforcement — ☐ NOT STARTED
No evidence in dump that controllers consult `saas_package.features` / `limits` JSON at request time. Need to ship `App_lib::featureEnabled($key)` + `App_lib::checkSaasLimit($what)` and wire them into:
- `Employee::add()` → staff cap
- `Student::add()` → student cap
- Module `__construct()` blocks → feature gates

### P8.3 Super-admin packages UI — ☐
### P8.4 Tenant-facing plan widget + /upgrade page — ☐
### P8.5 Acceptance gates — unchanged

---

## P9 — Marketing site + public surface (◐ schema + copy now live)

### P9.1 Apex landing page — ◐
The `landing_setting` table is the data layer for the eventual apex landing page. The active row already contains the full bilingual marketing copy:

| Field | Value |
|---|---|
| `active_variant` | `a` |
| `brand_color` | `#1f9d55` (forest green — a deliberate departure from the Fakel red `#051939`) |
| `hero_h1` (EN) | "Run your school in 5 minutes — on us." |
| `hero_bn` (BN) | "আপনার স্কুলের জন্য সম্পূর্ণ ফ্রি স্কুল ম্যানেজমেন্ট — কোনো কার্ড লাগবে না।" |
| `hero_eyebrow` | "Free for every Bangladeshi school — no card, no limits" |
| `hero_lead` | "Admissions, attendance, exams, fees, accounting, parent SMS and your own public school website — all in Bengali and English, on your own `schoolname.smartschool.bd`. Every feature, for every school, free for now." |
| `cta_primary_label` | "Sign your school up — free" |
| `cta_secondary_label` | "See what is included" |
| `pricing_mode` | `free` |
| `pricing_headline` | "One plan. Everything included." |
| `pricing_future_note` | (verbatim, explaining the future paid-plans roadmap and that existing data stays on free forever) |
| `show_features` / `show_pricing` / `show_testimonials` / `show_schools` | all 1 |

What's still missing: the actual landing view file (`application/views/landing/index.php` or similar) that consumes this row. The dump has the data; we need the template.

### P9.2 Pricing page — ◐
Will read from `saas_package` and `landing_setting.pricing_mode`. When mode='free', show a single "Everything, Free" card with a small footnote pointing to `pricing_future_note`. When mode flips to 'tiered', show all three saas_package rows.

### P9.3 Marketing copy — ☑ bilingual copy already drafted in `landing_setting`. Probably need a couple more iterations on Bengali phrasing.

### P9.4 SEO / analytics — ☐
- Sitemap.xml: needs a controller pulling `branch.subdomain` for each `status=1` row + static pages.
- robots.txt: deny `/admin`, `/superadmin`, `/uploads`. Allow everything else.
- Apex Google Search Console verification: pending.

### P9.5 Acceptance gates — unchanged

---

## P10 — Scale-out + finalization (☐ unchanged, defer until volume justifies)

Same plan as v1 draft. Triggers (≥50 tenants OR ≥10k DAU OR ≥5 GB DB) haven't fired yet (4 tenants in dump). Worker queue (P10.5) is partly de-risked because the `jobs` table is already in place — just need a long-running supervisor when we leave Spaceship.

---

## 3. Cross-cutting concerns (refreshed)

### 3.1 Migration order (the real migrations applied to live DB)

This list reverse-engineers what was applied based on the dump. `migrations.version=670` doesn't map to dated SQL files we can see, so naming is inferred.

| # | Migration (inferred) | Effect | Phase |
|---|---|---|---|
| 1 | `001_branch_extend.sql` | Adds `branch.subdomain`. Seeds NGPS. | P2 |
| 2 | `002_custom_domain_and_saas.sql` | Creates `custom_domain` + `saas` addon. Seeds ngps/test rows. | P2 |
| 3 | `003_repair_custom_domain.sql` | `UPDATE custom_domain SET school_id=…`. | P2 |
| 4 | `004_branch_saas_columns.sql` | Adds `branch.slug` + `branch.plan` + `branch.owner_user_id` + `branch.settings_json` + UNIQUE on slug + UNIQUE on subdomain. | (this drop) |
| 5 | `005_login_credential_branch.sql` | Adds `login_credential.branch_id` + composite UNIQUE(branch_id, username) + idx_login_credential_branch_role_active. | (this drop) |
| 6 | `006_jobs_table.sql` | Creates `jobs` table (queue/kind/payload/attempts/run_at/started_at/finished_at/status enum w/ 'dead'). | (this drop) |
| 7 | `007_saas_packages_and_subscriptions_v1.sql` | Creates `subscription` (per-branch, used by code path A). | (this drop) |
| 8 | `007b_saas_packages_seed.sql` | Seeds `saas_package` (free/starter/pro). | (this drop) |
| 9 | `008_saas_subscriptions.sql` | Creates `saas_subscriptions` (per-school, used by code path B — the active one) + `saas_pending_request` + `saas_payment`. | (this drop) |
| 10 | `009_invoice_payment.sql` | Creates `invoice` + `payment`. | (this drop) |
| 11 | `010_audit_log.sql` | Creates `audit_log` + indexes. | (this drop) |
| 12 | `011_landing_setting.sql` | Creates `landing_setting` + seeds the singleton row with bilingual copy. | (this drop) |

**Tech debt callout:** `subscription` (line 8735) and `saas_subscriptions` (line 6779) both exist. The latter is the actively used one. We should formally deprecate `subscription`, write `012_drop_subscription.sql` after confirming no controller still reads it, and migrate the 2 rows it holds into `saas_subscriptions` if any are still relevant. The names `subscription` vs `saas_subscriptions` will keep confusing future readers (and Devin) unless one is removed.

### 3.2 Test plan summary

| Test | Where | Phase |
|---|---|---|
| Smoke: every public page returns 200 | `tests/smoke.sh` | every phase |
| Smoke: stats strip on home page renders (after table-name bug fix) | `tests/home_template.sh` | P3.X |
| Isolation: tenant A can't see tenant B data | `tests/isolation.sh` | P1-T12, P4 |
| Isolation: `login_credential.branch_id` is set for every non-superadmin row | `tests/login_credential_backfill.sh` | P1-T10 |
| Signup → approve → login flow (super-admin only) | `tests/saas_signup_approve.sh` | P5.1 |
| Suspension cron on expired trial | `tests/trial_expiry.sh` | P5.4 |
| Billing: invoice → SSLCommerz callback → status=active | `tests/billing.sh` | P5.2 |
| Plan gating: blocked features return 403 | `tests/gating.sh` | P8 |
| Landing page reads `landing_setting` row | `tests/landing.sh` | P9 |
| Backup → restore round-trip | manual + scripted | P6 |
| Lighthouse ≥ 90 mobile on apex + a tenant subdomain | CI | P9, P10 |

### 3.3 Effort / timeline estimate (refreshed)

| Phase | Dev effort remaining | Calendar (with review gates) |
|---|---|---|
| P0.5 | 0.5d | done over a few weeks (non-blocking) |
| P0.6 | 0.5d | this week |
| P1 (T8 finish + T9 + T10 backfill + T11 worker + T12 tests) | 3-4d | 1 week |
| P3.X (home-template bug-fixes: table names, gallery source) | 0.5d | 1 day |
| P4 (datepicker workaround + P4.3 strict isolation patch + tests) | 1d | 3 days |
| P5.1 polish (plan column sync, subdomain-creation warning) | 0.5d | 1 day |
| P5.2 (billing: SSLCommerz + Stripe + manual + dunning) | 6-8d | 2-3 weeks |
| P5.5 (super-admin UI: tenants/plans/invoices/audit/reports) | 3-4d | 1.5 weeks |
| P5.6 (notification templates) | 1d | 3 days |
| P5.7 expansion (more audit_log call-sites) | 1d | 3 days |
| P6 (backups + monitoring + perf + security) | 3-4d | 1.5 weeks |
| P7 (custom domains + SSL) | 2d | 1 week |
| P8.2 (feature gate enforcement) | 1-2d | 4 days |
| P8.3/P8.4 (super-admin UI + tenant /upgrade page) | 2d | 1 week |
| P9.1 (apex landing view file) | 1-2d | 4 days |
| P9.2/P9.4 (pricing view + SEO) | 1d | 3 days |
| P10 (VPS + CDN + Redis + queue + docs) | 5-7d | 2-3 weeks |
| **Total remaining** | **~25-30 days dev** | **~2.5 months calendar** |

(Slightly less than v1 because P1 + P5.1 + P8.1 + P9 schema are already shipped.)

### 3.4 Risks (refreshed)

| Risk | Likelihood | Mitigation |
|---|---|---|
| Two subscription tables (`subscription` vs `saas_subscriptions`) drift further before we consolidate | Medium | Pick the active one (`saas_subscriptions`) and write a deprecation migration this iteration. Add a comment in `Saas` controller pointing to it. |
| `branch.plan` and `saas_subscriptions.package_id` get out of sync (already off: branch 6 says plan='starter' but its subscription is on Free package) | High (already happening) | Drop the `branch.plan` column OR refresh it on every subscription change via a model hook. Decide and ship in P5.1 polish. |
| Home template queries plural table names that don't exist (`students`, `classes`, `sessions`) → stats strip shows zero | High | Quick patch: change to `student`, `class`, `schoolyear`. Add a smoke test that asserts the stats numbers are non-zero on NGPS. |
| Login isolation: `login_credential.branch_id` is NULL for super-admin and ~half of existing rows → the UNIQUE index is permissive (NULLs compare unequal in MySQL) but a future P4.3 strict isolation check may still fail | Medium | Run the backfill UPDATE before enabling P4.3 strict mode. |
| SSLCommerz integration fails on first try | High | Manual provider stub already in `saas_payment` enum; ship that path first. |
| Custom domain SSL issuance manual | High | Cloudflare-in-front workaround. |
| Spaceship's wildcard SSL drifts (e.g. doesn't cover devine2eb subdomain) | Low (currently working) | Phase-0 verify script. |
| Super-admin locks self out with strict mode | Low | Default loose mode (exempt role_id=1) on first deploy. |
| LiteSpeed cache serves stale tenant data | Medium | `Cache-Control: no-cache` on `/admin`, `/authentication`, `/api`. |

### 3.5 Open user decisions (refreshed)

| # | Question | Default if no answer | Phase |
|---|---|---|---|
| 1 | Confirm "free for everyone" stance and how long it runs | The active `landing_setting.pricing_mode='free'` is treated as a commitment for at least the first 6-12 months. | strategy |
| 2 | Git repo URL? | Init one on GitHub private (org-owned), push current `application/` + `migrations/` (excluding `config/database.php`, `uploads/`, logs, cache) | P0.6 |
| 3 | Drop the legacy `subscription` table? | Mark deprecated this week, drop after one clean DB snapshot. | 3.1 tech debt |
| 4 | Drop `branch.plan` column (in favor of `saas_subscriptions.package_id`)? | Yes — single source of truth. | 3.1 tech debt |
| 5 | Staging environment? | Use `dev.smartschool.bd` (super-admin creates) as staging. | all |
| 6 | Real payment provider start day? | Defer until at least 20 tenants OR until first Pro-only feature is built. | P5.2 |
| 7 | Apex landing copy in EN + BN — sign-off on the `landing_setting` row text? | Treat current text as a v1 draft; tweak after first 10 signups. | P9 |
| 8 | Sequencing after P3.X bug-fixes: P4.3 strict isolation OR P8.2 feature gating OR P5.5 super-admin UI? | Default order: (1) P3.X bug-fixes (0.5d), (2) P4.3 strict isolation (1d), (3) P1-T10 backfill (0.5d), (4) P5.5 super-admin UI (3-4d), then (5) P8.2 feature gating. | sequencing |

---

## 4. Files / artifacts on disk

| Path | Purpose |
|---|---|
| `/home/ubuntu/work/PROJECT_DRAFT_v2.md` | **This document.** |
| `/home/ubuntu/attachments/.../zgruhjabaz_smartschoolbd+3.sql` | Live DB dump (2026-05-19 22:46). |
| `/home/ubuntu/attachments/.../smartschool_changed_files+7.zip` | Latest changed-files drop (4 home-template view files). |
| `/home/ubuntu/attachments/.../Full+PROJECT_DRAFT+.md` | Previous draft (2026-05-16). |
| `/home/ubuntu/work/changed_files/application/views/home/layout/index.php` | New master layout template. |
| `/home/ubuntu/work/changed_files/application/views/home/layout/header.php` | New topbar + brand banner + sticky nav. |
| `/home/ubuntu/work/changed_files/application/views/home/layout/footer.php` | New 4-col footer + copyright bar. |
| `/home/ubuntu/work/changed_files/application/views/home/index.php` | New home page body (hero, stats, welcome, speeches, sidebar, gallery). |
| `/home/ubuntu/ngps_spec/extracted2/application/` *(prior session)* | Unpacked Ramom source (legacy). |
| `/tmp/ngps_upload/login.sh` *(prior session)* | Curl super-admin login → cookies.txt. |
| `/tmp/ngps_upload/translate_menu.sh` *(prior session)* | Bengali menu translator (idempotent). |
| `/tmp/ngps_upload/create_ngps_admin.js` *(prior session)* | Playwright /employee/add automation (was blocked on datepicker). |

---

## 5. Resumption checklist (any future session)

1. **Pull this draft + the latest dump.**
   - `read /home/ubuntu/work/PROJECT_DRAFT_v2.md`
   - Confirm the dump file date — if there's a `+N+1` newer dump, re-run the deltas section.

2. **Reconnect to live.**
   - Pull `${RAMOM_CMS_SUPERADMIN}` / `${RAMOM_CMS_SUPERADMIN_PASSWORD}` from env.
   - Optional: re-run `/tmp/ngps_upload/login.sh` to regenerate cookies.
   - Confirm Chrome on CDP `localhost:29229` is up if you'll do UI work.

3. **Verify state hasn't drifted.**
   ```bash
   curl -sS https://smartschool.bd/        -o /dev/null -w "%{http_code}\n"   # expect 200
   curl -sS https://ngps.smartschool.bd/   -o /dev/null -w "%{http_code}\n"   # expect 200
   curl -sS https://devine2e.smartschool.bd/  -o /dev/null -w "%{http_code}\n"  # expect 200
   curl -sS https://devine2eb.smartschool.bd/ -o /dev/null -w "%{http_code}\n"  # expect 200
   ```

4. **Pick the next phase** per the table in §1:
   - First — **P3.X bug-fixes** (home template plural table names + gallery source). Cheap, high-visibility.
   - Then — **P0.6 git repo bootstrap** so every subsequent change has a PR.
   - Then — **P1-T10 backfill** of `login_credential.branch_id`.
   - Then — **P4.3 strict subdomain isolation** patch.
   - Then — **P5.5 super-admin tenant UI** (list/suspend/reactivate/impersonate).
   - Then — **P8.2 feature gate enforcement** so the Free/Starter/Pro caps actually bind.

5. **Always**:
   - One PR per sub-task.
   - Migrations numbered sequentially (next free number is `012_…`).
   - Update this `PROJECT_DRAFT_v2.md` status table at the top before closing the session.

---

## 6. Glossary (refreshed)

| Term | Meaning |
|---|---|
| Tenant / School | One school using the SaaS. = one `branch` row. |
| Branch ID | The DB primary key for a tenant (`branch.id`). |
| Subdomain | `<slug>.smartschool.bd` per tenant; stored as `branch.subdomain` (UNIQUE) and `custom_domain.url`. |
| Slug | URL-safe identifier (`branch.slug`, UNIQUE); currently mirrors `subdomain` for all rows. |
| Super-admin | `role_id = 1`. Can see/edit any tenant. One person (the owner). |
| Branch admin | `role_id = 2`. Scoped to one tenant. Can manage their school. |
| Apex | The root domain `smartschool.bd`. Will host the marketing landing page + signup form. |
| Custom domain | A tenant's own domain (`app.theirschool.com`). Schema supports it; flow not built; gated behind Pro plan when billing lights up. |
| Plan / Package | A pricing tier with features + limits. Stored in `saas_package`. |
| Subscription | Per-tenant assignment of a package + billing state. **Active table = `saas_subscriptions`** (keyed on `school_id`). The older `subscription` table (keyed on `branch_id`) is legacy and slated for removal. |
| Pending request | A signup-form submission awaiting super-admin approval. Stored in `saas_pending_request`. Becomes a `branch` row + `custom_domain` row + `saas_subscriptions` row on approve, with an `audit_log` entry. |
| `custom_domain` table | Maps host → branch_id. Public-frontend uses it to pick which tenant to render. |
| `loggedin_branch` | Session var set at login time from `staff.branch_id`. Drives admin scoping. |
| Job | Async unit of work in the `jobs` table (email, SMS, billing charge, export). Worker script not yet shipped. |
| `landing_setting` | Singleton row driving the apex marketing landing page. Controls hero/CTA copy, brand color, pricing mode, and which sections render. |
| `front_cms_setting` | Per-tenant CMS settings driving the **public school site theme** (logos, colors, contact info, EIIN, etc.). |

---

## 7. Sign-off line (for when project is done)

When every phase shows ☑ here, the project is done:

> "All phases P0-P10 verified. Multi-tenant SaaS production-ready, with self-service signup, billing, plans, custom domains, backups, monitoring, and docs. Tested with N≥3 paying tenants and N≥20 free tenants. Handover complete."

Signed: ____________________ Date: ____________
