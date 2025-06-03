# Forum

PHP x MySQL Forum (using infinityFree)

CreateThread: ~6 MySQL queries
GetThreads: 2 Queries
GetPosts: 2 Queries

# Moderation

Clearance level is an integer.
0 = Regular user
1 = Moderator (Can delete posts)
2 = Moderator (The above and can delete threads)
3 = Admin (The above and can ban users)
4 = Admin (The above and can promote and demote all of the below)
5 = Super Admin (The above and can promote and demote level 4 admin)

# TODO:

- [ ] Finalize profile settings page
- [ ] Make posting a new post focus on this post
- [ ] There are still some mismatches between menu displaying you are logged in while having no posting privliges
- [ ] Change initial load (php) and subsequent loads (js) to match each other.
- [ ] Change the /functions/require/posts.php to match the /function/require/threads.php and adjust accordingly in thread.php and api/getThreads.php
- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Delete button on profile page

- [ ] Make clearance levels, moderation tools, report system, history stack, etc.
- [ ] Add ToS, Privacy Policy and cookies warning
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Make notification system
- [ ] Implement thread search (?)

- [ ] Style everything
- [ ] Get mysql table creation code for README.md
