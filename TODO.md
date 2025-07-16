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
- [ ] Unsubscribe deleted users from all threads in a recoverable fashion (Set to 2/3 instead of 0/1)

## Profile/

- [ ] Notifications page
- [ ] Revise settings structure
- [ ] Show user sessions
- [ ] Decide: Make normal user moderation and moderator moderation hidable (for moderators only)?
- [ ] Make account deletion require password input
- [ ] Add manual number re-sync button for admin 5
- [ ] Scroll up the moderation history when moving from one page to another
- [ ] Add ToS, Privacy Policy and cookies warning

## Categories, Threads and Posts

- [ ] Make posting a new post focus on this post with #id
- [ ] Check whether thread is deleted before posting message
- [ ] Allow (authorized) moderators to view deleted threads
- [ ] Implement simple thread search
- [ ] Formatting and URL support (and maaaayybbeee external images)
- [ ] Ability to pin and lock threads
- [ ] Upon sending an edit for a post, scroll to that post again.
- [ ] Would it make sense to use the same varible names for e.g. totalItems or gotoPage for threads and topics for the sake of the page menu?

## Account / Menu / Footer

- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Make notification system (badge)
- [ ] Redesign footer and menu
- [ ] Add subscribe button to path menu (for /thread/ pages)

## Authentication / Sessions

- [ ] Decide: Include clearance in the session?
- [ ] Refactor the way it is authenticated (generally speaking)

# Scripts

- [ ] Cleanup
- [ ] Check if location.refresh() is necessary

## API

- [ ] Organize and clean up
- [ ] Re-organize order of functions for deleting/undoing things and standardize them (first count, then history, then soft delete?)
- [ ] Cannot delete threads of already deleted accounts -> FIX THIS (thread count issue)
- [ ] Consider seperating user from post data (reducing file size)
- [ ] Re-organize all include statements to make sure they are only included ONCE (relying on !function_exists possible, but unclean)
