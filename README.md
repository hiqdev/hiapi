# HiAPI

**HiAPI - base project for building API**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/hiapi/v/stable)](https://packagist.org/packages/hiqdev/hiapi)
[![Total Downloads](https://poser.pugx.org/hiqdev/hiapi/downloads)](https://packagist.org/packages/hiqdev/hiapi)
[![Dependency Status](https://www.versioneye.com/php/hiqdev:hiapi/dev-master/badge.svg)](https://www.versioneye.com/php/hiqdev:hiapi/dev-master)

HiAPI is a base project for building API.

## Installation

The preferred way to install this yii2-extension is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require "hiqdev/hiapi"
```

or add

```json
"hiqdev/hiapi": "*"
```

to the require section of your composer.json.

## Overview

- **Endpoint** - describes an endpoint:
    - name and description
    - availability: web, console, ...
    - authorization
    - input it takes - **Command**
    - output it returns - **Result**
    - execution conveyor: **Middlewares** and **Action**
- **Command** - describes and holds input data
- **Action** - takes **Command** and returns **Result**
- **Result** - describes and holds output data

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2017, HiQDev (http://hiqdev.com/)
