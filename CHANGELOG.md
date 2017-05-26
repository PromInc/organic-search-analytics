## 2.5.4 - 2017-05-25
### Bug Fixes
- Revert unintentional deployment of Apache Alias environment propsed in #31 and tracked in #38 and deployed in version 2.5.0 which issues with the report pages.
- Encode special characters in chart data to prevent broken charts when data contains single quotes

## 2.5.3 - 2017-05-17
### Bug Fixes
- Report chart usability and rendering improvements

### Addresses Github Issues
- #51

## 2.5.2 - 2017-04-20
### Added
- CRON functionality

## 2.5.1 - 2017-04-08
### Bug Fixes
- Bing data was not being captured.  Run the upgrade script to correct the database structure for this to capture.
- General fixes that would cause PHP errors
- Fix inaccurate file and line that called the debug logger in the logs
- Log API connection failures and handle errors instead of throwing fatal errors

### Added
- A link to the settings page from the data capture page when no domains have been configured for a search engine.
- warn css class for warning messages

## 2.5.0 - 2017-04-06
### Reports
- Add page data to the report
- Convert query into a link to Google
- Chart bug fixes
- Stylized display of applied report parameters
- Linked relevant applied report parameters
- Link between relevant reports in the report table
- Added tooltips to column headings
- Correct inaccuracy in average_position calculation
- Fix Date reports with granularity of Week, Month, and Year set

### Database updates
- Alter search_analytics table for improved data storage and analysis
	- avg_position from int(11) to float, default NULL
	- avg_position_click from int(11) to float
	- default of null for device_type, country, and query columns
	- add page column
	- change database charset for wider language compatibility
	- add settings table

### Added
- Added page level data in capture from Google and reporting
- Added debug logger
- Added the config and log directories to the repository so it isn’t necessary to add it on setup

### Changed
- Update Font Awesome to 4.7.0
- Minor style updates throughout the site
- Installation sql script to reflect new database structure
- Upgrade script to reflect these database and file system changes

### Fixes/Closes Github Issues
- #13
- #16
- #28
- #31
- #32
- #35

## 2.4.3 - 2016-07-01
### Bug Fix
- Data Capture would not import any records for new installs (non-upgraded) of this tool.  A MySQL error was thrown in the background due to a field not being available in the database.

## 2.4.2 - 2016-03-17
### Bug Fix
- On the data capture page, if multiple domains are present for a search engine, the capture action text may not display for the second or greater domain, and this may result in inaccurate data capture from the search engine.  This fix corrects that issue.

## 2.4.1 - 2016-01-27
### Bug Fix
- On the report page, the chart would fail to display if a query contained a single quote.  Escaped the quote to allow for the chart to reder properly.

## 2.4.0 - 2016-01-12
### Added
- Capture country from Google Search Console on a query level
- Fitler reports by country

## 2.3.7 - 2016-01-11
### Added
- Display date range on report and datepicker for number of days selected

## 2.3.6 - 2016-01-08
### Bug Fix
- The preset date range pickers were adding a day and the report wasn't displaying the accurate date range
- Modifying the date range on the report page for a custom date range was not setting correctly
- Missing a percent sign on the report totals table for CTR

## 2.3.5 - 2016-01-08
### Updated
- jQuery UI - custom theme and options
- Date picker for reports is now a selectable calendar view
- CSS styling for reports

### Bug Fix
- The date range that Google data is avaiable was off by 1 day
- Increased the max execution time for the report page
- Missing closing </div> tag on report page

## 2.3.4 - 2016-01-07
### Updated
- Longer execution time on the data-delete.php page

### Bug Fix
- Query checking if Google needed data didn't exclude Bing
- On data-delete.php clicking on the labels did not select the checkbox.

## 2.3.3 - 2016-01-07
### Added
- A page that allows the user to delete data from Google for dates that the data can be recaptured from Google.
- Session based alerts
- Redirect function in the Core class

## 2.3.2 - 2016-01-07
### Added
- search_type was added to the data-capture-run.php parameters to allow overriding the default Search Types
- Totals for report columns!!
- Sort by links on the report column headings

### Updated
- Changed look of "Save this Report to Quick Links" button on report page
- Changed how the tables on the report page get built from three separate tables into one.

### Bug Fix
- Removed "Query" from displaying as a default "Sort By" option on the report page as it's an invalid option unless the "Group By" is changed.

## 2.3.1 - 2016-01-06
### Fixed
- The data import to the database was failing for queries with special characters.  addslashes was added to the MySQL data to resolve this bug.

## 2.3.0 - 2015-12-14
### Updated
- Some CSS design improvements thanks to contributions from @ctseo
- General code tidyness

### Changed / Added
- A redesigned code structure for the reports that offers several enhancements:
  - Report Custom Links are available from the report page to quickly switich from one report to another
  - Ability to modify a reports parameters from the report display screen
  - Preset date ranges for a report (past 7 days, past 30 days, etc.)
  - Group By.  Group your reports by Query (no longer just Date!!!!!)

### Added
- FontAwesome library
- Added an ID to the <body> tag identifying the page currently loaded.  page_{file_name}

### NOTICE
- This update removes the inc/html/reportQuickLinks.php file that you may have saved report links to.  If so, be sure to back up this file before performing the update as the file may be removed and you will loose all information in this file.
- The file report-custom.php is removed and no longer in use.

## 2.2.0 - 2015-09-30
### Fixed
- Bing API call code cleanup and allow return mode to work correctly
- Reports - bug fix for sorting
- Reports - increased execution time to 5 minutes for large exhaustive queries/reports

### Added
- Reports - Added granularity for reports by day, week, month, or year
- Reports - Pull report by a predefined timeframe like past 7 days, past 30 days, etc.
- Reports - Quick Links functionality that allow saving of a report to easily pull up the same report.
- Created DB table structure for quick links
- A parse query parameters method was added to the Core class
- A reporting class was added to handle funtions in the reports
- A link to upgrade scripts on the home page
- An upgrade script to migrate to version 2.2.0
- Robots.txt file to discourage bots from crawling this site (only necessary if hosted on a publically accessible server, but added by default to all installs)

### Changed
- Moved the organic-search-analytics.sql file out of the site files
- Moved the installation instructions from the readme.txt to the wiki

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

### Fixed
- Upgrade notice was not working right

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
  - Import data from Google Search Analytics for each site
