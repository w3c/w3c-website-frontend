<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Sabre\VObject\Component;
use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\TimeZoneUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class IcalExporter
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $event
     *
     * @return VCalendar
     * @throws Exception
     */
    public function exportEvent(array $event): VCalendar
    {
        $vcalendar = new VCalendar();
        $vcalendar->add('NAME', $event['title']);
        $vcalendar->add('X-WR-CALNAME', $event['title']);

        $start   = $event['start']->setTimezone(new DateTimeZone($event['tz']));
        $end     = $event['end']->setTimezone(new DateTimeZone($event['tz']));
        $created = $event['postDate']->setTimezone(new DateTimeZone('UTC'));
        $updated = $event['dateUpdated']->setTimezone(new DateTimeZone('UTC'));

        $tz = self::getVtimezone(
            $event['tz'],
            $start->getTimestamp(),
            $end->getTimestamp()
        );
        $vcalendar->add($tz);

        $url = $this->router->generate('app_events_show', [
            'type' => $event['type']['slug'],
            'slug' => $event['slug'],
            'year' => $event['year']
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $vevent = $vcalendar->createComponent(
            'VEVENT',
            [
                'UID'           => $event['uid'],
                'SUMMARY'       => $event['title'],
                'DTSTART'       => $start,
                'DTEND'         => $end,
                'DESCRIPTION'   => $url . "\n\n" . $event['excerpt'],
                'STATUS'        => 'CONFIRMED',
                'CREATED'       => $created,
                'LAST-MODIFIED' => $updated,
                'DTSTAMP'       => $updated,
            ]
        );

        $vcalendar->add($vevent);

        return $vcalendar;
    }

    /**
     * Returns a VTIMEZONE component for a Olson timezone identifier
     * with daylight transitions covering the given date range.
     *
     * @param string   $tzid Timezone ID as used in PHP's Date functions
     * @param int|null $from Unix timestamp with first date/time in this timezone
     * @param int|null $to   Unix timestamp with last date/time in this timezone
     *
     * @return false|Component A Sabre\VObject\Component object representing a VTIMEZONE definition
     *               or false if no timezone information is available
     * @throws Exception
     */
    private static function getVtimezone(string $tzid, ?int $from = 0, ?int $to = 0)
    {
        if (!$from) {
            $from = time();
        }
        if (!$to) {
            $to = $from;
        }

        try {
            $tz = new DateTimeZone($tzid);
        } catch (Exception $e) {
            return false;
        }

        $year        = 86400 * 360;
        $transitions = $tz->getTransitions($from - $year, $to + $year);

        $vcal_creator = new VCalendar();
        $vt           = $vcal_creator->createComponent('VTIMEZONE');
        $vt->TZID     = $tz->getName();

        $std = null;
        $dst = null;
        foreach ($transitions as $i => $trans) {
            $cmp = null;

            if ($i == 0) {
                // but remember the offset for the next TZOFFSETFROM value
                $tzFrom = $trans['offset'] / 3600;
                // skip the first entry if there's more than one
                if (count($transitions) > 1) {
                    continue;
                }
            }

            if ($trans['isdst']) {
                $tDst = $trans['ts'];
                $dst   = $vcal_creator->createComponent('DAYLIGHT');
                $cmp   = $dst;
            } else {
                $tStd = $trans['ts'];
                $std   = $vcal_creator->createComponent('STANDARD');
                $cmp   = $std;
            }

            if ($cmp) {
                $dt     = new DateTimeImmutable($trans['time']);
                $offset = $trans['offset'] / 3600;

                $cmp->DTSTART      = $dt->format('Ymd\THis');
                $cmp->TZOFFSETFROM = sprintf(
                    '%s%02d%02d',
                    $tzFrom >= 0 ? '+' : '-',
                    abs(floor($tzFrom)),
                    ($tzFrom - floor($tzFrom)) * 60
                );
                $cmp->TZOFFSETTO   = sprintf(
                    '%s%02d%02d',
                    $offset >= 0 ? '+' : '-',
                    abs(floor($offset)),
                    ($offset - floor($offset)) * 60
                );

                if (!empty($trans['abbr'])) {
                    $cmp->TZNAME = $trans['abbr'];
                }

                $tzFrom = $offset;
                $vt->add($cmp);
            }

            // we covered the entire date range
            if ($std && $dst && min($tStd, $tDst) < $from && max($tStd, $tDst) > $to) {
                break;
            }
        }

        // add X-MICROSOFT-CDO-TZID if available
        $microsoftExchangeMap = array_flip(TimeZoneUtil::$microsoftExchangeMap);
        if (array_key_exists($tz->getName(), $microsoftExchangeMap)) {
            $vt->add('X-MICROSOFT-CDO-TZID', $microsoftExchangeMap[$tz->getName()]);
        }

        return $vt;
    }
}
