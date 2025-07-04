# TODO:

## General

- [ ] Cleanup
- [ ] PHP guard clauses using die()
- [ ] Styling
- [ ] Rethink: Does using Javascript for all subsequent loads make sense?
- [ ] Implement permanent deletion (after 60 days)
- [ ] Redesign http error pages
- [ ] Implement languages

## Profile/

- [ ] Notifications page
- [ ] Settings HTML
- [ ] Show user sessions
- [ ] Decide: Make normal user moderation and moderator moderation hidable (for moderators only)?
- [ ] Make account deletion require password input
- [ ] Add manual number re-sync button for admin 5
- [ ] Scroll up the moderation history when moving from one page to another

## Categories and Threads

- [ ] Make posting a new post focus on this post
- [ ] Check whether thread is deleted before posting message
- [ ] Allow (autherized) moderators to view deleted threads
- [ ] Rewrite forum to be 1-indexed (not 0-indexed)
- [ ] Implement simple thread search
- [ ] Scroll up the thread/threads list when moving from one page to another
- [ ] Separate location from Menu (Home -> Topic -> Thread)

## Account / Menu / Footer

- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Add ToS, Privacy Policy and cookies warning
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Make notification system
- [ ] Redesign footer and menu

## Authentication / Sessions

- [ ] Refactor file to be a function
- [ ] Delete expired sessions by chance (e.g. 1/100 chance)
- [ ] Decide: Include clearance in the session?
- [ ] Refactor the way it is authenticated
