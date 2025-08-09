# Craft RRULE Wrapper

A Craft CMS module wrapping [rlanvin/php-rrule](https://github.com/rlanvin/php-rrule) to provide recurrence rule functionality in Twig and PHP.

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

## Usage

### In Twig
```twig
{% set rr = craft.rrule.rrule('FREQ=WEEKLY;BYDAY=MO,WE,FR;DTSTART=20250101T090000Z') %}
{% for d in rr.getOccurrencesBetween('2025-02-01','2025-02-28') %}
  {{ d|date('Y-m-d H:i') }}<br>
{% endfor %}
```

### In PHP
```php
use lewisjenkins\rrulewrapper\RRuleWrapper;
$api = RRuleWrapper::getInstance()->rrule;
$rr  = $api->rrule('FREQ=MONTHLY;BYDAY=MO;BYSETPOS=1;DTSTART=20250101T09Z');
$occ = $rr->getOccurrencesBetween('2025-01-01','2025-06-30');
```
