# Third-party libraries & integrations

Everything used at runtime that isn't shipped by CodeIgniter 3 itself is **pre-vendored** into `application/third_party/`. There is **no Composer** at the application root and `$config['composer_autoload'] = FALSE` (see [`CONFIGURATION.md`](CONFIGURATION.md)). Each library is loaded the CI 3 way — either with `$this->load->library(...)` (when wrapped in `application/libraries/`) or with a direct `require_once APPPATH . 'third_party/...'`.

## Inventory

| Package | Path | Version (per shipped metadata) | Used by |
|---|---|---|---|
| **mPDF** | `application/third_party/mpdf/mpdf/mpdf/` | mPDF (see `composer.json`; ttfonts ship inline) | `application/libraries/Html2pdf.php` → marksheet PDFs, certificates, fee receipts, ID/admit cards |
| **PHPMailer** | `application/third_party/phpmailer/phpmailer/phpmailer/` | **6.8.0** (`VERSION` file) | `application/libraries/Mailer.php` → all outbound SMTP |
| **Stripe PHP SDK** | `application/third_party/stripe/vendor/stripe/stripe-php/` | **7.79.0** (`VERSION` file) | `application/libraries/Stripe_payment.php` (legacy fees flow) + `application/libraries/Saas_gateways/Saas_stripe_gateway.php` (SaaS billing) |
| **Omnipay (core + paypal + stripe)** | `application/third_party/omnipay/vendor/omnipay/` | bundled | `application/libraries/Paypal_payment.php` (legacy fees flow) |
| **Midtrans PHP SDK** | `application/third_party/midtrans/` | **2.3.2** (`composer.json`) | `application/libraries/Midtrans_payment.php` |
| **Razorpay PHP SDK** | `application/third_party/razorpay/` | — (no version key) | `application/libraries/Razorpay_payment.php` |
| **Twilio PHP SDK** | `application/third_party/twilio/` | — (no version key) | `application/libraries/Twilio.php` (SMS) |
| **BigBlueButton API client** | `application/third_party/bigbluebutton/vendor/bigbluebutton/bigbluebutton-api-php/` | bundled | `application/libraries/Bigbluebutton_lib.php` → `Live_class` controller |

PSR shims that mPDF / Omnipay depend on (also vendored):
- `application/third_party/mpdf/psr/{http-message,log}`
- `application/third_party/mpdf/myclabs/deep-copy`
- `application/third_party/mpdf/setasign/fpdi`
- `application/third_party/mpdf/paragonie/random_compat`
- `application/third_party/omnipay/vendor/{psr,moneyphp,symfony/...}` (full Omnipay v3 dep tree)

## Why everything is vendored

This codebase originates from a CodeCanyon distribution (Ramom School Management System) whose buyers are expected to drop the zip onto cPanel-style shared hosting without shell access. Composer at install time isn't realistic in that environment, so all dependencies are committed.

Implication: dependency upgrades require manually replacing the relevant subtree, **not** `composer require`/`composer update`.

---

# Payment gateways

Two parallel payment surfaces exist:

## 1) Legacy in-app payments — fees / admissions / online exams

These flow through the original Ramom controllers and use the gateway adapters in `application/libraries/`:

| Provider | Adapter library | Controller(s) that trigger it |
|---|---|---|
| SSLCommerz | `Sslcommerz.php` | `Sslcommerz` controller, `Feespayment`, `Admissionpayment`, `Onlineexam_payment`, `Subscription` |
| Stripe | `Stripe_payment.php` | same |
| PayPal | `Paypal_payment.php` (via Omnipay) | same |
| Razorpay | `Razorpay_payment.php` | same |
| Midtrans | `Midtrans_payment.php` | same |
| Paytm | `Paytm_kit_lib.php` | same |

Provider toggles per-tenant are stored in the `payment_config` table (one row per branch) with `paypal_status`, `stripe_status`, `payumoney_status`, `paystack_status`, `razorpay_status`, `sslcommerz_status`, `jazzcash_status`, `midtrans_status`, `flutterwave_status` boolean columns. `Application_model::getSectionsPaymentMethod()` reads these flags to populate the payment-method dropdown shown to users.

All five "carve-outs" from CSRF — `/feespayment/`, `/admissionpayment/`, `/onlineexam_payment/`, `/subscription/`, `/saas_payment/` — exist because external gateways POST IPN/callback payloads to those URIs.

## 2) SaaS billing gateways — tenant subscription invoices

New, separate from the legacy adapters above. Lives in `application/libraries/Saas_gateways/`:

| File | Provider |
|---|---|
| `Saas_gateway_base.php` | Shared base class for SaaS gateways. |
| `Saas_gateway_interface.php` | Common interface (`start_payment`, `verify_callback`, `verify_ipn`, etc.). |
| `Saas_manual_gateway.php` | Manual bank-transfer / cash deposit with proof upload. Default fallback. |
| `Saas_sslcommerz_gateway.php` | SSLCommerz (Bangladesh). |
| `Saas_stripe_gateway.php` | Stripe (international). |
| `Saas_bkash_gateway.php` | bKash (Bangladesh mobile financial service). |
| `Saas_nagad_gateway.php` | Nagad (Bangladesh MFS). |
| `Saas_rocket_gateway.php` | Rocket (Bangladesh MFS). |
| `Saas_paykureghor_gateway.php` | PayKureGhor (Bangladesh aggregator). |

The factory at `application/libraries/Saas_gateway.php` selects the provider for a given invoice based on the tenant's selected gateway / package. Gateway routing is exposed under `/billing/...` (see [`ROUTES.md`](ROUTES.md)). Activation/credentials per provider live in the `saas_payment_gateway` table.

The DB enum on `saas_payment.provider` constrains the value to `('manual','sslcommerz','stripe','bkash','nagad','rocket')` per the strategic docs (the `paykureghor` adapter is present in code but may not yet be in the enum — verify when seeding production).

---

# SMS / messaging integrations

Per-tenant outbound SMS provider is configurable. Adapter libraries:

| Library | Provider |
|---|---|
| `application/libraries/Bulksmsbd.php` | BulkSMSBD (BD) |
| `application/libraries/Clickatell.php` | Clickatell (global) |
| `application/libraries/Custom_sms.php` | Generic HTTP POST template (configure URL/template per tenant) |
| `application/libraries/Msg91.php` | MSG91 (India) |
| `application/libraries/Smscountry.php` | SMSCountry (global) |
| `application/libraries/Textlocal.php` | Textlocal (UK/IN) |
| `application/libraries/Twilio.php` | Twilio (global; uses vendored Twilio SDK) |

Dispatch is centralised in `application/models/Sms_model.php` which reads the selected provider from per-branch settings and instantiates the matching library.

---

# Live-class integrations

| Library | Provider | Used by |
|---|---|---|
| `application/libraries/Bigbluebutton_lib.php` | BigBlueButton | `Live_class` controller |
| `application/libraries/Zoom_lib.php` | Zoom (JWT/marketplace API) | `Live_class` controller |

---

# Other notable libraries

| Library | Purpose |
|---|---|
| `application/libraries/Ciqrcode.php` + `application/libraries/qrcode/` | QR generation. Gated by `App_lib::isExistingAddon('qrcode')`. Used by ID cards, admit cards, fee receipts. |
| `application/libraries/Recaptcha.php` | Google reCAPTCHA (signup form, contact form). |
| `application/libraries/Slug.php` | URL-slug helper. Used by the SaaS signup to validate `subdomain`. |
| `application/libraries/Csvimport.php` | CSV → DB import (students, employees). |
| `application/libraries/Bulk.php` | Bulk-action helpers (delete, status flip). |
| `application/libraries/Saas_renewal_runner.php` | The daily renewal worker — see [`SAAS.md`](SAAS.md). |

---

# Frontend assets (CDN-loaded)

The new public theme (`application/views/home/layout/index.php`) loads the following from CDN at runtime — these are **not** vendored:

- jQuery 3.7
- Bootstrap 5.3.2
- Swiper 10
- AOS 2.3.4
- GLightbox

Older admin views still rely on jQuery + Bootstrap 4 + DataTables from `assets/vendor/` (vendored into the repo under `assets/`).

---

*Source: scan of `application/third_party/*`, `application/libraries/`, `application/libraries/Saas_gateways/`, public theme HTML in `application/views/home/layout/index.php`.*
