# Tarantool JobQueue

[![Build Status](https://travis-ci.org/tarantool-php/jobqueue.svg?branch=master)](https://travis-ci.org/tarantool-php/jobqueue)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tarantool-php/jobqueue/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tarantool-php/jobqueue/?branch=master)
[![Telegram](https://img.shields.io/badge/Telegram-join%20chat-blue.svg)](https://t.me/tarantool_php)


## Installation

The recommended way to install the library is through [Composer](http://getcomposer.org):

```sh
composer require tarantool/jobqueue
```


## Usage

```bash
./jobqueue
./jobqueue help run
```

> Please check the [JobServer](https://github.com/tarantool-php/jobserver) application source code for a more 
complete usage example of this library.


### Running a worker

```bash
./jobqueue run <queue-name> -f worker.log -l debug -e executors.php
```


## Tests

```bash
docker run --name jobqueue -p3301:3301 -v `pwd`:/jobqueue tarantool/tarantool:1.7 tarantool /jobqueue/tests/Integration/queues.lua
vendor/bin/phpunit
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
