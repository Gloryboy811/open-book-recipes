# AI Reference: Open Book Recipes

This file is the fast-start reference for AI assistants working in this repository. Read it before making code changes. Treat the source code as authoritative if this file becomes stale.

## Project
- WordPress plugin: **Open Book Recipes**.
- Repository: https://github.com/Gloryboy811/open-book-recipes
- Main plugin entry point: `recipe-book.php`.
- Custom post type: `recipes`.
- Taxonomies: `mealtime` and `cuisine`.
- Admin UI and recipe editing live under `admin/`.
- Frontend recipe layouts live under `templates/single-*.php`; styles live under `styles/`; frontend interactions live in `script/recipe-book-front.js`.
- Archive template: `archive-recipes.php`.

## Important data model
Recipe content is stored in WordPress post content and post meta:
- `_rb_recipe_details`: JSON object with `cals`, `serves`, `url`, `difficulty`.
- `_rb_recipe_times`: JSON object with prep/cook/total hours and minutes.
- `_rb_recipe_components`: multiple meta rows, each containing a JSON component with `title` and an `ingredients` array.
- `_rb_recipe_steps`: multiple meta rows, one per direction step.
- `_rb_recipe_notes`: text.
- `_rb_nut_titles` and `_rb_nut_amounts`: arrays.
- `gallery_images`: array of attachment IDs.
- Recipe featured image is the WordPress post thumbnail.

Do not change these keys or their data shapes without a migration plan. Existing recipes depend on them.

## Current layout decision
- **Layout 1 / Basic** is the only supported selectable recipe layout.
- Former Layout 2 (Olive) and Layout 3 (Magazine) are retired because they are broken. Do not re-add them to the settings selector or validation allow-list.
- Unknown/legacy saved layout values must safely fall back to Basic.
- The settings preview is `admin/images/theme-basic.svg`.
- Basic markup: `templates/single-basic.php`; CSS: `styles/style-basic.css`.

## Frontend behaviour
- Recipe content is replaced by `add_recipe_info()` in `recipe-book.php` via the `the_content` filter.
- Ingredient scaling uses `.recipe-multiplier`, `.rb-rm-btn`, `.rb-rm-amount`, and `data-amount`. Preserve these hooks unless the JS is updated too.
- Keep CSS scoped to plugin-specific selectors where possible; the host WordPress theme controls the surrounding page.
- Escape output according to context: `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()`, or `wp_kses_post()`.

## Development rules
1. **Always bump the plugin header version for every PR/MR.** Also update the frontend script cache-busting version when frontend JS/CSS behaviour changes. Check all version strings before opening a PR.
2. Make changes on a feature/fix branch, never directly on `main`.
3. Open a draft PR unless the user explicitly asks otherwise. Target `main` unless a deliberate stacked-PR dependency is required.
4. Include a concise PR summary, validation performed, and anything not tested.
5. Never claim PHP lint, WordPress runtime, or browser testing unless actually run. State limitations plainly.
6. Preserve backwards compatibility for existing recipe metadata and settings; add migrations only when necessary.
7. Prefer small, focused changes. Remove a layout only after checking its settings, validation, renderer, template, stylesheet, preview and references.

## Validation checklist
- Search for every reference to changed layout IDs, metadata keys, CSS classes and JS selectors.
- Check PHP syntax when a PHP runtime is available.
- Check SVG/XML validity for preview assets.
- Verify the selector and settings allow-list agree.
- Verify legacy saved layout values fall back to Basic.
- Test recipe output with missing optional metadata and multiple ingredient components.
- Test ingredient multipliers at ½×, 1×, 2× and 4×.
- Test at mobile and desktop widths in a real WordPress install before calling the work complete.

## Current known limitation
This repository has no reliable automated WordPress integration test suite. A code review or static check is not a substitute for testing in the target WordPress theme.
