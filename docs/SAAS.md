# SaaS layer — code map

This document maps the multi-tenant / signup / subscription / billing code added on top of the base Ramom School Management System. For roadmap status of each piece, see [`PROJECT_DRAFT.md`](PROJECT_DRAFT.md) §P5–P9.

## High-level flow

```
                  apex marketing site                tenant signup
       ┌─────────────────────────────────┐    ┌──────────────────────────┐
       │  smartschool.bd                 │    │  /signup                 │
       │  → Landing::index()             │    │  → Signup::index()       │
       │  → applies landing_setting      │    │  → saas_pending_request  │
       │    + variant_a..e.php           │    │    (status='pending')    │
       └─────────────────────────────────┘    └──────────┬───────────────┘
                                                         │
                                              super-admin reviews
                                                         │
                                                         ▼
                                          ┌──────────────────────────────┐
                                          │  /saas/pending_request       │
                                          │  → Saas::pending_request()   │
                                          │  POST /saas/approve/<id>     │
                                          │  → Saas::approve()           │
                                          │     • insert branch row      │
                                          │     • insert custom_domain   │
                                          │     • insert saas_subscription│
                                          │     • insert audit_log       │
                                          │     • email owner            │
                                          └──────────┬───────────────────┘
                                                     │
                                                     ▼
                                          ┌──────────────────────────────┐
                                          │  <sub>.smartschool.bd        │
                                          │  → Application_model         │
                                          │    ::ss_resolve_branch_from_ │
                                          │      host() looks up         │
                                          │    custom_domain, pins      │
                                          │    branch_id for request    │
                                          │  → Home / Authentication /  │
                                          │    Admin_Controller pages   │
                                          └──────────┬───────────────────┘
                                                     │
                                              tenant uses the app
                                                     │
                                                     ▼
                                          ┌──────────────────────────────┐
                                          │  Saas_renewals_cli (cron)    │
                                          │  → Saas_renewal_runner       │
                                          │     • create invoice         │
                                          │     • email pay link         │
                                          │  → /billing/pay/<inv>        │
                                          │     → Saas_billing            │
                                          │     → Saas_gateway adapter   │
                                          └──────────────────────────────┘
```

## Controllers

| Controller | Surface | Notes |
|---|---|---|
| `application/controllers/Landing.php` | Public — apex marketing site. Reads singleton `landing_setting` row, picks `variant_a`..`variant_e`. `?variant=a..e` querystring overrides the saved variant for preview. | Routed only when `HTTP_HOST` == `smartschool.bd` or `www.smartschool.bd` (see `routes.php`). |
| `application/controllers/Landing_admin.php` | Super-admin editor for the landing page (copy / colour / variant / save / preview). | Routes: `/saas/landing`, `/saas/landing/save`, `/saas/landing/set_variant/<v>`, `/saas/landing/preview/<v>`. |
| `application/controllers/Signup.php` | Public self-service signup form. Validates → `saas_model::savePendingRequest()` → `/signup/thanks`. Also exposes `/signup/check_subdomain/<sd>` JSON endpoint. | Extends `MY_Controller`, not `Admin_Controller` (public access). |
| `application/controllers/Saas.php` | Super-admin hub for everything SaaS. ~957 lines. Every action gated by `is_superadmin_loggedin()`. | Methods: `school`, `pending_request`, `school_approved`, `approve`, `reject`, `package`, `package_edit`, `package_delete`, `assign_package`, `suspend`, `activate`, `cancel`, `extend`, `transactions`, `mark_paid`, `create_invoice`, `payment_gateways`, `payment_gateway_edit`, `save_payment_gateway`, `toggle_payment_gateway`, `manual_payments`, `approve_manual_payment`, `reject_manual_payment`, `notifications`, `save_notifications`, `test_telegram`, `billing_settings`, `save_billing_settings`. |
| `application/controllers/Subscription.php` | Tenant-facing dashboard. Shows current plan, usage, invoice history, payments. `/subscription/upgrade` (POST) assigns a new plan + creates invoice. | Extends `Admin_Controller`; requires `is_admin_loggedin()` (school admin) OR super-admin. |
| `application/controllers/Saas_billing.php` | Tenant-facing pay flow. Handles `/billing/pay/<inv>`, `/billing/start/<inv>/<provider>`, success/fail/cancel/ipn callbacks, plus manual-payment submission. | Extends `MY_Controller`; routes that take external callbacks are CSRF-excluded in `config.php`. |
| `application/controllers/Sslcommerz.php` | Legacy SSLCommerz callback handler kept for backwards-compat with older Fees / Admission flows. | Pre-dates the SaaS gateway architecture. |
| `application/controllers/Custom_domain.php` | Manage `custom_domain` rows (one row per pinned host). Used in both the super-admin SaaS section and the tenant dashboard. | Routes: `/custom_domain`, `/custom_domain/<action>`, `/custom_domain/<action>/<id>`. |
| `application/controllers/Offline_payments.php` | Approve manually-submitted offline payments. | Tagged `(Saas)` in its file header. |
| `application/controllers/Saas_renewals_cli.php` | CLI entry-point for the daily auto-renewal cron. Delegates to `Saas_renewal_runner`. | Idempotent; safe to re-run intra-day. |

## Models

| Model | Purpose |
|---|---|
| `Saas_model` | Core data accessors — branches, packages, subscriptions, invoices, payments, pending requests, audit log. The most-imported SaaS model. |
| `Saas_setting_model` | Typed accessors for the operator-tunable knobs (`renewal_grace_days`, email subject/body templates, billing contact, etc.). |
| `Saas_payment_gateway_model` | Per-gateway credential storage. |
| `Saas_manual_payment_submission_model` | Manual-payment proof uploads. |
| `Saas_notification_channel_model` | Per-tenant notification channel config (email, telegram, SMS). |
| `Tenant_provisioning_model` | Helper used by `Saas::approve()` to create the branch + seed default rows. |
| `Landing_model` | Landing-page CRUD + variant management. |
| `Custom_domain_model` | `custom_domain` table CRUD. |
| `Application_model` (autoloaded) | URL→branch resolver — see [`ARCHITECTURE.md`](ARCHITECTURE.md). |

Archived: `application/models/bak.Saas_model.php` (pre-refactor copy).

## Libraries

| Library | Purpose |
|---|---|
| `application/libraries/App_lib.php` | Hosts `isExistingAddon()` — the addon-active check the SaaS code branches on. |
| `application/libraries/Saas_gateway.php` | Factory that returns the correct gateway adapter for an invoice. |
| `application/libraries/Saas_gateways/Saas_gateway_interface.php` | Common interface (`start_payment`, `verify_callback`, `verify_ipn`, etc.). |
| `application/libraries/Saas_gateways/Saas_gateway_base.php` | Shared base class. |
| `application/libraries/Saas_gateways/Saas_manual_gateway.php` | Manual bank transfer / cash deposit; uploads a payment proof to `uploads/saas_manual_payments/`. |
| `application/libraries/Saas_gateways/Saas_sslcommerz_gateway.php` | SSLCommerz (BD). |
| `application/libraries/Saas_gateways/Saas_stripe_gateway.php` | Stripe (international). Uses the vendored Stripe SDK. |
| `application/libraries/Saas_gateways/Saas_bkash_gateway.php` | bKash (BD mobile financial service). |
| `application/libraries/Saas_gateways/Saas_nagad_gateway.php` | Nagad (BD). |
| `application/libraries/Saas_gateways/Saas_rocket_gateway.php` | Rocket (BD). |
| `application/libraries/Saas_gateways/Saas_paykureghor_gateway.php` | PayKureGhor (BD aggregator). |
| `application/libraries/Saas_renewal_runner.php` | The actual daily renewal worker: scans `saas_subscriptions` with `current_period_end` within `renewal_grace_days`, opens an invoice if not already covered, emails the school admin the `/billing/pay/<id>` link. Returns `['created' => N, 'emailed' => N, 'skipped' => N]`. |

## Helpers

| Helper | Purpose |
|---|---|
| `application/helpers/saas_notify_helper.php` | Email + (optional) SMS notifications for: signup approval, signup rejection, invoice created, renewal reminder, payment received. |
| `application/helpers/general_helper_patch.php` | Newer host/normalisation utilities used by both the tenancy and the SaaS code. |

## Views

| View folder | Purpose |
|---|---|
| `application/views/landing/` | `variant_a.php` .. `variant_e.php` for the apex landing page. (`bak.zip` and `bak.variant_b.php` are archived snapshots.) |
| `application/views/landing_admin/` | Super-admin editor UI. |
| `application/views/signup/` | Public signup form (`index.php`, `_index.php`, `thanks.php`). |
| `application/views/saas/` | Super-admin SaaS UI — `school.php`, `pending_request.php`, `school_approved.php`, `package.php`, `package_edit.php`, `settings_general.php`, `transactions.php`, `payment_gateways.php`, `payment_gateway_edit.php`, `manual_payments.php`, `notifications.php`, `billing_settings.php`, `_migration_required.php`. |
| `application/views/saas_billing/` | Tenant-facing pay screens — `pay.php`, `manual_pay.php`, `manual_submitted.php`, `result.php`. |
| `application/views/subscription/` | Tenant-facing plan/usage dashboard (`index.php`). |
| `application/views/custom_domain/` | CRUD UI for the `custom_domain` table. |
| `application/views/offline_payments/` | (Saas) offline payment review UI. |
| `application/views/home/layout/` + `application/views/home/index.php` | The redesigned public theme (Bootstrap 5.3, Swiper 10, AOS, GLightbox) that renders each tenant's public site. |

## Routes

See [`ROUTES.md`](ROUTES.md) for the full route table. The SaaS-relevant blocks:

| Surface | Patterns |
|---|---|
| Landing | `landing`, `saas/landing`, `saas/landing/save`, `saas/landing/set_variant/<v>`, `saas/landing/preview/<v>` |
| Signup | `signup`, `signup/<m>`, `signup/<m>/<arg>` |
| Saas (super-admin) | `saas`, `saas/<m>`, `saas/<m>/<arg>` |
| Custom domains | `custom_domain`, `custom_domain/<m>`, `custom_domain/<m>/<id>` |
| Subscription (tenant) | `subscription`, `subscription/<m>` |
| Billing (tenant pay flow) | `billing/pay/<id>`, `billing/start/<id>/<provider>`, `billing/success/<provider>`, `billing/fail/<provider>`, `billing/cancel/<provider>`, `billing/ipn/<provider>`, `billing/manual/<id>`, `billing/submit_manual/<id>` |
| Legacy SSLCommerz | `sslcommerz`, `sslcommerz/<m>` |

## CLI / cron

Daily renewal cron (from `Saas_renewals_cli.php` PHPDoc):

```
/opt/cpanel/ea-php82/root/usr/bin/php-cli \
  /home/zgruhjabaz/smartschool.bd/index.php \
  saas_renewals_cli run >> /home/zgruhjabaz/logs/saas-renew.log 2>&1
```

The same code path is also reachable from the super-admin UI button (`Saas::run_renewal_cron_now`) so an operator can trigger it without shell access.

The `jobs` table referenced by the strategic docs is set up to receive deferred work (queue/kind/payload/status), but no consumer (`run_jobs.php`) is checked in yet — see `PROJECT_DRAFT.md` §P1-T11.

## Activation switches

The whole SaaS layer is gated by:

1. **Row in the `addon` table with `prefix='saas'`.** `App_lib::isExistingAddon('saas')` is what `Application_model::ss_resolve_branch_from_host()` checks before trusting the `custom_domain` lookup. Without that row, the app silently behaves as a single-tenant Ramom install.
2. **`custom_domain` table exists.** Same resolver guards on `$this->db->table_exists('custom_domain')`. The base Ramom schema does not include this table; it ships as part of the SaaS migration.
3. **`$config['strict_subdomain_isolation']` in `config.php`.** Today FALSE. When TRUE, even super-admin requests are pinned to the host's branch (no cross-tenant administration without switching the URL).

## Files NOT covered by this map

- The DB schema for tables like `branch`, `saas_pending_request`, `saas_subscriptions`, `saas_package`, `saas_payment`, `invoice`, `payment`, `custom_domain`, `audit_log`, `jobs`, `landing_setting` is documented at row level inside `PROJECT_DRAFT.md`. There is no checked-in DDL/schema file in the repo at HEAD — the strategic doc references `docs/db/zgruhjabaz_smartschoolbd-2026-05-19_schema.sql` and `docs/db/migrations/*.sql`, neither of which is committed today. A future PR should add those.

---

*Source: full scan of `application/controllers/{Saas,Signup,Landing,Landing_admin,Subscription,Saas_billing,Saas_renewals_cli,Custom_domain,Offline_payments}.php`, `application/models/{Saas,Saas_setting,Saas_payment_gateway,Saas_manual_payment_submission,Saas_notification_channel,Tenant_provisioning,Landing,Custom_domain,Application}_model.php`, `application/libraries/{App_lib,Saas_gateway,Saas_renewal_runner}.php` + `application/libraries/Saas_gateways/*.php`, `application/helpers/{saas_notify,general_helper_patch}_helper.php`, `application/config/{routes,config}.php`.*
