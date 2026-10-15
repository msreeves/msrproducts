# Media Restoration Checklist (msrproducts)

Use this after any DB pull/sync to restore real images and remove placeholders.

## 1) Source path mapping

- Local uploads target:
  - `/Users/msreeves/Developer/workspace/sites/wp/main/wp-content/uploads`
- Typical source options:
  - Live server `wp-content/uploads`
  - Backup archive extracted uploads folder
  - Staging uploads mirror

Map source -> destination as:
- `SOURCE/wp-content/uploads/*` -> `LOCAL/wp-content/uploads/*`

## 2) Copy files (recommended)

- Use rsync/scp/cp with timestamps preserved.
- Verify year/month folders exist (`2023`, `2024`, `2025`, `2026`, etc).

Example shape:
- `uploads/2026/03/*.jpg`
- `uploads/2023/11/*.svg`

## 3) URL consistency

Ensure:
- `home` = `http://127.0.0.1/sites/wp/main`
- `siteurl` = `http://127.0.0.1/sites/wp/main`

Then flush rewrite rules:
- `wp rewrite flush --hard --path="/Users/msreeves/Developer/workspace/sites/wp/main"`

## 4) Verification script

Run:
- `php /Users/msreeves/Developer/workspace/scripts/verify-media-restoration.php`

Pass criteria:
- `missing_attachments` should be `0` (or near-zero if intentionally removed files).
- `missing_sample_urls` should be `0`.

## 4b) Relink missing attachment paths (after upload sync)

If files are present in uploads but WordPress meta still points to stale paths:

- Dry run:
  - `php /Users/msreeves/Developer/workspace/scripts/archive/relink-missing-attachments.php --dry-run`
- Apply updates:
  - `php /Users/msreeves/Developer/workspace/scripts/archive/relink-missing-attachments.php --apply`
- Re-verify:
  - `php /Users/msreeves/Developer/workspace/scripts/verify-media-restoration.php`

## 5) UX check pages

Confirm image-heavy pages:
- Home: `/?`
- Product archive: `/?post_type=product`
- Sample product: `/?product=wayfarer-messenger-bag`
- Page sample: `/?page_id=2`

## 6) If still broken

- Regenerate thumbnails (plugin/CLI).
- Re-run media verification script.
- Confirm file permissions on uploads folders.
