# SlimRSS
Micro CMS application that also allows the user to configure RSS channels to pull posts from.

Database Configuration
----------------------
The database tables used in the application and their structures are located in schema.sql.

rssfeed.php
-----------
This file is supposed to run as a cron job to fetch the RSS feed from the channels added in the admin panel.