# Routes

All routes live in [`application/config/routes.php`](../application/config/routes.php). 83 explicit route rules are defined; everything not matched falls through to the catch-all `(:any) → home/index/$1`.

The default controller is computed **dynamically from the HTTP host**:

```php
$ss_host = preg_replace('/^www\./', '', strtolower((string)($_SERVER['HTTP_HOST'] ?? '')));
$ss_host = preg_replace('/:\d+$/', '', $ss_host);
$route['default_controller'] = ($ss_host === 'smartschool.bd') ? 'landing' : 'home';
```

So an empty path on `smartschool.bd` renders the marketing landing page; an empty path on any other host (tenant subdomain or custom domain) renders the tenant's public site.

The 404 override is `errors`.
`translate_uri_dashes` is `FALSE`.

---

## Public tenant pages (`(:any)/...`)

These routes capture a leading URL-alias segment (legacy multi-tenant addressing — `smartschool.bd/<alias>/page`) and forward it to the public site controllers.

| Pattern | Maps to |
|---|---|
| `(:any)/authentication` | `authentication/index/$1` |
| `(:any)/forgot` | `authentication/forgot/$1` |
| `(:any)/teachers` | `home/teachers` |
| `(:any)/sovapoti` | `home/sovapoti` |
| `(:any)/principal` | `home/principal` |
| `(:any)/privacy` | `home/privacy` |
| `(:any)/terms` | `home/terms` |
| `(:any)/video` | `home/video` |
| `(:any)/events` | `home/events` |
| `(:any)/news` | `home/news/` |
| `(:any)/about` | `home/about` |
| `(:any)/faq` | `home/faq` |
| `(:any)/admission` | `home/admission` |
| `(:any)/gallery` | `home/gallery` |
| `(:any)/contact` | `home/contact` |
| `(:any)/admit_card` | `home/admit_card` |
| `(:any)/exam_results` | `home/exam_results` |
| `(:any)/certificates` | `home/certificates` |
| `(:any)/page/(:any)` | `home/page/$2` |
| `(:any)/gallery_view/(:any)` | `home/gallery_view/$2` |
| `(:any)/event_view/(:num)` | `home/event_view/$2` |
| `(:any)/news_view/(:any)` | `home/news_view/$2` |

(The `$1` capture is the url-alias for the school. The Phase 1 work — see [`PROJECT_DRAFT.md`](PROJECT_DRAFT.md) — is to phase these out in favour of host-based tenant pinning via `custom_domain`.)

## Admin module shortcuts

Simple `/<module>` → `<module>/index` shorthand for the admin chrome:

| Path | Maps to |
|---|---|
| `dashboard` | `dashboard/index` |
| `branch` | `branch/index` |
| `attachments` | `attachments/index` |
| `homework` | `homework/index` |
| `onlineexam` | `onlineexam/index` |
| `hostels` | `hostels/index` |
| `event` | `event/index` |
| `accounting` | `accounting/index` |
| `school_settings` | `school_settings/index` |
| `role` | `role/index` |
| `sessions` | `sessions/index` |
| `translations` | `translations/index` |
| `cron_api` | `cron_api/index` |
| `modules` | `modules/index` |
| `system_student_field` | `system_student_field/index` |
| `custom_field` | `custom_field/index` |
| `backup` | `backup/index` |
| `advance_salary` | `advance_salary/index` |
| `system_update` | `system_update/index` |
| `certificate` | `certificate/index` |
| `payroll` | `payroll/index` |
| `leave` | `leave/index` |
| `award` | `award/index` |
| `classes` | `classes/index` |
| `student_promotion` | `student_promotion/index` |
| `live_class` | `live_class/index` |
| `exam` | `exam/index` |
| `profile` | `profile/index` |
| `sections` | `sections/index` |
| `authentication` | `authentication/index` |

> Note: there is **no `Translations` controller checked in** (only the `translations` view folder). The `$route['translations']` shortcut therefore 404s today; the strategic docs flag this as a missing controller.

## Apex landing & landing admin

| Path | Maps to |
|---|---|
| `landing` | `landing/index` |
| `saas/landing` | `landing_admin/index` |
| `saas/landing/save` | `landing_admin/save` |
| `saas/landing/set_variant/(:any)` | `landing_admin/set_variant/$1` |
| `saas/landing/preview/(:any)` | `landing_admin/preview/$1` |

## SaaS signup & super-admin

| Path | Maps to |
|---|---|
| `signup` | `signup/index` |
| `signup/(:any)` | `signup/$1` |
| `signup/(:any)/(:any)` | `signup/$1/$2` |
| `saas` | `saas/index` |
| `saas/(:any)` | `saas/$1` |
| `saas/(:any)/(:any)` | `saas/$1/$2` |

Concrete actions exposed by the `Saas` controller (annotated in its PHPDoc):

- `/saas/school` — list all tenant subscriptions
- `/saas/pending_request` — pending signup queue
- `/saas/school_approved` — recently approved signups
- `/saas/package` — package catalog list
- `/saas/package_edit/<id?>` — edit/create a package
- `/saas/settings_general` — saas-level settings
- `/saas/transactions` — all payments
- `/saas/approve/<req_id>` — POST: approve → creates branch
- `/saas/reject/<req_id>` — POST: reject signup
- `/saas/suspend/<branch_id>` — POST: suspend tenant
- `/saas/activate/<branch_id>` — POST: re-activate tenant
- `/saas/extend/<branch_id>` — POST: extend by N days
- `/saas/mark_paid/<inv_id>` — POST: mark invoice paid manually

## Custom domains

| Path | Maps to |
|---|---|
| `custom_domain` | `custom_domain/list` |
| `custom_domain/(:any)` | `custom_domain/$1` |
| `custom_domain/(:any)/(:any)` | `custom_domain/$1/$2` |

## Subscription (tenant-facing)

| Path | Maps to |
|---|---|
| `subscription` | `subscription/index` |
| `subscription/(:any)` | `subscription/$1` |

## SaaS billing (tenant-facing pay flow)

| Path | Maps to |
|---|---|
| `billing/pay/(:num)` | `saas_billing/pay/$1` |
| `billing/start/(:num)/(:any)` | `saas_billing/start/$1/$2` |
| `billing/success/(:any)` | `saas_billing/success/$1` |
| `billing/fail/(:any)` | `saas_billing/fail/$1` |
| `billing/cancel/(:any)` | `saas_billing/cancel/$1` |
| `billing/ipn/(:any)` | `saas_billing/ipn/$1` |
| `billing/manual/(:num)` | `saas_billing/manual/$1` |
| `billing/submit_manual/(:num)` | `saas_billing/submit_manual/$1` |

## Legacy payment

| Path | Maps to |
|---|---|
| `sslcommerz` | `sslcommerz/index` |
| `sslcommerz/(:any)` | `sslcommerz/$1` |

The `Sslcommerz` controller is the legacy in-app SSLCommerz callback handler used by the original Fees / Admission payment flows. The new SaaS pay flow uses the gateway adapters under `application/libraries/Saas_gateways/` and routes via `billing/...`.

## Fall-through

| Path | Maps to |
|---|---|
| `home` | `home/index` |
| `(:any)` | `home/index/$1` |

So any unmatched single-segment URL is treated as a tenant url-alias forwarded to the public site. Two-segment + URLs that aren't matched above will 404 via `errors`.

---

*Source: `application/config/routes.php` (174 lines, 83 `$route` entries).*
