## 2.1.0 - 2015-09-18
### Changed
- Reports - first domain is checked by default
- Reports - added query match type (exact or broad)
- jQuery and jQuery UI added to the code repository as opposed to loading from 3rd party servers

### Added
- jqPlot library
- Reports - Graph added
- Reports - Sort by
- Reports - Sort Direction

## 2.0.2 - 2015-09-15
### Fixed
- Bing API was failing under some hosting environments.

## 2.0.0 - 2015-09-14
### Added
- Bing Webmaster Tools integration.
- Import Bing Search Keywords to the database.  Bing groups their keyword data on a weekly basis.
- An alternate method of returning Google and Bing data is avaiable by setting a query paramater (mode=return) on the data-capture-run.php request.
- Ability to customize the request to the Google API via query paramters on the data-capture-run.php request.  groupBy needs to be implemented yet, else all other API features are available.
- Text file with version number.

### Changed
- The type (data source) needs to be specified in the request to data-capture-run.php to indicate if data is to be captured from Google Search Console or Bing Webmaster tools. This is automatically set when using the GUI, but when making a manual request to data-capture-run.php the query parameter type needs to be set to either googleSearchAnalytics or bingSearchKeywords.

### Fixed
- Basic code cleanup where needed.
- Class descriptions cleanup where needed.

## 1.4.0 - 2015-09-03
### Changed
- Configuration settings can be set via the website under the Settings page as opposed manually creating/editing a file on the server.
  - README has been updated to reflect these changes.

### Added
- Alert boxes were added for messages upon saving, etc.

## 1.3.1 - 2015-09-02
### Fixed
- The list of domains on the report page uses the sites pulled from Google Search Console instead of the settings table.

## 1.3.0 - 2015-09-02
### Changed
- Instead of manually adding sites in the Settings page, it now pulls in the list of sites that have API access from Google Search Console.

## Fixed
- The import all button swithces from "Stop" to "Run" once all of the imports have been completed.
- The link to the Data Capture page on the homepage was incorrect.

## Added
- A quick links file has been added to the reporting page.  At this time it is very rudimentary - this is a file where you can hard-code in any quick links for reports you may want to access on a routine basis.

## 1.2.0 - 2015-08-31
### Added
- Basic reporting features added.

## 1.1.1 - 2015-08-28
### Fixed
- JS for the settings page was not loading, which created issues when updating and saving settings.

## 1.1.0 - 2015-08-28
### Added
- Import all button to import all days with data missing at once.

## 1.0.1 - 2015-08-27
### Added
- Initial release of repository with basic functionality.
  - Add sites in settings
<<<<<<< HEAD
  - Import data from Google Search Analytics for each site
=======
  - Import data from Google Search Analytics for each site
>>>>>>> eb79563da220f230f2fd7c3c8e8aea284076b523
