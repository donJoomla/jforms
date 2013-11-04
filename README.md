jForms Module
======
A slick AJAX contact form using Joomla's inbuilt jForms.


## Instructions

Install the module in Joomla, then open it in the module manager. Publish it to the position and pages you require, then click the options tabs where you will see following settings:

### Basic Options

**Form**  
Select a form XML to use for your module. To use your own forms upload them to `/modules/mod_jforms/forms/`.

**Introduction text**  
Enter text to display above the form

**Thank you message**  
Enter a message which will be displayed after successful form submission.

**Submit button text**  
Enter the text you would like to appear on the submit button.

### Email Settings

**Send email**  
Choose whether to send the form data via email. If you select no all following settings will be ignored.

**Email destination**  
Enter a valid email address where the form data will be sent to. 
To use a user entered email address enter the field names you specified your form XML for example `{email}`.

**Email subject**  
Enter a subject for the email that will be sent.
To use a user entered subject enter the field names you specified your form XML for example `{subject}`.

**Email text**  
Create the template of the email. 
Use placeholders for the form data using the field names you specified your form XML for example `{email}`.
To display all the form data in a list use `{fields}`.

### AJAX Settings

**Use Ajax to send form**  
Select this option to submit the form without reloading the page.

**Custom Functions**  
Execute custom JavaScript functions. To use your own functions upload them to `/modules/mod_jforms/functions/`.


## Requirements

- PHP 5
- Joomla 2.5+

## Roadmap

Planned features in no perticular order.

- Language file for multilingual support
- Drag & drop XML form creator
- Full documentation
- Developer wiki
