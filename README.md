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

### Building Blocks for Scripts and Commands

There are a few simple helpers that will hopefully make it nice and easy to craft new commands and scripts. To start,
we have two superficially similar means of getting data. The first approach loads everything first and then makes it 
available:

```php
foreach ( api()->all( 'contacts' ) as $contact ) {
    // Do something with each contact record
}
```

So, if there are 100,000 contacts in the database all 100,000 will be loaded into memory before we enter the loop. The
following alternative is more memory (and network) efficient, even though the syntax for basic usage is near identical:

```php
foreach ( api()->each( 'tickets' ) as $ticket ) {
    // Do something with each ticket record
}
```

Behind the scenes, no more than 100 (by default) records are obtained via the API. If there are 50,000 tickets in the
LiveAgent database, but we decide to bail out of our loop after 100 iterations, then this will have been the more 
efficient way to do things.

If we only need a single record, we can do:

```php
$record = api()->first( 'tickets' );
```

