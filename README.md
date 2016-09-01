# PHP library to interact with CPAMANAGER.NET API

This is a basic library written in PHP which allows you to interact with the CPAMANAGER.NET API. If you have any questions
or suggestions please do not hesitate and contact us by sending email to support@cpamanager.net or visit http://cpamanager.net

## How to use the library?

The first thing you have to do is to obtain the API KEY. You can do this by registering a new account. Later thigs are getting even simpler :)

The basic call for creating and changing the status of a transaction could look like below:

#### Create a new transaction
```php
// Create the ApiClient object. Remember that the domain has to exist (be added) to CPAMANAGER.NET account.
$client = new ApiClient('APIKEY', 'yourdomain.com');

// Create a new transaction with the global ID. The global ID must reflect the ORDER ID in your shop.
$result = $client->createTransaction('GID-123');
```

#### Change status for a transaction

```php
// Change the current status of the transaction.
$client = new ApiClient('APIKEY', 'yourdomain.com');
$client->setTransactionStatus('GID-123', ApiClient::TRANSACTION_STATUS_ACCEPTED);

// Or
$client = new ApiClient('APIKEY', 'yourdomain.com');
$client->setTransactionStatus('GID-123', ApiClient::TRANSACTION_STATUS_REFUSED);

// Or
$client = new ApiClient('APIKEY', 'yourdomain.com');
$client->setTransactionStatus('GID-123', ApiClient::TRANSACTION_STATUS_NEW);
```

#### Fetch recently created transactions (20)

```php
// Fetch transactions.
$client = new ApiClient('APIKEY', 'yourdomain.com');
$client->fetchTransactions();
```
