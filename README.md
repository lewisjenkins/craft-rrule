# Craft RRule Wrapper

A Craft CMS 5 module providing Twig access to the full [php-rrule](https://github.com/rlanvin/php-rrule) library.  
This allows you to create and work with recurrence rules (RRULE) and recurrence sets (RSET) directly in Twig templates.

The full API is available in PHP via the original library — this module simply exposes it to Twig without modifying behaviour.

---

## RRULE

### Twig Example

```twig
{% set rr = craft.rrule.rrule({
  freq: 'daily',
  count: 3,
  dtstart: date_create('2025-08-09 09:00', timezone('America/New_York'))
}) %}

{% for d in rr.getOccurrences() %}
    {% set tz = d.timezone %}
    {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

**Expected output**:
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

---

### Twig Example (from string)

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

**Expected output**:
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

---

### PHP Example

```php
use RRule\RRule;

$rr = new RRule([
    'FREQ' => 'DAILY',
    'COUNT' => 3,
    'DTSTART' => new DateTime('2025-08-09 09:00', new DateTimeZone('America/New_York'))
]);

foreach ($rr as $d) {
    echo $d->format('Y-m-d H:i T') . PHP_EOL;
}
```

---

## RSET

### Twig Example

```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({
  freq: 'daily',
  count: 2,
  dtstart: date_create('2025-08-09 09:00', timezone('America/New_York'))
}) %}
{% do rset.addDate(date_create('2025-08-15 09:00', timezone('America/New_York'))) %}

{% for d in rset.getOccurrences() %}
    {% set tz = d.timezone %}
    {{ d|date('Y-m-d H:i', tz) }}<br>
{% endfor %}
```

**Expected output**:
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-15 09:00
```

---

### PHP Example

```php
use RRule\RSet;

$rset = new RSet();
$rset->addRRule([
    'FREQ' => 'DAILY',
    'COUNT' => 2,
    'DTSTART' => new DateTime('2025-08-09 09:00', new DateTimeZone('America/New_York'))
]);
$rset->addDate(new DateTime('2025-08-15 09:00', new DateTimeZone('America/New_York')));

foreach ($rset as $d) {
    echo $d->format('Y-m-d H:i T') . PHP_EOL;
}
```

---

## Full API

For the complete list of available methods and options, see the original library documentation:

- [RRule documentation](https://github.com/rlanvin/php-rrule/wiki/RRule)
- [RSet documentation](https://github.com/rlanvin/php-rrule/wiki/RSet)
