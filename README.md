[![CI Tests](https://github.com/daycry/phpunit-extension-vcr/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/daycry/phpunit-extension-vcr/actions/workflows/phpunit.yml)
[![Coverage Status](https://coveralls.io/repos/github/daycry/phpunit-extension-vcr/badge.svg?branch=master)](https://coveralls.io/github/daycry/phpunit-extension-vcr?branch=master)
[![Latest Stable Version](https://poser.pugx.org/daycry/phpunit-extension-vcr/v/stable)](https://packagist.org/packages/daycry/phpunit-extension-vcr)
[![Total Downloads](https://poser.pugx.org/daycry/phpunit-extension-vcr/downloads)](https://packagist.org/packages/daycry/phpunit-extension-vcr)
[![License](https://poser.pugx.org/daycry/phpunit-extension-vcr/license)](https://packagist.org/packages/daycry/phpunit-extension-vcr)

# PHP-VCR Extension for PHPUnit

A modern library that provides seamless integration between [PHP-VCR](https://github.com/php-vcr/php-vcr) and PHPUnit, enabling you to record and replay HTTP interactions in your tests using PHP 8+ attributes.

## Table of Contents

- [Why Use This Extension?](#why-use-this-extension)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Features](#features)
- [Advanced Usage](#advanced-usage)
- [Testing Strategies](#testing-strategies)
- [Performance Tips](#performance-tips)
- [Troubleshooting](#troubleshooting)
- [Development](#development)
- [License](#license)
- [Acknowledgments](#acknowledgments)
- [Changelog](#changelog)

## Why Use This Extension?

- 🎯 **Zero Configuration**: Works out of the box with sensible defaults
- 🏷️ **Modern PHP 8+ Attributes**: Clean, declarative syntax using `#[UseCassette]`
- 🔄 **Automatic State Management**: Handles VCR lifecycle automatically
- 🎭 **Mixed Test Support**: Seamlessly mix tests with and without HTTP recording
- ⚡ **Performance**: Tests run faster by avoiding real HTTP requests
- 🛡️ **Reliability**: Consistent test results independent of external services

## Requirements

- **PHP**: 8.2 or higher
- **PHPUnit**: 10.0 or higher
- **php-vcr/php-vcr**: ^1.7

## Installation

Install via Composer:

```bash
composer require --dev daycry/phpunit-extension-vcr
```

## Configuration

Add the extension to your `phpunit.xml` or `phpunit.xml.dist` file:

```xml
<phpunit>
    <!-- Your existing configuration -->
    
    <extensions>
        <bootstrap class="\Daycry\PHPUnit\Vcr\Extension">
            <parameter name="cassettesPath" value="tests/fixtures" />
            <parameter name="storage" value="yaml" />
            <parameter name="mode" value="new_episodes" />
            <parameter name="libraryHooks" value="stream_wrapper, curl, soap" />
            <parameter name="requestMatchers" value="method, url, query_string, host" />
        </bootstrap>
    </extensions>
</phpunit>
```

### Configuration Parameters

All parameters are optional and will use sensible defaults:

| Parameter | Default | Description |
|-----------|---------|-------------|
| `cassettesPath` | `tests/fixtures` | Directory to store cassette files (relative to project root) |
| `storage` | `yaml` | Storage format (`yaml` for human-readable, `json` for smaller files) |
| `mode` | `new_episodes` | Recording mode ([see PHP-VCR docs](https://php-vcr.github.io/documentation/configuration/#record-modes)) |
| `libraryHooks` | `stream_wrapper` | Hooks to enable ([see PHP-VCR docs](https://php-vcr.github.io/documentation/configuration/#library-hooks)) |
| `requestMatchers` | `method, url` | Request matching strategy ([see PHP-VCR docs](https://php-vcr.github.io/documentation/configuration/#request-matching)) |
| `whitelistedPaths` | (empty) | Paths to allow real HTTP requests (comma-separated) |
| `blacklistedPaths` | (empty) | Paths to block from recording (comma-separated) |

#### Recording Modes

- **`none`**: Only playback existing cassettes, never record new ones
- **`once`**: Record new episodes only if the cassette file doesn't exist  
- **`new_episodes`**: Record new requests, replay existing ones (recommended for development)
- **`all`**: Always re-record all requests, overwriting the entire cassette

#### Library Hooks

Multiple hooks can be enabled by separating them with commas:

```xml
<!-- Enable multiple hooks for maximum compatibility -->
<parameter name="libraryHooks" value="stream_wrapper, curl, soap" />
```

- **`stream_wrapper`**: Intercepts `file_get_contents()`, `fopen()`, etc.
- **`curl`**: Intercepts cURL functions
- **`soap`**: Intercepts SOAP client requests

## Usage

The extension provides the `#[UseCassette]` attribute that can be applied to test classes or individual test methods.

### Basic Usage

#### Recording HTTP requests for all tests in a class:

```php
<?php

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[UseCassette("api_responses.yml")]
class ApiTest extends TestCase
{
    #[Test]
    public function testApiEndpoint(): void
    {
        // This HTTP request will be recorded to api_responses.yml
        $response = file_get_contents('https://api.example.com/users');
        
        $this->assertNotEmpty($response);
    }
    
    #[Test]
    public function testAnotherEndpoint(): void
    {
        // This request will also be recorded to the same cassette
        $response = file_get_contents('https://api.example.com/posts');
        
        $this->assertNotEmpty($response);
    }
}
```

#### Recording HTTP requests for specific test methods:

```php
<?php

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MixedApiTest extends TestCase
{
    #[Test]
    #[UseCassette("users.yml")]
    public function testUsersApi(): void
    {
        $response = file_get_contents('https://api.example.com/users');
        $this->assertNotEmpty($response);
    }

    #[Test]
    public function testWithoutRecording(): void
    {
        // This test won't use VCR - useful for unit tests
        $this->assertTrue(true);
    }

    #[Test]
    #[UseCassette("posts.yml")]
    public function testPostsApi(): void
    {
        $response = file_get_contents('https://api.example.com/posts');
        $this->assertNotEmpty($response);
    }
}
```

#### Method-level cassettes override class-level cassettes:

```php
<?php

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[UseCassette("default.yml")]  // Default cassette for the class
class OverrideTest extends TestCase
{
    #[Test]
    public function testUsesDefaultCassette(): void
    {
        // Uses default.yml
        $response = file_get_contents('https://api.example.com/default');
        $this->assertNotEmpty($response);
    }

    #[Test]
    #[UseCassette("special.yml")]  // Override for this specific test
    public function testUsesSpecificCassette(): void
    {
        // Uses special.yml instead of default.yml
        $response = file_get_contents('https://api.example.com/special');
        $this->assertNotEmpty($response);
    }
}
```

### Working with Different HTTP Clients

The extension works with any HTTP client that uses PHP's HTTP stream context:

```php
<?php

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[UseCassette("http_clients.yml")]
class HttpClientTest extends TestCase
{
    #[Test]
    public function testFileGetContents(): void
    {
        $response = file_get_contents('https://httpbin.org/get');
        $this->assertStringContainsString('httpbin.org', $response);
    }

    #[Test]
    public function testCurl(): void
    {
        $ch = curl_init('https://httpbin.org/get');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $this->assertNotFalse($response);
    }

    #[Test]
    public function testStreamContext(): void
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode(['key' => 'value'])
            ]
        ]);
        
        $response = file_get_contents('https://httpbin.org/post', false, $context);
        $this->assertNotEmpty($response);
    }
}
```

## Features

* **Automatic State Management**: The library automatically handles VCR state cleanup between tests, ensuring that tests without cassettes don't interfere with tests that use cassettes.
* **Flexible Configuration**: All VCR configuration options are supported through PHPUnit extension parameters.
* **PHP 8+ Attributes**: Uses modern PHP attributes for clean and readable test declarations.
* **Mixed Test Support**: Seamlessly handles test suites that mix tests with and without cassettes.

## Advanced Usage

### Working with Data Providers

The extension works seamlessly with PHPUnit data providers:

```php
<?php

use Daycry\PHPUnit\Vcr\Attributes\UseCassette;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[UseCassette("data_provider_tests.yml")]
class DataProviderTest extends TestCase
{
    public static function urlProvider(): array
    {
        return [
            'users endpoint' => ['https://api.example.com/users'],
            'posts endpoint' => ['https://api.example.com/posts'],
            'comments endpoint' => ['https://api.example.com/comments'],
        ];
    }

    #[Test]
    #[DataProvider('urlProvider')]
    public function testMultipleEndpoints(string $url): void
    {
        $response = file_get_contents($url);
        $this->assertNotEmpty($response);
        
        $data = json_decode($response, true);
        $this->assertIsArray($data);
    }
}
```

### Understanding VCR Modes

The `mode` parameter controls how VCR handles HTTP requests:

- **`none`**: Playback only. Throws exception if no matching cassette is found.
- **`once`**: Record new episodes only if cassette doesn't exist.
- **`new_episodes`** (recommended): Record new requests, replay existing ones.
- **`all`**: Always re-record all requests (overwrites cassette).

```xml
<!-- For development: record new requests -->
<parameter name="mode" value="new_episodes" />

<!-- For CI: only replay existing cassettes -->
<parameter name="mode" value="none" />
```

### Cassette File Structure

Cassettes are stored as YAML (default) or JSON files containing HTTP request/response pairs:

```yaml
# tests/fixtures/example.yml
-
    request:
        method: GET
        uri: https://api.example.com/users
        headers:
            Host: [api.example.com]
    response:
        status:
            http_version: '1.1'
            code: 200
            message: OK
        headers:
            Content-Type: ['application/json']
        body: '{"users": [{"id": 1, "name": "John"}]}'
```

### Custom Request Matching

You can customize how requests are matched to cassette entries:

```xml
<!-- Match by method and URL only (fastest) -->
<parameter name="requestMatchers" value="method, url" />

<!-- Include query parameters and headers (more precise) -->
<parameter name="requestMatchers" value="method, url, query_string, headers" />

<!-- Include request body for POST/PUT requests -->
<parameter name="requestMatchers" value="method, url, body" />
```

## Testing Strategies

### 1. API Integration Tests

Use VCR for testing external API integrations:

```php
#[UseCassette("github_api.yml")]
class GitHubApiTest extends TestCase
{
    #[Test]
    public function testFetchingUserRepositories(): void
    {
        $client = new GitHubApiClient();
        $repos = $client->getUserRepositories('octocat');
        
        $this->assertCount(8, $repos);
        $this->assertEquals('Hello-World', $repos[0]['name']);
    }
}
```

### 2. Service Layer Tests

Record interactions with external services:

```php
#[UseCassette("payment_service.yml")]
class PaymentServiceTest extends TestCase
{
    #[Test]
    public function testProcessPayment(): void
    {
        $service = new PaymentService();
        $result = $service->processPayment(100.00, 'USD');
        
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tx_123456', $result->getTransactionId());
    }
}
```

### 3. Webhook Testing

Test webhook handlers by recording webhook payloads:

```php
#[UseCassette("webhook_verification.yml")]
class WebhookTest extends TestCase
{
    #[Test]
    public function testWebhookSignatureVerification(): void
    {
        $handler = new WebhookHandler();
        
        // This will record the HTTP request to the verification service
        $isValid = $handler->verifySignature($payload, $signature);
        
        $this->assertTrue($isValid);
    }
}
```

## Performance Tips

1. **Use specific request matchers**: Only match what you need to avoid false matches
2. **Group related tests**: Use class-level cassettes for related API calls
3. **Clean up cassettes**: Remove outdated cassettes regularly
4. **Use `none` mode in CI**: Ensure tests only use existing cassettes in production

## Troubleshooting

### Common Issues

#### Cassette Not Found

```
VCR\Exception\RequestNotFound: Request not found in cassette
```

**Solution**: Check that the cassette file exists and the request matches exactly. Consider using fewer request matchers.

#### Permission Errors

```
Unable to write cassette to tests/fixtures/
```

**Solution**: Ensure the cassettes directory is writable:

```bash
chmod 755 tests/fixtures/
```

#### Tests Failing in CI

**Solution**: Set VCR mode to `none` in CI to ensure only existing cassettes are used:

```xml
<parameter name="mode" value="none" />
```

#### Outdated Cassettes

**Solution**: Delete cassette files and re-run tests with `new_episodes` mode:

```bash
rm tests/fixtures/*.yml
vendor/bin/phpunit
```

### Debug Mode

Enable VCR debug output by setting the `VCR_DEBUG` environment variable:

```bash
VCR_DEBUG=1 vendor/bin/phpunit
```

## Development

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run static analysis
composer phpstan

# Run code style checks
composer cs:check

# Fix code style
composer cs:fix
```

### Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes and add tests
4. Run the test suite: `composer test`
5. Submit a pull request

Please ensure:
- All tests pass
- Code follows PSR-12 standards
- New features include tests and documentation

### Code Quality Tools

This project uses several quality tools:

- **PHPStan**: Static analysis (Level 8)
- **PHP-CS-Fixer**: Code style enforcement
- **Infection**: Mutation testing
- **PHPUnit**: Unit and integration testing

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [PHP-VCR](https://github.com/php-vcr/php-vcr) - The underlying VCR library
- [PHPUnit](https://phpunit.de/) - The testing framework
- All contributors who have helped improve this library

## Changelog

### v2.0.0
- Added PHP 8+ attribute support
- Improved state management
- Added comprehensive test coverage
- Updated documentation

### v1.0.0
- Initial release
- Basic VCR integration with PHPUnit

For detailed changes, see [RELEASES](https://github.com/daycry/phpunit-extension-vcr/releases).
