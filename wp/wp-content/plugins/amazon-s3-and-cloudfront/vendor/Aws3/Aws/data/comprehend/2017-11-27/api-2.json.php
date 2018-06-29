<?php

// This file was auto-generated from sdk-root/src/data/comprehend/2017-11-27/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2017-11-27', 'endpointPrefix' => 'comprehend', 'jsonVersion' => '1.1', 'protocol' => 'json', 'serviceFullName' => 'Amazon Comprehend', 'serviceId' => 'Comprehend', 'signatureVersion' => 'v4', 'signingName' => 'comprehend', 'targetPrefix' => 'Comprehend_20171127', 'uid' => 'comprehend-2017-11-27'], 'operations' => ['BatchDetectDominantLanguage' => ['name' => 'BatchDetectDominantLanguage', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchDetectDominantLanguageRequest'], 'output' => ['shape' => 'BatchDetectDominantLanguageResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'BatchSizeLimitExceededException'], ['shape' => 'InternalServerException']]], 'BatchDetectEntities' => ['name' => 'BatchDetectEntities', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchDetectEntitiesRequest'], 'output' => ['shape' => 'BatchDetectEntitiesResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'BatchSizeLimitExceededException'], ['shape' => 'InternalServerException']]], 'BatchDetectKeyPhrases' => ['name' => 'BatchDetectKeyPhrases', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchDetectKeyPhrasesRequest'], 'output' => ['shape' => 'BatchDetectKeyPhrasesResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'BatchSizeLimitExceededException'], ['shape' => 'InternalServerException']]], 'BatchDetectSentiment' => ['name' => 'BatchDetectSentiment', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchDetectSentimentRequest'], 'output' => ['shape' => 'BatchDetectSentimentResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'BatchSizeLimitExceededException'], ['shape' => 'InternalServerException']]], 'DescribeTopicsDetectionJob' => ['name' => 'DescribeTopicsDetectionJob', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DescribeTopicsDetectionJobRequest'], 'output' => ['shape' => 'DescribeTopicsDetectionJobResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'JobNotFoundException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'InternalServerException']]], 'DetectDominantLanguage' => ['name' => 'DetectDominantLanguage', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DetectDominantLanguageRequest'], 'output' => ['shape' => 'DetectDominantLanguageResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'InternalServerException']]], 'DetectEntities' => ['name' => 'DetectEntities', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DetectEntitiesRequest'], 'output' => ['shape' => 'DetectEntitiesResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'InternalServerException']]], 'DetectKeyPhrases' => ['name' => 'DetectKeyPhrases', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DetectKeyPhrasesRequest'], 'output' => ['shape' => 'DetectKeyPhrasesResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'InternalServerException']]], 'DetectSentiment' => ['name' => 'DetectSentiment', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DetectSentimentRequest'], 'output' => ['shape' => 'DetectSentimentResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TextSizeLimitExceededException'], ['shape' => 'UnsupportedLanguageException'], ['shape' => 'InternalServerException']]], 'ListTopicsDetectionJobs' => ['name' => 'ListTopicsDetectionJobs', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'ListTopicsDetectionJobsRequest'], 'output' => ['shape' => 'ListTopicsDetectionJobsResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'InvalidFilterException'], ['shape' => 'InternalServerException']]], 'StartTopicsDetectionJob' => ['name' => 'StartTopicsDetectionJob', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'StartTopicsDetectionJobRequest'], 'output' => ['shape' => 'StartTopicsDetectionJobResponse'], 'errors' => [['shape' => 'InvalidRequestException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'InternalServerException']]]], 'shapes' => ['AnyLengthString' => ['type' => 'string'], 'BatchDetectDominantLanguageItemResult' => ['type' => 'structure', 'members' => ['Index' => ['shape' => 'Integer'], 'Languages' => ['shape' => 'ListOfDominantLanguages']]], 'BatchDetectDominantLanguageRequest' => ['type' => 'structure', 'required' => ['TextList'], 'members' => ['TextList' => ['shape' => 'StringList']]], 'BatchDetectDominantLanguageResponse' => ['type' => 'structure', 'required' => ['ResultList', 'ErrorList'], 'members' => ['ResultList' => ['shape' => 'ListOfDetectDominantLanguageResult'], 'ErrorList' => ['shape' => 'BatchItemErrorList']]], 'BatchDetectEntitiesItemResult' => ['type' => 'structure', 'members' => ['Index' => ['shape' => 'Integer'], 'Entities' => ['shape' => 'ListOfEntities']]], 'BatchDetectEntitiesRequest' => ['type' => 'structure', 'required' => ['TextList', 'LanguageCode'], 'members' => ['TextList' => ['shape' => 'StringList'], 'LanguageCode' => ['shape' => 'String']]], 'BatchDetectEntitiesResponse' => ['type' => 'structure', 'required' => ['ResultList', 'ErrorList'], 'members' => ['ResultList' => ['shape' => 'ListOfDetectEntitiesResult'], 'ErrorList' => ['shape' => 'BatchItemErrorList']]], 'BatchDetectKeyPhrasesItemResult' => ['type' => 'structure', 'members' => ['Index' => ['shape' => 'Integer'], 'KeyPhrases' => ['shape' => 'ListOfKeyPhrases']]], 'BatchDetectKeyPhrasesRequest' => ['type' => 'structure', 'required' => ['TextList', 'LanguageCode'], 'members' => ['TextList' => ['shape' => 'StringList'], 'LanguageCode' => ['shape' => 'String']]], 'BatchDetectKeyPhrasesResponse' => ['type' => 'structure', 'required' => ['ResultList', 'ErrorList'], 'members' => ['ResultList' => ['shape' => 'ListOfDetectKeyPhrasesResult'], 'ErrorList' => ['shape' => 'BatchItemErrorList']]], 'BatchDetectSentimentItemResult' => ['type' => 'structure', 'members' => ['Index' => ['shape' => 'Integer'], 'Sentiment' => ['shape' => 'SentimentType'], 'SentimentScore' => ['shape' => 'SentimentScore']]], 'BatchDetectSentimentRequest' => ['type' => 'structure', 'required' => ['TextList', 'LanguageCode'], 'members' => ['TextList' => ['shape' => 'StringList'], 'LanguageCode' => ['shape' => 'String']]], 'BatchDetectSentimentResponse' => ['type' => 'structure', 'required' => ['ResultList', 'ErrorList'], 'members' => ['ResultList' => ['shape' => 'ListOfDetectSentimentResult'], 'ErrorList' => ['shape' => 'BatchItemErrorList']]], 'BatchItemError' => ['type' => 'structure', 'members' => ['Index' => ['shape' => 'Integer'], 'ErrorCode' => ['shape' => 'String'], 'ErrorMessage' => ['shape' => 'String']]], 'BatchItemErrorList' => ['type' => 'list', 'member' => ['shape' => 'BatchItemError']], 'BatchSizeLimitExceededException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'ClientRequestTokenString' => ['type' => 'string', 'max' => 64, 'min' => 1, 'pattern' => '^[a-zA-Z0-9-]+$'], 'DescribeTopicsDetectionJobRequest' => ['type' => 'structure', 'required' => ['JobId'], 'members' => ['JobId' => ['shape' => 'JobId']]], 'DescribeTopicsDetectionJobResponse' => ['type' => 'structure', 'members' => ['TopicsDetectionJobProperties' => ['shape' => 'TopicsDetectionJobProperties']]], 'DetectDominantLanguageRequest' => ['type' => 'structure', 'required' => ['Text'], 'members' => ['Text' => ['shape' => 'String']]], 'DetectDominantLanguageResponse' => ['type' => 'structure', 'members' => ['Languages' => ['shape' => 'ListOfDominantLanguages']]], 'DetectEntitiesRequest' => ['type' => 'structure', 'required' => ['Text', 'LanguageCode'], 'members' => ['Text' => ['shape' => 'String'], 'LanguageCode' => ['shape' => 'LanguageCode']]], 'DetectEntitiesResponse' => ['type' => 'structure', 'members' => ['Entities' => ['shape' => 'ListOfEntities']]], 'DetectKeyPhrasesRequest' => ['type' => 'structure', 'required' => ['Text', 'LanguageCode'], 'members' => ['Text' => ['shape' => 'String'], 'LanguageCode' => ['shape' => 'LanguageCode']]], 'DetectKeyPhrasesResponse' => ['type' => 'structure', 'members' => ['KeyPhrases' => ['shape' => 'ListOfKeyPhrases']]], 'DetectSentimentRequest' => ['type' => 'structure', 'required' => ['Text', 'LanguageCode'], 'members' => ['Text' => ['shape' => 'String'], 'LanguageCode' => ['shape' => 'LanguageCode']]], 'DetectSentimentResponse' => ['type' => 'structure', 'members' => ['Sentiment' => ['shape' => 'SentimentType'], 'SentimentScore' => ['shape' => 'SentimentScore']]], 'DominantLanguage' => ['type' => 'structure', 'members' => ['LanguageCode' => ['shape' => 'String'], 'Score' => ['shape' => 'Float']]], 'Entity' => ['type' => 'structure', 'members' => ['Score' => ['shape' => 'Float'], 'Type' => ['shape' => 'EntityType'], 'Text' => ['shape' => 'String'], 'BeginOffset' => ['shape' => 'Integer'], 'EndOffset' => ['shape' => 'Integer']]], 'EntityType' => ['type' => 'string', 'enum' => ['PERSON', 'LOCATION', 'ORGANIZATION', 'COMMERCIAL_ITEM', 'EVENT', 'DATE', 'QUANTITY', 'TITLE', 'OTHER']], 'Float' => ['type' => 'float'], 'IamRoleArn' => ['type' => 'string', 'pattern' => 'arn:aws(-[^:]+)?:iam::[0-9]{12}:role/.+'], 'InputDataConfig' => ['type' => 'structure', 'required' => ['S3Uri'], 'members' => ['S3Uri' => ['shape' => 'S3Uri'], 'InputFormat' => ['shape' => 'InputFormat']]], 'InputFormat' => ['type' => 'string', 'enum' => ['ONE_DOC_PER_FILE', 'ONE_DOC_PER_LINE']], 'Integer' => ['type' => 'integer'], 'InternalServerException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true, 'fault' => \true], 'InvalidFilterException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'InvalidRequestException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'JobId' => ['type' => 'string', 'max' => 32, 'min' => 1], 'JobName' => ['type' => 'string', 'max' => 256, 'min' => 1, 'pattern' => '^([\\p{L}\\p{Z}\\p{N}_.:/=+\\-%@]*)$'], 'JobNotFoundException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'JobStatus' => ['type' => 'string', 'enum' => ['SUBMITTED', 'IN_PROGRESS', 'COMPLETED', 'FAILED']], 'KeyPhrase' => ['type' => 'structure', 'members' => ['Score' => ['shape' => 'Float'], 'Text' => ['shape' => 'String'], 'BeginOffset' => ['shape' => 'Integer'], 'EndOffset' => ['shape' => 'Integer']]], 'LanguageCode' => ['type' => 'string', 'enum' => ['en', 'es']], 'ListOfDetectDominantLanguageResult' => ['type' => 'list', 'member' => ['shape' => 'BatchDetectDominantLanguageItemResult']], 'ListOfDetectEntitiesResult' => ['type' => 'list', 'member' => ['shape' => 'BatchDetectEntitiesItemResult']], 'ListOfDetectKeyPhrasesResult' => ['type' => 'list', 'member' => ['shape' => 'BatchDetectKeyPhrasesItemResult']], 'ListOfDetectSentimentResult' => ['type' => 'list', 'member' => ['shape' => 'BatchDetectSentimentItemResult']], 'ListOfDominantLanguages' => ['type' => 'list', 'member' => ['shape' => 'DominantLanguage']], 'ListOfEntities' => ['type' => 'list', 'member' => ['shape' => 'Entity']], 'ListOfKeyPhrases' => ['type' => 'list', 'member' => ['shape' => 'KeyPhrase']], 'ListTopicsDetectionJobsRequest' => ['type' => 'structure', 'members' => ['Filter' => ['shape' => 'TopicsDetectionJobFilter'], 'NextToken' => ['shape' => 'String'], 'MaxResults' => ['shape' => 'MaxResultsInteger']]], 'ListTopicsDetectionJobsResponse' => ['type' => 'structure', 'members' => ['TopicsDetectionJobPropertiesList' => ['shape' => 'TopicsDetectionJobPropertiesList'], 'NextToken' => ['shape' => 'String']]], 'MaxResultsInteger' => ['type' => 'integer', 'max' => 500, 'min' => 1], 'NumberOfTopicsInteger' => ['type' => 'integer', 'max' => 100, 'min' => 1], 'OutputDataConfig' => ['type' => 'structure', 'required' => ['S3Uri'], 'members' => ['S3Uri' => ['shape' => 'S3Uri']]], 'S3Uri' => ['type' => 'string', 'max' => 1024, 'pattern' => 's3://([^/]+)(/.*)?'], 'SentimentScore' => ['type' => 'structure', 'members' => ['Positive' => ['shape' => 'Float'], 'Negative' => ['shape' => 'Float'], 'Neutral' => ['shape' => 'Float'], 'Mixed' => ['shape' => 'Float']]], 'SentimentType' => ['type' => 'string', 'enum' => ['POSITIVE', 'NEGATIVE', 'NEUTRAL', 'MIXED']], 'StartTopicsDetectionJobRequest' => ['type' => 'structure', 'required' => ['InputDataConfig', 'OutputDataConfig', 'DataAccessRoleArn'], 'members' => ['InputDataConfig' => ['shape' => 'InputDataConfig'], 'OutputDataConfig' => ['shape' => 'OutputDataConfig'], 'DataAccessRoleArn' => ['shape' => 'IamRoleArn'], 'JobName' => ['shape' => 'JobName'], 'NumberOfTopics' => ['shape' => 'NumberOfTopicsInteger'], 'ClientRequestToken' => ['shape' => 'ClientRequestTokenString', 'idempotencyToken' => \true]]], 'StartTopicsDetectionJobResponse' => ['type' => 'structure', 'members' => ['JobId' => ['shape' => 'JobId'], 'JobStatus' => ['shape' => 'JobStatus']]], 'String' => ['type' => 'string', 'min' => 1], 'StringList' => ['type' => 'list', 'member' => ['shape' => 'String']], 'TextSizeLimitExceededException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'Timestamp' => ['type' => 'timestamp'], 'TooManyRequestsException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true], 'TopicsDetectionJobFilter' => ['type' => 'structure', 'members' => ['JobName' => ['shape' => 'JobName'], 'JobStatus' => ['shape' => 'JobStatus'], 'SubmitTimeBefore' => ['shape' => 'Timestamp'], 'SubmitTimeAfter' => ['shape' => 'Timestamp']]], 'TopicsDetectionJobProperties' => ['type' => 'structure', 'members' => ['JobId' => ['shape' => 'JobId'], 'JobName' => ['shape' => 'JobName'], 'JobStatus' => ['shape' => 'JobStatus'], 'Message' => ['shape' => 'AnyLengthString'], 'SubmitTime' => ['shape' => 'Timestamp'], 'EndTime' => ['shape' => 'Timestamp'], 'InputDataConfig' => ['shape' => 'InputDataConfig'], 'OutputDataConfig' => ['shape' => 'OutputDataConfig'], 'NumberOfTopics' => ['shape' => 'Integer']]], 'TopicsDetectionJobPropertiesList' => ['type' => 'list', 'member' => ['shape' => 'TopicsDetectionJobProperties']], 'UnsupportedLanguageException' => ['type' => 'structure', 'members' => ['Message' => ['shape' => 'String']], 'exception' => \true]]];
