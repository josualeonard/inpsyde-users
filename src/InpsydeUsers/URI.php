<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

namespace InpsydeUsers;

/**
 * URI Class
 *
 * To handle everything about URI
 */
class URI
{
    private $uri;
    private $defaultUri = 'my-lovely-users-table';
    private $permalinkStructure;
    
    /**
     * Constructor
    */
    public function __construct()
    {
        $this->uri = get_site_option('inpsyde_uri', '');
        $this->permalinkStructure = get_option('permalink_structure');
    }

    public function default(): string
    {
        return $this->defaultUri;
    }

    public function uri(bool $pure = false): string
    {
        if ($pure) {
            return $this->uri;
        }
        return $this->uri?:$this->default();
    }

    public function usingPermalink(): bool
    {
        return ($this->permalinkStructure!=="");
    }

    public function updateUri(string $newUri)
    {
        $this->uri = $newUri;
        update_site_option('inpsyde_uri', $this->uri, '');
    }
}
