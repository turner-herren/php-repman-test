<?php

namespace logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use config\CloudWatchConfig;
use Exception;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\JsonFormatter;

class CloudWatchLogger
{
    private string $region;
    private string $accessKey;
    private string $secret;
    private string $version;
    private string $logGroupName;
    private string $streamName;
    private int $logBatchSize;
    private int $logRetentionDate;
    private int $logLevel;

    /**
     * @param CloudWatchConfig $cloudWatchConfig
     * @param int $logRetentionDate
     * @param int $logLevel
     * @param int $logBatchSize
     */
    public function __construct(CloudWatchConfig $cloudWatchConfig, int $logRetentionDate = 30, int $logLevel = 200, int $logBatchSize = 10)
    {
        $this->region = $cloudWatchConfig->getRegion();
        $this->accessKey = $cloudWatchConfig->getAccessKey();
        $this->secret = $cloudWatchConfig->getSecret();
        $this->version = $cloudWatchConfig->getVersion();
        $this->logGroupName = $cloudWatchConfig->getLogGroupName();
        $this->streamName = $cloudWatchConfig->getStreamName();
        $this->logRetentionDate = $logRetentionDate;
        $this->logLevel = $logLevel;
        $this->logBatchSize = $logBatchSize;
    }


    /**
     * @throws Exception
     */
    public function getCloudWatchLogHandler(): CloudWatch
    {
        $client = new CloudWatchLogsClient([
            'region' => $this->region,
            'version' => $this->version,
            'credentials' => [
                'key' => $this->accessKey,
                'secret' => $this->secret
            ]
        ]);
        $cloudWatchLogHandler = new CloudWatch($client, $this->logGroupName, $this->streamName, $this->logRetentionDate, $this->logBatchSize, [], $this->logLevel);
        $cloudWatchLogHandler->setFormatter(new JsonFormatter());

        return $cloudWatchLogHandler;
    }
}