[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) [![made-with-VSCode](https://img.shields.io/badge/Made%20with-VSCode-1f425f.svg)](https://code.visualstudio.com/) [![PHP](https://img.shields.io/badge/PHP-7.4.16-green.svg)](https://www.php.net/) [![PHP](https://img.shields.io/badge/Composer-1.10.21-green.svg)](https://getcomposer.org/) [![Wordpress](https://img.shields.io/badge/Wordpress-5.7.1-green.svg)](https://wordpress.org/download/)

# inpsyde-users
A Wordpress plugin to load and show user list and details from API call.
The page is shown using custom URI that can be set up on the wordpress settings page.

## Implementation Decisions

### Wordpress

I'm using Bedrock for the Wordpress package. I upgrade it to latest version.

### Namespace, Classes, and File Structure

I have arranged file structure based on it's type and functions. Using namespace for php codes. `Plugin.php` for the plugin functionality, and `UsersAPI.php` for handling API call. All of these files are loaded using autoload from main plugin file on plugin root directory.

### Admin Page

I added admin page for the plugin, it will be shown right below the dashboard once activated. It will also add rewrite rule for custom endpoint URL with default value: my-lovely-users-table.

### Custom Endpoint URL 

For the custom endpoint URL, I'm using wp_rewrite to add custom rewrite rule. But apparently wordpress doesn't write rule to .htaccess when permalink_structure is empty. When permalink_structure is empty, any rewrite rule won't work, including custom url endpoint unless I force write it using custom code (not using wordpress built in function, not recommended). The solution is when activating the custom url endpoint, I had to activate permalink_structure, then adding my custom endpoint URL.

The custom endpoint URL is customizable from wp admin page.

### API Call

For API call there is one class containing two main methods: users and user with user id argument. These methods is used for fetching users and single user data. I'm using guzzle package for API call since it's easy to use.

Caching data will be explained below.

### Frontend and Views

There are three files for the view. First and second is the template to show users and user data on page load in form of full html. This way, when user tried to access the url (/users or /users?id=1) it will be available.
The third template is for json format, this will only used for showing single user data.

On the frontend javascript, since there are also react in action, the existing table will be rebuilt in react component along with it's action.

So we have two alternative, if javascript is working then everything will go as planned:
* Showing users table (page load).
* React js take over the process and rebuild the table with element click events.
* When ID/Name/Username clicked will do ajax load of json user data from wordpress and show it in a popup window. There will be loading spinner when it happened.

If javascript not working (rarely happen):
* Showing users table (page load).
* ID/Name/Username clicked will load single user detail page.

### Caching

I decide to go with backend and frontend caching.
For backend caching I'm using wordpress built it transient: set_transient and get_transient with 300 seconds expiration. Loaded data will be stored for 300 seconds before it's expired.

For frontend caching, since I'm using react, for each user data loaded I'm storing it on react state so the next time user click user link it will show existing data from react state.

## Error Handling

API call has been handled using try/catch mechanism. When it's timeout, server issue, or user not available will return error code and message. This message will be shown on frontend.

### Code Style Standard

I have added Inpsyde code style standard package to the project. I'm using auto phpcs extension on my editor so it will be automatically checked on typing. Since I'm using windows, there are some warning that I can't get rid of due to line break character so I had to disable phpcs on that line. There are also few unavoidable 'else' warning left. Everything else should comply with the code style.

To run phpcs for this project can simply type:
```
composer phpcs
```
Or for windows
```
composer phpcsw
```

### Unit Test

I have added unit test for the project, testing main method for API call and implementing Brain Monkey & Mockery on the other test case. I learn alot about Brain Monkey and I get to know how to use it in a project, really make things easier especially for wordpress testing.

I have added phpunit.xml for unit test config, to run unit test can simply type:
```
composer test
```
Or for windows
```
composer testw
```

PS: Please be aware, if api call test giving 404 it's possible that jsonplaceholder.typicode.com is down.

## Project built with libraries, tools, and requirements:

* Built in Windows 10 with VisualStudio Code
* PHP 7.4
* Composer 1.10.21
* Wordpress installed using Bedrock package updated to version 5.7.1
* PHP CodeSniffer, auto phpcs using PHP Sniffer extension on VSCode
* PHPUnit with Brain Monkey for unit testing
* Using SCSS with Live SASS Compiler extension on VSCode
* Javascript using React v17.0.2 without build tooling
* Caching using wp & local caching on state
* Guzzle

PS: I'm currently not using jQuery, but I'm using react instead. The reason is, I wanted to showcase a bit of my react knowledge since jQuery has been used many times.

### Requirement

* guzzlehttp/guzzle": "^7.3"
* inpsyde/php-coding-standards": "^0.13.4",
* squizlabs/php_codesniffer": "^3.6",
* brain/monkey": "2.*",
* phpunit/phpunit": "^9.5"

## Installation

* Put the plugin files on plugins directory or pull from repository
* Do `composer install`
* Run `composer test`
* Run `composer phpcs`
* Go to wordpress wp-admin plugins page, activate the plugin
* Go to InpsydeUsers admin setting page
* Click go to url, or customize url

## Endpoint used:

```
'http://jsonplaceholder.typicode.com/users'

'http://jsonplaceholder.typicode.com/users/'.$id
```

*I hope this endpoint will be up when this project is tested.