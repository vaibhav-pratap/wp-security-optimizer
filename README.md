=== WP Security Optimizer ===
Contributors: Vaibhav
Tags: security, social login, caching, otp login, wordpress security
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 8.0
Stable tag: 3.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Advanced WordPress security plugin with social login, caching, and dynamic OTP login features.

== Description ==

WP Security Optimizer is a comprehensive WordPress plugin designed to enhance your site's security, performance, and user experience. It offers advanced security hardening, social login integration, caching mechanisms, and a flexible passwordless OTP login system with support for multiple vendors.

### Key Features

#### Security
- **XML-RPC Restriction**: Limits XML-RPC methods to essential ones.
- **SQL Injection Prevention**: Sanitizes queries to prevent malicious injections.
- **File Access Monitoring**: Blocks direct access to sensitive files (e.g., .php, .htaccess).
- **Security Headers**: Adds modern security headers (e.g., HSTS, CSP).
- **Login Rate Limiting**: Prevents brute force attacks with a 5-attempt limit per 15 minutes.
- **REST API Restriction**: Limits access to logged-in users only.

#### Social Login
- Integrates with Google, LinkedIn, Facebook, and GitHub using official OAuth APIs.
- Seamless user registration and login with social accounts.
- Uses Font Awesome CDN for stylish icons.

#### Caching
- **Page Cache**: File-based caching for static pages with configurable expiry and exclusions.
- **Object Cache**: Enhances WordPress option caching with a fallback system.
- **Cache Management**: Clear cache manually or automatically on content updates.

#### OTP Login
- **Passwordless Login**: Supports Email and Phone OTP with a 6-digit code (expires in 5 minutes).
- **Dynamic Vendors**: Choose from Twilio, Nexmo (Vonage), or MessageBird for phone OTP.
- **Flexible Options**: Users can switch between traditional, social, and OTP login methods via a tabbed interface.

#### Admin Interface
- Top-level menu with a shield icon for easy access.
- Comprehensive settings page to manage all features, API keys, and configurations.
- WordPress-compatible UI/UX design.

### Compatibility
- Requires PHP 8.0+ and WordPress 6.0+.
- Tested up to WordPress 6.5.

== Installation ==

1. **Download**: Obtain the plugin zip file from the WordPress repository or your source.
2. **Upload**: In your WordPress admin, go to Plugins > Add New > Upload Plugin, and upload the zip file.
3. **Activate**: Activate "WP Security Optimizer" from the Plugins page.
4. **Configure**: Navigate to WP Admin > WP Security to set up the plugin (see Configuration below).

Alternatively, install directly from the WordPress Plugin Directory if available.

== Configuration ==

### General Setup
1. Go to **WP Admin > WP Security**.
2. Configure each section as needed (detailed below).
3. Save changes with the "Save Changes" button.

### Security Settings
- **Enable Security Hardening**: Toggle to activate all security features.
- **Enable Login Rate Limiting**: Limit login attempts to 5 per 15 minutes.

### reCAPTCHA Settings
- **Enable reCAPTCHA**: Add Google reCAPTCHA to the login form.
- **Site Key / Secret Key**: Enter your Google reCAPTCHA credentials (get from [Google reCAPTCHA](https://www.google.com/recaptcha)).

### Trust Site Settings
- **Enable Trust Site**: Display a trust badge and log events.
- **API Key**: Enter your Trust Site API key (hypothetical service; replace with actual provider if used).

### Social Login Settings
- **Enable Social Login**: Activate social login options.
- **Client ID / Secret**: Configure for Google, LinkedIn, Facebook, and GitHub:
  - Get credentials from each provider’s developer console.
  - Set redirect URI to `https://your-site.com/wp-login.php?wpso_social={provider}`.

### Cache Settings
- **Enable Page Cache**: Cache static pages.
- **Enable Object Cache**: Enhance option caching.
- **Cache Expiry**: Set in seconds (default: 3600 = 1 hour).
- **Cache Exclusions**: Add URL patterns (e.g., `/shop/*`) to exclude from caching.
- **Clear Cache**: Manually clear all caches with the button.

### OTP Login Settings
- **Enable OTP Login**: Activate passwordless login.
- **Enable Email OTP**: Use WordPress email for OTP delivery.
- **Enable Phone OTP**: Use a vendor for SMS OTP.
- **OTP Vendor**: Select Twilio, Nexmo, or MessageBird.
- **Vendor Credentials**:
  - **Twilio**: SID, Auth Token, From Number (from [Twilio](https://www.twilio.com)).
  - **Nexmo**: API Key, API Secret, From Number (from [Vonage](https://www.vonage.com)).
  - **MessageBird**: API Key, From Number (from [MessageBird](https://www.messagebird.com)).
- Ensure users have a `phone_number` meta key for phone OTP (adjust code if using a different key).

== Usage ==

### Login Options
- **Traditional**: Default username/password login.
- **Social**: Click social buttons to log in (if enabled).
- **OTP**: Enter email/phone, request OTP, then verify to log in.

### Testing
- Test in a staging environment first.
- Verify security features (e.g., blocked file access, rate limiting).
- Check social login redirects and authentication.
- Confirm caching by checking `wp-content/cache/wpso/` for files.
- Test OTP with email and phone (ensure vendor credentials are valid).

== Frequently Asked Questions ==

= What are the minimum requirements? =
PHP 8.0 and WordPress 6.0 are required.

= Can I use multiple OTP vendors? =
No, you must select one vendor at a time, but you can switch vendors in settings.

= Does it conflict with other security plugins? =
It’s designed to work standalone but test compatibility with other plugins.

= How do I get API keys? =
Register with Google, social platforms, and OTP vendors (Twilio, Nexmo, MessageBird) to obtain credentials.

= Why isn’t phone OTP working? =
Ensure the vendor is configured correctly and users have a `phone_number` meta key.

== Screenshots ==

1. **Admin Settings Page**: Comprehensive interface for all features.
2. **Login Form**: Tabbed interface with Traditional, Social, and OTP options.
3. **Social Buttons**: Styled with Font Awesome icons.
4. **OTP Form**: Email/Phone input and OTP verification.

== Changelog ==

= 3.3.0 =
* Added dynamic OTP vendor support (Twilio, Nexmo, MessageBird).
* Enhanced admin settings for OTP configuration.

= 3.2.0 =
* Introduced passwordless OTP login with Email/Phone support.
* Added tabbed login interface for switching login types.

= 3.1.0 =
* Added advanced page and object caching with admin management.

= 3.0.0 =
* Initial release with security, social login, and basic features.

== Upgrade Notice ==

= 3.3.0 =
Update to enable dynamic OTP vendors. Configure your preferred vendor in settings.

== License ==
This plugin is licensed under the GPLv2 or later. See the `License URI` for details.
