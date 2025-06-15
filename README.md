# Forum

PHP x MySQL Forum (using infinityFree)

This is a personal project of mine to learn PHP. It is to be expected that:

- There are bugs
- Things are not optimized
- There are missing features
- It is unsafe

# Moderation

Clearance level is an integer.
0 = Regular user
1 = Moderator (Can delete posts)
2 = Moderator (The above and can delete threads)
3 = Admin (The above and can ban users)
4 = Admin (The above and can promote and demote all of the below)
5 = Super Admin (The above and can promote and demote level 3 to level 4 admin. Can also view all deleted posts and deleted accounts and restore them (within the time limit))

### Deletion

Posts will be soft deleted and only completely deleted after a set amount of time (60 days)

Threads that are deleted will follow the same logic. Posts under that thread will not be flagged as "deleted" though. Only moderators with clearance levels 2 or above can view deleted threads (for anyone else it will show an error message)

# Structure (tables)

### History

id -> id of post, thread or user
sender_id -> id of moderator
type -> 0 = post, 1 = thread, 2 = user
judgement -> 0 = deleted, 1 = restored, 2 = demoted, 3 = promoted
datetime -> when

### Posts

`POSTS` DELETED flag
VAL BINARY Meaning
0 -> 0000 -> Not deleted
1 -> 0001 -> User deleted
2 -> 0010 -> Mod deleted
4 -> 0100 -> Thread deleted
8 -> 1000 -> Ban/account deleted

Report types
0 = post
1 = thread
2 = user

# TODO:

- [ ] Finalize profile settings page
- [ ] Make posting a new post focus on this post
- [ ] There are still some mismatches between menu displaying you are logged in while having no posting privliges
- [ ] Change initial load (php) and subsequent loads (js) to match each other. (and maybe rethink system)
- [ ] Change the /functions/require/posts.php to match the /function/require/threads.php and adjust accordingly in thread.php and api/getThreads.php
- [ ] Make login or sign-up pop-up hide when sign-up/login is pressed respectively
- [ ] Rework moderation history

- [ ] Add ToS, Privacy Policy and cookies warning
- [ ] Adress, full legal name, etc. (pattern)
- [ ] Make notification system
- [ ] Implement thread search (?)

- [ ] Style everything
- [ ] Get mysql table creation code for README.md
