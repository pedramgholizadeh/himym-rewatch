<?php
/**
 * Streaming proxy: forwards Range requests and status codes so the browser
 * can seek inside .mkv files, and forces a video Content-Type.
 *
 * Deployed at: https://webiro.ir/api/proxy.php
 * Health check: https://webiro.ir/api/proxy.php?ping=1
 */
declare(strict_types=1);

@ini_set('output_buffering', '0');
@ini_set('zlib.output_compression', '0');
@ini_set('implicit_flush', '1');
while (ob_get_level() > 0) {
    @ob_end_clean();
}

const PROXY_VERSION = '2';
const ERROR_VIDEO = 'https://webiro.ir/stream/assets/error.mp4';
const ALLOWED_HOSTS = ['giftmond.ir'];

function fail(int $code = 302): void
{
    if ($code === 302) {
        header('Location: ' . ERROR_VIDEO, true, 302);
    } else {
        http_response_code($code);
    }
    exit;
}

if (isset($_GET['ping'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Proxy-Version: ' . PROXY_VERSION);
    echo json_encode(['ok' => true, 'version' => PROXY_VERSION], JSON_UNESCAPED_SLASHES);
    exit;
}

function mime_for(string $url): string
{
    $ext = strtolower(pathinfo((string)parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
    $map = [
        'mp4'  => 'video/mp4',
        'm4v'  => 'video/x-m4v',
        'mkv'  => 'video/x-matroska',
        'webm' => 'video/webm',
        'mov'  => 'video/quicktime',
        'avi'  => 'video/x-msvideo',
    ];
    return $map[$ext] ?? 'application/octet-stream';
}

$url = trim((string)($_GET['url'] ?? ''));

// Some clients double-encode the URL; undo one extra layer.
if (strpos($url, '%25') !== false) {
    $url = rawurldecode($url);
}

if ($url === '' || !preg_match('~^https?://~i', $url)) {
    fail();
}

$host = strtolower((string)parse_url($url, PHP_URL_HOST));
$allowed = false;
foreach (ALLOWED_HOSTS as $suffix) {
    if ($host === $suffix || str_ends_with($host, '.' . $suffix)) {
        $allowed = true;
        break;
    }
}
if (!$allowed) {
    fail(403);
}

$requestHeaders = [];
foreach (['HTTP_RANGE', 'HTTP_IF_RANGE', 'HTTP_IF_MODIFIED_SINCE', 'HTTP_IF_NONE_MATCH'] as $key) {
    if (!empty($_SERVER[$key])) {
        $name = str_replace('HTTP_', '', $key);
        $name = str_replace('_', '-', $name);
        $requestHeaders[] = $name . ': ' . $_SERVER[$key];
    }
}

$contentType = mime_for($url);
$forwarded  = [];
$hasRange   = false;
$upstreamCode = 0;
$headersSent = false;
$aborted     = false;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER      => $requestHeaders,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_MAXREDIRS       => 3,
    CURLOPT_PROTOCOLS       => CURLPROTO_HTTP | CURLPROTO_HTTPS,
    CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
    CURLOPT_SSL_VERIFYPEER  => false,
    CURLOPT_CONNECTTIMEOUT  => 15,
    CURLOPT_TIMEOUT         => 0,
    CURLOPT_BUFFERSIZE      => 1 << 16,
    CURLOPT_NOBODY          => strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD',
    CURLOPT_HEADERFUNCTION  => function ($ch, string $line) use (&$forwarded, &$hasRange, &$upstreamCode, &$headersSent, &$aborted, $contentType) {
        $trimmed = trim($line);

        if (preg_match('~^HTTP/\S+\s+(\d{3})~', $trimmed, $m)) {
            $upstreamCode = (int)$m[1];
            if ($upstreamCode >= 400) {
                $aborted = true;
                return 0;
            }
            return strlen($line);
        }

        if ($trimmed === '') {
            if (!$headersSent && !$aborted) {
                $code = $upstreamCode ?: 200;
                // Some backends stream a partial body while still answering 200.
                if ($code === 200 && $hasRange) {
                    $code = 206;
                }
                http_response_code($code);
                header_remove('Content-Encoding');
                header('Content-Type: ' . $contentType);
                header('Accept-Ranges: bytes');
                header('Cache-Control: public, max-age=86400');
                header('X-Proxy-Version: ' . PROXY_VERSION);
                header('X-Accel-Buffering: no');
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Expose-Headers: Content-Range, Content-Length, Accept-Ranges');
                foreach ($forwarded as $name => $value) {
                    header($name . ': ' . $value);
                }
                $headersSent = true;
            }
            return strlen($line);
        }

        if (str_contains($trimmed, ':')) {
            [$name, $value] = explode(':', $trimmed, 2);
            $key = strtolower(trim($name));
            if ($key === 'content-range' && trim($value) !== '') {
                $hasRange = true;
            }
            if (in_array($key, ['content-length', 'content-range', 'last-modified', 'etag'], true)) {
                $forwarded[$name] = trim($value);
            }
        }

        return strlen($line);
    },
    CURLOPT_WRITEFUNCTION   => function ($ch, string $data) use (&$aborted) {
        if ($aborted) {
            return 0;
        }
        echo $data;
        flush();
        return strlen($data);
    },
]);

curl_exec($ch);
$curlError = curl_errno($ch);
curl_close($ch);

if ($aborted || ($curlError && !$headersSent)) {
    fail();
}
