3.0.10 / 2017-08-22
==================

 * Fixed: payment issue with Stripe/Braintree
 * Fixed: tax billing information is configurable, no longer compulsory
 * Fixed: license verification does not work in certain cases
 * Fixed: speed up campaigns listing
 * Fixed: speed up subscribers counting
 * Fixed: automation trigger for date in the past
 * Fixed: reduce the number of SQL queries
 * Fixed: retain custom translation when upgrading
 * Added: allow resending a campaign
 * Added: support SendGrid subuser
 * Added: support sending a campaign again
 * Added: support verify-email.org service
 * Added: support importing subscribers via CLI

3.0.9 / 2017-07-13
==================

 * Fixed: bounced emails not added to blacklist
 * Fixed: Reply-To header not correctly set for SendGrid and ElasticEmail
 * Fixed: support Elastic {unsubscribe} tag
 * Added: include Portuguese translation
 * Added: speed up list loading
 * Added: support testing bounce/feedback handlers
 * Added: support sending a test email for sending server
 * Added: display the generated DKIM DNS record for sending domain
 * Added: reduce the tracking log size for faster delivery
 * Added: allow accessing the webapp from root directory
 * Added: support copying templates

3.0.8 / 2017-06-29
==================

 * Fixed: bounced email not added to blacklist
 * Fixed: make sure confirmation email be triggered
 * Added: reduce memory usage while sending
 * Added: speed up page load in general
 * Added: font selection support for email template
 * Added: speed up automation page load
 * Added: speed up subscribers listing

3.0.7 / 2017-06-19
==================

 * Fixed: message-id cannot be retrieved in bounced message
 * Fixed: JS error showing up in the edit template page
 * Fixed: lagging image/video in the email editor
 * Fixed: thumbnail not showing up correctly
 * Fixed: user cannot subscribe to certain plans
 * Fixed: speed up the customers list view

3.0.6 / 2017-06-08
==================

 * Fixed: issue with automation for subscriber events
 * Fixed: automation follow-up email not triggered in certain cases
 * Fixed: incorrect quota counting in certain cases
 * Fixed: cannot subscribe customer to a plan as administrator
 * Fixed: prevent double subscription
 * Fixed: check to make sure public/ folder is writable while installing
 * Changed: queued campaign is now of QUEUED status (READY is no longer used)
 * Changed: SMTP encryption is no longer a required field
 * Changed: remove GMP extension validation as it is no longer required

3.0.4 / 2017-06-01
==================

 * Added: subscribers count by unique email
 * Added: more test scripts
 * Fixed: fix potential security issues related to CSRF
 * Fixed: upgrade will no longer overwrite custom translation
 * Fixed: campaign does not pause in certain cases 
 * Fixed: CSV export may not work correctly

3.0.3 / 2017-05-25
==================

 * Added: support billing information page
 * Added: configure sending servers that can be added by customer
 * Added: look up GEO information from local database
 * Fixed: intermittent unsubscribe issue
 * Fixed: plain text glitch with ElasticEmail
 * Fixed: intermittent issue when saving payment methods
 * Fixed: update-profile URL does not work with automation
 * Fixed: editor inserts additional tags to the email content

3.0.2 / 2017-05-20
==================

 * Added: support working with Braintree's merchant accounts
 * Added: support updating site logo
 * Fixed: delivery handler issue with SSL
 * Fixed: eliminate duplicate background jobs
 * Fixed: automation issue with list subscription
 * Fixed: prevent duplicate unsubscription
 * Fixed: better mobile compatibility
 * Fixed: upgrade manager issue with old PHP versions

3.0.1 / 2017-05-11
==================

 * Added: integration with email verification service Kickbox.io
 * Added: integration with email verification service TheChecker.co
 * Added: upgrade manager - allow upgrading directly from the admin dashboard
 * Added: email web viewer (through the WEB_VIEW_URL tag)
 * Added: table features for email builder
 * Fixed: plain text campaign issue with ElasticEmail

3.0.0-p3 / 2017-04-20
==================

 * Added: payment integration with PayPal
 * Added: payment integration with Stripe
 * Added: preview campaign email before sending
 * Changed: change the API response format for CREATE USER, including api_token
 * Fixed: licence page error on certain systems
 * Fixed: duplicate queries generated for campaign statistics view

3.0.0-p1 / 2017-04-11
==================

 * Added: service plan management
 * Added: registration for visitor
 * Added: payment integration with Braintree (for Paypal and credit card)
 * Added: sending throttling setting
 * Added: subscription management
 * Added: customer management
 * Added: more flexible role based access control
 * Added: license verification
 * Added: clean up sending server's send() function
 * Added: more intuitive description on the UI
 * Fixed: automation sending duplicate follow-up email

2.2.0-p14 / 2017-03-30
==================

 * Added: support sending email through SparkPost API
 * Added: reduce memory usage by 40% for sending to 1M subscribers
 * Added: support file update via API
 * Added: better built-in  Spanish translation
 * Changed: suppress error messages in laravel.log
 * Fixed: import issue with non-break spaces
 * Fixed: check/uncheck sending server in Group Edit page
 * Fixed: feedback handling with ElasticEmail
 * Fixed: reduce subscribers list request size
 * Fixed: bounce handling issue: cannot retrieve message ID in certain cases

2.2.0-p13 / 2017-03-15
==================

 * Added: support more caching to speed up page loading
 * Added: better compatibility check
 * Added: better Message-Id generation, to avoid conflict
 * Added: improve list importing performance
 * Added: more API support, allow retrieving list's fields
 * Added: follow up campaign email when it is not opened/clicked
 * Added: new quota tracking system that supports multi-process
 * Fixed: inconsistent Open map
 * Fixed: inconsistent SendGrid bounce report
 * Fixed: inconsistent sending statistics
 * Fixed: email header Return-Path not properly setup
 * Fixed: campaign status not correctly set

2.2.0-p12 / 2017-02-27
===================

 * Added: speed up page loading using cache
 * Added: support additional user group API
 * Fixed: file manager's thumbnail image 
 * Fixed: intermittent glitch with code editor
 * Fixed: template upload issues with old HTML/CSS styles
 * Fixed: translating missing in system email

2.2.0-p11 / 2017-02-22
===================

 * Added: fully multi-process supported
 * Added: support more API for list/subscriber management
 * Changed: new default user permissions
 * Fixed: better handle invalid bounced message
 * Fixed: invalid bounces not showing up correctly
 * Fixed: automation cannot start for certain scenarios

2.2.0-p10 / 2017-02-15
====================

 * Fixed: suppress verbose error message
 * Fixed: do not fork new processes if it is not really needed
 * Fixed: file manager upload limit
 * Fixed: issue with plain text campaign when working with SendGrid
 * Fixed: prevent campaign from being queued more than one time 
 * Fixed: template selection issue on old Firefox browsers
 * Fixed: click-to-open rate not showing up correctly

2.2.0-p9 / 2017-02-10
=====================

 * Fixed: installation issues with exec on restricted OS
 * Added: support Re-captcha
 * Added: include more details in installation error messages
 * Changed: API - return newly created subscriber's UID in the response

2.2.0-p8 / 2017-02-06
=====================
 
 * Added: support advanced background job installation wizard
 * Added: support erasing existing database before initialization
 * Added: support customizing system's default language
 * Fixed: issue with auto login by token

2.2.0-p7 / 2017-01-30
=====================

 * Added: support updating the application configuration without re-installing
 * Added: support sending campaign without using cronjob
 * Fixed: timezone issue while scheduling future campaign

2.2.0-p6* / 2017-01-27
===================

 * Added: support sending campaign to multiple lists / segments
 * Fixed: issue of migrating from v2.0.4
 * Fixed: links sometimes do not work in test email
 * Fixed: intermittent memory issue of PHP 5.6 or below

2.2.0-p5 / 2017-01-23
===================

 * Fixed: sending server's quota 

2.2.0-p4 / 2017-01-20
===================

 * Fixed: file manager URL issue with old browsers
 * Changed: new quota renewal method

2.2.0-p3 / 2017-01-17
===================

 * Fixed: memory limit issue with importing
 * Fixed: MAC OS line-ending issue
 * Fixed: follow-up email is triggered more than one time

2.2.0-p2 / 2017-01-11
===================

 * Fixed: compatibility issues with old PHP versions
 * Fixed: compatibility issues with 
 * Fixed: calendar glitches on some browsers

2.2.0-p1 / 2017-01-08
===================

 * Fixed: mail list export compatibility issue on certain systems
 * Fixed: cannot delete out-dated campaigns
 * Fixed: php-curl compatibility issue
 * Fixed: improve subscribers import performance
 * Fixed: PHP 7.1 compatibility issues
 * Changed: php-xml is now required
 * Changed: refractor of the system jobs
 * Changed: only one cronjob is required
 * Added: support automation/autorespond functionality

2.0.4-p27 & 2.0.4-p28 / 2016-11-09
===================

 * Added: send a test email of campaign
 * Added: better internationalization support: allow creating new language
 * Added: better internationalization support: support custom translation
 * Changed: support running several campaigns at the same time

2.0.4-p26 / 2016-11-08

 * Added: send a test email of campaign
 * Added: better internationalization support: allow creating new language
 * Added: better internationalization support: support custom translation
 * Changed: support running several campaigns at the same time

2.0.4-p25 / 2016-11-01
==================

 * Fixed: certain encoding may cause corrupt links
 * Changed: default user policy change

2.0.4-p24 / 2016-10-28
==================
 
 * Fixed: subscriber import does not work well with async
 * Fixed: runtime-message-id with extra invisible space
 * Fixed: directory permission checking error
 * Fixed: campaign's wrong subscribers count in certain cases
 * Fixed: config cache with invalid values

2.0.4-p23 / 2016-10-23
==================

 * Added: ElasticEmail API/SMTP support
 * Fixed: reduce the delay time when sending email through SMTP
 * Changed: delivery server encryption method is no longer required

2.0.4-p22 / 2016-10-19
==================
 
 * Added: create-user API
 * Added: quick login support
 * Added: copy campaign
 * Fixed: detect more environment dependencies when installing
 * Fixed: layout crashes for old IE browser
 * Fixed: application crashes when mbstring is missing
 * Fixed: chart view issues on MS Edge

2.0.4-p20 / 2016-10-12
==================

 * Fixed: installation wizard compatibility issue
 * Added: drag & drop email builder

2.0.4-p19 / 2016-10-03
==================

 * Fixed: certain types of links are not tracked

2.0.4-p18 / 2016-10-02
==================

 * Fixed: open tracking causes broken image in email content

2.0.4-p17 / 2016-10-02
==================

 * Fixed intermittent issues with bar chart in Safari
 * Changed click-to-open ratio is now based on open count

2.0.4-p16 / 2016-09-30
==================

 * Fixed listing sometimes crashes due to slow internet connection
 * Fixed do not allow users to enter invalid IMAP encryption method
 * Fixed list import intermittent issue for ISO encoded CSV
 * Added pie chart visualization for top countries by open
 * Added pie chart visualization for top countries by click
 * Updated text & hints on the UI
 * Changed dashboard UI now contains more information
 * Changed click-rate is no longer computed based on specific URL

2.0.4-p11 / 2016-09-27
==================

 * Fixed SSL issue for bounce handler
 * Fixed bounce handler does not work correctly for certain type of IMAP servers
 * Changed sending campaign can be deleted
 * Added full support for SendGrid (web API & SMTP)

2.0.4-p8 / 2016-09-20
==================

 * Fixed HTML editor sometimes crashes on MS Edge 
 * Added clean up invalid bytes sequence in email content
 * Added check php-gd library availability in the installation wizard

2.0.4 / 2016-09-13
==================

This is the first publicly released version of Acelle Mail webapp (which was previously Turbo Mail 1.x, a private project at National Information System institute)

 * Fixed better compatibility with MS Edge browser
 * Multi-process support for sending large amounts of email
 * Added Mailgun API/SMTP integration full support
 * Added embeded form customization support
 * Added email extra headers for better RFC compliance
 * Added template gallery & template customization support

2.0.3 / 2016-07-01
==================

 * Added DKIM singing support for out-going message
 * Added better integration with Amazon SES
 * Added template preview support
 * Added bounce logging with more information
 * Changed refractor of quota system
