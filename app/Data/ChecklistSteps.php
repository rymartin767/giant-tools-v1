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
                    [
                        'text' => 'Charge iPad',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Minimum 80% battery required',
                        ],
                    ],
                ],
            ],
            '1.1' => [
                'title' => 'Pre-Duty: Flight Planning',
                'description' => 'Ensure all items are completed prior to duty start',
                'items' => [
                    'Download Flight Plan',
                    'Verify SOB',
                    [
                        'text' => 'Review Fuel Planning',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'WX at DEST+ALT (TS) = MORE FUEL!',
                        ],
                        'images' => [
                            [
                                'title' => 'Fuel Planning Reference',
                                'url' => '/images/fuel.PNG',
                                'alt' => 'Fuel planning chart',
                                'caption' => 'Review fuel requirements and reserves',
                            ],
                        ],
                    ],
                ],
            ],
            '2.0' => [
                'title' => 'NP15: Aircraft Log Inspection Procedure',
                'description' => 'Captain Aircraft Log - Check in accordance with the FOM',
                'items' => [
                    [
                        'text' => 'Verify Tail # (Logbook, OFP, & actual aircraft)',
                    ],
                    'Check Aircraft Log for write-ups and mx history',
                    [
                        'text' => 'Check Deferred Items',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Deferred Items Review',
                            'content' => 'Reference the DDG for any operating limitations or crew operating (O) procedures as a result of MEL/CDL/NEF items identified on the flight plan and/or in the Aircraft Log. If inoperative/missing equipment is not entered in the Aircraft Log or not posted with INOP Placards describing the limitation, contact maintenace or Maintenance Control.',
                        ],
                    ],
                    [
                        'text' => 'Check Last Autoland (30 days maximum)',
                    ],
                    [
                        'text' => 'Check Daily or Transit check',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Daily/Transit Check Requirements',
                            'content' => 'A daily check must be accomplished with an Airworthiness Release. The Daily Check is valid for 48 hours and is not allowed to expire in flight. The daily check will expire 48 hours from the Block Out Time of the flight following the daily check.',
                        ],
                    ],
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
            /*
            '3.0' => [
                'title' => 'ETOPS Procedures',
                'description' => 'Extended-range Twin-engine Operational Performance Standards',
                'items' => [
                    [
                        'text' => 'Verify ETOPS fuel requirements',
                        'conditions' => ['etops' => [true]],
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'ETOPS Fuel Requirements',
                            'content' => 'Ensure sufficient fuel for the critical fuel scenario, including holding at the most distant suitable airport. Verify fuel loaded matches or exceeds the ETOPS required fuel on the flight plan.',
                        ],
                    ],
                    [
                        'text' => 'Confirm ETOPS alternate airports',
                        'conditions' => ['etops' => [true]],
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Verify designated ETOPS alternates are available and meet weather requirements',
                        ],
                    ],
                    [
                        'text' => 'Review MEL items for ETOPS compliance',
                        'conditions' => ['etops' => [true]],
                    ],
                    [
                        'text' => 'ETOPS entry point noted',
                        'conditions' => ['etops' => [true]],
                    ],
                    [
                        'text' => 'ETOPS exit point noted',
                        'conditions' => ['etops' => [true]],
                    ],
                ],
            ],
            */
        ];
    }
}
