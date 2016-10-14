CHANGELOG
=========

1.4
---

 * Fixed issues with quote escaping
 * Replaced `text` input with `textarea` for `keywords` attribute
 * Deprecated ability to send email from the home page. MailForm module should be used instead
 * Merged `Page` with `AbstractPagesController`
 * Added `name` attribute
 * Added support for table prefix
 * Changed module icon
 * Improved internal structure

1.3
---

 * Improved internals

1.2
---

 * Reduced a list of returned controllers for custom pages. Now it contains only module controllers
 * Improved internal code base

1.1
---

 * Since now default pages cannot be removed. In order to remove that page, you need to mark some another page as a default one
 * Added `PageBag`. Now `isDefault()` can be used instead of `getDefault()`
 
1.0
---

 * First public version