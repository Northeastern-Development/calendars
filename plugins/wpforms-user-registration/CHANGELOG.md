Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](http://keepachangelog.com/).

## [1.1.0] - 2018-05-14
### Fixed
- Typo in user activation email.

## [1.0.9] - 2017-12-19
### Fixed
- Login form did not set proper cookie for https:// sites.

## [1.0.8] - 2017-08-21
### Changed
- Template uses new `core` property so it displays with other core templates.

## [1.0.7] - 2017-08-01
### Fixed
- Form builder alert containing misspelling.

## [1.0.6] - 2017-02-22
### Fixed
- Capitalized letters not being allowed in custom user meta keys.

## [1.0.5] - 2016-12-08
### Changed
- Emails sent to site admin/user on account creation now use HTML email template.
- For new registration forms, the Username field is no longer required; email address used as fallback.
- Additional user data is passed to `wpforms_user_registered` action.

## [1.0.4] - 2016-10-24
### Fixed
- Setting for login form template that was not displaying.

## [1.0.3] - 2016-10-05
### Fixed
- Misnamed function causing errors.

## [1.0.2] - 2016-09-15
### Added
- Errors indicating username/email already exist are now filterable.

### Changed
- User registration and login form templates load order so it shows after default templates.

## [1.0.1] - 2016-06-23
### Added
- New filters to allow for email customizations.

### Changed
- Prevent plugin from running if WPForms Pro is not activated.

## [1.0.0] - 2016-05-19
- Initial release.
