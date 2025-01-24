<?php

namespace App;

/**
 * URL Processor
 * Processador de URLs
 * 
 * This class combines the functionality of the previous p.php and api.php files
 * to provide a unified interface for URL processing, handling both web and API responses.
 * 
 * Esta classe combina as funcionalidades dos arquivos p.php e api.php anteriores
 * para fornecer uma interface unificada para processamento de URLs, tratando respostas web e API.
 */
class URLProcessor
{
    private $url;
    private $isApi;
    private $analyzer;

    /**
     * Constructor - initializes the processor with URL and mode
     * Construtor - inicializa o processador com URL e modo
     * 
     * @param string $url The URL to process
     * @param bool $isApi Whether to return API response
     */
    public function __construct(string $url = '', bool $isApi = false)
    {
        require_once __DIR__ . '/../config.php';
        require_once __DIR__ . '/../inc/URLAnalyzer.php';
        require_once __DIR__ . '/../inc/Language.php';

        $this->url = $url;
        $this->isApi = $isApi;
        $this->analyzer = new \URLAnalyzer();

        if ($isApi) {
            // Initialize language system for API responses
            \Language::init(LANGUAGE);
            
            // Set API headers
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET');
        }
    }

    /**
     * Sends a JSON response for API requests
     * Envia uma resposta JSON para requisições API
     */
    private function sendApiResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        $response = ['status' => $statusCode];

        if (isset($data['error'])) {
            $response['error'] = $data['error'];
        } else if (isset($data['url'])) {
            $response['url'] = $data['url'];
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Handles web redirects
     * Trata redirecionamentos web
     */
    private function redirect(string $path, string $message = ''): void
    {
        $url = $message ? $path . '?message=' . $message : $path;
        header('Location: ' . $url);
        exit;
    }

    /**
     * Process the URL and return appropriate response
     * Processa a URL e retorna resposta apropriada
     */
    public function process(): void
    {
        try {
            // Check for redirects in web mode
            if (!$this->isApi) {
                $redirectInfo = $this->analyzer->checkStatus($this->url);
                if ($redirectInfo['hasRedirect'] && $redirectInfo['finalUrl'] !== $this->url) {
                    $this->redirect(SITE_URL . '/p/' . urlencode($redirectInfo['finalUrl']));
                }
            }

            // Process the URL
            $content = $this->analyzer->analyze($this->url);

            if ($this->isApi) {
                $this->sendApiResponse([
                    'url' => SITE_URL . '/p/' . $this->url
                ]);
            } else {
                echo $content;
            }
        } catch (\URLAnalyzerException $e) {
            $errorType = $e->getErrorType();
            $additionalInfo = $e->getAdditionalInfo();

            if ($this->isApi) {
                // Add error headers for API responses
                header('X-Error-Type: ' . $errorType);
                if ($additionalInfo) {
                    header('X-Error-Info: ' . $additionalInfo);
                }

                $this->sendApiResponse([
                    'error' => [
                        'type' => $errorType,
                        'message' => $e->getMessage(),
                        'details' => $additionalInfo ?: null
                    ]
                ], $e->getCode());
            } else {
                // Handle blocked domain with redirect URL for web responses
                if ($errorType === \URLAnalyzer::ERROR_BLOCKED_DOMAIN && $additionalInfo) {
                    $this->redirect(trim($additionalInfo), $errorType);
                }
                $this->redirect(SITE_URL, $errorType);
            }
        } catch (\Exception $e) {
            if ($this->isApi) {
                $this->sendApiResponse([
                    'error' => [
                        'type' => \URLAnalyzer::ERROR_GENERIC_ERROR,
                        'message' => \Language::getMessage('GENERIC_ERROR')['message']
                    ]
                ], 500);
            } else {
                $this->redirect(SITE_URL, \URLAnalyzer::ERROR_GENERIC_ERROR);
            }
        }
    }
}
