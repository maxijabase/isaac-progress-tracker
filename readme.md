# Isaac Progress Tracker

A web app to track your Binding of Isaac: Rebirth achievement progress.

## Features

- **Steam login**: Sign in with your Steam account - no need to manually find your Steam ID
- Sync progress with your Steam account achievements
- Track progress for all characters, items, and secrets
- Fully client-side - no user data stored on server, no login required, no ads or tracking
- Mobile-friendly responsive design

## Fork Improvements

This is a fork of [donwilson/isaac-progress-tracker](https://github.com/donwilson/isaac-progress-tracker) with:

- **Steam login**: One-click login with Steam instead of manually entering your Steam ID
- **Profile display**: Shows your Steam avatar and username in the navbar
- **Latest achievements**: Added Repentance+ online achievements (Play Online, Win Online, Win Online Daily, Item Descriptions)
- **Progress toggle**: Switch between "My Progress" (incomplete only) and "All Achievements" views
- **Better notifications**: Clean toast notifications instead of browser alert popups
- **Loading indicators**: Visual feedback when syncing with Steam
- **Improved filters**: Search, filter, and sort work correctly in both view modes
- **Friendlier errors**: Helpful error messages when Steam sync fails

## Data Sources

- Achievement data from [Rebirth Achievement Helper](https://theriebel.de/tboirah/)
- Wiki data from [Binding of Isaac: Rebirth Wiki](https://bindingofisaacrebirth.fandom.com/)
