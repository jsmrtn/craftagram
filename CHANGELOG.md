# craftagram Changelog

## 2.0.0 - 2022-06-30
- Update cron example in README.txt (thanks @Ottergoose)
- Casing of plugin title in sidebar
- Craft 4 support

## 1.4.5 - 2021-06-02
### Fixed
- Don't output video with the image tag (#41)

## 1.4.4 - 2021-02-17
### Changed
- Add basic auth to headless endpoint. Existing installations will remain unsecured until the option is enabled.

### Fixed
- Allow album children (#38)
- Stop error on headless endpoint (#37)

## 1.4.3 - 2021-01-12
### Added
- Add API endpoint for headless installations (#36)
- Add preview of feed to settings page

### Fixed
- Fix pagination action, as it previously assumed that the default site had an ID of `1`

### Changed
- Update recommendations for pagination to use the `paginate->cursors->after` string, rather than needing to pass the entire `next` url

## 1.4.2 - 2021-01-06
### Fixed
- Fix refresh token endpoint

## 1.4.1 - 2021-01-05
### Fixed
- Fix migration

## 1.4.0 - 2021-01-04
### Added
- Allow multi-site authentications (#30)

## 1.3.1 - 2020-11-27
### Fixed
- Allow Craft to run from sub-directory (#32)

## 1.3.0 - 2020-10-22
### Added
- Add console action for refreshing tokens

## 1.2.6 - 2020-09-30
### Added
- Output verbose error messaging into log if no data is returned (#24)
### Fixed
- Set column type to `text` for `longAccessToken` for migrated installs (#28)

## 1.2.5 - 2020-09-14
### Fixed
- Remove limit hiding button, as env var can be any length

## 1.2.4 - 2020-08-04
### Changed
- Allow Long Access Token to be edited

## 1.2.3 - 2020-07-30
### Fixed
- Change minimum CraftCMS requirement

## 1.2.1 - 2020-07-27
### Fixed
- Fix redirect URL from auth function to prevent errors if `allowAdminChanges` is set to false

## 1.2.0 - 2020-07-22
### Added
- App ID and App Secret can now be added as environment variables.
- craftagram is available as a Control Panel section to allow authorisations if `allowAdminChanges` is set to false
- Long-access token is now saved into the database to prevent creating issues with `project.yaml` file

### Changed
- Logic changes to better handle of authorisation hand-off.

## 1.1.1 - 2020-07-10
### Fix
- Correctly remove trailing slash from `baseUrl` for `redirect_uri`.

## 1.1.0 - 2020-07-08
### Added
- Add `getProfileMeta` variable to return followers, following, and profile picture.

### Fixed
- Remove trailing slash from `baseUrl` for `redirect_uri`.

## 1.0.7 - 2020-07-01
### Fixed
- Fix release.

## 1.0.6 - 2020-06-30
### Changed
- Tweak plugin icon

### Fixed
- Fix #6. Thanks @JJimmyFlynn.

## 1.0.5 - 2020-04-20
### Fixed
- Fix #3 but for both env and aliases. Thanks @bymayo.

## 1.0.4 - 2020-04-01
### Fixed
- Fix #3, #4. Thanks @darylknight.

## 1.0.3 - 2020-03-24
### Fixed
- Fix #1. Thanks @Saboteur777.

## 1.0.2 - 2020-03-11
### Fixed
- Fail better if the `getInstagramFeed` service doesn't return any data

## 1.0.1 - 2020-03-11
### Changed
- Trigger authorization on button click instead of showing a URL

## 1.0.0 - 2020-03-11
### Added
- Initial deployment
