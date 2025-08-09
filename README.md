# Craft RRULE Wrapper

A **Craft CMS module** that exposes the excellent [rlanvin/php-rrule](https://github.com/rlanvin/php-rrule/wiki) library to **Twig** so you can work with recurrence rules in templates.

> ℹ️ In **PHP**, you can (and should) use the original library classes directly (`RRule\RRule`, `RRule\RSet`). The wrapper mainly exists for **Twig** access via `craft.rrule`.

---

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

---

## RRULE

An **RRULE** describes a single recurrence pattern.  
Reference: **RRule wiki** → https://github.com/rlanvin/php-rrule/wiki/RRule

### 1) Create from **string** and list all occurrences
```twig
{% set rr = craft.rrule.rrule('
DTSTART:20250809T090000Z
RRULE:FREQ=DAILY;COUNT=3
') %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i') }}<br>
{% endfor %}
```
**Expected output**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

```php
use RRule\RRule;

$rr = new RRule("\nDTSTART:20250809T090000Z\nRRULE:FREQ=DAILY;COUNT=3\n");
foreach ($rr->getOccurrences() as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```
> Note: RFC 5545 defines DTSTART and RRULE as separate properties on separate lines; the multi-line example above follows that format.

---

### 2) Create from **array** and list all occurrences
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  COUNT: 4,
  BYDAY: ['MO','WE'],
  DTSTART: date('2025-08-04 09:00')
}) %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i') }}<br>
{% endfor %}
```
**Expected output**
```
2025-08-04 09:00
2025-08-06 09:00
2025-08-11 09:00
2025-08-13 09:00
```

```php
use RRule\RRule;

$rr = new RRule([
    'FREQ' => 'WEEKLY',
    'COUNT' => 4,
    'BYDAY' => ['MO','WE'],
    'DTSTART' => new DateTime('2025-08-04 09:00'),
]);
foreach ($rr->getOccurrences() as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 3) Get occurrences **between** two dates
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: ['MO','FR'],
  DTSTART: date('2025-01-01 09:00')
}) %}
{% set dates = rr.getOccurrencesBetween(date('2025-01-01'), date('2025-02-01')) %}
{% for d in dates %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output**
```
2025-01-03 09:00
2025-01-06 09:00
2025-01-10 09:00
2025-01-13 09:00
2025-01-17 09:00
2025-01-20 09:00
2025-01-24 09:00
2025-01-27 09:00
2025-01-31 09:00
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'WEEKLY',
  'BYDAY' => ['MO','FR'],
  'DTSTART' => new DateTime('2025-01-01 09:00'),
]);
$dates = $rr->getOccurrencesBetween(new DateTime('2025-01-01'), new DateTime('2025-02-01'));
foreach ($dates as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 4) Get the **next** occurrence (after a date, with limit)
> API: `getOccurrencesAfter($after, bool $inclusive = false, int $limit = 0)`
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: 'MO',
  DTSTART: date('2025-01-01 09:00')
}) %}
{% set next = rr.getOccurrencesAfter('now', true, 1)|first %}
Next: {{ next|date('Y-m-d H:i') }}
```
**Expected output** (if “now” is 2025-08-09)
```
Next: 2025-08-11 09:00
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'WEEKLY',
  'BYDAY' => 'MO',
  'DTSTART' => new DateTime('2025-01-01 09:00'),
]);
$next = $rr->getOccurrencesAfter('now', true, 1)[0] ?? null;
echo 'Next: ', $next ? $next->format('Y-m-d H:i') : 'none', PHP_EOL;
```

---

### 5) Get occurrences **before** a date (with limit)
> API: `getOccurrencesBefore($before, bool $inclusive = false, int $limit = 0)`
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 5,
  DTSTART: date('2025-08-09 09:00')
}) %}
{% set list = rr.getOccurrencesBefore(date('2025-08-12 09:00'), true, 2) %}
{% for d in list %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output**
```
2025-08-11 09:00
2025-08-12 09:00
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'DAILY',
  'COUNT' => 5,
  'DTSTART' => new DateTime('2025-08-09 09:00'),
]);
$list = $rr->getOccurrencesBefore(new DateTime('2025-08-12 09:00'), true, 2);
foreach ($list as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 6) `count()` (finite rules)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 3,
  DTSTART: date('2025-08-09 09:00')
}) %}
Count: {{ rr.count() }}
```
**Expected output**
```
Count: 3
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'DAILY',
  'COUNT' => 3,
  'DTSTART' => new DateTime('2025-08-09 09:00'),
]);
echo 'Count: ', $rr->count(), PHP_EOL;
```

---

### 7) Human‑readable summary
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: ['MO','FR'],
  COUNT: 4,
  DTSTART: date('2025-01-01 09:00')
}) %}
{{ rr.humanReadable() }}
```
**Possible output**
```
Every week on Monday, Friday, 4 times
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'WEEKLY',
  'BYDAY' => ['MO','FR'],
  'COUNT' => 4,
  'DTSTART' => new DateTime('2025-01-01 09:00'),
]);
echo $rr->humanReadable(), PHP_EOL;
```

---

### 8) RFC string (serialize)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'MONTHLY',
  BYDAY: 'MO',
  BYSETPOS: 1,
  DTSTART: date('2025-01-01 09:00')
}) %}
{{ rr.rfcString() }}
```
**Example output**
```
RRULE:FREQ=MONTHLY;BYDAY=MO;BYSETPOS=1;DTSTART=20250101T090000Z
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'MONTHLY',
  'BYDAY' => 'MO',
  'BYSETPOS' => 1,
  'DTSTART' => new DateTime('2025-01-01 09:00'),
]);
echo $rr->rfcString(), PHP_EOL;
```

---

### 9) Inspect the parsed rule (`getRule()`)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 2,
  DTSTART: date('2025-08-09 09:00')
}) %}
{{ dump(rr.getRule()) }}
```

```php
use RRule\RRule;

$rr = new RRule([
  'FREQ' => 'DAILY',
  'COUNT' => 2,
  'DTSTART' => new DateTime('2025-08-09 09:00'),
]);
var_dump($rr->getRule());
```

---

## RSET

An **RSET** combines multiple RRULEs, exception rules, and explicit include/exclude dates.  
Reference: **RSet wiki** → https://github.com/rlanvin/php-rrule/wiki/RSet

### 1) Build with an RRULE and an **exclusion** date
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'DAILY', COUNT: 5, DTSTART: date('2025-08-09') }) %}
{% do rset.addExDate(date('2025-08-11')) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d') }}<br>{% endfor %}
```
**Expected output**
```
2025-08-09
2025-08-10
2025-08-12
2025-08-13
```

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRRule([
  'FREQ' => 'DAILY',
  'COUNT' => 5,
  'DTSTART' => new DateTime('2025-08-09'),
]);
$rset->addExDate(new DateTime('2025-08-11'));
foreach ($rset->getOccurrences() as $d) {
    echo $d->format('Y-m-d'), PHP_EOL;
}
```

---

### 2) Add **multiple** RRULEs
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'YEARLY', COUNT: 2, BYDAY: 'TU', DTSTART: date('1997-09-02 09:00') }) %}
{% do rset.addRRule({ FREQ: 'YEARLY', COUNT: 1, BYDAY: 'TH', DTSTART: date('1997-09-02 09:00') }) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output**
```
1997-09-02 09:00
1997-09-04 09:00
1997-09-09 09:00
```

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRRule([
  'FREQ' => 'YEARLY',
  'COUNT' => 2,
  'BYDAY' => 'TU',
  'DTSTART' => new DateTime('1997-09-02 09:00'),
]);
$rset->addRRule([
  'FREQ' => 'YEARLY',
  'COUNT' => 1,
  'BYDAY' => 'TH',
  'DTSTART' => new DateTime('1997-09-02 09:00'),
]);
foreach ($rset->getOccurrences() as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 3) Get occurrences **between** dates
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'WEEKLY', BYDAY: ['MO','FR'], DTSTART: date('2025-01-01 09:00') }) %}
{% set dates = rset.getOccurrencesBetween(date('2025-01-01'), date('2025-02-01')) %}
{% for d in dates %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output**
```
2025-01-03 09:00
2025-01-06 09:00
2025-01-10 09:00
2025-01-13 09:00
2025-01-17 09:00
2025-01-20 09:00
2025-01-24 09:00
2025-01-27 09:00
2025-01-31 09:00
```

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRRule([
  'FREQ' => 'WEEKLY',
  'BYDAY' => ['MO','FR'],
  'DTSTART' => new DateTime('2025-01-01 09:00'),
]);
$dates = $rset->getOccurrencesBetween(new DateTime('2025-01-01'), new DateTime('2025-02-01'));
foreach ($dates as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 4) Get occurrences **after**/**before** with a **limit**
> API: `getOccurrencesAfter($after, bool $inclusive = false, int $limit = 0)`
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'DAILY', DTSTART: date('2025-08-09 09:00') }) %}
{% set next3 = rset.getOccurrencesAfter('now', true, 3) %}
{% for d in next3 %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output** (if “now” is 2025-08-09 09:00)
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRRule([
  'FREQ' => 'DAILY',
  'DTSTART' => new DateTime('2025-08-09 09:00'),
]);
$next3 = $rset->getOccurrencesAfter('now', true, 3);
foreach ($next3 as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 5) Add/remove explicit dates (RDATE/EXDATE)
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRDate(date('2025-08-10 12:00')) %}
{% do rset.addExDate(date('2025-08-11 09:00')) %}
{% do rset.addRRule({ FREQ: 'DAILY', COUNT: 3, DTSTART: date('2025-08-09 09:00') }) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d H:i') }}<br>{% endfor %}
```
**Expected output**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-10 12:00
2025-08-12 09:00
```

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRDate(new DateTime('2025-08-10 12:00'));
$rset->addExDate(new DateTime('2025-08-11 09:00'));
$rset->addRRule([
  'FREQ' => 'DAILY',
  'COUNT' => 3,
  'DTSTART' => new DateTime('2025-08-09 09:00'),
]);
foreach ($rset->getOccurrences() as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

## ⚠ Infinite recurrences (what they are & how to avoid)

An RRULE (or an RSET containing one) is **infinite** if it omits both `COUNT` and `UNTIL`. Iterating an infinite rule with `getOccurrences()` will **never finish**.

**Infinite rule example (don’t do this)**
```twig
{% set rr = craft.rrule.rrule('FREQ=DAILY;DTSTART=20250809T090000Z') %}
{# ❌ Will never end: #}
{# for d in rr.getOccurrences() %}{{ d|date('Y-m-d H:i') }}{% endfor #}
```

**Safe alternatives**
```twig
{# 1) Add COUNT #}
{% set r1 = craft.rrule.rrule('FREQ=DAILY;COUNT=5;DTSTART=20250809T090000Z') %}

{# 2) Limit with a window #}
{% set r2 = craft.rrule.rrule('FREQ=DAILY;DTSTART=20250809T090000Z') %}
{% set window = r2.getOccurrencesBetween('2025-08-09','2025-08-12') %}

{# 3) Cap using getOccurrencesAfter with a limit #}
{% set next3 = r2.getOccurrencesAfter('2025-08-09 09:00', true, 3) %}
```

---

## References
- **RRule**: https://github.com/rlanvin/php-rrule/wiki/RRule  
- **RSet**: https://github.com/rlanvin/php-rrule/wiki/RSet  
- **RFC 5545** (iCalendar RRULE): https://datatracker.ietf.org/doc/html/rfc5545