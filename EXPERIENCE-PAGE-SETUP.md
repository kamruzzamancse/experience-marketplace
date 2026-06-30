# Dynamic Experience Page

The **Experiences** page keeps the theme’s existing custom header and footer and uses:

`templates/archive-tourbi-experiences.php`

## Hero image

The hero reads this existing theme image path first:

`assets/images/experience-hero-image.png`

Keep the image at that path and filename. If it is unavailable, the page Featured Image is used as a fallback, followed by the original theme reference image.

## Search and filtering

A compact search/filter bar appears directly below the hero. It works through normal server-side WordPress database queries, so it remains functional without JavaScript.

It includes:

- Keyword search
- Dynamic Experience Category filter
- Dynamic saved-location filter
- Recommended, newest, price, and duration sorting
- Clear/reset controls

Only published Rent Items with `_tourbi_experience_enabled = yes` are shown.

## Edit the hero text and bottom CTA

1. Go to **Pages → Experiences → Edit**.
2. Open **Experience Page Design & Content**.
3. Edit the hero title, accent title, subtitle, benefit items, hero image position, and bottom CTA.

## Create the category sections

1. Open the Experience Category taxonomy used by Tourbi Core.
2. Create the required categories.
3. Each category includes controls for its icon, heading, accent color, section order, card count, and visibility.
4. Assign each enabled Experience to an Experience Category.

## Experience cards

Every card is generated from the database. The image priority is:

1. Experience WordPress **Featured Image**
2. First saved Experience gallery image
3. Theme fallback image

The title, description, duration, price, URL, rating, review count, badge, and display order also come from the saved Experience data and optional **Experience Page Card** settings.

## Notes

- No ACF or extra page-builder plugin is required.
- The existing custom header and footer files were not replaced.
- The layout is responsive for desktop, tablet, and mobile.

## Experience page v3 fixes

- The fixed hero asset is now included at `assets/images/experience-hero-image.png` and rendered as a real responsive image.
- Experience cards prefer the WordPress Featured Image, then the Experience Builder main image, then the first gallery image.
- Category, location, and sorting fields use accessible custom dropdowns so background, text, hover, selected, and active colours are consistent across browsers.
