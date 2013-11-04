jForms Google Spreadsheets Plugin
======
A jForms plugin to save submitted data to a Google Spreadsheet.

## Instructions

Install the plugin in Joomla, then configure & publish it in your plugin manager. 
You will need to enter your Google username and password.

### Important information for GMail Users
If you are using GMail (rather than Google Apps) you will get blocked on the first call of the API. 
To fix this, open a jForms module in your module manager after setting up the plugin. You will see an error. 
Now head over to [gmail.com] and sign in, you should see a notification about access to your account. 
Authorise access and you're good to go ;)

Open the jForms module which you wish to use in your module manager, click on the options tab and scroll to the bottom. 
You will see a new set of options called "Google Spreadsheets" with the following options:

**Save to spreadsheet**  
Choose whether to send the form data to a Google Spreasheet. If you select no all following settings will be ignored.

**Select spreadsheet**  
Select which spreasheet to save to form data to.

## Requirements

- PHP 5
- Joomla 2.5+
- jForms Plugin Framework (plg_system_jforms)

## Roadmap

- Use OAuth for connecting to Google account
- Display error messages using Joomla API
