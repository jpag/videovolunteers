<?php

class VideoVoices_Client_Youtube
{
	const BATCH_REQUEST_URI = 'http://gdata.youtube.com/feeds/api/videos/batch';
	const XMLNS_ATOM_URI = 'http://www.w3.org/2005/Atom';
	const XMLNS_BATCH_URI = 'http://schemas.google.com/gdata/batch';
	const XMLNS_MEDIA_URI = 'http://search.yahoo.com/mrss/';
	const XMLNS_GD_URI = 'http://schemas.google.com/g/2005';
	const XMLNS_YT_URI = 'http://gdata.youtube.com/schemas/2007';

	function getEntries($ids)
	{
		$entries = array();

		foreach (array_chunk($ids, 50) as $chunk) {
			$payload = '<feed
				xmlns="' . self::XMLNS_ATOM_URI . '"
				xmlns:media="' . self::XMLNS_MEDIA_URI . '"
				xmlns:batch="' . self::XMLNS_BATCH_URI . '"
				xmlns:yt="' . self::XMLNS_YT_URI . '">
				<batch:operation type="query"/>';
			foreach ($chunk as $id) {
				$payload .= '<entry><id>http://gdata.youtube.com/feeds/api/videos/' . $id . '</id></entry>';
			}
			$payload .= '</feed>';

			$context = stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-Type: text/xml',
					'content' => $payload,
				)
			));

			$response = file_get_contents(self::BATCH_REQUEST_URI, false, $context);

			$xml = new SimpleXMLElement($response);
			$feed = $xml->children(self::XMLNS_ATOM_URI);
			if (!$feed) {
				continue;
			}

			foreach ($feed->entry as $entry) {
				$batch = $entry->children(self::XMLNS_BATCH_URI);
				if ($batch) {
					$batchAtts = $batch->status->attributes();
					if (isset($batchAtts['code']) && $batchAtts['code'] == 200) {
						$media = $entry->children(self::XMLNS_MEDIA_URI);
						$yt = $entry->children(self::XMLNS_YT_URI);
						$ytAtts = $yt->statistics->attributes();
						$entries[basename($entry->id)] = array('title' => (string)$media->group->title, 'count' => (string)$ytAtts['viewCount']);
					}
				}
			}
		}

		return $entries;
	}
}
