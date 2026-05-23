# RESUME — pick up the SmartSchool.bd work from any session

Last updated: **2026-05-20**

This document is the "fast on-ramp" for any new Devin session (or human) continuing this work. Read this first. The deep architecture/phase plan lives in [`docs/PROJECT_DRAFT.md`](PROJECT_DRAFT.md) — read it second.

---

## 0. One-paragraph recap

SmartSchool.bd is a multi-tenant SaaS overlay on **Ramom School Management System v6.8** (CodeIgniter 3.1.13, PHP 8.2/8.3, MariaDB 11.4). Multi-tenancy uses **shared DB + `branch_id` column** isolation. Each tenant gets a subdomain (`<slug>.smartschool.bd`) and optionally a custom domain (rows in `custom_domain`). Self-service signup lives in `saas_pending_request`; super-admin approval auto-provisions a branch, custom_domain, subscription, and audit_log row.

The code+docs were pushed to GitHub on 2026-05-20 at commit `f12a8a0`. Repo: https://github.com/academicschoolbd/smartschool

---

## 1. Where things are

| What | Path |
|---|---|
| Living phase plan | [`docs/PROJECT_DRAFT.md`](PROJECT_DRAFT.md) |
| This file | `docs/RESUME.md` |
| Schema snapshot (no PII) | [`docs/db/zgruhjabaz_smartschoolbd-2026-05-19_schema.sql`](db/zgruhjabaz_smartschoolbd-2026-05-19_schema.sql) |
| One-shot migration SQLs | `docs/db/migrations/*.sql` |
| App code | `application/` |
| Multi-tenant branch resolver | `application/models/Application_model.php::get_branch_id()` (lines 13–26) |
| MY_Controller (auto-scoping + strict-mode hook) | `application/core/MY_Controller.php` |
| SaaS controllers | `application/controllers/{Saas,Signup,Landing,Landing_admin}.php` |
| SaaS models | `application/models/{Saas_model,Landing_model,Tenant_provisioning_model}.php` |
| Per-school settings UI | `application/controllers/School_settings.php` (writes to `branch` row) |
| Public site theme overrides | `application/views/home/layout/{index,header,footer}.php` + `home/index.php` |

---

## 2. Current state (as of 2026-05-20)

### Done
- Multi-tenant foundation (P1) — `branch_id` everywhere; `login_credential.branch_id` + composite `UNIQUE(branch_id, username)` shipped.
- Self-service signup live (P5.1) — `saas_pending_request` + super-admin `Saas::approve()` end-to-end.
- Packages seeded (P8) — Free / Starter / Pro with features+limits JSON.
- Landing-page configuration (P9) — `landing_setting` row seeded (`pricing_mode='free'`, EN+BN copy, `#1f9d55` green); the actual landing view file is still TBD.
- Audit log live for tenant approval events.
- Tenants live: id=1 (Fakel/apex), id=4 (NGPS), id=5 (`devine2e`), id=6 (`devine2eb`).
- Repo on GitHub at https://github.com/academicschoolbd/smartschool (main).

### In progress (this PR)
**Branch:** `fix/p1-closer` — three small fixes that close out Phase 1:
- (a) SQL to backfill `login_credential.branch_id` for legacy rows.
- (b) Flip `$config['strict_subdomain_isolation']` to `TRUE` in `application/config/config.php`.
- (c) Fix `application/views/home/index.php` stats-strip table names (`students`→`student`, `classes`→`class`, `sessions`→`schoolyear`).

### Pending
| # | Phase | What |
|---|---|---|
| P3.X | bug-fixes | (mostly) covered by `fix/p1-closer`; further audit may surface more |
| P4.4 | controller audit | grep every controller for `branch_id` POST handlers, ensure they cannot escape host pinning |
| P5.2 | billing | wire SSLCommerz → `Saas::create_invoice()` + auto-renew cron |
| P5.5 | super-admin tenant UI | improvements to the Saas dashboard (search/sort/filter, bulk operations) |
| P6 | operations | nightly DB backup to S3, error monitoring (Sentry), perf budget per route |
| P7 | custom domains | wire DNS-detection → automatic SSL via Let's Encrypt |
| P8.2 | feature gating | enforce `saas_package.limits_json` per-request (student count, storage, addon access) |
| P9.view | landing view | actually render the apex landing page from `landing_setting` |
| P10 | scale-out | move to VPS, Redis sessions, queue worker for `jobs` table |

Detailed per-phase status: [`docs/PROJECT_DRAFT.md` §3](PROJECT_DRAFT.md).

---

## 3. How to resume in a fresh Devin session

### From a fresh VM
```bash
# 1. Clone
cd /home/ubuntu && mkdir -p repos && cd repos
git clone https://github.com/academicschoolbd/smartschool.git
cd smartschool

# 2. Read the project state
cat docs/RESUME.md         # <-- this file
cat docs/PROJECT_DRAFT.md  # <-- the deep phase plan

# 3. Check what's in flight
git fetch --all
git branch -a
gh pr list   # or curl the GitHub API
```

### To push a change
```bash
# Branch off main
git checkout main && git pull
git checkout -b fix/short-description

# Make edits, commit
git add -p
git commit -m "Short summary

Longer body if needed."

# Push and open a PR (never push directly to main)
git push -u origin fix/short-description
gh pr create --base main --title "..." --body "..."
```

The `GITHUB_PAT` secret is saved at user scope; future sessions will have it available automatically as `${GITHUB_PAT}`. If push gets blocked by the Devin proxy, use a credentialed remote URL:
```bash
git remote set-url origin "https://x-access-token:${GITHUB_PAT}@github.com/academicschoolbd/smartschool.git"
```

---

## 4. Things the live production server is missing

These don't ship in code — they live in the DB or on the server:

1. **`login_credential.branch_id` backfill** — see `docs/db/migrations/2026-05-20_backfill_login_credential_branch_id.sql`. Run this on production once before flipping `strict_subdomain_isolation` on.

2. **Spaceship subdomain creation is still manual.** When a new tenant gets approved, the super-admin must create the subdomain in Spaceship cPanel + point it at the wildcard. (See draft P0 for the long-term automation plan.)

3. **No jobs worker yet.** The `jobs` table exists but nothing consumes it. Until that ships, billing/SMS/email tasks run inline in the request.

4. **DB backups are manual.** Production has no scheduled `mysqldump`-to-S3 yet. Until that ships, run `mysqldump` from cPanel weekly.

---

## 5. How to verify the current state matches this doc

```bash
# 1. Schema sanity
mysql -u USER -p DB < docs/db/zgruhjabaz_smartschoolbd-2026-05-19_schema.sql --force --no-data
mysql -u USER -p DB -e "SELECT version FROM migrations;"
# Expected: 670

# 2. Tenants live
mysql -u USER -p DB -e "SELECT id, slug, subdomain, name, plan, status FROM branch;"
# Expected: 4 rows (ids 1, 4, 5, 6)

# 3. Packages seeded
mysql -u USER -p DB -e "SELECT id, name, price, billing_cycle FROM saas_package;"
# Expected: 3 rows (Free / Starter / Pro)

# 4. UNIQUE index on login_credential
mysql -u USER -p DB -e "SHOW INDEX FROM login_credential WHERE Key_name LIKE '%branch%';"
# Expected: composite UNIQUE on (branch_id, username)
```

---

## 6. Open questions the next session should ask the user

- Do you want me to actually RUN the backfill SQL against production from the repo, or just deliver the SQL file?
- When you're ready for P5.2 (billing), do we wire SSLCommerz only, or also bKash/Nagad in the first pass?
- When you're ready for P7 (custom domains), do we use Let's Encrypt automation (acme.sh) or rely on Spaceship's UI?
- Is the apex landing page (P9.view) a higher priority than billing automation?

---

## 7. Glossary

- **Branch / tenant / school** — the same thing. `branch_id` is the per-tenant primary scoping key.
- **Apex** — the bare `smartschool.bd` domain. Branch id=1 is the apex branch.
- **Super-admin** — user with `role=1` in `login_credential`. Spans all branches; `branch_id` IS NULL for super-admin rows.
- **Pinned host** — an HTTP request whose `Host:` header matches a `custom_domain` row; resolves to a single branch and locks the request to it.
- **Strict subdomain isolation** — config flag in `application/config/config.php`. When TRUE, MY_Controller refuses requests where the session's branch ≠ the host's branch. Today: FALSE.
