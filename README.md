# Organic Search Analytics Importer
Import organic search analytics from Google Search Console (Google Webmaster Tools) into your local database to keep a historical record of your data (past the 30 days that Google offers)

## Installation
1. Setup a hosting environemnt.  PHP and MySQL are required.  If you'd like to install and run this on a local machine, I suggest using [XAMPP](https://www.apachefriends.org/index.html) or [MAMP](https://www.mamp.info/en/).
  - NOTE: The [Google API states](https://developers.google.com/api-client-library/php/) it requires PHP 5.2.1 or higher, however in testing I found that it does NOT work on PHP  5.2.17.  I then upgraded to PHP 5.4.43 and all works great - so I'm not entirely sure what the first stable version of PHP this will operate is.
2. Copy the files from this repository to your new hosting environment.
3. Add a MySQL Database.  Create it with whatever name you prefer, thought **organic-search-analytics** is suggested.
4. Import database structure.  Using phpMyAdmin or other MySQL tools, import the file: settings/mysql_structure.sql
5. Setup Google API Access
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
6. Setup config file.  Open **/config/config.php** in a text editor and fill in your credentials.
  1. DB_CONNECTION_DOMAIN = the domain name of the MySQL database connection.  If you are using XAMPP or MAMP this will be *localhost*.
  2. DB_CONNECTION_USER = the username to connect to the MySQL database.  If you are using XAMPP or MAMP this will be *root*.
  3. DB_CONNECTION_PASSWORD = the password to connect to the MySQL database.  If you are using XAMPP or MAMP this will be *root*.
  4. DB_CONNECTION_DATABASE = the MySQL database name as defined in step 3.
  5. OAUTH_CREDENTIALS_EMAIL = the Google API Access email address from step 5.xv.
  6. OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME = The file name of the P12 file from step 5.xiv.
7. Grant access to Google Search Console.
  1. Log into [Google Search Console](https://www.google.com/webmasters/)
  2. For each site you wish to access data from, choose **Manage property -> Add or remove users**.
  3. Click **ADD A NEW USER**
  4. Enter the email address from ste 5.xv in the **User email** field.
  5. Set the **Permision** dropdown to **Full**.
  6. Click the **Add** button.
  7. To grant access to other sites, click **Search Console** in the top left and repeat steps iii through vi, using the same email address in each step.