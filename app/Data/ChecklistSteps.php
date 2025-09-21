<?php

namespace App\Data;

class ChecklistSteps
{
    final public static function getSteps(): array
    {
        return [
            '1.0' => [
                'title' => 'Pre-Duty Checklist Items',
                'description' => 'Ensure all items are completed prior to duty start',
                'items' => [
                    'Sync Comply 360',
                    'Update JeppFD Pro',
                    'Review NOTAMS',
                    'Charge iPad',
                ],
            ],
            '1.1' => [
                'title' => 'Pre-Duty: Flight Planning',
                'description' => 'Ensure all items are completed prior to duty start',
                'items' => [
                    'Download Flight Plan',
                    'Verify SOB',
                    'Review Fuel Planning',
                    'If WX @ DEST + ALT include TS: Order more fuel',
                ],
            ],
            '2.0' => [
                'title' => 'NP15: Aircraft Log Inspection Procedure',
                'description' => 'Captain Aircraft Log - Check in accordance with the FOM',
                'items' => [
                    'Verify Tail # (Logbook, OFP, & actual aircraft)',
                    'Check Aircraft Log for write-ups and mx history',
                    'Check Deferred Items',
                    'Check Last Autoland (30 days maximum)',
                    'Check Daily or Transit check',
                    'Check PDSC (if applicable)',
                    'Check Verification flight (if applicable)',
                ],
            ],
            '2.1' => [
                'title' => 'NP15: Final Document Procedure',
                'description' => 'Ensure required documents are onboard prior to BEFORE START checklist',
                'items' => [
                    'Aircraft Logbook',
                    'Flight Plan / Dispatch Release',
                    'Loadsheet (Weight and Balance)',
                    'NOTOC, or AMC equivalent * - As applicable',
                    'General Declarations * - As applicable',
                    'Wx and NOTAMs',
                    'Track Message * - As applicable',
                    'Permit to Proceed * - As applicable',
                    'Fuel Receipts * - As applicable',
                ],
            ],
            '2.2' => [
                'title' => 'NP30: Before Takeoff Procedure',
                'description' => 'Engine warm up and final taxi checks',
                'items' => [
                    [
                        'text' => 'Engine warm up requirements: GE • eng oil temp ^ scale',
                        'conditions' => ['engines' => ['GE']],
                    ],
                    [
                        'text' => 'Engine warm up requirements: PW • eng oil temp ^ lower amber band',
                        'conditions' => ['engines' => ['PW']],
                    ],
                    [
                        'text' => '[PAX] "CABIN CREW, BE SEATED FOR TAKEOFF" approx two mins prior to taking the active runway. After PA notification, verify chime received prior to takeoff. Note: If the Cabin Crew wishes to talk to the cockpit, multiple chimes will sound',
                        'conditions' => ['config' => ['PAX']],
                    ],
                    [
                        'text' => '[FRT] Notify the supernumeraries to prepare for takeoff',
                        'conditions' => ['config' => ['FRT'], 'supernumeraries' => [true]],
                    ],
                    [
                        'text' => '[FRT] No supernumeraries - skip notification',
                        'conditions' => ['config' => ['FRT'], 'supernumeraries' => [false]],
                    ],
                    [
                        'text' => 'PF updates takeoff briefing as needed',
                        'conditions' => [],
                    ],
                    [
                        'text' => 'CTR FUEL PUMPS OFF for takeoff (< 5000 lbs)',
                        'conditions' => [],
                    ],
                    [
                        'text' => 'Set WX/TERR as required',
                        'conditions' => [],
                    ],
                ],
            ],
            '2.3' => [
                'title' => 'NP35: Takeoff Procedure',
                'description' => 'Final runway entry and takeoff checks',
                'items' => [
                    'Verify runway and runway entry point',
                    'Verify first fix',
                    'MIN FUEL CHECK',
                    'XPNDR: TA/RA',
                    'Verify TFC on both NDs & final approach clear of conflicting traffic',
                ],
            ],
        ];
    }
}
