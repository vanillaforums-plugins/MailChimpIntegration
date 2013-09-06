## MailChimp Integration plugin for Vanilla Forums
Allow to autosubscribe new and existing users to your MailChimp newsletter. Requires Vanilla >= 2.0 (2.1 tested).

### Features
* autosubscribe new users at registration
* bulk subscribe all existing forum users (from 0.2)
* show optin/optout checkbox at registration (from 0.3 - sponsor [phreak](http://vanillaforums.org/profile/4038/phreak))
* customizable registration checkbox text (using vanilla translation file: $Definition['Subscribe to the newsletter'] = 'Your text here')

### Quickstart
Insert in settings page your MailChimp APIKey and ListID and it works. Now you can bulk import all users from settings page. Try to register a new account to check mailing list subscription feature (based on plugin settings).

#### APIKey
You can generate an apikey in MailChimp "Account settings"->"Extras"->"API keys" page. It is an alpha-numeric string.

#### ListID
You can find your list ID in MailChimp "Lists"->"Click on your list name"->"Settings"->"List name & defaults"->"List ID" page. Probably it is ten char string.

### New features (planned)
* track registered/unregistered vanillaforums users
* multiple list support
* groups/roles integration
* ask! (or make a pull request)
* [Donate with PayPal to support development](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GNW69WBHA5KYU)

##Author & License
Alessandro Miliucci GPL v3. Icon by the [Gnome Project](http://art.gnome.org/themes/icon) under GPL license.
