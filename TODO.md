# TODO:

## Safety & Authentication
- [ ] Add rate limiting to all write queries via session
- [ ] Limit ability to create new threads to users who have more than 20 posts
- [ ] Make account deletion require password input
- [ ] Refactor authentication to be more secure and/or efficient if necessary and possible.

## Readme & Documentation
- [ ] Update schematic and SQL table creation code to reflect all new changes - e.g. pinned threads, edited_datetime
- [ ] Add function documentation to all JavaScript and PHP functions

## Continue implementation of
- [ ] Language support

## Bug fixes & Redesign
- [ ] Redesign moderation panel for /user/ page
- [ ] Redesign moderation history and report history for /profile/moderation/ page
- [ ] Scroll up moderation history and report history page upon loading next or previous page (and/or implement proper pages)
- [ ] Standardize the Box Shadow styling (not all components have it yet either)
- [ ] Redesign HTTP error pages
- [ ] Redesign footer with better patterns, i.e. contact us, privacy policy link, ToS link, etc. 
- [ ] Time format - it may be good to display XXX Ago for dates less than 3 days and the dd/mm/yyyy date for dates older than 3 days.
- [ ] Standardize JavaScript pop-up function(s)
- [ ] Use ` around all eligible names in SQL queries
- [ ] Disallow undo for moderation rows older than 60 days
- [ ] Disallow anyone from posting on deleteed threads
- [ ] Upon sending an edit for a post, scroll to said post again
- [ ] Standardize wording of "clearance" and "auth", preferably "auth"
- [ ] Standardize order of functions for deleting/undoing items - NOTE, the order has an impact on functionality, i.e. wrong order *will* break things.
- [ ] Cannot delete threads of already deleted accounts (thread count issue occurs)
- [ ] Track include statements -> If a file is included multiple times, consider rethinking structure (collissions are avoided due to usage of require_once)

## Ideas & Rethink
- [ ] Allow users to edit the thread name of their own threads (once a week except after creation) which changes Slug
- [ ] Store country in database to reduce amount of API calls
- [ ] Deleting notifications initiated by deleted users.
- [ ] Does using JavaScript for all subsequent loads make sense given the amount of data being transferred relative to the total site size?
- [ ] How user settings are saved: A) Refresh page upon any save or B) Save all components individually and give a success message
- [ ] Add manual re-sync button for Super Admin to sync all numeric values (API already implemented)
- [ ] Simple thread search (searching titles only)
- [ ] Ability to lock threads
- [ ] Implement or reject: Using the same variable names (totalItems, gotoPage) for /threads/ and /topics/ for the page menu
- [ ] Include clearance in session?

## To check
- [ ] Permenant deletion for all non-permenant data (60 days)
- [ ] Expired session deletion (5 days, 1/100 chance)
- [ ] Current implementation of (un)subscribe feature for deleted users. Goal: No change to subscription entry and delete the subscription upon hard deletion.
- [ ] Session across multiple different devices.
- [ ] Is `location.reload()` necessary across all instances (JavaScript files)

## To add...
- [ ] ToS, Privacy Policy, Cookies pop-up...
