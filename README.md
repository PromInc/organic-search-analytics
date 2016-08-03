# Organic Search Analytics Importer
## Version 2.3.7 Now Available!!! (January 12th, 2016)
**Official Release Version**
> This is a highly recommended update for all users.  This update fixes a bug where queries from Google that contain special characters are not being saved to the database.  It is suggested that you update to this version immediately.

> Once updated, there is a **Delete Data** option found on the home page.  This page can be used to delete data that was affected by this bug.  Once deleted, you can re-capture data for that date.  This process ensures your data is as accurate as possible.

## Version 2.4.0
**Current Master Branch**
At this time, the current master branch has been updated to 2.4.0 and not commited as an official release.

This update adds the country field, as requested in Issue #11.

> **Notice** You will need to run an update script from the home page for this version to run smoothly as it needs to update the database.  Go to the homepage of your installation, choose **Upgrade Scripts** and then choose **Run Update for Version 2.x.x to 2.4.0**.  Failure to run this script will result in undesired results across the utility.

> The update script performs a database update to add the *country* column.


## Features
Import organic search analytics from Google Search Console (Google Webmaster Tools) and Bing Search Keywords (Bing Webmaster Tools) into a local database to keep a historical, local, and combined record of your (past the 30 days that Google offers).

This archive of organic search analytics will allow you to study long term SEO trends and shifts and can help to monitor and gauge performance of a websites over time.

Once setup, this tool will do all of the hard work, allowing the 'savy marketer' or the tech guy that wants to impress the boss to quickly get to data that Google no longer provides.

Reporting features allow you to get to data that makes a difference in your marketing decisions.  The reports are flexible and can be saved for ease of access/use.

## Requirements
- PHP (5.4.1+)
- MySQL

## Installation
[Installation Instructions can be found in the Wiki](https://github.com/PromInc/organic-search-analytics/wiki/Installation).

## Upgrade Information
Upgrading between certain versions requires an additional step.  Instructions on how to perform the upgrade can be found in the ['Upgrading between versions' Wiki page](https://github.com/PromInc/organic-search-analytics/wiki/Upgrading-between-Versions).

## Additional Information
More information can be found in the [Wiki](https://github.com/PromInc/organic-search-analytics/wiki).

## Related Information
- [Google's definition of it's Search Analytics metrics](https://support.google.com/webmasters/answer/7042828)
