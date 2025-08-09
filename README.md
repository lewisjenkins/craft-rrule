# Craft RRule Wrapper

A Craft CMS module that exposes the [php-rrule](https://github.com/rlanvin/php-rrule) library directly to Twig templates.  
It provides access to all core `RRule` and `RSet` methods so you can generate and work with recurrence rules without writing PHP plugins yourself.

For the complete API, see the [php-rrule wiki](https://github.com/rlanvin/php-rrule/wiki).

---

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

Once installed, the module is available in Twig as:

```twig
craft.rrule.rrule()   {# Create an RRule instance #}
craft.rrule.rset()    {# Create an RSet instance #}
```

---

## RRULE Examples

### 1. Create from array (Twig)
```twig
{% set rr = craft.rrule.rrule({
    freq: 'daily',
    count: 3,
    dtstart: '2025-08-09 09:00 America/New_York'
}) %}

{% for d in rr.getOccurrences() %}
    {% set tz = d.timezone %}
    {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```
**Expected output:**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

**PHP equivalent:**
```php
use RRule\RRule;

$rr = new RRule([
    'FREQ' => 'DAILY',
    'COUNT' => 3,
    'DTSTART' => new DateTime('2025-08-09 09:00', new DateTimeZone('America/New_York')),
]);

foreach ($rr as $d) {
    echo $d->format('Y-m-d H:i T') . "\n";
}
```

---

### 2. Create from RFC-style string (Twig)
```twig
{% set rr = craft.rrule.rrule('
DTSTART;TZID=America/New_York:20250809T090000
RRULE:FREQ=DAILY;COUNT=3
') %}

{% for d in rr.getOccurrences() %}
    {% set tz = d.timezone %}
    {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```
**Expected output:**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

**PHP equivalent:**
```php
use RRule\RRule;

$rr = new RRule("DTSTART;TZID=America/New_York:20250809T090000\nRRULE:FREQ=DAILY;COUNT=3");

foreach ($rr as $d) {
    echo $d->format('Y-m-d H:i T') . "\n";
}
```

---

## RSET Examples

### 1. Multiple rules in a set (Twig)
```twig
{% set rset = craft.rrule.rset() %}

{% do rset.addRRule({
    freq: 'yearly',
    count: 2,
    byday: 'TU',
    dtstart: '1997-09-02 09:00 America/New_York'
}) %}

{% do rset.addRRule({
    freq: 'yearly',
    count: 1,
    byday: 'TH',
    dtstart: '1997-09-02 09:00 America/New_York'
}) %}

{% for d in rset.getOccurrences() %}
    {% set tz = d.timezone %}
    {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```
**Expected output:**
```
1997-09-02 09:00
1997-09-04 09:00
1997-09-09 09:00
```

**PHP equivalent:**
```php
use RRule\RSet;

$rset = new RSet();

$rset->addRRule([
    'FREQ' => 'YEARLY',
    'COUNT' => 2,
    'BYDAY' => 'TU',
    'DTSTART' => new DateTime('1997-09-02 09:00', new DateTimeZone('America/New_York')),
]);

$rset->addRRule([
    'FREQ' => 'YEARLY',
    'COUNT' => 1,
    'BYDAY' => 'TH',
    'DTSTART' => new DateTime('1997-09-02 09:00', new DateTimeZone('America/New_York')),
]);

foreach ($rset as $d) {
    echo $d->format('Y-m-d H:i T') . "\n";
}
```

---

## Notes
- All Twig examples set `tz = d.timezone` so the correct timezone is used in formatting.  
- You can pass either:
  - An **array** with keys matching the RRule spec (`freq`, `count`, `dtstart`, etc.)  
  - An **RFC-style string** with `DTSTART` and `RRULE` on separate lines.  
- Infinite rules (e.g., no `COUNT` or `UNTIL`) may produce **very large datasets**. Always use methods like `getOccurrencesBetween()` or `count()` to avoid excessive loops.

For the complete method list and advanced usage, see the  
[php-rrule RRule docs](https://github.com/rlanvin/php-rrule/wiki/RRule) and [php-rrule RSet docs](https://github.com/rlanvin/php-rrule/wiki/RSet).
