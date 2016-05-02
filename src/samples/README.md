# PHP AAD Sample App:
This sample app demonstrates how to use the Azure Active Directory via PHP to implement connected accounts and various authentication flows.

## Installation instructions:
1. Install dependencies and generate autoloader with composer:
  * `curl -sS https://getcomposer.org/installer | php`
  * `php composer.phar install`
2. Copy storagedb.dist.sqlite to storagedb.sqlite (in the same folder):
  * Your webserver needs to write to both the the storagedb.sqlite file and the samples folder, so ensure permissions are set to allow this.
3. Copy config.dist.php to config.php (in the same folder).
4. Edit config.php and enter your client ID, client secret, and redirect URI.
  1. The redirect URI will be the URI that points to the redirect.php file in this folder, for example "http://example.com/src/samples/redirect.php". This will depend on your environment.
  2. The redirect URI entered in the config.php and in the Azure management portal must match exactly.
5. Visit the index.php page in your browser, for example http://example.com/src/samples/index.php.
6. Sign up using the email address for an existing Azure AD user. This will create a local account for that user.
7. Link that local account to the appropriate Azure AD account by clicking on the "Link" link on the user's home page that shows up when you sign in.
6. Once the accounts are linked, there are four options to demonstrate authentication flows:
  1. Authorization code flow: This is the common 3 legged OAuth2 flow. This will redirect you to the Azure AD login page if you aren't already logged in to Azure AD and take you through the login process.
  2. Hybrid code flow: This is the quicker ID Token OAuth2 flow. This will redirect you to the Azure AD login page if you haven't already logged in to Azure AD and take you through the login process.
  3. Resource Owner Password Credentials Grant: This allows you to enter Azure AD login credentials directly in the login form and have authentication happen behind the scenes. You will not be redirected to Azure AD to log in.
  4. Local account: You can log in to your local account using the user ID and password you specified when you signed up.
