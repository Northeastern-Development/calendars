# Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](http://keepachangelog.com/).

## [1.3.9.1] - 2017-08-02
## Changed
- Template Export excludes array items with empty strings

### Fixed
- Admin notices displaying on plugin Welcome/activation screen
- WPForms admin pages displaying blank due to conflicts with a few other plugins
- License related notices not removed immediately after key is activated
- Addons page items not displaying with uniform height
- Addons page installing returned JS object instead of message

## [1.3.9] - 2017-08-01
### Added
- Complete redesign and refactor of admin area
- New Settings API
- Entry print preview compact mode
- Entry print preview view entry notes
- Dynamic field choices nest hierarchical items

### Changed
- Moved Import/Export and System Info content to new Tools sub-page
- Shortcode provided in form builder now includes title/description arguments
- Don't show CSS layout selector helper in Pagebreak fields

### Fixed
- Form builder URL redirect issue on the Marketing tab with some configurations
- Password field item mislabeled
- PHP notices on Entries page if form contained no fields
- PHP notices when using HTML field with conditional logic

## [1.3.8] - 2017-06-13
### Added
- Conditional logic functionality is now in the core plugin - the Conditional Logic addon can be removed
- New conditional logic rules: empty and not empty
- Conditional logic can now be applied to fields that are marked as required

### Changed
- Available conditional logic rules/functionality with Providers have been updated
- Updated form builder modals (jquery-confirm.js)
- Many Form Builder performance enhancements

### Fixed
- Allowing Storing entries form setting to be enabled when form is connected to payments
- Number field validation message not saving
- Email/Password confirmation setting not displaying correctly with Small field size

## [1.3.7.3] - 2017-05-12
## Fixed
- Required setting checkbox getting out of sync when duplicating fields
- CSS class name typo in the form builder layout selector
- Excel mangling non-english characters when opening CSV export files
- Smart Tag `field_id` stripping line breaks
- Multiple Items field choices not updating correctly in form builder preview
- Form JS settings `wpforms_settings` missing due to some caching plugins
- Empty classes causing `array` string to be printed in some use cases

## Changed
- Updated credit card, page break, password, and phone fields to improved field class.

## [1.3.7.2] - 2017-04-26
### Fixed
- PHP warning when displaying page break indicator at the top of a form
- Error for some users with PHP 5.4 and below

## [1.3.7.1] - 2017-04-26
### Fixed
- Issue sending form notifications using email fields that had confirmation enabled

## [1.3.7] - 2017-04-26
### Added
- Google Invisible reCAPTCHA support
- Custom field validation messages (see WPForms Settings page)
- Bulk add choices for Checkbox, Multiple Choice, and Dropdown fields
- Filter to allow email notifications to include empty fields, `wpforms_email_display_empty_fields`
- Custom form template exporting
- Field CSS layout selector
- Total payment fields can now be marked as required, preventing the field from submitting unless it contains a payment

### Changed
- HTML fields now allow and run WordPress shortcodes
- Leverage `wp_json_encode` instead of native PHP function
- Various WordPress coding standard improvements (work in progress)
- Refactored form front-end code to allow for more customizations
- Refactored text, textarea, email, number, name, divider, file upload, hidden, html, payment total, and URL fields to allow for more customizations (more coming next release)

### Fixed
- Welcome page typo
- Address field options getting off sync inside form builder
- Bug adding new notifications and element IDs not updating
- Page indicator (navigation) overflowing in some use cases
- SmartTag selectors getting off sync inside form builder
- File upload routine using `pathinfo` which is not reliable with some locales

## [1.3.6] - 2017-03-09
### Added
- Constant Contact integration

### Changed
- Don't strip tags from plain text emails

### Fixed
- Address field variable name typo
- Form builder javascript conflict with Clef plugin
- Form builder logo URL double slash
- Form builder embed code field not being selectable

## [1.3.5] - 2017-02-22
### Fixed
- Some browers allowing unexpected characters inside number input fields
- Error when resending email notifications through Single Entry page
- Issue with Dropdown field placeholder text
- Select few plugins loading conflicting scripts in form builder

## [1.3.4] - 2017-02-09
### Added
- reCAPTCHA improvements; reCAPTCHA now required if turned on

### Fixed
- Date/Time Smart Tag not using WordPress time zone settings
- Name field defaults not processing Smart Tags

## [1.3.3] - 2017-02-01
### Added
- Default value support in the email field
- Related Entries metabox on single entry page
- Various new hooks and filters for improved extendibility

### Changed
- Payment status is now displayed in status column, indicated with money icon
- Multi-page scroll can be customized via JS overrides, `wpform_pageScroll`

### Fixed
- Possible errors if web host had `set_time_limit()` disabled
- File upload failing in edge cases due to library not being loaded
- PHP 7.1 warning message inside the form builder when using payments

## [1.3.2] - 2017-01-17
### Added
- CSS class support for hidden fields, for easier targeting
- New form class, `.inline-fields`, to apply single line form layout
- Allow date and time pickers properties to be specified on a per form/field basis

### Changed
- All Smart Tags now available for Email Subject field in form notifications
- License checks rely on options, instead of transients, for more reliability
- Enable date picker on mobile devices

### Fixed
- Email addresses reporting as invalid of the domain contained capitalization
- Error uploading MP3 files when File upload was using the media library
- Author related Smart Tags not working in form notification fields
- Typo on settings page related to Carbon Copy
- Incorrect messaging/layout on plugins addon page for Basic license users
- Date Time field date picker causing validation issues for mobile users
- PHP 7.1 warning messages inside the form builder

## [1.3.1.2] - 2016-12-12
### Fixed
- Plugin name to correctly indicate Lite for Lite release

## [1.3.1.1] - 2016-12-12
### Fixed
- Error with 1.3.1 Lite release

## [1.3.1] - 2016-12-08
### Added
- Dropdown Items payment field
- Smart Tags for author ID, email, and name
- Carbon Copy (CC) support for form notifications; enable in WPForms Settings

### Changed
- Form data and fields publicly accessible in email class

### Fixed
- Field duplication issues
- Total payment field error when only using Multiple Items payment field
- TinyMCE "Add Form" button not opening modal with dynamic TinyMCE instances
- Email formatting issues when using plain text formatting
- Number field validation tripping when number submitted is zero
- reCAPTCHA validation passing when reCAPTCHA left blank
- Dropdown field size not reflecting in builder
- File Upload field offering Size option but not supported (option removed)
- File uploads configured to go to the media library not working
- Server-side file upload errors not displaying correct due to a type

## [1.3.0.1] - 2016-11-10
### Added
- Context usage param to `wpforms_html_field_value` filter
- New filter, `wpforms_plaintext_field_value`, for plaintext email values

### Fixed
- Bug with date picker limiting date selection to current year
- PHP notice when uploading non-media library files
- Issue with form title/description being toggled with shortcode
- Secured `target=_blank` usage

## [1.3.0] - 2016-10-24
### Added
- Email field confirmantion
- Password field confirmation
- Support for Visual Composer
- Additional date field type, dropdowns
- Field class to force elements to full-width on mobile devices, `wpforms-mobile-full`

### Changed
- Datepicker library
- Timepicker library
- Placeholders are added/updated in real-time for Dropdown fields in the form builder
- Add empty value to select element placeholders when displaying form for better markup validation

### Fixed
- Multiple instances of reCAPTCHA on a page not correctly loading
- Field choice defaults not restoring in form builder
- Field alignment issues in the form builder when dragging field more than once
- PHP fatal erroring if form notification email address provided is not valid upon sending
- Date field Datepicker allows empty submit when marked as required
- Compatibility issuses when network activated on a Multisite install

## [1.2.9.1] - 2016-10-07
### Fixed
- Compatibility issue with Stripe addon

## [1.2.9] - 2016-10-04
### Added
- Individual fields can be duplicated in the form builder

### Changed
- How data is stored for fields using Dynanic Choices
- File Upload contents can (optionally) be stored in the WordPress media library

### Fixed
- CSV exports not handling new lines well
- Global assets setting causing errors in some cases
- Writing setting ("correct invalidly nested XHTML") breaking forms containing HTML
- Forms being displayed/included on the native WordPress Export page
- Dynamic Choices erroring when used with Post Types
- Form labels including blank IDs

## [1.2.8.1] - 2016-09-19
### Fixed
- Form javascript email validation being too strict (introducted in 1.2.8)
- Provider sub-group IDs not correctly stored with connection information

## [1.2.8] - 2016-09-15
### Added
- Dynamic choice feature for Dropdown, Multiple Choice, and Checkbox fields

### Changed
- Loading order of templates and field classes - moved to `init`
- Form javascript email validation requires domain TLD to pass
- File Upload file size setting now allows non-whole numbers, eg 0.5

### Fixed
- HTML email notification templates uses site locale text-direction
- Javascript in the form builder conflicting with certain locales
- Datepicker overflowing off screen on small devices

## [1.2.7] - 2016-08-31
### Added
- Store intial plugin activation date
- Input mask for US zip code within Address field, supports both 5 and 9 digit formats
- Duplicate form submit protection

### Changed
- Entry dates includes GMT offset defined in WordPress settings
- Entry export now includes both local and GMT dates
- Improved Address field to allow for new schemes/formats to be create and better customizations

### Fixed
- Provider conditonal logic processing when using checkbox field
- Strip slashes from entry data before processing
- Single Item field price not live updating inside form builder

## [1.2.6] - 2016-08-24
### Added
- Expanded support for additional currencies
- Display payment status and total column on entry list screen as allow sorting with these new columns
- Display payment details on single entry screen
- Miscellaneous internal improvements

### Changed
- Added month/year selector to date picker for better accessibility
- Payment validation methods

### Fixed
- Incorrectly named variables in the front-end javascript preventing features from properly being extendable

## [1.2.5] - 2016-08-03
### Added
- Setting for Email template background color
- Form setting for form wrapper CSS class

### Changed
- Multiple Payment field stores Choice label text
- reCAPTCHA tweaks and added filter
- Improved IP detection

### Fixed
- Mapped select fields in builder triggering JS error

## [1.2.4] - 2016-07-07
### Added
- Form import and exporting
- Additional logging and error reporting

### Changed
- Footer asset detection priority, for improved capatibility with other services
- Refactored and refined front-end javascript

### Fixed
- Restored form notification defaults for Blank template
- Default field validation considered 0 value as empty
- Rogue PHP notices

## [1.2.3] - 2016-06-23
### Added
- Multiple form notifications capability
- Form notification message setting
- Form notification conditional logic (via add-on)
- Additional Smart Tags available inside Form Settings panels
- Process Smart Tags inside form confirmation messages and URLs
- Hide WPForms Preview page from WordPress dashboard
- System Details tab to WPForms Settings, to display debug information, etc

### Changed
- Center align text inside page break navigation buttons
- Scroll to top most validation error when using form pagination
- Many form builder javascript improvements
- Improved internal logging and debugging tools
- Don't show Page Break fields in Entry Tables

### Fixed
- Form select inside modal window overflowing when a form exists with a long title
- Large forms not always saving because of max_input_vars PHP setting
- Entry Read/Unread count incorrect after AJAX toggle
- Single Payment field failed validation if configured for user input and amount contained a comma

## [1.2.2.1] - 2016-06-13
### Fixed
- Entry ID not always correctly passing to hooks

## [1.2.2] - 2016-06-03
### Added
- Page Break navigation buttons now have an alignment setting
- Page Break previous navigation button is togglable and defaults to off

### Changed
- Improved styling of Page Break fields in the builder
- Choice Layouts now use flexbox instead of CSS columns for better rendering

### Fixed
- Class name typo in a CSS column class introduced with 1.2.1
- PHP notice on Entries page when there are no forms

## [1.2.1] - 2016-05-30
### Added
- Drag and drop field buttons - simply drag the desired field to the form!
- Page Break progress indicator themes, with optional page titles
- Choice Layout option for Checkboxes and Multiple Choice fields (under Advanced Options)
- Full and expanded column class/grid support

### Changed
- Refactored Page Break field, fully backwards compatible with previous version
- Page Break navigation buttons with without a label do not display
- Refactored CSS column classes, previous classes are deprecated
- Improved field and column gutter consistency

### Fixed
- Form ending with column classes not closing correctly
- reCAPTCHA button overlaying submit button preventing it from being clicked

## [1.2] - 2016-05-19
### Added
- Column classes for Checkbox and Multiple choice inputs

### Changed
- Improved file upload text format inside entry tables

### Fixed
- Removed nonce verification
- Issue with Address fields not processing correctly when using international format

## [1.1.9.1] - 2016-05-06
### Fixed
- Payment calculations incorrect with large values

## [1.1.9] - 2016-05-06
### Added
- Form preview
- Other small misc. updates

### Changed
- reCAPTCHA settings description to include link to how-to article
- Some fields did not have the correct (unique) CSS ID, this has been corrected, which means custom styling may need to be adjusted
- Form notification settings hide if set to Off

### Fixed
- Issue with submit button position when form ends with columns classes
- PHP warnings inside the form builder

## [1.1.8] - 2016-04-29
### Added
- "WPForm" to new-content admin bar menu item

### Changed
- Removed "New" field name prefix
- Moved email related settings into email settings group

### Fixed
- Incorrect i18n strings
- Load order causing add-on update conflicts

## [1.1.7] - 2016-04-26
### Added
- Smart Tag for Dropdown/Multiple choice raw values, allowing for conditional email addres notifications ([link](https://wpforms.com/docs/how-to-create-conditional-form-notifications-in-wpforms/))
- HTML/Code field Conditional Logic support
- HTML/Code field CSS class support
- Three column CSS field classes ([link](https://wpforms.com/docs/how-to-create-multi-column-form-layouts-in-wpforms/))
- Support for WordPress Zero Spam plugin (https://wordpress.org/plugins/zero-spam/)

### Changed
- Checkbox/Multiple Choice fields allow certain HTML to display in choice labels

### Fixed
- Issue when stacking fields with 2 column classes

## [1.1.6] - 2016-04-22
### Added
- Entry starring
- Entry read/unread tracking
- Entry filtering by stars/read state
- Entry notes
- Entry exports (csv) for all entries in a form

### Changed
- Improved entries table overview page
- Email Header Image setting description to include recommended sizing

### Fixed
- reCAPTCHA cutting off with full form theme
- Debug output from wpforms.js
- Conflict between confirmation action and filter

## [1.1.5] - 2016-04-15
### Added
- Print entry for single entries
- Export (CSV) for single entries
- Resend notifications for single entries
- Store user ID, IP address, and user agent for entries

### Changed
- Improved single entry page (more improvements soon!)
- HTML Email template footer text appearance

### Fixed
- Form builder textareas not displaying full width
- HTML emails not displaying correctly in Thunderbird

## [1.1.4] - 2016-04-12
### Added
- Form general setting for "Submit Button CSS Class"
- Duplicate forms from the Forms Overview page (All Forms)
- Suggestion form template

### Changed
- Improved error logging for providers, now writes to CPT error log
- Adjusted field display inside the Form Builder to better resemble full theme

### Fixed
- Firefox CSS issue in form base theme
- Don't allow inserting shortcode via modal if there are no forms
- Issue limiting Total field display amount

## [1.1.3] - 2016-04-06
### Added
- New class that handles sending/processing emails
- Form notification setting for "From Address", defaults to site administrator's email address
- HTML email template for sleek emails (enabled by default, see more below)
- General setting to configure email notification format
- General setting to optionally configure email notification header image

### Changed
- Default email notification format is now HTML, can go back to plain text format via option on WPForms > Settings page
- File Upload field now saves original file name
- Empty fields are no longer included in email notifications

### Fixed
- Various issues with File Upload field in different configurations
- Address field saving select values when empty
- Issue with Checkbox field when empty

## [1.1.2] - 2016-04-01
### Added
- Form option to scroll page to form after submit, defaults on for new forms

### Changed
- Revamped "Full" form theme to be more consistent across different themes, browsers, and devices
- Full theme and bare theme separated

### Fixed
- File upload required message when not set to required

## [1.1.1] - 2016-03-29
### Fixed
- Settings page typo
- Providers issue causing AJAX to fail

## [1.1] - 2016-03-28
### Added
- Credit Card payment field

### Changed
- CSS updates to improve compatibility

### Fixed
- PHP notices when saving plugin Settings

## [1.0.9] - 2016-03-25
### Changed
- Email field defaulting to Required

## [1.0.8] - 2016-03-24
### Fixed
- Name field setting always showing Required
- Debug function incorrectly requiring WP_DEBUG

## [1.0.7] - 2016-03-22
## Changed
- CSS tweaks

### Fixed
- Issue with File Upload field returning incorrect file URL
- Filter (wpforms_manage_cap) incorrectly named in some instances

## [1.0.6] - 2016-03-21
### Added
- Embed button inside the Form Builder
- Basic two column CSS class support
- French translation

## Changed
- Form names are no longer required, if no form name is provided the template name is used
- Inputmask script, for better broad device support
- Field specific assets are now conditionally loaded
- CSS tweaks for form display

### Fixed
- Issue with Date/Time field
- Issue Address field preventing Country select from hiding in some configurations
- Localization string errors

## [1.0.5] - 2016-03-18
### Added
- Pagination for Entries table

## Changed
- Checkboxes/Dropdown/Multiple Choice fields always show choice label value in e-mail notifications

### Fixed
- PHP notices inside the Form Builder
- Typo inside Form Builder tooltip

## [1.0.4.1] - 2016-03-17
### Added
- Check for TinyMCE in the builder before triggering TinyMCE save

### Fixed
- Sub labels showing when configured to hide
- Forms pagination number screen setting not saving
- Email notification setting always displaying "On"

## [1.0.4] - 2016-03-16
### Changed
- Improved marketing provider conditional logic
- Addons page [Lite]

### Fixed
- Variable assignment in the builder

## [1.0.3] - 2016-03-15
### Added
- Basic TinyMCE editor for form confirmation messages

### Changed
- Removed form ID from form overview table, ID still visible in shortcode column

### Fixed
- Checkbox/radio form elements alignment
- Quotation slashes in email notification text
- SSL verification preventing proper API calls on some servers

## [1.0.2] - 2016-03-13
### Added
- Widget to display form
- Function to display form, `wpforms_display( $form_id )`

## Changed
- Default notification settings for Contact form template
- Success message styling for full form theme

## [1.0.1] - 2016-03-12
### Added
- "From Name" and "Reply To" Setting>Notification fields
- Smart Tags feature to all Setting>Notification fields

## [1.0.0] - 2016-03-11
- Initial release
