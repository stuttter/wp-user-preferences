# WP User Preferences contributor guidance

## Compatibility

- Preserve PHP 7.4 and WordPress 6.4 compatibility unless a dedicated pull
  request explicitly changes the published minimums.
- Preserve the public functions, filters, key mapping, and user-to-site-to-
  network fallback order unless a deprecation path is part of the change.
- Treat the distinction between missing metadata and intentionally stored
  empty values as compatibility-sensitive.
- Preserve multisite behavior: the network option is only the final fallback
  when neither a user preference nor a site option exists.

## Tests

- Add or update a regression test before changing observed PHP behavior.
- Characterize user, site, and network precedence; missing values; explicitly
  empty values; key mapping; filters; and multisite context when touching
  those paths.
- Run `composer test`, the declared PHP syntax matrix, and metadata/artifact
  validation before requesting review.

## Releases

The source version, readme stable tag, Git tag, and WordPress.org version must
agree before publishing. A release requires explicit authorization and must use
the protected WordPress.org environment.

## Automation

Follow the organization-level safety boundaries. AI-authored implementation
must remain a draft pull request and cannot modify workflows, release policy,
ownership, security policy, dependencies, or this file without a specific
maintainer decision for that change.
