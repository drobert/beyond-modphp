# Beyond Mod_php

Samples of architecture paradigms for php beyond the limitations of multi-process mod_php

## What Is This?

Mod_php and other multi-process php paradigms have a number of big limitations. Among them:
* multiple processes cannot log/write to the same file simultaneously without problems or data loss
* asynchronous operations are not really possible
* mod_php provides minimial native logging options. typically this is limited to:
  * error_log()
  * STDOUT
  * syslog

Modern enterprise web applications have moved from batch processing of hourly/daily log files to 
real-time event streams. To demonstrate doing this in an efficient, non-blocking manner while still 
retaining batch logging functionality, this project will show a progression from a straighforward 
mod_php application using the Silex microframework to one with the following architecture:

* mod_php/silex for standard client request processing (e.g. a web page)
  * Monolog for a powerful set of logging options based on PSR-3
* reactphp - a node.js-like event-loop framework for php for logging and asynchronous event handling
* zeromq - sockets with superpowers, for IPC

The benefits of this architecture extend well beyond this trivial logging example. Aside from streaming 
log events, other use cases lend themselves to this paradigm as well. For example:
* offline cache invalidation
* async notifcations 
* performance profiling and data collection

This application will be extended over time to demonstrate more of its potential. But for now, this will 
simply demonstrate utilizing a reactphp-based logging daemon for php.

## Getting Started

### Requirements

A PHP 5.4+ installation, with:
* zmq support (http://zeromq.org/bindings:php)

Install: 
> php ./composer.phar install

``

### URLs:

To load a user by name from the default web server on port 8000:
> http://localhost:8000/hello/USERNAME

e.g.

>  http://localhost:8000/hello/bill

## Running:

### Stand-alone, classic mod_php style app

```
cd src/web/std
php -S localhost:8000

### Logging via zmq/reactphp

Note, startup order is _not_ important.
In two separate terminals:

```
mkdir src/web/react/logs
php src/web/react/app_logger.php
```

```
cd src/web/zmqlog
php -S localhost:8000
```

In this application, any name shorter than three characters will be logged to src/web/react/logs/app_logger_short.log
All requests will be logged to src/web/react/logs/app_logger_all.log

