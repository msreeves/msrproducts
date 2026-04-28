# Full-Site Test Report

Date: 2026-04-28

## Runtime stability
- Homepage returns 200.
- No PHP warning/fatal markers in rendered homepage HTML.

## Key route checks
- Home: `200`
- Shop archive (`?post_type=product`): `200`
- Single product (`?p=2742`): `200`
- Compare page (`?page_id=2840`): `200`
- FAQ page (`?page_id=2839`): `200`
- Privacy page (`?page_id=3`): `200`

## Link integrity
- Homepage internal-link crawl (excluding wp-json/oEmbed API endpoints):
  - Checked: `68`
  - Bad links: `0`

## Feature checks
- Inquiry CTA visible on product contexts.
- Compare controls present on product cards and compare page renders.
- FAQ shortcode renders.
- Legal/footer IA links render.
- Cookie consent banner renders and stores local acceptance state.

## Known environment caveat
- Node build toolchain currently fails on local machine due missing ICU dynamic library for Node binary.
- To avoid blocked delivery, required modern styles were also written to `style.css`.
