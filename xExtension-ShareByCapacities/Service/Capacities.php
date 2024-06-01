<?php

declare(strict_types=1);

class FreshExtension_ShareByCapacities_Service_Capacities
{
    public function __construct(
        private string $apiToken,
        private string $spaceId,
    )
    {
    }

    public function shareEntryAsWeblink(FreshRSS_Entry $entry): bool
    {
        /**
         * send a post to https://api.capacities.io/save-weblink
         * Use curl to send the post request
         * 
         * This is an example of the body
         * 
         * {
         *   "spaceId": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
         *   "url": "https://example.com/",
         *   "titleOverwrite": "My custom title",
         *    "tags": [
         *      "todo",
         *      "important"
         *    ],
         *    "mdText": "This is a **note** for the weblink"
         * }
         *
         **/

        $data = [
            'spaceId' => $this->spaceId,
            'url' => $entry->link(),
            'titleOverwrite' => $entry->title(),
            'tags' => ['FreshRSS'],
        ];
        
        $ch = curl_init('https://api.capacities.io/save-weblink');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiToken,
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if ($response === false) {
            return false;
        }
        
        return true;
    }
}