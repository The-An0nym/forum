# TODO:

## General

- [ ] add rate limiting to all queries (via session and common endpoint?)
- [ ] Update schematic + sql codes to include edited_datetime
- [ ] Add pinned to Schematic and SQL tables creation code & make new threads "pinnable" by admins
- [ ] Somehow undo formatting when editing post
- [ ] Store country in database instead of making repeated calls in session tab
- [ ] Still receiving notifications from deleted users (posts) -> Is that really necessary?
- [ ] Maybe add more auth -> One for being able to create threads and one for being able to report. Then allow both of those after posting 10 posts.
- [ ] Refactor pop-ups to be handled through ONE JS function (consider using JSON objects)
- [ ] Redesign moderation panel for /user/
- [ ] Cleanup
- [ ] Styling -> E.g. Box shadows (not all have them yet // Standardize it)
- [ ] Rethink: Does using Javascript for all subsequent loads make sense?
- [ ] Check if permanent deletion is implemented for all non-permenant data (after 60 days)
- [ ] Redesign http error pages
- [ ] Implement languages & customizable language support
- [ ] Format time more nicely -> Maybe format with XXX Ago and in final state the date instead of converting to client's time? Or store in cookie//Session
- [ ] Unsubscribe deleted users from all threads in a recoverable fashion (Set to 2/3 instead of 0/1) -> Or just don't unsubscribe deleted users (and they will keep receiving notifications until hard deletion)
- [ ] Use ` around column and table names for better SQL formatting
- [ ] Moderation rows older than 60 days cannot be undone
- [ ] Test user sessions and having several sessions across different devices
- [ ] Add ToS, Privacy Policy and cookies warning

## Profile/

- [ ] Question: How to handle user settings save? -> Refresh or save pop-up?
- [ ] Fix aspect ratio of preview
- [ ] Decide: Make normal user moderation and moderator moderation hidable (for moderators only)?
- [ ] Make account deletion require password input
- [ ] Add manual number re-sync button for admin 5
- [ ] Scroll up the moderation history when moving from one page to another
- [ ] List of subscribed threads (?)

## Categories, Threads and Posts

- [ ] Check whether thread is deleted before posting message
- [ ] Implement simple thread search
- [ ] Formatting and URL support (and maaaayybbeee external images)
- [ ] Ability to lock threads
- [ ] Upon sending an edit for a post, scroll to that post again.
- [ ] Would it make sense to use the same varible names for e.g. totalItems or gotoPage for threads and topics for the sake of the page menu?
- [ ] Limit users to send max. one message per 15 seconds and 1 thread per week. (moderators exempt)
- [ ] Allow users to edit thread names (with time limit) -> Does this change the slug?

## Account / Menu / Footer

- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Redesign footer and menu

## Authentication / Sessions

- [ ] Decide: Include clearance in the session? (Have already done so on login, but not sure how to verify)
- [ ] Refactor the way it is authenticated (generally speaking)

## Scripts

- [ ] Cleanup
- [ ] Check if location.reload() is necessary across all instances

## API

- [ ] Organize and clean up
- [ ] Re-organize order of functions for deleting/undoing things and standardize them (first count, then history, then soft delete?)
- [ ] Cannot delete threads of already deleted accounts -> FIX THIS (thread count issue)
- [ ] Consider seperating user from post data (reducing file size)
- [ ] Re-organize all include statements to make sure they are only included ONCE (relying on !function_exists possible, but unclean)
- [ ] Standardize introduction of session and when it reads the user_id
