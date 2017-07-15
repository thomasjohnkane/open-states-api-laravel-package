<?php
namespace Tkane\OpenStates;

use \GuzzleHttp\Client as Client;

use GuzzleHttp\Exception\ClientException;
use Tkane\OpenStates\Exceptions\OpenStatesApiException;

class OpenStatesApi
{
    const QUERY_BILLS_URL = 'bills/';
    const QUERY_LEGISLATORS_URL = 'legislators/';
    const QUERY_COMMITTEES_URL = 'committees/';

    /**
     * @var
     */
    public $status;

    /**
     * @var null
     */
    private $key;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * OpenStatesApi constructor.
     *
     * @param null $key
     */
    public function __construct()
    {
        $this->key = env('OPEN_STATES_KEY');

        $this->client = new Client([
            'base_uri' => 'https://openstates.org/api/v1/',
        ]);
    }


    /**
     * Query Bills Request to the open states api.
     *
     * @param $input
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    public function getBills($input, $params = [])
    {
        $this->checkKey();

        // EX usage: $bills = OpenStates::getBills('tx');

        // $params['state'] = $input;

        $response = $this->makeRequest(self::QUERY_BILLS_URL, $params);

        return $this->convertToCollection($response);
    }

    /**
     * Query Bill Request to the open states api.
     *
     * @param $input
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    public function getBill($bill_id, $params = [])
    {
        $this->checkKey();

        // EX usage: $bills = OpenStates::getBills('tx');

        // $params['state'] = $input;

        $response = $this->makeRequest(self::QUERY_BILLS_URL . '/' . $bill_id, $params);

        return $this->convertToCollection($response);
    }

    /**
     * Query Legislators Request to the open states api.
     *
     * @param $input
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    public function getLegislators($input, $params = [])
    {
        $this->checkKey();

        // EX usage: $bills = OpenStates::getLegislators('tx');

        $params['state'] = $input;

        $response = $this->makeRequest(self::QUERY_LEGISLATORS_URL, $params);

        return $this->convertToCollection($response);
    }

    /**
     * Query Committees Request to the open states api.
     *
     * @param $input
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    public function getCommittees($input, $params = [])
    {
        $this->checkKey();

        // EX usage: $bills = OpenStates::getLegislators('tx');

        $params['state'] = $input;
        $params['q'] = 'Public Education Committee';

        $response = $this->makeRequest(self::QUERY_COMMITTEES_URL, $params);

        return $this->convertToCollection($response);
    }

    /**
     * Query Committee Request to the open states api.
     *
     * @param $input
     * @param array $params
     *
     * @return \Illuminate\Support\Collection
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    public function getCommittee($committee_id, $params = [])
    {
        $this->checkKey();

        // EX usage: $bills = OpenStates::getBills('tx');

        // $params['state'] = $input;

        $response = $this->makeRequest(self::QUERY_COMMITTEES_URL . '/' . $committee_id, $params);

        return $this->convertToCollection($response);
    }

    /**
     * @param $uri
     * @param $params
     *
     * @return mixed|string
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    private function makeRequest($uri, $params)
    {
        $options = [
            'query' => [
                'apikey' => $this->key,
            ],
        ];

        $options['query'] = array_merge($options['query'], $params);
        try {
            $response = json_decode($this->client->get($uri, $options)
                                             ->getBody()->getContents(), true);
        } catch (ClientException $e) {
            throw new OpenStatesApiException($e->getMessage());
        }

        if (array_key_exists('status', $response)) {
            $this->setStatus($response['status']);

            if ($response['status'] !== 'OK') {
               // throw new OpenStatesApiException("Response returned with status: "
               //     . $response['status']);
            }
        }


        return $response;
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Support\Collection
     */
    private function convertToCollection(array $data, $index = null)
    {
        $data = collect($data);

        if ($index) {
            $data[$index] = collect($data[$index]);
        }

        return $data;
    }

    /**
     * @param mixed $status
     */
    private function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param null $key
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws \Tkane\OpenStates\Exceptions\OpenStatesApiException
     */
    private function checkKey()
    {
        if (!$this->key) {
            throw new OpenStatesApiException('API KEY is not specified.');
        }
    }
}
