# Project: staging.inkrockit.com (Admin Panel)

> **READ FIRST**: See `DEVELOPER.md` for complete technical documentation.
> Also see `SOURCE_OF_TRUTH.md` for the dashboard build spec.

## Git Safety Protocol
Before making any code changes:
1. Run `git status` to check for uncommitted changes
2. Run `git log --oneline -3` to see recent commits
3. If uncommitted changes exist, STOP and ask before proceeding

## Quick Start
1. Read `DEVELOPER.md` for full context
2. Check git status before any changes
3. The admin panel runs on the server at `/var/www/staging.inkrockit.com`
4. Push to `main` to deploy (if CI/CD is configured)

## Key Files
| File | Purpose |
|------|---------|
| `DEVELOPER.md` | **Complete technical docs - READ THIS** |
| `SOURCE_OF_TRUTH.md` | Dashboard build specification |
| `web/application/` | Kohana MVC application |
| `web/application/classes/Controller/Admin/` | Admin controllers |
| `web/application/classes/Model/Admin/` | Admin models |
| `web/application/views/admin/` | Admin views |

## Database (CRITICAL)
- Database: `preprod` on localhost (67.205.161.183)
- **SHARED with**: inkrockit.com, new.inkrockit.com
- **~20,000 users** - be careful with bulk operations
- ALWAYS backup before destructive operations
- ALWAYS use DRY RUN mode first (SELECT before DELETE)

## DO NOT MODIFY
- `new.inkrockit.com` - Different codebase (React/Vite)
- Frontend site files - Managed by different repo

## Framework
- **Kohana 3.3** - PHP MVC framework
- Controllers: `web/application/classes/Controller/`
- Models: `web/application/classes/Model/`
- Views: `web/application/views/`

