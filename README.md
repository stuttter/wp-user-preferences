# WP User Preferences

Prefer personal user settings over site and network settings.

WP User Preferences allows users to override certain site & network configurations to suit their own personal needs. Languages, time-zones, date & time formats, currency formats, and email notifications are a few examples of user preferences.

## Installation

* Download and install using the built in WordPress plugin installer.
* Activate in the "Plugins" area of your admin by clicking the "Activate" link.
* No further setup or configuration is necessary.

## API

Use `wp_get_user_preference( $user_id, $key )` to retrieve a preference for a
specific user, or `wp_get_current_user_preference( $key )` for the current
user. The plugin checks an existing user-meta value first, then the site option,
and finally the network option on multisite.

Use the `wp_map_user_preference_key` filter when the corresponding user, site,
and network keys differ. Use `wp_get_user_preference` to filter the resolved
value.

## FAQ

### Does this create new database tables?

No. There are no new database tables with this plugin.

### Does this modify existing database tables?

No. All of WordPress's core database tables remain untouched.

### Does this plugin work with multisite?

Yes. In a multisite installation, the network setting is the last fallback used.

### Where can I get support?

The WordPress support forums: https://wordpress.org/support/plugin/wp-user-preferences/

### Can I contribute?

Yes, please! Having an easy-to-use API and powerful set of functions is critical to managing complex WordPress installations. If this is your thing, please help us out!
