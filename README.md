# LiveAgent Tools

Command line tools and utilities to make various LiveAgent operations easier.

### Setup

* Install dependencies via `composer install`
* Configure your `config.json` file
* The latter can be based upon `config.sample.json`

### Add Contacts to Group

To add a list of contacts to a group, you can use the `bin/add-contacts-to-group` command. Basic usage:

```
bin/add-contacts-to-group user-group-a /file/path/list-of-email-addresses.txt
```

The first argument can either be a group name or a group ID. The second argument should be the path to a plain text list
of email addresses (one email address per line).