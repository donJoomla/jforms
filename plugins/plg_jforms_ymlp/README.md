jForms YMLP Plugin
======
A jForms plugin to save submitted data to a YMLP mailing list.

## Instructions

Install the plugin in Joomla, then configure & publish it in your plugin manager. 
You will need your username and API key from ymlp.com

Open the jForms module which you wish to use in your module manager, click on the options tab and scroll to the bottom. You will see a new set of options called "Your Mailing List Provider" with the following options:

**Save to mailing list**  
Choose whether to use the YMLP plugin for this form. If you select no all following settings will be ignored.

**Overrule blacklist**  
If you select yes the user will be subscribed, even if he or she previously unsubscribed from your mailing list.

**Select mailing list**  
Choose which mailing list to subscribe the user to.

**Map YMLP fields**  
Map your form fields to the fields in YMLP. Use the field names you specified your form XML for example `{email}`. 
Notice how the name is in squiggly brackets, this tells the plugin to use the data from the form. 
To pass a static value to, for example, track where they signed up from, just enter standard text eg. `Website Contact Form`.

## Roadmap

- Allow selecting mailing list based on form field (list, radio or checkboxes)
