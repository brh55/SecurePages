# SecurePages - Currently in Development

A simple method of creating secure pages throughout the entire site, through system settings and an assigned secure template inspired Ladage's PageLocker.

Licenses under GPLv2, modifiying this code must have the GPLv2 or later licenses attached.

## Dependencies

This fork requires Bob's Ray CAPTCHA[https://github.com/BobRay/Captcha] in order to function properly.
Directions [HAVE CHANGED]

## Installation
A zip is available if you want to use this for your MODx site. Feel free to reach out to me and I'll be gladly to upload it to a host. This is currently not complete, thus installation instructions through git are not provided.

## Dependencies 
This fork requires Bob's Ray CAPTCHA[https://github.com/BobRay/Captcha] in order to function properly.
 
## Directions
1. Install the pageLocker - Fork ZIP
2. Create a new resource called "formResource"
  1. Assign the template called "secureFormTemplate"
  2. Check Hide from Menu
  3. Publish the Resource
  4. This template can be restyled by duplicating your subpage, and entering the form code to your desired location.
3. Edit the template called the "SecureTemplate"
  1. Copy the code from the SecureTemplate, now style the form as you please
    1. You can copy your subpage code and enter into the secure template, and paste the "Secure Form Code" to your desired location
4. Create two new system settings
  1. Click System > System Setting
  2. Create the first setting named "Secure Page Password". 
  ```
    1. Key: secure_password
    2. Name: Secure Page Password
    3. Description: Set password for the secure pages of your site.
    4. Field Type: Textfield
    5. Namespace: Core
    6. Area Lexicon Entry: authentication
    7. Value: setyourpasswordhere
  ```
  3. Create the second setting named "Secure Template ID"
  ```
    1. Key: secure_template
    2. Name: Secure Template ID
    3. Description: Set the ID of your secure template.
    4. Field Type: Textfield
    5. Namespace: Core
    6. Area Lexicon Entry: authentication
    7. Value: ID of your secure template (numeric)
  ```
5. Go to Elements Tab > Plugins > Click on PageLocker Plugins
  1. Click on the Properities Tab
  2. Click on Default Properties Locked to unlock the Default Properities
  3. Edit the following values to the new values:
    1. formResourceID = ID of the formResource
  4. Save
6. Now assign the secureTemplate to any page you want to be secured
7. Test pages and ensure it is working properly.
