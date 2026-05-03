# msrproducts Local Restore Runbook

## 1) Baseline restore
- Import database snapshot.
- Ensure `woocommerce` plugin is installed and active.
- Ensure active theme is `msrproducts`.

## 2) URL normalization
- Canonical local URL:
  - `http://127.0.0.1:8888/sites/wp/main/`
- Normalize options:
  - `wp option update home "http://127.0.0.1:8888/sites/wp/main" --path="/Applications/MAMP/htdocs/sites/wp/main"`
  - `wp option update siteurl "http://127.0.0.1:8888/sites/wp/main" --path="/Applications/MAMP/htdocs/sites/wp/main"`

## 3) Core pages and policy pages
- `wp eval 'msrproducts_ensure_core_pages();' --path="/Applications/MAMP/htdocs/sites/wp/main"`

## 4) Permalink reliability
- Local setup uses plain/query routes to avoid Apache rewrite drift.

## 5) Smoke tests
- Home: `/?`
- Shop archive: `/?post_type=product`
- Single product: `/?product=<slug>` or `/?p=<id>`
- Compare page: `/?page_id=<compare-project-id>`
- FAQ page: `/?page_id=<faq-page-id>`
- Privacy page: `/?page_id=3`

## 6) Regression checks
- Homepage renders with no PHP warnings/fatals.
- Cart/checkout routes no longer expose purchase flow.
- Filter, compare, and search interactions work.
- Footer legal links resolve and cookie notice appears once.
