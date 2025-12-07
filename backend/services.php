<?php
// Redis接続クラス
class RedisCache
{
    private $redis;
    private $available;

    public function __construct()
    {
        $this->available = false;
        $this->redis = null;
        
        // Redis拡張が利用可能かチェック
        if (extension_loaded('redis') && class_exists('Redis')) {
            try {
                // @phpstan-ignore-next-line
                $this->redis = new Redis();
                if ($this->redis->connect('redis', 6379)) {
                    $this->available = true;
                } else {
                    error_log("Redis connection failed");
                    $this->available = false;
                }
            } catch (Exception $e) {
                error_log("Redis connection failed: " . $e->getMessage());
                $this->available = false;
            }
        } else {
            error_log("Redis extension not available");
        }
    }

    public function get($key)
    {
        if (!$this->available) {
            return false;
        }
        
        $value = $this->redis->get($key);
        return $value ? json_decode($value, true) : false;
    }

    public function set($key, $value, $ttl = 3600)
    {
        if (!$this->available) {
            return false;
        }
        
        return $this->redis->setex($key, $ttl, json_encode($value));
    }

    public function delete($key)
    {
        if (!$this->available) {
            return false;
        }
        
        return $this->redis->del($key);
    }

    public function flush()
    {
        if (!$this->available) {
            return false;
        }
        
        return $this->redis->flushAll();
    }
}

// Elasticsearch接続クラス
class ElasticsearchClient
{
    private $baseUrl;
    private $available;

    public function __construct()
    {
        $this->baseUrl = 'http://elasticsearch:9200';
        $this->available = function_exists('curl_init');
        
        if (!$this->available) {
            error_log("cURL extension not available");
        }
    }

    private function makeRequest($method, $path, $data = null)
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10秒のタイムアウト
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5秒の接続タイムアウト

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log("Elasticsearch request failed: " . $error);
            return ['code' => 0, 'body' => ['error' => $error]];
        }
        
        curl_close($ch);

        $body = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            return ['code' => $httpCode, 'body' => ['error' => 'Invalid JSON response']];
        }

        return ['code' => $httpCode, 'body' => $body];
    }

    public function indexTodo($id, $todo)
    {
        if (!$this->available) {
            return ['code' => 0, 'body' => ['error' => 'Elasticsearch not available']];
        }
        return $this->makeRequest('PUT', "/todos/_doc/{$id}", $todo);
    }

    public function searchTodos($query)
    {
        if (!$this->available) {
            return ['code' => 0, 'body' => ['error' => 'Elasticsearch not available']];
        }
        
        $searchBody = [
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^2', 'description']
                ]
            ]
        ];

        return $this->makeRequest('POST', '/todos/_search', $searchBody);
    }

    public function deleteTodo($id)
    {
        if (!$this->available) {
            return ['code' => 0, 'body' => ['error' => 'Elasticsearch not available']];
        }
        return $this->makeRequest('DELETE', "/todos/_doc/{$id}");
    }

    public function createIndex()
    {
        if (!$this->available) {
            return ['code' => 0, 'body' => ['error' => 'Elasticsearch not available']];
        }
        
        $mapping = [
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text'],
                    'description' => ['type' => 'text'],
                    'completed' => ['type' => 'boolean'],
                    'created_at' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                    ],
                    'updated_at' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                    ]
                ]
            ]
        ];

        return $this->makeRequest('PUT', '/todos', $mapping);
    }
}
