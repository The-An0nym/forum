# Forum

PHP x MySQL Forum (using infinityFree)

CreateThread: ~6 MySQL queries
GetThreads: 2 Queries
GetPosts: 2 Queries

# TODO:

- Implement user profile page
- Keep logout or change to fetch()
- Make initial load with PHP, all subsequent calls with Fetch (-> Makes refreshing faster by ~100ms. Subsequent calls via API are ~100ms faster than refreshing) -> Decide whether to actually implement this (Would in the end result in a lot of duplicate code)
- Check if directory (thread or category) exists before adding posts/threads to them

- Make clearance levels and moderation tools
- Add ToS, Privacy Policy and cookies warning
- Adress, full legal name, etc. (pattern)
- Make notification system
- Implement thread search (?)
- Add user profile picture system (max. 2mb)

- Style everything
- Get mysql table creation code for README.md
