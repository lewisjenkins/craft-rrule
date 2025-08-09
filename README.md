# Craft RRULE Wrapper

A **Craft CMS module** that exposes the excellent [rlanvin/php-rrule](https://github.com/rlanvin/php-rrule/wiki) library to **Twig** so you can work with recurrence rules in templates.

> ℹ️ In **PHP**, you can (and should) use the original library classes directly (`RRule\RRule`, `RRule\RSet`). The wrapper mainly exists for **Twig** access via `craft.rrule`.

---

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

---

## Timezone Handling

When working with rules that include a timezone, you should explicitly set the timezone before formatting in Twig:

```twig
{% for d in rr.getOccurrences() %}
  {% set tz = d.timezone %}
  {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

---

## RRULE

An **RRULE** describes a single recurrence pattern.  
Reference: **RRule wiki** → https://github.com/rlanvin/php-rrule/wiki/RRule

### 1) Create from **string** and list all occurrences
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
**Expected output**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

```php
use RRule\RRule;

$rr = new RRule("DTSTART;TZID=America/New_York:20250809T090000\nRRULE:FREQ=DAILY;COUNT=3");
foreach ($rr->getOccurrences() as $d) {
    echo $d->format('Y-m-d H:i'), PHP_EOL;
}
```

---

### 2) Create from **array** and list all occurrences
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  COUNT: 4,
  BYDAY: ['MO','WE'],
  DTSTART: date('2025-08-04 09:00', 'America/New_York')
}) %}
{% for d in rr.getOccurrences() %}
  {% set tz = d.timezone %}
  {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

---

### 3) Get occurrences **between** two dates
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: ['MO','FR'],
  DTSTART: date('2025-01-01 09:00', 'America/New_York')
}) %}
{% set dates = rr.getOccurrencesBetween(date('2025-01-01'), date('2025-02-01')) %}
{% for d in dates %}
  {% set tz = d.timezone %}
  {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

---

### 4) Get the **next** occurrence
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: 'MO',
  DTSTART: date('2025-01-01 09:00', 'America/New_York')
}) %}
{% set next = rr.getOccurrencesAfter('now', true, 1)|first %}
{% if next %}
  {% set tz = next.timezone %}
  Next: {{ next|date('Y-m-d H:i', tz) }}
{% endif %}
```

---

### 5) Get occurrences **before** a date (with limit)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 5,
  DTSTART: date('2025-08-09 09:00', 'America/New_York')
}) %}
{% set list = rr.getOccurrencesBefore(date('2025-08-12 09:00'), true, 2) %}
{% for d in list %}
  {% set tz = d.timezone %}
  {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

---

## RSET Examples

### 1) Build with an RRULE and an **exclusion** date
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'DAILY', COUNT: 5, DTSTART: date('2025-08-09', 'America/New_York') }) %}
{% do rset.addExDate(date('2025-08-11', 'America/New_York')) %}
{% for d in rset.getOccurrences() %}
  {% set tz = d.timezone %}
  {{ d|date('Y-m-d', tz) }}<br>
{% endfor %}
```

---

## ⚠ Infinite recurrences

Avoid creating rules without `COUNT` or `UNTIL`, as they can loop forever.
