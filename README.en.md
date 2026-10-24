# PS Mitgliedschaften

[Deutsch](README.md) | **English**

[![Version](https://img.shields.io/badge/Version-1.0.3-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-up%20to%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

**Your content. Your community. Your memberships.**

Build a membership website that fits your project: from a free community area to a digital magazine or paid access to exclusive content. PS Mitgliedschaften combines flexible access rules, member management and payment processing in a single plugin for ClassicPress and WordPress.

## What you can build

- **A community with an exclusive area:** Make selected content available only to registered members.
- **A digital magazine:** Combine public posts with protected articles and downloads.
- **A learning or knowledge platform:** Release content gradually instead of making everything available at once.
- **A paid membership offering:** Manage free access, one-time payments and recurring membership fees.
- **A protected multisite network:** Manage access rules centrally when network-wide protection is enabled.

## Key features

| Area | Features |
| --- | --- |
| Memberships | Standard, drip content, guest and no-membership access; free and paid offerings |
| Content protection | Posts, pages, categories, post types, media, URLs and individual content sections |
| Access periods & billing | Time-limited access, recurring payments and trial periods |
| Member management | Assign members, manage subscriptions and keep track of statuses and payments |
| Communication | Automated emails and custom protection messages |
| Extensions | Coupons, invitation codes, profile fields, attributes and pro-rated billing |
| Integrations | BuddyPress, bbPress, Mailchimp, WPML and reCaptcha, among others |
| Multisite | Network-wide content protection and central administration |

Many features are enabled through the integrated add-ons, allowing you to tailor the plugin to your website. External integrations require the corresponding additional plugin or an account with the relevant service.

### Four membership types

- **Standard membership:** Protected content for assigned members.
- **Drip content membership:** Release content over time, for example for lessons that build on one another.
- **Guest membership:** Content exclusively for visitors who are not logged in.
- **No Membership:** Content for logged-in users who have not yet joined another membership.

### Payments that fit your model

The plugin includes integrations for **Stripe**, **PayPal** and **Authorize.net**, as well as **manual payments**. Free memberships do not require an external payment provider.

Available billing models depend on the selected gateway and its configuration. External providers require your own credentials; their fees and terms apply independently of the plugin.

> Manual payments are not confirmed automatically. Check that the payment has been received, then mark the invoice as paid.

## Installation

### Requirements

- A ClassicPress or WordPress installation.
- **PHP 8.0 or newer**, according to the current project requirements.
- For payment providers: the appropriate accounts, API credentials and webhooks where required.
- For a production membership and payment area: HTTPS.

Version information and requirements are listed in the [plugin readme](readme.txt). Test updates and your chosen integrations in a staging environment before deploying them to production.

### Install the plugin

1. Download an available plugin package from the [releases](https://github.com/Power-Source/ps-mitgliedschaften/releases) or clone this repository.
2. Place the plugin files in `wp-content/plugins/ps-mitgliedschaften/`. The `membership2.php` file must be directly inside this directory.
3. Activate **PS Mitgliedschaften** in the plugin administration screen.
4. Follow the setup process and check the membership pages that have been created or assigned.
5. Create your first membership and configure its protection rules.

**No Node.js build is required** to use the included JavaScript and CSS files.

## Get started in a few steps

### 1. Define your memberships

Decide which offerings to provide, who can join them and whether access should be free, time-limited or paid.

### 2. Protect your content

Choose the appropriate protection rules for pages, posts, categories or other content types. Enable additional protection features only where you need them.

### 3. Set up registration and membership pages

Check the pages for login, registration, membership selection and account management. Adapt protection messages and email text to your community.

### 4. Connect a payment provider

Configure a suitable gateway for paid offerings. Test the entire process using the selected provider's testing facilities first.

### 5. Test the member experience

Check access as a guest, as a logged-in user without a membership and as a member. Also test registration, payment, expiration and cancellation.

> **Important for caching and media protection:** Make sure page caches and CDNs do not serve protected content to visitors. For downloads, also test direct file access; protecting a content page alone does not guarantee that linked files are protected.

## Multisite

PS Mitgliedschaften supports multisite environments and provides network-wide protection. Enable and configure this feature deliberately to suit your network.

Check the rules on every affected website and with different user accounts. A network installation does not replace checking individual access paths.

## Languages & translations

The original strings are predominantly **German**. Translation files are also included for:

- English (United States and United Kingdom)
- French
- Italian
- Spanish

The English catalogs have been synchronized with the current source code and fully populated as PO and MO files. Translation coverage for other languages may vary.

Language files are located in [`languages/`](languages/); the text domain is `membership2`. Translation improvements are welcome. Maintenance and validation instructions are available in the [build documentation](docs/BUILD.md#english-translations).

## For developers

### Set up the repository

```bash
git clone https://github.com/Power-Source/ps-mitgliedschaften.git
cd ps-mitgliedschaften
npm ci
```

The asset tooling requires **Node.js 18 or newer**.

### Build assets & run tests

```bash
# Minify JavaScript and CSS
npm run build

# Development mode with file watching and sourcemaps
npm run dev

# Redirect regression tests
npm test
```

The build writes the minified files used by the plugin directly alongside their source files. Rebuild them after changes to keep the source and distributed assets in sync.

Check the English translations with:

```bash
php scripts/check-translations.php
```

The check uses the POMO classes from an enclosing WordPress/ClassicPress installation and does not require a database connection. For a standalone checkout, pass the installation path:

```bash
php scripts/check-translations.php /path/to/wordpress
```

Further details on builds, distribution and language files are available in [`docs/BUILD.md`](docs/BUILD.md).

### Project structure

| Path | Contents |
| --- | --- |
| [`membership2.php`](membership2.php) | Plugin entry point |
| [`app/`](app/) | Core functionality, administration, views, rules and assets |
| [`app/addon/`](app/addon/) | Integrated extensions |
| [`app/gateway/`](app/gateway/) | Payment integrations |
| [`premium/`](premium/) | Additional bundled extensions and gateways |
| [`languages/`](languages/) | Translation template, PO and MO files |
| [`scripts/`](scripts/) | Build tools and checks |
| [`docs/`](docs/) | Project and build documentation |

The `premium/` directory name comes from the project structure. It does not imply an additional purchase requirement: PSOURCE provides its projects free of charge and as open source, as described in the [project overview](docs/about.md).

## Contributing & support

Found a bug, have an idea or want to improve a translation?

- **Bugs and suggestions:** Open an [issue](https://github.com/Power-Source/ps-mitgliedschaften/issues).
- **Code and translations:** Submit a [pull request](https://github.com/Power-Source/ps-mitgliedschaften/pulls) with a clear description.
- **When reporting bugs:** Include the plugin, PHP and CMS versions, relevant integrations, steps to reproduce, and the expected and actual behavior.

Do not publish passwords, API keys, payment details or members' personal data in issues, screenshots or logs.

## Changelog & license

Version history is maintained centrally in the [plugin readme changelog](readme.txt).

PS Mitgliedschaften is licensed under the **GNU General Public License, version 2 or later (GPL-2.0-or-later)**. You may use, modify and redistribute the plugin under the terms of this license. Bundled third-party libraries are subject to their respective license notices.

[Full GPL v2 license text](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

---

**A PSOURCE project - for websites that turn visitors into a community.**
