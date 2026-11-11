# Agent instructions

Before making changes in this repository, read [AI_CONTEXT.md](AI_CONTEXT.md). It contains the project structure, the user's standing requirements, the layout rules, and the current pull-request workflow.

Key non-negotiable rule: bump the plugin version in `recipe-book.php` for every PR/MR, and keep the frontend script cache-busting version in sync.

Keep changes scoped to the request. In particular, do not change the Basic recipe layout's frontend markup or styles when the task concerns only its settings preview.
