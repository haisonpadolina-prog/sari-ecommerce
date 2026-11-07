# Seller size guide image (November 2026)

## Seller flow
In Add Product > Product details, upload **one** JPG, PNG, or WEBP chart (maximum 4 MB) using drag-and-drop or file selection. This is **not** a gallery image. The draft saves the image and reopens its preview. Publishing promotes a saved draft chart, or uploads a newly selected chart. Edit Product supports replacement or explicit removal. Chart changes count as sensitive edits and re-enter normal moderation.

## Buyer flow
Only approved, buyer-visible products expose `/buyer/products/{product}/size-guide`. The chart appears in Product details below Description, displayed at its natural aspect ratio and linked to its full-size file. Seller and draft routes are owner-scoped.

## AI recommendation flow
The Buyer AI has optional fit/style inputs (skin tone, preferred fit, height, weight, chest and waist), plus free-text chat. The submitted details are not stored on the Buyer account, but are included in a chat request and sent to the configured Gemini provider if available. When an approved recommended product has a size chart and Gemini is configured, a bounded number of chart images are sent as *image parts* to Gemini for chart-grounded size advice. Never infer the buyer's precise garment size from height or weight, or fabricate chart dimensions. Without Gemini or a legible chart, the assistant must say it cannot read the measurements and direct the buyer to View Product > Size guide.

## Install
Apply files relative to the Laravel project root, back up your DB/files, then run `php artisan migrate`. Existing products have no size chart until a seller uploads one. Existing public storage disk must be configured for uploaded images. Run `php artisan test` after applying and verify seller draft/publish/edit, buyer authorization, and AI behavior with real charts. No secret values are included in this patch.
