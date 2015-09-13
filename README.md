# Organic Search Analytics Importer
## Features
Import organic search analytics from Google Search Console (Google Webmaster Tools) and Bing Search Keywords (Bing Webmaster Tools) into a local database to keep a historical, local, and combined record of your (past the 30 days that Google offers).

This archive of organic search analytics will allow you to study long term SEO trends and shifts and can help to monitor and gauge performance of a websites over time.

Once setup, this tool will do all of the hard work, allowing the 'savy marketer' or the tech guy that wants to impress the boss to quickly get to data that Google no longer provides.

Reporting features allow you to get to data that makes a difference in your marketing decisions.

## Requirements
- PHP (5.4.1+)
-  MySQL

## Installation
1. Setup a hosting environemnt.  PHP (5.4.1+) and MySQL are required.  If you'd like to install and run this on a local machine, I suggest using [XAMPP](https://www.apachefriends.org/index.html) or [MAMP](https://www.mamp.info/en/).
2. Copy the files from this repository to your new hosting environment.
3. Within the new directory, create a directory named `config`.
4. Add a MySQL Database.  Create it with whatever name you prefer, thought **organic-search-analytics** is suggested.
5. Import database structure.  Using phpMyAdmin or other MySQL tools, import the file: settings/mysql_structure.sql
6. Setup Google API Access
  1. Go to the [Google Developers Console](https://console.developers.google.com/project).
  2. Click the **Create Project** button
  3. Enter a name for this API access project, **organic-search-analytics** is suggested, and click the **Create** button
  4. Once the creation is complete, in the left hand navigation choose **APIs & auth -> APIs**
  5. In the search box, search for **Google Search Console API** and select the option named as such in the search results.
  6. Click the **Enable API** button
  7. In the left hand navigation, choose **APIs & auth -> Credentials**.
  8. Click **Add credentials** and choose **OAuth 2.0 client ID**.
  9. Click the **Configure consent screen** button.
  10. Enter **Organic Search Analytics** (or any other name you prefer) in the **Product Name** field and click **Save**.
  11. Choose **Web application** and click the **Create** button.  Close the popup with your email and secret key.
  12. Click the **Add credentials** button and choose **Service account**.
  13. Choose **P12** and click the **Create** button.  Click the **Ok** button to close the popup.
  14. A file should have downloaed automatically - copy that file to your webserver to the **/config** directory.
  15. Copy the **Email address** from the **Service accounts** section and keep this handy - it's needed for a later step.
7. [Grant access to Google Search Console](http://promincproductions.com/blog/google-api-access-google-search-analytics-from-google-search-console/).
  1. Log into [Google Search Console](https://www.google.com/webmasters/)
  2. For each site you wish to access data from, choose **Manage property -> Add or remove users**.
  3. Click **ADD A NEW USER**
  4. Enter the email address from ste 6.xv in the **User email** field.
  5. Set the **Permision** dropdown to **Full**.
  6. Click the **Add** button.
  7. To grant access to other sites, click **Search Console** in the top left and repeat steps iii through vi, using the same email address in each step.
8. Set configuration.  Access the site on your web server and choose **Settings -> Create Configuration File**.  Fill in the following fields and click **Save Configuration**.
  - Database Host = the domain name of the MySQL database connection.  If you are using XAMPP or MAMP this will be *localhost*.
  - Database Username = the username to connect to the MySQL database.  If you are using XAMPP or MAMP this will be *root*.
  - Database Password = the password to connect to the MySQL database.  If you are using XAMPP or MAMP this will be *root*.
  - Database Name = the MySQL database name as defined in step 4.
  - OOAuth 2.0 Email Address = the Google API Access email address from step 6.xv.
  - OAuth 2.0 P12 File Name = The file name of the P12 file from step 6.xiv.

## Upgrade Information
Upgrading between various versions, in particular version 1.x.x to 2.0.0 requires an additional step.  Instructions on how to perform the upgrade can be found in the ['Upgrading between versions' Wiki page](https://github.com/PromInc/organic-search-analytics/wiki/Upgrading-between-Versions).