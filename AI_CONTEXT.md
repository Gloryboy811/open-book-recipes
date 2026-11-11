# AI Context: Open Book Recipes

This file is the working brief for future AI-assisted changes to this repository. Read it before making code changes.

## Project
- Repository: `Gloryboy811/open-book-recipes`
- Product: a WordPress plugin for managing and displaying recipes.
- Main plugin bootstrap: `recipe-book.php`
- Recipe custom post type: `recipes`
- Recipe templates: `templates/single-{layout}.php`
- Layout selector UI: `admin/settings-page.php`
- Layout setting validation: `admin/settings.php`
- Frontend styles: `styles/`
- Frontend ingredient multiplier: `script/recipe-book-front.js`
- Admin preview assets: `admin/images/`

## User's standing requirements
1. **Every PR/MR must bump the plugin version.** Update the `Version:` header in `recipe-book.php` and keep the frontend script enqueue/cache-busting version in sync. Check for other version constants or asset versions before finishing.
2. Be direct about uncertainty. Never claim a file was inspected, a test was run, or a live site was checked unless it actually was.
3. Prefer focused changes. Do not redesign or refactor unrelated layouts when asked to change a preview or settings option.
4. Preserve existing recipe metadata formats and compatibility unless the task explicitly calls for a migration.
5. Report the branch, PR link, version bump, and tests actually run in the final response.

## Layouts
- `basic` is the original layout and is user-facing as Layout 1.
- `olive` and `magazine` were previously selectable as Layouts 2 and 3; they are being removed from the settings selector because the user reports them broken.
- `modern` is the additional mobile-first layout.
- Do not alter the rendering or styling of Basic when asked only to change its settings preview. The user's supplied mobile screenshot is the visual reference for the Basic preview thumbnail, not permission to redesign the Basic template.
- Only expose layouts that are supported in the settings UI and accepted by the validation allow-list.
- Keep old template files unless deletion is explicitly requested or repository references prove they are dead. Removing an option is not automatically permission to delete its template.

## Git / PR workflow
- Current working branch for the layout work: `feat/mobile-first-modern-theme`.
- Existing PR #4 is stacked on PR #3: base `fix/security-data-integrity`, head `feat/mobile-first-modern-theme`. Do not retarget it to `main` until PR #3 is merged.
- Add follow-up commits to the existing PR when they belong to this work; do not open a duplicate PR.
- Confirm the current PR/base/head before editing. Preserve the stacked dependency unless the user changes it.

## Current change context (2026-11-11)
- Plugin version before this change: 0.1.2.
- This change bumps the version to 0.1.3.
- Settings should expose Basic and Modern only; Olive and Magazine are removed from the selector and validation allow-list.
- Basic's actual template is not to be changed. Its settings preview is switched to a new SVG based on the supplied Creamy Lemon Chicken screenshot.
- Modern remains selectable.
- This branch is still a draft PR. PHP lint and live WordPress tests have not been run in this environment.
