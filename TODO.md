# TODO:

## General

- [ ] Cleanup
- [ ] Styling
- [ ] Rethink: Does using Javascript for all subsequent loads make sense?
- [ ] Implement permanent deletion (after 60 days)
- [ ] Redesign http error pages
- [ ] Implement languages
- [ ] Format time more nicely -> Maybe format with XXX Ago and in final state the date instead of converting to client's time? Or store in cookie//Session
- [ ] Save ms in datetime !NOT easily implementable or recommended... so maybe not?
- [ ] Customizable language support
- [ ] Notification system

## Profile/

- [ ] Notifications page
- [ ] Revise settings structure
- [ ] Show user sessions
- [ ] Decide: Make normal user moderation and moderator moderation hidable (for moderators only)?
- [ ] Make account deletion require password input
- [ ] Add manual number re-sync button for admin 5
- [ ] Scroll up the moderation history when moving from one page to another
- [ ] Add ToS, Privacy Policy and cookies warning

## Categories and Threads

- [ ] Make posting a new post focus on this post
- [ ] Check whether thread is deleted before posting message
- [ ] Allow (autherized) moderators to view deleted threads
- [ ] Rewrite forum to be 1-indexed (not 0-indexed)
- [ ] Implement simple thread search
- [ ] Scroll up the thread/threads list when moving from one page to another
- [ ] Separate location from Menu (Home -> Topic -> Thread)
- [ ] Formatting and URL support (and maaaayybbeee external images)
- [ ] Ability to pin and lock threads

## Account / Menu / Footer

- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Make notification system (badge)
- [ ] Redesign footer and menu
- [ ] Add subscribe button to path menu (for /thread/ pages)

## Authentication / Sessions

- [ ] Decide: Include clearance in the session?
- [ ] Refactor the way it is authenticated (generally speaking)

## API

- [ ] In /profile/moderation/undo.php, check if it was the last action (only last actions can be undone)
- [ ] For calls to /functions/statCount.php, make it return any error messages (and handle those accordingly)
- [ ] Organize and clean up
- [ ] Rewrite to be 100% JSON outputs/responses
- [ ] Handle error from function on report.php, as well as in /delete/, undo.php and others (search for "catch errors" or function names/file inclusions)
